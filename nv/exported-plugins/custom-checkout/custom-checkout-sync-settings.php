<?php
/**
 * Plugin Name: Custom Checkout
 * Description: Fetches cart data on checkout and redirects to a custom URL.
 * Version: 1.0
 * Author: Kodrite
 */

if (!defined('ABSPATH')) exit;

define('CCS_PATH', plugin_dir_path(__FILE__));

/**
 * ACTIVATION HOOK (MUST BE OUTSIDE CLASS)
 */
function ccs_activate_plugin() {
    // activation logic here
}
register_activation_hook(__FILE__, 'ccs_activate_plugin');


// add_action('woocommerce_store_api_checkout_order_processed', 'ccs_checkout_order_processed', 10, 1);
//add_action('woocommerce_store_api_checkout_update_order_from_request', 'ccs_checkout_order_processed', 10, 1);
add_action('get_header', 'ccs_checkout_order_processed', 10, 1);



function ccs_checkout_order_processed($order) {
    global $wp;
    if (isset($_GET['order']) && isset($_GET['ccsback']) && $_GET['ccsback'] == 'returned') {
        var_dump('reached');
        die('00000');
    }
    
    
    if ($_GET['key'] && $_GET['ccs_pay'] === 'completed') {
        $orderID = $wp->query_vars['order-received'];
        
        if ($orderID && $orderID != 0 && $orderID !== null) {
            return;
        }
    }
    
    if(is_page(13)){
        
        $order_id = absint($wp->query_vars['order-received']);
        $order = wc_get_order( $order_id );
        
        $selectedPaymentMethod = $order->get_payment_method();
        
        if ($selectedPaymentMethod === "advanced_emt") {
            $order->update_status('on-hold', 'Order awaiting payment verification.');
            $order_key = $order->get_order_key();
            
//             $thankYouURL = $order->get_checkout_order_received_url().'&ccs_pay=completed';
			
			$thankYouURL = "https://nattyvision.com/ethankyou/?key=".$order_key."&ccs_pay=completed";

            wp_safe_redirect($thankYouURL, 301);
        } else {
        
            try {

                $order_id = $order->get_id();

                $items_data = [];

                $currency_code = $order->get_currency();

                foreach ($order->get_items() as $item) {
                    $product = $item->get_product();

                    $image_id = $product->get_image_id();
                    $image_url = wp_get_attachment_url($image_id);

                    $items_data[] = [
                        'name'  => $item->get_name(),
                        'qty'   => $item->get_quantity(),
                        'price' => $product ? $product->get_price() : 0,
                        'image' => $image_url,
                    ];
                }

                // ✅ Shipping
                $shipping_data = [];
                foreach ($order->get_shipping_methods() as $shipping) {
                    $shipping_data[] = [
                        'method' => $shipping->get_name(),
                        'total'  => wc_format_decimal($shipping->get_total(), 2),
                    ];
                }

                // ✅ Coupons
                $coupons_data = [];
                foreach ($order->get_coupon_codes() as $code) {

                    $coupon = new WC_Coupon($code);

                    $coupons_data[] = [
                        'code'     => $code,
                        'type'     => $coupon->get_discount_type(),
                        'amount'   => wc_format_decimal($coupon->get_amount(), 2),
                        'discount' => wc_format_decimal($order->get_discount_total(), 2),
                    ];
                }

                // ✅ Taxes
                $taxes_data = [];
                foreach ($order->get_tax_totals() as $tax) {
                    $taxes_data[] = [
                        'label'  => $tax->label,
                        'amount' => wc_format_decimal($tax->amount, 2),
                    ];
                }

                // ✅ Payload
                $payload = [
                    'siteurl'  => site_url(),
                    'order_id' => $order_id,

                    'total'    => wc_format_decimal($order->get_total(), 2),
                    'subtotal' => wc_format_decimal($order->get_subtotal(), 2),
                    'shipping_total' => wc_format_decimal($order->get_shipping_total(), 2),
                    'shipping'       => $shipping_data,
                    'tax_total' => wc_format_decimal($order->get_total_tax(), 2),
                    'taxes'     => $taxes_data,
                    'discount_total' => wc_format_decimal($order->get_discount_total(), 2),
                    'coupons'        => $coupons_data,
                    'currency' => get_woocommerce_currency_symbol($currency_code),
                    'name'  => $order->get_formatted_billing_full_name(),
                    'email' => $order->get_billing_email(),
                    'shipping_address' => $order->get_formatted_shipping_address(),
                    'items' => $items_data,
                ];

                // error_log('Payload: ' . print_r($payload, true));

                // ✅ BLOCKING request to get response
                $response = wp_remote_post('https://stashvpn.com/checkoutprocess.php', [
                    'timeout'  => 10,
                    'blocking' => true,
                    'headers'  => [
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode($payload)
                ]);

                // ✅ Log response properly
                if (is_wp_error($response)) {
                    error_log('API ERROR: ' . $response->get_error_message());
                } else {
                    error_log('API RESPONSE: ' . print_r($response, true));

                    $body = wp_remote_retrieve_body($response);
                    error_log('API BODY: ' . $body);

                    $status = wp_remote_retrieve_response_code($response);
                    // error_log('API STATUS: ' . $status);

                    $responseData = json_decode($body, true);
                    error_log('JSON Decoded BODY: ' . print_r($responseData, true));

                    if ($responseData['status'] == true) {
                        $redirectURL = $responseData['redirect_url'];

                        error_log('URL after string Replaced: ' . $redirectURL);

                        wp_redirect( $redirectURL, 301 );
                        // echo '<script type="text/javascript">window.location.href="' . $redirectURL . '";</script>';
                        // return $redirectURL;

        //                 $order->update_meta_data('_ccs_redirect_url', $redirectURL);
        //                 $order->save();

                        // exit;
                    }
                }

            } catch (Throwable $e) {
                error_log('Custom Hook Error: ' . $e->getMessage());
            }
        }
    }
}

// add_filter('woocommerce_get_checkout_order_received_url', 'ccs_custom_redirect', 10, 2);

// function ccs_custom_redirect($url, $order) {

//     $redirect = $order->get_meta('_ccs_redirect_url');

//     if (!empty($redirect)) {
//         return $redirect; // ✅ FINAL redirect happens here
//     }

//     return $url;
// }

add_action('init', 'ccs_update_order_status');

function ccs_update_order_status() {
    

    if( $_GET['orderid'] != 0 && $_GET['ccs_payment'] === 'completed') {

        $orderID = $_GET['orderid'];

        if (!$orderID) {
            return;
        }

        $order = wc_get_order($orderID);

        if ($order && $order->get_status() !== 'processing') {
            $order->update_status('processing', 'Order updated programmatically.');
            
            $return_url = $order->get_checkout_order_received_url().'&ccs_pay=completed';
            
            wp_safe_redirect($return_url);
        }
    }
}


add_action('wp_loaded', "ccs_checkout_back");

function ccs_checkout_back () {

    if (isset($_GET['order']) && isset($_GET['ccsback']) && $_GET['ccsback'] == 'returned') {

        $order_id = absint($_GET['order']);
        $order    = wc_get_order($order_id);

        if (!$order) {
            return;
        }

        // Only restore for on-hold orders
        if (!$order->has_status('on-hold')) {
            return;
        }

        // Ensure cart exists
        if (!WC()->cart) {
            wc_load_cart();
        }

        // Empty existing cart
        WC()->cart->empty_cart();

        /*
         * Restore Products
         */
        foreach ($order->get_items() as $item) {

            $product_id   = $item->get_product_id();
            $variation_id = $item->get_variation_id();
            $quantity     = $item->get_quantity();
            
            $variation = array();

            // For variation products
            if ($variation_id) {

                $product = wc_get_product($variation_id);

                if ($product && $product->is_type('variation')) {
                    $variation = $product->get_variation_attributes();
                }
            }

            WC()->cart->add_to_cart(
                $product_id,
                $quantity,
                $variation_id,
                $variation
            );
        }

        /*
         * Restore Coupons
         */
        $coupons = $order->get_coupon_codes();

        if (!empty($coupons)) {

            foreach ($coupons as $coupon_code) {
                WC()->cart->apply_coupon($coupon_code);
            }
        }

        /*
         * Recalculate totals
         */
        WC()->cart->calculate_totals();
        
        /*
         * Prevent loop
         */
        wp_safe_redirect(wc_get_checkout_url());
        exit;
    }
}

add_filter('woocommerce_gateway_title', 'custom_gateway_icons_parallel', 10, 2);

function custom_gateway_icons_parallel($title, $gateway_id){

    // Replace this with your gateway ID
    if($gateway_id == 'bacs'){ 

        $icons = '
        <span class="custom-card-icons">
            <img src="https://nattyvision.com/wp-content/uploads/2026/05/icon-1.svg">
            <img src="https://nattyvision.com/wp-content/uploads/2026/05/icon-2.svg">
            <img src="https://nattyvision.com/wp-content/uploads/2026/05/icon-3-ae.svg">
            <img src="https://nattyvision.com/wp-content/uploads/2026/05/icon-4.svg">
        </span>';

        $title = 'Pay with Credit or Debit Card ' . $icons;
    }

    return $title;
}

add_action('wp_head', function(){
if(is_checkout()){
?>
<style>
/* PAYMENT METHODS CONTAINER */
.woocommerce-checkout #payment ul.payment_methods {
    display: flex !important;
    flex-direction: column !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* MOVE CREDIT CARD TO TOP */
.woocommerce-checkout #payment li.wc_payment_method:has(input[id*="payment_method_bacs"])
{
    order: -1 !important;
}

/* PAYMENT METHOD BOX */
.woocommerce-checkout #payment li.wc_payment_method {
    border: 1px solid #ddd !important;
/*     margin-bottom: 15px !important; */
    background: #fff !important;
    padding: 0 !important;
    overflow: hidden !important;
}

/* HEADER ROW */
.woocommerce-checkout #payment li.wc_payment_method > label {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    width: 100% !important;
    padding: 22px 20px !important;
    font-size: 16px !important;
    font-weight: 400 !important;
    cursor: pointer !important;
    box-sizing: border-box !important;
}

/* RADIO BUTTON */
.woocommerce-checkout #payment input[type="radio"] {
    margin-right: 14px !important;
}

/* PAYMENT ICONS */
.woocommerce-checkout #payment li.wc_payment_method label img {
    max-height: 32px !important;
    width: auto !important;
    margin-left: 10px !important;
}

