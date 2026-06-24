<?php
/**
 * Plugin Name: Natty Vision Quiz Popup
 * Plugin URI:  https://nattyvision.com
 * Description: 3-step product recommendation quiz popup with Omnisend integration. Triggers 5s after page load, captures email, syncs to Omnisend with goal tags, and unlocks a 10% off code (QUIZ10).
 * Version:     1.3.2
 * Author:      Natty Vision
 * License:     GPL-2.0+
 * Requires PHP: 7.4
 * Requires at least: 5.8
 */

if (!defined('ABSPATH')) exit;

define('NVQP_VERSION', '1.3.2');

/* ============================================================
 * QUIZ POPUP — Product Recommendation + 10% Off
 *
 * Triggers 5s after page load. Asks 3 questions:
 *   1. Primary goal (skin/hair, cognitive, fat loss, muscle/recovery)
 *   2. Experience level
 *   3. Email capture
 *
 * On submit:
 *   - Pushes contact to Omnisend (via API) with goal tags
 *   - Auto-creates QUIZ10 coupon if missing
 *   - Shows personalized product recommendation + code
 *
 * Behavior:
 *   - Skipped if user is logged in
 *   - Skipped on cart/checkout/my-account pages
 *   - One-time-ever per visitor (cookie nvqp_seen)
 * ============================================================ */

/* ------------------------------------------------------------
 * ADMIN SETTINGS PAGE: Tools → Quiz Popup Settings
 * Stores Omnisend API key + list name in wp_options
 * ------------------------------------------------------------ */
add_action('admin_menu', function() {
    add_management_page(
        'Quiz Popup Settings',
        'Quiz Popup Settings',
        'manage_options',
        'nvqp-settings',
        'nvqp_settings_page'
    );
});

// Load the WP media uploader only on our settings screen
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook === 'tools_page_nvqp-settings') {
        wp_enqueue_media();
    }
});

