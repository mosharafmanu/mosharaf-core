<?php
/**
 * Shop toolbar
 *
 * Filter toggle button, WooCommerce result count, and ordering dropdown —
 * laid out in one bar above the product grid. The ordering/result-count
 * hooks are removed from their default loop position by shop-archive.php
 * and reprinted here instead.
 *
 * @package mosharaf-core
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="shop-toolbar">
	<div class="shop-toolbar-inner">
		<div class="filters-section">
			<button class="filter-toggle-btn" aria-label="<?php esc_attr_e( 'Toggle Filters', 'mosharaf-core' ); ?>">
				<div class="filters-label">
					<span class="filter-icon"><?php get_template_part( 'assets/svgs/filter-icon' ); ?></span>
					<span><?php esc_html_e( 'Filters', 'mosharaf-core' ); ?></span>
				</div>
				<div class="filter-toggle-text-wrapper">
					<span class="filter-toggle-text"><?php esc_html_e( 'Hide filters', 'mosharaf-core' ); ?></span>
					<span class="filter-icon filter-icon-close">
						<?php get_template_part( 'assets/svgs/close-icon' ); ?>
					</span>
					<span class="filter-icon filter-icon-show" style="display: none;">
						<?php get_template_part( 'assets/svgs/angle-right-pagination' ); ?>
					</span>
				</div>
			</button>
		</div>

		<div class="result-count-wrapper">
			<?php woocommerce_result_count(); ?>
		</div>

		<div class="woocommerce-ordering-wrapper">
			<?php woocommerce_catalog_ordering(); ?>
		</div>
	</div>

	<div class="result-count-wrapper-mobile">
		<!-- Result count is moved here on mobile/tablet via mosharaf-core-shop-archive.js -->
	</div>
</div>
