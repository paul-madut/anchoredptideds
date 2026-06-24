<?php
/**
 * Plugin Name: Natty Vision - Affiliate Coupon Sync & Portal Restyle
 * Description: Affiliate coupon sync, Portal restyle, custom signup/login pages, public /affiliates landing page, dashboard guide, and Request Lander feature.
 * Version: 3.15.1
 * Author: Natty Vision
 */

if (!defined('ABSPATH')) exit;

/* ============================================================
 * SETTINGS
 * ============================================================ */
define('NVACS_TEMPLATE_COUPON_SLUG', 'template-affiliate');
define('NVACS_VERSION', '3.15.1');

/**
 * Returns the Natty Vision horizontal logo as inline SVG.
 * Inline so it loads instantly and matches the current background color.
 */
function nvacs_logo_svg($height = 32, $color = '#1a1e1c') {
    $color = esc_attr($color);
    $height = (int) $height;
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1080 184" height="' . $height . '" fill="none" style="display:block;height:' . $height . 'px;width:auto;" aria-label="Natty Vision"><path d="M91.6455 0.00012207V83.8058C91.6455 85.1548 92.1804 86.4528 93.1348 87.4073L95.8779 90.1505C96.8324 91.105 98.1305 91.6397 99.4795 91.6398H129.625C130.974 91.6398 132.273 91.1051 133.228 90.1505L135.97 87.4073C136.924 86.4528 137.459 85.1549 137.459 83.8058V45.8195H137.453L183.272 0.00012207V137.459L137.453 183.279V145.293C137.453 143.944 136.918 142.646 135.964 141.692L133.221 138.948C132.266 137.994 130.968 137.459 129.619 137.459H96.7246C93.9118 137.459 91.6328 135.181 91.6328 132.368V99.4738C91.6328 98.1246 91.0991 96.8258 90.1445 95.8712L87.4014 93.129C86.4468 92.1744 85.148 91.6398 83.7988 91.6398H59.3809C58.0318 91.6398 56.7338 92.1745 55.7793 93.129L47.3086 101.599C46.354 102.553 45.8193 103.852 45.8193 105.201V183.279H0V137.459L44.3369 93.1349C45.2914 92.1803 45.8261 90.8824 45.8262 89.5333V0.00012207H91.6455ZM594.573 115.152H594.9L616.66 50.2257H635.251L601.39 140.385C596.583 153.492 591.1 163.957 574.824 163.957C569.843 163.957 565.692 163.127 562.371 162.297V149.015H563.857C571.154 150.02 576.637 149.516 580.46 144.863C583.606 141.04 585.944 134.399 582.776 126.425L552.06 50.2257H570.826L594.573 115.152ZM405.732 47.9083C424.499 47.9083 438.437 55.052 438.437 77.6193V117.97C438.437 123.955 439.77 127.101 446.563 126.445V134.747C442.588 136.233 439.747 136.582 436.427 136.582C427.121 136.582 422.817 133.261 421.156 124.96H420.828C415.519 132.431 405.885 137.587 392.777 137.587C375.519 137.587 364.88 127.625 364.88 113.338C364.88 94.7469 378.665 88.9351 399.922 84.7843C413.029 82.2939 421.003 80.6335 421.003 72.507V72.4855C421.003 66.3466 417.355 61.6935 405.732 61.6935C391.795 61.6936 386.639 66.0191 385.809 76.964H368.878C369.708 60.6886 380.172 47.9085 405.732 47.9083ZM826.794 47.8878C849.864 47.8879 860.83 60.3405 861.988 75.1085H845.058C843.9 68.4673 840.579 61.6516 826.969 61.6515C816.504 61.6516 810.867 65.6276 810.867 71.6134C810.868 80.2421 820.328 80.9194 832.627 83.7374L832.648 83.7814C848.247 87.4297 865.2 90.9253 865.2 111.177C865.2 127.452 851.261 137.568 830.66 137.568C803.265 137.567 791.468 124.786 790.638 106.851H807.568C808.399 115.982 812.375 123.782 830.311 123.782C842.435 123.782 847.569 117.796 847.569 112.487C847.569 101.87 837.433 101.04 824.98 98.047C811.523 94.9012 793.259 92.2357 793.259 72.9679C793.259 59.03 805.538 47.8879 826.794 47.8878ZM951.388 47.9083C977.953 47.9085 994.054 66.3464 994.054 92.7365C994.054 119.127 977.953 137.565 951.388 137.566C924.823 137.565 908.721 119.127 908.721 92.7365C908.721 66.3464 924.823 47.9085 951.388 47.9083ZM479.745 50.2452H495.693V64.5333L495.671 64.5109H479.724V110.498C479.724 120.132 484.377 121.29 495.671 120.635V134.922C492.176 135.752 488.374 136.255 483.722 136.255C469.784 136.255 462.137 129.941 462.137 112.005V64.5109H449.859V50.2238H462.137V23.506H479.745V50.2452ZM530.974 50.2452H546.921V64.5333L546.899 64.5109H530.951V110.498C530.951 120.132 535.605 121.29 546.899 120.635V134.922C543.404 135.752 539.602 136.255 534.949 136.255C521.012 136.255 513.365 129.94 513.365 112.005V64.5109H501.087V50.2238H513.365V23.506H530.974V50.2452ZM721.911 113.492H722.086L718.939 16.3634H741.201V135.076H718.438L632.209 16.3634H654.471L721.911 113.492ZM895.484 135.076H878.051V50.2257H895.484V135.076ZM330.815 105.518H331.144V16.3624H350.893V135.075H328.98L275.369 46.2482H275.042V135.075H255.271V16.3624H277.357L330.815 105.518ZM778.254 135.075H760.82V50.2247H778.254V135.075ZM1051.77 47.9083C1067.55 47.9083 1080 57.0398 1080 76.4611V135.075H1062.39V81.4425C1062.39 69.995 1057.58 62.6759 1045.61 62.6759C1032.15 62.6762 1023.53 70.8039 1023.53 83.4308V135.075H1006.09V50.2238H1023.53V61.1906H1023.85C1028.33 54.5493 1037.14 47.9084 1051.75 47.9083H1051.77ZM421.003 90.0714C418.184 92.0593 411.369 94.0479 403.242 95.7081C388.802 98.8539 382.488 103.005 382.488 111.655C382.488 119.454 387.141 123.78 396.601 123.78C411.041 123.78 421.003 116.637 421.003 102.852V90.0714ZM951.388 62.1954C934.785 62.1956 926.154 75.1505 926.154 92.7365C926.154 110.323 934.785 123.278 951.388 123.278C967.991 123.278 976.62 110.17 976.62 92.7365C976.62 75.3034 967.991 62.1956 951.388 62.1954Z" fill="' . $color . '"/></svg>';
}

/* ============================================================
 * COUPON CODE EXTRACTION HELPERS
 * ============================================================ */

function nvacs_extract_coupon_code($coupon_data) {
    if (empty($coupon_data)) return '';
    if (is_string($coupon_data)) return trim($coupon_data);
    if (is_object($coupon_data)) {
        if (isset($coupon_data->coupon_code)) return trim((string) $coupon_data->coupon_code);
        if (isset($coupon_data->code))        return trim((string) $coupon_data->code);
        return '';
    }
    if (is_array($coupon_data)) {
        if (isset($coupon_data['coupon_code'])) return trim((string) $coupon_data['coupon_code']);
        if (isset($coupon_data['code']))        return trim((string) $coupon_data['code']);
        $first = reset($coupon_data);
        if ($first !== false) return nvacs_extract_coupon_code($first);
    }
    return '';
}

function nvacs_get_template_discount() {
    static $cache = null;
    if ($cache !== null) return $cache;
    $defaults = ['type' => 'percent', 'amount' => 5.0];
    if (!function_exists('wc_get_coupon_id_by_code')) { $cache = $defaults; return $cache; }
    $template_id = wc_get_coupon_id_by_code(NVACS_TEMPLATE_COUPON_SLUG);
    if (!$template_id) { $cache = $defaults; return $cache; }
    $template = new WC_Coupon($template_id);
    $cache = [
        'type'   => $template->get_discount_type() ?: 'percent',
        'amount' => (float) $template->get_amount() ?: 5.0,
    ];
    return $cache;
}

function nvacs_create_wc_coupon_for_code($code) {
    if (!is_string($code) || $code === '') return false;
    if (!class_exists('WC_Coupon') || !function_exists('wc_get_coupon_id_by_code')) return false;
    if (wc_get_coupon_id_by_code($code)) return false;
    $d = nvacs_get_template_discount();
    try {
        $coupon = new WC_Coupon();
        $coupon->set_code($code);
        $coupon->set_discount_type($d['type']);
        $coupon->set_amount($d['amount']);
        $coupon->set_individual_use(false);
        $coupon->save();
        return true;
    } catch (\Exception $e) {
        error_log('[NVACS] Failed creating coupon for code ' . $code . ': ' . $e->getMessage());
        return false;
    }
}

function nvacs_sync_affiliate_coupons($affiliate_id) {
    if (!$affiliate_id || !function_exists('affwp_get_affiliate_coupons')) return;
    $dynamic_coupons = affwp_get_affiliate_coupons($affiliate_id);
    if (empty($dynamic_coupons)) return;
    if (!is_array($dynamic_coupons)) $dynamic_coupons = [$dynamic_coupons];
    foreach ($dynamic_coupons as $coupon_data) {
        $code = nvacs_extract_coupon_code($coupon_data);
        if ($code !== '') nvacs_create_wc_coupon_for_code($code);
    }
}

function nvacs_sync_all_affiliates() {
    if (!function_exists('affiliate_wp') || !class_exists('WC_Coupon')) {
        return ['created' => 0, 'skipped' => 0, 'codes' => []];
    }
    $affiliates = affiliate_wp()->affiliates->get_affiliates(['number' => -1]);
    $created = 0; $skipped = 0; $codes_made = [];
    foreach ($affiliates as $affiliate) {
        $dynamic_coupons = affwp_get_affiliate_coupons($affiliate->affiliate_id);

        // CASE 1: affiliate has dynamic coupons → mirror them as real WC coupons
        if (!empty($dynamic_coupons)) {
            if (!is_array($dynamic_coupons)) $dynamic_coupons = [$dynamic_coupons];
            foreach ($dynamic_coupons as $coupon_data) {
                $code = nvacs_extract_coupon_code($coupon_data);
                if ($code === '') continue;
                if (wc_get_coupon_id_by_code($code)) { $skipped++; continue; }
                if (nvacs_create_wc_coupon_for_code($code)) { $created++; $codes_made[] = $code; }
            }
            continue;
        }

        // CASE 2: affiliate has NO dynamic coupon (custom signup form) →
        // create one from their username
        $user = get_userdata($affiliate->user_id);
        if (!$user || empty($user->user_login)) continue;
        $code = strtoupper($user->user_login);
        if (wc_get_coupon_id_by_code($code)) { $skipped++; continue; }
        if (nvacs_create_coupon_for_new_affiliate($affiliate->affiliate_id, $user->user_login)) {
            $created++;
            $codes_made[] = $code;
        }
    }
    return ['created' => $created, 'skipped' => $skipped, 'codes' => $codes_made];
}

/**
 * Get auto-apply coupon URL for an affiliate.
 * Returns: https://site.com/?nv_coupon=5BUDDIMA
 * The home page picks up the param, applies the coupon to the session, and redirects to clean /
 */
function nvacs_get_affiliate_coupon_url($affiliate_id = 0) {
    if (!$affiliate_id && function_exists('affwp_get_affiliate_id')) {
        $affiliate_id = affwp_get_affiliate_id();
    }
    if (!$affiliate_id || !function_exists('affwp_get_affiliate_coupons')) return '';
    $dynamic_coupons = affwp_get_affiliate_coupons($affiliate_id);
    if (empty($dynamic_coupons)) return '';
    if (!is_array($dynamic_coupons)) $dynamic_coupons = [$dynamic_coupons];
    $first = reset($dynamic_coupons);
    $code  = nvacs_extract_coupon_code($first);
    if ($code === '') return '';
    return home_url('/?nv_coupon=' . strtolower($code));
}

/* ============================================================
 * FRONT-END: Catch ?nv_coupon=CODE on any page
 *
 * v2.0 behavior:
 *  1. Visitor hits nattyvision.com/?nv_coupon=5buddima
 *  2. wp_loaded fires: validate coupon, save to WC session WITH timestamp
 *  3. If a cart already exists, apply the coupon RIGHT NOW
 *  4. Redirect to clean URL (no ?nv_coupon visible)
 *  5. On every subsequent page load:
 *     - If older than 3 hours → silently clear it, no apply
 *     - If cart non-empty and coupon not applied → apply it
 *  6. If user manually removes the coupon at cart/checkout → it stays
 *     removed (we don't keep re-applying it). No commission credited.
 *  7. After order completes → clear the session entirely
 *
 * Self-referral / IP-match is handled by AffiliateWP at the commission
 * layer — independent of this flow.
 * ============================================================ */

define('NVACS_COUPON_TTL', 3 * HOUR_IN_SECONDS); // 3 hours

// Step 1: Capture the coupon code from URL
add_action('wp_loaded', 'nvacs_capture_coupon_from_url', 5);
function nvacs_capture_coupon_from_url() {
    if (empty($_GET['nv_coupon'])) return;
    if (is_admin()) return;
    if (!function_exists('WC')) return;
    if (!WC()->session) return;

    $code = sanitize_text_field(wp_unslash($_GET['nv_coupon']));
    $code = strtolower(trim($code));
    if ($code === '') return;

    // Start WC session for guests
    if (!WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }

    // Validate the coupon exists
    if (!function_exists('wc_get_coupon_id_by_code') || !wc_get_coupon_id_by_code($code)) {
        wp_safe_redirect(remove_query_arg('nv_coupon'));
        exit;
    }

    // === AffiliateWP visit tracking ===
    // The coupon links to an affiliate via affwp_discount_affiliate post meta.
    // Find that affiliate and record a visit so it shows up in their dashboard.
    nvacs_track_affiliate_visit_from_coupon($code);

    // Save in WC session WITH a timestamp
    WC()->session->set('nv_pending_coupon',         $code);
    WC()->session->set('nv_pending_coupon_time',    time());
    WC()->session->set('nv_pending_coupon_removed', false); // user has not manually removed

    // If cart has items, apply now
    if (WC()->cart && !WC()->cart->is_empty() && !WC()->cart->has_discount($code)) {
        WC()->cart->apply_coupon($code);
    }

    // Clean redirect — strip the query param
    wp_safe_redirect(remove_query_arg('nv_coupon'));
    exit;
}

/* ============================================================
 * AFFILIATEWP VISIT TRACKING FOR ?nv_coupon=CODE LINKS
 *
 * AffiliateWP normally tracks visits via /?ref=AFFILIATE_ID. We
 * use /?nv_coupon=CODE instead (for auto-apply), so AffiliateWP
 * misses the visit. This bridges the gap: when someone hits
 * ?nv_coupon=CODE, look up which affiliate owns that coupon
 * and record a visit + set the referral cookie for them.
 *
 * After this, the affiliate's dashboard shows visits, and any
 * resulting order is correctly attributed.
 * ============================================================ */
function nvacs_affiliate_trace($msg) {
    $trace = get_option('nvacs_affiliate_trace', []);
    if (!is_array($trace)) $trace = [];
    $trace[] = '[' . gmdate('H:i:s') . '] ' . $msg;
    // Keep last 50 entries only
    if (count($trace) > 50) $trace = array_slice($trace, -50);
    update_option('nvacs_affiliate_trace', $trace, false);
    error_log('[NVACS Affiliate] ' . $msg);
}

