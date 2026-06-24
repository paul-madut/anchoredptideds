<?php global $product; ?>
<div <?php wc_product_class('nv-shop-card'); ?>>
<a href="<?php the_permalink(); ?>">
<?php if($product->is_on_sale()): ?><span class="nv-shop-badge"><?php esc_html_e('Sale','natty-vision'); ?></span><?php endif; ?>
<div class="nv-shop-thumb"><?php echo woocommerce_get_product_thumbnail(); ?></div>
<h3 class="nv-shop-title"><?php the_title(); ?></h3>
<p class="nv-shop-price"><?php echo $product->get_price_html(); ?></p>
</a></div>
