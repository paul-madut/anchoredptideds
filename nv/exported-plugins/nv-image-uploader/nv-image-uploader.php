<?php
/**
 * Plugin Name: NV Bulk Image Uploader
 * Description: Upload a zip of product images named by SKU and auto-assign them as product images. Matches files like "NV-RT10 Dark.png" to the parent product containing that SKU.
 * Version: 1.3.0
 * Author: Natty Vision
 * Requires Plugins: woocommerce
 */

if (!defined('ABSPATH')) exit;

class NV_Image_Uploader {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_menu_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
        add_action('admin_post_nv_upload_images', [$this, 'handle_upload']);
        add_action('admin_post_nv_assign_existing', [$this, 'handle_assign_existing']);
    }

    public function add_menu_page() {
        add_menu_page(
            'Bulk Image Upload',
            'Bulk Images',
            'manage_woocommerce',
            'nv-image-uploader',
            [$this, 'render_page'],
            'dashicons-format-gallery',
            57
        );
    }

    public function enqueue_styles($hook) {
        if ($hook !== 'toplevel_page_nv-image-uploader') return;
        wp_enqueue_style('nv-iu-styles', false);
        wp_add_inline_style('nv-iu-styles', $this->get_styles());
    }

    /**
     * Build a map of SKU -> parent product ID.
     * If the SKU belongs to a variation, we return the parent product instead.
     */
    private function build_sku_to_parent_map() {
        $sku_map = [];

        // Get all simple and variable products
        $products = wc_get_products(['limit' => -1, 'status' => 'publish', 'type' => ['simple', 'variable']]);
        foreach ($products as $product) {
            $sku = $product->get_sku();
            if ($sku) {
                $sku_map[strtoupper($sku)] = $product;
            }

            // For variable products, map all variation SKUs back to the parent
            if ($product->is_type('variable')) {
                $variations = $product->get_children();
                foreach ($variations as $var_id) {
                    $var = wc_get_product($var_id);
                    if ($var) {
                        $var_sku = $var->get_sku();
                        if ($var_sku) {
                            // Point variation SKU to the PARENT product
                            $sku_map[strtoupper($var_sku)] = $product;
                        }
                    }
                }
            }

            // Also check custom _nv_variants JSON field
            $product_id = $product->get_id();
            $variants_json = get_post_meta($product_id, '_nv_variants', true);
            if ($variants_json) {
                $variants = json_decode($variants_json, true);
                if (is_array($variants)) {
                    foreach ($variants as $v) {
                        if (!empty($v['sku'])) {
                            $sku_map[strtoupper($v['sku'])] = $product;
                        }
                    }
                }
            }
        }

        return $sku_map;
    }

    private function find_unassigned_images() {
        $results = [];
        $sku_map = $this->build_sku_to_parent_map();
        $seen_products = [];

        $attachments = get_posts([
            'post_type' => 'attachment',
            'post_mime_type' => 'image',
            'posts_per_page' => -1,
            's' => 'NV-',
        ]);

        foreach ($attachments as $attachment) {
            $filename = basename(get_attached_file($attachment->ID));
            $name_without_ext = pathinfo($filename, PATHINFO_FILENAME);

            if (preg_match('/(NV-[A-Z0-9]+)/i', $name_without_ext, $matches)) {
                $sku = strtoupper($matches[1]);
            } else {
                continue;
            }

            if (!isset($sku_map[$sku])) continue;

            $product = $sku_map[$sku];
            $product_id = $product->get_id();

            // Skip if we already have an entry for this parent product
            if (isset($seen_products[$product_id])) continue;
            $seen_products[$product_id] = true;

            $current_thumb = get_post_thumbnail_id($product_id);

            $thumb_url = wp_get_attachment_image_url($attachment->ID, 'thumbnail');
            $results[] = [
                'attachment_id' => $attachment->ID,
                'sku' => $sku,
                'product_id' => $product_id,
                'product_name' => $product->get_name(),
                'has_image' => !empty($current_thumb),
                'thumb' => $thumb_url,
            ];
        }

        return $results;
    }

    public function handle_assign_existing() {
        if (!current_user_can('manage_woocommerce')) wp_die('Unauthorized');
        check_admin_referer('nv_assign_existing', 'nv_assign_nonce');

        $results = ['matched' => [], 'skipped' => [], 'errors' => []];
        $unassigned = $this->find_unassigned_images();

        foreach ($unassigned as $item) {
            // Set on parent product using direct post meta
            update_post_meta($item['product_id'], '_thumbnail_id', $item['attachment_id']);
            clean_post_cache($item['product_id']);
            wc_delete_product_transients($item['product_id']);

            $results['matched'][] = [
                'file' => get_the_title($item['attachment_id']),
                'sku' => $item['sku'],
                'product' => $item['product_name'],
            ];
        }

        set_transient('nv_image_upload_results', $results, 60);
        wp_redirect(admin_url('admin.php?page=nv-image-uploader'));
        exit;
    }

    public function handle_upload() {
        if (!current_user_can('manage_woocommerce')) wp_die('Unauthorized');
        check_admin_referer('nv_upload_images', 'nv_upload_nonce');

        $results = ['matched' => [], 'skipped' => [], 'errors' => []];
        $overwrite = isset($_POST['overwrite']);

        if (empty($_FILES['image_zip']['tmp_name'])) {
            $results['errors'][] = 'No file uploaded.';
            set_transient('nv_image_upload_results', $results, 60);
            wp_redirect(admin_url('admin.php?page=nv-image-uploader'));
            exit;
        }

        $zip = new ZipArchive();
        $tmp_dir = wp_tempnam('nv_images_');
        @unlink($tmp_dir);
        wp_mkdir_p($tmp_dir);

        if ($zip->open($_FILES['image_zip']['tmp_name']) !== true) {
            $results['errors'][] = 'Failed to open zip file.';
            set_transient('nv_image_upload_results', $results, 60);
            wp_redirect(admin_url('admin.php?page=nv-image-uploader'));
            exit;
        }

        $zip->extractTo($tmp_dir);
        $zip->close();

        $sku_map = $this->build_sku_to_parent_map();

        $files = $this->get_image_files($tmp_dir);
        foreach ($files as $file_path) {
            $filename = basename($file_path);

            if (strpos($filename, '.') === 0 || strpos($filename, '__MACOSX') !== false) continue;

            $name_without_ext = pathinfo($filename, PATHINFO_FILENAME);
            if (preg_match('/(NV-[A-Z0-9]+)/i', $name_without_ext, $matches)) {
                $sku = strtoupper($matches[1]);
            } else {
                $results['skipped'][] = ['file' => $filename, 'sku' => '—', 'reason' => 'Could not extract SKU from filename'];
                continue;
            }

            if (!isset($sku_map[$sku])) {
                $results['skipped'][] = ['file' => $filename, 'sku' => $sku, 'reason' => 'No product found with this SKU'];
                continue;
            }

            $product = $sku_map[$sku];
            $product_id = $product->get_id();

            if (!$overwrite && get_post_thumbnail_id($product_id)) {
                $results['skipped'][] = ['file' => $filename, 'sku' => $sku, 'reason' => 'Already has product image (overwrite disabled)'];
                continue;
            }

            $upload = $this->upload_image($file_path, $filename);
            if (is_wp_error($upload)) {
                $results['errors'][] = "$filename: " . $upload->get_error_message();
                continue;
            }

            update_post_meta($product_id, '_thumbnail_id', $upload);
            clean_post_cache($product_id);
            wc_delete_product_transients($product_id);

            $results['matched'][] = [
                'file' => $filename,
                'sku' => $sku,
                'product' => $product->get_name(),
            ];
        }

        $this->rmdir_recursive($tmp_dir);

        set_transient('nv_image_upload_results', $results, 60);
        wp_redirect(admin_url('admin.php?page=nv-image-uploader'));
        exit;
    }

    private function upload_image($file_path, $filename) {
        $file_content = file_get_contents($file_path);
        $upload = wp_upload_bits($filename, null, $file_content);

        if ($upload['error']) {
            return new WP_Error('upload_error', $upload['error']);
        }

        $file_type = wp_check_filetype($filename);
        $attachment = [
            'post_mime_type' => $file_type['type'],
            'post_title' => sanitize_file_name(pathinfo($filename, PATHINFO_FILENAME)),
            'post_content' => '',
            'post_status' => 'inherit',
        ];

        $attach_id = wp_insert_attachment($attachment, $upload['file']);
        if (is_wp_error($attach_id)) return $attach_id;

        require_once ABSPATH . 'wp-admin/includes/image.php';
        $metadata = wp_generate_attachment_metadata($attach_id, $upload['file']);
        wp_update_attachment_metadata($attach_id, $metadata);

        return $attach_id;
    }

    private function get_image_files($dir) {
        $files = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
        foreach ($iterator as $file) {
            if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $path = $file->getPathname();
                if (strpos($path, '__MACOSX') === false) {
                    $files[] = $path;
                }
            }
        }
        return $files;
    }

    private function rmdir_recursive($dir) {
        if (!is_dir($dir)) return;
        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($dir);
    }

    public function render_page() {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Unauthorized');
        }

        $results = get_transient('nv_image_upload_results');
        if ($results) {
            delete_transient('nv_image_upload_results');
        }

        $unassigned = $this->find_unassigned_images();
        ?>
        <div class="nv-iu-wrap">
            <h1>Bulk Image Upload</h1>
            <p class="nv-iu-desc">Upload a zip file containing product images. Files should be named with the SKU (e.g. <code>NV-RT10 Dark.png</code> matches SKU <code>NV-RT10</code>). Images are assigned to the parent product.</p>

            <?php if (!empty($unassigned)): ?>
            <div class="nv-iu-card" style="border-left: 4px solid #3b82f6;">
                <h2 style="margin-top:0;">Images Found in Media Library</h2>
                <p class="nv-iu-desc">These images match product SKUs and will be set as the <strong>parent product image</strong> (the main image shown on shop pages).</p>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <?php wp_nonce_field('nv_assign_existing', 'nv_assign_nonce'); ?>
                    <input type="hidden" name="action" value="nv_assign_existing" />
                    <table class="nv-iu-table">
                        <thead><tr><th>Image</th><th>SKU</th><th>Parent Product</th><th>Status</th></tr></thead>
                        <tbody>
                        <?php foreach ($unassigned as $item): ?>
                            <tr>
                                <td><img src="<?php echo esc_url($item['thumb']); ?>" style="width:50px;height:50px;object-fit:cover;border-radius:4px;" /></td>
                                <td><code><?php echo esc_html($item['sku']); ?></code></td>
                                <td><?php echo esc_html($item['product_name']); ?> (ID: <?php echo $item['product_id']; ?>)</td>
                                <td><?php echo $item['has_image'] ? '⚠ Will overwrite current image' : 'No image set'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="nv-iu-btn" style="margin-top:16px;">Assign All as Parent Product Images</button>
                </form>
            </div>
            <?php endif; ?>

            <div class="nv-iu-card">
                <h2 style="margin-top:0;">Upload New Images</h2>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                    <?php wp_nonce_field('nv_upload_images', 'nv_upload_nonce'); ?>
                    <input type="hidden" name="action" value="nv_upload_images" />

                    <div class="nv-iu-upload-area" id="nv-drop-zone">
                        <div class="nv-iu-upload-icon">📦</div>
                        <p>Drag & drop your zip file here, or click to browse</p>
                        <input type="file" name="image_zip" id="nv-file-input" accept=".zip" required />
                    </div>

                    <div class="nv-iu-options">
                        <label>
                            <input type="checkbox" name="overwrite" value="1" checked />
                            Overwrite existing product images
                        </label>
                    </div>

                    <button type="submit" class="nv-iu-btn">Upload & Assign Images</button>
                </form>
            </div>

            <?php if ($results): ?>
            <div class="nv-iu-results">
                <h2>Results</h2>

                <?php if (!empty($results['matched'])): ?>
                <div class="nv-iu-result-section nv-iu-success">
                    <h3>✅ Assigned (<?php echo count($results['matched']); ?>)</h3>
                    <table class="nv-iu-table">
                        <thead><tr><th>File</th><th>SKU</th><th>Product</th></tr></thead>
                        <tbody>
                        <?php foreach ($results['matched'] as $m): ?>
                            <tr>
                                <td><?php echo esc_html($m['file']); ?></td>
                                <td><code><?php echo esc_html($m['sku']); ?></code></td>
                                <td><?php echo esc_html($m['product']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <?php if (!empty($results['skipped'])): ?>
                <div class="nv-iu-result-section nv-iu-warning">
                    <h3>⚠ Skipped (<?php echo count($results['skipped']); ?>)</h3>
                    <table class="nv-iu-table">
                        <thead><tr><th>File</th><th>Extracted SKU</th><th>Reason</th></tr></thead>
                        <tbody>
                        <?php foreach ($results['skipped'] as $s): ?>
                            <tr>
                                <td><?php echo esc_html($s['file']); ?></td>
                                <td><code><?php echo esc_html($s['sku']); ?></code></td>
                                <td><?php echo esc_html($s['reason']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <?php if (!empty($results['errors'])): ?>
                <div class="nv-iu-result-section nv-iu-error">
                    <h3>❌ Errors (<?php echo count($results['errors']); ?>)</h3>
                    <ul>
                    <?php foreach ($results['errors'] as $e): ?>
                        <li><?php echo esc_html($e); ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var dropZone = document.getElementById('nv-drop-zone');
            var fileInput = document.getElementById('nv-file-input');

            dropZone.addEventListener('click', function() { fileInput.click(); });
            dropZone.addEventListener('dragover', function(e) { e.preventDefault(); dropZone.classList.add('nv-iu-dragover'); });
            dropZone.addEventListener('dragleave', function() { dropZone.classList.remove('nv-iu-dragover'); });
            dropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                dropZone.classList.remove('nv-iu-dragover');
                if (e.dataTransfer.files.length) {
                    fileInput.files = e.dataTransfer.files;
                    dropZone.querySelector('p').textContent = e.dataTransfer.files[0].name;
                }
            });
            fileInput.addEventListener('change', function() {
                if (fileInput.files.length) {
                    dropZone.querySelector('p').textContent = fileInput.files[0].name;
                }
            });
        });
        </script>
        <?php
    }

    private function get_styles() {
        return '
        .nv-iu-wrap { max-width: 800px; margin: 20px auto; padding: 0 20px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .nv-iu-wrap h1 { font-size: 28px; font-weight: 700; color: #1a1a2e; margin-bottom: 8px; }
        .nv-iu-desc { color: #667085; font-size: 14px; margin-bottom: 24px; }
        .nv-iu-desc code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
        .nv-iu-card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 32px; margin-bottom: 24px; }
        .nv-iu-upload-area { border: 2px dashed #d0d5dd; border-radius: 12px; padding: 48px; text-align: center; cursor: pointer; transition: all 0.2s; position: relative; }
        .nv-iu-upload-area:hover, .nv-iu-dragover { border-color: #3b82f6; background: #eff6ff; }
        .nv-iu-upload-area input[type="file"] { position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
        .nv-iu-upload-icon { font-size: 48px; margin-bottom: 12px; }
        .nv-iu-upload-area p { color: #667085; font-size: 15px; margin: 0; }
        .nv-iu-options { margin: 20px 0; }
        .nv-iu-options label { font-size: 14px; color: #344054; cursor: pointer; }
        .nv-iu-btn { background: #3b82f6; color: #fff; border: none; padding: 12px 28px; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: background 0.15s; }
        .nv-iu-btn:hover { background: #2563eb; }
        .nv-iu-results { margin-top: 32px; }
        .nv-iu-results h2 { font-size: 20px; margin-bottom: 16px; }
        .nv-iu-result-section { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; margin-bottom: 16px; }
        .nv-iu-result-section h3 { margin: 0 0 12px; font-size: 16px; }
        .nv-iu-success { border-left: 4px solid #059669; }
        .nv-iu-warning { border-left: 4px solid #f59e0b; }
        .nv-iu-error { border-left: 4px solid #dc2626; }
        .nv-iu-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .nv-iu-table th { text-align: left; padding: 8px; color: #667085; font-weight: 600; border-bottom: 1px solid #e5e7eb; }
        .nv-iu-table td { padding: 8px; border-bottom: 1px solid #f3f4f6; }
        .nv-iu-table code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; }
        ';
    }
}

new NV_Image_Uploader();