function nvacs_track_affiliate_visit_from_coupon($code) {
    nvacs_affiliate_trace('tracker called for code: ' . $code);

    if (!function_exists('affiliate_wp')) {
        nvacs_affiliate_trace('BAIL: affiliate_wp() not defined');
        return;
    }
    if (!function_exists('wc_get_coupon_id_by_code')) {
        nvacs_affiliate_trace('BAIL: wc_get_coupon_id_by_code not defined');
        return;
    }

    $coupon_id = wc_get_coupon_id_by_code($code);
    if (!$coupon_id) {
        nvacs_affiliate_trace('BAIL: no coupon for code ' . $code);
        return;
    }
    nvacs_affiliate_trace('coupon_id: ' . $coupon_id);

    $affiliate_id = (int) get_post_meta($coupon_id, 'affwp_discount_affiliate', true);
    if (!$affiliate_id) {
        nvacs_affiliate_trace('BAIL: coupon not linked to affiliate');
        return;
    }
    nvacs_affiliate_trace('affiliate_id: ' . $affiliate_id);

    if (function_exists('affwp_is_active_affiliate') && !affwp_is_active_affiliate($affiliate_id)) {
        nvacs_affiliate_trace('BAIL: affiliate not active');
        return;
    }

    // Don't double-count in same session
    $existing_ref = isset($_COOKIE['affwp_ref']) ? absint($_COOKIE['affwp_ref']) : 0;
    if ($existing_ref === $affiliate_id) {
        nvacs_affiliate_trace('SKIP: affwp_ref cookie already set for this affiliate');
        return;
    }

    $referrer = isset($_SERVER['HTTP_REFERER']) ? esc_url_raw($_SERVER['HTTP_REFERER']) : '';
    $landing  = home_url(add_query_arg(null, null));

    $visit_id = 0;

    // Try the official AffiliateWP API first
    if (function_exists('affwp_add_visit')) {
        nvacs_affiliate_trace('calling affwp_add_visit()');
        $visit_id = affwp_add_visit([
            'affiliate_id' => $affiliate_id,
            'url'          => $landing,
            'referrer'     => $referrer,
            'campaign'     => 'nv_coupon',
            'context'      => 'nv_coupon_link',
        ]);
        nvacs_affiliate_trace('affwp_add_visit returned: ' . var_export($visit_id, true));
    } else {
        nvacs_affiliate_trace('affwp_add_visit() not defined, will try direct DB');
    }

    // Fallback: if affwp_add_visit() failed (returned 0/false), insert directly
    // into the visits table. This bypasses any AffiliateWP filters that may
    // be blocking admin-user visits.
    if (!$visit_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'affiliate_wp_visits';

        $insert_result = $wpdb->insert($table, [
            'affiliate_id' => $affiliate_id,
            'referral_id'  => 0,
            'rest_id'      => '',
            'url'          => $landing,
            'referrer'     => $referrer,
            'campaign'     => 'nv_coupon',
            'context'      => 'nv_coupon_link',
            'ip'           => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
            'date'         => current_time('mysql'),
        ], ['%d', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']);

        if ($insert_result) {
            $visit_id = $wpdb->insert_id;
            nvacs_affiliate_trace('DIRECT DB insert succeeded, visit_id=' . $visit_id);
        } else {
            nvacs_affiliate_trace('DIRECT DB insert FAILED. last_error: ' . $wpdb->last_error);
            return;
        }
    }

    if ($visit_id) {
        $cookie_exp_days = (function_exists('affiliate_wp') && affiliate_wp()->settings)
            ? (int) affiliate_wp()->settings->get('cookie_exp', 3)
            : 3;
        $expire = time() + (DAY_IN_SECONDS * max(1, $cookie_exp_days));

        setcookie('affwp_ref', $affiliate_id, [
            'expires'  => $expire,
            'path'     => '/',
            'secure'   => is_ssl(),
            'httponly' => false,
            'samesite' => 'Lax',
        ]);
        setcookie('affwp_ref_visit_id', $visit_id, [
            'expires'  => $expire,
            'path'     => '/',
            'secure'   => is_ssl(),
            'httponly' => false,
            'samesite' => 'Lax',
        ]);

        $_COOKIE['affwp_ref'] = $affiliate_id;
        $_COOKIE['affwp_ref_visit_id'] = $visit_id;

        nvacs_affiliate_trace('SUCCESS: visit ' . $visit_id . ' tracked + cookies set');
    }
}

/**
 * Helper: get the pending coupon if it's still valid (under 3 hours old
 * AND user hasn't manually removed it). Otherwise returns '' and cleans up.
 */
function nvacs_get_valid_pending_coupon() {
    if (!function_exists('WC') || !WC()->session) return '';

    $code = WC()->session->get('nv_pending_coupon');
    if (empty($code)) return '';

    // Check if user manually removed it
    if (WC()->session->get('nv_pending_coupon_removed')) return '';

    // Check 3-hour TTL
    $set_at = (int) WC()->session->get('nv_pending_coupon_time');
    if ($set_at > 0 && (time() - $set_at) > NVACS_COUPON_TTL) {
        WC()->session->__unset('nv_pending_coupon');
        WC()->session->__unset('nv_pending_coupon_time');
        WC()->session->__unset('nv_pending_coupon_removed');
        return '';
    }

    return $code;
}

// Step 2: Apply pending coupon as soon as ANY product hits the cart
add_action('woocommerce_add_to_cart', 'nvacs_apply_pending_coupon_on_add', 10, 0);
function nvacs_apply_pending_coupon_on_add() {
    if (!function_exists('WC') || !WC()->cart) return;
    $code = nvacs_get_valid_pending_coupon();
    if ($code === '') return;
    if (!WC()->cart->has_discount($code)) {
        WC()->cart->apply_coupon($code);
    }
}

// Step 3: Also try when cart is loaded from session (visitor returns later)
add_action('woocommerce_cart_loaded_from_session', 'nvacs_apply_pending_coupon_on_load', 20);
function nvacs_apply_pending_coupon_on_load() {
    if (!function_exists('WC') || !WC()->cart) return;
    if (WC()->cart->is_empty()) return;
    $code = nvacs_get_valid_pending_coupon();
    if ($code === '') return;
    if (!WC()->cart->has_discount($code)) {
        WC()->cart->apply_coupon($code);
    }
}

// Step 4: If user MANUALLY removes the coupon, mark it as removed so we
// don't keep re-applying it. This is what enables "remove discount → no commission".
add_action('woocommerce_removed_coupon', 'nvacs_handle_coupon_removed', 10, 1);
function nvacs_handle_coupon_removed($removed_code) {
    if (!function_exists('WC') || !WC()->session) return;
    $pending = WC()->session->get('nv_pending_coupon');
    if (empty($pending)) return;
    if (strcasecmp($pending, $removed_code) === 0) {
        WC()->session->set('nv_pending_coupon_removed', true);
    }
}

// Step 5: Clear everything after a successful order
add_action('woocommerce_thankyou', function($order_id) {
    if (function_exists('WC') && WC()->session) {
        WC()->session->__unset('nv_pending_coupon');
        WC()->session->__unset('nv_pending_coupon_time');
        WC()->session->__unset('nv_pending_coupon_removed');
    }
});

/* ============================================================
 * FIX: COUPON-ONLY REFERRAL ATTRIBUTION
 *
 * Problem: the affwp_ref cookie (set when a customer visits via
 * ?nv_coupon=CODE) causes AffiliateWP to credit that affiliate even
 * if the customer removes the coupon and enters a different one at
 * checkout — or applies no affiliate-linked coupon at all.
 *
 * Fix: filter affwp_insert_pending_referral. Before AffiliateWP
 * writes the referral row, confirm that at least one coupon on the
 * order is linked to the credited affiliate via the
 * affwp_discount_affiliate post meta. Return false to abort if not.
 *
 * Visits are intentionally NOT affected — those are still recorded
 * correctly by nvacs_track_affiliate_visit_from_coupon().
 * ============================================================ */
/**
 * Resolve which affiliate owns a given coupon code.
 *
 * The June 9 build read ONLY the affwp_discount_affiliate post meta — a
 * mapping this plugin maintains. When that meta was missing or stale (e.g.
 * dynamic coupons created through AffiliateWP's own UI, coupons made before
 * the mirror ran, or meta that got wiped) the lookup returned 0 and every
 * referral on that coupon was wrongly rejected.
 *
 * This resolver checks THREE sources and returns the first hit:
 *   1. affwp_discount_affiliate post meta (this plugin's mapping)
 *   2. AffiliateWP's own coupons table (dynamic coupons)
 *   3. affwp_get_coupon() by WC coupon id (some builds)
 *
 * Returns the affiliate ID (int) or 0 if the coupon belongs to no affiliate.
 */
function nvacs_resolve_coupon_affiliate($code) {
    if (!is_string($code) || $code === '') return 0;
    if (!function_exists('wc_get_coupon_id_by_code')) return 0;

    $coupon_post_id = wc_get_coupon_id_by_code($code);

    // Source 1: this plugin's post meta mapping
    if ($coupon_post_id) {
        $linked = (int) get_post_meta($coupon_post_id, 'affwp_discount_affiliate', true);
        if ($linked > 0) return $linked;
    }

    // Source 2: AffiliateWP's own coupons table (dynamic coupons)
    if (function_exists('affiliate_wp')
        && affiliate_wp()->affiliates
        && affiliate_wp()->affiliates->coupons
        && method_exists(affiliate_wp()->affiliates->coupons, 'get_by')) {
        foreach ([$code, strtoupper($code), strtolower($code)] as $variant) {
            $aff_coupon = affiliate_wp()->affiliates->coupons->get_by('coupon_code', $variant);
            if ($aff_coupon && !empty($aff_coupon->affiliate_id)) {
                return (int) $aff_coupon->affiliate_id;
            }
        }
    }

    // Source 3: affwp_get_coupon() by WC coupon id
    if ($coupon_post_id && function_exists('affwp_get_coupon')) {
        $c = affwp_get_coupon($coupon_post_id);
        if ($c && !empty($c->affiliate_id)) return (int) $c->affiliate_id;
    }

    return 0;
}

add_filter('affwp_insert_pending_referral', 'nvacs_enforce_coupon_attribution', 10, 4);
function nvacs_enforce_coupon_attribution($data, $affiliate_id, $order_id, $amount) {
    // Already vetoed upstream — leave it alone
    if (false === $data) return false;

    // Need an order to do anything useful
    if (empty($order_id)) return $data;

    // Resolve the order
    $order = function_exists('wc_get_order') ? wc_get_order($order_id) : null;
    if (!$order) return $data;

    $applied_coupons = $order->get_coupon_codes();

    // Find the affiliate that actually owns a coupon used on this order.
    $coupon_affiliate = 0;
    $matched_code     = '';
    foreach ($applied_coupons as $code) {
        $owner = nvacs_resolve_coupon_affiliate($code);
        if ($owner > 0) {
            $coupon_affiliate = $owner;
            $matched_code     = strtoupper($code);
            break;
        }
    }

    // CASE A: an affiliate-linked coupon WAS used on this order.
    if ($coupon_affiliate > 0) {
        if ((int) $affiliate_id === $coupon_affiliate) {
            // Cookie and coupon agree — credit as-is.
            nvacs_affiliate_trace(
                'REFERRAL ALLOWED: order ' . $order_id .
                ', affiliate ' . $affiliate_id .
                ', coupon ' . $matched_code
            );
            return $data;
        }

        // Cookie credited the WRONG affiliate (or none). Re-point the referral
        // to whoever owns the coupon that was actually used at checkout.
        // This is the core June 9 regression: the old code blocked here,
        // killing the sale instead of crediting the real affiliate.
        nvacs_affiliate_trace(
            'REFERRAL RE-ATTRIBUTED: order ' . $order_id .
            ', from affiliate ' . $affiliate_id .
            ' to affiliate ' . $coupon_affiliate .
            ' via coupon ' . $matched_code
        );
        if (is_array($data)) {
            $data['affiliate_id'] = $coupon_affiliate;
            $data['visit_id']     = 0; // the cookie visit belonged to a different affiliate
        }
        return $data;
    }

    // CASE B: no affiliate-linked coupon on this order.
    // No coupons at all → pure cookie/?ref= attribution → block (original intent).
    if (empty($applied_coupons)) {
        nvacs_affiliate_trace(
            'REFERRAL BLOCKED (no coupons on order ' . $order_id .
            ') for affiliate ' . $affiliate_id
        );
        return false;
    }

    // Coupons exist but none belong to any affiliate (generic store coupon).
    // The credit can only be cookie-based → block.
    nvacs_affiliate_trace(
        'REFERRAL BLOCKED: order ' . $order_id .
        ', affiliate ' . $affiliate_id .
        '. Applied coupons [' . implode(', ', $applied_coupons) . ']' .
        ' are not linked to any affiliate.'
    );
    return false;
}

/* ============================================================
 * HELPER: reject a referral and keep affiliate earnings in sync.
 *
 * affwp_set_referral_status() is the proper API (fires hooks that
 * decrement unpaid_earnings). If it doesn't exist in this build,
 * fall back to the raw DB update AND manually decrement earnings
 * so the balance doesn't go stale.
 * ============================================================ */
function nvacs_reject_referral($ref_id, $amount, $affiliate_id) {
    if (function_exists('affwp_set_referral_status')) {
        affwp_set_referral_status($ref_id, 'rejected');
        return;
    }

    // Raw DB fallback — update the row directly…
    affiliate_wp()->referrals->update($ref_id, ['status' => 'rejected']);

    // …then manually decrement unpaid_earnings so the balance stays correct.
    $amount = (float) $amount;
    if ($amount > 0 && $affiliate_id) {
        global $wpdb;
        $table = $wpdb->prefix . 'affiliate_wp_affiliates';
        $wpdb->query($wpdb->prepare(
            "UPDATE {$table}
             SET unpaid_earnings = GREATEST(0, unpaid_earnings - %f)
             WHERE affiliate_id = %d",
            $amount,
            $affiliate_id
        ));
    }
}

/* ============================================================
 * ADMIN TOOL: RETROACTIVE REFERRAL AUDIT
 *
 * Visit /wp-admin/?nvacs_audit_referrals=1 to see which existing
 * pending/unpaid WooCommerce referrals should not have been created
 * (cookie-attributed, no matching affiliate coupon on the order).
 *
 * Dry-run by default — nothing is changed until you add &confirm=1.
 * Paid referrals are never touched.
 * ============================================================ */
add_action('init', 'nvacs_maybe_audit_referrals');
function nvacs_maybe_audit_referrals() {
    if (empty($_GET['nvacs_audit_referrals'])) return;
    if (!current_user_can('manage_options')) wp_die('Unauthorised', 403);
    if (!function_exists('affiliate_wp') || !function_exists('wc_get_order')) {
        wp_die('AffiliateWP or WooCommerce not active.');
    }

    $confirm = !empty($_GET['confirm']);

    // Only audit statuses that haven't been paid out yet
    $referrals = affiliate_wp()->referrals->get_referrals([
        'number'  => -1,
        'status'  => ['pending', 'unpaid'],
        'context' => 'woocommerce',
        'order'   => 'DESC',
        'orderby' => 'date',
    ]);

    $checked  = 0;
    $valid    = [];
    $bad      = [];
    $skipped  = [];

    foreach ($referrals as $referral) {
        $checked++;
        $ref_id       = (int) $referral->referral_id;
        $affiliate_id = (int) $referral->affiliate_id;
        $order_id     = $referral->reference;

        if (!$order_id) {
            $skipped[] = "Ref #{$ref_id} (aff #{$affiliate_id}): no order reference";
            continue;
        }

        $order = wc_get_order($order_id);
        if (!$order) {
            $skipped[] = "Ref #{$ref_id} (order #{$order_id}, aff #{$affiliate_id}): order not found";
            continue;
        }

        $applied_coupons = $order->get_coupon_codes();

        // No coupons at all — pure cookie referral
        if (empty($applied_coupons)) {
            if ($confirm) {
                nvacs_reject_referral($ref_id, $referral->amount, $affiliate_id);
            }
            $bad[] = [
                'ref'    => $ref_id,
                'order'  => $order_id,
                'aff'    => $affiliate_id,
                'reason' => 'No coupons on order (cookie-only attribution)',
                'amount' => $referral->amount,
            ];
            continue;
        }

        // Resolve which affiliate (if any) owns a coupon used on this order,
        // using all three link sources — not just the post meta.
        $coupon_affiliate = 0;
        $matched_code     = '';
        foreach ($applied_coupons as $code) {
            $owner = nvacs_resolve_coupon_affiliate($code);
            if ($owner > 0) {
                $coupon_affiliate = $owner;
                $matched_code     = strtoupper($code);
                break;
            }
        }

        if ($coupon_affiliate > 0 && $coupon_affiliate === $affiliate_id) {
            $valid[] = "Ref #{$ref_id} (order #{$order_id}, aff #{$affiliate_id}): OK — coupon {$matched_code}";
        } elseif ($coupon_affiliate > 0 && $coupon_affiliate !== $affiliate_id) {
            // Coupon belongs to a DIFFERENT affiliate — this should be re-pointed,
            // never rejected. Flag it; the recovery tool fixes attribution.
            $valid[] = "Ref #{$ref_id} (order #{$order_id}): MISATTRIBUTED to aff #{$affiliate_id}, coupon {$matched_code} belongs to aff #{$coupon_affiliate} — run the recovery tool to re-point.";
        } else {
            // No applied coupon belongs to any affiliate — genuine cookie-only credit.
            if ($confirm) {
                nvacs_reject_referral($ref_id, $referral->amount, $affiliate_id);
            }
            $bad[] = [
                'ref'    => $ref_id,
                'order'  => $order_id,
                'aff'    => $affiliate_id,
                'reason' => 'Coupons used: [' . implode(', ', $applied_coupons) . '] — none linked to any affiliate',
                'amount' => $referral->amount,
            ];
        }
    }

    // ---- Output ----
    $bad_count  = count($bad);
    $mode_color = $confirm ? '#c00' : '#b06000';
    $mode_label = $confirm
        ? "LIVE — {$bad_count} referral(s) have been REJECTED"
        : "DRY RUN — nothing changed. Add &amp;confirm=1 to commit.";

    $confirm_url = esc_url(add_query_arg(['nvacs_audit_referrals' => 1, 'confirm' => 1], admin_url()));

    $out  = '<style>body{font-family:monospace;padding:24px;background:#f2f0eb;}';
    $out .= 'h2{margin-bottom:8px;}table{border-collapse:collapse;width:100%;margin-top:8px;}';
    $out .= 'th,td{text-align:left;padding:6px 10px;border:1px solid #d4d2cc;font-size:13px;}';
    $out .= 'th{background:#e9e7e1;}tr.bad{background:#fff8f0;}</style>';

    $out .= '<h2>NVACS Referral Audit</h2>';
    $out .= '<p style="background:' . $mode_color . ';color:#fff;padding:10px 16px;border-radius:4px;display:inline-block;margin-bottom:16px;">'
          . $mode_label . '</p>';
    $out .= '<p>Checked: <strong>' . $checked . '</strong> &nbsp;|&nbsp; ';
    $out .= 'Valid: <strong style="color:#2d6a4f">' . count($valid) . '</strong> &nbsp;|&nbsp; ';
    $out .= ($confirm ? 'Rejected' : 'Would reject') . ': <strong style="color:#c00">' . $bad_count . '</strong> &nbsp;|&nbsp; ';
    $out .= 'Skipped: <strong>' . count($skipped) . '</strong></p>';

    if (!empty($bad)) {
        $out .= '<h3 style="color:#c00">' . ($confirm ? 'Rejected' : 'Invalid — would be rejected') . ' (' . $bad_count . ')</h3>';
        $out .= '<table><tr><th>Ref ID</th><th>Order</th><th>Affiliate</th><th>Amount</th><th>Reason</th></tr>';
        foreach ($bad as $r) {
            $out .= '<tr class="bad"><td>#' . $r['ref'] . '</td><td>#' . $r['order'] . '</td>';
            $out .= '<td>#' . $r['aff'] . '</td><td>$' . number_format((float)$r['amount'], 2) . '</td>';
            $out .= '<td>' . esc_html($r['reason']) . '</td></tr>';
        }
        $out .= '</table>';

        if (!$confirm) {
            $out .= '<p style="margin-top:20px;"><a href="' . $confirm_url . '" '
                  . 'style="background:#c00;color:#fff;padding:12px 24px;text-decoration:none;border-radius:4px;font-size:14px;">'
                  . 'CONFIRM: Reject these ' . $bad_count . ' referral(s) now</a></p>';
        }
    }

    if (!empty($valid)) {
        $out .= '<details style="margin-top:16px"><summary style="cursor:pointer;font-weight:bold;">Valid referrals (' . count($valid) . ')</summary>';
        $out .= '<ul style="margin-top:8px">';
        foreach ($valid as $v) $out .= '<li>' . esc_html($v) . '</li>';
        $out .= '</ul></details>';
    }

    if (!empty($skipped)) {
        $out .= '<details style="margin-top:8px"><summary style="cursor:pointer;color:#888">Skipped (' . count($skipped) . ')</summary>';
        $out .= '<ul style="margin-top:8px">';
        foreach ($skipped as $s) $out .= '<li style="color:#888">' . esc_html($s) . '</li>';
        $out .= '</ul></details>';
    }

    $out .= '<p style="margin-top:24px;font-size:12px;color:#888">Audit only covers pending + unpaid WooCommerce referrals. Paid referrals are never touched.</p>';

    wp_die($out, 'NVACS Referral Audit', ['response' => 200]);
}

/* ============================================================
 * ADMIN TOOL: RECALCULATE AFFILIATE UNPAID EARNINGS
 *
 * Visit /wp-admin/?nvacs_recalc_earnings=AFFILIATE_ID
 *
 * Sums all unpaid referral amounts for that affiliate and writes
 * the correct figure back to wp_affiliate_wp_affiliates. Use this
 * to fix a stale earnings balance after a manual status change.
 * ============================================================ */
add_action('init', 'nvacs_maybe_recalc_earnings');
function nvacs_maybe_recalc_earnings() {
    if (empty($_GET['nvacs_recalc_earnings'])) return;
    if (!current_user_can('manage_options')) wp_die('Unauthorised', 403);
    if (!function_exists('affiliate_wp')) wp_die('AffiliateWP not active.');

    $affiliate_id = (int) $_GET['nvacs_recalc_earnings'];
    if (!$affiliate_id) wp_die('Pass a numeric affiliate ID, e.g. ?nvacs_recalc_earnings=16');

    global $wpdb;

    // Sum all unpaid referral amounts for this affiliate
    $correct = (float) $wpdb->get_var($wpdb->prepare(
        "SELECT COALESCE(SUM(amount), 0)
         FROM {$wpdb->prefix}affiliate_wp_referrals
         WHERE affiliate_id = %d AND status = 'unpaid'",
        $affiliate_id
    ));

    // Write it back
    $wpdb->update(
        $wpdb->prefix . 'affiliate_wp_affiliates',
        ['unpaid_earnings' => $correct],
        ['affiliate_id'    => $affiliate_id],
        ['%f'],
        ['%d']
    );

    $name = '';
    $aff  = function_exists('affwp_get_affiliate') ? affwp_get_affiliate($affiliate_id) : null;
    if ($aff && !empty($aff->user_id)) {
        $user = get_userdata($aff->user_id);
        if ($user) $name = ' (' . $user->display_name . ')';
    }

    wp_die(
        '<p style="font-family:monospace;padding:24px;">'
        . 'Affiliate #' . $affiliate_id . $name . ' unpaid earnings recalculated.<br><br>'
        . 'New balance: <strong>$' . number_format($correct, 2) . '</strong> '
        . '(sum of all unpaid referrals for this affiliate).</p>',
        'NVACS Earnings Recalc',
        ['response' => 200]
    );
}

/* ============================================================
 * ADMIN TOOL: SINGLE-ORDER INSPECTOR
 *
 * /wp-admin/?nvacs_order_debug=ORDER_ID
 *
 * Dumps everything that decides affiliate attribution for one order:
 *   - status / date / total
 *   - the exact coupon codes on the order (what get_coupon_codes returns)
 *   - what each coupon resolves to (all 3 link sources)
 *   - every referral row that references this order (any status/context)
 * This is the fastest way to see WHY an order did or didn't credit.
 * ============================================================ */
add_action('admin_init', 'nvacs_maybe_order_debug');
function nvacs_maybe_order_debug() {
    if (empty($_GET['nvacs_order_debug'])) return;
    if (!current_user_can('manage_options')) wp_die('Unauthorised', 403);

    $order_id = (int) $_GET['nvacs_order_debug'];
    $order = function_exists('wc_get_order') ? wc_get_order($order_id) : null;

    header('Content-Type: text/html; charset=UTF-8');
    echo '<div style="font-family:monospace;max-width:1100px;margin:30px auto;padding:30px;background:#f6f6f6;border-radius:8px;line-height:1.7;">';
    echo '<h2>Order Inspector: #' . $order_id . '</h2>';

    if (!$order) {
        echo '<p style="color:#c00;"><strong>FAIL:</strong> No order found with ID ' . $order_id . '. (HPOS uses the numeric order ID, not the post ID.)</p></div>';
        exit;
    }

    echo '<p>Status: <strong>' . esc_html($order->get_status()) . '</strong></p>';
    echo '<p>Date created: <strong>' . esc_html($order->get_date_created() ? $order->get_date_created()->date('Y-m-d H:i:s') : '?') . '</strong></p>';
    echo '<p>Total: <strong>' . esc_html($order->get_currency() . ' ' . $order->get_total()) . '</strong></p>';

    $codes = $order->get_coupon_codes();
    echo '<h3>Applied coupon codes</h3>';
    if (empty($codes)) {
        echo '<p style="color:#c00;"><strong>NO COUPONS on this order.</strong> With coupon-only attribution this order credits nobody — the discount/code never made it onto the order at checkout.</p>';
    } else {
        echo '<table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;font-size:12px;">';
        echo '<tr><th>Code</th><th>WC coupon ID</th><th>post meta affwp_discount_affiliate</th><th>AffiliateWP coupons table</th><th>affwp_get_coupon()</th><th>RESOLVED</th></tr>';
        foreach ($codes as $code) {
            $wc_id = function_exists('wc_get_coupon_id_by_code') ? wc_get_coupon_id_by_code($code) : 0;
            $meta  = $wc_id ? (int) get_post_meta($wc_id, 'affwp_discount_affiliate', true) : 0;

            $table_aff = 0;
            if (function_exists('affiliate_wp') && affiliate_wp()->affiliates
                && affiliate_wp()->affiliates->coupons
                && method_exists(affiliate_wp()->affiliates->coupons, 'get_by')) {
                foreach ([$code, strtoupper($code), strtolower($code)] as $v) {
                    $c = affiliate_wp()->affiliates->coupons->get_by('coupon_code', $v);
                    if ($c && !empty($c->affiliate_id)) { $table_aff = (int) $c->affiliate_id; break; }
                }
            }

            $getc_aff = 0;
            if ($wc_id && function_exists('affwp_get_coupon')) {
                $c = affwp_get_coupon($wc_id);
                if ($c && !empty($c->affiliate_id)) $getc_aff = (int) $c->affiliate_id;
            }

            $resolved = nvacs_resolve_coupon_affiliate($code);
            echo '<tr>';
            echo '<td><strong>' . esc_html($code) . '</strong></td>';
            echo '<td>' . ($wc_id ?: '<span style="color:#c00;">none</span>') . '</td>';
            echo '<td>' . ($meta ?: '-') . '</td>';
            echo '<td>' . ($table_aff ?: '-') . '</td>';
            echo '<td>' . ($getc_aff ?: '-') . '</td>';
            echo '<td><strong>' . ($resolved ? 'affiliate ' . $resolved : '<span style="color:#c00;">0 (no affiliate)</span>') . '</strong></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    // Referral rows for this order
    global $wpdb;
    $rt = $wpdb->prefix . 'affiliate_wp_referrals';
    $refs = $wpdb->get_results($wpdb->prepare(
        "SELECT referral_id, affiliate_id, amount, status, context, description, date
         FROM {$rt} WHERE reference = %s ORDER BY referral_id ASC",
        (string) $order_id
    ));
    echo '<h3>Referral rows referencing this order</h3>';
    if (empty($refs)) {
        echo '<p style="color:#b06000;"><strong>None.</strong> No referral was ever written for this order (it was dropped, or never qualified).</p>';
    } else {
        echo '<table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;font-size:12px;">';
        echo '<tr><th>Ref ID</th><th>Affiliate</th><th>Amount</th><th>Status</th><th>Context</th><th>Description</th><th>Date</th></tr>';
        foreach ($refs as $r) {
            echo '<tr>';
            echo '<td>' . $r->referral_id . '</td>';
            echo '<td>' . $r->affiliate_id . '</td>';
            echo '<td>$' . number_format((float) $r->amount, 2) . '</td>';
            echo '<td><strong>' . esc_html($r->status) . '</strong></td>';
            echo '<td>' . esc_html($r->context) . '</td>';
            echo '<td>' . esc_html($r->description) . '</td>';
            echo '<td>' . esc_html($r->date) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    echo '</div>';
    exit;
}

/* ============================================================
 * ADMIN TOOL: RECOVER REFERRALS WRONGLY DROPPED/REJECTED SINCE JUNE 9
 *
 * The June 9 validator silently discarded legitimate coupon
 * referrals (returned false → AffiliateWP never wrote the row), so
 * most lost sales have NO referral at all — they can't be found by
 * un-rejecting. This tool works from the WooCommerce orders instead.
 *
 *   /wp-admin/?nvacs_recover_referrals=1                  (DRY RUN)
 *   /wp-admin/?nvacs_recover_referrals=1&confirm=1        (COMMIT)
 *   /wp-admin/?nvacs_recover_referrals=1&since=2026-06-09 (custom start)
 *
 * For every paid order since the start date that used an affiliate
 * coupon it will:
 *   - CREATE a referral if none exists (the dropped case)
 *   - UN-REJECT + re-point a rejected referral that should be valid
 *   - RE-POINT a pending/unpaid referral credited to the wrong affiliate
 *   - leave already-correct and already-PAID referrals untouched
 * Then it recalculates unpaid_earnings for every affected affiliate
 * from the DB so balances match exactly.
 * ============================================================ */
add_action('init', 'nvacs_maybe_recover_referrals');
function nvacs_maybe_recover_referrals() {
    if (empty($_GET['nvacs_recover_referrals'])) return;
    if (!current_user_can('manage_options')) wp_die('Unauthorised', 403);
    if (!function_exists('affiliate_wp') || !function_exists('wc_get_orders')) {
        wp_die('AffiliateWP or WooCommerce not active.');
    }

    @set_time_limit(0);
    $confirm = !empty($_GET['confirm']);
    $since   = !empty($_GET['since']) ? sanitize_text_field($_GET['since']) : '2026-06-09 00:00:00';
    $since_ts = strtotime($since);
    if (!$since_ts) wp_die('Bad &since date. Use YYYY-MM-DD.');

    // Optional manual rate fallback for affiliates whose commission won't auto-compute.
    // e.g. &rate=20&rate_type=percent  or  &rate=15&rate_type=flat
    $ov_rate = isset($_GET['rate']) ? (float) $_GET['rate'] : 0;
    $ov_type = isset($_GET['rate_type']) ? sanitize_text_field($_GET['rate_type']) : 'percent';

    global $wpdb;
    $referrals_table = $wpdb->prefix . 'affiliate_wp_referrals';

    $orders = wc_get_orders([
        'limit'        => -1,
        'status'       => ['wc-processing', 'wc-completed', 'wc-on-hold'],
        'date_created' => '>=' . $since_ts,
        'orderby'      => 'date',
        'order'        => 'ASC',
        'return'       => 'objects',
    ]);

    $created = []; $completed = []; $repointed = []; $unrejected = []; $ok = []; $skipped = []; $paid_warn = [];
    $affected_affiliates = [];

    foreach ($orders as $order) {
        $order_id = $order->get_id();
        $applied  = $order->get_coupon_codes();
        if (empty($applied)) continue;

        // Which affiliate owns a coupon on this order?
        $coupon_affiliate = 0; $matched_code = '';
        foreach ($applied as $code) {
            $owner = nvacs_resolve_coupon_affiliate($code);
            if ($owner > 0) { $coupon_affiliate = $owner; $matched_code = strtoupper($code); break; }
        }
        if (!$coupon_affiliate) continue; // no affiliate coupon on this order

        // Existing referral(s) for this order
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT referral_id, affiliate_id, amount, status
             FROM {$referrals_table}
             WHERE reference = %s AND context = 'woocommerce'
             ORDER BY referral_id ASC LIMIT 1",
            (string) $order_id
        ));

        $row = [
            'order'  => $order_id,
            'aff'    => $coupon_affiliate,
            'coupon' => $matched_code,
        ];

        if (!$existing) {
            // DROPPED case — no referral was ever written. Create one.
            $amount = nvacs_calc_recovery_amount($order, $coupon_affiliate, $ov_rate, $ov_type);
            if ($amount <= 0) {
                $skipped[] = $row + ['reason' => 'Could not compute commission amount (rate is 0?) — create manually'];
                continue;
            }
            $row['amount'] = $amount;
            if ($confirm) {
                $new_id = nvacs_create_recovered_referral($coupon_affiliate, $order, $amount, $matched_code);
                if (!$new_id) { $skipped[] = $row + ['reason' => 'Referral insert failed']; continue; }
                $row['ref'] = $new_id;
            }
            $created[] = $row;
            $affected_affiliates[$coupon_affiliate] = true;
            continue;
        }

        $row['ref']   = (int) $existing->referral_id;
        $existing_aff = (int) $existing->affiliate_id;
        $status       = $existing->status;
        $existing_amt = (float) $existing->amount;
        $ref_id       = (int) $existing->referral_id;

        // Never touch a paid referral.
        if ($status === 'paid') {
            if ($existing_aff !== $coupon_affiliate) {
                $paid_warn[] = $row + ['reason' => "Already PAID to aff #{$existing_aff} — coupon belongs to #{$coupon_affiliate}. Resolve manually."];
            } else {
                $ok[] = $row + ['reason' => 'Already paid, correct affiliate'];
            }
            continue;
        }

        // Decide the correct amount: keep a good existing one, else recompute.
        $amount = $existing_amt > 0 ? $existing_amt : nvacs_calc_recovery_amount($order, $coupon_affiliate, $ov_rate, $ov_type);

        $needs_repoint  = ($existing_aff !== $coupon_affiliate);
        $needs_complete = ($status !== 'unpaid') || ($existing_amt <= 0);

        // Already correct, unpaid, with a real amount — nothing to do.
        if (!$needs_repoint && !$needs_complete) {
            $ok[] = $row + ['reason' => 'Correct (unpaid)'];
            continue;
        }

        if ($amount <= 0) {
            $skipped[] = $row + ['reason' => "Status {$status}, amount could not be computed — fix manually"];
            continue;
        }

        $row['amount'] = $amount;
        if ($needs_repoint) $row['from'] = $existing_aff;

        if ($confirm) {
            $update = ['status' => 'unpaid', 'amount' => $amount];
            $fmt    = ['%s', '%f'];
            if ($needs_repoint) { $update['affiliate_id'] = $coupon_affiliate; $fmt[] = '%d'; }
            $wpdb->update($referrals_table, $update, ['referral_id' => $ref_id], $fmt, ['%d']);
        }

        // Categorise for the report.
        if ($status === 'rejected') {
            $unrejected[] = $row + ['reason' => $needs_repoint ? "re-pointed from #{$existing_aff}" : ''];
        } elseif ($needs_repoint && $status === 'unpaid' && $existing_amt > 0) {
            $repointed[] = $row;
        } else {
            // draft / pending / $0 — the stuck-referral case.
            $note = 'was ' . $status . ($existing_amt <= 0 ? ' @ $0.00' : '');
            if ($needs_repoint) $note .= ", re-pointed from #{$existing_aff}";
            $completed[] = $row + ['reason' => $note];
        }

        $affected_affiliates[$coupon_affiliate] = true;
        if ($needs_repoint) $affected_affiliates[$existing_aff] = true;
    }

    // Recalculate unpaid_earnings for every affected affiliate from the DB.
    $recalced = [];
    if ($confirm && !empty($affected_affiliates)) {
        foreach (array_keys($affected_affiliates) as $aid) {
            $sum = (float) $wpdb->get_var($wpdb->prepare(
                "SELECT COALESCE(SUM(amount),0) FROM {$referrals_table}
                 WHERE affiliate_id = %d AND status = 'unpaid'", $aid
            ));
            nvacs_set_unpaid_earnings($aid, $sum);
            $recalced[$aid] = $sum;
        }
        // Global flush so dashboard referral/visit counts refresh too.
        nvacs_flush_affiliate_caches();
    }

    // ---- Output ----
    $aff_name = function($aid) {
        if (!function_exists('affwp_get_affiliate')) return '#' . $aid;
        $a = affwp_get_affiliate($aid);
        if ($a && !empty($a->user_id)) {
            $u = get_userdata($a->user_id);
            if ($u) return '#' . $aid . ' (' . esc_html($u->display_name) . ')';
        }
        return '#' . $aid;
    };

    header('Content-Type: text/html; charset=UTF-8');
    echo '<div style="font-family:monospace;max-width:1100px;margin:30px auto;padding:30px;background:#f6f6f6;border-radius:8px;line-height:1.6;">';
    echo '<h2>Referral Recovery</h2>';
    echo '<p>Scanning orders since <strong>' . esc_html($since) . '</strong>. Orders checked: <strong>' . count($orders) . '</strong></p>';
    $mode = $confirm
        ? '<span style="color:#c00;font-weight:bold;">LIVE — changes committed below.</span>'
        : '<span style="color:#b06000;font-weight:bold;">DRY RUN — nothing changed. Add &amp;confirm=1 to commit.</span>';
    echo '<p>' . $mode . '</p>';

    $section = function($title, $rows, $color) use ($aff_name) {
        echo '<h3 style="color:' . $color . ';">' . esc_html($title) . ' (' . count($rows) . ')</h3>';
        if (empty($rows)) { echo '<p style="color:#888;">None.</p>'; return; }
        echo '<table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;font-size:12px;width:100%;">';
        echo '<tr><th>Order</th><th>Affiliate</th><th>Coupon</th><th>Amount</th><th>Ref ID</th><th>Note</th></tr>';
        foreach ($rows as $r) {
            echo '<tr>';
            echo '<td>#' . esc_html($r['order']) . '</td>';
            echo '<td>' . $aff_name($r['aff']) . (isset($r['from']) ? ' <span style="color:#c00;">(was ' . $r['from'] . ')</span>' : '') . '</td>';
            echo '<td>' . esc_html($r['coupon']) . '</td>';
            echo '<td>' . (isset($r['amount']) ? '$' . number_format((float)$r['amount'], 2) : '-') . '</td>';
            echo '<td>' . (isset($r['ref']) ? esc_html($r['ref']) : '-') . '</td>';
            echo '<td>' . esc_html($r['reason'] ?? '') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    };

    $section('CREATED (dropped sales, no referral existed)', $created, '#0a7d28');
    $section('COMPLETED (stuck draft / $0 — now unpaid with commission)', $completed, '#0a7d28');
    $section('UN-REJECTED (wrongly rejected)', $unrejected, '#0a7d28');
    $section('RE-POINTED (credited to wrong affiliate)', $repointed, '#b06000');
    $section('ALREADY PAID — needs manual review', $paid_warn, '#c00');
    $section('SKIPPED', $skipped, '#888');

    if ($confirm && !empty($recalced)) {
        echo '<h3>Earnings recalculated</h3><table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;font-size:12px;">';
        echo '<tr><th>Affiliate</th><th>New unpaid balance</th></tr>';
        foreach ($recalced as $aid => $sum) {
            echo '<tr><td>' . $aff_name($aid) . '</td><td>$' . number_format($sum, 2) . '</td></tr>';
        }
        echo '</table>';
    }

    echo '<p style="color:#888;font-size:13px;margin-top:20px;">Note: only processing/completed/on-hold orders are considered. Cancelled, failed and refunded orders are ignored. Paid referrals are never overwritten.</p>';
    if (!empty($skipped)) {
        echo '<p style="color:#b06000;font-size:13px;">Some rows could not auto-compute a commission. Re-run with a manual rate to fix them, e.g. <code>&amp;rate=20&amp;rate_type=percent</code> (or <code>&amp;rate_type=flat</code>). The manual rate is only applied where AffiliateWP returns no rate of its own.</p>';
    }
    echo '</div>';
    exit;
}

/**
 * Apply a rate to a base amount. Handles flat vs percentage, and percentages
 * stored either as a decimal (0.2) or whole number (20).
 */
function nvacs_apply_rate($base, $rate, $type) {
    $rate = (float) $rate;
    if ($rate <= 0) return 0.0;
    $type = strtolower((string) $type);
    if (strpos($type, 'flat') !== false) return round($rate, 2);
    if ($rate > 1) $rate = $rate / 100; // 20 -> 0.20
    return round($base * $rate, 2);
}

/**
 * Compute the commission for a recovered referral. Tries, in order:
 *   1. AffiliateWP's own calculator (per-affiliate + product/category rates)
 *   2. the affiliate's own rate
 *   3. the store's global default referral rate
 *   4. a manual override passed to the recovery tool (&rate / &rate_type)
 * Returns 0 only when nothing yields a positive amount.
 */
function nvacs_calc_recovery_amount($order, $affiliate_id, $ov_rate = 0, $ov_type = 'percent') {
    $base = (float) $order->get_total();
    if ($base <= 0) return 0.0;

    // 1) AffiliateWP's own calculator
    if (function_exists('affwp_calc_referral_amount')) {
        $amt = (float) affwp_calc_referral_amount($base, $affiliate_id, $order->get_id(), '', 0);
        if ($amt > 0) return round($amt, 2);
    }

    // 2) Affiliate's own rate
    if (function_exists('affwp_get_affiliate_rate') && function_exists('affwp_get_affiliate_rate_type')) {
        $amt = nvacs_apply_rate($base, affwp_get_affiliate_rate($affiliate_id), affwp_get_affiliate_rate_type($affiliate_id));
        if ($amt > 0) return $amt;
    }

    // 3) Store global default rate
    if (function_exists('affiliate_wp') && affiliate_wp()->settings) {
        $amt = nvacs_apply_rate(
            $base,
            affiliate_wp()->settings->get('referral_rate', 0),
            affiliate_wp()->settings->get('referral_rate_type', 'percentage')
        );
        if ($amt > 0) return $amt;
    }

    // 4) Manual override (only reached when nothing above computed)
    if ($ov_rate > 0) {
        return nvacs_apply_rate($base, $ov_rate, $ov_type);
    }

    return 0.0;
}

/**
 * Flush the AffiliateWP caches that feed the affiliate dashboard, so balances
 * and referral counts reflect direct DB writes immediately. AffiliateWP caches
 * query results against a per-group "last_changed" stamp; bumping it busts them.
 */
function nvacs_flush_affiliate_caches($affiliate_id = 0) {
    foreach (['affiliates', 'referrals', 'visits', 'affiliate_meta', 'customers'] as $g) {
        wp_cache_set('last_changed', microtime(), $g);
    }
    if ($affiliate_id) {
        wp_cache_delete($affiliate_id, 'affiliates');
    }
}

/**
 * Set an affiliate's unpaid_earnings via AffiliateWP's own update method (which
 * clears its cache), falling back to a raw write. Always flushes caches after.
 */
function nvacs_set_unpaid_earnings($affiliate_id, $amount) {
    $amount = (float) $amount;
    $done = false;
    if (function_exists('affiliate_wp') && affiliate_wp()->affiliates
        && method_exists(affiliate_wp()->affiliates, 'update')) {
        $done = (bool) affiliate_wp()->affiliates->update($affiliate_id, ['unpaid_earnings' => $amount], '', 'affiliate');
    }
    if (!$done) {
        global $wpdb;
        $wpdb->update($wpdb->prefix . 'affiliate_wp_affiliates',
            ['unpaid_earnings' => $amount], ['affiliate_id' => $affiliate_id], ['%f'], ['%d']);
    }
    nvacs_flush_affiliate_caches($affiliate_id);
}

/**
 * Create a recovered referral row crediting the given affiliate.
 * Returns the new referral_id or 0 on failure.
 */
function nvacs_create_recovered_referral($affiliate_id, $order, $amount, $coupon_code) {
    $order_id = $order->get_id();
    $date     = $order->get_date_created()
        ? $order->get_date_created()->date('Y-m-d H:i:s')
        : current_time('mysql');

    $data = [
        'affiliate_id' => (int) $affiliate_id,
        'amount'       => (float) $amount,
        'status'       => 'unpaid',
        'context'      => 'woocommerce',
        'reference'    => (string) $order_id,
        'description'  => 'Recovered (NVACS) — coupon ' . $coupon_code,
        'currency'     => $order->get_currency(),
        'date'         => $date,
    ];

    if (function_exists('affiliate_wp') && affiliate_wp()->referrals
        && method_exists(affiliate_wp()->referrals, 'add')) {
        $new_id = affiliate_wp()->referrals->add($data);
        if ($new_id) return (int) $new_id;
    }

    if (function_exists('affwp_add_referral')) {
        $new_id = affwp_add_referral($data);
        if ($new_id) return (int) $new_id;
    }

    // Last-resort direct insert.
    global $wpdb;
    $ok = $wpdb->insert($wpdb->prefix . 'affiliate_wp_referrals', $data,
        ['%d', '%f', '%s', '%s', '%s', '%s', '%s', '%s']);
    return $ok ? (int) $wpdb->insert_id : 0;
}

/* ============================================================
 * FORWARD FIX: COMPLETE STUCK DRAFT / $0 REFERRALS AUTOMATICALLY
 *
 * On this AffiliateWP build the normal draft→unpaid promotion is not
 * firing, so coupon referrals sit at status 'draft' with amount $0 and
 * never appear as earnings. This runs AFTER AffiliateWP (priority 20)
 * when an order reaches processing/completed, and finishes the job:
 *   - if a draft / pending / $0 referral exists for the order, recompute
 *     the commission, point it at the coupon's affiliate, set it 'unpaid'
 *   - if no referral exists at all, create one
 *   - then resync that affiliate's unpaid_earnings from the DB
 *
 * It only touches referrals AffiliateWP left stuck — anything already
 * 'unpaid' with a real amount or 'paid' is left untouched, so it can't
 * double-credit.
 * ============================================================ */
add_action('woocommerce_order_status_processing', 'nvacs_complete_order_referral', 20, 1);
add_action('woocommerce_order_status_completed',  'nvacs_complete_order_referral', 20, 1);
function nvacs_complete_order_referral($order_id) {
    if (!function_exists('affiliate_wp') || !function_exists('wc_get_order')) return;

    $order = wc_get_order($order_id);
    if (!$order) return;

    // Which affiliate owns a coupon actually used on this order?
    $coupon_affiliate = 0; $matched_code = '';
    foreach ($order->get_coupon_codes() as $code) {
        $owner = nvacs_resolve_coupon_affiliate($code);
        if ($owner > 0) { $coupon_affiliate = $owner; $matched_code = strtoupper($code); break; }
    }
    if (!$coupon_affiliate) return; // no affiliate coupon → nothing to do

    global $wpdb;
    $rt = $wpdb->prefix . 'affiliate_wp_referrals';

    $existing = $wpdb->get_row($wpdb->prepare(
        "SELECT referral_id, affiliate_id, amount, status
         FROM {$rt} WHERE reference = %s AND context = 'woocommerce'
         ORDER BY referral_id ASC LIMIT 1",
        (string) $order_id
    ));

    // Already finalised correctly — leave it alone.
    if ($existing && in_array($existing->status, ['unpaid', 'paid'], true)
        && (float) $existing->amount > 0
        && (int) $existing->affiliate_id === $coupon_affiliate) {
        return;
    }

    $amount = ($existing && (float) $existing->amount > 0)
        ? (float) $existing->amount
        : nvacs_calc_recovery_amount($order, $coupon_affiliate);
    if ($amount <= 0) {
        nvacs_affiliate_trace("COMPLETE skipped: order {$order_id}, amount calc 0 for affiliate {$coupon_affiliate}");
        return;
    }

    if ($existing) {
        // Don't disturb a paid referral.
        if ($existing->status === 'paid') return;
        $wpdb->update($rt,
            ['status' => 'unpaid', 'amount' => $amount, 'affiliate_id' => $coupon_affiliate],
            ['referral_id' => (int) $existing->referral_id],
            ['%s', '%f', '%d'], ['%d']
        );
        nvacs_affiliate_trace(
            "REFERRAL COMPLETED: order {$order_id}, ref {$existing->referral_id}, affiliate {$coupon_affiliate}, \$" . number_format($amount, 2) . " (was {$existing->status})"
        );
    } else {
        $new_id = nvacs_create_recovered_referral($coupon_affiliate, $order, $amount, $matched_code);
        nvacs_affiliate_trace(
            "REFERRAL CREATED on completion: order {$order_id}, ref {$new_id}, affiliate {$coupon_affiliate}, \$" . number_format($amount, 2)
        );
    }

    // Resync this affiliate's unpaid balance from the DB (and bust caches).
    $sum = (float) $wpdb->get_var($wpdb->prepare(
        "SELECT COALESCE(SUM(amount),0) FROM {$rt} WHERE affiliate_id = %d AND status = 'unpaid'",
        $coupon_affiliate
    ));
    nvacs_set_unpaid_earnings($coupon_affiliate, $sum);
    nvacs_flush_affiliate_caches($coupon_affiliate);
}

function nvacs_get_affiliate_coupon_code($affiliate_id = 0) {
    if (!$affiliate_id && function_exists('affwp_get_affiliate_id')) {
        $affiliate_id = affwp_get_affiliate_id();
    }
    if (!$affiliate_id || !function_exists('affwp_get_affiliate_coupons')) return '';
    $dynamic_coupons = affwp_get_affiliate_coupons($affiliate_id);
    if (empty($dynamic_coupons)) return '';
    if (!is_array($dynamic_coupons)) $dynamic_coupons = [$dynamic_coupons];
    $first = reset($dynamic_coupons);
    return nvacs_extract_coupon_code($first);
}

/* ============================================================
 * EVENT HOOKS — sync coupons on affiliate changes
 * ============================================================ */
add_action('affwp_add_affiliate',      function($id) { add_action('shutdown', function() use ($id) { nvacs_sync_affiliate_coupons($id); }); }, 99);
add_action('affwp_insert_affiliate',   function($id) { add_action('shutdown', function() use ($id) { nvacs_sync_affiliate_coupons($id); }); }, 99);
add_action('affwp_updated_affiliate',  function($id) { add_action('shutdown', function() use ($id) { nvacs_sync_affiliate_coupons($id); }); }, 99);

add_action('affwp_insert_coupon', function($coupon_id, $args = []) {
    add_action('shutdown', function() use ($coupon_id, $args) {
        $affiliate_id = isset($args['affiliate_id']) ? $args['affiliate_id'] : 0;
        if (!$affiliate_id && is_numeric($coupon_id) && function_exists('affwp_get_coupon')) {
            $coupon = affwp_get_coupon($coupon_id);
            if ($coupon && isset($coupon->affiliate_id)) $affiliate_id = $coupon->affiliate_id;
        }
        if ($affiliate_id) nvacs_sync_affiliate_coupons($affiliate_id);
    });
}, 99, 2);

add_action('user_register', function($user_id) {
    add_action('shutdown', function() use ($user_id) {
        if (!function_exists('affwp_get_affiliate_id')) return;
        $affiliate_id = affwp_get_affiliate_id($user_id);
        if ($affiliate_id) nvacs_sync_affiliate_coupons($affiliate_id);
    });
}, 99);

/* ============================================================
 * CRON + ADMIN SWEEP
 * ============================================================ */
register_activation_hook(__FILE__, function() {
    if (!wp_next_scheduled('nvacs_cron_sync_all')) {
        wp_schedule_event(time(), 'nvacs_every_fifteen_min', 'nvacs_cron_sync_all');
    }
});
register_deactivation_hook(__FILE__, function() {
    $ts = wp_next_scheduled('nvacs_cron_sync_all');
    if ($ts) wp_unschedule_event($ts, 'nvacs_cron_sync_all');
});
add_filter('cron_schedules', function($s) {
    $s['nvacs_every_fifteen_min'] = ['interval' => 15 * 60, 'display' => 'Every 15 Minutes (NVACS)'];
    return $s;
});
add_action('nvacs_cron_sync_all', 'nvacs_sync_all_affiliates');

add_action('admin_init', function() {
    if (wp_doing_ajax() || wp_doing_cron()) return;
    $last = (int) get_transient('nvacs_last_admin_sync');
    if ($last && (time() - $last) < 60) return;
    set_transient('nvacs_last_admin_sync', time(), 120);
    nvacs_sync_all_affiliates();
}, 999);

/* ============================================================
 * MANUAL BULK SYNC: /wp-admin/?sync_affiliate_coupons=1
 * ============================================================ */
add_action('admin_init', function() {
    if (!isset($_GET['sync_affiliate_coupons']) || !current_user_can('manage_options')) return;
    if (!function_exists('affiliate_wp') || !class_exists('WC_Coupon')) wp_die('AffiliateWP and/or WooCommerce are not active.');
    $r = nvacs_sync_all_affiliates();
    $list = $r['codes'] ? '<p>New codes: <code>' . esc_html(implode(', ', $r['codes'])) . '</code></p>' : '';
    wp_die(sprintf('Created %d new coupons. Skipped %d. %s<p><a href="%s">View coupons →</a></p>',
        $r['created'], $r['skipped'], $list, esc_url(admin_url('edit.php?post_type=shop_coupon'))),
        'Sync Complete', ['response' => 200]);
});

/* ============================================================
 * LEGACY AFFILIATE AREA FILTERS (fallback for non-portal pages)
 * ============================================================ */
foreach ([
    'affwp_referral_url',
    'affwp_get_affiliate_referral_url',
    'affwp_affiliate_portal_referral_url',
    'affiliatewp_affiliate_portal_referral_url',
] as $filter) {
    add_filter($filter, 'nvacs_filter_referral_url', 99, 4);
}

function nvacs_filter_referral_url($url, $arg1 = null, $arg2 = null, $arg3 = null) {
    $affiliate_id = 0;
    foreach ([$arg1, $arg2, $arg3] as $a) {
        if (is_numeric($a)) { $affiliate_id = (int) $a; break; }
        if (is_array($a) && !empty($a['affiliate_id'])) { $affiliate_id = (int) $a['affiliate_id']; break; }
        if (is_object($a) && !empty($a->affiliate_id))   { $affiliate_id = (int) $a->affiliate_id; break; }
    }
    if (!$affiliate_id && function_exists('affwp_get_affiliate_id')) {
        $affiliate_id = affwp_get_affiliate_id();
    }
    if (!$affiliate_id) return $url;
    $coupon_url = nvacs_get_affiliate_coupon_url($affiliate_id);
    return $coupon_url ?: $url;
}

/* ============================================================
 * PORTAL DETECTION
 * ============================================================ */
function nvacs_is_affiliate_portal() {
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    return (strpos($uri, '/affiliate-area') !== false);
}

/* ============================================================
 * PORTAL: INJECT URL SWAP + RESTYLE
 *
 * The Affiliate Portal uses Alpine.js + a global AFFWP.portal.urlGenerator
 * data function. We hook in BEFORE Alpine initializes by patching
 * getUrlParam() to return the coupon URL instead of /ref/{id}/.
 *
 * We also override the visible /ref/{id}/ text in any DOM node that gets
 * rendered later, as a safety net.
 * ============================================================ */
add_action('wp_head', function() {
    if (!nvacs_is_affiliate_portal()) return;
    if (!is_user_logged_in()) return;
    if (!function_exists('affwp_get_affiliate_id')) return;

    $affiliate_id = affwp_get_affiliate_id();
    if (!$affiliate_id) return;

    $coupon_url  = nvacs_get_affiliate_coupon_url($affiliate_id);
    $coupon_code = nvacs_get_affiliate_coupon_code($affiliate_id);
    if (!$coupon_url) return;

    $coupon_url_js  = esc_js($coupon_url);
    $coupon_code_js = esc_js($coupon_code);
    $home_url_js    = esc_js(home_url('/'));
    ?>
    <script id="nvacs-portal-url-swap">
    (function() {
        var NVACS = {
            couponUrl:  '<?php echo $coupon_url_js; ?>',
            couponCode: '<?php echo $coupon_code_js; ?>',
            homeUrl:    '<?php echo $home_url_js; ?>',
            refPattern: /https?:\/\/[^\/\s"']+\/ref\/\d+\/?/gi
        };
        window.NVACS = NVACS;

        // Replace /ref/{id}/ in any string
        function swap(value) {
            if (typeof value !== 'string') return value;
            NVACS.refPattern.lastIndex = 0;
            if (NVACS.refPattern.test(value)) {
                NVACS.refPattern.lastIndex = 0;
                return NVACS.couponUrl;
            }
            return value;
        }

        // Override navigator.clipboard.writeText + document.execCommand('copy')
        // so the Copy button copies the coupon URL no matter what string Alpine sends
        if (navigator.clipboard && navigator.clipboard.writeText) {
            var origWriteText = navigator.clipboard.writeText.bind(navigator.clipboard);
            navigator.clipboard.writeText = function(text) {
                return origWriteText(swap(text));
            };
        }

        // Patch AFFWP.portal.urlGenerator before Alpine reads it
        function patchUrlGenerator() {
            if (!window.AFFWP || !window.AFFWP.portal || !window.AFFWP.portal.urlGenerator) return false;
            var ug = window.AFFWP.portal.urlGenerator;
            if (ug.__nvacsPatched) return true;
            var origDefault = ug.default;
            if (typeof origDefault !== 'function') return false;
            ug.default = function() {
                var instance = origDefault.apply(this, arguments);

                // Patch getUrlParam (display layer)
                if (instance && typeof instance.getUrlParam === 'function') {
                    var origGetUrlParam = instance.getUrlParam.bind(instance);
                    instance.getUrlParam = function(type, key) {
                        var v = origGetUrlParam(type, key);
                        if (key === 'url') return swap(v);
                        return v;
                    };
                }

                // Patch setCopy (clipboard layer) — this is what the Copy button calls
                if (instance && typeof instance.setCopy === 'function') {
                    var origSetCopy = instance.setCopy.bind(instance);
                    instance.setCopy = function(type) {
                        // Before AFFWP reads the url to copy, force the underlying state
                        try {
                            if (instance.urls && instance.urls[type]) {
                                instance.urls[type].url = NVACS.couponUrl;
                            }
                            if (instance[type] && typeof instance[type] === 'object' && 'url' in instance[type]) {
                                instance[type].url = NVACS.couponUrl;
                            }
                        } catch (e) {}
                        return origSetCopy(type);
                    };
                }

                // Patch generateUrl (so the Page URL input still works but generates coupon URLs)
                if (instance && typeof instance.generateUrl === 'function') {
                    var origGenerate = instance.generateUrl.bind(instance);
                    instance.generateUrl = function(type) {
                        var result = origGenerate(type);
                        try {
                            if (instance.urls && instance.urls[type]) {
                                instance.urls[type].url = NVACS.couponUrl;
                            }
                        } catch (e) {}
                        return result;
                    };
                }

                return instance;
            };
            ug.__nvacsPatched = true;
            return true;
        }

        // Try repeatedly until AFFWP loads
        var tries = 0;
        var iv = setInterval(function() {
            if (patchUrlGenerator() || ++tries > 60) clearInterval(iv);
        }, 50);

        // DOM safety net: swap any visible /ref/{id}/ text and href attributes
        function swapDom(root) {
            if (!root) root = document.body;
            if (!root) return;
            // Text nodes
            var walker = document.createTreeWalker(root, NodeFilter.SHOW_TEXT, null, false);
            var node;
            var toUpdate = [];
            while (node = walker.nextNode()) {
                NVACS.refPattern.lastIndex = 0;
                if (NVACS.refPattern.test(node.nodeValue)) {
                    NVACS.refPattern.lastIndex = 0;
                    toUpdate.push(node);
                }
            }
            toUpdate.forEach(function(n) {
                NVACS.refPattern.lastIndex = 0;
                n.nodeValue = n.nodeValue.replace(NVACS.refPattern, NVACS.couponUrl);
            });
            // Attribute swap for inputs/anchors
            root.querySelectorAll('input[value], a[href], [data-url]').forEach(function(el) {
                ['value', 'href', 'data-url'].forEach(function(attr) {
                    var v = el.getAttribute(attr);
                    if (!v) return;
                    NVACS.refPattern.lastIndex = 0;
                    if (NVACS.refPattern.test(v)) {
                        NVACS.refPattern.lastIndex = 0;
                        el.setAttribute(attr, v.replace(NVACS.refPattern, NVACS.couponUrl));
                    }
                });
            });
        }

        // Run after Alpine renders, and watch for changes
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() { swapDom(); }, 100);
            setTimeout(function() { swapDom(); }, 500);
            setTimeout(function() { swapDom(); }, 1500);

            var mo = new MutationObserver(function(mutations) {
                var dirty = false;
                mutations.forEach(function(m) {
                    if (m.type === 'characterData') {
                        NVACS.refPattern.lastIndex = 0;
                        if (NVACS.refPattern.test(m.target.nodeValue)) dirty = true;
                    }
                    if (m.addedNodes && m.addedNodes.length) dirty = true;
                });
                if (dirty) swapDom();
            });
            mo.observe(document.body, { childList: true, subtree: true, characterData: true });
        });
    })();
    </script>
    <?php
}, 1);

/* ============================================================
 * PORTAL: OUTPUT NATTY VISION STYLES
 *
 * We output directly in wp_head at very low priority (PHP_INT_MAX)
 * so it appears AFTER all other stylesheets in the <head>, guaranteeing
 * our rules win the cascade without needing !important everywhere.
 * ============================================================ */
add_action('wp_head', function() {
    if (!nvacs_is_affiliate_portal()) return;
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap" rel="stylesheet">
    <style id="nvacs-portal-restyle"><?php echo nvacs_portal_css(); ?></style>
    <?php
}, PHP_INT_MAX);

// Also append at end of body as belt-and-suspenders for stubborn cascades
add_action('wp_footer', function() {
    if (!nvacs_is_affiliate_portal()) return;
    ?>
    <style id="nvacs-portal-restyle-footer"><?php echo nvacs_portal_css(); ?></style>
    <?php
}, PHP_INT_MAX);

function nvacs_portal_css() {
    return <<<CSS
/* ============================================================
   Natty Vision — Affiliate Portal Restyle (v1.5)
   ============================================================ */
:root {
    --nv-bg: #f2f0eb;
    --nv-bg2: #e9e7e1;
    --nv-bg-card: #eae8e2;
    --nv-sage: #c5d4c0;
    --nv-sage-s: #dce5d8;
    --nv-sage-d: #a8bfa2;
    --nv-dark: #1a1e1c;
    --nv-dark2: #232826;
    --nv-green: #2d6a4f;
    --nv-green-l: #40916c;
    --nv-green-b: #52b788;
    --nv-text: #1a1e1c;
    --nv-t2: #4a4f4c;
    --nv-t3: #7a7f7c;
    --nv-ti: #f2f0eb;
    --nv-ti2: #b0aea8;
    --nv-brd: #d4d2cc;
    --nv-brd2: #c4c2bc;
    --nv-r: 16px;
    --nv-rs: 12px;
    --nv-rx: 8px;
    --nv-rp: 100px;
}

/* Body — off-white, Neue Montreal */
body.antialiased,
body {
    font-family: 'Neue Montreal', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, sans-serif !important;
    background: var(--nv-bg) !important;
    color: var(--nv-text) !important;
    -webkit-font-smoothing: antialiased;
}

/* Portal container */
.portal {
    background: var(--nv-bg) !important;
}

/* ============================================================
   SIDEBAR — light off-white, sage accents
   ============================================================ */
.portal .bg-gray-800 {
    background: var(--nv-bg) !important;
    border-right: 1px solid var(--nv-brd);
}

/* Logo container — replace portal's native logo with horizontal SVG */
.portal .h-16.bg-gray-800 {
    background: var(--nv-bg) !important;
    border-bottom: 1px solid var(--nv-brd);
    border-right: 1px solid var(--nv-brd);
    position: relative;
    padding: 0 !important;
}
/* Hide the portal's default square logo div */
.portal .h-16.bg-gray-800 > div[style*="background-image"] {
    background-image: none !important;
}
/* Inject our horizontal Natty Vision logo */
.portal .h-16.bg-gray-800::after {
    content: '';
    position: absolute;
    inset: 0;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1080 184' fill='none'><path d='M91.6455 0.00012207V83.8058C91.6455 85.1548 92.1804 86.4528 93.1348 87.4073L95.8779 90.1505C96.8324 91.105 98.1305 91.6397 99.4795 91.6398H129.625C130.974 91.6398 132.273 91.1051 133.228 90.1505L135.97 87.4073C136.924 86.4528 137.459 85.1549 137.459 83.8058V45.8195H137.453L183.272 0.00012207V137.459L137.453 183.279V145.293C137.453 143.944 136.918 142.646 135.964 141.692L133.221 138.948C132.266 137.994 130.968 137.459 129.619 137.459H96.7246C93.9118 137.459 91.6328 135.181 91.6328 132.368V99.4738C91.6328 98.1246 91.0991 96.8258 90.1445 95.8712L87.4014 93.129C86.4468 92.1744 85.148 91.6398 83.7988 91.6398H59.3809C58.0318 91.6398 56.7338 92.1745 55.7793 93.129L47.3086 101.599C46.354 102.553 45.8193 103.852 45.8193 105.201V183.279H0V137.459L44.3369 93.1349C45.2914 92.1803 45.8261 90.8824 45.8262 89.5333V0.00012207H91.6455ZM594.573 115.152H594.9L616.66 50.2257H635.251L601.39 140.385C596.583 153.492 591.1 163.957 574.824 163.957C569.843 163.957 565.692 163.127 562.371 162.297V149.015H563.857C571.154 150.02 576.637 149.516 580.46 144.863C583.606 141.04 585.944 134.399 582.776 126.425L552.06 50.2257H570.826L594.573 115.152ZM405.732 47.9083C424.499 47.9083 438.437 55.052 438.437 77.6193V117.97C438.437 123.955 439.77 127.101 446.563 126.445V134.747C442.588 136.233 439.747 136.582 436.427 136.582C427.121 136.582 422.817 133.261 421.156 124.96H420.828C415.519 132.431 405.885 137.587 392.777 137.587C375.519 137.587 364.88 127.625 364.88 113.338C364.88 94.7469 378.665 88.9351 399.922 84.7843C413.029 82.2939 421.003 80.6335 421.003 72.507V72.4855C421.003 66.3466 417.355 61.6935 405.732 61.6935C391.795 61.6936 386.639 66.0191 385.809 76.964H368.878C369.708 60.6886 380.172 47.9085 405.732 47.9083ZM826.794 47.8878C849.864 47.8879 860.83 60.3405 861.988 75.1085H845.058C843.9 68.4673 840.579 61.6516 826.969 61.6515C816.504 61.6516 810.867 65.6276 810.867 71.6134C810.868 80.2421 820.328 80.9194 832.627 83.7374L832.648 83.7814C848.247 87.4297 865.2 90.9253 865.2 111.177C865.2 127.452 851.261 137.568 830.66 137.568C803.265 137.567 791.468 124.786 790.638 106.851H807.568C808.399 115.982 812.375 123.782 830.311 123.782C842.435 123.782 847.569 117.796 847.569 112.487C847.569 101.87 837.433 101.04 824.98 98.047C811.523 94.9012 793.259 92.2357 793.259 72.9679C793.259 59.03 805.538 47.8879 826.794 47.8878ZM951.388 47.9083C977.953 47.9085 994.054 66.3464 994.054 92.7365C994.054 119.127 977.953 137.565 951.388 137.566C924.823 137.565 908.721 119.127 908.721 92.7365C908.721 66.3464 924.823 47.9085 951.388 47.9083ZM479.745 50.2452H495.693V64.5333L495.671 64.5109H479.724V110.498C479.724 120.132 484.377 121.29 495.671 120.635V134.922C492.176 135.752 488.374 136.255 483.722 136.255C469.784 136.255 462.137 129.941 462.137 112.005V64.5109H449.859V50.2238H462.137V23.506H479.745V50.2452ZM530.974 50.2452H546.921V64.5333L546.899 64.5109H530.951V110.498C530.951 120.132 535.605 121.29 546.899 120.635V134.922C543.404 135.752 539.602 136.255 534.949 136.255C521.012 136.255 513.365 129.94 513.365 112.005V64.5109H501.087V50.2238H513.365V23.506H530.974V50.2452ZM721.911 113.492H722.086L718.939 16.3634H741.201V135.076H718.438L632.209 16.3634H654.471L721.911 113.492ZM895.484 135.076H878.051V50.2257H895.484V135.076ZM330.815 105.518H331.144V16.3624H350.893V135.075H328.98L275.369 46.2482H275.042V135.075H255.271V16.3624H277.357L330.815 105.518ZM778.254 135.075H760.82V50.2247H778.254V135.075ZM1051.77 47.9083C1067.55 47.9083 1080 57.0398 1080 76.4611V135.075H1062.39V81.4425C1062.39 69.995 1057.58 62.6759 1045.61 62.6759C1032.15 62.6762 1023.53 70.8039 1023.53 83.4308V135.075H1006.09V50.2238H1023.53V61.1906H1023.85C1028.33 54.5493 1037.14 47.9084 1051.75 47.9083H1051.77ZM421.003 90.0714C418.184 92.0593 411.369 94.0479 403.242 95.7081C388.802 98.8539 382.488 103.005 382.488 111.655C382.488 119.454 387.141 123.78 396.601 123.78C411.041 123.78 421.003 116.637 421.003 102.852V90.0714ZM951.388 62.1954C934.785 62.1956 926.154 75.1505 926.154 92.7365C926.154 110.323 934.785 123.278 951.388 123.278C967.991 123.278 976.62 110.17 976.62 92.7365C976.62 75.3034 967.991 62.1956 951.388 62.1954Z' fill='%231a1e1c'/></svg>");
    background-repeat: no-repeat;
    background-position: 16px center;
    background-size: auto 22px;
    pointer-events: none;
}

/* Back to site link */
.portal #back-to-site-link {
    color: var(--nv-t2) !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 10px !important;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    transition: color 0.3s;
}
.portal #back-to-site-link:hover {
    color: var(--nv-text) !important;
    background: transparent !important;
}
.portal #back-to-site-link-icon,
.portal #back-to-site-link svg {
    color: var(--nv-t3) !important;
}

/* Sidebar nav items — default state */
.portal nav a[id$="_nav_item"] {
    color: var(--nv-t2) !important;
    background: transparent !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 11px !important;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    border-radius: var(--nv-rx) !important;
    transition: all 0.3s;
}
.portal nav a[id$="_nav_item"]:hover {
    color: var(--nv-text) !important;
    background: var(--nv-bg-card) !important;
}
.portal nav a[id$="_nav_item"] svg {
    color: var(--nv-t3) !important;
}
.portal nav a[id$="_nav_item"]:hover svg {
    color: var(--nv-green) !important;
}

/* Sidebar nav item — ACTIVE state (currently selected page) */
.portal nav a[id$="_nav_item"].bg-gray-900 {
    background: var(--nv-sage-s) !important;
    color: var(--nv-green) !important;
    border: 1px solid var(--nv-sage);
}
.portal nav a[id$="_nav_item"].bg-gray-900 svg {
    color: var(--nv-green) !important;
}

/* ============================================================
   TOP BAR — white-ish, subtle border
   ============================================================ */
.portal .bg-white {
    background: var(--nv-bg) !important;
}
.portal .h-16.bg-white {
    background: var(--nv-bg) !important;
    border-bottom: 1px solid var(--nv-brd) !important;
}

/* Mobile sidebar toggle button */
.portal button[aria-label="Open sidebar"] {
    border-right-color: var(--nv-brd) !important;
    color: var(--nv-t2) !important;
}
.portal button[aria-label="Open sidebar"]:hover {
    background: var(--nv-bg-card) !important;
    color: var(--nv-text) !important;
}

/* User menu dropdown chevron */
.portal #user-menu .text-gray-500 {
    color: var(--nv-t3) !important;
}

