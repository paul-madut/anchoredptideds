<?php
/**
 * Thankyou page — Natty Vision branded override
 *
 * Replaces the default WooCommerce thank-you template.
 * Original: woocommerce/templates/checkout/thankyou.php
 *
 * @var WC_Order $order
 */

if (!defined('ABSPATH')) exit;
?>

<style>
:root{--bg:#f2f0eb;--bg2:#e9e7e1;--bg-card:#eae8e2;--sage:#c5d4c0;--sage-s:#dce5d8;--sage-d:#a8bfa2;--dark:#1a1e1c;--dark2:#232826;--green:#2d6a4f;--green-l:#40916c;--green-b:#52b788;--text:#1a1e1c;--t2:#4a4f4c;--t3:#7a7f7c;--ti:#f2f0eb;--ti2:#b0aea8;--brd:#d4d2cc;--brd2:#c4c2bc;--r:16px;--rs:12px;--rx:8px}
.nv-ty{font-family:'Neue Montreal',-apple-system,Helvetica,sans-serif;background:var(--bg);color:var(--text);-webkit-font-smoothing:antialiased;padding:60px 20px 100px;min-height:90vh}
.nv-ty *{box-sizing:border-box}
.nv-ty-c{max-width:1100px;margin:0 auto}

/* Hero confirmation */
.nv-ty-hero{display:flex;flex-direction:column;align-items:flex-start;gap:36px;margin-bottom:64px}
.nv-success{display:flex;align-items:center;gap:18px;font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.15em;color:var(--green);background:var(--sage-s);border:1px solid var(--sage);padding:10px 18px 10px 14px;border-radius:100px;opacity:0;transform:translateY(20px);animation:nv-fadeup 1s cubic-bezier(.16,1,.3,1) .1s both}
.nv-success-dot{width:10px;height:10px;border-radius:50%;background:var(--green);position:relative;flex-shrink:0}
.nv-success-dot::before{content:'';position:absolute;inset:-6px;border:2px solid var(--green);border-radius:50%;animation:nv-pulse-ring 2s cubic-bezier(.16,1,.3,1) infinite}
@keyframes nv-pulse-ring{0%{transform:scale(.6);opacity:1}100%{transform:scale(1.6);opacity:0}}

.nv-ty-h{font-family:'Instrument Serif',serif;font-size:clamp(48px,7vw,96px);font-weight:400;line-height:1;letter-spacing:-.03em;opacity:0;transform:translateY(28px);animation:nv-fadeup 1.1s cubic-bezier(.16,1,.3,1) .25s both;margin:0}
.nv-ty-h em{font-style:italic;color:var(--green)}
.nv-ty-sub{font-size:17px;line-height:1.6;color:var(--t2);max-width:580px;opacity:0;transform:translateY(20px);animation:nv-fadeup 1.1s cubic-bezier(.16,1,.3,1) .4s both;margin:0}
.nv-ty-sub strong{color:var(--text);font-weight:500}
.nv-ty-orderno{font-family:'DM Mono',monospace;font-size:13px;letter-spacing:.08em;color:var(--t3);text-transform:uppercase;opacity:0;animation:nv-fadeup 1.1s cubic-bezier(.16,1,.3,1) .5s both}
.nv-ty-orderno strong{color:var(--text);font-weight:500;letter-spacing:.04em}

@keyframes nv-fadeup{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

/* Layout */
.nv-ty-grid{display:grid;grid-template-columns:1fr 1.1fr;gap:48px;align-items:start;opacity:0;animation:nv-fadeup 1s cubic-bezier(.16,1,.3,1) .6s both}

/* Left col — order meta + addresses */
.nv-ty-meta{display:grid;grid-template-columns:1fr 1fr;gap:24px;padding:28px 0;border-top:1px solid var(--brd);border-bottom:1px solid var(--brd);margin-bottom:36px}
.nv-meta-l{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:var(--t3);margin-bottom:6px}
.nv-meta-v{font-family:'Instrument Serif',serif;font-size:24px;line-height:1.2;font-weight:400;letter-spacing:-.01em}
.nv-meta-v.mono{font-family:'DM Mono',monospace;font-size:18px;letter-spacing:.02em}

.nv-ty-payment{margin-bottom:36px}
.nv-ty-payment-l{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:var(--t3);margin-bottom:10px}
.nv-ty-payment-v{font-size:15px;font-weight:500;margin-bottom:14px}
.nv-ty-payment img{max-height:24px;margin-right:8px;vertical-align:middle}

.nv-ty-addrs{display:grid;grid-template-columns:1fr 1fr;gap:32px}
.nv-addr-l{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.12em;color:var(--t3);margin-bottom:12px}
.nv-addr address{font-style:normal;font-size:14px;line-height:1.7;color:var(--t2)}
.nv-addr address .name{color:var(--text);font-weight:500;display:block;margin-bottom:2px}
.nv-addr-contact{margin-top:14px;padding-top:14px;border-top:1px solid var(--brd);font-size:13px;color:var(--t2);line-height:1.7}
.nv-addr-contact a{color:var(--t2);text-decoration:none}
.nv-addr-contact a:hover{color:var(--green)}
.nv-addr-contact .ico{display:inline-block;width:14px;height:14px;margin-right:8px;vertical-align:middle;color:var(--t3)}

/* Right col — order summary card */
.nv-ty-summary{background:var(--sage-s);border:1px solid var(--sage);border-radius:24px;padding:32px;position:sticky;top:24px}
.nv-summary-h{font-family:'Instrument Serif',serif;font-size:24px;font-weight:400;letter-spacing:-.01em;margin-bottom:24px}
.nv-summary-h em{font-style:italic;color:var(--green)}

.nv-item{display:flex;gap:14px;padding:14px 0;border-bottom:1px solid rgba(168,191,162,.4)}
.nv-item:first-of-type{padding-top:0}
.nv-item-img{width:64px;height:64px;border-radius:10px;background:var(--bg);overflow:hidden;flex-shrink:0;display:flex;align-items:center;justify-content:center;border:1px solid rgba(168,191,162,.5)}
.nv-item-img img{width:100%;height:100%;object-fit:cover}
.nv-item-img .ic{font-size:24px;opacity:.2;color:var(--green)}
.nv-item-body{flex:1;min-width:0}
.nv-item-name{font-size:14px;font-weight:500;line-height:1.3;margin-bottom:4px}
.nv-item-meta{font-family:'DM Mono',monospace;font-size:10px;color:var(--t2);letter-spacing:.04em;line-height:1.5}
.nv-item-price{font-family:'DM Mono',monospace;font-size:13px;color:var(--text);font-weight:500;margin-top:6px}

.nv-totals{padding-top:18px;margin-top:8px}
.nv-totals .row{display:flex;justify-content:space-between;align-items:baseline;padding:8px 0;font-size:14px;color:var(--t2)}
.nv-totals .row.total{padding-top:14px;margin-top:8px;border-top:1px solid rgba(168,191,162,.5);font-size:20px;font-family:'Instrument Serif',serif;font-weight:400;color:var(--text)}
.nv-totals .row.total .v{font-size:22px}
.nv-totals .v{font-family:'DM Mono',monospace;color:var(--text);font-weight:500}
.nv-totals .v small{font-family:'Neue Montreal',sans-serif;color:var(--t3);font-weight:400;font-size:11px;margin-left:6px;text-transform:uppercase;letter-spacing:.05em}

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
.nv-status-step.current .nv-status-dot{background:var(--green-b);border-color:var(--green-b);box-shadow:0 0 0 4px rgba(82,183,136,.2)}
.nv-status-step.current .nv-status-dot::after{content:'';position:absolute;inset:-4px;border:2px solid var(--green-b);border-radius:50%;animation:nv-pulse-ring 2s cubic-bezier(.16,1,.3,1) infinite}
.nv-status-label{font-family:'DM Mono',monospace;font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:var(--t3);line-height:1.3}
.nv-status-step.done .nv-status-label,.nv-status-step.current .nv-status-label{color:var(--text)}

/* Failed/cancelled state */
.nv-failed{padding:24px;background:#fef3c7;border:1px solid #fbbf24;border-radius:16px;color:#78350f;margin-bottom:32px;font-size:14px;line-height:1.6}
.nv-failed strong{display:block;margin-bottom:6px;color:#451a03;font-size:15px}
.nv-failed a{color:#78350f;text-decoration:underline}

@media(max-width:900px){
.nv-ty{padding:40px 20px 60px}
.nv-ty-grid{grid-template-columns:1fr;gap:32px}
.nv-ty-summary{position:relative;top:0}
.nv-ty-meta{grid-template-columns:1fr 1fr;gap:18px;padding:20px 0;margin-bottom:28px}
.nv-meta-v{font-size:20px}
.nv-ty-addrs{grid-template-columns:1fr;gap:24px}

/* Status timeline — vertical stack on mobile */
.nv-status{padding:24px}
.nv-status-steps{grid-template-columns:1fr;gap:0}
.nv-status-step{flex-direction:row;align-items:center;gap:14px;padding:0 0 18px 0;padding-right:0}
.nv-status-step::after{top:14px;left:6px;right:auto;bottom:-4px;width:2px;height:auto;border-radius:1px}
.nv-status-step:last-child{padding-bottom:0}
.nv-status-step:last-child::after{display:none}
.nv-status-step .nv-status-dot{margin-top:0}
.nv-status-label{font-size:12px;letter-spacing:.06em;flex:1}
}
@media(max-width:480px){
.nv-ty-h{font-size:48px}
.nv-ty-sub{font-size:15px}
.nv-ty-summary{padding:24px 22px;border-radius:18px}
.nv-ty-meta{grid-template-columns:1fr}
}
</style>

<div class="nv-ty">
<div class="nv-ty-c">

<?php if ($order) : ?>

    <?php if ($order->has_status('failed')) : ?>
        <!-- Hero (failed state) -->
        <div class="nv-ty-hero">
            <div class="nv-success" style="background:#fef3c7;border-color:#fbbf24;color:#78350f">
                <span class="nv-success-dot" style="background:#d4a94c"></span>
                Payment unsuccessful
            </div>
            <h1 class="nv-ty-h">Something <em>went wrong</em></h1>
            <div class="nv-failed">
                <strong>Your payment didn't go through.</strong>
                Please try a different payment method or contact us for help. Your order has been saved.
            </div>
            <p>
                <a class="bd" href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" style="font-family:'DM Mono',monospace;font-size:11px;text-transform:uppercase;letter-spacing:.1em;background:var(--dark);color:var(--ti);padding:14px 28px;border-radius:8px;text-decoration:none">Try payment again →</a>
            </p>
        </div>

    <?php else : ?>

        <!-- Hero -->
        <div class="nv-ty-hero">
            <div class="nv-success">
                <span class="nv-success-dot"></span>
                Order confirmed
            </div>
            <h1 class="nv-ty-h">Thank <em>you</em>.</h1>
            <p class="nv-ty-sub">
                <?php if ($order->has_status('on-hold')) : ?>
                    Your order is on hold while we wait for payment confirmation. We'll send you an email update as soon as it's processed.
                <?php elseif ($order->has_status('processing')) : ?>
                    We've received your order and we're getting it ready. A confirmation email is on its way to <strong><?php echo esc_html($order->get_billing_email()); ?></strong>.
                <?php else : ?>
                    Your order has been received and a confirmation email is on its way to <strong><?php echo esc_html($order->get_billing_email()); ?></strong>.
                <?php endif; ?>
            </p>
            <div class="nv-ty-orderno">Order &nbsp;<strong>#<?php echo esc_html($order->get_order_number()); ?></strong></div>
        </div>

        <!-- Main grid -->
        <div class="nv-ty-grid">

            <!-- Left: meta + addresses -->
            <div>

                <div class="nv-ty-meta">
                    <div>
                        <div class="nv-meta-l">Date</div>
                        <div class="nv-meta-v"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></div>
                    </div>
                    <div>
                        <div class="nv-meta-l">Total</div>
                        <div class="nv-meta-v mono"><?php echo wp_kses_post($order->get_formatted_order_total()); ?></div>
                    </div>
                </div>

                <div class="nv-ty-payment">
                    <div class="nv-ty-payment-l">Payment method</div>
                    <div class="nv-ty-payment-v"><?php echo wp_kses_post($order->get_payment_method_title()); ?></div>
                    <?php
                    // Render gateway-specific icons via standard WC filter so any gateway plugin's logos show up
                    $icons = apply_filters('woocommerce_gateway_icon', '', $order->get_payment_method());
                    if ($icons) {
                        echo '<div>' . wp_kses_post($icons) . '</div>';
                    }
                    ?>
                </div>

                <div class="nv-ty-addrs">
                    <?php if ($order->get_formatted_billing_address()) : ?>
                        <div class="nv-addr">
                            <div class="nv-addr-l">Billing address</div>
                            <address>
                                <span class="name"><?php echo esc_html($order->get_formatted_billing_full_name()); ?></span>
                                <?php echo wp_kses_post(str_replace($order->get_formatted_billing_full_name() . '<br/>', '', $order->get_formatted_billing_address())); ?>
                            </address>
                            <div class="nv-addr-contact">
                                <?php if ($order->get_billing_phone()) : ?>
                                    <div>
                                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <a href="tel:<?php echo esc_attr($order->get_billing_phone()); ?>"><?php echo esc_html($order->get_billing_phone()); ?></a>
                                    </div>
                                <?php endif; ?>
                                <?php if ($order->get_billing_email()) : ?>
                                    <div>
                                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                        <a href="mailto:<?php echo esc_attr($order->get_billing_email()); ?>"><?php echo esc_html($order->get_billing_email()); ?></a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $order->get_formatted_shipping_address()) : ?>
                        <div class="nv-addr">
                            <div class="nv-addr-l">Shipping address</div>
                            <address>
                                <span class="name"><?php echo esc_html($order->get_formatted_shipping_full_name()); ?></span>
                                <?php echo wp_kses_post(str_replace($order->get_formatted_shipping_full_name() . '<br/>', '', $order->get_formatted_shipping_address())); ?>
                            </address>
                            <?php if ($order->get_shipping_phone()) : ?>
                                <div class="nv-addr-contact">
                                    <div>
                                        <svg class="ico" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                        <a href="tel:<?php echo esc_attr($order->get_shipping_phone()); ?>"><?php echo esc_html($order->get_shipping_phone()); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Status timeline -->
                <?php
                $status = $order->get_status();
                $is_done = in_array($status, ['completed'], true);
                $is_processing = in_array($status, ['processing', 'completed'], true);
                $is_paid = !in_array($status, ['pending', 'failed', 'cancelled'], true);
                ?>
                <div class="nv-status">
                    <div class="nv-status-h">Order status</div>
                    <div class="nv-status-steps">
                        <div class="nv-status-step done">
                            <span class="nv-status-dot"></span>
                            <span class="nv-status-label">Order placed</span>
                        </div>
                        <div class="nv-status-step <?php echo $is_paid ? 'done' : 'current'; ?>">
                            <span class="nv-status-dot"></span>
                            <span class="nv-status-label">Payment received</span>
                        </div>
                        <div class="nv-status-step <?php echo $is_done ? 'done' : ($is_processing ? 'current' : ''); ?>">
                            <span class="nv-status-dot"></span>
                            <span class="nv-status-label">Preparing shipment</span>
                        </div>
                        <div class="nv-status-step <?php echo $is_done ? 'current' : ''; ?>">
                            <span class="nv-status-dot"></span>
                            <span class="nv-status-label">On the way</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right: order summary -->
            <div class="nv-ty-summary">
                <h2 class="nv-summary-h">Your <em>order</em></h2>

                <?php
                foreach ($order->get_items() as $item_id => $item) :
                    $product = $item->get_product();
                    if (!$product) continue;
                    $thumb_id = $product->get_image_id();
                    $thumb_url = $thumb_id ? wp_get_attachment_image_url($thumb_id, 'thumbnail') : '';
                    $meta_html = wc_display_item_meta($item, ['echo' => false, 'separator' => ' · ']);
                ?>
                    <div class="nv-item">
                        <div class="nv-item-img">
                            <?php if ($thumb_url) : ?>
                                <img src="<?php echo esc_url($thumb_url); ?>" alt="">
                            <?php else : ?>
                                <span class="ic">⬡</span>
                            <?php endif; ?>
                        </div>
                        <div class="nv-item-body">
                            <div class="nv-item-name"><?php echo esc_html($item->get_name()); ?></div>
                            <?php if ($meta_html) : ?>
                                <div class="nv-item-meta"><?php echo wp_kses_post($meta_html); ?></div>
                            <?php endif; ?>
                            <div class="nv-item-price">
                                <?php echo wp_kses_post(wc_price($order->get_line_subtotal($item, false, false) / max($item->get_quantity(), 1))); ?>
                                &nbsp;×&nbsp; <?php echo esc_html($item->get_quantity()); ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="nv-totals">
                    <?php
                    foreach ($order->get_order_item_totals() as $key => $total) :
                        $is_total_row = ($key === 'order_total');
                    ?>
                        <div class="row<?php echo $is_total_row ? ' total' : ''; ?>">
                            <span><?php echo esc_html($total['label']); ?></span>
                            <span class="v"><?php echo wp_kses_post($total['value']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

    <?php endif; ?>

<?php else : ?>
    <div class="nv-ty-hero">
        <h1 class="nv-ty-h">Thank <em>you</em>.</h1>
        <p class="nv-ty-sub">Your order has been received.</p>
    </div>
<?php endif; ?>

</div>
</div>

<?php do_action('woocommerce_thankyou', $order ? $order->get_id() : 0); ?>
