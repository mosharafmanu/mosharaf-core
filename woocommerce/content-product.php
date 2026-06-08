<?php
/**
 * Product card template — used in the shop/category archive loop.
 *
 * @package mosharaf-core
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$permalink = get_permalink();
?>
<div <?php wc_product_class( 'wc-product-card', $product ); ?>>

	<a href="<?php echo esc_url( $permalink ); ?>" class="wc-product-card__image" tabindex="-1" aria-hidden="true">
		<?php woocommerce_show_product_loop_sale_flash(); ?>
		<?php woocommerce_template_loop_product_thumbnail(); ?>
	</a>

	<div class="wc-product-card__body">
		<h2 class="woocommerce-loop-product__title">
			<a href="<?php echo esc_url( $permalink ); ?>"><?php the_title(); ?></a>
		</h2>
		<?php woocommerce_template_loop_rating(); ?>
		<?php woocommerce_template_loop_price(); ?>
	</div>

	<div class="wc-product-card__footer">
		<?php woocommerce_template_loop_add_to_cart(); ?>
	</div>

</div>