function nvqp_settings_page() {
    if (!current_user_can('manage_options')) return;

    if (isset($_POST['nvqp_save_settings']) && check_admin_referer('nvqp_settings')) {
        $api_key = sanitize_text_field(wp_unslash($_POST['nvqp_omnisend_api_key'] ?? ''));
        $enabled = isset($_POST['nvqp_enabled']) ? '1' : '0';
        $hero    = esc_url_raw(wp_unslash($_POST['nvqp_hero_image'] ?? ''));

        if ($api_key !== '') update_option('nvqp_omnisend_api_key', $api_key, false);
        update_option('nvqp_enabled', $enabled, false);
        update_option('nvqp_hero_image', $hero, false);

        echo '<div class="notice notice-success"><p>Settings saved.</p></div>';
    }

    $api_key = get_option('nvqp_omnisend_api_key', '');
    $enabled = get_option('nvqp_enabled', '0');
    $hero    = get_option('nvqp_hero_image', '');
    $hero_const = defined('NVQP_HERO_IMAGE') && NVQP_HERO_IMAGE;

    // Mask the API key for display
    $masked_key = $api_key ? substr($api_key, 0, 6) . str_repeat('•', max(0, strlen($api_key) - 10)) . substr($api_key, -4) : '';
    ?>
    <div class="wrap">
        <h1>Quiz Popup Settings</h1>
        <p>Configure the product-recommendation quiz that pops up 5 seconds after a visitor lands. Captured emails sync to Omnisend.</p>

        <form method="post" action="">
            <?php wp_nonce_field('nvqp_settings'); ?>

            <table class="form-table">
                <tr>
                    <th><label for="nvqp_enabled">Quiz Popup Active?</label></th>
                    <td>
                        <label>
                            <input type="checkbox" id="nvqp_enabled" name="nvqp_enabled" value="1" <?php checked($enabled, '1'); ?>>
                            Show quiz popup to visitors
                        </label>
                        <p class="description">Uncheck to disable the popup site-wide without losing your config.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="nvqp_hero_image">Full-screen hero image</label></th>
                    <td>
                        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
                            <input type="text" id="nvqp_hero_image" name="nvqp_hero_image" value="<?php echo esc_attr($hero); ?>" class="regular-text" placeholder="https://nattyvision.com/wp-content/uploads/.../hero.jpg" <?php echo $hero_const ? 'disabled' : ''; ?>>
                            <button type="button" class="button" id="nvqp_hero_pick" <?php echo $hero_const ? 'disabled' : ''; ?>>Choose / Upload</button>
                            <button type="button" class="button" id="nvqp_hero_clear" <?php echo $hero_const ? 'disabled' : ''; ?>>Clear</button>
                        </div>
                        <p class="description">
                            <?php if ($hero_const): ?>
                                Set via the <code>NVQP_HERO_IMAGE</code> constant in <code>wp-config.php</code> — edit it there to change.
                            <?php else: ?>
                                Background image for the full-screen quiz. Upload a wide, web-optimized image (≈2000px). Leave blank to use the bundled default hero.
                            <?php endif; ?>
                        </p>
                        <div id="nvqp_hero_preview" style="margin-top:10px;<?php echo $hero ? '' : 'display:none;'; ?>">
                            <img src="<?php echo esc_url($hero); ?>" alt="" style="max-width:380px;height:auto;border-radius:8px;border:1px solid #d4d2cc;display:block;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <th><label for="nvqp_omnisend_api_key">Omnisend API Key</label></th>
                    <td>
                        <input type="password" id="nvqp_omnisend_api_key" name="nvqp_omnisend_api_key" value="" class="regular-text" placeholder="<?php echo $api_key ? esc_attr('Current: ' . $masked_key) : 'Paste your Omnisend API key'; ?>" autocomplete="new-password">
                        <p class="description">Get this from Omnisend → Store settings → Integrations & API → API keys.<br>Needs <strong>Contacts: Write</strong> permission. Leave blank to keep current key.</p>
                    </td>
                </tr>
            </table>

            <p><button type="submit" name="nvqp_save_settings" class="button button-primary">Save Settings</button></p>
        </form>

        <hr>
        <h2>Omnisend Setup</h2>
        <p>The plugin tags each quiz taker with:</p>
        <ul style="list-style:disc;margin-left:24px;">
            <li><code>quiz-taker</code> (everyone)</li>
            <li><code>quiz-goal-fat-loss-weight-management</code> (or the relevant goal)</li>
            <li><code>quiz-experience-new-to-peptides</code> (or experienced)</li>
        </ul>
        <p>And these custom properties (usable in email templates):</p>
        <ul style="list-style:disc;margin-left:24px;">
            <li><code>quiz_goal</code> — readable goal label</li>
            <li><code>quiz_experience</code> — experience level</li>
            <li><code>quiz_recommendation</code> — product name we recommended</li>
            <li><code>quiz_taken_at</code> — ISO timestamp</li>
        </ul>
        <p>In Omnisend: create a <strong>Segment</strong> filtered by <code>Tag is "quiz-taker"</code>, then build an <strong>Automation</strong> with trigger <strong>"Tag added: quiz-taker"</strong> to send the welcome email.</p>

        <hr>
        <h2>Test / Preview</h2>
        <p>
            <a href="<?php echo esc_url(home_url('/?nvqp_preview=1')); ?>" target="_blank" class="button">Preview popup on homepage</a>
            <span style="color:#666;margin-left:10px;font-size:13px;">Opens homepage with quiz forced to show, even if logged in.</span>
        </p>
        <p>
            <a href="<?php echo esc_url(admin_url('?nvqp_test_omnisend=1')); ?>" class="button">Test Omnisend API connection</a>
            <span style="color:#666;margin-left:10px;font-size:13px;">Verifies your API key works.</span>
        </p>
    </div>
    <?php if (!$hero_const): ?>
    <script>
    (function(){
        var frame;
        var input   = document.getElementById('nvqp_hero_image');
        var pick    = document.getElementById('nvqp_hero_pick');
        var clear   = document.getElementById('nvqp_hero_clear');
        var preview = document.getElementById('nvqp_hero_preview');
        if (!pick) return;
        pick.addEventListener('click', function(e){
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({ title: 'Select hero image', button: { text: 'Use this image' }, multiple: false });
            frame.on('select', function(){
                var att = frame.state().get('selection').first().toJSON();
                input.value = att.url;
                preview.querySelector('img').src = att.url;
                preview.style.display = '';
            });
            frame.open();
        });
        clear.addEventListener('click', function(e){
            e.preventDefault();
            input.value = '';
            preview.style.display = 'none';
        });
    })();
    </script>
    <?php endif; ?>
    <?php
}

/* ------------------------------------------------------------
 * Omnisend API: test connection endpoint
 * ------------------------------------------------------------ */