/* DESCRIPTION BOX */
.woocommerce-checkout #payment div.payment_box {
    background: #fff !important;
    border-top: 1px solid #e5e5e5 !important;
    padding: 25px !important;
    margin: 0 !important;
    font-size: 16px !important;
}

/* REMOVE DEFAULT TRIANGLE */
.woocommerce-checkout #payment div.payment_box::before {
    display: none !important;
}
	
	/* Interac row */
.payment_method_advanced_emt .payment_method_title_wrap label {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
}

/* Move text left */
.payment_method_advanced_emt .payment_method_title_wrap label div span img {
    order: 2;
    margin-left: auto;
}
</style>
<?php
}
});

add_action('wp_head', 'custom_thankyou_page_css');

function custom_thankyou_page_css() {

    if ( is_order_received_page() ) {
        ?>
        <style>
            .nv-totals > .row:nth-child(2) .v {
                margin-left: 6.5rem;
            }
            
            .nv-totals > .row:nth-child(4) .v {
                margin-left: 3rem;
            }
            
            .nv-totals > .row:last-child {
                display: none;
            }
            
            .woocommerce-order-details{
                display: none;
            }

            .woocommerce-customer-details{
                display: none;
            }

        </style>
        <?php
    }
}

function custom_payment_icons_admin($order){
?>
<script>
jQuery(function($){

setTimeout(function(){

let html = 'Payment via Pay with Credit or Debit Card <span class="custom-card-icons"> <img src="https://nattyvision.com/wp-content/uploads/2026/05/icon-1.svg"> <img src="https://nattyvision.com/wp-content/uploads/2026/05/icon-2.svg"> <img src="https://nattyvision.com/wp-content/uploads/2026/05/icon-3-ae.svg"> <img src="https://nattyvision.com/wp-content/uploads/2026/05/icon-4.svg"> </span>';

$('.order_data_header .order_data_header_column p').first().html(html);

},500);

});
</script>
<?php
}

