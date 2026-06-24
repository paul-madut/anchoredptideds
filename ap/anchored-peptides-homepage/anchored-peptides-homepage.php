<?php
/**
 * Plugin Name: Anchored Peptides Homepage
 * Description: Custom homepage template for Anchored Peptides — includes logo settings, product-lookup helpers, and an activation routine that scaffolds the brand categories + pages. Data-driven: the homepage pulls best-sellers and categories straight from WooCommerce.
 * Version: 1.0
 * Author: Anchored Peptides
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'APH_FILE', __FILE__ );
define( 'APH_DIR', plugin_dir_path( __FILE__ ) );
define( 'APH_URL', plugin_dir_url( __FILE__ ) );

/* =========================================================
 * ACTIVATION — scaffold categories + pages (idempotent).
 * Applies the "auto-create on activation" efficiency pattern
 * so a fresh site wires up its taxonomy + pages automatically.
 * ========================================================= */
register_activation_hook( __FILE__, 'aph_activate' );
function aph_activate() {
    // 1) Product categories (parent "Peptides" + children with exact slugs the theme expects).
    if ( taxonomy_exists( 'product_cat' ) ) {
        $parent = term_exists( 'peptides', 'product_cat' );
        if ( ! $parent ) $parent = wp_insert_term( 'Peptides', 'product_cat', array( 'slug' => 'peptides' ) );
        $parent_id = is_array( $parent ) ? (int) $parent['term_id'] : 0;

        // Natty Vision category scheme (1:1 with the source catalog).
        $children = array(
            'Weight Loss' => 'weight-loss',
            'Energy'      => 'energy',
            'Healing'     => 'healing',
            'Skin'        => 'skin',
            'Brain'       => 'brain',
            'Stacks'      => 'stacks',
            'Supplies'    => 'supplies',
        );
        foreach ( $children as $name => $slug ) {
            if ( ! term_exists( $slug, 'product_cat' ) ) {
                wp_insert_term( $name, 'product_cat', array( 'slug' => $slug, 'parent' => $parent_id ) );
            }
        }
    }

    // 2) Pages with their templates.
    $pages = array(
        'home'        => array( 'title' => 'Home',        'template' => 'anchored-peptides-homepage.php', 'slug' => 'home' ),
        'coa-library' => array( 'title' => 'COA Library', 'template' => 'page-coa-library.php',           'slug' => 'coa-library' ),
        'learn'       => array( 'title' => 'Learn',       'template' => 'page-learn.php',                 'slug' => 'learn' ),
    );
    $created = array();
    foreach ( $pages as $key => $p ) {
        $existing = get_page_by_path( $p['slug'] );
        if ( $existing ) { $created[ $key ] = $existing->ID; continue; }
        $id = wp_insert_post( array(
            'post_title'   => $p['title'],
            'post_name'    => $p['slug'],
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ) );
        if ( $id && ! is_wp_error( $id ) ) {
            update_post_meta( $id, '_wp_page_template', $p['template'] );
            $created[ $key ] = $id;
        }
    }

    // 3) Set the static front page to Home.
    if ( ! empty( $created['home'] ) ) {
        update_option( 'show_on_front', 'page' );
        update_option( 'page_on_front', $created['home'] );
    }

    // 4) Pretty permalinks (the shop filter + COA query rely on them).
    if ( '' === get_option( 'permalink_structure' ) ) {
        update_option( 'permalink_structure', '/%postname%/' );
        if ( function_exists( 'flush_rewrite_rules' ) ) flush_rewrite_rules();
    }
}

/* =========================================================
 * SETTINGS PAGE — logo upload (shared ap_logo_id option).
 * ========================================================= */
add_action( 'admin_menu', function () {
    add_menu_page( 'Anchored Peptides', 'Anchored Peptides', 'manage_options', 'anchored-peptides', 'aph_settings_page', 'dashicons-anchor', 30 );
} );