/* User dropdown panel */
.portal .rounded-md.bg-white.shadow-xs {
    background: var(--nv-bg) !important;
    border: 1px solid var(--nv-brd);
    box-shadow: 0 12px 30px rgba(26,30,28,0.08) !important;
}
.portal .rounded-md.bg-white.shadow-xs a {
    color: var(--nv-t2) !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 11px !important;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    transition: all 0.3s;
}
.portal .rounded-md.bg-white.shadow-xs a:hover {
    color: var(--nv-text) !important;
    background: var(--nv-bg-card) !important;
}
.portal .rounded-md.bg-white.shadow-xs .text-sm.leading-5 {
    color: var(--nv-t2) !important;
}
.portal .rounded-md.bg-white.shadow-xs .text-gray-900 {
    color: var(--nv-text) !important;
    font-family: 'Neue Montreal', sans-serif !important;
}
.portal .rounded-md.bg-white.shadow-xs .border-gray-100 {
    border-color: var(--nv-brd) !important;
}

/* ============================================================
   MAIN CONTENT AREA
   ============================================================ */
#portal-content-wrap,
#affiliate-portal-content {
    background: var(--nv-bg) !important;
}

/* Bg gray-100 (main scroll area) -> off-white */
.portal .bg-gray-100 {
    background: var(--nv-bg) !important;
}

/* ============================================================
   PAGE HEADINGS — Instrument Serif
   ============================================================ */
#affiliate-portal-content h1,
#affiliate-portal-content .text-3xl.font-semibold {
    font-family: 'Instrument Serif', serif !important;
    font-weight: 400 !important;
    color: var(--nv-text) !important;
    font-size: clamp(36px, 4vw, 52px) !important;
    line-height: 1.05 !important;
    letter-spacing: -0.02em !important;
}

/* Section headings (h2) */
#affiliate-portal-content h2,
#affiliate-portal-content .text-xl.font-medium {
    font-family: 'Instrument Serif', serif !important;
    font-weight: 400 !important;
    color: var(--nv-text) !important;
    font-size: 26px !important;
    line-height: 1.15 !important;
    letter-spacing: -0.01em !important;
}

/* Section descriptions */
#affiliate-portal-content .text-gray-600 {
    color: var(--nv-t2) !important;
    font-size: 14px !important;
    line-height: 1.6;
}

/* ============================================================
   CARDS — white with sage-tinted border
   ============================================================ */
#affiliate-portal-content .bg-white {
    background: var(--nv-bg2) !important;
    border: 1px solid var(--nv-brd) !important;
}

#affiliate-portal-content .shadow,
#affiliate-portal-content .shadow-sm {
    box-shadow: 0 1px 3px rgba(26,30,28,0.04) !important;
    border-radius: var(--nv-r) !important;
}

#affiliate-portal-content .sm\:rounded-md,
#affiliate-portal-content .rounded-md {
    border-radius: var(--nv-r) !important;
}

/* URL display box (the bordered container holding the URL) */
#affiliate-portal-content .border.border-gray-200 {
    border-color: var(--nv-brd) !important;
    background: var(--nv-bg) !important;
    border-radius: var(--nv-rx) !important;
}

/* The URL text itself */
#affiliate-portal-content [x-text*="getUrlParam"],
#affiliate-portal-content .truncate.break-words {
    color: var(--nv-text) !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 13px !important;
}

/* URL icons */
#affiliate-portal-content svg.text-gray-400 {
    color: var(--nv-green) !important;
}

/* ============================================================
   BUTTONS — "Copy link" → black pill (.bd style)
   ============================================================ */
#affiliate-portal-content button.text-indigo-600,
#affiliate-portal-content button[id$="-copy"] {
    font-family: 'DM Mono', monospace !important;
    font-size: 10px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    background: var(--nv-dark) !important;
    color: var(--nv-ti) !important;
    padding: 10px 20px !important;
    border-radius: var(--nv-rx) !important;
    border: none !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
    text-decoration: none !important;
}
#affiliate-portal-content button.text-indigo-600:hover,
#affiliate-portal-content button[id$="-copy"]:hover {
    background: #2a302d !important;
    color: var(--nv-ti) !important;
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(26,30,28,0.12);
}

/* ============================================================
   FORM INPUTS — page URL, campaign name
   ============================================================ */
#affiliate-portal-content .form-input,
#affiliate-portal-content input[type="text"] {
    background: var(--nv-bg) !important;
    border: 1px solid var(--nv-brd) !important;
    color: var(--nv-text) !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 13px !important;
    border-radius: var(--nv-rx) !important;
    transition: all 0.3s;
}
#affiliate-portal-content .form-input:focus,
#affiliate-portal-content input[type="text"]:focus {
    border-color: var(--nv-green) !important;
    box-shadow: 0 0 0 3px rgba(45,106,79,0.15) !important;
    outline: none !important;
}

#affiliate-portal-content label {
    font-family: 'DM Mono', monospace !important;
    font-size: 10px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    color: var(--nv-t2) !important;
    margin-bottom: 6px !important;
    display: block;
}

#affiliate-portal-content .text-gray-500 {
    color: var(--nv-t3) !important;
    font-size: 12px !important;
}

/* Section divider */
#affiliate-portal-content .border-t.border-gray-200 {
    border-color: var(--nv-brd) !important;
}

/* ============================================================
   DASHBOARD STATS CARDS (if/when they render)
   ============================================================ */
#affiliate-portal-content .bg-gray-50 {
    background: var(--nv-bg-card) !important;
}
#affiliate-portal-content .bg-indigo-100,
#affiliate-portal-content .bg-green-100,
#affiliate-portal-content .bg-blue-100,
#affiliate-portal-content .bg-pink-100 {
    background: var(--nv-sage-s) !important;
}
#affiliate-portal-content .text-indigo-500,
#affiliate-portal-content .text-indigo-600,
#affiliate-portal-content .text-indigo-800,
#affiliate-portal-content .text-green-500,
#affiliate-portal-content .text-green-600,
#affiliate-portal-content .text-green-800 {
    color: var(--nv-green) !important;
}

/* Table styles (Referrals, Payouts, Visits, Coupons) */
#affiliate-portal-content table {
    background: var(--nv-bg2) !important;
    border-radius: var(--nv-r) !important;
    overflow: hidden;
}
#affiliate-portal-content table thead {
    background: var(--nv-bg-card) !important;
    border-bottom: 1px solid var(--nv-brd);
}
#affiliate-portal-content table th {
    font-family: 'DM Mono', monospace !important;
    font-size: 10px !important;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--nv-t2) !important;
    font-weight: 500 !important;
}
#affiliate-portal-content table td {
    color: var(--nv-text) !important;
    border-color: var(--nv-brd) !important;
    font-size: 13px;
}
#affiliate-portal-content table tr {
    border-color: var(--nv-brd) !important;
}

/* Page number / pagination */
#affiliate-portal-content .page-numbers {
    background: var(--nv-bg) !important;
    border-color: var(--nv-brd) !important;
    color: var(--nv-t2) !important;
    font-family: 'DM Mono', monospace !important;
    font-size: 11px !important;
}
#affiliate-portal-content .page-numbers:hover {
    background: var(--nv-bg-card) !important;
    color: var(--nv-text) !important;
}
#affiliate-portal-content .page-numbers.current {
    background: var(--nv-dark) !important;
    color: var(--nv-ti) !important;
    border-color: var(--nv-dark) !important;
}

/* ============================================================
   ERROR STATES
   ============================================================ */
#affiliate-portal-content .border-red-300,
#affiliate-portal-content .text-red-300,
#affiliate-portal-content .text-red-900 {
    border-color: #d4a94c !important;
    color: #b8860b !important;
}
#affiliate-portal-content .text-red-500 {
    color: #d4a94c !important;
}
#affiliate-portal-content .bg-red-50,
#affiliate-portal-content .bg-red-100 {
    background: #faf0d6 !important;
}

/* ============================================================
   AVATAR + USER DETAILS — HIDDEN FOR CLEAN AESTHETIC
   ============================================================ */
.portal .avatar,
.portal img.avatar,
.portal [class*="avatar"] img,
.portal .user-avatar,
#affiliate-portal-content .avatar,
#affiliate-portal-content img.avatar,
.user-avatar,
img.avatar {
    display: none !important;
}
.portal .avatar-wrap,
#affiliate-portal-content .avatar-wrap {
    display: none !important;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1024px) {
    #affiliate-portal-content h1 {
        font-size: 36px !important;
    }
}

@media (max-width: 768px) {
    #affiliate-portal-content h1 {
        font-size: 32px !important;
    }
    #affiliate-portal-content h2 {
        font-size: 22px !important;
    }
}

/* ============================================================
   HIDE REFERRAL URL GENERATOR — we only use the coupon URL
   ============================================================ */
/* Hide the divider + everything after the first URL block on /affiliate-area/urls/ */
#affiliate-portal-content .py-8,
#affiliate-portal-content #referral-url-generator,
#affiliate-portal-content #referral-url-generator ~ * {
    display: none !important;
}
/* The generator is in a sibling grid block — hide the parent grid containing it */
#affiliate-portal-content .md\:grid.md\:grid-cols-3.md\:gap-6:has(#referral-url-generator) {
    display: none !important;
}

/* ============================================================
   COUPON URL HIGHLIGHT — make it FEEL like it's the affiliate's
   ============================================================ */
#affiliate-portal-content #referral-url + .mt-5 .border,
#affiliate-portal-content #referral-url ~ * .border.border-gray-200:first-of-type {
    background: linear-gradient(135deg, var(--nv-sage-s) 0%, var(--nv-bg) 100%) !important;
    border-color: var(--nv-sage) !important;
}
CSS;
}

/* ============================================================
 * AFFILIATE SIGNUP + LOGIN PAGES
 *
 * Registers two virtual URLs (no need to create WP pages):
 *   /affiliate-signup  → custom signup form
 *   /affiliate-login   → custom login form
 *   /affiliate         → redirects to /affiliate-area
 *
 * Styled to match nattyvision.com aesthetic.
 * ============================================================ */

// Add rewrite rules
add_action('init', function() {
    add_rewrite_rule('^affiliates/?$',        'index.php?nv_affiliate_page=landing', 'top');
    add_rewrite_rule('^affiliate-signup/?$',  'index.php?nv_affiliate_page=signup', 'top');
    add_rewrite_rule('^affiliate-login/?$',   'index.php?nv_affiliate_page=login',  'top');
    add_rewrite_rule('^affiliate/?$',         'index.php?nv_affiliate_page=redirect','top');
});

// Whitelist our query var
add_filter('query_vars', function($vars) {
    $vars[] = 'nv_affiliate_page';
    return $vars;
});

// Flush rules once on activation (and on version bump)
register_activation_hook(__FILE__, 'nvacs_flush_rewrites');
function nvacs_flush_rewrites() {
    add_rewrite_rule('^affiliates/?$',        'index.php?nv_affiliate_page=landing', 'top');
    add_rewrite_rule('^affiliate-signup/?$',  'index.php?nv_affiliate_page=signup', 'top');
    add_rewrite_rule('^affiliate-login/?$',   'index.php?nv_affiliate_page=login',  'top');
    add_rewrite_rule('^affiliate/?$',         'index.php?nv_affiliate_page=redirect','top');
    flush_rewrite_rules();
}
// Auto-flush if version changed (so user doesn't have to deactivate/reactivate)
add_action('init', function() {
    if (get_option('nvacs_rules_version') !== NVACS_VERSION) {
        nvacs_flush_rewrites();
        update_option('nvacs_rules_version', NVACS_VERSION);
    }
}, 100);

// Handle our virtual pages
add_action('template_redirect', function() {
    $page = get_query_var('nv_affiliate_page');
    if (!$page) return;

    if ($page === 'redirect') {
        wp_safe_redirect(home_url('/affiliate-area/'));
        exit;
    }

    if ($page === 'landing') {
        nvacs_render_landing_page();
        exit;
    }

    // If user is already logged in AND is an affiliate, send to portal
    if (is_user_logged_in() && function_exists('affwp_is_affiliate') && affwp_is_affiliate()) {
        wp_safe_redirect(home_url('/affiliate-area/'));
        exit;
    }

    if ($page === 'signup') {
        nvacs_render_signup_page();
        exit;
    }
    if ($page === 'login') {
        nvacs_render_login_page();
        exit;
    }
});

/* ============================================================
 * SIGNUP HANDLER — process form POST
 * ============================================================ */
function nvacs_handle_signup_post() {
    $errors  = [];
    $success = false;

    if (empty($_POST['nvacs_signup_nonce']) || !wp_verify_nonce($_POST['nvacs_signup_nonce'], 'nvacs_signup')) {
        return ['errors' => ['Security check failed. Please refresh and try again.'], 'success' => false];
    }

    $first    = sanitize_text_field(wp_unslash($_POST['first_name']  ?? ''));
    $last     = sanitize_text_field(wp_unslash($_POST['last_name']   ?? ''));
    $email    = sanitize_email(wp_unslash($_POST['email']            ?? ''));
    $username = sanitize_user(wp_unslash($_POST['username']          ?? ''), true);
    $password = (string) ($_POST['password'] ?? '');

    if ($first === '')              $errors[] = 'First name is required.';
    if ($last === '')               $errors[] = 'Last name is required.';
    if (!is_email($email))          $errors[] = 'Please enter a valid email address.';
    if ($username === '')           $errors[] = 'Username is required.';
    if (strlen($username) < 3)      $errors[] = 'Username must be at least 3 characters.';
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) $errors[] = 'Username can only contain letters, numbers, hyphens, and underscores.';
    if (strlen($password) < 8)      $errors[] = 'Password must be at least 8 characters.';
    if (username_exists($username)) $errors[] = 'That username is already taken.';
    if (email_exists($email))       $errors[] = 'An account with that email already exists. <a href="' . esc_url(home_url('/affiliate-login/')) . '">Log in instead?</a>';

    if (!empty($errors)) return ['errors' => $errors, 'success' => false];

    // Create the user
    $user_id = wp_insert_user([
        'user_login'   => $username,
        'user_email'   => $email,
        'user_pass'    => $password,
        'first_name'   => $first,
        'last_name'    => $last,
        'display_name' => $first . ' ' . $last,
        'role'         => 'subscriber',
    ]);

    if (is_wp_error($user_id)) {
        return ['errors' => [$user_id->get_error_message()], 'success' => false];
    }

    // Create affiliate + auto-approve
    $affiliate_id = 0;
    if (function_exists('affwp_add_affiliate')) {
        $affiliate_id = affwp_add_affiliate([
            'user_id' => $user_id,
            'status'  => 'active', // auto-approve, no manual review needed
        ]);
    }

    // Create the affiliate's coupon directly using their username as the code.
    // Custom registration forms don't trigger AffiliateWP's dynamic coupon
    // generation, so we do it ourselves.
    if ($affiliate_id) {
        nvacs_create_coupon_for_new_affiliate($affiliate_id, $username);
    }

    // Auto-login
    wp_clear_auth_cookie();
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    return ['errors' => [], 'success' => true, 'user_id' => $user_id];
}

/* ============================================================
 * Manually create the affiliate's coupon when our custom signup form is used.
 *
 * Steps:
 *  1. Build the coupon code from the username (uppercase)
 *  2. Create a WC coupon based on the template-affiliate settings
 *  3. Tag the coupon with affwp_discount_affiliate meta so AffiliateWP
 *     recognizes it for commission tracking
 *  4. Insert into AffiliateWP's coupons table via affiliate_wp()->affiliates->coupons
 * ============================================================ */
function nvacs_create_coupon_for_new_affiliate($affiliate_id, $username) {
    if (!$affiliate_id || !$username) return false;
    if (!class_exists('WC_Coupon') || !function_exists('wc_get_coupon_id_by_code')) return false;

    // Coupon code = uppercase username (matches the "username" format setting)
    $code = strtoupper($username);

    // Don't duplicate
    if (wc_get_coupon_id_by_code($code)) {
        $wc_coupon_id = wc_get_coupon_id_by_code($code);
    } else {
        $d = nvacs_get_template_discount();
        try {
            $coupon = new WC_Coupon();
            $coupon->set_code($code);
            $coupon->set_discount_type($d['type']);
            $coupon->set_amount($d['amount']);
            $coupon->set_individual_use(false);
            $coupon->save();
            $wc_coupon_id = $coupon->get_id();
        } catch (\Exception $e) {
            error_log('[NVACS] Failed creating coupon for ' . $username . ': ' . $e->getMessage());
            return false;
        }
    }

    if (!$wc_coupon_id) return false;

    // Tag the WC coupon with the affiliate ID so AffiliateWP tracks commissions
    update_post_meta($wc_coupon_id, 'affwp_discount_affiliate', $affiliate_id);

    // Insert into AffiliateWP's coupons table so it shows in their "Coupons" tab
    if (function_exists('affiliate_wp') && affiliate_wp()->affiliates && affiliate_wp()->affiliates->coupons) {
        $existing = affwp_get_affiliate_coupons($affiliate_id);
        $already  = false;
        if (!empty($existing)) {
            if (!is_array($existing)) $existing = [$existing];
            foreach ($existing as $c) {
                $existing_code = nvacs_extract_coupon_code($c);
                if (strtoupper($existing_code) === $code) { $already = true; break; }
            }
        }
        if (!$already) {
            affiliate_wp()->affiliates->coupons->add([
                'affiliate_id'  => $affiliate_id,
                'coupon_code'   => $code,
                'coupon_id'     => $wc_coupon_id,
                'integration'   => 'woocommerce',
                'status'        => 'active',
                'is_template'   => 0,
            ]);
        }
    }

    return $wc_coupon_id;
}

/* ============================================================
 * LOGIN HANDLER — process form POST
 * ============================================================ */
function nvacs_handle_login_post() {
    if (empty($_POST['nvacs_login_nonce']) || !wp_verify_nonce($_POST['nvacs_login_nonce'], 'nvacs_login')) {
        return ['errors' => ['Security check failed. Please refresh and try again.']];
    }

    $username = sanitize_text_field(wp_unslash($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $remember = !empty($_POST['remember']);

    if ($username === '' || $password === '') {
        return ['errors' => ['Please enter both username/email and password.']];
    }

    $user = wp_signon([
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => $remember,
    ], is_ssl());

    if (is_wp_error($user)) {
        return ['errors' => ['Invalid username or password.']];
    }

    return ['errors' => [], 'user' => $user];
}

/* ============================================================
 * SIGNUP PAGE RENDER
 * ============================================================ */
function nvacs_render_signup_page() {
    $errors  = [];
    $success = false;
    $values  = ['first_name' => '', 'last_name' => '', 'email' => '', 'username' => ''];

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nvacs_signup_nonce'])) {
        $result  = nvacs_handle_signup_post();
        $errors  = $result['errors'];
        $success = $result['success'];

        // Repopulate form on error
        $values = [
            'first_name' => sanitize_text_field(wp_unslash($_POST['first_name'] ?? '')),
            'last_name'  => sanitize_text_field(wp_unslash($_POST['last_name']  ?? '')),
            'email'      => sanitize_email(wp_unslash($_POST['email']           ?? '')),
            'username'   => sanitize_user(wp_unslash($_POST['username']         ?? ''), true),
        ];

        if ($success) {
            // Give the affiliate creation a moment, then redirect to portal
            wp_safe_redirect(home_url('/affiliate-area/'));
            exit;
        }
    }

    nvacs_render_page_shell('Become an Affiliate', function() use ($errors, $values) {
        ?>
        <div class="nv-form-intro">
            <h1>Become an <em>Affiliate</em></h1>
            <p>Earn commission on every order placed through your unique coupon. Sign up below and your code goes live instantly.</p>
        </div>

        <?php if (!empty($errors)) : ?>
            <div class="nv-errors">
                <?php foreach ($errors as $err) : ?>
                    <p><?php echo wp_kses_post($err); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="nv-form">
            <?php wp_nonce_field('nvacs_signup', 'nvacs_signup_nonce'); ?>

            <div class="nv-form-row nv-form-row-2">
                <div class="nv-field">
                    <label for="first_name">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="<?php echo esc_attr($values['first_name']); ?>" required>
                </div>
                <div class="nv-field">
                    <label for="last_name">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="<?php echo esc_attr($values['last_name']); ?>" required>
                </div>
            </div>

            <div class="nv-field">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" value="<?php echo esc_attr($values['email']); ?>" required>
            </div>

            <div class="nv-field">
                <label for="username">Username <span class="nv-label-hint">(this becomes your coupon code)</span></label>
                <input type="text" name="username" id="username" value="<?php echo esc_attr($values['username']); ?>" pattern="[a-zA-Z0-9_-]+" minlength="3" required>
                <p class="nv-field-help">Choose carefully — your username becomes your unique affiliate coupon code. Example: username <strong>bob</strong> → coupon code <strong>BOB</strong>.</p>
            </div>

            <div class="nv-field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" minlength="8" required>
                <p class="nv-field-help">At least 8 characters.</p>
            </div>

            <button type="submit" class="nv-btn nv-btn-dark">Create Affiliate Account →</button>

            <p class="nv-form-foot">
                Already an affiliate? <a href="<?php echo esc_url(home_url('/affiliate-login/')); ?>">Log in</a>
            </p>
        </form>
        <?php
    });
}

/* ============================================================
 * LOGIN PAGE RENDER
 * ============================================================ */
function nvacs_render_login_page() {
    $errors = [];
    $username = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nvacs_login_nonce'])) {
        $result   = nvacs_handle_login_post();
        $errors   = $result['errors'];
        $username = sanitize_text_field(wp_unslash($_POST['username'] ?? ''));

        if (empty($errors)) {
            wp_safe_redirect(home_url('/affiliate-area/'));
            exit;
        }
    }

    nvacs_render_page_shell('Affiliate Login', function() use ($errors, $username) {
        ?>
        <div class="nv-form-intro">
            <h1>Affiliate <em>Login</em></h1>
            <p>Welcome back. Sign in to see your stats and grab your coupon URL.</p>
        </div>

        <?php if (!empty($errors)) : ?>
            <div class="nv-errors">
                <?php foreach ($errors as $err) : ?>
                    <p><?php echo wp_kses_post($err); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" class="nv-form">
            <?php wp_nonce_field('nvacs_login', 'nvacs_login_nonce'); ?>

            <div class="nv-field">
                <label for="username">Username or Email</label>
                <input type="text" name="username" id="username" value="<?php echo esc_attr($username); ?>" required>
            </div>

            <div class="nv-field">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <div class="nv-field nv-field-row">
                <label class="nv-checkbox">
                    <input type="checkbox" name="remember" value="1" checked>
                    <span>Remember me</span>
                </label>
                <a href="<?php echo esc_url(wp_lostpassword_url(home_url('/affiliate-login/'))); ?>" class="nv-forgot">Forgot password?</a>
            </div>

            <button type="submit" class="nv-btn nv-btn-dark">Sign In →</button>

            <p class="nv-form-foot">
                Don't have an account? <a href="<?php echo esc_url(home_url('/affiliate-signup/')); ?>">Sign up</a>
            </p>
        </form>
        <?php
    });
}

/* ============================================================
 * SHARED PAGE SHELL — full HTML wrapper with nattyvision styling
 * ============================================================ */
function nvacs_render_page_shell($title, $body_callback) {
    $logo = home_url();
    ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($title); ?> — <?php bloginfo('name'); ?></title>
    <meta name="robots" content="noindex,follow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap" rel="stylesheet">
    <style><?php echo nvacs_signup_login_css(); ?></style>
</head>
<body class="nv-auth-body">
    <div class="nv-auth-wrap">
        <header class="nv-auth-header">
            <a href="<?php echo esc_url($logo); ?>" class="nv-auth-logo" aria-label="Natty Vision">
                <?php echo nvacs_logo_svg(32); ?>
            </a>
        </header>

        <main class="nv-auth-main">
            <div class="nv-auth-card">
                <?php $body_callback(); ?>
            </div>
        </main>

        <footer class="nv-auth-footer">
            <p>©<?php echo date('Y'); ?> Natty Vision. For research purposes only.</p>
        </footer>
    </div>
</body>
</html>
    <?php
}

/* ============================================================
 * SIGNUP + LOGIN PAGE STYLES
 * ============================================================ */
function nvacs_signup_login_css() {
    return <<<CSS
:root {
    --bg: #f2f0eb;
    --bg2: #e9e7e1;
    --bg-card: #eae8e2;
    --sage: #c5d4c0;
    --sage-s: #dce5d8;
    --sage-d: #a8bfa2;
    --dark: #1a1e1c;
    --green: #2d6a4f;
    --green-b: #52b788;
    --text: #1a1e1c;
    --t2: #4a4f4c;
    --t3: #7a7f7c;
    --ti: #f2f0eb;
    --brd: #d4d2cc;
    --brd2: #c4c2bc;
    --r: 16px;
    --rx: 8px;
    --rp: 100px;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body.nv-auth-body {
    font-family: 'Neue Montreal', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, sans-serif;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
    line-height: 1.5;
}

.nv-auth-wrap {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding: 32px;
}

.nv-auth-header {
    text-align: center;
    padding: 16px 0 40px;
}
.nv-auth-logo {
    display: inline-block;
}
.nv-auth-logo svg {
    height: 32px;
    width: auto;
    display: block;
}
.nv-auth-logo img {
    height: 44px;
    width: auto;
    display: inline-block;
}

.nv-auth-main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px 0;
}

.nv-auth-card {
    background: var(--bg2);
    border: 1px solid var(--brd);
    border-radius: 20px;
    padding: 48px;
    width: 100%;
    max-width: 520px;
    box-shadow: 0 8px 30px rgba(26,30,28,0.05);
}

.nv-form-intro {
    margin-bottom: 32px;
}
.nv-form-intro h1 {
    font-family: 'Instrument Serif', serif;
    font-size: clamp(36px, 5vw, 52px);
    font-weight: 400;
    line-height: 1.05;
    letter-spacing: -0.02em;
    margin-bottom: 12px;
    color: var(--text);
}
.nv-form-intro h1 em {
    font-style: italic;
    color: var(--green);
}
.nv-form-intro p {
    font-size: 14px;
    color: var(--t2);
    line-height: 1.6;
}

.nv-errors {
    background: #faf0d6;
    border: 1px solid #e5cb74;
    border-radius: var(--rx);
    padding: 14px 18px;
    margin-bottom: 24px;
}
.nv-errors p {
    font-size: 13px;
    color: #6b5414;
    line-height: 1.5;
}
.nv-errors p + p {
    margin-top: 6px;
}
.nv-errors a {
    color: var(--green);
    text-decoration: underline;
}

.nv-form {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.nv-form-row-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.nv-field {
    display: flex;
    flex-direction: column;
}
.nv-field label {
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--t2);
    margin-bottom: 8px;
}
.nv-label-hint {
    color: var(--green);
    text-transform: none;
    letter-spacing: 0;
    font-size: 10px;
    font-style: italic;
}
.nv-field input[type="text"],
.nv-field input[type="email"],
.nv-field input[type="password"] {
    background: var(--bg);
    border: 1px solid var(--brd);
    border-radius: var(--rx);
    padding: 12px 14px;
    font-family: 'DM Mono', monospace;
    font-size: 13px;
    color: var(--text);
    transition: all 0.3s;
    width: 100%;
}
.nv-field input:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
    background: var(--bg);
}
.nv-field-help {
    font-size: 12px;
    color: var(--t3);
    margin-top: 8px;
    line-height: 1.5;
}
.nv-field-help strong {
    color: var(--green);
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    text-transform: uppercase;
}

.nv-field-row {
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}
.nv-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--t2);
    margin: 0;
}
.nv-checkbox input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--green);
    margin: 0;
}
.nv-checkbox span {
    user-select: none;
}
.nv-forgot {
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--green);
    text-decoration: none;
    transition: color 0.3s;
}
.nv-forgot:hover {
    color: var(--dark);
}

.nv-btn {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 16px 28px;
    border-radius: var(--rx);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none;
    margin-top: 8px;
    width: 100%;
}
.nv-btn-dark {
    background: var(--dark);
    color: var(--ti);
}
.nv-btn-dark:hover {
    background: #2a302d;
    transform: translateY(-1px);
    box-shadow: 0 12px 30px rgba(26,30,28,0.15);
}

.nv-form-foot {
    text-align: center;
    margin-top: 12px;
    font-size: 13px;
    color: var(--t2);
}
.nv-form-foot a {
    color: var(--green);
    text-decoration: none;
    font-weight: 500;
    border-bottom: 1px solid var(--sage);
    padding-bottom: 1px;
    transition: all 0.3s;
}
.nv-form-foot a:hover {
    color: var(--dark);
    border-color: var(--dark);
}

.nv-auth-footer {
    text-align: center;
    padding: 32px 0 8px;
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--t3);
}

@media (max-width: 768px) {
    .nv-auth-wrap { padding: 16px; }
    .nv-auth-header { padding: 8px 0 24px; }
    .nv-auth-logo svg { height: 26px; }
    .nv-auth-card { padding: 28px 22px; border-radius: 16px; }
    .nv-form-intro h1 { font-size: 32px; margin-bottom: 10px; }
    .nv-form-intro p { font-size: 13px; }
    .nv-form-intro { margin-bottom: 24px; }
    .nv-form { gap: 16px; }
    .nv-form-row-2 { grid-template-columns: 1fr; gap: 16px; }
    .nv-field-row { flex-direction: column; align-items: stretch; gap: 12px; }
    .nv-forgot { text-align: right; }
    .nv-field input { font-size: 14px; padding: 13px 14px; }
    .nv-btn {
        padding: 16px 20px;
        font-size: 11px;
    }
    .nv-form-foot { font-size: 13px; }
    .nv-auth-footer { padding: 24px 8px; }
}
CSS;
}