add_action('woocommerce_admin_order_data_after_order_details', 'custom_payment_icons_admin');

add_action('admin_head', 'custom_wc_order_edit_css');

function custom_wc_order_edit_css() {

    // Only HPOS order edit page
    if (
        isset($_GET['page'], $_GET['action'], $_GET['id']) &&
        $_GET['page'] === 'wc-orders' &&
        $_GET['action'] === 'edit'
    ) {
        ?>
        <style>
            #order_data p.order_number {
                display: flex;
                align-items: center;
                gap: 15px;
            }
            .custom-card-icons img{
                height:30px;
                margin-right:4px;
                vertical-align:middle;
            }

            .custom-card-icons{
                display:inline-flex;
                align-items:center;
                gap:4px;
            }

            /* Example extra styling only on order edit page */
            .woocommerce-layout__header{
                background:#fff;
            }
        </style>
        <?php
    }
}

add_action( 'template_redirect', 'force_default_payment_gateway' );
function force_default_payment_gateway(){
    if( is_checkout() && ! is_wc_endpoint_url() ) {
        // Replace 'stripe' with your actual credit card gateway ID
        WC()->session->set( 'chosen_payment_method', 'stripe' );
    }
}

add_filter( 'woocommerce_available_payment_gateways', 'restrict_interac_to_canada' );
function restrict_interac_to_canada( $available_gateways ) {
    // Do not run in the WordPress admin backend
    if ( is_admin() ) {
        return $available_gateways;
    }

    // Replace 'interac' with your actual Interac gateway ID
    $interac_gateway_id = 'advanced_emt'; 

    // Ensure the customer object exists before checking the country
    if ( WC()->customer ) {
        $billing_country = WC()->customer->get_billing_country();
        
        // If the country is NOT Canada ('CA'), remove the Interac gateway
        if ( $billing_country !== 'CA' && isset( $available_gateways[ $interac_gateway_id ] ) ) {
            unset( $available_gateways[ $interac_gateway_id ] );
        }
    }

    return $available_gateways;
}

add_action( 'template_redirect', 'restore_cart_from_pending_order' );

function restore_cart_from_pending_order() {
	if ( isset( $_GET['oid'] ) && $_GET['oid'] > 0) {
		$order_id = absint( $_GET['oid'] );
		$order    = wc_get_order( $order_id );

		// 3. Abort if the order doesn't exist
		if ( ! $order ) {
			return;
		}
		if ( ! $order->has_status( array( 'pending', 'on-hold' ) ) ) {
			return; 
		}
		
		foreach ( $order->get_items() as $item ) {
			// Ensure we pass the correct variation ID if it's a variable product
			$product_id   = $item->get_product_id();
			$variation_id = $item->get_variation_id();
			$quantity     = $item->get_quantity();

			WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
		}

		// 7. Redirect to the clean Checkout page
		// Using wc_get_checkout_url() ensures the '?order_id=' parameter is dropped, 
		// which prevents an infinite redirect loop.
		wp_safe_redirect( wc_get_checkout_url() );
		exit;
	}
}

