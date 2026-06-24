<?php
/**
 * Plugin Name: Natty Vision Homepage
 * Description: Custom homepage template for Natty Vision — includes logo settings
 * Version: 4.0
 * Author: Natty Vision
 */

if (!defined('ABSPATH')) exit;

// ─── SETTINGS PAGE ───
add_action('admin_menu', function() {
    add_menu_page(
        'Natty Vision Settings',
        'Natty Vision',
        'manage_options',
        'natty-vision',
        'nv_settings_page',
        'dashicons-store',
        30
    );
});

function nv_settings_page() {
    if (isset($_POST['nv_save']) && check_admin_referer('nv_settings')) {
        $messages = [];
        if (isset($_POST['nv_remove_logo'])) {
            delete_option('nv_logo_id');
            $messages[] = ['updated', 'Logo removed.'];
        }
        if (!empty($_FILES['nv_logo']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            // Allow SVG uploads (only while this admin user is uploading)
            $svg_filter = function($mimes) {
                $mimes['svg']  = 'image/svg+xml';
                $mimes['svgz'] = 'image/svg+xml';
                return $mimes;
            };
            add_filter('upload_mimes', $svg_filter);

            // WP also runs a "real MIME check" that can reject SVGs even when the extension is allowed
            $real_mime_filter = function($data, $file, $filename, $mimes) {
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if ($ext === 'svg' || $ext === 'svgz') {
                    $data['ext']  = $ext;
                    $data['type'] = 'image/svg+xml';
                }
                return $data;
            };
            add_filter('wp_check_filetype_and_ext', $real_mime_filter, 10, 4);

            $attachment_id = media_handle_upload('nv_logo', 0);

            remove_filter('upload_mimes', $svg_filter);
            remove_filter('wp_check_filetype_and_ext', $real_mime_filter, 10);

            if (is_wp_error($attachment_id)) {
                $messages[] = ['error', 'Upload failed: ' . esc_html($attachment_id->get_error_message())];
            } else {
                update_option('nv_logo_id', $attachment_id);
                $messages[] = ['updated', 'Logo uploaded.'];
            }
        }
        foreach ($messages as $m) {
            echo '<div class="' . esc_attr($m[0]) . '"><p>' . $m[1] . '</p></div>';
        }
    }

    $logo_id = get_option('nv_logo_id');
    $logo_url = '';
    if ($logo_id) {
        // Try medium first, fall back to full so smaller logos still render
        $logo_url = wp_get_attachment_image_url($logo_id, 'medium');
        if (!$logo_url) {
            $logo_url = wp_get_attachment_image_url($logo_id, 'full');
        }
    }
    ?>
    <div class="wrap">
        <h1>Natty Vision Homepage Settings</h1>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field('nv_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Logo</th>
                    <td>
                        <?php if ($logo_url): ?>
                            <div style="margin-bottom:12px;">
                                <img src="<?php echo esc_url($logo_url); ?>" style="max-height:60px;background:#f2f0eb;padding:10px;border-radius:8px;">
                            </div>
                            <label><input type="checkbox" name="nv_remove_logo" value="1"> Remove current logo</label><br><br>
                        <?php endif; ?>
                        <input type="file" name="nv_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                        <p class="description">Upload PNG, SVG, JPG, or WebP. SVG with transparent background works best. Recommended height: 80–120px. Max file size depends on your server (usually 2–10MB).</p>
                    </td>
                </tr>
            </table>
            <p class="submit"><input type="submit" name="nv_save" class="button-primary" value="Save Settings"></p>
        </form>
        <hr>
        <h2>Quick Guide</h2>
        <ul style="list-style:disc;padding-left:20px;">
            <li>Upload your logo here — it automatically appears in the navbar and footer</li>
            <li>If no logo is uploaded, the default SVG logo is used</li>
            <li>To use this template: create a Page in WordPress, then choose <strong>"Natty Vision Homepage"</strong> from the Page Template dropdown in the sidebar</li>
            <li>To set the page as your homepage: Settings → Reading → "Your homepage displays" → A static page</li>
            <li>To edit page content or links — go to <strong>Plugins → Plugin Editor → Natty Vision Homepage → template.php</strong></li>
        </ul>
    </div>
    <?php
}

function nv_get_logo($height = 38) {
    $logo_id = get_option('nv_logo_id');
    if ($logo_id) {
        // Same fallback chain as the admin preview
        $url = wp_get_attachment_image_url($logo_id, 'medium');
        if (!$url) {
            $url = wp_get_attachment_image_url($logo_id, 'full');
        }
        if ($url) {
            return '<img src="' . esc_url($url) . '" alt="Natty Vision" style="height:' . intval($height) . 'px;width:auto;">';
        }
    }
    return '<svg viewBox="0 0 180 36" xmlns="http://www.w3.org/2000/svg" style="height:' . intval($height) . 'px;width:auto"><g fill="#1a1e1c"><rect x="0" y="0" width="12" height="20"/><rect x="12" y="0" width="12" height="8"/><rect x="24" y="0" width="12" height="36"/><rect x="0" y="28" width="12" height="8"/><rect x="12" y="20" width="12" height="16"/></g><text x="44" y="25" font-family="Neue Montreal,Helvetica,sans-serif" font-size="21" font-weight="500" fill="#1a1e1c" letter-spacing="-0.02em">Natty Vision</text></svg>';
}

// ─── REGISTER TEMPLATE ───

// Helper: find a WooCommerce product by name (cached per request).
function nv_find_product( $name ) {
    static $cache = array();
    $key = strtolower( trim( $name ) );
    if ( isset( $cache[ $key ] ) ) return $cache[ $key ];

    // Try exact title match first via WP_Query.
    $posts = get_posts( array(
        'post_type'   => 'product',
        'post_status' => 'publish',
        'title'       => $name,
        'numberposts' => 1,
    ) );
    if ( ! empty( $posts ) ) {
        $product = wc_get_product( $posts[0]->ID );
        if ( $product ) {
            $cache[ $key ] = $product;
            return $product;
        }
    }

    // Fallback: WooCommerce search.
    $products = wc_get_products( array( 'limit' => 5, 'status' => 'publish', 's' => $name ) );
    if ( ! empty( $products ) ) {
        foreach ( $products as $p ) {
            if ( strtolower( $p->get_name() ) === $key ) {
                $cache[ $key ] = $p;
                return $p;
            }
        }
        // Partial match fallback.
        foreach ( $products as $p ) {
            if ( stripos( $p->get_name(), $name ) !== false ) {
                $cache[ $key ] = $p;
                return $p;
            }
        }
        $cache[ $key ] = $products[0];
        return $products[0];
    }

    $cache[ $key ] = null;
    return null;
}

// Helper: get product permalink by name.
function nv_product_url( $name ) {
    $product = nv_find_product( $name );
    return $product ? get_permalink( $product->get_id() ) : home_url( '/shop/' );
}

// Helper: get product thumbnail URL by name.
function nv_product_img( $name, $size = 'medium' ) {
    $product = nv_find_product( $name );
    if ( $product ) {
        $thumb_id = get_post_thumbnail_id( $product->get_id() );
        if ( $thumb_id ) {
            $url = wp_get_attachment_image_url( $thumb_id, $size );
            if ( $url ) return $url;
        }
    }
    return '';
}

// Helper: get shop page URL filtered by category slug.
function nv_shop_cat_url( $slug ) {
    return home_url( '/shop/?category=' . sanitize_title( $slug ) );
}

// Helper: get product price string by name (e.g. "$79.99" or "$78.00 – $132.00").
function nv_product_price( $name ) {
    $product = nv_find_product( $name );
    if ( $product ) {
        $price = $product->get_price();
        if ( $price ) return '$' . number_format( (float) $price, 2 );
        $price_html = strip_tags( $product->get_price_html() );
        if ( $price_html ) return $price_html;
    }
    return '';
}

add_filter('theme_page_templates', function($templates) {
    $templates['natty-vision-homepage.php'] = 'Natty Vision Homepage';
    return $templates;
});

add_filter('template_include', function($template) {
    if (is_page()) {
        $custom = get_page_template_slug();
        if ($custom === 'natty-vision-homepage.php') {
            return plugin_dir_path(__FILE__) . 'template.php';
        }
    }
    return $template;
});

// ─── WOOCOMMERCE TEMPLATE OVERRIDES ───
// Tell WooCommerce to look in this plugin's /woocommerce/ folder for template overrides.
// Falls back to theme overrides and then the WC defaults if our file isn't found.
add_filter('woocommerce_locate_template', function($template, $template_name, $template_path) {
    $plugin_template = plugin_dir_path(__FILE__) . 'woocommerce/' . $template_name;
    if (file_exists($plugin_template)) {
        return $plugin_template;
    }
    return $template;
}, 10, 3);

// Load brand fonts on Woo order-received and on pages using our homepage template
add_action('wp_enqueue_scripts', function() {
    $load = false;
    if (function_exists('is_order_received_page') && is_order_received_page()) {
        $load = true;
    }
    if (is_page() && get_page_template_slug() === 'natty-vision-homepage.php') {
        $load = true;
    }
    if ($load) {
        wp_enqueue_style('nv-fonts-google', 'https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap', [], null);
        wp_enqueue_style('nv-fonts-fontshare', 'https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap', [], null);
    }
});