/* ============================================================
 * LANDING PAGE RENDER (/affiliates) — public marketing page
 * ============================================================ */
function nvacs_render_landing_page() {
    $signup_url = esc_url(home_url('/affiliate-signup/'));
    $login_url  = esc_url(home_url('/affiliate-login/'));
    $home       = esc_url(home_url('/'));
    ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Affiliate Program — <?php bloginfo('name'); ?></title>
    <meta name="description" content="Earn 25% commission on every order through your unique coupon code. Join the Natty Vision affiliate program.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap" rel="stylesheet">
    <style><?php echo nvacs_landing_css(); ?></style>
</head>
<body class="nv-landing-body">

<header class="nv-land-nav">
    <a href="<?php echo $home; ?>" class="nv-land-logo" aria-label="Natty Vision">
        <?php echo nvacs_logo_svg(28); ?>
    </a>
    <div class="nv-land-nav-r">
        <a href="<?php echo $login_url; ?>" class="nv-land-nav-link">Log in</a>
        <a href="<?php echo $signup_url; ?>" class="nv-btn nv-btn-dark nv-btn-sm">Sign up</a>
    </div>
</header>

<section class="nv-land-hero">
    <div class="nv-land-eyebrow">Affiliate Program</div>
    <h1>Earn <em>25%</em> on every order</h1>
    <p class="nv-land-sub">Share your unique coupon code with your audience. They get a discount, you earn commission on every sale — automatically.</p>
    <div class="nv-land-cta">
        <a href="<?php echo $signup_url; ?>" class="nv-btn nv-btn-dark">Become an Affiliate →</a>
        <a href="<?php echo $login_url; ?>" class="nv-btn nv-btn-ghost">I have an account</a>
    </div>
</section>

<section class="nv-land-benefits">
    <div class="nv-land-benefit">
        <div class="nv-land-bn">25%</div>
        <div class="nv-land-bt">Commission per sale</div>
        <p>Every order placed with your code earns you 25%. No tiers, no minimums.</p>
    </div>
    <div class="nv-land-benefit">
        <div class="nv-land-bn">Instant</div>
        <div class="nv-land-bt">Approval & setup</div>
        <p>Sign up and your coupon code is live immediately. No waiting, no manual approval.</p>
    </div>
    <div class="nv-land-benefit">
        <div class="nv-land-bn">5%</div>
        <div class="nv-land-bt">Discount for your audience</div>
        <p>Your followers save when they use your code, so sharing actually helps them too.</p>
    </div>
</section>

<section class="nv-land-final">
    <h2>Ready to <em>start</em>?</h2>
    <p>Takes 30 seconds. Pick your username, get your code.</p>
    <a href="<?php echo $signup_url; ?>" class="nv-btn nv-btn-dark">Create Affiliate Account →</a>
</section>

<footer class="nv-land-footer">
    <p>©<?php echo date('Y'); ?> Natty Vision. For research purposes only.</p>
</footer>

</body>
</html>
    <?php
}

/* ============================================================
 * LANDING PAGE CSS
 * ============================================================ */
function nvacs_landing_css() {
    return <<<CSS
:root {
    --bg: #f2f0eb;
    --bg2: #e9e7e1;
    --bg-card: #eae8e2;
    --sage: #c5d4c0;
    --sage-s: #dce5d8;
    --sage-d: #a8bfa2;
    --dark: #1a1e1c;
    --green: #2d6a4f;
    --green-b: #52b788;
    --text: #1a1e1c;
    --t2: #4a4f4c;
    --t3: #7a7f7c;
    --ti: #f2f0eb;
    --brd: #d4d2cc;
    --brd2: #c4c2bc;
    --r: 16px;
    --rx: 8px;
    --rp: 100px;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body.nv-landing-body {
    font-family: 'Neue Montreal', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, sans-serif;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    line-height: 1.5;
    min-height: 100vh;
}

/* Top nav */
.nv-land-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 48px;
    border-bottom: 1px solid var(--brd);
    background: var(--bg);
}
.nv-land-logo { display: inline-flex; align-items: center; }
.nv-land-logo svg { height: 28px; width: auto; display: block; }
.nv-land-logo img { height: 38px; width: auto; display: block; }
.nv-land-nav-r { display: flex; align-items: center; gap: 18px; }
.nv-land-nav-link {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--t2);
    text-decoration: none;
    transition: color 0.3s;
}
.nv-land-nav-link:hover { color: var(--text); }

/* Hero */
.nv-land-hero {
    max-width: 760px;
    margin: 0 auto;
    padding: 120px 32px 80px;
    text-align: center;
}
.nv-land-eyebrow {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--green);
    margin-bottom: 20px;
}
.nv-land-hero h1 {
    font-family: 'Instrument Serif', serif;
    font-weight: 400;
    font-size: clamp(48px, 7vw, 88px);
    line-height: 1.02;
    letter-spacing: -0.03em;
    margin-bottom: 24px;
    color: var(--text);
}
.nv-land-hero h1 em { font-style: italic; color: var(--green); }
.nv-land-sub {
    font-size: 17px;
    line-height: 1.65;
    color: var(--t2);
    max-width: 560px;
    margin: 0 auto 36px;
}
.nv-land-cta {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Buttons */
.nv-btn {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    padding: 14px 28px;
    border-radius: var(--rx);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    text-decoration: none;
}
.nv-btn-dark { background: var(--dark); color: var(--ti); }
.nv-btn-dark:hover {
    background: #2a302d;
    transform: translateY(-1px);
    box-shadow: 0 12px 30px rgba(26,30,28,0.15);
}
.nv-btn-ghost {
    background: transparent;
    color: var(--text);
    border: 1px solid var(--brd2);
}
.nv-btn-ghost:hover { background: var(--bg-card); border-color: var(--t3); }
.nv-btn-sm { padding: 10px 20px; font-size: 10px; }

/* Benefits */
.nv-land-benefits {
    max-width: 1080px;
    margin: 0 auto;
    padding: 60px 32px 80px;
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
.nv-land-benefit {
    background: var(--sage-s);
    border: 1px solid var(--sage);
    border-radius: var(--r);
    padding: 36px 30px;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.nv-land-benefit:hover {
    transform: translateY(-4px);
    background: #d3e0cf;
    border-color: var(--sage-d);
    box-shadow: 0 16px 40px rgba(45, 106, 79, 0.08);
}
.nv-land-bn {
    font-family: 'Instrument Serif', serif;
    font-size: 56px;
    line-height: 1;
    color: var(--green);
    margin-bottom: 14px;
    letter-spacing: -0.02em;
}
.nv-land-bt {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text);
    margin-bottom: 12px;
}
.nv-land-benefit p {
    font-size: 14px;
    line-height: 1.6;
    color: var(--t2);
}

/* Final CTA */
.nv-land-final {
    background: var(--dark);
    color: var(--ti);
    padding: 100px 32px;
    text-align: center;
}
.nv-land-final h2 {
    font-family: 'Instrument Serif', serif;
    font-size: clamp(38px, 5vw, 64px);
    font-weight: 400;
    line-height: 1.05;
    letter-spacing: -0.02em;
    margin-bottom: 16px;
    color: var(--ti);
}
.nv-land-final h2 em { font-style: italic; color: var(--green-b); }
.nv-land-final p {
    font-size: 16px;
    color: #b0aea8;
    margin-bottom: 32px;
}
.nv-land-final .nv-btn-dark {
    background: var(--green-b);
    color: var(--dark);
}
.nv-land-final .nv-btn-dark:hover {
    background: #6cc99a;
    box-shadow: 0 12px 30px rgba(82,183,136,0.3);
}

/* Footer */
.nv-land-footer {
    padding: 32px 24px;
    text-align: center;
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--t3);
    border-top: 1px solid var(--brd);
}

@media (max-width: 768px) {
    .nv-land-nav {
        padding: 14px 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    .nv-land-logo svg { height: 24px; }
    .nv-land-nav-r { gap: 12px; }
    .nv-land-nav-link { font-size: 10px; }
    .nv-btn-sm { padding: 9px 14px; font-size: 10px; }

    .nv-land-hero { padding: 48px 20px 40px; }
    .nv-land-eyebrow { font-size: 10px; margin-bottom: 14px; }
    .nv-land-sub { font-size: 15px; margin-bottom: 28px; }
    .nv-land-cta { flex-direction: column; gap: 10px; width: 100%; }
    .nv-land-cta .nv-btn { width: 100%; padding: 16px 20px; }

    .nv-land-benefits {
        grid-template-columns: 1fr;
        padding: 20px 20px 50px;
        gap: 12px;
    }
    .nv-land-benefit { padding: 26px 22px; }
    .nv-land-bn { font-size: 44px; margin-bottom: 10px; }
    .nv-land-bt { font-size: 10px; margin-bottom: 10px; }
    .nv-land-benefit p { font-size: 13px; }

    .nv-land-final { padding: 60px 20px; }
    .nv-land-final p { font-size: 14px; margin-bottom: 24px; }
    .nv-land-final .nv-btn { width: 100%; padding: 16px 20px; }

    .nv-land-footer { padding: 24px 20px; font-size: 9px; }
}
CSS;
}

/* ============================================================
 * DASHBOARD MINI-GUIDE — injected into /affiliate-area/
 *
 * Adds a "Your Affiliate Toolkit" block at the top of the dashboard
 * showing the affiliate's code, a "share your URL" tip, and a copy button.
 * Only renders on the dashboard page (not other portal tabs).
 * ============================================================ */
add_action('wp_footer', function() {
    if (!nvacs_is_affiliate_portal()) return;
    if (!is_user_logged_in()) return;

    // Only inject on dashboard, not other portal pages
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $is_dashboard = preg_match('#/affiliate-area/?(\?|$)#', $uri);
    if (!$is_dashboard) return;

    $affiliate_id = function_exists('affwp_get_affiliate_id') ? affwp_get_affiliate_id() : 0;
    if (!$affiliate_id) return;

    $user = wp_get_current_user();
    $username = $user ? $user->user_login : '';
    if (!$username) return;

    $code      = strtoupper($username);
    $share_url = home_url('/?nv_coupon=' . strtolower($username));
    $urls_url  = home_url('/affiliate-area/urls/');

    ?>
    <script>
    (function() {
        // Wait for the dashboard to render
        function injectGuide() {
            var content = document.getElementById('affiliate-portal-content');
            if (!content) return false;
            if (document.getElementById('nv-dash-guide')) return true; // already injected

            var h1 = content.querySelector('h1');
            if (!h1) return false;

            var guide = document.createElement('div');
            guide.id = 'nv-dash-guide';
            guide.className = 'nv-dash-guide';
            guide.innerHTML = <?php echo wp_json_encode(nvacs_dashboard_guide_html($code, $share_url, $urls_url)); ?>;

            // Insert after the h1
            h1.parentNode.insertBefore(guide, h1.nextSibling);

            // Wire up copy buttons
            guide.querySelectorAll('.nv-dg-copy').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var text = btn.getAttribute('data-copy') || '';
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text).then(function() {
                            var label = btn.querySelector('span');
                            var original = label.textContent;
                            label.textContent = 'Copied!';
                            setTimeout(function() { label.textContent = original; }, 1800);
                        });
                    }
                });
            });
            return true;
        }

        var tries = 0;
        var iv = setInterval(function() {
            if (injectGuide() || ++tries > 60) clearInterval(iv);
        }, 100);
    })();
    </script>
    <style>
    .nv-dash-guide {
        background: linear-gradient(135deg, #dce5d8 0%, #f2f0eb 100%);
        border: 1px solid #c5d4c0;
        border-radius: 16px;
        padding: 28px 32px;
        margin: 0 0 32px 0;
        font-family: 'Neue Montreal', -apple-system, sans-serif;
    }
    .nv-dg-label {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #2d6a4f;
        margin-bottom: 10px;
    }
    .nv-dg-title {
        font-family: 'Instrument Serif', serif;
        font-size: 28px;
        font-weight: 400;
        line-height: 1.1;
        letter-spacing: -0.01em;
        color: #1a1e1c;
        margin-bottom: 18px;
    }
    .nv-dg-title em { font-style: italic; color: #2d6a4f; }
    .nv-dg-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-bottom: 16px;
    }
    .nv-dg-block {
        background: rgba(255,255,255,0.5);
        border: 1px solid #c5d4c0;
        border-radius: 10px;
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .nv-dg-block-label {
        font-family: 'DM Mono', monospace;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #4a4f4c;
    }
    .nv-dg-value {
        font-family: 'DM Mono', monospace;
        font-size: 14px;
        color: #1a1e1c;
        word-break: break-all;
        line-height: 1.4;
    }
    .nv-dg-copy {
        font-family: 'DM Mono', monospace;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        background: #1a1e1c;
        color: #f2f0eb;
        padding: 8px 16px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        align-self: flex-start;
    }
    .nv-dg-copy:hover {
        background: #2a302d;
        transform: translateY(-1px);
    }
    .nv-dg-tip {
        font-size: 13px;
        line-height: 1.6;
        color: #4a4f4c;
        margin-top: 8px;
    }
    .nv-dg-tip strong { color: #1a1e1c; font-weight: 500; }
    .nv-dg-tip a { color: #2d6a4f; text-decoration: underline; }
    @media (max-width: 700px) {
        .nv-dg-grid { grid-template-columns: 1fr; }
        .nv-dash-guide { padding: 22px 22px; }
    }
    </style>
    <?php
}, PHP_INT_MAX);

function nvacs_dashboard_guide_html($code, $share_url, $urls_url) {
    $code      = esc_html($code);
    $share_url_esc = esc_html($share_url);
    $share_url_attr = esc_attr($share_url);
    $code_attr = esc_attr($code);
    return <<<HTML
<div class="nv-dg-label">Your Affiliate Toolkit</div>
<div class="nv-dg-title">Welcome — here's <em>how to share</em></div>
<div class="nv-dg-grid">
    <div class="nv-dg-block">
        <div class="nv-dg-block-label">Your code</div>
        <div class="nv-dg-value">{$code}</div>
        <button type="button" class="nv-dg-copy" data-copy="{$code_attr}"><span>Copy code</span></button>
    </div>
    <div class="nv-dg-block">
        <div class="nv-dg-block-label">Your share URL</div>
        <div class="nv-dg-value">{$share_url_esc}</div>
        <button type="button" class="nv-dg-copy" data-copy="{$share_url_attr}"><span>Copy URL</span></button>
    </div>
</div>
<p class="nv-dg-tip"><strong>How to share:</strong> Post your code <strong>{$code}</strong> on social media so your audience can use it at checkout. Or share your URL — anyone who clicks it gets the discount auto-applied. You can grab both anytime from the <a href="{$urls_url}">Affiliate URLs</a> tab.</p>
HTML;
}

/* ============================================================
 * REQUEST CUSTOM LANDER — sidebar item + form page
 *
 * Adds "Request Custom Lander" to the portal sidebar nav,
 * pointing to /affiliate-area/request-lander/.
 * The page is intercepted here and rendered with the matching style.
 * On submit, emails admin@nattyvision.com.
 * ============================================================ */
define('NVACS_ADMIN_EMAIL', 'admin@nattyvision.com');

/* ============================================================
 * Auto-create a real WP page for "Request Lander" on activation.
 *
 * AffiliateWP's Menu Links feature requires a real WordPress page,
 * not a virtual URL. We create one (slug: request-lander) so it
 * shows up in the dropdown. The page content is empty — when an
 * affiliate visits it, our template_redirect handler intercepts
 * and renders the form instead.
 * ============================================================ */
register_activation_hook(__FILE__, 'nvacs_create_request_lander_page');
add_action('init', 'nvacs_create_request_lander_page', 50);
function nvacs_create_request_lander_page() {
    static $checked = false;
    if ($checked) return;
    $checked = true;

    $existing = get_page_by_path('request-lander');
    if ($existing) return;

    wp_insert_post([
        'post_title'   => 'Request Lander',
        'post_name'    => 'request-lander',
        'post_status'  => 'publish',
        'post_type'    => 'page',
        'post_content' => '<!-- Handled by Natty Affiliate Coupon Sync plugin -->',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
    ]);
}

/*
 * To add the "Request Lander" sidebar item in the Affiliate Portal:
 *   1. WP Admin → AffiliateWP → Settings → Affiliate Portal
 *   2. Scroll to "Menu Links"
 *   3. Click "Add New Menu Link"
 *   4. Name: Request Lander
 *   5. Page: select "Request Lander" from the dropdown
 *   6. Save Changes
 *
 * When affiliates click that link, they hit /request-lander/
 * (or /affiliate-area/request-lander/) which our plugin intercepts
 * and renders the form.
 */

// Intercept /request-lander/ or /affiliate-area/request-lander/ — render the form
add_action('template_redirect', function() {
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $is_request_lander =
        strpos($uri, '/affiliate-area/request-lander') !== false ||
        preg_match('#/request-lander/?(\?|$)#', $uri);
    if (!$is_request_lander) return;

    if (!is_user_logged_in() || !function_exists('affwp_is_affiliate') || !affwp_is_affiliate()) {
        wp_safe_redirect(home_url('/affiliate-login/'));
        exit;
    }

    nvacs_render_request_lander_page();
    exit;
});

function nvacs_render_request_lander_page() {
    $sent       = false;
    $errors     = [];
    $subject_in = '';
    $message_in = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nvacs_lander_nonce'])) {
        if (!wp_verify_nonce($_POST['nvacs_lander_nonce'], 'nvacs_request_lander')) {
            $errors[] = 'Security check failed. Please refresh and try again.';
        } else {
            $subject_in = sanitize_text_field(wp_unslash($_POST['subject'] ?? ''));
            $message_in = sanitize_textarea_field(wp_unslash($_POST['message'] ?? ''));

            if ($subject_in === '') $errors[] = 'Subject is required.';
            if ($message_in === '') $errors[] = 'Message is required.';
            if (strlen($message_in) < 20) $errors[] = 'Please give us at least 20 characters of detail.';

            if (empty($errors)) {
                $user         = wp_get_current_user();
                $affiliate_id = function_exists('affwp_get_affiliate_id') ? affwp_get_affiliate_id() : 0;
                $code         = $user ? strtoupper($user->user_login) : '';

                $email_subject = '[Lander Request] ' . $subject_in;
                $email_body  = "New custom lander request from an affiliate.\n\n";
                $email_body .= "Affiliate name: " . ($user ? $user->display_name : '(unknown)') . "\n";
                $email_body .= "Affiliate email: " . ($user ? $user->user_email : '(unknown)') . "\n";
                $email_body .= "Affiliate username: " . ($user ? $user->user_login : '(unknown)') . "\n";
                $email_body .= "Affiliate ID: " . $affiliate_id . "\n";
                $email_body .= "Coupon code: " . $code . "\n";
                $email_body .= "\n---\n\n";
                $email_body .= "Subject: " . $subject_in . "\n\n";
                $email_body .= "Message:\n" . $message_in . "\n";

                // Use the WordPress admin email as From — same one WooCommerce
                // uses for order confirmations, so we know mail accepts it.
                $from_email = get_option('admin_email');
                $site_name  = get_bloginfo('name');
                $headers = [
                    'Content-Type: text/plain; charset=UTF-8',
                    'From: ' . $site_name . ' <' . $from_email . '>',
                ];
                if ($user && $user->user_email) {
                    $headers[] = 'Reply-To: ' . $user->user_email;
                }

                // Always save the request to the database first (in case email fails)
                $saved_request = [
                    'time'         => current_time('mysql'),
                    'affiliate_id' => $affiliate_id,
                    'user_id'      => $user ? $user->ID : 0,
                    'name'         => $user ? $user->display_name : '',
                    'email'        => $user ? $user->user_email : '',
                    'username'     => $user ? $user->user_login : '',
                    'code'         => $code,
                    'subject'      => $subject_in,
                    'message'      => $message_in,
                ];
                $all_requests = get_option('nvacs_lander_requests', []);
                array_unshift($all_requests, $saved_request);
                $all_requests = array_slice($all_requests, 0, 100); // keep last 100
                update_option('nvacs_lander_requests', $all_requests, false);

                // Hook for debugging mail failures
                $mail_error = '';
                $catch_error = function($wp_error) use (&$mail_error) {
                    if (is_object($wp_error) && method_exists($wp_error, 'get_error_message')) {
                        $mail_error = $wp_error->get_error_message();
                    }
                };
                add_action('wp_mail_failed', $catch_error);

                $sent = wp_mail(NVACS_ADMIN_EMAIL, $email_subject, $email_body, $headers);

                remove_action('wp_mail_failed', $catch_error);

                if (!$sent) {
                    error_log('[NVACS] wp_mail to ' . NVACS_ADMIN_EMAIL . ' failed: ' . $mail_error);
                    // Don't show error to user — request is saved to DB, we'll see it in admin
                    // Mark as sent so user sees success message
                    $sent = true;
                }
            }
        }
    }

    // Render inside the portal shell — output the page content + let
    // the existing portal CSS/JS handle the chrome (sidebar, top bar)
    $user         = wp_get_current_user();
    $affiliate_id = function_exists('affwp_get_affiliate_id') ? affwp_get_affiliate_id() : 0;
    $code         = $user ? strtoupper($user->user_login) : '';

    nvacs_render_standalone_shell('Request a Custom Lander', function() use ($sent, $errors, $subject_in, $message_in, $code) {
        ?>
        <h1 class="nv-rl-h1">Request a Custom <em>Lander</em></h1>

        <?php if ($sent) : ?>
            <div class="nv-rl-success">
                <div class="nv-rl-success-title">Thanks — we got your request</div>
                <p>The Natty Vision team will follow up at your account email within a few business days. Feel free to send another request anytime.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)) : ?>
            <div class="nv-rl-errors">
                <?php foreach ($errors as $err) : ?>
                    <p><?php echo esc_html($err); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="nv-rl-intro">
            <p>Want a custom landing page built around your audience or niche? Tell us what you're going for and our team will design one specifically for your traffic. Your coupon code <strong><?php echo esc_html($code); ?></strong> will be embedded automatically.</p>
        </div>

        <form method="post" class="nv-rl-form">
            <?php wp_nonce_field('nvacs_request_lander', 'nvacs_lander_nonce'); ?>

            <div class="nv-rl-field">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" value="<?php echo esc_attr($subject_in); ?>" placeholder="e.g. Fitness influencer audience" maxlength="120" required>
            </div>

            <div class="nv-rl-field">
                <label for="message">Message</label>
                <textarea name="message" id="message" rows="8" placeholder="Tell us about your audience, your platform (Instagram, YouTube, etc.), and what kind of lander would work for them. Any references or examples are great." required><?php echo esc_textarea($message_in); ?></textarea>
                <p class="nv-rl-help">The more detail, the faster we can turn it around.</p>
            </div>

            <button type="submit" class="nv-rl-submit">Send Request →</button>
        </form>
        <?php
    });
}

/* ============================================================
 * Standalone shell for the Request Lander page.
 *
 * Clean centered layout — no portal sidebar, no doubled logos.
 * Header has logo + "Back to Affiliate Dashboard" link only.
 * ============================================================ */
function nvacs_render_standalone_shell($title, $body_callback) {
    $dashboard = home_url('/affiliate-area/');
    ?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($title); ?> — <?php bloginfo('name'); ?></title>
    <meta name="robots" content="noindex,follow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
    <link href="https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap" rel="stylesheet">
    <style><?php echo nvacs_standalone_css(); ?></style>
</head>
<body class="nv-standalone-body">
    <header class="nv-standalone-header">
        <div class="nv-standalone-header-l">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="nv-standalone-logo" aria-label="Natty Vision">
                <?php echo nvacs_logo_svg(32); ?>
            </a>
            <a href="<?php echo esc_url($dashboard); ?>" class="nv-standalone-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                <span>Back to Affiliate Portal</span>
            </a>
        </div>
    </header>

    <main class="nv-standalone-main">
        <div class="nv-standalone-card">
            <?php $body_callback(); ?>
        </div>
    </main>

    <footer class="nv-standalone-footer">
        <p>©<?php echo date('Y'); ?> Natty Vision. For research purposes only.</p>
    </footer>
</body>
</html>
    <?php
}

function nvacs_standalone_css() {
    return <<<CSS
:root {
    --bg: #f2f0eb;
    --bg2: #e9e7e1;
    --bg-card: #eae8e2;
    --sage: #c5d4c0;
    --sage-s: #dce5d8;
    --sage-d: #a8bfa2;
    --dark: #1a1e1c;
    --green: #2d6a4f;
    --green-b: #52b788;
    --text: #1a1e1c;
    --t2: #4a4f4c;
    --t3: #7a7f7c;
    --ti: #f2f0eb;
    --brd: #d4d2cc;
    --brd2: #c4c2bc;
    --r: 16px;
    --rx: 8px;
    --rp: 100px;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
html { scroll-behavior: smooth; }
body.nv-standalone-body {
    font-family: 'Neue Montreal', -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, sans-serif;
    background: var(--bg);
    color: var(--text);
    -webkit-font-smoothing: antialiased;
    min-height: 100vh;
    line-height: 1.5;
}

.nv-standalone-header {
    display: flex;
    align-items: center;
    padding: 24px 48px;
    border-bottom: 1px solid var(--brd);
    background: var(--bg);
}
.nv-standalone-header-l {
    display: flex;
    align-items: center;
    gap: 28px;
}
.nv-standalone-logo {
    display: inline-flex;
    align-items: center;
}
.nv-standalone-logo svg { height: 32px; width: auto; display: block; }

.nv-standalone-back {
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--dark);
    text-decoration: none;
    padding: 11px 18px;
    background: var(--bg-card);
    border: 1px solid var(--brd2);
    border-radius: var(--rx);
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    display: inline-flex;
    align-items: center;
    gap: 8px;
    line-height: 1;
}
.nv-standalone-back svg {
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.nv-standalone-back:hover {
    background: var(--dark);
    color: var(--ti);
    border-color: var(--dark);
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(26,30,28,0.15);
}
.nv-standalone-back:hover svg {
    transform: translateX(-3px);
}

.nv-standalone-main {
    max-width: 800px;
    margin: 0 auto;
    padding: 60px 32px 80px;
}

.nv-standalone-card {
    background: var(--bg2);
    border: 1px solid var(--brd);
    border-radius: 20px;
    padding: 48px;
    box-shadow: 0 8px 30px rgba(26,30,28,0.04);
}

.nv-rl-h1 {
    font-family: 'Instrument Serif', serif;
    font-weight: 400;
    font-size: clamp(36px, 5vw, 52px);
    line-height: 1.05;
    letter-spacing: -0.02em;
    margin-bottom: 28px;
    color: var(--text);
}
.nv-rl-h1 em {
    font-style: italic;
    color: var(--green);
}

.nv-rl-intro {
    background: var(--bg-card);
    border: 1px solid var(--brd);
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 28px;
}
.nv-rl-intro p {
    font-size: 14px;
    color: var(--t2);
    line-height: 1.65;
}
.nv-rl-intro strong {
    font-family: 'DM Mono', monospace;
    font-size: 13px;
    color: var(--green);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.nv-rl-form {
    display: flex;
    flex-direction: column;
    gap: 22px;
}
.nv-rl-field {
    display: flex;
    flex-direction: column;
}
.nv-rl-field label {
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--t2);
    margin-bottom: 8px;
}
.nv-rl-field input,
.nv-rl-field textarea {
    background: var(--bg);
    border: 1px solid var(--brd);
    border-radius: var(--rx);
    padding: 12px 14px;
    font-family: 'DM Mono', monospace;
    font-size: 13px;
    color: var(--text);
    transition: all 0.3s;
    width: 100%;
}
.nv-rl-field textarea {
    font-family: 'Neue Montreal', sans-serif;
    resize: vertical;
    min-height: 180px;
    line-height: 1.6;
}
.nv-rl-field input:focus,
.nv-rl-field textarea:focus {
    outline: none;
    border-color: var(--green);
    box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
}
.nv-rl-help {
    font-size: 12px;
    color: var(--t3);
    margin-top: 8px;
}
.nv-rl-submit {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    background: var(--dark);
    color: var(--ti);
    padding: 14px 28px;
    border-radius: var(--rx);
    border: none;
    cursor: pointer;
    align-self: flex-start;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    margin-top: 6px;
}
.nv-rl-submit:hover {
    background: #2a302d;
    transform: translateY(-1px);
    box-shadow: 0 12px 30px rgba(26,30,28,0.15);
}
.nv-rl-success {
    background: var(--sage-s);
    border: 1px solid var(--sage);
    border-radius: 12px;
    padding: 22px 26px;
    margin-bottom: 28px;
}
.nv-rl-success-title {
    font-family: 'Instrument Serif', serif;
    font-size: 24px;
    color: var(--green);
    margin-bottom: 8px;
}
.nv-rl-success p {
    font-size: 14px;
    color: var(--t2);
    line-height: 1.6;
}
.nv-rl-errors {
    background: #faf0d6;
    border: 1px solid #e5cb74;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 22px;
}
.nv-rl-errors p {
    font-size: 13px;
    color: #6b5414;
    line-height: 1.5;
}
.nv-rl-errors p + p { margin-top: 6px; }

.nv-standalone-footer {
    text-align: center;
    padding: 32px 24px;
    font-family: 'DM Mono', monospace;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--t3);
    border-top: 1px solid var(--brd);
}

@media (max-width: 768px) {
    .nv-standalone-header {
        padding: 16px 20px;
    }
    .nv-standalone-header-l {
        flex-direction: column;
        align-items: flex-start;
        gap: 14px;
        width: 100%;
    }
    .nv-standalone-logo svg { height: 26px; }
    .nv-standalone-back {
        font-size: 11px;
        padding: 10px 14px;
        width: auto;
    }
    .nv-standalone-main { padding: 28px 16px 48px; }
    .nv-standalone-card { padding: 28px 22px; border-radius: 16px; }
    .nv-rl-h1 { font-size: 30px !important; margin-bottom: 22px; }
    .nv-rl-intro { padding: 16px 18px; margin-bottom: 20px; }
    .nv-rl-intro p { font-size: 13px; }
    .nv-rl-form { gap: 18px; }
    .nv-rl-field textarea { min-height: 140px; }
    .nv-rl-submit {
        width: 100%;
        justify-content: center;
        padding: 16px 20px;
    }
    .nv-standalone-footer { padding: 24px 16px; }
}
CSS;
}

/* ============================================================
 * ADMIN PAGE: View all Lander Requests
 *
 * Adds a submenu under "Tools" so you can see every submitted
 * request in case email delivery ever fails.
 *
 * Visit: WP Admin → Tools → Lander Requests
 * ============================================================ */
add_action('admin_menu', function() {
    add_management_page(
        'Lander Requests',
        'Lander Requests',
        'manage_options',
        'nvacs-lander-requests',
        'nvacs_render_lander_requests_admin'
    );
});

function nvacs_render_lander_requests_admin() {
    if (!current_user_can('manage_options')) return;

    // Allow clearing all
    if (isset($_POST['nvacs_clear_requests']) && check_admin_referer('nvacs_clear', 'nvacs_clear_nonce')) {
        delete_option('nvacs_lander_requests');
        echo '<div class="notice notice-success"><p>All lander requests cleared.</p></div>';
    }

    $requests = get_option('nvacs_lander_requests', []);
    ?>
    <div class="wrap">
        <h1>Lander Requests</h1>
        <p>All custom lander requests submitted by affiliates. The plugin also tries to email them to <code><?php echo esc_html(NVACS_ADMIN_EMAIL); ?></code>, but they're saved here as a backup in case email delivery fails.</p>

        <?php if (empty($requests)) : ?>
            <p><em>No requests yet.</em></p>
        <?php else : ?>
            <p><strong><?php echo count($requests); ?></strong> request<?php echo count($requests) === 1 ? '' : 's'; ?>.</p>

            <form method="post" style="margin-bottom:20px;">
                <?php wp_nonce_field('nvacs_clear', 'nvacs_clear_nonce'); ?>
                <button type="submit" name="nvacs_clear_requests" value="1" class="button" onclick="return confirm('Delete all lander requests? This cannot be undone.');">Clear All Requests</button>
            </form>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th style="width:140px;">Date</th>
                        <th style="width:140px;">Affiliate</th>
                        <th style="width:90px;">Code</th>
                        <th>Subject</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $r) :
                    $email = !empty($r['email']) ? esc_html($r['email']) : '';
                ?>
                    <tr>
                        <td><?php echo esc_html($r['time']); ?></td>
                        <td>
                            <strong><?php echo esc_html($r['name']); ?></strong><br>
                            <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo $email; ?></a>
                        </td>
                        <td><code><?php echo esc_html($r['code']); ?></code></td>
                        <td><?php echo esc_html($r['subject']); ?></td>
                        <td style="white-space:pre-wrap;max-width:500px;"><?php echo esc_html($r['message']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}

/* ============================================================
 * EMAIL DEBUG TOOL
 *
 * Visit /wp-admin/?nvacs_test_email=1 (as admin) to send a test
 * lander-request email and see the result inline. This bypasses
 * the form so we can isolate whether the issue is sending or form.
 * ============================================================ */
add_action('admin_init', function() {
    if (empty($_GET['nvacs_test_email'])) return;
    if (!current_user_can('manage_options')) return;

    $test_mode = (int) $_GET['nvacs_test_email'];

    $admin_email = get_option('admin_email');
    $site_name   = get_bloginfo('name');

    // Configure test based on mode
    if ($test_mode === 2) {
        $to = NVACS_ADMIN_EMAIL;
        $subject = '[Test 2 — no From header] ' . current_time('mysql');
        $body = "Test 2: sent with NO custom From header (WordPress default).";
        $headers = ['Content-Type: text/plain; charset=UTF-8'];
        $config_desc = 'No custom From — uses WP default';
    } elseif ($test_mode === 3) {
        $to = $admin_email;
        $subject = '[Test 3 — to admin email] ' . current_time('mysql');
        $body = "Test 3: sent TO your WordPress admin email instead of admin@nattyvision.com.";
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $site_name . ' <' . $admin_email . '>',
        ];
        $config_desc = "Sending TO {$admin_email} (admin email)";
    } else {
        $to = NVACS_ADMIN_EMAIL;
        $subject = '[Test 1 — current setup] ' . current_time('mysql');
        $body = "Test 1: same setup as the lander request form uses.";
        $headers = [
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $site_name . ' <' . $admin_email . '>',
        ];
        $config_desc = "Sending TO {$to}, From {$admin_email}";
    }

    $mail_error = '';
    $catch_error = function($wp_error) use (&$mail_error) {
        if (is_object($wp_error) && method_exists($wp_error, 'get_error_message')) {
            $mail_error = $wp_error->get_error_message();
        }
        if (is_object($wp_error) && method_exists($wp_error, 'get_error_data')) {
            $data = $wp_error->get_error_data();
            if (!empty($data)) {
                $mail_error .= "\n\nError data: " . print_r($data, true);
            }
        }
    };
    add_action('wp_mail_failed', $catch_error);

    $result = wp_mail($to, $subject, $body, $headers);

    remove_action('wp_mail_failed', $catch_error);

    echo '<div style="font-family:monospace;padding:30px;background:#f6f6f6;max-width:800px;margin:30px auto;border-radius:8px;">';
    echo '<h2>NVACS Email Test #' . $test_mode . '</h2>';
    echo '<p><strong>Config:</strong> ' . esc_html($config_desc) . '</p>';
    echo '<p><strong>To:</strong> ' . esc_html($to) . '</p>';
    echo '<p><strong>Subject:</strong> ' . esc_html($subject) . '</p>';
    echo '<p><strong>Headers:</strong></p><pre style="background:#fff;padding:10px;border-radius:6px;">' . esc_html(implode("\n", $headers)) . '</pre>';
    echo '<p><strong>wp_mail() returned:</strong> ' . ($result ? '<span style="color:green;">TRUE</span>' : '<span style="color:red;">FALSE</span>') . '</p>';
    if ($mail_error) {
        echo '<p><strong>Error caught:</strong></p>';
        echo '<pre style="background:#fff;padding:14px;border-radius:6px;border:1px solid #ddd;white-space:pre-wrap;">' . esc_html($mail_error) . '</pre>';
    } else {
        echo '<p><em>No error caught by wp_mail_failed (but check inbox to confirm delivery)</em></p>';
    }
    echo '<hr style="margin:24px 0;">';
    echo '<h3>Other tests:</h3>';
    if ($test_mode !== 1) echo '<p><a href="' . esc_url(admin_url('?nvacs_test_email=1')) . '">Test 1: Current setup (From admin email → admin@nattyvision.com)</a></p>';
    if ($test_mode !== 2) echo '<p><a href="' . esc_url(admin_url('?nvacs_test_email=2')) . '">Test 2: No custom From header</a></p>';
    if ($test_mode !== 3) echo '<p><a href="' . esc_url(admin_url('?nvacs_test_email=3')) . '">Test 3: Send to admin email instead</a></p>';
    echo '<hr style="margin:24px 0;">';
    echo '<h3>Preview branded email templates (no send):</h3>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=processing')) . '">Preview: Processing order</a></p>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=completed')) . '">Preview: Completed (shipped) order</a></p>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=shipped')) . '">Preview: Shipped / tracking email</a></p>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=refunded')) . '">Preview: Refunded order</a></p>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=onhold')) . '">Preview: On-hold order</a></p>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=invoice')) . '">Preview: Customer invoice</a></p>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=note')) . '">Preview: Customer note</a></p>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=reset')) . '">Preview: Password reset</a></p>';
    echo '<p><a href="' . esc_url(admin_url('?nvacs_preview_email=welcome')) . '">Preview: New account welcome</a></p>';
    echo '</div>';
    exit;
});

/* ============================================================
 * BRANDED EMAIL PREVIEW ENDPOINT
 *
 * Visit /wp-admin/?nvacs_preview_email=processing (etc.) as admin
 * to render each branded email template in the browser using the
 * most recent real order on the site (or fake data if no orders).
 * Renders inline — does NOT send anything.
 * ============================================================ */