add_action('admin_init', function() {
    if (empty($_GET['nvqp_test_omnisend'])) return;
    if (!current_user_can('manage_options')) return;

    $api_key = get_option('nvqp_omnisend_api_key', '');
    header('Content-Type: text/html; charset=UTF-8');
    echo '<div style="font-family:monospace;padding:30px;background:#f6f6f6;max-width:900px;margin:30px auto;border-radius:8px;">';
    echo '<h2>Omnisend API Test</h2>';

    if (!$api_key) {
        echo '<p style="color:red;">No API key configured. Save one in Tools → Quiz Popup Settings first.</p></div>';
        exit;
    }

    // Try a simple GET to /v3/contacts (validates key without modifying anything)
    $response = wp_remote_get('https://api.omnisend.com/v3/contacts?limit=1', [
        'headers' => [
            'X-API-KEY'    => $api_key,
            'Content-Type' => 'application/json',
        ],
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        echo '<p style="color:red;">Connection failed: ' . esc_html($response->get_error_message()) . '</p>';
    } else {
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        echo '<p><strong>HTTP status:</strong> ' . $code . '</p>';
        if ($code === 200) {
            echo '<p style="color:green;font-size:18px;"><strong>✅ Connection successful!</strong> API key works.</p>';
        } else {
            echo '<p style="color:red;"><strong>❌ Failed.</strong></p>';
            echo '<pre style="background:#fff;padding:14px;border:1px solid #ddd;overflow:auto;">' . esc_html(substr($body, 0, 1000)) . '</pre>';
        }
    }
    echo '</div>';
    exit;
});

/* ------------------------------------------------------------
 * Push a contact to Omnisend
 * ------------------------------------------------------------ */
function nvqp_omnisend_push_contact($email, $first_name, $goal_label, $experience_label, $product_recommendation) {
    $api_key = get_option('nvqp_omnisend_api_key', '');
    if (!$api_key) {
        error_log('[NVQP] Omnisend push skipped: no API key');
        return false;
    }

    $payload = [
        'identifiers' => [[
            'type'     => 'email',
            'id'       => $email,
            'channels' => [
                'email' => ['status' => 'subscribed', 'statusDate' => gmdate('c')],
            ],
        ]],
        'firstName' => $first_name,
        'tags' => [
            'quiz-taker',
            'quiz-goal-' . sanitize_title($goal_label),
            'quiz-experience-' . sanitize_title($experience_label),
        ],
        'customProperties' => [
            'quiz_goal'          => $goal_label,
            'quiz_experience'    => $experience_label,
            'quiz_recommendation' => $product_recommendation,
            'quiz_taken_at'      => gmdate('c'),
        ],
    ];

    $response = wp_remote_post('https://api.omnisend.com/v3/contacts', [
        'headers' => [
            'X-API-KEY'    => $api_key,
            'Content-Type' => 'application/json',
        ],
        'body'    => wp_json_encode($payload),
        'timeout' => 15,
    ]);

    if (is_wp_error($response)) {
        error_log('[NVQP] Omnisend push failed: ' . $response->get_error_message());
        return false;
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        error_log('[NVQP] Omnisend contact created/updated: ' . $email);
        return true;
    }

    error_log('[NVQP] Omnisend push got HTTP ' . $code . ' for ' . $email . ': ' . wp_remote_retrieve_body($response));
    return false;
}

/* ------------------------------------------------------------
 * Ensure the QUIZ10 coupon exists in WooCommerce
 * ------------------------------------------------------------ */
function nvqp_ensure_coupon() {
    $code = 'QUIZ10';
    if (!function_exists('wc_get_coupon_id_by_code')) return false;

    $existing = wc_get_coupon_id_by_code($code);
    if ($existing) return $code;

    $coupon = new WC_Coupon();
    $coupon->set_code($code);
    $coupon->set_discount_type('percent');
    $coupon->set_amount(10);
    $coupon->set_individual_use(false);
    $coupon->set_usage_limit(0);
    $coupon->set_usage_limit_per_user(1); // each customer can only use it once
    $coupon->set_description('10% off — auto-generated by quiz popup');
    $coupon->save();

    return $code;
}

/* ------------------------------------------------------------
 * Quiz logic: map answers → product recommendation
 * ------------------------------------------------------------ */
function nvqp_get_recommendation($goal) {
    $recommendations = [
        'skin-hair' => [
            'name'        => 'GHK-Cu',
            'description' => 'For collagen production, skin elasticity, and hair regrowth.',
            'url'         => 'https://nattyvision.com/product/ghk-cu/',
        ],
        'cognitive' => [
            'name'        => 'Semax',
            'description' => 'Sharper focus, improved memory, and reduced mental fatigue.',
            'url'         => 'https://nattyvision.com/product/semax/',
        ],
        'fat-loss' => [
            'name'        => 'Retatrutide',
            'description' => 'Industry-leading GLP-1 for accelerated fat loss and appetite control.',
            'url'         => 'https://nattyvision.com/product/retatrutide/',
        ],
        'muscle' => [
            'name'        => 'BPC-157',
            'description' => 'Faster recovery, joint repair, and muscle preservation.',
            'url'         => 'https://nattyvision.com/product/bpc-157/',
        ],
    ];

    return $recommendations[$goal] ?? $recommendations['fat-loss'];
}

/* ------------------------------------------------------------
 * Resolve the full-screen hero image URL
 * Priority: NVQP_HERO_IMAGE constant > saved option > none (gradient fallback)
 * ------------------------------------------------------------ */
function nvqp_get_hero_url() {
    if (defined('NVQP_HERO_IMAGE') && NVQP_HERO_IMAGE) return NVQP_HERO_IMAGE;
    $u = get_option('nvqp_hero_image', '');
    if ($u) return $u;
    // Bundled default so the popup always has a background, even before config.
    return 'data:image/jpeg;base64,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';
}

/* ------------------------------------------------------------
 * AJAX handler: process quiz submission
 * ------------------------------------------------------------ */
add_action('wp_ajax_nvqp_submit',        'nvqp_submit');
add_action('wp_ajax_nopriv_nvqp_submit', 'nvqp_submit');
function nvqp_submit() {
    check_ajax_referer('nvqp', 'nonce');

    $email      = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $first_name = sanitize_text_field(wp_unslash($_POST['first_name'] ?? ''));
    $goal       = sanitize_key(wp_unslash($_POST['goal'] ?? ''));
    $experience = sanitize_key(wp_unslash($_POST['experience'] ?? ''));

    if (!is_email($email)) {
        wp_send_json_error(['message' => 'Please enter a valid email address.']);
    }
    if (!in_array($goal, ['skin-hair', 'cognitive', 'fat-loss', 'muscle'], true)) {
        wp_send_json_error(['message' => 'Please select a goal.']);
    }

    // Get recommendation
    $rec = nvqp_get_recommendation($goal);

    // Map labels
    $goal_labels = [
        'skin-hair' => 'Skin / Hair / Aesthetics',
        'cognitive' => 'Cognitive / Focus',
        'fat-loss'  => 'Fat Loss / Weight Management',
        'muscle'    => 'Muscle / Recovery / Performance',
    ];
    $experience_labels = [
        'new'         => 'New to peptides',
        'experienced' => 'Experienced',
    ];

    $goal_label = $goal_labels[$goal] ?? $goal;
    $experience_label = $experience_labels[$experience] ?? $experience;

    // Ensure coupon exists
    $coupon_code = nvqp_ensure_coupon();

    // Push to Omnisend
    nvqp_omnisend_push_contact($email, $first_name, $goal_label, $experience_label, $rec['name']);

    wp_send_json_success([
        'product_name'        => $rec['name'],
        'product_description' => $rec['description'],
        'product_url'         => $rec['url'],
        'coupon_code'         => $coupon_code,
    ]);
}

/* ------------------------------------------------------------
 * Render the quiz popup on the front-end
 * Only renders if: enabled + not logged in + not on cart/checkout/account
 * + cookie not present (or preview mode)
 * ------------------------------------------------------------ */
// Hook into multiple actions to ensure the popup renders even on themes that
// don't reliably call wp_footer (some custom and page-builder themes skip it).
add_action('wp_footer', 'nvqp_render_popup', 999);
add_action('shutdown', 'nvqp_render_popup_shutdown_fallback', 0);

function nvqp_render_popup_shutdown_fallback() {
    // Only run on front-end shutdown, not admin, not REST, not AJAX
    if (is_admin()) return;
    if (defined('REST_REQUEST') && REST_REQUEST) return;
    if (defined('DOING_AJAX') && DOING_AJAX) return;
    if (defined('DOING_CRON') && DOING_CRON) return;
    // Already rendered via wp_footer?
    if (defined('NVQP_RENDERED')) return;
    nvqp_render_popup();
}

function nvqp_render_popup() {
    // Prevent double-rendering across hooks
    if (defined('NVQP_RENDERED')) return;

    $preview = isset($_GET['nvqp_preview']) && current_user_can('manage_options');

    // Enabled check — but preview mode bypasses it so admins can always test
    if (!$preview && get_option('nvqp_enabled', '0') !== '1') return;

    if (!$preview) {
        // Homepage-only check: cover both WP's is_front_page() AND a URL-based
        // fallback in case the theme or page setup doesn't make is_front_page()
        // return true reliably.
        $is_homepage = is_front_page();
        if (!$is_homepage) {
            $request_path = trim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), '/');
            $is_homepage = ($request_path === '' || $request_path === 'home');
        }
        if (!$is_homepage) return;

        if (is_user_logged_in()) return;
        if (function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page())) return;
        if (!empty($_COOKIE['nvqp_seen'])) return;
    }

    // Mark as rendered so the shutdown fallback doesn't duplicate
    if (!defined('NVQP_RENDERED')) define('NVQP_RENDERED', true);

    $nonce = wp_create_nonce('nvqp');
    $ajax_url = admin_url('admin-ajax.php');
    $hero_url = nvqp_get_hero_url();
    $hero_css = (strpos($hero_url, 'data:') === 0) ? $hero_url : esc_url($hero_url);
    ?>
    <style>
    #nvqp-overlay {
        position: fixed;
        inset: 0;
        z-index: 999998;
        display: none;
        opacity: 0;
        transition: opacity 0.45s ease;
        /* Hero image is set inline on the element; this is the fallback look */
        background-color: #e7e9e3;
        background-image: linear-gradient(135deg, #dce5d8 0%, #f2f0eb 55%, #e7e9e3 100%);
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        font-family: 'Neue Montreal', -apple-system, sans-serif;
    }
    #nvqp-overlay.nvqp-show { display: flex; opacity: 1; }

    /* Left content sheet (desktop): solid brand panel sitting over the hero */
    #nvqp-panel {
        position: relative;
        flex: 0 0 auto;
        width: 50%;
        min-width: 480px;
        max-width: 100%;
        height: 100%;
        background: #f2f0eb;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
        box-shadow: 40px 0 110px rgba(26,30,28,0.20);
        transform: translateX(-24px);
        transition: transform 0.5s cubic-bezier(0.22,1,0.36,1);
    }
    #nvqp-overlay.nvqp-show #nvqp-panel { transform: translateX(0); }
    /* Feather the panel's right edge so it melts into the photo */
    #nvqp-panel::after {
        content: '';
        position: absolute;
        top: 0; right: -60px; bottom: 0;
        width: 60px;
        background: linear-gradient(90deg, #f2f0eb 0%, rgba(242,240,235,0) 100%);
        pointer-events: none;
    }
    #nvqp-modal {
        width: 100%;
        max-width: 620px;
        padding: clamp(40px, 5vh, 72px) clamp(40px, 5vw, 80px);
        box-sizing: border-box;
    }
    /* Mobile-only spacer that lets the hero show above the bottom sheet */
    .nvqp-hero-spacer { display: none; }

    .nvqp-close {
        position: absolute;
        top: 20px; right: 22px;
        z-index: 5;
        background: rgba(26,30,28,0.85);
        border: none;
        cursor: pointer;
        font-size: 20px;
        line-height: 1;
        color: #f2f0eb;
        width: 38px; height: 38px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 50%;
        backdrop-filter: blur(2px);
        transition: background 0.2s, transform 0.2s;
    }
    .nvqp-close:hover { background: #1a1e1c; transform: scale(1.05); }
    .nvqp-eyebrow {
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #2d6a4f;
        margin-bottom: 16px;
    }
    .nvqp-h1 {
        font-family: 'Instrument Serif', Georgia, serif;
        font-weight: 400;
        font-size: clamp(40px, 3.6vw, 60px);
        line-height: 1.04;
        letter-spacing: -0.02em;
        color: #1a1e1c;
        margin: 0 0 16px;
    }
    .nvqp-h1 em { font-style: italic; color: #2d6a4f; }
    .nvqp-sub {
        font-size: clamp(15px, 1.1vw, 18px);
        line-height: 1.55;
        color: #4a4f4c;
        margin: 0 0 34px;
    }
    .nvqp-step { display: none; }
    .nvqp-step.active { display: block; }
    .nvqp-option {
        display: block;
        background: #e9e7e1;
        border: 1.5px solid #d4d2cc;
        border-radius: 14px;
        padding: 22px 26px;
        margin-bottom: 14px;
        cursor: pointer;
        transition: all 0.25s ease;
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: clamp(14px, 1.05vw, 16px);
        color: #1a1e1c;
        letter-spacing: 0.02em;
    }
    .nvqp-option:hover { background: #dce5d8; border-color: #2d6a4f; transform: translateX(4px); }
    .nvqp-option.selected { background: #2d6a4f; color: #f2f0eb; border-color: #2d6a4f; }
    .nvqp-input {
        width: 100%;
        background: #e9e7e1;
        border: 1.5px solid #d4d2cc;
        border-radius: 12px;
        padding: 18px 20px;
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 16px;
        color: #1a1e1c;
        margin-bottom: 16px;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }
    .nvqp-input:focus {
        outline: none;
        border-color: #2d6a4f;
        box-shadow: 0 0 0 3px rgba(45,106,79,0.15);
    }
    .nvqp-btn {
        background: #1a1e1c;
        color: #f2f0eb;
        border: none;
        border-radius: 12px;
        padding: 20px 32px;
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        font-weight: 500;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s ease;
        margin-top: 12px;
    }
    .nvqp-btn:hover { background: #2a302d; transform: translateY(-1px); }
    .nvqp-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    .nvqp-progress {
        height: 4px;
        background: #e9e7e1;
        border-radius: 2px;
        overflow: hidden;
        margin-bottom: 34px;
    }
    .nvqp-progress-bar {
        height: 100%;
        background: #2d6a4f;
        transition: width 0.4s ease;
    }
    .nvqp-result-card {
        background: #dce5d8;
        border-radius: 14px;
        padding: 24px;
        margin: 18px 0 20px;
    }
    .nvqp-product-name {
        font-family: 'Instrument Serif', Georgia, serif;
        font-style: italic;
        font-size: clamp(32px, 2.6vw, 40px);
        color: #2d6a4f;
        margin: 0 0 8px;
    }
    .nvqp-product-desc {
        font-size: 14px;
        color: #2d4a3a;
        line-height: 1.5;
        margin: 0;
    }
    .nvqp-code-box {
        background: #1a1e1c;
        color: #f2f0eb;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        margin: 18px 0;
    }
    .nvqp-code-label {
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        color: #c5d4c0;
        margin-bottom: 6px;
    }
    .nvqp-code-value {
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 38px;
        font-weight: 600;
        letter-spacing: 0.18em;
        color: #f2f0eb;
    }
    .nvqp-error {
        background: #faf0d6;
        border: 1px solid #e5cb74;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 14px;
        font-size: 13px;
        color: #6b5414;
        display: none;
    }
    .nvqp-error.show { display: block; }
    .nvqp-offer-banner {
        background: #1a1e1c;
        border-radius: 16px;
        padding: 30px 32px;
        margin-bottom: 32px;
        text-align: center;
        color: #f2f0eb;
        position: relative;
        overflow: hidden;
    }
    .nvqp-offer-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent 40%, rgba(82,183,136,0.15) 100%);
        pointer-events: none;
    }
    .nvqp-offer-label {
        font-family: 'DM Mono', 'Courier New', monospace;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.18em;
        color: #c5d4c0;
        margin-bottom: 8px;
        position: relative;
    }
    .nvqp-offer-amount {
        font-family: 'Instrument Serif', Georgia, serif;
        font-size: clamp(60px, 5vw, 82px);
        line-height: 1;
        font-weight: 400;
        letter-spacing: 0.02em;
        color: #52b788;
        margin: 6px 0;
        position: relative;
    }
    .nvqp-offer-sub {
        font-family: 'Neue Montreal', sans-serif;
        font-size: 14px;
        color: #c5d4c0;
        letter-spacing: 0.02em;
        margin-top: 6px;
        position: relative;
    }
    /* ===== MOBILE / TABLET (<=860px): classic centered modal, no hero ===== */
    @media (max-width: 860px) {
        #nvqp-overlay {
            flex-direction: row;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(26,30,28,0.55);
            backdrop-filter: blur(4px);
        }
        .nvqp-hero-spacer { display: none; }
        #nvqp-panel {
            width: 100%;
            min-width: 0;
            max-width: 520px;
            height: auto;
            max-height: 90vh;
            flex: 0 0 auto;
            border-radius: 24px;
            box-shadow: 0 30px 80px rgba(0,0,0,0.30);
            align-items: stretch;
            transform: translateY(20px);
        }
        #nvqp-overlay.nvqp-show #nvqp-panel { transform: translateY(0); }
        #nvqp-panel::after { display: none; }
        #nvqp-modal { width: 100%; max-width: 100%; padding: 44px 32px; margin: 0; }
        /* Close sits on the dim backdrop, light so it reads */
        .nvqp-close {
            background: rgba(242,240,235,0.18);
            color: #f2f0eb;
        }
        .nvqp-close:hover { background: rgba(242,240,235,0.32); }
        /* Comfortable modal-scale typography */
        .nvqp-eyebrow { font-size: 12px; margin-bottom: 12px; }
        .nvqp-h1 { font-size: 32px; margin-bottom: 12px; }
        .nvqp-sub { font-size: 14px; margin-bottom: 26px; }
        .nvqp-progress { margin-bottom: 24px; }
        .nvqp-offer-banner { padding: 22px 24px; margin-bottom: 26px; border-radius: 14px; }
        .nvqp-offer-amount { font-size: 50px; }
        .nvqp-offer-label { font-size: 11px; }
        .nvqp-offer-sub { font-size: 12px; }
        .nvqp-option { font-size: 13px; padding: 16px 20px; margin-bottom: 10px; border-radius: 12px; }
        .nvqp-input { font-size: 14px; padding: 14px 16px; border-radius: 10px; }
        .nvqp-btn { font-size: 12px; padding: 16px 28px; border-radius: 10px; }
        .nvqp-product-name { font-size: 28px; }
        .nvqp-code-value { font-size: 30px; }
    }
    @media (max-width: 540px) {
        #nvqp-panel { border-radius: 18px; max-height: 92vh; }
        #nvqp-modal { padding: 34px 24px; }
        .nvqp-h1 { font-size: 28px; }
        .nvqp-offer-amount { font-size: 42px; }
        .nvqp-offer-banner { padding: 18px 20px; }
    }
    </style>
    <style>@media (min-width: 861px) { #nvqp-overlay { background-image: url("<?php echo $hero_css; ?>"); } }</style>

    <div id="nvqp-overlay" role="dialog" aria-modal="true" aria-labelledby="nvqp-title">
        <button type="button" class="nvqp-close" aria-label="Close">×</button>
        <div class="nvqp-hero-spacer" aria-hidden="true"></div>
        <div id="nvqp-panel">
            <div id="nvqp-modal">

                <div class="nvqp-progress"><div class="nvqp-progress-bar" style="width:33%;"></div></div>

            <!-- Step 1: Goal -->
            <div class="nvqp-step active" data-step="1">
                <div class="nvqp-offer-banner">
                    <div class="nvqp-offer-label">Take the quiz · Unlock</div>
                    <div class="nvqp-offer-amount">10% OFF</div>
                    <div class="nvqp-offer-sub">+ a personalized product recommendation</div>
                </div>
                <h2 id="nvqp-title" class="nvqp-h1">What's your primary <em>goal?</em></h2>
                <p class="nvqp-sub">Pick the one that fits you best. We'll match you with the right peptide.</p>

                <div class="nvqp-option" data-goal="fat-loss">→ Fat loss / weight management</div>
                <div class="nvqp-option" data-goal="muscle">→ Muscle / recovery / performance</div>
                <div class="nvqp-option" data-goal="skin-hair">→ Skin / hair / aesthetics</div>
                <div class="nvqp-option" data-goal="cognitive">→ Cognitive / focus</div>
            </div>

            <!-- Step 2: Experience -->
            <div class="nvqp-step" data-step="2">
                <div class="nvqp-eyebrow">Step 2 of 3</div>
                <h2 class="nvqp-h1">How familiar are you with <em>peptides?</em></h2>
                <p class="nvqp-sub">No wrong answer — we'll tailor recommendations to your level.</p>

                <div class="nvqp-option" data-experience="new">→ I'm new to peptides</div>
                <div class="nvqp-option" data-experience="experienced">→ I've used them before</div>
            </div>

            <!-- Step 3: Email + Name -->
            <div class="nvqp-step" data-step="3">
                <div class="nvqp-eyebrow">Step 3 of 3 · One last step</div>
                <h2 class="nvqp-h1">Where should we send your <em>10% off?</em></h2>
                <p class="nvqp-sub">Your personalized recommendation + discount code, straight to your inbox.</p>

                <div class="nvqp-error" id="nvqp-error"></div>
                <input type="text" id="nvqp-name" class="nvqp-input" placeholder="First name" autocomplete="given-name">
                <input type="email" id="nvqp-email" class="nvqp-input" placeholder="you@example.com" autocomplete="email" required>
                <button type="button" class="nvqp-btn" id="nvqp-submit">Unlock my 10% off →</button>
            </div>

            <!-- Step 4: Result -->
            <div class="nvqp-step" data-step="4">
                <div class="nvqp-eyebrow">Your recommendation</div>
                <h2 class="nvqp-h1">You're a great fit for…</h2>

                <div class="nvqp-result-card">
                    <p class="nvqp-product-name" id="nvqp-result-name">—</p>
                    <p class="nvqp-product-desc" id="nvqp-result-desc">—</p>
                </div>

                <div class="nvqp-code-box">
                    <div class="nvqp-code-label">Your 10% off code</div>
                    <div class="nvqp-code-value" id="nvqp-result-code">QUIZ10</div>
                </div>

                <a href="#" id="nvqp-result-shop" class="nvqp-btn" style="display:block;text-align:center;text-decoration:none;">Shop with this code →</a>
            </div>
            </div>
        </div>
    </div>

    <script>
    (function() {
        var ajax_url = <?php echo wp_json_encode($ajax_url); ?>;
        var nonce = <?php echo wp_json_encode($nonce); ?>;
        var isPreview = <?php echo $preview ? 'true' : 'false'; ?>;

        var overlay = document.getElementById('nvqp-overlay');
        var modal = document.getElementById('nvqp-modal');
        var closeBtn = overlay.querySelector('.nvqp-close');
        var steps = overlay.querySelectorAll('.nvqp-step');
        var progressBar = overlay.querySelector('.nvqp-progress-bar');
        var errorBox = document.getElementById('nvqp-error');

        var state = { goal: '', experience: '', step: 1 };

        function goToStep(n) {
            state.step = n;
            steps.forEach(function(s) {
                s.classList.toggle('active', parseInt(s.dataset.step) === n);
            });
            progressBar.style.width = (n / 3 * 100) + '%';
            if (n === 4) progressBar.parentElement.style.display = 'none';
        }

        function setCookie(name, value, days) {
            var d = new Date();
            d.setTime(d.getTime() + (days*24*60*60*1000));
            document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
        }

        function closeQuiz() {
            overlay.classList.remove('nvqp-show');
            if (!isPreview) setCookie('nvqp_seen', '1', 365);
        }

        function showQuiz() {
            overlay.classList.add('nvqp-show');
        }

        // Open after 5s (unless preview, which opens immediately)
        if (isPreview) {
            setTimeout(showQuiz, 200);
        } else {
            setTimeout(showQuiz, 5000);
        }

        closeBtn.addEventListener('click', closeQuiz);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeQuiz();
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && overlay.classList.contains('nvqp-show')) closeQuiz();
        });

        // Goal selection
        overlay.querySelectorAll('[data-goal]').forEach(function(el) {
            el.addEventListener('click', function() {
                state.goal = el.dataset.goal;
                goToStep(2);
            });
        });

        // Experience selection
        overlay.querySelectorAll('[data-experience]').forEach(function(el) {
            el.addEventListener('click', function() {
                state.experience = el.dataset.experience;
                goToStep(3);
                setTimeout(function() {
                    var emailInput = document.getElementById('nvqp-email');
                    if (emailInput) emailInput.focus();
                }, 300);
            });
        });

        // Submit
        document.getElementById('nvqp-submit').addEventListener('click', function() {
            var emailEl = document.getElementById('nvqp-email');
            var nameEl = document.getElementById('nvqp-name');
            var email = emailEl.value.trim();
            var firstName = nameEl.value.trim();

            errorBox.classList.remove('show');

            if (!email || email.indexOf('@') < 1) {
                errorBox.textContent = 'Please enter a valid email address.';
                errorBox.classList.add('show');
                emailEl.focus();
                return;
            }

            var btn = this;
            btn.disabled = true;
            btn.textContent = 'Unlocking...';

            var formData = new FormData();
            formData.append('action', 'nvqp_submit');
            formData.append('nonce', nonce);
            formData.append('email', email);
            formData.append('first_name', firstName);
            formData.append('goal', state.goal);
            formData.append('experience', state.experience);

            fetch(ajax_url, { method: 'POST', body: formData, credentials: 'same-origin' })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        document.getElementById('nvqp-result-name').textContent = data.data.product_name;
                        document.getElementById('nvqp-result-desc').textContent = data.data.product_description;
                        document.getElementById('nvqp-result-code').textContent = data.data.coupon_code;
                        document.getElementById('nvqp-result-shop').href = data.data.product_url;
                        goToStep(4);
                    } else {
                        errorBox.textContent = (data.data && data.data.message) || 'Something went wrong. Try again.';
                        errorBox.classList.add('show');
                        btn.disabled = false;
                        btn.textContent = 'Unlock my 10% off →';
                    }
                })
                .catch(function(err) {
                    errorBox.textContent = 'Network error. Please try again.';
                    errorBox.classList.add('show');
                    btn.disabled = false;
                    btn.textContent = 'Unlock my 10% off →';
                });
        });
    })();
    </script>
    <?php
}