add_shortcode('etransfer-thankyou', 'ccs_render_etransfer_thankyou');
function ccs_render_etransfer_thankyou() {
    ob_start();
    
    if($_GET['key'] && $_GET['ccs_pay'] === 'completed') {
        $orderKey = $_GET['key'];
        $order_id = wc_get_order_id_by_order_key( $orderKey );
        
        $order = wc_get_order( $order_id );
        
        $currency_code = $order->get_currency();
        $currency = get_woocommerce_currency_symbol($currency_code);
        
        $formatted = (new DateTime($order->get_date_created()))->format('M d, Y');
		
		$subtotal = 0.0;
        
        $curlURL = 'https://api.exchangerate.host/convert?access_key=2f13a80b1c2c9dd3f8b3a9109febd02c&from=USD&to=CAD&amount='.$order->get_total();
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $curlURL,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        $exchangeData = json_decode($response);
		
        $exchangeAmount = number_format($exchangeData->result, 2);
		$exchangeRate = number_format($exchangeData->info->quote, 2);

        
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Complete your payment — Natty Vision</title>
<link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=DM+Mono:wght@300;400;500&display=swap" rel="stylesheet">
<link href="https://api.fontshare.com/v2/css?f[]=neue-montreal@400,500,700&display=swap" rel="stylesheet">
<style>
:root{--bg:#f2f0eb;--bg2:#e9e7e1;--bg-card:#eae8e2;--sage:#c5d4c0;--sage-s:#dce5d8;--sage-d:#a8bfa2;--dark:#1a1e1c;--dark2:#232826;--green:#2d6a4f;--green-l:#40916c;--green-b:#52b788;--amber:#b78a2d;--amber-s:#f4e8c8;--amber-bg:#faf2dc;--text:#1a1e1c;--t2:#4a4f4c;--t3:#7a7f7c;--ti:#f2f0eb;--ti2:#b0aea8;--brd:#d4d2cc;--brd2:#c4c2bc;--r:16px;--rs:12px;--rx:8px;--rp:100px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Neue Montreal',-apple-system,Helvetica,sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased}

/* Top nav */
.nv-nav{background:rgba(242,240,235,.85);backdrop-filter:blur(20px);border-bottom:1px solid var(--brd);padding:0 40px;position:sticky;top:0;z-index:50}
.nv-nav-i{max-width:1240px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;height:64px}
.nv-nav-l{display:flex;align-items:center;text-decoration:none}
.nv-nav-l svg{height:26px;width:auto;display:block}
.nv-nav-r{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--t2);text-decoration:none}

.nv-ty{padding:60px 20px 100px;min-height:90vh}
.nv-ty-c{max-width:1100px;margin:0 auto}
	
.nv-container > article > h1:first-of-type {
    display: none;
}

/* Hero */
.nv-ty-hero{display:flex;flex-direction:column;align-items:flex-start;gap:36px;margin-bottom:56px}
.nv-pending{display:flex;align-items:center;gap:18px;font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.15em;color:var(--amber);background:var(--amber-bg);border:1px solid var(--amber-s);padding:10px 18px 10px 14px;border-radius:100px;opacity:0;transform:translateY(20px);animation:nv-fadeup 1s cubic-bezier(.16,1,.3,1) .1s both}
.nv-pending-dot{width:10px;height:10px;border-radius:50%;background:var(--amber);position:relative;flex-shrink:0}
.nv-pending-dot::before{content:'';position:absolute;inset:-6px;border:2px solid var(--amber);border-radius:50%;animation:nv-pulse-ring 2s cubic-bezier(.16,1,.3,1) infinite}
@keyframes nv-pulse-ring{0%{transform:scale(.6);opacity:1}100%{transform:scale(1.6);opacity:0}}

.nv-ty-h{font-family:'Instrument Serif',serif;font-size:clamp(48px,7vw,96px);font-weight:400;line-height:1;letter-spacing:-.03em;opacity:0;transform:translateY(28px);animation:nv-fadeup 1.1s cubic-bezier(.16,1,.3,1) .25s both;margin:0}
.nv-ty-h em{font-style:italic;color:var(--green)}
.nv-ty-sub{font-size:17px;line-height:1.6;color:var(--t2);max-width:620px;opacity:0;transform:translateY(20px);animation:nv-fadeup 1.1s cubic-bezier(.16,1,.3,1) .4s both;margin:0}
.nv-ty-sub strong{color:var(--text);font-weight:500}
.nv-ty-orderno{font-family:'DM Mono',monospace;font-size:13px;letter-spacing:.08em;color:var(--t3);text-transform:uppercase;opacity:0;animation:nv-fadeup 1.1s cubic-bezier(.16,1,.3,1) .5s both}
.nv-ty-orderno strong{color:var(--text);font-weight:500;letter-spacing:.04em}

@keyframes nv-fadeup{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

/* Main grid */
.nv-ty-grid{display:grid;grid-template-columns:1fr 1.1fr;gap:48px;align-items:start;opacity:0;animation:nv-fadeup 1s cubic-bezier(.16,1,.3,1) .55s both}

/* Left col */
.nv-ty-meta{display:grid;grid-template-columns:1fr 1fr;gap:24px;padding:28px 0;border-top:1px solid var(--brd);border-bottom:1px solid var(--brd);margin-bottom:36px}
.nv-meta-l{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:var(--t3);margin-bottom:6px}
.nv-meta-v{font-family:'Instrument Serif',serif;font-size:24px;line-height:1.2;font-weight:400;letter-spacing:-.01em}
.nv-meta-v.mono{font-family:'DM Mono',monospace;font-size:18px;letter-spacing:.02em}

/* Payment instruction card — the hero of the page */
.nv-pay-card{background:#fff;border:1px solid var(--brd);border-radius:24px;padding:32px;margin-bottom:28px;position:relative;overflow:hidden}
.nv-pay-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--green-b),var(--sage-d),var(--green-b));background-size:200% 100%;animation:nv-shimmer 3s linear infinite}
@keyframes nv-shimmer{0%{background-position:0% 0}100%{background-position:200% 0}}
.nv-pay-card-h{font-family:'Instrument Serif',serif;font-size:22px;font-weight:400;letter-spacing:-.01em;margin-bottom:6px;display:flex;align-items:center;gap:10px}
.nv-pay-card-h em{font-style:italic;color:var(--green)}
.nv-pay-card-sub{font-size:13px;color:var(--t3);line-height:1.6;margin-bottom:26px}

/* Copy fields */
.nv-field{margin-bottom:18px}
.nv-field-l{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:var(--t3);margin-bottom:8px}
.nv-field-box{display:flex;align-items:center;gap:8px;background:var(--bg);border:1px solid var(--brd);border-radius:12px;padding:14px 14px 14px 18px;transition:border-color .25s ease,box-shadow .25s ease}
.nv-field-box:hover{border-color:var(--sage-d)}
.nv-field-v{flex:1;font-family:'DM Mono',monospace;font-size:15px;color:var(--text);font-weight:500;letter-spacing:.01em;word-break:break-all;min-width:0}
.nv-field-v.lg{font-size:18px;font-family:'Instrument Serif',serif;font-weight:400;letter-spacing:.01em}
.nv-copy{flex-shrink:0;display:inline-flex;align-items:center;gap:6px;font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.1em;color:var(--text);background:#fff;border:1px solid var(--brd);border-radius:8px;padding:8px 12px;cursor:pointer;transition:all .2s ease}
.nv-copy:hover{background:var(--sage-s);border-color:var(--sage-d);color:var(--green)}
.nv-copy.ok{background:var(--green);border-color:var(--green);color:#fff}
.nv-copy svg{width:12px;height:12px}

/* Amount field — emphasised */
.nv-field-amount .nv-field-box{background:var(--sage-s);border-color:var(--sage)}
.nv-field-amount .nv-field-v{font-family:'Instrument Serif',serif;font-size:28px;font-weight:400;color:var(--green);letter-spacing:-.01em}
.nv-fx-note{display:flex;align-items:center;gap:8px;margin-top:10px;padding:8px 12px;font-family:'DM Mono',monospace;font-size:11px;color:var(--t3);letter-spacing:.02em;line-height:1.5}
.nv-fx-note svg{width:12px;height:12px;flex-shrink:0;color:var(--green)}
.nv-fx-note strong{color:var(--text);font-weight:500}

/* CTA button */
.nv-paid-btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;background:var(--dark);color:var(--ti);border:none;border-radius:12px;padding:16px;font-family:'Neue Montreal',sans-serif;font-size:14px;font-weight:500;letter-spacing:.01em;cursor:pointer;margin-top:8px;transition:background .25s ease,transform .15s ease}
.nv-paid-btn:hover{background:#000;transform:translateY(-1px)}
.nv-paid-btn:active{transform:translateY(0)}
.nv-paid-btn svg{width:14px;height:14px}

/* Important section */
.nv-important{background:var(--amber-bg);border:1px solid var(--amber-s);border-radius:20px;padding:26px 28px;margin-bottom:36px}
.nv-important-h{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:var(--amber);margin-bottom:14px;display:flex;align-items:center;gap:8px}
.nv-important-h svg{width:14px;height:14px}
.nv-important ul{list-style:none;padding:0;margin:0}
.nv-important li{font-size:13.5px;line-height:1.6;color:var(--t2);padding:7px 0 7px 22px;position:relative}
.nv-important li::before{content:'';position:absolute;left:0;top:14px;width:8px;height:8px;border-radius:2px;background:var(--amber);opacity:.4}
.nv-important li strong{color:var(--text);font-weight:500;font-family:'DM Mono',monospace;font-size:12.5px;letter-spacing:.02em;background:#fff;padding:1px 6px;border-radius:4px;border:1px solid var(--amber-s)}

/* How-to steps */
.nv-howto{margin-bottom:0}
.nv-howto-h{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:var(--t3);margin-bottom:18px}
.nv-howto-steps{display:flex;flex-direction:column;gap:14px}
.nv-howto-step{display:flex;gap:16px;align-items:flex-start}
.nv-howto-num{flex-shrink:0;width:28px;height:28px;border-radius:50%;background:var(--sage-s);border:1px solid var(--sage);font-family:'DM Mono',monospace;font-size:12px;font-weight:500;color:var(--green);display:flex;align-items:center;justify-content:center}
.nv-howto-txt{font-size:14px;line-height:1.6;color:var(--t2);padding-top:3px}
.nv-howto-txt strong{color:var(--text);font-weight:500}

/* Right col — order summary card */
.nv-ty-summary{background:var(--sage-s);border:1px solid var(--sage);border-radius:24px;padding:32px;position:sticky;top:88px}
.nv-summary-h{font-family:'Instrument Serif',serif;font-size:24px;font-weight:400;letter-spacing:-.01em;margin-bottom:24px}
.nv-summary-h em{font-style:italic;color:var(--green)}

.nv-item{display:flex;gap:14px;padding:14px 0;border-bottom:1px solid rgba(168,191,162,.4)}
.nv-item:first-of-type{padding-top:0}
.nv-item-img{width:64px;height:64px;border-radius:10px;background:var(--bg);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:1px solid rgba(168,191,162,.5)}
.nv-item-img .ic{font-size:24px;opacity:.25;color:var(--green)}
.nv-item-body{flex:1;min-width:0}
.nv-item-name{font-size:14px;font-weight:500;line-height:1.3;margin-bottom:4px}
.nv-item-meta{font-family:'DM Mono',monospace;font-size:10px;color:var(--t2);letter-spacing:.04em;line-height:1.5}
.nv-item-price{font-family:'DM Mono',monospace;font-size:13px;color:var(--text);font-weight:500;margin-top:6px}

.nv-totals{padding-top:18px;margin-top:8px}
.nv-totals .row::before {
    content: none !important;
}
.nv-totals .row{display:flex;justify-content:space-between;align-items:baseline;padding:8px 0;font-size:14px;color:var(--t2)}
.nv-totals .row.total{padding-top:14px;margin-top:8px;border-top:1px solid rgba(168,191,162,.5);font-size:20px;font-family:'Instrument Serif',serif;font-weight:400;color:var(--text)}
.nv-totals .row.total .v{font-size:22px}
.nv-totals .v{font-family:'DM Mono',monospace;color:var(--text);font-weight:500}
.nv-totals .v small{font-family:'Neue Montreal',sans-serif;color:var(--t3);font-weight:400;font-size:11px;margin-left:6px;text-transform:uppercase;letter-spacing:.05em}
.nv-totals > .row:nth-child(3) .v {
    margin-left: 6.5rem;
}
.nv-totals > .row:nth-child(5) .v {
    margin-left: 5rem;
}

.nv-cad-row{display:flex;justify-content:space-between;align-items:baseline;margin-top:14px;padding:14px 16px;background:#fff;border:1px dashed var(--sage-d);border-radius:12px;font-size:13px;color:var(--t2)}
.nv-cad-row .v{font-family:'DM Mono',monospace;color:var(--green);font-weight:500;font-size:15px}
.nv-cad-row .v small{font-family:'Neue Montreal',sans-serif;color:var(--green);font-weight:500;font-size:10px;margin-left:4px;text-transform:uppercase;letter-spacing:.06em;opacity:.8}

.nv-summary-note{margin-top:22px;padding-top:20px;border-top:1px solid rgba(168,191,162,.5);font-size:12.5px;color:var(--t2);line-height:1.6}
.nv-summary-note strong{color:var(--green);font-weight:500}

/* Status timeline */
.nv-status{margin-top:48px;padding:32px;background:var(--bg-card);border:1px solid var(--brd);border-radius:20px;opacity:0;animation:nv-fadeup 1s cubic-bezier(.16,1,.3,1) .8s both}
.nv-status-h{font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.12em;color:var(--t3);margin-bottom:20px}
.nv-status-steps{display:grid;grid-template-columns:repeat(4,1fr);gap:8px;position:relative}
.nv-status-step{display:flex;flex-direction:column;align-items:flex-start;gap:8px;position:relative;padding-right:14px}
.nv-status-step::after{content:'';position:absolute;top:6px;left:14px;right:0;height:2px;background:var(--brd);border-radius:1px}
.nv-status-step:last-child::after{display:none}
.nv-status-step.done::after{background:var(--green-b)}
.nv-status-dot{width:14px;height:14px;border-radius:50%;background:var(--bg);border:2px solid var(--brd);position:relative;z-index:1;flex-shrink:0}
.nv-status-step.done .nv-status-dot{background:var(--green-b);border-color:var(--green-b)}
.nv-status-step.current .nv-status-dot{background:var(--amber);border-color:var(--amber);box-shadow:0 0 0 4px rgba(183,138,45,.2)}
.nv-status-step.current .nv-status-dot::after{content:'';position:absolute;inset:-4px;border:2px solid var(--amber);border-radius:50%;animation:nv-pulse-ring 2s cubic-bezier(.16,1,.3,1) infinite}
.nv-status-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--t3);line-height:1.3}
.nv-status-step.done .nv-status-label,.nv-status-step.current .nv-status-label{color:var(--text)}

@media(max-width:900px){
.nv-nav{padding:0 20px}
.nv-ty{padding:40px 20px 60px}
.nv-ty-grid{grid-template-columns:1fr;gap:32px}
.nv-ty-summary{position:relative;top:0}
.nv-ty-meta{grid-template-columns:1fr 1fr;gap:18px;padding:20px 0;margin-bottom:28px}
.nv-meta-v{font-size:20px}
.nv-pay-card{padding:26px 24px}
.nv-important{padding:22px}
.nv-status{padding:24px}
.nv-status-steps{grid-template-columns:1fr;gap:0}
.nv-status-step{flex-direction:row;align-items:center;gap:14px;padding:0 0 18px 0;padding-right:0}
.nv-status-step::after{top:14px;left:6px;right:auto;bottom:-4px;width:2px;height:auto;border-radius:1px}
.nv-status-step:last-child{padding-bottom:0}
.nv-status-step:last-child::after{display:none}
.nv-status-label{font-size:12px;letter-spacing:.06em;flex:1}
}
@media(max-width:480px){
.nv-ty-h{font-size:48px}
.nv-ty-sub{font-size:15px}
.nv-ty-summary{padding:24px 22px;border-radius:18px}
.nv-ty-meta{grid-template-columns:1fr}
.nv-pay-card{padding:24px 20px}
.nv-field-amount .nv-field-v{font-size:24px}
.nv-field-box{padding:12px 12px 12px 14px}
}
</style>
</head>
<body>

<div class="nv-ty">
<div class="nv-ty-c">

  <!-- Hero -->
  <div class="nv-ty-hero">
    <div class="nv-pending">
      <span class="nv-pending-dot"></span>
      Awaiting payment
    </div>
    <h1 class="nv-ty-h">One <em>last</em> step.</h1>
    <p class="nv-ty-sub">
      Your order is reserved. To complete it, send an <strong>Interac e-Transfer in CAD</strong> using the details below. We'll ship within 24 hours of receiving payment, and tracking will be emailed to <strong><?=$order->get_billing_email()?></strong>.
    </p>
    <div class="nv-ty-orderno">Order &nbsp;<strong>#<?=$order_id?></strong></div>
  </div>

  <!-- Main grid -->
  <div class="nv-ty-grid">

    <!-- Left: payment instructions -->
    <div>
      <div class="nv-ty-meta">
        <div>
          <div class="nv-meta-l">Date</div>
          <div class="nv-meta-v"><?=$formatted?></div>
        </div>
        <div>
          <div class="nv-meta-l">Total due</div>
          <div class="nv-meta-v mono"><?=$currency?><?=$exchangeAmount?> <span style="font-size:11px;color:var(--t3);letter-spacing:.04em;font-weight:400">CAD</span></div>
          <div style="font-family:'DM Mono',monospace;font-size:11px;color:var(--t3);margin-top:4px;letter-spacing:.02em">≈ <?=$currency?><?=$order->get_total()?> USD</div>
        </div>
      </div>

      <!-- Payment instructions card -->
      <div class="nv-pay-card">
        <h2 class="nv-pay-card-h">Send <em>e-Transfer</em> to</h2>
        <p class="nv-pay-card-sub">Log into your online banking and send an Interac e-Transfer with the details below. Your order total has been converted to <strong style="color:var(--text);font-weight:500">CAD</strong> — please send the exact CAD amount shown.</p>

        <div class="nv-field">
          <div class="nv-field-l">Recipient email</div>
          <div class="nv-field-box">
            <span class="nv-field-v lg" id="nv-email">admin@nattyvision.com</span>
            <button class="nv-copy" data-copy="admin@nattyvision.com">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              <span class="lbl">Copy</span>
            </button>
          </div>
        </div>

        <div class="nv-field nv-field-amount">
          <div class="nv-field-l">Amount to send</div>
          <div class="nv-field-box">
            <span class="nv-field-v"><?=$currency?><?=$exchangeAmount?> <span style="font-family:'DM Mono',monospace;font-size:11px;color:var(--t3);letter-spacing:.06em;margin-left:6px;text-transform:uppercase;font-weight:400">CAD</span></span>
            <button class="nv-copy" data-copy="<?=$currency?><?=$exchangeAmount?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              <span class="lbl">Copy</span>
            </button>
          </div>
          <div class="nv-fx-note">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 1l4 4-4 4"/><path d="M3 11V9a4 4 0 0 1 4-4h14"/><path d="M7 23l-4-4 4-4"/><path d="M21 13v2a4 4 0 0 1-4 4H3"/></svg>
            <span>Converted from <strong><?=$currency?><?=$order->get_total()?> <?=$currency_code?></strong> at $1 USD = <?=$currency?><?=$exchangeRate?> CAD</span>
          </div>
        </div>

        <div class="nv-field">
          <div class="nv-field-l">Message / Memo</div>
          <div class="nv-field-box">
            <span class="nv-field-v lg">#<?=$order_id?></span>
            <button class="nv-copy" data-copy="#<?=$order_id?>">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              <span class="lbl">Copy</span>
            </button>
          </div>
        </div>

        <button class="nv-paid-btn" id="nv-paid">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
          I've sent the e-Transfer
        </button>
      </div>

      <!-- Important rules -->
      <div class="nv-important">
        <div class="nv-important-h">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
          Important — please follow exactly
        </div>
        <ul>
          <li>Send the exact amount of <strong><?=$currency?><?=$exchangeAmount?> CAD</strong></li>
          <li>Put <strong>#<?=$order_id?></strong> in the message/memo field</li>
          <li>Send only to <strong>admin@nattyvision.com</strong></li>
          <li>Do not include product names or any other information in the memo</li>
          <li>Transfers that don't follow these instructions may be delayed or refunded</li>
        </ul>
      </div>

      <!-- Status timeline -->
      <div class="nv-status">
        <div class="nv-status-h">Order status</div>
        <div class="nv-status-steps">
          <div class="nv-status-step done">
            <span class="nv-status-dot"></span>
            <span class="nv-status-label">Order placed</span>
          </div>
          <div class="nv-status-step current">
            <span class="nv-status-dot"></span>
            <span class="nv-status-label">Awaiting payment</span>
          </div>
          <div class="nv-status-step">
            <span class="nv-status-dot"></span>
            <span class="nv-status-label">Preparing shipment</span>
          </div>
          <div class="nv-status-step">
            <span class="nv-status-dot"></span>
            <span class="nv-status-label">On the way</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: order summary -->
    <div class="nv-ty-summary">
      <h2 class="nv-summary-h">Your <em>order</em></h2>
    
        <?php foreach ($order->get_items() as $item_id => $item): ?>
            <?php
                $product = $item->get_product();

                $image_id = $product->get_image_id();
                $image_url = wp_get_attachment_url($image_id);
	
				$subtotal += ($product->get_price() * $item->get_quantity());

            ?>
            <div class="nv-item">
                <div class="nv-item-img"><span class="ic"><img src="<?= esc_url($image_url) ?>" width="60px" height="60px"/></span></div>
                <div class="nv-item-body">
                    <div class="nv-item-name"><?= esc_html($item->get_name()) ?></div>
<!--                    <div class="nv-item-meta">color: Red &middot; size: M</div> -->
                    <div class="nv-item-price"><?= esc_html($currency) ?><?= esc_html(number_format($product->get_price(), 2)) ?> &nbsp;×&nbsp; <?= intval($item->get_quantity()) ?></div>
                </div>
            </div>
        <?php endforeach; ?>

      <div class="nv-totals">
        <div class="row">
          <span>Subtotal</span>
          <span class="v"><?= esc_html($currency) ?><?= esc_html(number_format($subtotal, 2)) ?></span>
        </div>
        <div class="row">
          <span>Discount</span>
          <span class="v">-<?= esc_html($currency) ?><?= esc_html(number_format($order->get_discount_total(), 2)) ?></span>
        </div>
        <div class="row">
          <span>Shipping</span>
          <span class="v"><?= esc_html($currency) ?><?= esc_html(number_format($order->get_shipping_total(), 2)) ?> <small>via Flat rate</small></span>
        </div>
        <div class="row">
          <span>Flat Tax</span>
          <span class="v"><?= esc_html($currency) ?><?= esc_html(number_format($order->get_total_tax(), 2)) ?></span>
        </div>
        <div class="row total">
          <span>Total</span>
          <span class="v"><?= esc_html($currency) ?><?=$order->get_total()?><small><?=$currency_code?></small></span>
        </div>
        <div class="nv-cad-row">
          <span>Amount to send via e-Transfer</span>
          <span class="v"><?=$currency?><?=$exchangeAmount?> <small>CAD</small></span>
        </div>
      </div>

      <div class="nv-summary-note">
        Need help? Email us at <strong>admin@nattyvision.com</strong> with your order number and we'll get back to you within a few hours.
      </div>
    </div>

  </div>

</div>
</div>

<script>
// Copy buttons
document.querySelectorAll('.nv-copy').forEach(btn=>{
  btn.addEventListener('click',()=>{
    const txt=btn.getAttribute('data-copy');
    navigator.clipboard.writeText(txt).then(()=>{
      const lbl=btn.querySelector('.lbl');
      const orig=lbl.textContent;
      btn.classList.add('ok');
      lbl.textContent='Copied';
      setTimeout(()=>{btn.classList.remove('ok');lbl.textContent=orig},1600);
    });
  });
});

// I've sent button — visual confirmation only
document.getElementById('nv-paid').addEventListener('click',function(){
  this.style.background='var(--green)';
  this.innerHTML='<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Thanks — we\'ll confirm shortly';
  this.disabled=true;
});
</script>

</body>
</html>

<?php
return ob_get_clean();
}