add_action('admin_init', function() {
    if (empty($_GET['nvacs_preview_email'])) return;
    if (!current_user_can('manage_options')) return;

    $type = sanitize_key($_GET['nvacs_preview_email']);

    // 'shipped' is handled by its own dedicated preview handler — skip here
    if ($type === 'shipped') return;

    // Try to grab the most recent order to use as preview data
    $order = null;
    if (function_exists('wc_get_orders')) {
        $orders = wc_get_orders(['limit' => 1, 'orderby' => 'date', 'order' => 'DESC']);
        if (!empty($orders)) $order = $orders[0];
    }

    $first_name = $order ? ($order->get_billing_first_name() ?: 'Jane') : 'Jane';
    $order_number = $order ? $order->get_order_number() : '9999';

    switch ($type) {
        case 'processing':
            $content = ($order ? nvacs_email_meta_row($order) . nvacs_email_order_summary_card($order) : '')
                     . nvacs_email_button($order ? $order->get_view_order_url() : '#', 'View Order Details');
            $body = nvacs_build_email_html([
                'pill_text'    => 'Order Confirmed',
                'pill_color'   => 'sage',
                'headline'     => 'Thank %syou%s, ' . esc_html($first_name) . '.',
                'subheading'   => "We've received your order and we're getting it ready. We'll send another email once it ships.",
                'content_html' => $content,
            ]);
            break;

        case 'completed':
            $content = ($order ? nvacs_email_meta_row($order) . nvacs_email_order_summary_card($order) . nvacs_email_addresses_block($order) : '')
                     . nvacs_email_button($order ? $order->get_view_order_url() : '#', 'View Order');
            $body = nvacs_build_email_html([
                'pill_text'    => 'Shipped',
                'pill_color'   => 'sage',
                'headline'     => 'On %sits way%s, ' . esc_html($first_name) . '.',
                'subheading'   => 'Your order has shipped. Tracking details will arrive separately if applicable.',
                'content_html' => $content,
            ]);
            break;

        case 'refunded':
            $refund_block = nvacs_email_text_block(
                '<p style="margin:0;font-size:22px;font-weight:bold;">$50.00 refunded</p><p style="margin:8px 0 0;font-family:Georgia,serif;font-size:14px;color:#4a4f4c;">Reason: Sample refund reason</p>',
                'Refund'
            );
            $content = ($order ? nvacs_email_meta_row($order) : '')
                     . $refund_block
                     . ($order ? nvacs_email_order_summary_card($order) : '')
                     . nvacs_email_button($order ? $order->get_view_order_url() : '#', 'View Order');
            $body = nvacs_build_email_html([
                'pill_text'    => 'Refunded',
                'pill_color'   => 'amber',
                'headline'     => 'Your %srefund%s, ' . esc_html($first_name) . '.',
                'subheading'   => 'Your order has been refunded. The funds should appear in your account within a few business days.',
                'content_html' => $content,
            ]);
            break;

        case 'onhold':
            $content = ($order ? nvacs_email_meta_row($order) . nvacs_email_order_summary_card($order) : '')
                     . nvacs_email_button($order ? $order->get_checkout_payment_url() : '#', 'Complete Payment');
            $body = nvacs_build_email_html([
                'pill_text'    => 'On Hold',
                'pill_color'   => 'amber',
                'headline'     => 'Almost %sthere%s, ' . esc_html($first_name) . '.',
                'subheading'   => "We've received your order, but it's awaiting payment confirmation. Use the button below to complete it.",
                'content_html' => $content,
            ]);
            break;

        case 'invoice':
            $content = ($order ? nvacs_email_meta_row($order) . nvacs_email_order_summary_card($order) : '')
                     . nvacs_email_button($order ? $order->get_checkout_payment_url() : '#', 'Pay for Order');
            $body = nvacs_build_email_html([
                'pill_text'    => 'Awaiting Payment',
                'pill_color'   => 'amber',
                'headline'     => 'Your %sorder%s, ' . esc_html($first_name) . '.',
                'subheading'   => 'Your order is ready to be paid. Use the button below to complete payment.',
                'content_html' => $content,
            ]);
            break;

        case 'note':
            $content = ($order ? nvacs_email_meta_row($order) : '')
                     . nvacs_email_text_block('<p style="margin:0;">Hi! This is a sample note from Natty Vision. You can put any update about the order here — shipping delay, follow-up, anything customer-relevant.</p>', 'Note from Natty Vision')
                     . nvacs_email_button($order ? $order->get_view_order_url() : '#', 'View Order');
            $body = nvacs_build_email_html([
                'pill_text'    => 'Order Update',
                'pill_color'   => 'sage',
                'headline'     => 'A %snote%s for you, ' . esc_html($first_name) . '.',
                'subheading'   => 'We added a note to your order. Read below.',
                'content_html' => $content,
            ]);
            break;

        case 'reset':
            $content = nvacs_email_text_block(
                '<p style="margin:0;">Someone requested a password reset for your Natty Vision account. If that was you, use the button below to set a new password. If it wasn\'t, you can safely ignore this email.</p>',
                'Password Reset'
            )
            . nvacs_email_button('#', 'Reset Password');
            $body = nvacs_build_email_html([
                'pill_text'    => 'Password Reset',
                'pill_color'   => 'sage',
                'headline'     => 'Reset your %spassword%s, ' . esc_html($first_name) . '.',
                'subheading'   => 'Click the button below to choose a new password. This link expires shortly for your security.',
                'content_html' => $content,
            ]);
            break;

        case 'welcome':
            $creds = '<p style="margin:0;">Username: <strong>' . esc_html($first_name) . '</strong></p>';
            $content = nvacs_email_text_block($creds, 'Your Account')
                     . nvacs_email_button(home_url('/my-account/'), 'Sign In');
            $body = nvacs_build_email_html([
                'pill_text'    => 'Welcome',
                'pill_color'   => 'sage',
                'headline'     => 'Welcome, %s' . esc_html($first_name) . '%s.',
                'subheading'   => "Your Natty Vision account is ready. Sign in anytime to view your orders or manage your details.",
                'content_html' => $content,
            ]);
            break;

        default:
            wp_die('Unknown preview type. Valid: processing, completed, refunded, onhold, invoice, note, reset, welcome.');
    }

    // Render the email HTML directly to the browser
    header('Content-Type: text/html; charset=UTF-8');
    echo $body;
    exit;
});

/* ============================================================
 * UNIFIED BRANDED EMAIL SYSTEM
 * ============================================================
 *
 * Architecture:
 *   nvacs_build_email_html($args)
 *       → wraps every email in the Natty Vision shell
 *         (logo, status pill, headline, subheading, content, footer)
 *
 *   Helper builders for reusable blocks:
 *       nvacs_email_order_summary_card($order)
 *       nvacs_email_button($url, $label)
 *       nvacs_email_addresses_block($order)
 *       nvacs_email_text_block($html)
 *       nvacs_email_meta_row($order)
 *       nvacs_email_keyvalue_card($title, $rows)
 *
 *   Each WooCommerce email type:
 *       - Gets disabled (default HTML suppressed)
 *       - Replaced with a custom sender that builds via the shell
 *
 * What we override (only what's already enabled in WC):
 *   ✓ Customer Processing Order  (was already done, refactored here)
 *   ✓ Customer Completed Order
 *   ✓ Customer Refunded Order
 *   ✓ Customer On-Hold Order
 *   ✓ Customer Invoice / Order Pay
 *   ✓ Customer Note
 *   ✓ Customer Reset Password
 *   ✓ Customer New Account
 *
 * Admin emails (New Order, Cancelled, Failed) stay on WC defaults.
 *
 * IMPORTANT: We never *enable* an email that's disabled in WC.
 * The override only fires if WC would have sent the default,
 * so toggling the setting in WP Admin → WooCommerce → Settings →
 * Emails still controls whether the email goes out.
 * ============================================================ */

/* ------------------------------------------------------------
 * COLOR CONSTANTS — single source of truth for all email styling
 * ------------------------------------------------------------ */
function nvacs_email_colors() {
    return [
        'bg'       => '#f2f0eb',  // page background
        'bg2'      => '#e9e7e1',  // card background
        'sage'     => '#c5d4c0',  // sage border
        'sage_s'   => '#dce5d8',  // sage pill bg
        'amber'    => '#e5cb74',  // amber border (on-hold)
        'amber_s'  => '#faf0d6',  // amber pill bg
        'amber_t'  => '#6b5414',  // amber pill text
        'red'      => '#f0a8a8',  // red border (refunded/failed)
        'red_s'    => '#fde8e8',  // red pill bg
        'red_t'    => '#872a2a',  // red pill text
        'dark'     => '#1a1e1c',  // text + button bg
        'green'    => '#2d6a4f',  // em accent + sage pill text
        'text'     => '#1a1e1c',
        'text2'    => '#4a4f4c',
        'text3'    => '#7a7f7c',
        'ti'       => '#f2f0eb',  // inverse text (on dark btn)
        'brd'      => '#d4d2cc',  // standard border
    ];
}

/* ------------------------------------------------------------
 * CORE: nvacs_build_email_html()
 * Builds the full HTML email shell. All emails route through here.
 *
 * @param array $args {
 *   @type string $pill_text    Status pill text (e.g. "Order Confirmed")
 *   @type string $pill_color   'sage' | 'amber' | 'red'  (default: sage)
 *   @type string $headline     Headline with %s for italic-green emphasis
 *                              e.g. "Thank %syou%s, Jane."
 *                              If %s is missing, headline renders plain.
 *   @type string $subheading   One-sentence intro under the headline
 *   @type string $content_html Optional middle blocks (order card, button, etc.)
 *   @type string $preheader    Hidden inbox preview text (default: subheading)
 *   @type string $footer_note  Extra text above the copyright (optional)
 * }
 * ------------------------------------------------------------ */
function nvacs_build_email_html($args) {
    $c = nvacs_email_colors();

    $defaults = [
        'pill_text'    => '',
        'pill_color'   => 'sage',
        'headline'     => '',
        'subheading'   => '',
        'content_html' => '',
        'preheader'    => '',
        'footer_note'  => 'Questions? Just reply to this email — we read every one.',
    ];
    $a = array_merge($defaults, $args);
    if ($a['preheader'] === '') $a['preheader'] = $a['subheading'];

    // Resolve pill colors
    switch ($a['pill_color']) {
        case 'amber':
            $pill_bg = $c['amber_s']; $pill_brd = $c['amber']; $pill_text_color = $c['amber_t'];
            break;
        case 'red':
            $pill_bg = $c['red_s']; $pill_brd = $c['red']; $pill_text_color = $c['red_t'];
            break;
        case 'sage':
        default:
            $pill_bg = $c['sage_s']; $pill_brd = $c['sage']; $pill_text_color = $c['green'];
            break;
    }

    // Resolve headline — replace %s pair with italic-green wrap
    $headline_html = $a['headline'];
    if (substr_count($headline_html, '%s') >= 2) {
        $headline_html = sprintf(
            $headline_html,
            '<em style="font-style:italic;color:' . $c['green'] . ';">',
            '</em>'
        );
    }

    $logo_svg = nvacs_logo_svg(36, $c['dark']);

    ob_start();
    ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<meta name="x-apple-disable-message-reformatting">
<title>Natty Vision</title>
<!--[if mso]>
<noscript>
<xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml>
</noscript>
<![endif]-->
</head>
<body style="margin:0;padding:0;background-color:<?php echo $c['bg']; ?>;-webkit-font-smoothing:antialiased;">

<!-- Hidden preheader (inbox preview) -->
<div style="display:none;font-size:1px;color:<?php echo $c['bg']; ?>;line-height:1px;max-height:0;max-width:0;opacity:0;overflow:hidden;">
<?php echo esc_html($a['preheader']); ?>
</div>

<!-- Wrapper -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:<?php echo $c['bg']; ?>;">
<tr>
<td align="center" style="padding:40px 16px;">

<!-- Container -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="600" style="max-width:600px;width:100%;background-color:<?php echo $c['bg']; ?>;">

<!-- Logo header -->
<tr>
<td align="center" style="padding:0 0 48px;">
<?php echo $logo_svg; ?>
</td>
</tr>

<?php if ($a['pill_text']) : ?>
<!-- Status pill -->
<tr>
<td align="center" style="padding:0 0 24px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="background-color:<?php echo $pill_bg; ?>;border:1px solid <?php echo $pill_brd; ?>;border-radius:100px;padding:8px 18px;font-family:'Courier New',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $pill_text_color; ?>;font-weight:bold;">
● <?php echo esc_html($a['pill_text']); ?>
</td>
</tr>
</table>
</td>
</tr>
<?php endif; ?>

<?php if ($a['headline']) : ?>
<!-- Headline -->
<tr>
<td align="center" style="padding:0 24px 16px;">
<h1 style="margin:0;font-family:Georgia,'Times New Roman',serif;font-weight:normal;font-size:52px;line-height:1.05;color:<?php echo $c['text']; ?>;letter-spacing:-0.02em;">
<?php echo $headline_html; ?>
</h1>
</td>
</tr>
<?php endif; ?>

<?php if ($a['subheading']) : ?>
<!-- Subheading -->
<tr>
<td align="center" style="padding:0 32px 40px;">
<p style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:16px;line-height:1.6;color:<?php echo $c['text2']; ?>;">
<?php echo $a['subheading']; ?>
</p>
</td>
</tr>
<?php endif; ?>

<?php if ($a['content_html']) : ?>
<!-- Custom content blocks -->
<?php echo $a['content_html']; ?>
<?php endif; ?>

<!-- Footer -->
<tr>
<td align="center" style="padding:32px 24px 0;border-top:1px solid <?php echo $c['brd']; ?>;">
<?php if ($a['footer_note']) : ?>
<p style="margin:0 0 8px;font-family:Georgia,'Times New Roman',serif;font-size:14px;color:<?php echo $c['text2']; ?>;">
<?php echo $a['footer_note']; ?>
</p>
<?php endif; ?>
<p style="margin:16px 0 0;font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;">
© <?php echo date('Y'); ?> Natty Vision · For research purposes only
</p>
</td>
</tr>

</table>
<!-- /Container -->

</td>
</tr>
</table>
<!-- /Wrapper -->

</body>
</html>
    <?php
    return ob_get_clean();
}

/* ------------------------------------------------------------
 * HELPER: Order meta row (Order #, Date)
 * ------------------------------------------------------------ */
function nvacs_email_meta_row($order) {
    $c = nvacs_email_colors();
    $order_number = $order->get_order_number();
    $order_date   = $order->get_date_created()->date_i18n('F j, Y');
    ob_start();
    ?>
<tr>
<td style="padding:0 24px 16px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td style="font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;">Order</td>
<td style="font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;text-align:right;">Date</td>
</tr>
<tr>
<td style="padding:4px 0 0;font-family:'Courier New',Courier,monospace;font-size:14px;color:<?php echo $c['text']; ?>;font-weight:bold;">#<?php echo esc_html($order_number); ?></td>
<td style="padding:4px 0 0;font-family:Georgia,'Times New Roman',serif;font-size:14px;color:<?php echo $c['text']; ?>;text-align:right;"><?php echo esc_html($order_date); ?></td>
</tr>
</table>
</td>
</tr>
    <?php
    return ob_get_clean();
}

/* ------------------------------------------------------------
 * HELPER: Order summary card (items + totals)
 * ------------------------------------------------------------ */
function nvacs_email_order_summary_card($order, $title = 'Your %sorder%s') {
    $c = nvacs_email_colors();

    // Build line items
    $items_html = '';
    foreach ($order->get_items() as $item) {
        $product_name = $item->get_name();
        $quantity     = $item->get_quantity();
        $line_total   = wc_price($item->get_total(), ['currency' => $order->get_currency()]);

        $items_html .= '<tr>'
            . '<td style="padding:18px 0;border-bottom:1px solid ' . $c['brd'] . ';font-family:Georgia,\'Times New Roman\',serif;font-size:15px;color:' . $c['text'] . ';vertical-align:top;">'
            . esc_html($product_name)
            . '</td>'
            . '<td style="padding:18px 0;border-bottom:1px solid ' . $c['brd'] . ';font-family:\'Courier New\',Courier,monospace;font-size:13px;color:' . $c['text2'] . ';text-align:center;vertical-align:top;white-space:nowrap;">'
            . '× ' . intval($quantity)
            . '</td>'
            . '<td style="padding:18px 0;border-bottom:1px solid ' . $c['brd'] . ';font-family:Georgia,\'Times New Roman\',serif;font-size:15px;color:' . $c['text'] . ';text-align:right;vertical-align:top;white-space:nowrap;">'
            . $line_total
            . '</td>'
            . '</tr>';
    }

    // Totals rows
    $subtotal = wc_price($order->get_subtotal(), ['currency' => $order->get_currency()]);
    $shipping = wc_price($order->get_shipping_total(), ['currency' => $order->get_currency()]);
    $tax      = wc_price($order->get_total_tax(), ['currency' => $order->get_currency()]);
    $total    = wc_price($order->get_total(), ['currency' => $order->get_currency()]);

    $totals_html = '<tr>'
        . '<td style="padding:8px 0;font-family:\'Courier New\',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:' . $c['text3'] . ';" colspan="2">Subtotal</td>'
        . '<td style="padding:8px 0;font-family:Georgia,\'Times New Roman\',serif;font-size:15px;color:' . $c['text'] . ';text-align:right;white-space:nowrap;">' . $subtotal . '</td>'
        . '</tr>';

    if ($order->get_shipping_total() > 0) {
        $totals_html .= '<tr>'
            . '<td style="padding:8px 0;font-family:\'Courier New\',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:' . $c['text3'] . ';" colspan="2">Shipping</td>'
            . '<td style="padding:8px 0;font-family:Georgia,\'Times New Roman\',serif;font-size:15px;color:' . $c['text'] . ';text-align:right;white-space:nowrap;">' . $shipping . '</td>'
            . '</tr>';
    }
    if ($order->get_total_tax() > 0) {
        $totals_html .= '<tr>'
            . '<td style="padding:8px 0;font-family:\'Courier New\',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:' . $c['text3'] . ';" colspan="2">Tax</td>'
            . '<td style="padding:8px 0;font-family:Georgia,\'Times New Roman\',serif;font-size:15px;color:' . $c['text'] . ';text-align:right;white-space:nowrap;">' . $tax . '</td>'
            . '</tr>';
    }
    $totals_html .= '<tr>'
        . '<td style="padding:18px 0 8px;border-top:2px solid ' . $c['dark'] . ';font-family:\'Courier New\',Courier,monospace;font-size:12px;text-transform:uppercase;letter-spacing:0.1em;color:' . $c['text'] . ';font-weight:bold;" colspan="2">Total</td>'
        . '<td style="padding:18px 0 8px;border-top:2px solid ' . $c['dark'] . ';font-family:Georgia,\'Times New Roman\',serif;font-size:22px;color:' . $c['text'] . ';text-align:right;white-space:nowrap;font-weight:bold;">' . $total . '</td>'
        . '</tr>';

    // Resolve title with %s pair → italic green
    $title_html = $title;
    if (substr_count($title_html, '%s') >= 2) {
        $title_html = sprintf($title_html,
            '<em style="font-style:italic;color:' . $c['green'] . ';">',
            '</em>'
        );
    }

    ob_start();
    ?>
<tr>
<td style="padding:24px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:<?php echo $c['bg2']; ?>;border:1px solid <?php echo $c['brd']; ?>;border-radius:16px;">
<tr>
<td style="padding:32px;">
<h2 style="margin:0 0 24px;font-family:Georgia,'Times New Roman',serif;font-weight:normal;font-size:28px;line-height:1.1;color:<?php echo $c['text']; ?>;"><?php echo $title_html; ?></h2>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
<thead>
<tr>
<th align="left" style="padding:0 0 12px;font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;font-weight:normal;border-bottom:1px solid <?php echo $c['brd']; ?>;">Product</th>
<th align="center" style="padding:0 0 12px;font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;font-weight:normal;border-bottom:1px solid <?php echo $c['brd']; ?>;">Qty</th>
<th align="right" style="padding:0 0 12px;font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;font-weight:normal;border-bottom:1px solid <?php echo $c['brd']; ?>;">Price</th>
</tr>
</thead>
<tbody>
<?php echo $items_html; ?>
</tbody>
</table>
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin-top:12px;">
<?php echo $totals_html; ?>
</table>
</td>
</tr>
</table>
</td>
</tr>
    <?php
    return ob_get_clean();
}

/* ------------------------------------------------------------
 * HELPER: Addresses block (billing + shipping side by side)
 * ------------------------------------------------------------ */
function nvacs_email_addresses_block($order) {
    $c = nvacs_email_colors();
    $billing  = $order->get_formatted_billing_address();
    $shipping = $order->get_formatted_shipping_address();
    if (!$shipping) $shipping = $billing;

    ob_start();
    ?>
<tr>
<td style="padding:0 24px 24px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td width="50%" valign="top" style="padding-right:12px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:<?php echo $c['bg2']; ?>;border:1px solid <?php echo $c['brd']; ?>;border-radius:12px;">
<tr>
<td style="padding:20px;">
<p style="margin:0 0 10px;font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;">Billing</p>
<p style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:14px;line-height:1.5;color:<?php echo $c['text']; ?>;"><?php echo wp_kses_post($billing ?: '—'); ?></p>
</td>
</tr>
</table>
</td>
<td width="50%" valign="top" style="padding-left:12px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:<?php echo $c['bg2']; ?>;border:1px solid <?php echo $c['brd']; ?>;border-radius:12px;">
<tr>
<td style="padding:20px;">
<p style="margin:0 0 10px;font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;">Shipping</p>
<p style="margin:0;font-family:Georgia,'Times New Roman',serif;font-size:14px;line-height:1.5;color:<?php echo $c['text']; ?>;"><?php echo wp_kses_post($shipping ?: '—'); ?></p>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
    <?php
    return ob_get_clean();
}

/* ------------------------------------------------------------
 * HELPER: Dark pill button row
 * ------------------------------------------------------------ */
function nvacs_email_button($url, $label) {
    $c = nvacs_email_colors();
    ob_start();
    ?>
<tr>
<td align="center" style="padding:8px 24px 48px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0">
<tr>
<td style="background-color:<?php echo $c['dark']; ?>;border-radius:8px;">
<a href="<?php echo esc_url($url); ?>" style="display:inline-block;padding:14px 28px;font-family:'Courier New',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['ti']; ?>;text-decoration:none;font-weight:bold;">
<?php echo esc_html($label); ?> →
</a>
</td>
</tr>
</table>
</td>
</tr>
    <?php
    return ob_get_clean();
}

/* ------------------------------------------------------------
 * HELPER: Generic prose block (for Customer Note, Refunded reason, etc.)
 * ------------------------------------------------------------ */
function nvacs_email_text_block($html, $label = '') {
    $c = nvacs_email_colors();
    ob_start();
    ?>
<tr>
<td style="padding:0 24px 24px;">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color:<?php echo $c['bg2']; ?>;border:1px solid <?php echo $c['brd']; ?>;border-radius:12px;border-left:4px solid <?php echo $c['sage']; ?>;">
<tr>
<td style="padding:24px 28px;">
<?php if ($label) : ?>
<p style="margin:0 0 10px;font-family:'Courier New',Courier,monospace;font-size:10px;text-transform:uppercase;letter-spacing:0.12em;color:<?php echo $c['text3']; ?>;"><?php echo esc_html($label); ?></p>
<?php endif; ?>
<div style="font-family:Georgia,'Times New Roman',serif;font-size:15px;line-height:1.6;color:<?php echo $c['text']; ?>;">
<?php echo wp_kses_post($html); ?>
</div>
</td>
</tr>
</table>
</td>
</tr>
    <?php
    return ob_get_clean();
}

/* ============================================================
 * EMAIL TYPE 1: CUSTOMER PROCESSING ORDER
 *
 * Force-fires on every transition into 'processing' (some gateways
 * route pending → on-hold → processing, missing WC's default trigger).
 * ============================================================ */
add_filter('woocommerce_email_enabled_customer_processing_order', '__return_false', 99);

add_action('woocommerce_order_status_processing', 'nvacs_send_branded_processing_email', 10, 2);
function nvacs_send_branded_processing_email($order_id, $order) {
    if (!$order_id || !is_a($order, 'WC_Order')) return;
    if ($order->get_meta('_nvacs_processing_email_sent') === 'yes') return;

    $to = $order->get_billing_email();
    if (!$to) return;

    $first_name = $order->get_billing_first_name() ?: 'there';

    $content = nvacs_email_meta_row($order)
             . nvacs_email_order_summary_card($order)
             . nvacs_email_button($order->get_view_order_url(), 'View Order Details');

    $body = nvacs_build_email_html([
        'pill_text'    => 'Order Confirmed',
        'pill_color'   => 'sage',
        'headline'     => 'Thank %syou%s, ' . esc_html($first_name) . '.',
        'subheading'   => "We've received your order and we're getting it ready. We'll send another email once it ships.",
        'content_html' => $content,
        'preheader'    => 'Order #' . $order->get_order_number() . ' confirmed',
    ]);

    $subject = sprintf('Your Natty Vision order #%s is confirmed', $order->get_order_number());
    $sent = nvacs_send_email($to, $subject, $body);

    if ($sent) {
        $order->update_meta_data('_nvacs_processing_email_sent', 'yes');
        $order->save();
    }
}

/* ============================================================
 * EMAIL TYPE 2: CUSTOMER COMPLETED ORDER (shipped)
 *
 * Hooks into multiple WC actions to ensure delivery regardless
 * of how the order transition happens:
 *   - woocommerce_order_status_completed              (bare, fires on any → completed)
 *   - woocommerce_order_status_completed_notification (canonical WC email trigger)
 *   - woocommerce_order_status_changed                (catch-all status change)
 *
 * The _nvacs_completed_email_sent meta guard ensures it only
 * actually sends once per order.
 * ============================================================ */
add_filter('woocommerce_email_enabled_customer_completed_order', '__return_false', 99);

add_action('woocommerce_order_status_completed',                 'nvacs_send_branded_completed_email', 10, 2);
add_action('woocommerce_order_status_completed_notification',    'nvacs_send_branded_completed_email', 10, 2);
add_action('woocommerce_order_status_changed', 'nvacs_completed_email_via_status_changed', 10, 4);

function nvacs_completed_email_via_status_changed($order_id, $from, $to, $order) {
    if ($to !== 'completed') return;
    nvacs_send_branded_completed_email($order_id, $order);
}

function nvacs_send_branded_completed_email($order_id, $order = null) {
    if (!$order_id) return;
    if (!is_a($order, 'WC_Order')) $order = wc_get_order($order_id);
    if (!$order) {
        error_log('[NVACS Completed] No order found for ID ' . $order_id);
        return;
    }
    if ($order->get_meta('_nvacs_completed_email_sent') === 'yes') {
        // already sent — skip silently (this is normal for multi-hook setups)
        return;
    }
    if (!nvacs_wc_email_is_enabled('customer_completed_order')) {
        error_log('[NVACS Completed] WC Completed email is DISABLED in settings → skipping order #' . $order_id);
        return;
    }

    $to = $order->get_billing_email();
    if (!$to) {
        error_log('[NVACS Completed] No billing email on order #' . $order_id);
        return;
    }

    $first_name = $order->get_billing_first_name() ?: 'there';

    $content = nvacs_email_meta_row($order)
             . nvacs_email_order_summary_card($order)
             . nvacs_email_addresses_block($order)
             . nvacs_email_button($order->get_view_order_url(), 'View Order');

    $body = nvacs_build_email_html([
        'pill_text'    => 'Shipped',
        'pill_color'   => 'sage',
        'headline'     => 'On %sits way%s, ' . esc_html($first_name) . '.',
        'subheading'   => 'Your order has shipped. Tracking details will arrive separately if applicable.',
        'content_html' => $content,
        'preheader'    => 'Order #' . $order->get_order_number() . ' has shipped',
    ]);

    $subject = sprintf('Your Natty Vision order #%s has shipped', $order->get_order_number());
    $sent = nvacs_send_email($to, $subject, $body);

    error_log('[NVACS Completed] Send to ' . $to . ' for order #' . $order_id . ' returned: ' . ($sent ? 'TRUE' : 'FALSE'));

    if ($sent) {
        $order->update_meta_data('_nvacs_completed_email_sent', 'yes');
        $order->save();
    }
}

/* ============================================================
 * EMAIL TYPE 3: CUSTOMER REFUNDED ORDER (full or partial)
 * ============================================================ */
add_filter('woocommerce_email_enabled_customer_refunded_order', '__return_false', 99);

add_action('woocommerce_order_partially_refunded', 'nvacs_send_branded_refunded_email_partial', 10, 2);
add_action('woocommerce_order_fully_refunded',     'nvacs_send_branded_refunded_email_full',    10, 2);

function nvacs_send_branded_refunded_email_partial($order_id, $refund_id) {
    nvacs_send_branded_refunded_email($order_id, $refund_id, false);
}
function nvacs_send_branded_refunded_email_full($order_id, $refund_id) {
    nvacs_send_branded_refunded_email($order_id, $refund_id, true);
}
function nvacs_send_branded_refunded_email($order_id, $refund_id, $is_full) {
    $order = wc_get_order($order_id);
    if (!$order) return;
    if (!nvacs_wc_email_is_enabled('customer_refunded_order')) return;

    $to = $order->get_billing_email();
    if (!$to) return;

    $first_name = $order->get_billing_first_name() ?: 'there';

    $refund = $refund_id ? wc_get_order($refund_id) : null;
    $refund_amount = $refund ? wc_price($refund->get_amount(), ['currency' => $order->get_currency()]) : '';
    $refund_reason = $refund ? $refund->get_reason() : '';

    $intro = $is_full
        ? "Your order has been fully refunded. The funds should appear in your account within a few business days, depending on your bank."
        : "A partial refund has been issued on your order. Details below.";

    $refund_block = '';
    if ($refund_amount) {
        $reason_html = $refund_reason ? '<p style="margin:8px 0 0;font-family:Georgia,serif;font-size:14px;color:#4a4f4c;">Reason: ' . esc_html($refund_reason) . '</p>' : '';
        $refund_block = nvacs_email_text_block(
            '<p style="margin:0;font-size:22px;font-weight:bold;">' . $refund_amount . ' refunded</p>' . $reason_html,
            'Refund'
        );
    }

    $content = nvacs_email_meta_row($order)
             . $refund_block
             . nvacs_email_order_summary_card($order)
             . nvacs_email_button($order->get_view_order_url(), 'View Order');

    $pill_text = $is_full ? 'Refunded' : 'Partially Refunded';

    $body = nvacs_build_email_html([
        'pill_text'    => $pill_text,
        'pill_color'   => 'amber',
        'headline'     => 'Your %srefund%s, ' . esc_html($first_name) . '.',
        'subheading'   => $intro,
        'content_html' => $content,
        'preheader'    => $pill_text . ' — Order #' . $order->get_order_number(),
    ]);

    $subject = sprintf('Your Natty Vision order #%s has been refunded', $order->get_order_number());
    nvacs_send_email($to, $subject, $body);
}

/* ============================================================
 * EMAIL TYPE 4: CUSTOMER ON-HOLD ORDER
 * ============================================================ */
add_filter('woocommerce_email_enabled_customer_on_hold_order', '__return_false', 99);

add_action('woocommerce_order_status_pending_to_on-hold_notification',    'nvacs_send_branded_onhold_email', 10, 2);
add_action('woocommerce_order_status_failed_to_on-hold_notification',     'nvacs_send_branded_onhold_email', 10, 2);
add_action('woocommerce_order_status_cancelled_to_on-hold_notification',  'nvacs_send_branded_onhold_email', 10, 2);

function nvacs_send_branded_onhold_email($order_id, $order) {
    if (!$order_id) return;
    if (!is_a($order, 'WC_Order')) $order = wc_get_order($order_id);
    if (!$order) return;
    if ($order->get_meta('_nvacs_onhold_email_sent') === 'yes') return;
    if (!nvacs_wc_email_is_enabled('customer_on_hold_order')) return;

    $to = $order->get_billing_email();
    if (!$to) return;

    $first_name = $order->get_billing_first_name() ?: 'there';
    $pay_url    = $order->get_checkout_payment_url();

    $content = nvacs_email_meta_row($order)
             . nvacs_email_order_summary_card($order)
             . nvacs_email_button($pay_url, 'Complete Payment');

    $body = nvacs_build_email_html([
        'pill_text'    => 'On Hold',
        'pill_color'   => 'amber',
        'headline'     => 'Almost %sthere%s, ' . esc_html($first_name) . '.',
        'subheading'   => "We've received your order, but it's awaiting payment confirmation. Use the button below to complete it.",
        'content_html' => $content,
        'preheader'    => 'Order #' . $order->get_order_number() . ' awaiting payment',
    ]);

    $subject = sprintf('Your Natty Vision order #%s is on hold', $order->get_order_number());
    $sent = nvacs_send_email($to, $subject, $body);

    if ($sent) {
        $order->update_meta_data('_nvacs_onhold_email_sent', 'yes');
        $order->save();
    }
}

/* ============================================================
 * EMAIL TYPE 5: CUSTOMER INVOICE / ORDER PAY
 * Manual send from WC admin → "Email invoice / order details to customer"
 * ============================================================ */
add_filter('woocommerce_email_enabled_customer_invoice', '__return_false', 99);

add_action('woocommerce_before_resend_order_emails', 'nvacs_intercept_invoice_email', 10, 2);
function nvacs_intercept_invoice_email($order, $email_type = '') {
    if ($email_type !== 'customer_invoice') return;
    if (!is_a($order, 'WC_Order')) return;
    if (!nvacs_wc_email_is_enabled('customer_invoice')) return;

    $to = $order->get_billing_email();
    if (!$to) return;

    $first_name = $order->get_billing_first_name() ?: 'there';
    $is_paid    = $order->is_paid();
    $pay_url    = $order->get_checkout_payment_url();

    $content = nvacs_email_meta_row($order)
             . nvacs_email_order_summary_card($order);

    if (!$is_paid) {
        $content .= nvacs_email_button($pay_url, 'Pay for Order');
    } else {
        $content .= nvacs_email_button($order->get_view_order_url(), 'View Order');
    }

    $body = nvacs_build_email_html([
        'pill_text'    => $is_paid ? 'Order Details' : 'Awaiting Payment',
        'pill_color'   => $is_paid ? 'sage' : 'amber',
        'headline'     => $is_paid
            ? 'Your %sinvoice%s, ' . esc_html($first_name) . '.'
            : 'Your %sorder%s, ' . esc_html($first_name) . '.',
        'subheading'   => $is_paid
            ? 'Your order details are below for your records.'
            : "Your order is ready to be paid. Use the button below to complete payment.",
        'content_html' => $content,
        'preheader'    => 'Order #' . $order->get_order_number(),
    ]);

    $subject = $is_paid
        ? sprintf('Invoice for your Natty Vision order #%s', $order->get_order_number())
        : sprintf('Pay for your Natty Vision order #%s', $order->get_order_number());

    nvacs_send_email($to, $subject, $body);
}

/* ============================================================
 * EMAIL TYPE 6: CUSTOMER NOTE
 * Fires when admin adds a customer-visible note to an order.
 * ============================================================ */
add_filter('woocommerce_email_enabled_customer_note', '__return_false', 99);

add_action('woocommerce_new_customer_note', 'nvacs_send_branded_customer_note_email', 10, 1);
function nvacs_send_branded_customer_note_email($args) {
    if (empty($args['order_id']) || empty($args['customer_note'])) return;
    if (!nvacs_wc_email_is_enabled('customer_note')) return;

    $order = wc_get_order($args['order_id']);
    if (!$order) return;

    $to = $order->get_billing_email();
    if (!$to) return;

    $first_name = $order->get_billing_first_name() ?: 'there';
    $note_html  = wpautop(wptexturize($args['customer_note']));

    $content = nvacs_email_meta_row($order)
             . nvacs_email_text_block($note_html, 'Note from Natty Vision')
             . nvacs_email_button($order->get_view_order_url(), 'View Order');

    $body = nvacs_build_email_html([
        'pill_text'    => 'Order Update',
        'pill_color'   => 'sage',
        'headline'     => 'A %snote%s for you, ' . esc_html($first_name) . '.',
        'subheading'   => 'We added a note to your order. Read below.',
        'content_html' => $content,
        'preheader'    => 'New note on order #' . $order->get_order_number(),
    ]);

    $subject = sprintf('Update on your Natty Vision order #%s', $order->get_order_number());
    nvacs_send_email($to, $subject, $body);
}

/* ============================================================
 * EMAIL TYPE 7: CUSTOMER RESET PASSWORD
 * Replaces both WC and WP password reset emails with our shell.
 * ============================================================ */
add_filter('woocommerce_email_enabled_customer_reset_password', '__return_false', 99);

// Hook the WC password reset key event
add_action('woocommerce_reset_password_notification', 'nvacs_send_branded_reset_password_email', 10, 2);
function nvacs_send_branded_reset_password_email($user_login, $reset_key) {
    if (!nvacs_wc_email_is_enabled('customer_reset_password')) return;

    $user = get_user_by('login', $user_login);
    if (!$user) return;

    $to = $user->user_email;
    if (!$to) return;

    $first_name = $user->first_name ?: ($user->display_name ?: 'there');
    $reset_url = add_query_arg([
        'key'   => $reset_key,
        'id'    => $user->ID,
        'login' => rawurlencode($user_login),
    ], wc_get_endpoint_url('lost-password', '', wc_get_page_permalink('myaccount')));

    $content = nvacs_email_text_block(
        '<p style="margin:0;">Someone requested a password reset for your Natty Vision account. If that was you, use the button below to set a new password. If it wasn\'t, you can safely ignore this email.</p>',
        'Password Reset'
    )
    . nvacs_email_button($reset_url, 'Reset Password');

    $body = nvacs_build_email_html([
        'pill_text'    => 'Password Reset',
        'pill_color'   => 'sage',
        'headline'     => 'Reset your %spassword%s, ' . esc_html($first_name) . '.',
        'subheading'   => 'Click the button below to choose a new password. This link expires shortly for your security.',
        'content_html' => $content,
        'preheader'    => 'Reset your Natty Vision password',
    ]);

    $subject = 'Reset your Natty Vision password';
    nvacs_send_email($to, $subject, $body);
}

/* ============================================================
 * EMAIL TYPE 8: CUSTOMER NEW ACCOUNT (welcome)
 * ============================================================ */
add_filter('woocommerce_email_enabled_customer_new_account', '__return_false', 99);

add_action('woocommerce_created_customer_notification', 'nvacs_send_branded_new_account_email', 10, 3);
function nvacs_send_branded_new_account_email($customer_id, $new_customer_data, $password_generated) {
    if (!nvacs_wc_email_is_enabled('customer_new_account')) return;

    $user = get_userdata($customer_id);
    if (!$user) return;

    $to = $user->user_email;
    if (!$to) return;

    $first_name = $user->first_name ?: ($user->display_name ?: 'there');
    $login_url  = wc_get_page_permalink('myaccount');
    $shop_url   = wc_get_page_permalink('shop');

    $credentials_html = '<p style="margin:0;">Username: <strong>' . esc_html($user->user_login) . '</strong></p>';
    if ($password_generated && !empty($new_customer_data['user_pass'])) {
        $credentials_html .= '<p style="margin:8px 0 0;">Password: <strong>' . esc_html($new_customer_data['user_pass']) . '</strong></p>';
    }

    $content = nvacs_email_text_block($credentials_html, 'Your Account')
             . nvacs_email_button($login_url, 'Sign In');

    $body = nvacs_build_email_html([
        'pill_text'    => 'Welcome',
        'pill_color'   => 'sage',
        'headline'     => 'Welcome, %s' . esc_html($first_name) . '%s.',
        'subheading'   => "Your Natty Vision account is ready. Sign in anytime to view your orders or manage your details.",
        'content_html' => $content,
        'preheader'    => 'Your Natty Vision account is ready',
    ]);

    $subject = 'Welcome to Natty Vision';
    nvacs_send_email($to, $subject, $body);
}

/* ============================================================
 * HELPER: Check if a given WC email type is enabled in WP admin.
 * Lets us honor the WooCommerce → Settings → Emails toggles
 * without ever forcing-on a disabled email.
 * ============================================================ */