function aph_settings_page() {
    if ( isset( $_POST['aph_save'] ) && check_admin_referer( 'aph_settings' ) ) {
        if ( isset( $_POST['aph_remove_logo'] ) ) { delete_option( 'ap_logo_id' ); echo '<div class="updated"><p>Logo removed.</p></div>'; }
        if ( ! empty( $_FILES['aph_logo']['name'] ) ) {
            require_once ABSPATH . 'wp-admin/includes/image.php';
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            $svg = function ( $m ) { $m['svg'] = 'image/svg+xml'; return $m; };
            add_filter( 'upload_mimes', $svg );
            $aid = media_handle_upload( 'aph_logo', 0 );
            remove_filter( 'upload_mimes', $svg );
            if ( is_wp_error( $aid ) ) {
                echo '<div class="error"><p>Upload failed: ' . esc_html( $aid->get_error_message() ) . '</p></div>';
            } else {
                // Scrub uploaded SVGs (script/handlers/foreignObject) to prevent stored XSS.
                $path = get_attached_file( $aid );
                if ( $path && preg_match( '/\.svgz?$/i', $path ) ) aph_sanitize_svg_file( $path );
                update_option( 'ap_logo_id', $aid );
                echo '<div class="updated"><p>Logo uploaded.</p></div>';
            }
        }
    }
    $logo_id  = get_option( 'ap_logo_id' );
    $logo_url = $logo_id ? ( wp_get_attachment_image_url( $logo_id, 'medium' ) ?: wp_get_attachment_image_url( $logo_id, 'full' ) ) : '';
    ?>
    <div class="wrap">
        <h1>Anchored Peptides Settings</h1>
        <form method="post" enctype="multipart/form-data">
            <?php wp_nonce_field( 'aph_settings' ); ?>
            <table class="form-table"><tr><th scope="row">Logo</th><td>
                <?php if ( $logo_url ) : ?>
                    <div style="margin-bottom:12px"><img src="<?php echo esc_url( $logo_url ); ?>" style="max-height:60px;background:#ECE7DA;padding:10px;border-radius:8px"></div>
                    <label><input type="checkbox" name="aph_remove_logo" value="1"> Remove current logo</label><br><br>
                <?php endif; ?>
                <input type="file" name="aph_logo" accept="image/png,image/jpeg,image/svg+xml,image/webp">
                <p class="description">PNG, SVG, JPG or WebP. Shows in the navbar, footer and homepage.</p>
            </td></tr></table>
            <p class="submit"><input type="submit" name="aph_save" class="button-primary" value="Save Settings"></p>
        </form>
        <hr>
        <h2>Quick guide</h2>
        <ul style="list-style:disc;padding-left:20px">
            <li>Categories + Home / COA Library / Learn pages were created automatically on activation.</li>
            <li>The homepage best-sellers pull live from WooCommerce — mark products <strong>Featured</strong> (or they fall back to top-rated) to control them.</li>
            <li>Edit homepage copy in <strong>Plugins → Plugin Editor → Anchored Peptides Homepage → template.php</strong>.</li>
        </ul>
    </div>
    <?php
}

/* =========================================================
 * LOGO renderer (mirrors theme's ap_render_logo()).
 * ========================================================= */
function ap_get_logo( $height = 34 ) {
    $logo_id = get_option( 'ap_logo_id' );
    if ( $logo_id ) {
        $url = wp_get_attachment_image_url( $logo_id, 'medium' ) ?: wp_get_attachment_image_url( $logo_id, 'full' );
        if ( $url ) return '<img src="' . esc_url( $url ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" style="height:' . intval( $height ) . 'px;width:auto">';
    }
    $name = get_bloginfo( 'name' ) ?: 'Anchored Peptides';
    return '<span class="ap-logo"><svg width="' . intval( $height ) . '" height="' . intval( $height ) . '" viewBox="0 0 40 40" fill="none" stroke="#3E412E" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="20" cy="9" r="3.5"/><line x1="20" y1="12.5" x2="20" y2="31"/><line x1="13" y1="16" x2="27" y2="16"/><path d="M11 24c0 6 9 9.5 9 9.5s9-3.5 9-9.5"/></svg><span style="font-family:var(--ap-serif);font-weight:600;font-size:19px;color:var(--ap-ink)">' . esc_html( $name ) . '</span></span>';
}

/* =========================================================
 * PRODUCT HELPERS (for any name-based references).
 * ========================================================= */
function ap_find_product( $name ) {
    static $cache = array();
    $key = strtolower( trim( $name ) );
    if ( array_key_exists( $key, $cache ) ) return $cache[ $key ]; // array_key_exists so cached nulls hit
    if ( ! function_exists( 'wc_get_product' ) ) return $cache[ $key ] = null;
    $posts = get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'title' => $name, 'numberposts' => 1 ) );
    if ( ! empty( $posts ) ) return $cache[ $key ] = wc_get_product( $posts[0]->ID );
    $found = wc_get_products( array( 'limit' => 5, 'status' => 'publish', 's' => $name ) );
    foreach ( $found as $p ) { if ( strtolower( $p->get_name() ) === $key ) return $cache[ $key ] = $p; }
    return $cache[ $key ] = ( ! empty( $found ) ? $found[0] : null );
}
function aph_img( $file ) { return APH_URL . 'images/' . ltrim( $file, '/' ); }

/**
 * Basic SVG sanitizer for uploaded logos — removes script/foreignObject/iframe/
 * embed/object elements, on* event-handler attributes, and javascript:/data:
 * URIs. Admin-only (upload is behind manage_options), this is defense-in-depth
 * against stored XSS. For untrusted multi-author sites use a vetted library.
 */
