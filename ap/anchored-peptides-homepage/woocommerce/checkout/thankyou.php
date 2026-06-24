<?php
/**
 * Anchored Peptides — branded order-received (thank-you) page.
 * Overrides woocommerce/templates/checkout/thankyou.php.
 *
 * @var WC_Order $order
 * @package AnchoredPeptides
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="ap-ty">
<style>
.ap-ty{font-family:var(--ap-sans,'Hanken Grotesk',sans-serif);background:var(--ap-bg,#ECE7DA);color:var(--ap-ink,#2C2E22);padding:60px 20px 100px;min-height:80vh}
.ap-ty-c{max-width:760px;margin:0 auto}
.ap-ty-badge{display:inline-flex;align-items:center;gap:10px;font-size:11px;letter-spacing:.14em;text-transform:uppercase;font-weight:600;color:var(--ap-olive,#3E412E);background:var(--ap-bg2,#F4F0E6);border:1px solid var(--ap-border,#DCD5C4);padding:9px 16px;border-radius:100px}
.ap-ty-badge .dot{width:9px;height:9px;border-radius:50%;background:var(--ap-green-ok,#3E6F4E)}
.ap-ty h1{font-family:var(--ap-serif,'Newsreader',serif);font-weight:600;font-size:clamp(32px,5vw,48px);line-height:1.05;margin:22px 0 10px}
.ap-ty-sub{color:var(--ap-muted,#6E6A5C);font-size:15px;margin-bottom:32px;max-width:520px}
.ap-ty-card{background:var(--ap-bg2,#F4F0E6);border:1px solid var(--ap-border,#DCD5C4);border-radius:16px;padding:24px;margin-bottom:18px}
.ap-ty-rows{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:16px}
.ap-ty-rows li small{display:block;font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--ap-muted,#6E6A5C);margin-bottom:4px}
.ap-ty-rows li b{font-size:16px;font-family:var(--ap-serif,'Newsreader',serif);font-weight:600}
.ap-ty-note{display:flex;gap:12px;align-items:flex-start;background:var(--ap-bg2,#F4F0E6);border:1px solid var(--ap-border,#DCD5C4);border-radius:12px;padding:16px;font-size:13.5px;color:var(--ap-ink2,#3C3E32)}
.ap-ty-cta{display:inline-block;margin-top:24px;background:var(--ap-olive,#3E412E);color:var(--ap-cream,#F4F0E6);padding:14px 28px;border-radius:40px;font-weight:600;font-size:14px;text-decoration:none}
</style>
<div class="ap-ty-c">
    <?php if ( $order ) : ?>
        <span class="ap-ty-badge"><span class="dot"></span> <?php esc_html_e( 'Order confirmed', 'anchored-peptides' ); ?></span>
        <h1><?php esc_html_e( 'Thank you — your research is on its way.', 'anchored-peptides' ); ?></h1>
        <p class="ap-ty-sub"><?php printf( esc_html__( 'We’ve received order #%s. Orders placed before 2 PM ET ship same-day from our Canadian fulfillment centre.', 'anchored-peptides' ), esc_html( $order->get_order_number() ) ); ?></p>

        <div class="ap-ty-card">
            <ul class="ap-ty-rows">
                <li><small><?php esc_html_e( 'Order number', 'anchored-peptides' ); ?></small><b><?php echo esc_html( $order->get_order_number() ); ?></b></li>
                <li><small><?php esc_html_e( 'Date', 'anchored-peptides' ); ?></small><b><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></b></li>
                <li><small><?php esc_html_e( 'Total', 'anchored-peptides' ); ?></small><b><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></b></li>
                <li><small><?php esc_html_e( 'Payment', 'anchored-peptides' ); ?></small><b><?php echo esc_html( $order->get_payment_method_title() ); ?></b></li>
            </ul>
        </div>

        <div class="ap-ty-note">
            <span style="font-size:20px">🇨🇦</span>
            <span><b><?php esc_html_e( 'Canadian domestic fulfillment.', 'anchored-peptides' ); ?></b> <?php esc_html_e( 'You’ll receive a tracking email once your order ships. For research use only — not for human consumption.', 'anchored-peptides' ); ?></span>
        </div>

        <a class="ap-ty-cta" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Continue browsing →', 'anchored-peptides' ); ?></a>
    <?php else : ?>
        <h1><?php esc_html_e( 'Thank you', 'anchored-peptides' ); ?></h1>
        <p class="ap-ty-sub"><?php esc_html_e( 'Your order has been received.', 'anchored-peptides' ); ?></p>
    <?php endif; ?>
</div>
</div>