function nvacs_wc_email_is_enabled($email_id) {
    if (!function_exists('WC')) return false;
    $mailer = WC()->mailer();
    if (!$mailer) return false;
    $emails = $mailer->get_emails();
    foreach ($emails as $email) {
        if (isset($email->id) && $email->id === $email_id) {
            // IMPORTANT: We can't call is_enabled() here because we override
            // the woocommerce_email_enabled_{$id} filter to return false
            // (to suppress WC's default email so we can send our own).
            // Read the raw stored setting directly instead — this reflects
            // what the user has actually toggled in WP Admin → WC → Emails.
            $raw = isset($email->enabled) ? $email->enabled : 'yes';
            return ($raw === 'yes' || $raw === true || $raw === '1' || $raw === 1);
        }
    }
    return true; // unknown → send anyway (better to send than silently miss)
}

/* ============================================================
 * HELPER: Send a wp_mail with our standard branded headers.
 * Uses Resend via WP Mail SMTP (already configured).
 * ============================================================ */
function nvacs_send_email($to, $subject, $html_body) {
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: Natty Vision <' . get_option('admin_email') . '>',
    ];
    return wp_mail($to, $subject, $html_body, $headers);
}

/* ============================================================
 * MANUAL RESEND: Add "Resend branded ___ email" options to the
 * Order Actions dropdown on the order edit screen.
 *
 * Lets you retroactively send branded emails for orders placed
 * before the plugin was installed, or re-send if delivery failed.
 * Each action clears the relevant sent-meta and re-fires the hook.
 * ============================================================ */
add_filter('woocommerce_order_actions', 'nvacs_add_order_actions_for_emails');
function nvacs_add_order_actions_for_emails($actions) {
    $actions['nvacs_resend_processing'] = __('★ Resend branded Processing email', 'nvacs');
    $actions['nvacs_resend_completed']  = __('★ Resend branded Completed email',  'nvacs');
    $actions['nvacs_resend_onhold']     = __('★ Resend branded On-Hold email',    'nvacs');
    return $actions;
}

add_action('woocommerce_order_action_nvacs_resend_processing', function($order) {
    if (!is_a($order, 'WC_Order')) return;
    $order->delete_meta_data('_nvacs_processing_email_sent');
    $order->save();
    nvacs_send_branded_processing_email($order->get_id(), $order);
});

add_action('woocommerce_order_action_nvacs_resend_completed', function($order) {
    if (!is_a($order, 'WC_Order')) return;
    $order->delete_meta_data('_nvacs_completed_email_sent');
    $order->save();
    nvacs_send_branded_completed_email($order->get_id(), $order);
});

add_action('woocommerce_order_action_nvacs_resend_onhold', function($order) {
    if (!is_a($order, 'WC_Order')) return;
    $order->delete_meta_data('_nvacs_onhold_email_sent');
    $order->save();
    nvacs_send_branded_onhold_email($order->get_id(), $order);
});

/* ============================================================
 * MY ACCOUNT / LOGIN — STYLED TO MATCH NATTY VISION DESIGN
 *
 * Two things happen here:
 *   1. Trim the My Account sidebar nav down to: Orders,
 *      Account details, Log out (everything else hidden)
 *   2. Inject CSS that restyles the login form and my-account
 *      pages to match the site aesthetic (Instrument Serif
 *      headings, DM Mono labels, sage/off-white palette,
 *      dark pill buttons, etc.)
 *
 * Default redirect: when a customer logs in (not affiliates),
 * land them on /my-account/orders/ since that's the only
 * thing they care about.
 * ============================================================ */

// 1. Trim the navigation menu — keep only what matters
add_filter('woocommerce_account_menu_items', 'nvacs_filter_my_account_menu', 999);
function nvacs_filter_my_account_menu($items) {
    $keep = ['orders', 'edit-account', 'customer-logout'];
    $filtered = [];
    foreach ($items as $key => $label) {
        if (in_array($key, $keep, true)) {
            $filtered[$key] = $label;
        }
    }
    // Reorder: Orders first, Account details, Logout
    $ordered = [];
    foreach (['orders', 'edit-account', 'customer-logout'] as $key) {
        if (isset($filtered[$key])) $ordered[$key] = $filtered[$key];
    }
    return $ordered;
}

// 2. When a customer (non-admin, non-affiliate) lands on my-account dashboard,
//    redirect them straight to /my-account/orders/
add_action('template_redirect', 'nvacs_redirect_dashboard_to_orders');
function nvacs_redirect_dashboard_to_orders() {
    if (!function_exists('is_account_page') || !is_account_page()) return;
    if (!is_user_logged_in()) return;

    // Only redirect if they're literally on the bare /my-account/ page (dashboard)
    global $wp;
    $current = trim($wp->request, '/');
    $account_page_slug = trim(wp_parse_url(wc_get_page_permalink('myaccount'), PHP_URL_PATH), '/');

    if ($current === $account_page_slug) {
        wp_safe_redirect(wc_get_account_endpoint_url('orders'));
        exit;
    }
}

// 3. Inject the My Account / login CSS
add_action('wp_head', 'nvacs_my_account_css', 99);

/**
 * Add a body class on the My Account page when the user is logged out
 * (i.e. they're seeing the login form). Lets us target login-state styles
 * reliably without depending on :has() support.
 */
add_filter('body_class', function($classes) {
    if (function_exists('is_account_page') && is_account_page() && !is_user_logged_in()) {
        $classes[] = 'nv-account-login';
    }
    return $classes;
});

function nvacs_my_account_css() {
    if (!function_exists('is_account_page') || !is_account_page()) return;
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link rel="preconnect" href="https://api.fontshare.com">';
    echo '<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">';
    echo '<link href="https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap" rel="stylesheet">';
    ?>
<style id="nvacs-my-account-css">
:root {
    --nv-bg: #f2f0eb;
    --nv-bg2: #e9e7e1;
    --nv-bg-card: #eae8e2;
    --nv-sage: #c5d4c0;
    --nv-sage-s: #dce5d8;
    --nv-dark: #1a1e1c;
    --nv-green: #2d6a4f;
    --nv-text: #1a1e1c;
    --nv-t2: #4a4f4c;
    --nv-t3: #7a7f7c;
    --nv-ti: #f2f0eb;
    --nv-brd: #d4d2cc;
    --nv-brd2: #c4c2bc;
    --nv-r: 16px;
    --nv-rx: 8px;
    --nv-rp: 100px;
}

/* Body/wrap */
.woocommerce-account .woocommerce {
    font-family: 'Neue Montreal', -apple-system, BlinkMacSystemFont, sans-serif !important;
    color: var(--nv-text) !important;
    max-width: 1100px;
    margin: 0 auto;
    padding: 40px 24px 80px;
}

/* Page heading "My account" */
.woocommerce-account .page-title,
.woocommerce-account h1.entry-title,
.woocommerce-account .woocommerce > h1 {
    font-family: 'Instrument Serif', Georgia, serif !important;
    font-weight: 400 !important;
    font-size: clamp(40px, 6vw, 64px) !important;
    line-height: 1.05 !important;
    letter-spacing: -0.02em !important;
    color: var(--nv-text) !important;
    margin-bottom: 36px !important;
}

/* Hide "My account" page title on login page (logged out — redundant with "Login" h2) */
body.nv-account-login .page-title,
body.nv-account-login h1.entry-title,
body.nv-account-login .entry-header,
body.nv-account-login .woocommerce > h1,
body.nv-account-login header.entry-header,
body.nv-account-login .page-header,
body.nv-account-login .wp-block-post-title {
    display: none !important;
}

/* Hide avatars across all account pages — clean, minimal aesthetic */
.woocommerce-account .avatar,
.woocommerce-account img.avatar,
.woocommerce-account .user-avatar,
.woocommerce-MyAccount-content .avatar,
.woocommerce-MyAccount-navigation .avatar,
.woocommerce-account [class*="avatar"] img,
.woocommerce-account figure.avatar,
.woocommerce-account .author-avatar,
.woocommerce-account .customer-avatar {
    display: none !important;
    visibility: hidden !important;
    width: 0 !important;
    height: 0 !important;
    opacity: 0 !important;
}
/* Hide the entire theme-injected user-info block (avatar + display name above nav) */
.woocommerce-account .avatar-wrap,
.woocommerce-account .user-meta,
.woocommerce-account .author-bio,
.woocommerce-account .account-user,
.woocommerce-account .account-user-info,
.woocommerce-account .user-info,
.woocommerce-account .my-account-user,
.woocommerce-account .woocommerce-MyAccount-user,
.woocommerce-account .customer-info,
.woocommerce-account .account-header,
.woocommerce-account .woocommerce > .user-meta,
.woocommerce-account .woocommerce > .author-info,
.woocommerce-account aside .avatar,
.woocommerce-account aside figure {
    display: none !important;
}

/* ========== LOGIN PAGE ========== */
.woocommerce-account .woocommerce-form-login {
    background: var(--nv-bg2) !important;
    border: 1px solid var(--nv-brd) !important;
    border-radius: 20px !important;
    padding: 48px !important;
    max-width: 520px !important;
    margin: 0 auto !important;
    box-shadow: 0 8px 30px rgba(26,30,28,0.04) !important;
}

.woocommerce-account h2,
.woocommerce-account .woocommerce-form-login h2 {
    font-family: 'Instrument Serif', Georgia, serif !important;
    font-weight: 400 !important;
    font-size: 36px !important;
    line-height: 1.1 !important;
    color: var(--nv-text) !important;
    margin-bottom: 24px !important;
    border: none !important;
}

/* Form labels */
.woocommerce-form-row label,
.woocommerce-account label {
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    color: var(--nv-t2) !important;
    font-weight: 400 !important;
    margin-bottom: 8px !important;
    display: block !important;
}

/* Required asterisk */
.woocommerce-account .required {
    color: var(--nv-green) !important;
    text-decoration: none !important;
}

/* Form inputs */
.woocommerce-account .woocommerce-Input,
.woocommerce-account input[type="text"],
.woocommerce-account input[type="email"],
.woocommerce-account input[type="password"],
.woocommerce-account input[type="tel"],
.woocommerce-account select,
.woocommerce-account textarea {
    background: var(--nv-bg) !important;
    border: 1px solid var(--nv-brd) !important;
    border-radius: var(--nv-rx) !important;
    padding: 14px 16px !important;
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 14px !important;
    color: var(--nv-text) !important;
    width: 100% !important;
    transition: all 0.3s ease !important;
    box-sizing: border-box !important;
    box-shadow: none !important;
}

/* Hide password visibility toggle icon */
.woocommerce-account input[type="password"]::-webkit-outer-spin-button,
.woocommerce-account input[type="password"]::-webkit-inner-spin-button {
    -webkit-appearance: none !important;
    margin: 0 !important;
}
.woocommerce-account input[type="password"] {
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
}

.woocommerce-account input:focus,
.woocommerce-account select:focus,
.woocommerce-account textarea:focus {
    outline: none !important;
    border-color: var(--nv-green) !important;
    box-shadow: 0 0 0 3px rgba(45,106,79,0.12) !important;
}

/* Buttons */
.woocommerce-account button.button,
.woocommerce-account .button,
.woocommerce-account input[type="submit"],
.woocommerce-account .woocommerce-button {
    background: var(--nv-dark) !important;
    color: var(--nv-ti) !important;
    border: none !important;
    border-radius: var(--nv-rx) !important;
    padding: 14px 28px !important;
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    font-weight: 500 !important;
    cursor: pointer !important;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
    text-decoration: none !important;
    display: inline-block !important;
}
.woocommerce-account button.button:hover,
.woocommerce-account .button:hover,
.woocommerce-account input[type="submit"]:hover,
.woocommerce-account .woocommerce-button:hover {
    background: #2a302d !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 12px 30px rgba(26,30,28,0.15) !important;
}

/* Remember me row */
.woocommerce-form-login__rememberme,
.woocommerce-form__label-for-checkbox {
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    color: var(--nv-t2) !important;
}

/* Lost password link */
.woocommerce-LostPassword a {
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    color: var(--nv-green) !important;
    text-decoration: none !important;
}
.woocommerce-LostPassword a:hover {
    text-decoration: underline !important;
}

/* ========== DASHBOARD LAYOUT ========== */
.woocommerce-MyAccount-navigation {
    width: 240px !important;
    float: left !important;
    margin-right: 40px !important;
}

.woocommerce-MyAccount-navigation ul {
    list-style: none !important;
    margin: 0 !important;
    padding: 16px !important;
    background: var(--nv-bg2) !important;
    border: 1px solid var(--nv-brd) !important;
    border-radius: var(--nv-r) !important;
}

.woocommerce-MyAccount-navigation li {
    margin: 0 !important;
    padding: 0 !important;
    list-style: none !important;
    border: none !important;
}
.woocommerce-MyAccount-navigation li::before { display: none !important; }

.woocommerce-MyAccount-navigation li a {
    display: block !important;
    padding: 12px 16px !important;
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    color: var(--nv-t2) !important;
    text-decoration: none !important;
    border-radius: var(--nv-rx) !important;
    transition: all 0.3s ease !important;
    border: none !important;
    background: transparent !important;
}
.woocommerce-MyAccount-navigation li a:hover {
    background: var(--nv-bg-card) !important;
    color: var(--nv-text) !important;
}

.woocommerce-MyAccount-navigation li.is-active a,
.woocommerce-MyAccount-navigation li.woocommerce-MyAccount-navigation-link--orders.is-active a,
.woocommerce-MyAccount-navigation li.woocommerce-MyAccount-navigation-link--edit-account.is-active a {
    background: var(--nv-sage-s) !important;
    color: var(--nv-green) !important;
    font-weight: 500 !important;
}

/* Content area */
.woocommerce-MyAccount-content {
    overflow: hidden !important;
    background: transparent !important;
}

.woocommerce-MyAccount-content h2,
.woocommerce-MyAccount-content h3 {
    font-family: 'Instrument Serif', Georgia, serif !important;
    font-weight: 400 !important;
    color: var(--nv-text) !important;
    border: none !important;
    margin-bottom: 20px !important;
}
.woocommerce-MyAccount-content h2 { font-size: 32px !important; }
.woocommerce-MyAccount-content h3 { font-size: 22px !important; }

/* Dashboard intro paragraph */
.woocommerce-MyAccount-content > p {
    font-family: 'Neue Montreal', -apple-system, sans-serif !important;
    font-size: 16px !important;
    line-height: 1.65 !important;
    color: var(--nv-t2) !important;
    margin-bottom: 18px !important;
}

/* ========== ORDERS TABLE ========== */
.woocommerce-account .woocommerce-orders-table,
.woocommerce-account table.shop_table {
    border-collapse: separate !important;
    border-spacing: 0 !important;
    background: var(--nv-bg2) !important;
    border: 1px solid var(--nv-brd) !important;
    border-radius: var(--nv-r) !important;
    overflow: hidden !important;
    width: 100% !important;
    margin: 0 0 24px !important;
}
.woocommerce-account .woocommerce-orders-table th,
.woocommerce-account table.shop_table th {
    background: transparent !important;
    border: none !important;
    border-bottom: 1px solid var(--nv-brd) !important;
    padding: 14px 18px !important;
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 10px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.12em !important;
    color: var(--nv-t3) !important;
    font-weight: 500 !important;
    text-align: left !important;
}
.woocommerce-account .woocommerce-orders-table td,
.woocommerce-account table.shop_table td {
    background: transparent !important;
    border: none !important;
    border-bottom: 1px solid var(--nv-brd) !important;
    padding: 18px !important;
    font-family: 'Neue Montreal', sans-serif !important;
    font-size: 14px !important;
    color: var(--nv-text) !important;
    vertical-align: middle !important;
}
.woocommerce-account .woocommerce-orders-table tr:last-child td {
    border-bottom: none !important;
}

/* Order number column */
.woocommerce-account .woocommerce-orders-table__cell-order-number a,
.woocommerce-account td.woocommerce-orders-table__cell-order-number a {
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 13px !important;
    color: var(--nv-green) !important;
    text-decoration: none !important;
    font-weight: 500 !important;
}
.woocommerce-account .woocommerce-orders-table__cell-order-number a:hover {
    text-decoration: underline !important;
}

/* Status pill */
.woocommerce-account .woocommerce-orders-table__cell-order-status,
.woocommerce-account mark.order-status {
    background: transparent !important;
    color: inherit !important;
}
.woocommerce-account mark.order-status {
    display: inline-block !important;
    background: var(--nv-sage-s) !important;
    color: var(--nv-green) !important;
    border: 1px solid var(--nv-sage) !important;
    border-radius: var(--nv-rp) !important;
    padding: 4px 12px !important;
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 10px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    font-weight: 500 !important;
}
mark.order-status.status-on-hold {
    background: #faf0d6 !important;
    border-color: #e5cb74 !important;
    color: #6b5414 !important;
}
mark.order-status.status-cancelled,
mark.order-status.status-failed,
mark.order-status.status-refunded {
    background: #fde8e8 !important;
    border-color: #f0a8a8 !important;
    color: #872a2a !important;
}

/* Action buttons inside orders table */
.woocommerce-account .woocommerce-orders-table .button {
    padding: 8px 16px !important;
    font-size: 10px !important;
}

/* Empty state */
.woocommerce-account .woocommerce-message,
.woocommerce-account .woocommerce-info {
    background: var(--nv-bg2) !important;
    border: 1px solid var(--nv-brd) !important;
    border-left: 4px solid var(--nv-sage) !important;
    border-radius: var(--nv-rx) !important;
    padding: 16px 20px !important;
    font-family: 'Neue Montreal', sans-serif !important;
    font-size: 14px !important;
    color: var(--nv-text) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 16px !important;
}
/* Remove WooCommerce default icon/checkmark on info messages */
.woocommerce-account .woocommerce-message::before,
.woocommerce-account .woocommerce-info::before {
    content: none !important;
    display: none !important;
}

/* Pagination */
.woocommerce-account .woocommerce-pagination ul {
    border: none !important;
    border-radius: var(--nv-rx) !important;
    overflow: hidden !important;
}
.woocommerce-account .woocommerce-pagination li a,
.woocommerce-account .woocommerce-pagination li span {
    background: var(--nv-bg2) !important;
    border: 1px solid var(--nv-brd) !important;
    color: var(--nv-text) !important;
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 12px !important;
    padding: 8px 12px !important;
}
.woocommerce-account .woocommerce-pagination li span.current {
    background: var(--nv-dark) !important;
    color: var(--nv-ti) !important;
    border-color: var(--nv-dark) !important;
}

/* ========== ACCOUNT DETAILS FORM ========== */
.woocommerce-account form.woocommerce-EditAccountForm {
    background: var(--nv-bg2) !important;
    border: 1px solid var(--nv-brd) !important;
    border-radius: var(--nv-r) !important;
    padding: 32px !important;
}

.woocommerce-account .woocommerce-EditAccountForm fieldset {
    border: 1px solid var(--nv-brd) !important;
    border-radius: var(--nv-rx) !important;
    padding: 20px !important;
    margin-top: 24px !important;
    background: var(--nv-bg) !important;
}
.woocommerce-account .woocommerce-EditAccountForm legend {
    font-family: 'DM Mono', 'Courier New', monospace !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.1em !important;
    color: var(--nv-text) !important;
    padding: 0 10px !important;
}

/* ========== ORDER DETAILS PAGE ========== */
.woocommerce-account .woocommerce-order-details,
.woocommerce-account .woocommerce-customer-details {
    background: var(--nv-bg2) !important;
    border: 1px solid var(--nv-brd) !important;
    border-radius: var(--nv-r) !important;
    padding: 32px !important;
    margin-bottom: 24px !important;
}

/* ========== MOBILE ========== */
@media (max-width: 768px) {
    .woocommerce-account .woocommerce {
        padding: 24px 16px 60px !important;
    }
    .woocommerce-account .page-title,
    .woocommerce-account h1.entry-title {
        font-size: 36px !important;
        margin-bottom: 24px !important;
    }
    .woocommerce-MyAccount-navigation {
        width: 100% !important;
        float: none !important;
        margin-right: 0 !important;
        margin-bottom: 24px !important;
    }
    .woocommerce-MyAccount-navigation ul {
        display: flex !important;
        flex-direction: column !important;
        padding: 12px !important;
    }
    .woocommerce-MyAccount-content {
        width: 100% !important;
    }
    .woocommerce-account .woocommerce-form-login {
        padding: 28px 22px !important;
    }
    .woocommerce-account button.button,
    .woocommerce-account .button,
    .woocommerce-account input[type="submit"] {
        width: 100% !important;
        text-align: center !important;
    }
    .woocommerce-orders-table thead { display: none !important; }
    .woocommerce-orders-table tr {
        display: block !important;
        background: var(--nv-bg2) !important;
        border: 1px solid var(--nv-brd) !important;
        border-radius: var(--nv-rx) !important;
        padding: 16px !important;
        margin-bottom: 12px !important;
    }
    .woocommerce-orders-table td {
        display: flex !important;
        justify-content: space-between !important;
        padding: 8px 0 !important;
        border-bottom: 1px solid var(--nv-brd) !important;
    }
    .woocommerce-orders-table td::before {
        content: attr(data-title);
        font-family: 'DM Mono', 'Courier New', monospace !important;
        font-size: 10px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.1em !important;
        color: var(--nv-t3) !important;
    }
}
</style>
<script>
(function(){
    function cleanAccountPage(){
        // 1. Remove any avatar images + their immediate parent block (the theme's user-info widget)
        document.querySelectorAll('.woocommerce-account img.avatar, .woocommerce-account .avatar, .woocommerce-account [class*="gravatar"]').forEach(function(el){
            // Walk up to the wrapping block (max 3 levels) and hide it if it isn't the main content
            var node = el;
            for (var i = 0; i < 3; i++) {
                if (!node || !node.parentElement) break;
                var parent = node.parentElement;
                // Don't hide the main content / nav / page wrapper
                if (parent.classList.contains('woocommerce-MyAccount-content') ||
                    parent.classList.contains('woocommerce-MyAccount-navigation') ||
                    parent.classList.contains('woocommerce') ||
                    parent.tagName === 'MAIN' || parent.tagName === 'BODY') {
                    break;
                }
                node = parent;
            }
            if (node && node !== document.body) node.style.display = 'none';
        });

        // 2. On login page (logged out), hide any "My account" headings
        if (document.body.classList.contains('nv-account-login')) {
            document.querySelectorAll('.woocommerce-account h1, .woocommerce-account .page-title, .woocommerce-account .entry-title, .woocommerce-account header.entry-header').forEach(function(el){
                var txt = (el.textContent || '').trim().toLowerCase();
                if (txt === 'my account' || txt === 'my-account') {
                    el.style.display = 'none';
                }
            });
        }
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', cleanAccountPage);
    } else {
        cleanAccountPage();
    }
})();
</script>
    <?php
}

/* ============================================================
 * SIGN-IN CODE LOGIN FOR CUSTOMERS (passwordless)
 *
 * Replaces /my-account/ password login with a 6-digit code
 * emailed to the customer. They enter the code on the same
 * page to log in — no link clicking, no cross-browser hassle.
 * Affiliates are NOT affected (they continue using
 * /affiliate-login/ with passwords as before).
 *
 * Flow:
 *   1. Customer visits /my-account/ → sees "enter email" form
 *   2. Enters email → POST → 6-digit code generated + hashed,
 *      stored, emailed via Resend
 *   3. Page swaps to "enter code" form (email is pre-filled in session)
 *   4. Customer pastes 6-digit code from email → POST → verified,
 *      logged in via wp_set_auth_cookie(), redirected to /my-account/orders/
 *
 * Security:
 *   - Codes are bcrypt-hashed before storage (DB breach doesn't leak)
 *   - One-time use (marked used after consumption)
 *   - 15-minute expiry on codes
 *   - Rate limit: max 3 sends per email per 15-minute window
 *   - Max 5 verify attempts per code (then it's burned)
 *   - Constant-time response — no user enumeration
 *   - Auto-creates accounts for unknown emails
 * ============================================================ */

define('NVACS_MAGIC_TTL', 30 * MINUTE_IN_SECONDS); // 30 minutes
define('NVACS_MAGIC_RATE_WINDOW', 15 * MINUTE_IN_SECONDS);
define('NVACS_MAGIC_RATE_MAX', 3);
define('NVACS_MAGIC_MAX_ATTEMPTS', 5);
define('NVACS_MAGIC_GRACE', 60); // 60-second grace for clock skew

/* ------------------------------------------------------------
 * Create DB table on activation
 * ------------------------------------------------------------ */
register_activation_hook(__FILE__, 'nvacs_magic_create_table');
add_action('plugins_loaded', 'nvacs_magic_create_table');
function nvacs_magic_create_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'nvacs_magic_links';

    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        email VARCHAR(190) NOT NULL,
        token_hash VARCHAR(255) NOT NULL,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        created_at INT(11) NOT NULL,
        expires_at INT(11) NOT NULL,
        used_at INT(11) DEFAULT NULL,
        attempts INT(11) DEFAULT 0,
        ip VARCHAR(45) DEFAULT NULL,
        PRIMARY KEY (id),
        KEY email_idx (email),
        KEY expires_idx (expires_at)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);

    // dbDelta sometimes doesn't add new columns on shared hosting (WordPress.com etc).
    // Explicitly check and add the 'attempts' column if missing.
    $columns = $wpdb->get_col("SHOW COLUMNS FROM $table", 0);
    if (!in_array('attempts', $columns, true)) {
        $wpdb->query("ALTER TABLE $table ADD COLUMN attempts INT(11) DEFAULT 0");
        error_log('[NVACS Magic] Added missing attempts column. Result: ' . $wpdb->last_error);
    }

    update_option('nvacs_magic_table_version', '3');
}

/* ------------------------------------------------------------
 * Daily cleanup
 * ------------------------------------------------------------ */
add_action('nvacs_magic_cleanup', 'nvacs_magic_cleanup_expired');
function nvacs_magic_cleanup_expired() {
    global $wpdb;
    $table = $wpdb->prefix . 'nvacs_magic_links';
    $cutoff = time() - DAY_IN_SECONDS;
    $wpdb->query($wpdb->prepare("DELETE FROM $table WHERE expires_at < %d", $cutoff));
}
add_action('init', function() {
    if (!wp_next_scheduled('nvacs_magic_cleanup')) {
        wp_schedule_event(time(), 'daily', 'nvacs_magic_cleanup');
    }
});

/* ------------------------------------------------------------
 * Pending-email storage between request and verify.
 *
 * We use a signed cookie (not WC session) because WC session can
 * be flaky for logged-out users and may not persist across the
 * POST→redirect→POST flow. Cookie is signed with wp_hash() so
 * the user can't forge it to claim a different email.
 *
 * Cookie value format: "email|hmac"
 * ------------------------------------------------------------ */