function aph_sanitize_svg_file( $path ) {
    $svg = @file_get_contents( $path );
    if ( ! $svg ) return;
    if ( ! class_exists( 'DOMDocument' ) ) return;
    libxml_use_internal_errors( true );
    // Block external entity loading on libxml < 2.9 / PHP < 8 (no-op otherwise).
    if ( function_exists( 'libxml_disable_entity_loader' ) ) {
        $prev_loader = @libxml_disable_entity_loader( true );
    }
    $dom = new DOMDocument();
    $ok  = $dom->loadXML( $svg, LIBXML_NONET );
    if ( $ok ) {
        foreach ( array( 'script', 'foreignObject', 'iframe', 'embed', 'object', 'animate', 'set', 'handler' ) as $tag ) {
            $nodes = $dom->getElementsByTagName( $tag );
            for ( $i = $nodes->length - 1; $i >= 0; $i-- ) {
                $n = $nodes->item( $i );
                if ( $n && $n->parentNode ) $n->parentNode->removeChild( $n );
            }
        }
        $xpath = new DOMXPath( $dom );
        foreach ( $xpath->query( '//*' ) as $el ) {
            if ( ! $el->attributes ) continue;
            for ( $i = $el->attributes->length - 1; $i >= 0; $i-- ) {
                $attr = $el->attributes->item( $i );
                $name = strtolower( $attr->nodeName );
                $val  = strtolower( trim( (string) $attr->nodeValue ) );
                if ( strpos( $name, 'on' ) === 0
                    || strpos( $val, 'javascript:' ) !== false
                    || ( in_array( $name, array( 'href', 'xlink:href' ), true ) && ( strpos( $val, 'data:' ) === 0 || strpos( $val, 'javascript:' ) === 0 ) ) ) {
                    $el->removeAttribute( $attr->nodeName );
                }
            }
        }
        $clean = $dom->saveXML();
        if ( $clean ) @file_put_contents( $path, $clean );
    }
    if ( function_exists( 'libxml_disable_entity_loader' ) && isset( $prev_loader ) ) {
        @libxml_disable_entity_loader( $prev_loader );
    }
    libxml_clear_errors();
}
function ap_product_url( $name ) { $p = ap_find_product( $name ); return $p ? get_permalink( $p->get_id() ) : home_url( '/shop/' ); }
function ap_product_price( $name ) { $p = ap_find_product( $name ); return $p ? wp_strip_all_tags( $p->get_price_html() ) : ''; }
function ap_shop_cat_url( $slug ) { return home_url( '/shop/?category=' . sanitize_title( $slug ) ); }

/**
 * Best-sellers for the homepage: featured products first, topped up with
 * top-rated, so the grid is always full WITHOUT hardcoding product names.
 */
function ap_homepage_products( $limit = 10 ) {
    if ( ! function_exists( 'wc_get_products' ) ) return array();
    $featured = wc_get_products( array( 'status' => 'publish', 'limit' => $limit, 'featured' => true, 'orderby' => 'menu_order', 'order' => 'ASC' ) );
    $picked = array();
    $ids    = array(); // id => true (built by foreach; never wp_list_pluck on objects)
    foreach ( $featured as $p ) { $picked[] = $p; $ids[ $p->get_id() ] = true; }
    // Top up with popular then newest so the grid always fills, even when few
    // products carry a rating/sales meta (a plain rating query would exclude them).
    foreach ( array( 'popularity', 'date' ) as $orderby ) {
        if ( count( $picked ) >= $limit ) break;
        $more = wc_get_products( array( 'status' => 'publish', 'limit' => $limit * 2, 'orderby' => $orderby, 'exclude' => array_keys( $ids ) ) );
        foreach ( $more as $p ) {
            if ( isset( $ids[ $p->get_id() ] ) ) continue;
            $picked[] = $p; $ids[ $p->get_id() ] = true;
            if ( count( $picked ) >= $limit ) break;
        }
    }
    return array_slice( $picked, 0, $limit );
}

/**
 * Category cards for the homepage "browse by goal" — pulled from product_cat.
 */
function ap_homepage_categories() {
    $order = array( 'weight-loss', 'energy', 'healing', 'skin', 'brain', 'stacks' );
    $out   = array();
    foreach ( $order as $slug ) {
        $term = get_term_by( 'slug', $slug, 'product_cat' );
        if ( $term && ! is_wp_error( $term ) ) $out[] = $term;
    }
    return $out;
}

/* =========================================================
 * TEMPLATE REGISTRATION.
 * ========================================================= */
add_filter( 'theme_page_templates', function ( $t ) { $t['anchored-peptides-homepage.php'] = 'Anchored Peptides Homepage'; return $t; } );
add_filter( 'template_include', function ( $template ) {
    if ( is_page() && get_page_template_slug() === 'anchored-peptides-homepage.php' ) {
        return APH_DIR . 'template.php';
    }
    return $template;
} );

// Route WooCommerce template overrides through this plugin's /woocommerce/ folder too.
add_filter( 'woocommerce_locate_template', function ( $template, $template_name ) {
    $candidate = APH_DIR . 'woocommerce/' . $template_name;
    return file_exists( $candidate ) ? $candidate : $template;
}, 10, 2 );

// Load brand fonts + tokens on the order-received page and homepage template.
add_action( 'wp_enqueue_scripts', function () {
    $load = ( function_exists( 'is_order_received_page' ) && is_order_received_page() )
        || ( is_page() && get_page_template_slug() === 'anchored-peptides-homepage.php' );
    if ( $load && function_exists( 'ap_fonts_url' ) ) {
        wp_enqueue_style( 'ap-fonts', ap_fonts_url(), array(), null );
        wp_enqueue_style( 'ap-tokens', get_template_directory_uri() . '/assets/css/tokens.css', array(), '1.0.0' );
    }
} );