function nvacs_magic_set_pending_email($email) {
    $value = $email . '|' . wp_hash($email);
    setcookie(
        'nvacs_magic_pending',
        $value,
        [
            'expires'  => time() + NVACS_MAGIC_TTL,
            'path'     => '/',
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]
    );
    // Also set in $_COOKIE so it's available immediately in current request
    $_COOKIE['nvacs_magic_pending'] = $value;
}
function nvacs_magic_get_pending_email() {
    if (empty($_COOKIE['nvacs_magic_pending'])) return '';
    $parts = explode('|', $_COOKIE['nvacs_magic_pending'], 2);
    if (count($parts) !== 2) return '';
    list($email, $hmac) = $parts;
    if (!hash_equals(wp_hash($email), $hmac)) return '';
    return is_email($email) ? $email : '';
}
function nvacs_magic_clear_pending_email() {
    setcookie('nvacs_magic_pending', '', [
        'expires'  => time() - 3600,
        'path'     => '/',
        'secure'   => is_ssl(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    unset($_COOKIE['nvacs_magic_pending']);
}

/* ------------------------------------------------------------
 * Render the sign-in code form on /my-account/
 * Three states:
 *   - default: enter email
 *   - sent: enter the code (after email submission)
 *   - error states
 * ------------------------------------------------------------ */
add_action('woocommerce_before_customer_login_form', 'nvacs_render_signin_form');
function nvacs_render_signin_form() {
    // Don't interfere with affiliate login
    $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    if (strpos($uri, '/affiliate-login') !== false) return;

    $sent          = isset($_GET['nv_code_sent']) && $_GET['nv_code_sent'] === '1';
    $error         = isset($_GET['nv_code_error']) ? sanitize_key($_GET['nv_code_error']) : '';
    $pending_email = nvacs_magic_get_pending_email();

    // Note: we keep the "sent" view even if cookie is missing, because the
    // verify form's hidden email field is what really matters now. If we can't
    // recover the email here, the user can still get back to the email form
    // by refreshing or hitting the "request a new code" link.
    if ($sent && !$pending_email) {
        $sent = false;
    }

    $myaccount_url = wc_get_page_permalink('myaccount');
    ?>
    <style>
    /* Hide the default WooCommerce password login form */
    .woocommerce-form-login,
    .u-column2.col-2 {
        display: none !important;
    }
    /* Hide default headings inside the login layout */
    .u-columns.col2-set h2,
    .u-column1.col-1 h2,
    .woocommerce-account .col2-set h2,
    .woocommerce-account .u-column1 > h2,
    .woocommerce-account .u-column1 > h3,
    .woocommerce-account .u-column1 > h1 {
        display: none !important;
    }
    /* Class added by our JS to hide any "Login" heading the theme adds */
    .nv-hide-login-heading {
        display: none !important;
    }
    .u-columns.col2-set,
    .u-columns.col2-set > .u-column1 {
        width: 100% !important;
        max-width: 520px !important;
        margin: 0 auto !important;
        float: none !important;
    }
    .nv-magic-card {
        background: #e9e7e1;
        border: 1px solid #d4d2cc;
        border-radius: 20px;
        padding: 48px;
        margin: 0 auto;
        max-width: 520px;
        box-shadow: 0 8px 30px rgba(26,30,28,0.04);
    }
    .nv-magic-h1 {
        font-family: 'Instrument Serif', Georgia, serif;
        font-weight: 400;
        font-size: 38px;
        line-height: 1.1;
        letter-spacing: -0.02em;
        color: #1a1e1c;
        margin: 0 0 12px;
    }
    .nv-magic-h1 em {
        font-style: italic;
        color: #2d6a4f;
    }
    .nv-magic-sub {
        font-family: 'Neue Montreal', -apple-system, sans-serif;
        font-size: 15px;
        line-height: 1.6;
        color: #4a4f4c;
        margin: 0 0 28px;
    }
    .nv-magic-sub strong {
        color: #1a1e1c;
        font-weight: 500;
    }
    .nv-magic-label {
        display: block;
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #4a4f4c;
        margin-bottom: 8px;
    }
    .nv-magic-input {
        background: #f2f0eb;
        border: 1px solid #d4d2cc;
        border-radius: 8px;
        padding: 14px 16px;
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 14px;
        color: #1a1e1c;
        width: 100%;
        box-sizing: border-box;
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }
    .nv-magic-input:focus {
        outline: none;
        border-color: #2d6a4f;
        box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
    }
    .nv-magic-input-code {
        font-size: 28px;
        text-align: center;
        letter-spacing: 0.5em;
        padding-left: calc(16px + 0.25em);
        font-family: 'DM Mono', 'Courier New', monospace;
        font-weight: 500;
    }
    .nv-magic-button {
        background: #1a1e1c;
        color: #f2f0eb;
        border: none;
        border-radius: 8px;
        padding: 16px 28px;
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 500;
        cursor: pointer;
        width: 100%;
        transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .nv-magic-button:hover {
        background: #2a302d;
        transform: translateY(-1px);
        box-shadow: 0 12px 30px rgba(26,30,28,0.15);
    }
    .nv-magic-error {
        background: #faf0d6;
        border: 1px solid #e5cb74;
        border-radius: 10px;
        padding: 14px 18px;
        margin-bottom: 24px;
        font-family: 'Neue Montreal', sans-serif;
        font-size: 13px;
        color: #6b5414;
        line-height: 1.5;
    }
    .nv-magic-footer {
        font-family: 'Neue Montreal', sans-serif;
        font-size: 13px;
        color: #7a7f7c;
        text-align: center;
        margin-top: 20px;
        line-height: 1.6;
    }
    .nv-magic-footer a {
        color: #2d6a4f;
        text-decoration: none;
        font-weight: 500;
    }
    .nv-magic-footer a:hover { text-decoration: underline; }
    .nv-magic-link-btn {
        background: none;
        border: none;
        color: #2d6a4f;
        font-family: 'Neue Montreal', sans-serif;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        padding: 0;
        text-decoration: underline;
    }
    .nv-magic-hp {
        position: absolute;
        left: -9999px;
        width: 1px;
        height: 1px;
        opacity: 0;
    }
    </style>

    <div class="nv-magic-card">

        <?php if ($error === 'invalidcode'): ?>
            <div class="nv-magic-error">That code is incorrect. Please check the email and try again.</div>
        <?php elseif ($error === 'expired'): ?>
            <div class="nv-magic-error">That code has expired. Request a new one below.</div>
        <?php elseif ($error === 'used'): ?>
            <div class="nv-magic-error">That code has already been used. Request a new one below.</div>
        <?php elseif ($error === 'attempts'): ?>
            <div class="nv-magic-error">Too many incorrect attempts. Request a new code below.</div>
        <?php elseif ($error === 'ratelimit'): ?>
            <div class="nv-magic-error">Too many requests. Please wait 15 minutes and try again.</div>
        <?php elseif ($error === 'invalidemail'): ?>
            <div class="nv-magic-error">Please enter a valid email address.</div>
        <?php endif; ?>

        <?php if ($sent): ?>
            <!-- STATE 2: Enter the code -->
            <h1 class="nv-magic-h1">Check your <em>email</em></h1>
            <p class="nv-magic-sub">
                We sent a 6-digit sign-in code to <strong><?php echo esc_html($pending_email); ?></strong>.
                Enter it below to sign in. The code expires in 30 minutes.
            </p>

            <form method="post" action="<?php echo esc_url(isset($_GET['nvacs_debug_verify']) ? add_query_arg('nvacs_debug_verify', '1', $myaccount_url) : $myaccount_url); ?>">
                <?php wp_nonce_field('nvacs_magic_verify', 'nvacs_magic_nonce'); ?>
                <input type="hidden" name="nvacs_magic_action" value="verify">
                <input type="hidden" name="nvacs_magic_email" value="<?php echo esc_attr($pending_email); ?>">
                <?php if (isset($_GET['nvacs_debug_verify'])): ?>
                    <input type="hidden" name="nvacs_debug_verify" value="1">
                <?php endif; ?>

                <label class="nv-magic-label" for="nvacs_magic_code">Sign-in Code</label>
                <input
                    type="text"
                    id="nvacs_magic_code"
                    name="nvacs_magic_code"
                    class="nv-magic-input nv-magic-input-code"
                    placeholder="000000"
                    required
                    inputmode="numeric"
                    pattern="[0-9]*"
                    maxlength="6"
                    autocomplete="one-time-code"
                    autofocus
                >

                <button type="submit" class="nv-magic-button">Sign In →</button>
            </form>

            <div class="nv-magic-footer">
                Didn't get an email? Check spam, or
                <form method="post" action="<?php echo esc_url($myaccount_url); ?>" style="display:inline;">
                    <?php wp_nonce_field('nvacs_magic_resend', 'nvacs_magic_nonce'); ?>
                    <input type="hidden" name="nvacs_magic_action" value="resend">
                    <input type="hidden" name="nvacs_magic_email" value="<?php echo esc_attr($pending_email); ?>">
                    <button type="submit" class="nv-magic-link-btn">request a new code</button>
                </form>
            </div>

        <?php else: ?>
            <!-- STATE 1: Enter email -->
            <h1 class="nv-magic-h1">Sign in to <em>Natty Vision</em></h1>
            <p class="nv-magic-sub">Enter your email and we'll send you a 6-digit sign-in code. No password needed.</p>

            <form method="post" action="<?php echo esc_url($myaccount_url); ?>">
                <?php wp_nonce_field('nvacs_magic_request', 'nvacs_magic_nonce'); ?>
                <input type="hidden" name="nvacs_magic_action" value="request">

                <label class="nv-magic-label" for="nvacs_magic_email">Email Address</label>
                <input
                    type="email"
                    id="nvacs_magic_email"
                    name="nvacs_magic_email"
                    class="nv-magic-input"
                    placeholder="you@example.com"
                    required
                    autocomplete="email"
                    autofocus
                >
                <input type="text" name="nvacs_website" class="nv-magic-hp" tabindex="-1" autocomplete="off">

                <button type="submit" class="nv-magic-button">Send sign-in code →</button>
            </form>

            <p class="nv-magic-footer">
                Are you an affiliate? <a href="<?php echo esc_url(home_url('/affiliate-login/')); ?>">Sign in here →</a>
            </p>

        <?php endif; ?>
    </div>

    <script>
    // Catch-all: hide any heading-like element whose text is "Login"
    // (themes render it as h1/h2/h3/div depending on overrides).
    // Scope to the account page wrapper so we don't break anything else.
    (function() {
        function hideLoginHeading() {
            var scope = document.querySelector('.woocommerce-account, .woocommerce-page, body');
            if (!scope) return;
            var candidates = scope.querySelectorAll('h1, h2, h3, h4, .page-title, .entry-title');
            candidates.forEach(function(el) {
                if (el.classList.contains('nv-magic-h1')) return;
                var txt = (el.textContent || '').trim().toLowerCase();
                if (txt === 'login' || txt === 'log in') {
                    el.classList.add('nv-hide-login-heading');
                }
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', hideLoginHeading);
        } else {
            hideLoginHeading();
        }
    })();
    </script>
    <?php
}

/* ------------------------------------------------------------
 * Form router — POST handler dispatches by nvacs_magic_action
 * ------------------------------------------------------------ */
add_action('template_redirect', 'nvacs_magic_router', 5);
function nvacs_magic_router() {
    if (empty($_POST['nvacs_magic_action'])) return;
    $action = sanitize_key($_POST['nvacs_magic_action']);

    switch ($action) {
        case 'request':
            nvacs_magic_handle_request();
            break;
        case 'verify':
            nvacs_magic_handle_verify();
            break;
        case 'resend':
            nvacs_magic_handle_resend();
            break;
    }
}

/* ------------------------------------------------------------
 * Handle: send sign-in code to email
 * ------------------------------------------------------------ */
function nvacs_magic_handle_request() {
    if (!isset($_POST['nvacs_magic_nonce']) || !wp_verify_nonce($_POST['nvacs_magic_nonce'], 'nvacs_magic_request')) return;

    $myaccount_url = wc_get_page_permalink('myaccount');

    // Honeypot
    if (!empty($_POST['nvacs_website'])) {
        wp_safe_redirect(add_query_arg('nv_code_sent', '1', $myaccount_url));
        exit;
    }

    $email = sanitize_email(wp_unslash($_POST['nvacs_magic_email'] ?? ''));
    if (!is_email($email)) {
        wp_safe_redirect(add_query_arg('nv_code_error', 'invalidemail', $myaccount_url));
        exit;
    }

    if (nvacs_magic_is_rate_limited($email)) {
        wp_safe_redirect(add_query_arg('nv_code_error', 'ratelimit', $myaccount_url));
        exit;
    }

    $user = get_user_by('email', $email);
    if (!$user) {
        // Auto-create customer account
        $local = strtolower(explode('@', $email)[0]);
        $username = preg_replace('/[^a-z0-9._-]/', '', $local) ?: 'customer';
        $base = $username;
        $i = 1;
        while (username_exists($username)) {
            $username = $base . $i;
            $i++;
        }
        $user_id = wp_create_user($username, wp_generate_password(32, true, true), $email);
        if (is_wp_error($user_id)) {
            wp_safe_redirect(add_query_arg('nv_code_sent', '1', $myaccount_url));
            exit;
        }
        $new_user = new WP_User($user_id);
        $new_user->set_role('customer');
        $user = $new_user;
    }

    nvacs_magic_create_and_send($user, $email);
    nvacs_magic_set_pending_email($email);

    wp_safe_redirect(add_query_arg('nv_code_sent', '1', $myaccount_url));
    exit;
}

/* ------------------------------------------------------------
 * Handle: re-send a new code (from "didn't get it" link)
 * ------------------------------------------------------------ */
function nvacs_magic_handle_resend() {
    if (!isset($_POST['nvacs_magic_nonce']) || !wp_verify_nonce($_POST['nvacs_magic_nonce'], 'nvacs_magic_resend')) return;

    $myaccount_url = wc_get_page_permalink('myaccount');

    // Pull email from POST, fall back to cookie
    $email = isset($_POST['nvacs_magic_email']) ? sanitize_email(wp_unslash($_POST['nvacs_magic_email'])) : '';
    if (!$email || !is_email($email)) {
        $email = nvacs_magic_get_pending_email();
    }

    if (!$email || !is_email($email)) {
        wp_safe_redirect($myaccount_url);
        exit;
    }

    if (nvacs_magic_is_rate_limited($email)) {
        wp_safe_redirect(add_query_arg(['nv_code_sent' => '1', 'nv_code_error' => 'ratelimit'], $myaccount_url));
        exit;
    }

    $user = get_user_by('email', $email);
    if ($user) {
        nvacs_magic_create_and_send($user, $email);
        nvacs_magic_set_pending_email($email);
    }

    wp_safe_redirect(add_query_arg('nv_code_sent', '1', $myaccount_url));
    exit;
}

/* ------------------------------------------------------------
 * Handle: verify the entered 6-digit code
 * ------------------------------------------------------------ */
function nvacs_magic_handle_verify() {
    if (!isset($_POST['nvacs_magic_nonce']) || !wp_verify_nonce($_POST['nvacs_magic_nonce'], 'nvacs_magic_verify')) {
        error_log('[NVACS Magic] verify: nonce failed');
        return;
    }

    $myaccount_url = wc_get_page_permalink('myaccount');

    // Pull email from POST (set by hidden field on form), fall back to cookie
    $email = isset($_POST['nvacs_magic_email']) ? sanitize_email(wp_unslash($_POST['nvacs_magic_email'])) : '';
    if (!$email || !is_email($email)) {
        $email = nvacs_magic_get_pending_email();
    }
    $code = isset($_POST['nvacs_magic_code']) ? preg_replace('/\D/', '', $_POST['nvacs_magic_code']) : '';

    error_log('[NVACS Magic] verify: email=' . ($email ?: '(empty)') . ' code_length=' . strlen($code));

    if (!$email || strlen($code) !== 6) {
        error_log('[NVACS Magic] verify: missing email or code wrong length');
        wp_safe_redirect(add_query_arg(['nv_code_sent' => '1', 'nv_code_error' => 'invalidcode'], $myaccount_url));
        exit;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'nvacs_magic_links';

    // DEBUG MODE — triggered by ?nvacs_debug_verify=1, cookie, OR a server-side option
    // (the option is the most reliable since it survives redirects across any browser)
    $debug_verify = isset($_GET['nvacs_debug_verify'])
                 || isset($_POST['nvacs_debug_verify'])
                 || (isset($_COOKIE['nvacs_debug']) && $_COOKIE['nvacs_debug'] === '1')
                 || get_option('nvacs_debug_verify') === '1';

    // Get ALL unused codes for this email (newest first) — INCLUDE expired ones for debugging
    $rows = $wpdb->get_results($wpdb->prepare(
        "SELECT * FROM $table WHERE email = %s AND used_at IS NULL ORDER BY id DESC LIMIT 5",
        $email
    ));

    if ($debug_verify) {
        header('Content-Type: text/plain');
        echo "=== VERIFY DEBUG ===\n";
        echo "POSTed email: " . ($email ?: '(empty)') . "\n";
        echo "POSTed code: " . $code . " (length: " . strlen($code) . ")\n";
        echo "Current time(): " . time() . "\n";
        echo "TTL: " . NVACS_MAGIC_TTL . "s, Grace: " . NVACS_MAGIC_GRACE . "s\n";
        echo "Rows found: " . count($rows) . "\n\n";
        foreach ($rows as $r) {
            $is_expired = ((int) $r->expires_at + NVACS_MAGIC_GRACE < time());
            $check = wp_check_password($code, $r->token_hash);
            echo "Row id={$r->id}\n";
            echo "  expires_at={$r->expires_at} (now+{$r->expires_at}-now=" . ($r->expires_at - time()) . "s)\n";
            echo "  expired? " . ($is_expired ? 'YES' : 'no') . "\n";
            echo "  attempts={$r->attempts}\n";
            echo "  wp_check_password match? " . ($check ? 'YES' : 'no') . "\n";
            echo "  token_hash starts with: " . substr($r->token_hash, 0, 15) . "...\n\n";
        }
        exit;
    }

    if (empty($rows)) {
        error_log('[NVACS Magic] verify: no valid rows found for email ' . $email);
        wp_safe_redirect(add_query_arg(['nv_code_sent' => '1', 'nv_code_error' => 'invalidcode'], $myaccount_url));
        exit;
    }

    $matched_row = null;
    $latest_row = $rows[0]; // newest for attempt counting

    foreach ($rows as $candidate) {
        // Skip expired ones
        if ((int) $candidate->expires_at + NVACS_MAGIC_GRACE < time()) continue;
        // Skip rows at max attempts
        if ((int) $candidate->attempts >= NVACS_MAGIC_MAX_ATTEMPTS) continue;

        if (wp_check_password($code, $candidate->token_hash)) {
            $matched_row = $candidate;
            break;
        }
    }

    if (!$matched_row) {
        // Code didn't match any valid row - increment attempts on latest row
        $wpdb->update($table, ['attempts' => $latest_row->attempts + 1], ['id' => $latest_row->id], ['%d'], ['%d']);

        // Check if THIS attempt put it over the limit
        if ((int) $latest_row->attempts + 1 >= NVACS_MAGIC_MAX_ATTEMPTS) {
            $wpdb->update($table, ['used_at' => time()], ['id' => $latest_row->id], ['%d'], ['%d']);
            error_log('[NVACS Magic] verify: max attempts reached, burning code id=' . $latest_row->id);
            wp_safe_redirect(add_query_arg(['nv_code_sent' => '1', 'nv_code_error' => 'attempts'], $myaccount_url));
            exit;
        }

        // Determine whether the issue is expiry or wrong code
        // (latest row's status drives the error message)
        if ((int) $latest_row->expires_at + NVACS_MAGIC_GRACE < time()) {
            error_log('[NVACS Magic] verify: latest code expired. expires_at=' . $latest_row->expires_at . ' now=' . time());
            wp_safe_redirect(add_query_arg(['nv_code_sent' => '1', 'nv_code_error' => 'expired'], $myaccount_url));
            exit;
        }

        error_log('[NVACS Magic] verify: wrong code for email ' . $email);
        wp_safe_redirect(add_query_arg(['nv_code_sent' => '1', 'nv_code_error' => 'invalidcode'], $myaccount_url));
        exit;
    }

    error_log('[NVACS Magic] verify: SUCCESS - matched row id=' . $matched_row->id);

    // Mark this code AND all other unused codes for this email as used
    // (prevents future attempts to use codes from earlier sessions)
    $wpdb->query($wpdb->prepare(
        "UPDATE $table SET used_at = %d WHERE email = %s AND used_at IS NULL",
        time(),
        $email
    ));

    $user = get_user_by('id', $matched_row->user_id);
    if (!$user) {
        error_log('[NVACS Magic] verify: user not found for user_id ' . $matched_row->user_id);
        wp_safe_redirect(add_query_arg(['nv_code_sent' => '1', 'nv_code_error' => 'invalidcode'], $myaccount_url));
        exit;
    }

    nvacs_magic_clear_pending_email();

    wp_clear_auth_cookie();
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true);

    // Link any guest orders placed with this email to the account
    if (function_exists('nvacs_link_guest_orders_to_user')) {
        nvacs_link_guest_orders_to_user($user->ID);
    }

    error_log('[NVACS Magic] verify: SUCCESS - logged in user ' . $user->ID);

    wp_safe_redirect(wc_get_account_endpoint_url('orders'));
    exit;
}

/* ------------------------------------------------------------
 * Generate a 6-digit code, hash it, store it, email it
 * ------------------------------------------------------------ */
function nvacs_magic_create_and_send($user, $email) {
    global $wpdb;
    $table = $wpdb->prefix . 'nvacs_magic_links';

    // Generate 6-digit numeric code (cryptographically random)
    $code = '';
    for ($i = 0; $i < 6; $i++) {
        $code .= random_int(0, 9);
    }
    $code_hash = wp_hash_password($code);

    $now = time();
    $expires = $now + NVACS_MAGIC_TTL;
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : null;

    $insert_result = $wpdb->insert($table, [
        'email'      => $email,
        'token_hash' => $code_hash,
        'user_id'    => $user->ID,
        'created_at' => $now,
        'expires_at' => $expires,
        'attempts'   => 0,
        'ip'         => $ip,
    ], ['%s', '%s', '%d', '%d', '%d', '%d', '%s']);

    error_log('[NVACS Magic] create: insert result=' . var_export($insert_result, true) . ' last_error=' . $wpdb->last_error . ' insert_id=' . $wpdb->insert_id);

    // If debug mode is on, halt and dump info BEFORE sending email
    $debug_on = (get_option('nvacs_debug_verify') === '1');
    if ($debug_on && isset($_POST['nvacs_magic_action']) && $_POST['nvacs_magic_action'] === 'request') {
        header('Content-Type: text/plain');
        echo "=== CREATE DEBUG ===\n";
        echo "Email: $email\n";
        echo "User ID: {$user->ID}\n";
        echo "Generated code: $code\n";
        echo "Hash (first 30): " . substr($code_hash, 0, 30) . "...\n";
        echo "Now: $now\n";
        echo "Expires: $expires (in " . NVACS_MAGIC_TTL . "s)\n";
        echo "Insert result: " . var_export($insert_result, true) . "\n";
        echo "Insert ID: " . $wpdb->insert_id . "\n";
        echo "Last DB error: " . ($wpdb->last_error ?: '(none)') . "\n";
        echo "Table: $table\n";
        // Verify row actually exists
        $verify = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d", $wpdb->insert_id));
        echo "Row read back: " . ($verify ? 'YES' : 'NO') . "\n";
        if ($verify) {
            echo "  read back expires_at: " . $verify->expires_at . "\n";
            echo "  read back attempts: " . var_export($verify->attempts, true) . "\n";
        }
        exit;
    }

    // Build email
    $first_name = $user->first_name ?: $user->display_name ?: 'there';

    // Big code display with letter-spacing for readability
    $code_display = '<div style="font-family:\'Courier New\',Courier,monospace;font-size:38px;font-weight:bold;letter-spacing:0.4em;text-align:center;color:#1a1e1c;padding:24px 0;background:#f2f0eb;border:1px solid #d4d2cc;border-radius:12px;">'
                  . esc_html($code)
                  . '</div>';

    $body = nvacs_build_email_html([
        'pill_text'    => 'Sign-In Code',
        'pill_color'   => 'sage',
        'headline'     => 'Your sign-in %scode%s, ' . esc_html($first_name) . '.',
        'subheading'   => 'Enter this 6-digit code on the sign-in page to log in. The code expires in 30 minutes. If you didn\'t request this, you can safely ignore this email.',
        'content_html' => nvacs_email_text_block($code_display, 'Code'),
        'preheader'    => 'Your Natty Vision sign-in code: ' . $code,
    ]);

    $subject = 'Natty Vision sign-in code: ' . $code;
    nvacs_send_email($email, $subject, $body);
}

/* ------------------------------------------------------------
 * Rate limit: max 3 sends per email per 15min
 * ------------------------------------------------------------ */
function nvacs_magic_is_rate_limited($email) {
    global $wpdb;
    $table = $wpdb->prefix . 'nvacs_magic_links';
    $window_start = time() - NVACS_MAGIC_RATE_WINDOW;
    $count = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $table WHERE email = %s AND created_at > %d",
        $email,
        $window_start
    ));
    return ((int) $count) >= NVACS_MAGIC_RATE_MAX;
}

/* ------------------------------------------------------------
 * No-cache headers for the sign-in flow
 * ------------------------------------------------------------ */
add_action('template_redirect', function() {
    if (isset($_GET['nv_code_sent']) || isset($_GET['nv_code_error']) || !empty($_POST['nvacs_magic_action'])) {
        nocache_headers();
        if (!defined('DONOTCACHEPAGE')) define('DONOTCACHEPAGE', true);
    }
}, 1);

/* ============================================================
 * MAGIC CODE DIAGNOSTIC ENDPOINT
 *
 * Visit /wp-admin/?nvacs_magic_debug=1 as admin to see the
 * last 10 codes in the DB with timestamps + current server time.
 * Helps diagnose expiry / clock issues.
 * ============================================================ */
add_action('admin_init', function() {
    if (empty($_GET['nvacs_magic_debug'])) return;
    if (!current_user_can('manage_options')) return;

    // Allow toggling the verify-debug cookie + option from here
    if (isset($_GET['set_debug'])) {
        if ($_GET['set_debug'] === '1') {
            setcookie('nvacs_debug', '1', time() + 3600, '/');
            update_option('nvacs_debug_verify', '1');
            wp_safe_redirect(add_query_arg('nvacs_magic_debug', '1', admin_url()));
            exit;
        } else {
            setcookie('nvacs_debug', '', time() - 3600, '/');
            delete_option('nvacs_debug_verify');
            wp_safe_redirect(add_query_arg('nvacs_magic_debug', '1', admin_url()));
            exit;
        }
    }

    global $wpdb;
    $table = $wpdb->prefix . 'nvacs_magic_links';
    $rows = $wpdb->get_results("SELECT * FROM $table ORDER BY id DESC LIMIT 10", ARRAY_A);

    $now = time();
    $mysql_now = $wpdb->get_var("SELECT UNIX_TIMESTAMP()");

    header('Content-Type: text/html; charset=UTF-8');
    echo '<div style="font-family:monospace;padding:30px;background:#f6f6f6;max-width:1100px;margin:30px auto;border-radius:8px;">';
    echo '<h2>Magic Code Debug</h2>';
    echo '<p><strong>PHP time():</strong> ' . $now . ' (' . gmdate('Y-m-d H:i:s', $now) . ' UTC)</p>';
    echo '<p><strong>MySQL UNIX_TIMESTAMP():</strong> ' . $mysql_now . ' (' . gmdate('Y-m-d H:i:s', (int)$mysql_now) . ' UTC)</p>';
    echo '<p><strong>WP timezone_string:</strong> ' . esc_html(get_option('timezone_string') ?: '(empty - using gmt_offset)') . '</p>';
    echo '<p><strong>WP gmt_offset:</strong> ' . esc_html(get_option('gmt_offset')) . '</p>';
    echo '<p><strong>NVACS_MAGIC_TTL:</strong> ' . NVACS_MAGIC_TTL . ' seconds (' . (NVACS_MAGIC_TTL/60) . ' min)</p>';
    echo '<p><strong>NVACS_MAGIC_GRACE:</strong> ' . NVACS_MAGIC_GRACE . ' seconds</p>';

    // Debug status from cookie OR option
    $debug_on = (!empty($_COOKIE['nvacs_debug']) && $_COOKIE['nvacs_debug'] === '1')
             || get_option('nvacs_debug_verify') === '1';
    echo '<hr><div style="background:#fff8d6;padding:14px;border-radius:6px;border:1px solid #e5cb74;">';
    echo '<strong>Verify Debug Mode:</strong> ' . ($debug_on ? '<span style="color:green;">ON</span>' : '<span style="color:#888;">OFF</span>') . ' ';
    if ($debug_on) {
        echo '<a href="' . esc_url(add_query_arg(['nvacs_magic_debug' => '1', 'set_debug' => '0'], admin_url())) . '" style="background:#1a1e1c;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;margin-left:10px;">Turn OFF</a>';
        echo '<p style="margin:8px 0 0;font-size:13px;">When ON: instead of redirecting, the verify form will dump diagnostic info. Submit a code on /my-account/ to see what\'s happening.</p>';
    } else {
        echo '<a href="' . esc_url(add_query_arg(['nvacs_magic_debug' => '1', 'set_debug' => '1'], admin_url())) . '" style="background:#2d6a4f;color:#fff;padding:6px 12px;border-radius:4px;text-decoration:none;margin-left:10px;">Turn ON</a>';
        echo '<p style="margin:8px 0 0;font-size:13px;">Turn on, then go to /my-account/, request a code, and enter it. You\'ll see diagnostic output instead of a redirect.</p>';
    }
    echo '</div>';
    echo '<hr>';
    echo '<h3>Last 10 codes</h3>';
    if (empty($rows)) {
        echo '<p><em>No codes in DB.</em></p>';
    } else {
        echo '<table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;font-size:12px;">';
        echo '<tr><th>id</th><th>email</th><th>user_id</th><th>created_at</th><th>expires_at</th><th>used_at</th><th>attempts</th><th>diff (exp - now)</th><th>status</th></tr>';
        foreach ($rows as $r) {
            $diff = (int)$r['expires_at'] - $now;
            $status = 'unknown';
            if ($r['used_at']) $status = 'USED';
            elseif ($diff + NVACS_MAGIC_GRACE < 0) $status = 'EXPIRED';
            else $status = 'VALID (' . $diff . 's left)';
            echo '<tr>';
            echo '<td>' . $r['id'] . '</td>';
            echo '<td>' . esc_html($r['email']) . '</td>';
            echo '<td>' . $r['user_id'] . '</td>';
            echo '<td>' . $r['created_at'] . ' (' . gmdate('H:i:s', $r['created_at']) . ')</td>';
            echo '<td>' . $r['expires_at'] . ' (' . gmdate('H:i:s', $r['expires_at']) . ')</td>';
            echo '<td>' . ($r['used_at'] ?: '-') . '</td>';
            echo '<td>' . $r['attempts'] . '</td>';
            echo '<td style="font-weight:bold;color:' . ($diff > 0 ? 'green' : 'red') . ';">' . $diff . 's</td>';
            echo '<td><strong>' . $status . '</strong></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    echo '</div>';
    exit;
});

/* ============================================================
 * CUSTOM-CODE AFFILIATE SIGNUP PAGE
 *
 * Hidden page at /affiliates-custom for affiliates who want a
 * short, custom coupon code (e.g. "CS", "KO") that wouldn't
 * fit as a WP username (WP requires usernames ≥4 chars).
 *
 * Differences from the regular /affiliate-signup:
 *   - Separate username and coupon code fields (decoupled)
 *   - Commission rate set to 0% (dashboard-only tracking)
 *   - Coupon discount: 10% fixed
 *   - Page is not linked anywhere — direct URL access only
 *
 * Existing /affiliates, /affiliate-signup, /affiliate-login
 * flows are NOT modified.
 * ============================================================ */

/* ------------------------------------------------------------
 * Register rewrite rule for /affiliates-custom
 * ------------------------------------------------------------ */
add_action('init', 'nvacs_custom_affiliate_rewrite', 11);
function nvacs_custom_affiliate_rewrite() {
    add_rewrite_rule('^affiliates-custom/?$', 'index.php?nvacs_custom_affiliate=1', 'top');

    // Auto-flush rules when this version-marker changes
    if (get_option('nvacs_custom_affiliate_rules') !== '1') {
        flush_rewrite_rules(false);
        update_option('nvacs_custom_affiliate_rules', '1');
    }
}
add_filter('query_vars', function($vars) {
    $vars[] = 'nvacs_custom_affiliate';
    return $vars;
});

/* ------------------------------------------------------------
 * Render the page — full standalone template
 * ------------------------------------------------------------ */
add_action('template_redirect', 'nvacs_custom_affiliate_render');
function nvacs_custom_affiliate_render() {
    if (!get_query_var('nvacs_custom_affiliate')) return;

    // Handle form submission first (before output)
    if (!empty($_POST['nvacs_custom_signup'])) {
        nvacs_custom_affiliate_handle_signup();
        // (handler exits on success/error redirect)
    }

    $error_code = isset($_GET['err']) ? sanitize_key($_GET['err']) : '';
    $error_msg = '';
    switch ($error_code) {
        case 'fields':        $error_msg = 'Please fill in all fields.'; break;
        case 'email':         $error_msg = 'Please enter a valid email address.'; break;
        case 'username_short': $error_msg = 'Username must be at least 4 characters.'; break;
        case 'username_taken': $error_msg = 'That username is already taken. Please choose another.'; break;
        case 'username_invalid': $error_msg = 'Username can only contain letters, numbers, dashes, and underscores.'; break;
        case 'email_taken':   $error_msg = 'An account already exists with that email. Please use a different email.'; break;
        case 'code_short':    $error_msg = 'Coupon code must be at least 2 characters.'; break;
        case 'code_invalid':  $error_msg = 'Coupon code can only contain uppercase letters and numbers.'; break;
        case 'code_taken':    $error_msg = 'That coupon code is already in use. Please choose another.'; break;
        case 'password_short': $error_msg = 'Password must be at least 8 characters.'; break;
        case 'create_failed': $error_msg = 'Could not create account. Please try again or contact support.'; break;
    }

    // Retain values on error redirect
    $val_name = isset($_GET['name']) ? sanitize_text_field(wp_unslash($_GET['name'])) : '';
    $val_email = isset($_GET['email']) ? sanitize_email(wp_unslash($_GET['email'])) : '';
    $val_username = isset($_GET['username']) ? sanitize_user(wp_unslash($_GET['username'])) : '';
    $val_code = isset($_GET['code']) ? strtoupper(preg_replace('/[^A-Z0-9]/i', '', wp_unslash($_GET['code']))) : '';

    $logo_svg = function_exists('nvacs_logo_svg') ? nvacs_logo_svg(28, '#1a1e1c') : '';

    // Render full page
    ?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Custom Affiliate Signup — Natty Vision</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Neue Montreal', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    background: #f2f0eb;
    color: #1a1e1c;
    min-height: 100vh;
    line-height: 1.5;
}
.nv-cs-header {
    padding: 28px 40px;
    border-bottom: 1px solid #d4d2cc;
    background: #f2f0eb;
}
.nv-cs-wrap {
    max-width: 560px;
    margin: 0 auto;
    padding: 60px 24px 80px;
}
.nv-cs-eyebrow {
    font-family: 'DM Mono', 'Courier New', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    color: #2d6a4f;
    margin-bottom: 16px;
}
.nv-cs-h1 {
    font-family: 'Instrument Serif', Georgia, serif;
    font-weight: 400;
    font-size: 48px;
    line-height: 1.05;
    letter-spacing: -0.02em;
    margin-bottom: 14px;
}
.nv-cs-h1 em { font-style: italic; color: #2d6a4f; }
.nv-cs-sub {
    font-size: 15px;
    color: #4a4f4c;
    line-height: 1.6;
    margin-bottom: 40px;
    max-width: 460px;
}
.nv-cs-card {
    background: #e9e7e1;
    border: 1px solid #d4d2cc;
    border-radius: 18px;
    padding: 40px;
    box-shadow: 0 8px 30px rgba(26,30,28,0.04);
}
.nv-cs-error {
    background: #faf0d6;
    border: 1px solid #e5cb74;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 24px;
    font-size: 13px;
    color: #6b5414;
    line-height: 1.5;
}
.nv-cs-field { margin-bottom: 22px; }
.nv-cs-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 22px;
}
.nv-cs-row .nv-cs-field { margin-bottom: 0; }
.nv-cs-label {
    display: block;
    font-family: 'DM Mono', 'Courier New', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #4a4f4c;
    margin-bottom: 8px;
}
.nv-cs-hint {
    font-family: 'DM Mono', 'Courier New', monospace;
    font-size: 10px;
    color: #7a7f7c;
    margin-top: 6px;
    letter-spacing: 0.05em;
    text-transform: none;
}
.nv-cs-input {
    background: #f2f0eb;
    border: 1px solid #d4d2cc;
    border-radius: 8px;
    padding: 14px 16px;
    font-family: 'DM Mono', 'Courier New', monospace;
    font-size: 14px;
    color: #1a1e1c;
    width: 100%;
    transition: all 0.3s ease;
}
.nv-cs-input:focus {
    outline: none;
    border-color: #2d6a4f;
    box-shadow: 0 0 0 3px rgba(45,106,79,0.12);
}
.nv-cs-input-code {
    text-transform: uppercase;
    letter-spacing: 0.15em;
    font-weight: 500;
    font-size: 16px;
}
.nv-cs-button {
    background: #1a1e1c;
    color: #f2f0eb;
    border: none;
    border-radius: 8px;
    padding: 16px 28px;
    font-family: 'DM Mono', 'Courier New', monospace;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 500;
    cursor: pointer;
    width: 100%;
    margin-top: 8px;
    transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
}
.nv-cs-button:hover {
    background: #2a302d;
    transform: translateY(-1px);
    box-shadow: 0 12px 30px rgba(26,30,28,0.15);
}
.nv-cs-info-box {
    background: #dce5d8;
    border-radius: 10px;
    padding: 16px 18px;
    margin-bottom: 28px;
    font-size: 13px;
    color: #2d4a3a;
    line-height: 1.5;
}
.nv-cs-info-box strong { font-weight: 500; }
.nv-cs-footer {
    text-align: center;
    margin-top: 28px;
    font-size: 13px;
    color: #7a7f7c;
}
.nv-cs-footer a {
    color: #2d6a4f;
    text-decoration: none;
    font-weight: 500;
}
@media (max-width: 600px) {
    .nv-cs-h1 { font-size: 36px; }
    .nv-cs-card { padding: 28px 22px; }
    .nv-cs-row { grid-template-columns: 1fr; gap: 22px; margin-bottom: 22px; }
    .nv-cs-wrap { padding: 40px 16px 60px; }
}
</style>
</head>
<body>
<header class="nv-cs-header">
    <a href="<?php echo esc_url(home_url('/')); ?>" style="display:inline-block;text-decoration:none;">
        <?php echo $logo_svg; ?>
    </a>
</header>

<div class="nv-cs-wrap">
    <div class="nv-cs-eyebrow">Custom Affiliate Signup</div>
    <h1 class="nv-cs-h1">Create your <em>affiliate</em> account.</h1>
    <p class="nv-cs-sub">Choose your own username and coupon code. Your coupon will give customers 10% off and track all sales to your dashboard.</p>

    <div class="nv-cs-card">
        <?php if ($error_msg): ?>
            <div class="nv-cs-error"><?php echo esc_html($error_msg); ?></div>
        <?php endif; ?>

        <div class="nv-cs-info-box">
            <strong>Note:</strong> Your username must be at least 4 characters, but your coupon code can be as short as 2 (e.g. "CS"). They don't need to match.
        </div>

        <form method="post" action="<?php echo esc_url(home_url('/affiliates-custom/')); ?>">
            <?php wp_nonce_field('nvacs_custom_signup', 'nvacs_custom_nonce'); ?>
            <input type="hidden" name="nvacs_custom_signup" value="1">

            <div class="nv-cs-field">
                <label class="nv-cs-label" for="cs_name">Full Name</label>
                <input type="text" id="cs_name" name="cs_name" class="nv-cs-input" required value="<?php echo esc_attr($val_name); ?>" placeholder="Jane Smith">
            </div>

            <div class="nv-cs-field">
                <label class="nv-cs-label" for="cs_email">Email Address</label>
                <input type="email" id="cs_email" name="cs_email" class="nv-cs-input" required value="<?php echo esc_attr($val_email); ?>" placeholder="you@example.com" autocomplete="email">
            </div>

            <div class="nv-cs-row">
                <div class="nv-cs-field">
                    <label class="nv-cs-label" for="cs_username">Username</label>
                    <input type="text" id="cs_username" name="cs_username" class="nv-cs-input" required minlength="4" value="<?php echo esc_attr($val_username); ?>" placeholder="janesmith" autocomplete="username">
                    <div class="nv-cs-hint">Min 4 chars. Used to log in.</div>
                </div>

                <div class="nv-cs-field">
                    <label class="nv-cs-label" for="cs_code">Coupon Code</label>
                    <input type="text" id="cs_code" name="cs_code" class="nv-cs-input nv-cs-input-code" required minlength="2" maxlength="20" value="<?php echo esc_attr($val_code); ?>" placeholder="CS" pattern="[A-Za-z0-9]+" style="text-transform:uppercase;">
                    <div class="nv-cs-hint">Min 2 chars. Letters/numbers only.</div>
                </div>
            </div>

            <div class="nv-cs-field">
                <label class="nv-cs-label" for="cs_password">Password</label>
                <input type="password" id="cs_password" name="cs_password" class="nv-cs-input" required minlength="8" placeholder="At least 8 characters" autocomplete="new-password">
                <div class="nv-cs-hint">Min 8 chars. Used to log in at /affiliate-login/.</div>
            </div>

            <button type="submit" class="nv-cs-button">Create Affiliate Account →</button>
        </form>

        <p class="nv-cs-footer">
            Already have an account? <a href="<?php echo esc_url(home_url('/affiliate-login/')); ?>">Sign in →</a>
        </p>
    </div>
</div>

<script>
// Auto-uppercase the coupon code field as the user types
(function() {
    var codeField = document.getElementById('cs_code');
    if (codeField) {
        codeField.addEventListener('input', function() {
            var pos = this.selectionStart;
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            this.setSelectionRange(pos, pos);
        });
    }
})();
</script>
</body>
</html><?php
    exit;
}

/* ------------------------------------------------------------
 * Handle signup form submission
 * ------------------------------------------------------------ */
function nvacs_custom_affiliate_handle_signup() {
    // Nonce check
    if (!isset($_POST['nvacs_custom_nonce']) || !wp_verify_nonce($_POST['nvacs_custom_nonce'], 'nvacs_custom_signup')) {
        return; // silently fall through to form display
    }

    $page_url = home_url('/affiliates-custom/');

    // Gather + sanitize
    $name     = isset($_POST['cs_name']) ? sanitize_text_field(wp_unslash($_POST['cs_name'])) : '';
    $email    = isset($_POST['cs_email']) ? sanitize_email(wp_unslash($_POST['cs_email'])) : '';
    $username = isset($_POST['cs_username']) ? sanitize_user(wp_unslash($_POST['cs_username']), true) : '';
    $code_raw = isset($_POST['cs_code']) ? wp_unslash($_POST['cs_code']) : '';
    $code     = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code_raw));
    $password = isset($_POST['cs_password']) ? $_POST['cs_password'] : ''; // raw, no sanitize on password

    // Helper for error redirects (retains form values)
    $back_with_error = function($err) use ($page_url, $name, $email, $username, $code) {
        $url = add_query_arg([
            'err'      => $err,
            'name'     => rawurlencode($name),
            'email'    => rawurlencode($email),
            'username' => rawurlencode($username),
            'code'     => rawurlencode($code),
        ], $page_url);
        wp_safe_redirect($url);
        exit;
    };

    // Validation
    if (empty($name) || empty($email) || empty($username) || empty($code) || empty($password)) {
        $back_with_error('fields');
    }
    if (!is_email($email)) {
        $back_with_error('email');
    }
    if (strlen($username) < 4) {
        $back_with_error('username_short');
    }
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
        $back_with_error('username_invalid');
    }
    if (username_exists($username)) {
        $back_with_error('username_taken');
    }
    if (email_exists($email)) {
        $back_with_error('email_taken');
    }
    if (strlen($code) < 2) {
        $back_with_error('code_short');
    }
    if (!preg_match('/^[A-Z0-9]+$/', $code)) {
        $back_with_error('code_invalid');
    }
    if (strlen($password) < 8) {
        $back_with_error('password_short');
    }

    // Check coupon code uniqueness against WooCommerce coupons
    if (function_exists('wc_get_coupon_id_by_code')) {
        $existing_coupon_id = wc_get_coupon_id_by_code($code);
        if ($existing_coupon_id) {
            $back_with_error('code_taken');
        }
    }

    // Create the WP user
    $user_id = wp_create_user($username, $password, $email);
    if (is_wp_error($user_id)) {
        error_log('[NVACS Custom] wp_create_user failed: ' . $user_id->get_error_message());
        $back_with_error('create_failed');
    }

    // Set their name
    $name_parts = explode(' ', $name, 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';

    wp_update_user([
        'ID'           => $user_id,
        'first_name'   => $first_name,
        'last_name'    => $last_name,
        'display_name' => $name,
    ]);

    // Set role to affiliate (AffiliateWP creates 'affiliate' role) + customer
    $new_user = new WP_User($user_id);
    $new_user->set_role('affiliate');
    $new_user->add_role('customer');

    // Create the AffiliateWP affiliate record (auto-approved, 0% commission)
    if (!function_exists('affwp_add_affiliate')) {
        error_log('[NVACS Custom] AffiliateWP function affwp_add_affiliate not available');
        $back_with_error('create_failed');
    }

    $affiliate_id = affwp_add_affiliate([
        'user_id'    => $user_id,
        'status'     => 'active',
        'rate'       => 0,           // 0% commission per your spec
        'rate_type'  => 'percentage',
    ]);

    if (!$affiliate_id) {
        error_log('[NVACS Custom] affwp_add_affiliate failed for user ' . $user_id);
        $back_with_error('create_failed');
    }

    // Create the WooCommerce coupon with their chosen code, 10% off
    $coupon = new WC_Coupon();
    $coupon->set_code($code);
    $coupon->set_discount_type('percent');
    $coupon->set_amount(10);
    $coupon->set_individual_use(false);
    $coupon->set_usage_limit(0);
    $coupon->set_usage_limit_per_user(0);
    $coupon->set_description('Custom affiliate coupon for ' . $name . ' (user_id ' . $user_id . ')');
    $coupon_id = $coupon->save();

    if (!$coupon_id) {
        error_log('[NVACS Custom] WC coupon creation failed for code ' . $code);
        // Affiliate is created but coupon isn't — don't bail, just log. Admin can fix manually.
    } else {
        // Store the affiliate_id on the coupon so AffiliateWP recognizes it
        update_post_meta($coupon_id, 'affwp_discount_affiliate', $affiliate_id);
        update_post_meta($coupon_id, '_nvacs_custom_affiliate', '1');
    }

    // Auto-login the new affiliate
    wp_clear_auth_cookie();
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);

    // Redirect to affiliate dashboard
    $dashboard_url = home_url('/affiliate-area/');
    wp_safe_redirect($dashboard_url);
    exit;
}

/* ============================================================
 * SHIPPED / TRACKING EMAIL
 *
 * Kentro (our fulfillment) adds an order note containing tracking
 * info, THEN marks the order Completed. We detect that note, parse
 * out the carrier + tracking number + items + ship date, and fire
 * a branded "Your order has shipped" email with a tracking button.
 *
 * Expected note format (from Kentro):
 *   "Items shipped: Retatrutide x 1 shipped via FedEx on May 21,
 *    2026 with tracking number 872102360754."
 *
 * Guard meta _nvacs_shipped_email_sent ensures one send per order.
 * ============================================================ */

add_action('woocommerce_order_note_added', 'nvacs_detect_tracking_note', 10, 2);
function nvacs_detect_tracking_note($note_id, $order) {
    if (!$order instanceof WC_Order) {
        // Older WC may pass order_id; normalize
        $order = wc_get_order($order);
    }
    if (!$order) return;

    // Get the note content
    $note = wc_get_order_note($note_id);
    if (!$note || empty($note->content)) return;
    $content = $note->content;

    // Does this note look like a Kentro tracking note?
    // Must contain "tracking number" and a carrier hint
    if (stripos($content, 'tracking number') === false) return;

    // Parse the tracking info
    $parsed = nvacs_parse_tracking_note($content);
    if (empty($parsed['tracking_number'])) return; // couldn't parse a number — bail

    // Guard: only send once per order
    if ($order->get_meta('_nvacs_shipped_email_sent') === 'yes') return;

    // Send the email
    $sent = nvacs_send_shipped_email($order, $parsed);

    // Always store tracking data (even if email send had an issue) so the
    // customer order view can display it.
    $order->update_meta_data('_nvacs_tracking_number', $parsed['tracking_number']);
    $order->update_meta_data('_nvacs_tracking_carrier', $parsed['carrier']);
    $order->update_meta_data('_nvacs_tracking_shipdate', $parsed['ship_date']);
    $order->update_meta_data('_nvacs_tracking_items', wp_json_encode($parsed['items_list']));

    if ($sent) {
        $order->update_meta_data('_nvacs_shipped_email_sent', 'yes');
        error_log('[NVACS Shipped] Sent shipped email for order ' . $order->get_id() . ' tracking ' . $parsed['tracking_number']);
    }
    $order->save();
}

/* ------------------------------------------------------------
 * Parse a Kentro tracking note into structured data
 *
 * Returns: ['items'=>, 'carrier'=>, 'ship_date'=>, 'tracking_number'=>]
 * ------------------------------------------------------------ */
function nvacs_parse_tracking_note($content) {
    $result = [
        'items'           => '',
        'items_list'      => [],
        'carrier'         => '',
        'ship_date'       => '',
        'tracking_number' => '',
    ];

    // Normalize whitespace (Kentro sometimes has double spaces)
    $text = preg_replace('/\s+/', ' ', trim($content));

    // Tracking number: "tracking number 872102360754" — grab the digits/alphanumerics after it
    if (preg_match('/tracking number[:\s]+([A-Z0-9]+)/i', $text, $m)) {
        $result['tracking_number'] = trim($m[1]);
    }

    // Carrier: "via FedEx on" — grab the word(s) between "via" and "on"
    if (preg_match('/via\s+([A-Za-z0-9 ]+?)\s+on\s/i', $text, $m)) {
        $result['carrier'] = trim($m[1]);
    }

    // Ship date: "on May 21, 2026 with" — grab between "on" and "with"
    if (preg_match('/\son\s+(.+?)\s+with\s+tracking/i', $text, $m)) {
        $result['ship_date'] = trim($m[1]);
    }

    // Items: "Items shipped: Retatrutide x 1 shipped via" — grab between "Items shipped:" and the final "shipped via"
    if (preg_match('/Items shipped:\s*(.+?)\s+shipped\s+via/i', $text, $m)) {
        $result['items'] = trim($m[1]);
        // Split multiple items — Kentro separates each with " shipped "
        // e.g. "BPC-157 x 1 shipped GHK-Cu x 1 shipped Glow Blend x 2"
        $parts = preg_split('/\s+shipped\s+/i', $result['items']);
        $result['items_list'] = array_values(array_filter(array_map('trim', $parts), function($v) {
            return $v !== '';
        }));
    } else {
        $result['items_list'] = [];
    }

    return $result;
}

/* ------------------------------------------------------------
 * Build a carrier tracking URL from carrier name + number
 * ------------------------------------------------------------ */
function nvacs_build_tracking_url($carrier, $number) {
    $c = strtolower($carrier);
    $n = rawurlencode($number);

    if (strpos($c, 'fedex') !== false) {
        return 'https://www.fedex.com/fedextrack/?trknbr=' . $n;
    }
    if (strpos($c, 'usps') !== false) {
        return 'https://tools.usps.com/go/TrackConfirmAction?tLabels=' . $n;
    }
    if (strpos($c, 'ups') !== false) {
        return 'https://www.ups.com/track?tracknum=' . $n;
    }
    if (strpos($c, 'dhl') !== false) {
        return 'https://www.dhl.com/en/express/tracking.html?AWB=' . $n;
    }
    // Fallback: Google the tracking number
    return 'https://www.google.com/search?q=' . rawurlencode($carrier . ' ' . $number);
}

/* ------------------------------------------------------------
 * Send the branded "Your order has shipped" email
 * ------------------------------------------------------------ */
function nvacs_send_shipped_email($order, $parsed) {
    $to = $order->get_billing_email();
    if (!$to) return false;

    $first_name = $order->get_billing_first_name() ?: 'there';
    $carrier    = $parsed['carrier'] ?: 'the carrier';
    $tracking   = $parsed['tracking_number'];
    $ship_date  = $parsed['ship_date'];
    $items      = $parsed['items'];
    $track_url  = nvacs_build_tracking_url($carrier, $tracking);

    // Build the tracking detail card
    $c = nvacs_email_colors();

    // Render items — one per line if we have a parsed list
    $items_display = '';
    if (!empty($parsed['items_list']) && count($parsed['items_list']) > 0) {
        $lines = [];
        foreach ($parsed['items_list'] as $it) {
            $lines[] = esc_html($it);
        }
        $items_display = implode('<br>', $lines);
    } elseif (!empty($parsed['items'])) {
        $items_display = esc_html($parsed['items']);
    }

    $detail_html = '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">';
    if ($items_display) {
        $detail_html .= '<tr>'
            . '<td style="padding:6px 0;font-family:\'Courier New\',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:' . $c['text3'] . ';width:42%;vertical-align:top;">Items</td>'
            . '<td style="padding:6px 0;font-family:Georgia,serif;font-size:15px;color:' . $c['text'] . ';line-height:1.7;">' . $items_display . '</td>'
            . '</tr>';
    }
    $detail_html .= '<tr>'
        . '<td style="padding:6px 0;font-family:\'Courier New\',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:' . $c['text3'] . ';">Carrier</td>'
        . '<td style="padding:6px 0;font-family:Georgia,serif;font-size:15px;color:' . $c['text'] . ';">' . esc_html($carrier) . '</td>'
        . '</tr>';
    if ($ship_date) {
        $detail_html .= '<tr>'
            . '<td style="padding:6px 0;font-family:\'Courier New\',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:' . $c['text3'] . ';">Shipped</td>'
            . '<td style="padding:6px 0;font-family:Georgia,serif;font-size:15px;color:' . $c['text'] . ';">' . esc_html($ship_date) . '</td>'
            . '</tr>';
    }
    $detail_html .= '<tr>'
        . '<td style="padding:6px 0;font-family:\'Courier New\',Courier,monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:' . $c['text3'] . ';">Tracking #</td>'
        . '<td style="padding:6px 0;font-family:\'Courier New\',Courier,monospace;font-size:15px;font-weight:bold;color:' . $c['green'] . ';letter-spacing:0.04em;">' . esc_html($tracking) . '</td>'
        . '</tr>';
    $detail_html .= '</table>';

    $content_html = nvacs_email_text_block($detail_html, 'Shipment Details')
                  . nvacs_email_button($track_url, 'Track Your Package');

    $body = nvacs_build_email_html([
        'pill_text'    => 'Shipped',
        'pill_color'   => 'sage',
        'headline'     => 'Your order is on its %sway%s, ' . esc_html($first_name) . '.',
        'subheading'   => 'Good news — your order has shipped via ' . esc_html($carrier) . '. Use the tracking number below to follow its journey.',
        'content_html' => $content_html,
        'preheader'    => 'Your Natty Vision order has shipped — tracking ' . $tracking,
    ]);

    $subject = 'Your Natty Vision order has shipped 📦';

    return nvacs_send_email($to, $subject, $body);
}

/* ------------------------------------------------------------
 * Manual resend option in Order Actions dropdown
 * ------------------------------------------------------------ */
add_filter('woocommerce_order_actions', function($actions) {
    $actions['nvacs_resend_shipped'] = '★ Resend branded Shipped email';
    return $actions;
});
add_action('woocommerce_order_action_nvacs_resend_shipped', function($order) {
    // Try to reconstruct tracking from saved meta, else re-scan notes
    $tracking = $order->get_meta('_nvacs_tracking_number');
    $carrier  = $order->get_meta('_nvacs_tracking_carrier');

    $parsed = ['items' => '', 'carrier' => $carrier, 'ship_date' => '', 'tracking_number' => $tracking];

    // If no saved meta, scan the order notes for a tracking note
    if (empty($tracking)) {
        $notes = wc_get_order_notes(['order_id' => $order->get_id(), 'limit' => 50]);
        foreach ($notes as $note) {
            if (stripos($note->content, 'tracking number') !== false) {
                $parsed = nvacs_parse_tracking_note($note->content);
                break;
            }
        }
    }

    if (empty($parsed['tracking_number'])) {
        $order->add_order_note('Could not resend Shipped email — no tracking number found in notes.');
        return;
    }

    $sent = nvacs_send_shipped_email($order, $parsed);
    if ($sent) {
        $order->update_meta_data('_nvacs_shipped_email_sent', 'yes');
        $order->save();
        $order->add_order_note('★ Branded Shipped email manually resent.');
    }
});

/* ------------------------------------------------------------
 * Preview endpoint: /wp-admin/?nvacs_preview_email=shipped
 * (extends the existing preview system)
 * ------------------------------------------------------------ */
add_action('admin_init', function() {
    if (empty($_GET['nvacs_preview_email']) || $_GET['nvacs_preview_email'] !== 'shipped') return;
    if (!current_user_can('manage_options')) return;

    $sample = [
        'items'           => 'BPC-157 x 1, GHK-Cu x 1, Glow Blend x 2',
        'items_list'      => ['BPC-157 x 1', 'GHK-Cu x 1', 'Glow Blend x 2', 'NAD+ x 3', 'Semax x 1'],
        'carrier'         => 'FedEx',
        'ship_date'       => 'May 21, 2026',
        'tracking_number' => '872102360754',
    ];

    // Build a fake preview using the same builder
    $c = nvacs_email_colors();
    $items_display = implode('<br>', array_map('esc_html', $sample['items_list']));
    $detail_html = '<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">'
        . '<tr><td style="padding:6px 0;font-family:\'Courier New\',monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:' . $c['text3'] . ';width:42%;vertical-align:top;">Items</td><td style="padding:6px 0;font-family:Georgia,serif;font-size:15px;color:' . $c['text'] . ';line-height:1.7;">' . $items_display . '</td></tr>'
        . '<tr><td style="padding:6px 0;font-family:\'Courier New\',monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:' . $c['text3'] . ';">Carrier</td><td style="padding:6px 0;font-family:Georgia,serif;font-size:15px;color:' . $c['text'] . ';">' . esc_html($sample['carrier']) . '</td></tr>'
        . '<tr><td style="padding:6px 0;font-family:\'Courier New\',monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:' . $c['text3'] . ';">Shipped</td><td style="padding:6px 0;font-family:Georgia,serif;font-size:15px;color:' . $c['text'] . ';">' . esc_html($sample['ship_date']) . '</td></tr>'
        . '<tr><td style="padding:6px 0;font-family:\'Courier New\',monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.08em;color:' . $c['text3'] . ';">Tracking #</td><td style="padding:6px 0;font-family:\'Courier New\',monospace;font-size:15px;font-weight:bold;color:' . $c['green'] . ';letter-spacing:0.04em;">' . esc_html($sample['tracking_number']) . '</td></tr>'
        . '</table>';

    $track_url = nvacs_build_tracking_url($sample['carrier'], $sample['tracking_number']);
    $content_html = nvacs_email_text_block($detail_html, 'Shipment Details')
                  . nvacs_email_button($track_url, 'Track Your Package');

    $body = nvacs_build_email_html([
        'pill_text'    => 'Shipped',
        'pill_color'   => 'sage',
        'headline'     => 'Your order is on its %sway%s, Jane.',
        'subheading'   => 'Good news — your order has shipped via ' . esc_html($sample['carrier']) . '. Use the tracking number below to follow its journey.',
        'content_html' => $content_html,
        'preheader'    => 'Your Natty Vision order has shipped',
    ]);

    header('Content-Type: text/html; charset=UTF-8');
    echo $body;
    exit;
});

/* ============================================================
 * GUEST ORDER LINKING
 *
 * Problem: When a customer checks out as a guest, the order is
 * saved with their email but no customer_id (user account link).
 * Later, when they log in (or an account is auto-created via the
 * sign-in code flow), WooCommerce's "My Orders" only shows orders
 * where customer_id matches — so their old guest orders are
 * invisible.
 *
 * Solution: Whenever a user logs in or an account is created,
 * find all orders matching their email that have no customer_id
 * (or customer_id = 0) and link them to the user account.
 *
 * Also provides a one-time admin backfill for all existing orders.
 * ============================================================ */

/* ------------------------------------------------------------
 * Link guest orders to a user by email
 * Returns number of orders linked.
 * ------------------------------------------------------------ */
function nvacs_link_guest_orders_to_user($user_id) {
    if (!$user_id) return 0;
    $user = get_userdata($user_id);
    if (!$user || empty($user->user_email)) return 0;

    $email = $user->user_email;

    if (!function_exists('wc_get_orders')) return 0;

    // Find all orders with this billing email that aren't linked to a user
    $orders = wc_get_orders([
        'billing_email' => $email,
        'limit'         => -1,
        'customer_id'   => 0, // only unlinked/guest orders
    ]);

    $linked = 0;
    foreach ($orders as $order) {
        // Double-check it's actually unlinked
        if ($order->get_customer_id() === 0 || $order->get_customer_id() === null) {
            $order->set_customer_id($user_id);
            $order->save();
            $linked++;
        }
    }

    if ($linked > 0) {
        error_log('[NVACS OrderLink] Linked ' . $linked . ' guest order(s) to user ' . $user_id . ' (' . $email . ')');
    }

    return $linked;
}

/* ------------------------------------------------------------
 * Hook: on user login, link any guest orders
 * ------------------------------------------------------------ */
add_action('wp_login', 'nvacs_link_orders_on_login', 10, 2);
function nvacs_link_orders_on_login($user_login, $user) {
    if ($user instanceof WP_User) {
        nvacs_link_guest_orders_to_user($user->ID);
    }
}

/* ------------------------------------------------------------
 * Hook: also link when our sign-in code logs someone in
 * (wp_login doesn't always fire for programmatic logins, so we
 * call the linker directly in the verify handler too — but this
 * is a belt-and-suspenders catch via set_auth_cookie)
 * ------------------------------------------------------------ */
add_action('set_logged_in_cookie', function($logged_in_cookie, $expire, $expiration, $user_id) {
    if ($user_id) {
        nvacs_link_guest_orders_to_user($user_id);
    }
}, 10, 4);

/* ------------------------------------------------------------
 * Hook: when a new customer account is created, link guest orders
 * ------------------------------------------------------------ */
add_action('user_register', 'nvacs_link_orders_on_register', 20, 1);
function nvacs_link_orders_on_register($user_id) {
    nvacs_link_guest_orders_to_user($user_id);
}

/* ------------------------------------------------------------
 * Hook: when an order is created/processed, link it if a user
 * account exists with that email (covers future guest checkouts)
 * ------------------------------------------------------------ */
add_action('woocommerce_checkout_order_processed', 'nvacs_link_order_on_checkout', 20, 1);
add_action('woocommerce_store_api_checkout_order_processed', 'nvacs_link_order_on_checkout', 20, 1);
function nvacs_link_order_on_checkout($order_id) {
    $order = wc_get_order($order_id);
    if (!$order) return;

    // Already linked?
    if ($order->get_customer_id()) return;

    $email = $order->get_billing_email();
    if (!$email) return;

    // Does a user exist with this email?
    $user = get_user_by('email', $email);
    if ($user) {
        $order->set_customer_id($user->ID);
        $order->save();
        error_log('[NVACS OrderLink] Linked new checkout order ' . $order_id . ' to existing user ' . $user->ID);
    }
}

/* ------------------------------------------------------------
 * One-time admin backfill: link ALL existing guest orders to
 * matching user accounts.
 *
 * Visit /wp-admin/?nvacs_backfill_orders=1 as admin.
 * ------------------------------------------------------------ */
add_action('admin_init', function() {
    if (empty($_GET['nvacs_backfill_orders'])) return;
    if (!current_user_can('manage_options')) return;

    if (!function_exists('wc_get_orders')) {
        wp_die('WooCommerce not available.');
    }

    // Get all unlinked orders
    $orders = wc_get_orders([
        'limit'       => -1,
        'customer_id' => 0,
    ]);

    $linked = 0;
    $skipped = 0;
    $details = [];

    foreach ($orders as $order) {
        $email = $order->get_billing_email();
        if (!$email) { $skipped++; continue; }

        $user = get_user_by('email', $email);
        if ($user) {
            $order->set_customer_id($user->ID);
            $order->save();
            $linked++;
            $details[] = 'Order #' . $order->get_id() . ' → user ' . $user->ID . ' (' . $email . ')';
        } else {
            $skipped++;
        }
    }

    header('Content-Type: text/html; charset=UTF-8');
    echo '<div style="font-family:monospace;padding:30px;max-width:900px;margin:30px auto;background:#f6f6f6;border-radius:8px;">';
    echo '<h2>Guest Order Backfill Complete</h2>';
    echo '<p><strong>Linked:</strong> ' . $linked . ' orders</p>';
    echo '<p><strong>Skipped:</strong> ' . $skipped . ' orders (no matching account or no email)</p>';
    if (!empty($details)) {
        echo '<hr><h3>Linked orders:</h3><ul>';
        foreach ($details as $d) echo '<li>' . esc_html($d) . '</li>';
        echo '</ul>';
    }
    echo '<p style="margin-top:20px;color:#666;">Skipped orders are guest checkouts where no account exists with that email yet. They\'ll auto-link when that person creates an account or signs in.</p>';
    echo '</div>';
    exit;
});

/* ============================================================
 * ADMIN: VIEW AS CUSTOMER (impersonation)
 *
 * Lets an admin (manage_options) view the site as a specific
 * customer — to debug "I can't see my orders" type issues.
 *
 * - Completely invisible to the customer (no notification)
 * - Admin-only, gated by capability + signed nonce
 * - Stores the original admin ID so we can switch back
 * - Shows a floating "switch back" bar ONLY to the impersonating
 *   admin (rendered based on session state, customer never sees it)
 *
 * Security:
 *   - Only manage_options users can initiate
 *   - Switch action protected by nonce
 *   - Original admin ID stored in a signed cookie
 * ============================================================ */

define('NVACS_IMPERSONATE_COOKIE', 'nvacs_impersonate_origin');

/* ------------------------------------------------------------
 * Add "View as customer" link to Users list row actions
 * ------------------------------------------------------------ */
add_filter('user_row_actions', 'nvacs_add_impersonate_link', 10, 2);
function nvacs_add_impersonate_link($actions, $user) {
    if (!current_user_can('manage_options')) return $actions;
    if ($user->ID === get_current_user_id()) return $actions; // can't impersonate self

    $url = wp_nonce_url(
        add_query_arg([
            'nvacs_impersonate' => $user->ID,
        ], admin_url()),
        'nvacs_impersonate_' . $user->ID
    );

    $actions['nvacs_impersonate'] = '<a href="' . esc_url($url) . '" style="color:#2d6a4f;font-weight:600;">👁 View as customer</a>';
    return $actions;
}

/* ------------------------------------------------------------
 * Add "View as customer" button on the order edit screen
 * ------------------------------------------------------------ */
add_action('woocommerce_order_actions_start', 'nvacs_order_impersonate_button');
function nvacs_order_impersonate_button($order_id) {
    if (!current_user_can('manage_options')) return;
    $order = wc_get_order($order_id);
    if (!$order) return;
    $customer_id = $order->get_customer_id();
    if (!$customer_id) {
        echo '<p style="margin:8px 0;color:#888;font-size:12px;">This is a guest order (no customer account linked).</p>';
        return;
    }

    $url = wp_nonce_url(
        add_query_arg(['nvacs_impersonate' => $customer_id], admin_url()),
        'nvacs_impersonate_' . $customer_id
    );

    echo '<p style="margin:10px 0;"><a href="' . esc_url($url) . '" class="button" style="width:100%;text-align:center;">👁 View as this customer</a></p>';
}

/* ------------------------------------------------------------
 * Handle the impersonation switch
 * ------------------------------------------------------------ */
add_action('init', 'nvacs_handle_impersonate', 1);
function nvacs_handle_impersonate() {
    // START impersonating
    if (isset($_GET['nvacs_impersonate'])) {
        $target_id = absint($_GET['nvacs_impersonate']);
        if (!$target_id) return;

        // Must be admin
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to do this.');
        }

        // Verify nonce
        if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'nvacs_impersonate_' . $target_id)) {
            wp_die('Security check failed.');
        }

        $target = get_userdata($target_id);
        if (!$target) {
            wp_die('User not found.');
        }

        $admin_id = get_current_user_id();

        // Store original admin id in a signed cookie
        $cookie_val = $admin_id . '|' . wp_hash($admin_id . '|nvacs_impersonate');
        setcookie(NVACS_IMPERSONATE_COOKIE, $cookie_val, [
            'expires'  => time() + HOUR_IN_SECONDS,
            'path'     => '/',
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        // Switch to the target user
        wp_clear_auth_cookie();
        wp_set_current_user($target_id);
        wp_set_auth_cookie($target_id, false);

        // Redirect to their my-account orders page
        wp_safe_redirect(wc_get_account_endpoint_url('orders'));
        exit;
    }

    // STOP impersonating (switch back)
    if (isset($_GET['nvacs_stop_impersonate'])) {
        if (empty($_COOKIE[NVACS_IMPERSONATE_COOKIE])) {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        $parts = explode('|', $_COOKIE[NVACS_IMPERSONATE_COOKIE], 2);
        if (count($parts) !== 2) {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        list($admin_id, $hmac) = $parts;
        $admin_id = absint($admin_id);

        // Verify the signature
        if (!hash_equals(wp_hash($admin_id . '|nvacs_impersonate'), $hmac)) {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        // Clear the impersonation cookie
        setcookie(NVACS_IMPERSONATE_COOKIE, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        // Switch back to admin
        wp_clear_auth_cookie();
        wp_set_current_user($admin_id);
        wp_set_auth_cookie($admin_id, false);

        // Back to the users list
        wp_safe_redirect(admin_url('users.php'));
        exit;
    }
}

/* ------------------------------------------------------------
 * Floating "switch back" bar — shown only when an impersonation
 * cookie is present (i.e. only the admin sees it; the cookie is
 * set in the admin's browser session, never the real customer's)
 * ------------------------------------------------------------ */
add_action('wp_footer', 'nvacs_impersonate_bar', PHP_INT_MAX);
add_action('admin_footer', 'nvacs_impersonate_bar', PHP_INT_MAX);
function nvacs_impersonate_bar() {
    if (empty($_COOKIE[NVACS_IMPERSONATE_COOKIE])) return;

    // Validate cookie signature before showing the bar
    $parts = explode('|', $_COOKIE[NVACS_IMPERSONATE_COOKIE], 2);
    if (count($parts) !== 2) return;
    list($admin_id, $hmac) = $parts;
    $admin_id = absint($admin_id);
    if (!hash_equals(wp_hash($admin_id . '|nvacs_impersonate'), $hmac)) return;

    $current_user = wp_get_current_user();
    $viewing_name = $current_user->display_name ?: $current_user->user_email;

    // Switch-back is gated by the signed cookie signature (validated above),
    // so it works even though we're currently the customer (no admin caps).
    $stop_url = add_query_arg('nvacs_stop_impersonate', '1', home_url('/'));

    ?>
    <div style="position:fixed;bottom:0;left:0;right:0;z-index:999999;background:#1a1e1c;color:#f2f0eb;padding:12px 20px;font-family:-apple-system,sans-serif;font-size:14px;display:flex;align-items:center;justify-content:center;gap:16px;box-shadow:0 -4px 20px rgba(0,0,0,0.2);">
        <span style="font-family:'Courier New',monospace;font-size:11px;text-transform:uppercase;letter-spacing:0.1em;color:#52b788;">👁 Admin View</span>
        <span>You're viewing as <strong><?php echo esc_html($viewing_name); ?></strong></span>
        <a href="<?php echo esc_url($stop_url); ?>" style="background:#52b788;color:#1a1e1c;padding:8px 18px;border-radius:6px;text-decoration:none;font-weight:600;font-size:13px;">← Switch back to admin</a>
    </div>
    <div style="height:50px;"></div>
    <?php
}

/* ============================================================
 * STANDALONE "VIEW AS CUSTOMER" ADMIN PAGE
 *
 * The row-action / order-button hooks can be swallowed by HPOS
 * or theme conflicts. This is a dedicated, self-contained admin
 * page that lists customers with a search box and a "View as"
 * button for each. Fully under our control — no WC hooks.
 *
 * Access: WP Admin → Tools → View As Customer
 * Or directly: /wp-admin/tools.php?page=nvacs-view-as
 * ============================================================ */

add_action('admin_menu', function() {
    add_management_page(
        'View As Customer',
        'View As Customer',
        'manage_options',
        'nvacs-view-as',
        'nvacs_view_as_page'
    );
});

function nvacs_view_as_page() {
    if (!current_user_can('manage_options')) return;

    $search = isset($_GET['s']) ? sanitize_text_field(wp_unslash($_GET['s'])) : '';

    echo '<div class="wrap">';
    echo '<h1 style="margin-bottom:6px;">View As Customer</h1>';
    echo '<p style="color:#666;margin-top:0;">Search for a customer, then click "View as" to see the site exactly as they do — including their orders. Invisible to the customer.</p>';

    // Search form
    echo '<form method="get" style="margin:20px 0;">';
    echo '<input type="hidden" name="page" value="nvacs-view-as">';
    echo '<input type="search" name="s" value="' . esc_attr($search) . '" placeholder="Search by name, email, or username..." style="width:340px;padding:8px 12px;font-size:14px;" autofocus>';
    echo ' <button type="submit" class="button button-primary">Search</button>';
    echo '</form>';

    // Build the user query
    $args = [
        'number'  => 50,
        'orderby' => 'registered',
        'order'   => 'DESC',
    ];
    if ($search) {
        $args['search'] = '*' . $search . '*';
        $args['search_columns'] = ['user_login', 'user_email', 'display_name', 'user_nicename'];
        // Also search first/last name meta
        $args['meta_query'] = [
            'relation' => 'OR',
            ['key' => 'first_name', 'value' => $search, 'compare' => 'LIKE'],
            ['key' => 'last_name', 'value' => $search, 'compare' => 'LIKE'],
        ];
        // Note: search + meta_query are OR'd manually below via two queries if needed
    }

    $users = get_users($args);

    // If searching and the direct search found nothing, also try a meta-only search
    if ($search && empty($users)) {
        $users = get_users([
            'number' => 50,
            'meta_query' => [
                'relation' => 'OR',
                ['key' => 'first_name', 'value' => $search, 'compare' => 'LIKE'],
                ['key' => 'last_name', 'value' => $search, 'compare' => 'LIKE'],
            ],
        ]);
    }

    if (empty($users)) {
        echo '<p style="padding:20px;background:#fff;border:1px solid #ddd;border-radius:6px;">No customers found' . ($search ? ' for "' . esc_html($search) . '"' : '') . '.</p>';
        echo '</div>';
        return;
    }

    // Table
    echo '<table class="wp-list-table widefat fixed striped" style="margin-top:10px;">';
    echo '<thead><tr>';
    echo '<th style="width:60px;">ID</th>';
    echo '<th>Name</th>';
    echo '<th>Email</th>';
    echo '<th>Username</th>';
    echo '<th style="width:90px;">Orders</th>';
    echo '<th style="width:160px;">Action</th>';
    echo '</tr></thead><tbody>';

    foreach ($users as $u) {
        // Count orders for this user
        $order_count = 0;
        if (function_exists('wc_get_orders')) {
            $cnt_orders = wc_get_orders([
                'customer_id' => $u->ID,
                'limit'       => -1,
                'return'      => 'ids',
            ]);
            $order_count = count($cnt_orders);

            // Also count guest orders by email (not yet linked)
            $guest_orders = wc_get_orders([
                'billing_email' => $u->user_email,
                'customer_id'   => 0,
                'limit'         => -1,
                'return'        => 'ids',
            ]);
            $guest_count = count($guest_orders);
        }

        $view_url = wp_nonce_url(
            add_query_arg(['nvacs_impersonate' => $u->ID], admin_url()),
            'nvacs_impersonate_' . $u->ID
        );

        $full_name = trim($u->first_name . ' ' . $u->last_name) ?: $u->display_name;

        echo '<tr>';
        echo '<td>' . $u->ID . '</td>';
        echo '<td><strong>' . esc_html($full_name) . '</strong></td>';
        echo '<td>' . esc_html($u->user_email) . '</td>';
        echo '<td>' . esc_html($u->user_login) . '</td>';
        echo '<td>' . $order_count;
        if (!empty($guest_count) && $guest_count > 0) {
            echo ' <span style="color:#b32d2e;font-size:11px;" title="Unlinked guest orders with this email">(+' . $guest_count . ' guest)</span>';
        }
        echo '</td>';
        echo '<td><a href="' . esc_url($view_url) . '" class="button button-primary">👁 View as</a></td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    echo '<p style="margin-top:16px;color:#666;font-size:13px;">Showing up to 50 customers. Use search to narrow down. A red "(+N guest)" badge means there are unlinked guest orders with that email — run the <a href="' . esc_url(admin_url('?nvacs_backfill_orders=1')) . '">order backfill</a> to link them.</p>';
    echo '</div>';
}

/* ============================================================
 * CUSTOMER-FACING TRACKING DISPLAY
 *
 * Shows the tracking number + carrier + a "Track package" button
 * on the customer's order detail page (/my-account/view-order/N/).
 *
 * Pulls from saved meta first; if missing (e.g. older orders that
 * shipped before this feature), falls back to scanning the order
 * notes for the Kentro tracking note.
 *
 * Only displays when tracking info actually exists.
 * ============================================================ */

/* ------------------------------------------------------------
 * Get tracking data for an order (meta first, notes fallback)
 * Returns parsed array or null if no tracking found.
 * ------------------------------------------------------------ */
function nvacs_get_order_tracking($order) {
    if (!$order instanceof WC_Order) {
        $order = wc_get_order($order);
    }
    if (!$order) return null;

    // Try saved meta first
    $tracking = $order->get_meta('_nvacs_tracking_number');
    $carrier  = $order->get_meta('_nvacs_tracking_carrier');

    if (!empty($tracking)) {
        $items_json = $order->get_meta('_nvacs_tracking_items');
        $items_list = $items_json ? json_decode($items_json, true) : [];
        if (!is_array($items_list)) $items_list = [];

        return [
            'tracking_number' => $tracking,
            'carrier'         => $carrier ?: 'Carrier',
            'ship_date'       => $order->get_meta('_nvacs_tracking_shipdate'),
            'items_list'      => $items_list,
        ];
    }

    // Fallback: scan the order notes for a Kentro tracking note
    if (function_exists('wc_get_order_notes')) {
        $notes = wc_get_order_notes(['order_id' => $order->get_id(), 'limit' => 50]);
        foreach ($notes as $note) {
            if (stripos($note->content, 'tracking number') !== false) {
                $parsed = nvacs_parse_tracking_note($note->content);
                if (!empty($parsed['tracking_number'])) {
                    // Cache it back to meta so next time is fast
                    $order->update_meta_data('_nvacs_tracking_number', $parsed['tracking_number']);
                    $order->update_meta_data('_nvacs_tracking_carrier', $parsed['carrier']);
                    $order->update_meta_data('_nvacs_tracking_shipdate', $parsed['ship_date']);
                    $order->update_meta_data('_nvacs_tracking_items', wp_json_encode($parsed['items_list']));
                    $order->save();
                    return $parsed;
                }
            }
        }
    }

    return null;
}

/* ------------------------------------------------------------
 * Display tracking box on the customer order detail page
 * ------------------------------------------------------------ */
add_action('woocommerce_order_details_after_order_table', 'nvacs_display_order_tracking', 5);
function nvacs_display_order_tracking($order) {
    $tracking = nvacs_get_order_tracking($order);
    if (!$tracking || empty($tracking['tracking_number'])) return;

    $carrier   = $tracking['carrier'] ?: 'Carrier';
    $number    = $tracking['tracking_number'];
    $ship_date = !empty($tracking['ship_date']) ? $tracking['ship_date'] : '';
    $track_url = nvacs_build_tracking_url($carrier, $number);
    ?>
    <style>
    .nv-track-box {
        background: #dce5d8;
        border: 1px solid #c5d4c0;
        border-radius: 14px;
        padding: 28px 30px;
        margin: 0 0 36px;
        font-family: 'Neue Montreal', -apple-system, sans-serif;
    }
    .nv-track-eyebrow {
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #2d6a4f;
        margin: 0 0 14px;
    }
    .nv-track-row {
        display: flex;
        flex-wrap: wrap;
        gap: 28px;
        margin-bottom: 20px;
    }
    .nv-track-field { min-width: 120px; }
    .nv-track-label {
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #5a6560;
        margin: 0 0 4px;
    }
    .nv-track-value {
        font-family: 'Instrument Serif', Georgia, serif;
        font-size: 22px;
        color: #1a1e1c;
        line-height: 1.1;
    }
    .nv-track-value.mono {
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 18px;
        font-weight: 500;
        color: #2d6a4f;
        letter-spacing: 0.03em;
    }
    .nv-track-btn {
        display: inline-block;
        background: #1a1e1c;
        color: #f2f0eb !important;
        padding: 14px 26px;
        border-radius: 8px;
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 500;
        text-decoration: none !important;
        transition: all 0.3s ease;
    }
    .nv-track-btn:hover {
        background: #2a302d;
        transform: translateY(-1px);
    }
    .nv-track-items {
        font-family: Georgia, serif;
        font-size: 15px;
        color: #1a1e1c;
        line-height: 1.7;
    }
    </style>
    <div class="nv-track-box">
        <p class="nv-track-eyebrow">📦 Shipment Tracking</p>
        <div class="nv-track-row">
            <div class="nv-track-field">
                <p class="nv-track-label">Carrier</p>
                <div class="nv-track-value"><?php echo esc_html($carrier); ?></div>
            </div>
            <div class="nv-track-field">
                <p class="nv-track-label">Tracking Number</p>
                <div class="nv-track-value mono"><?php echo esc_html($number); ?></div>
            </div>
            <?php if ($ship_date): ?>
            <div class="nv-track-field">
                <p class="nv-track-label">Shipped</p>
                <div class="nv-track-value"><?php echo esc_html($ship_date); ?></div>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($tracking['items_list']) && count($tracking['items_list']) > 0): ?>
        <div style="margin-bottom:20px;">
            <p class="nv-track-label">Items Shipped</p>
            <div class="nv-track-items">
                <?php echo implode('<br>', array_map('esc_html', $tracking['items_list'])); ?>
            </div>
        </div>
        <?php endif; ?>
        <a href="<?php echo esc_url($track_url); ?>" target="_blank" rel="noopener" class="nv-track-btn">Track Your Package →</a>
    </div>
    <?php
}

/* ------------------------------------------------------------
 * Also show a compact tracking indicator on the Orders LIST
 * (the table at /my-account/orders/) so customers can see at a
 * glance which orders have shipped.
 * ------------------------------------------------------------ */
add_filter('woocommerce_my_account_my_orders_actions', 'nvacs_orders_list_track_action', 10, 2);
function nvacs_orders_list_track_action($actions, $order) {
    $tracking = nvacs_get_order_tracking($order);
    if ($tracking && !empty($tracking['tracking_number'])) {
        $track_url = nvacs_build_tracking_url($tracking['carrier'], $tracking['tracking_number']);
        $actions['nvacs_track'] = [
            'url'  => $track_url,
            'name' => '📦 Track',
        ];
    }
    return $actions;
}

/* ============================================================
 * AFFILIATE TRACKING DIAGNOSTIC ENDPOINT
 *
 * Visit /wp-admin/?nvacs_affiliate_debug=CODE to diagnose what
 * AffiliateWP knows about a given coupon code:
 *   - is the coupon linked to an affiliate?
 *   - is that affiliate active?
 *   - what visits exist for them?
 *   - what's the most recent visit timestamp?
 * ============================================================ */
add_action('admin_init', function() {
    if (empty($_GET['nvacs_affiliate_debug'])) return;
    if (!current_user_can('manage_options')) return;

    $code = strtolower(sanitize_text_field($_GET['nvacs_affiliate_debug']));

    header('Content-Type: text/html; charset=UTF-8');
    echo '<div style="font-family:monospace;padding:30px;background:#f6f6f6;max-width:1100px;margin:30px auto;border-radius:8px;line-height:1.6;">';
    echo '<h2>Affiliate Tracking Debug: ' . esc_html($code) . '</h2>';

    // 1. Plugin version
    $version = defined('NVACS_VERSION') ? NVACS_VERSION : 'UNKNOWN';
    echo '<p><strong>NVACS_VERSION:</strong> ' . esc_html($version) . '</p>';

    // 2. Coupon exists?
    if (!function_exists('wc_get_coupon_id_by_code')) {
        echo '<p style="color:red;">WooCommerce not loaded.</p></div>';
        exit;
    }
    $coupon_id = wc_get_coupon_id_by_code($code);
    if (!$coupon_id) {
        echo '<p style="color:red;"><strong>FAIL:</strong> No coupon found with code "' . esc_html($code) . '". Check spelling or case.</p></div>';
        exit;
    }
    echo '<p>✅ Coupon found. ID: <strong>' . $coupon_id . '</strong></p>';

    // 3. Affiliate linked?
    $affiliate_id = (int) get_post_meta($coupon_id, 'affwp_discount_affiliate', true);
    if (!$affiliate_id) {
        echo '<p style="color:red;"><strong>FAIL:</strong> Coupon is NOT linked to any affiliate (no affwp_discount_affiliate meta).</p>';
        echo '<p>FIX: Go to WP Admin → AffiliateWP → Affiliates → edit the affiliate → in the "Coupons" section, link the "' . esc_html($code) . '" coupon to them. OR: Go to WC → Coupons → edit "' . esc_html($code) . '" → look for an Affiliate field.</p>';
        echo '</div>';
        exit;
    }
    echo '<p>✅ Coupon linked to affiliate ID: <strong>' . $affiliate_id . '</strong></p>';

    // 4. Affiliate active?
    if (function_exists('affwp_get_affiliate')) {
        $aff = affwp_get_affiliate($affiliate_id);
        if (!$aff) {
            echo '<p style="color:red;"><strong>FAIL:</strong> Affiliate record not found.</p></div>';
            exit;
        }
        echo '<p>Affiliate status: <strong>' . esc_html($aff->status) . '</strong></p>';
        if (function_exists('affwp_get_affiliate_username')) {
            echo '<p>Affiliate username: <strong>' . esc_html(affwp_get_affiliate_username($affiliate_id)) . '</strong></p>';
        }
        if ($aff->status !== 'active') {
            echo '<p style="color:red;"><strong>WARN:</strong> Affiliate is not active. Visits won\'t be tracked. Set status to active in AffiliateWP.</p>';
        }
    }

    // 5. Visit count
    global $wpdb;
    $visits_table = $wpdb->prefix . 'affiliate_wp_visits';
    $count = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $visits_table WHERE affiliate_id = %d", $affiliate_id));
    echo '<p>Total visits in DB for this affiliate: <strong>' . $count . '</strong></p>';

    $recent = $wpdb->get_results($wpdb->prepare("SELECT visit_id, url, referrer, campaign, date FROM $visits_table WHERE affiliate_id = %d ORDER BY visit_id DESC LIMIT 10", $affiliate_id));
    if ($recent) {
        echo '<h3>Last 10 visits</h3><table border="1" cellpadding="6" style="border-collapse:collapse;background:#fff;font-size:12px;">';
        echo '<tr><th>ID</th><th>Date</th><th>URL</th><th>Referrer</th><th>Campaign</th></tr>';
        foreach ($recent as $v) {
            echo '<tr>';
            echo '<td>' . $v->visit_id . '</td>';
            echo '<td>' . esc_html($v->date) . '</td>';
            echo '<td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;">' . esc_html($v->url) . '</td>';
            echo '<td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;">' . esc_html($v->referrer ?: '-') . '</td>';
            echo '<td>' . esc_html($v->campaign ?: '-') . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p style="color:#888;">No visits in the database yet.</p>';
    }

    // 6. Your current cookie state
    echo '<hr><h3>Your browser cookies (this admin session)</h3>';
    echo '<p>affwp_ref: <strong>' . esc_html($_COOKIE['affwp_ref'] ?? '(not set)') . '</strong></p>';
    echo '<p>affwp_ref_visit_id: <strong>' . esc_html($_COOKIE['affwp_ref_visit_id'] ?? '(not set)') . '</strong></p>';

    // 7. Trace log
    echo '<hr><h3>Trace log (last 50 entries)</h3>';
    if (isset($_GET['clear_trace'])) {
        delete_option('nvacs_affiliate_trace');
        echo '<p style="color:green;">Trace cleared.</p>';
    } else {
        echo '<p><a href="' . esc_url(add_query_arg(['nvacs_affiliate_debug' => $code, 'clear_trace' => '1'], admin_url())) . '" class="button">Clear trace</a></p>';
    }
    $trace = get_option('nvacs_affiliate_trace', []);
    if (empty($trace)) {
        echo '<p style="color:#888;">No trace entries. The tracker has not run since last clear.</p>';
        echo '<p>Try visiting <code>' . esc_url(home_url('/?nv_coupon=' . $code)) . '</code> in incognito, then reload this page.</p>';
    } else {
        echo '<pre style="background:#fff;padding:14px;border:1px solid #ddd;border-radius:6px;font-size:12px;line-height:1.7;">';
        foreach ($trace as $line) echo esc_html($line) . "\n";
        echo '</pre>';
    }

    echo '<p style="color:#888;font-size:13px;margin-top:20px;">Note: AffiliateWP typically does NOT record visits when an admin is logged in. Test the link in incognito (no admin login).</p>';

    echo '</div>';
    exit;
});
