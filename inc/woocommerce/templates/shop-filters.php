<?php
/**
 * Shop sidebar — product category filters
 *
 * Renders a parent/child product_cat tree as clickable filter buttons.
 * Clicking a filter rewrites the URL with a `filter_category` query
 * argument (handled by mosharaf-core-shop-archive.js); the resulting
 * request is turned into a tax_query by mosharaf_filter_shop_products_by_category()
 * in shop-archive.php — no AJAX, plain links/buttons all the way down.
 *
 * @package mosharaf-core
 */

defined( 'ABSPATH' ) || exit;

// Parse the active filter from the URL.
$current_filters = isset( $_GET['filter_category'] ) ? sanitize_text_field( wp_unslash( $_GET['filter_category'] ) ) : '';
$current_filters = $current_filters ? array_filter( array_map( 'sanitize_title', explode( ',', $current_filters ) ) ) : [];

// On a category page, treat the current term as an active filter too.
$is_category_page      = is_product_taxonomy( 'product_cat' );
$current_category_slug = '';

if ( $is_category_page ) {
	$current_term = get_queried_object();
	if ( $current_term && ! is_wp_error( $current_term ) ) {
		$current_category_slug = $current_term->slug;
		if ( ! in_array( $current_category_slug, $current_filters, true ) ) {
			$current_filters[] = $current_category_slug;
		}
	}
}

$has_active_filters = isset( $_GET['filter_category'] ) && ! empty( $_GET['filter_category'] );
$clear_filters_url  = remove_query_arg( 'filter_category' );

$categories = get_terms( [
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'parent'     => 0,
] );
?>
<div class="product-filters">

	<button class="filters-close-btn" aria-label="<?php esc_attr_e( 'Close Filters', 'mosharaf-core' ); ?>">
		<?php get_template_part( 'assets/svgs/close-icon' ); ?>
	</button>

	<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>

		<?php
		$total_products = wp_count_posts( 'product' )->publish;
		$shop_url       = wc_get_page_permalink( 'shop' );
		?>

		<div class="filter-group">
			<ul class="filter-list">

				<li class="filter-item">
					<a
						href="<?php echo esc_url( $shop_url ); ?>"
						class="filter-link <?php echo ( ! $has_active_filters && ! $is_category_page ) ? 'active' : ''; ?>"
					>
						<span class="filter-name"><?php esc_html_e( 'All Products', 'mosharaf-core' ); ?></span>
						<span class="filter-count">(<?php echo absint( $total_products ); ?>)</span>
					</a>
				</li>

				<?php foreach ( $categories as $category ) : ?>
					<?php
					$child_categories = get_terms( [
						'taxonomy'   => 'product_cat',
						'hide_empty' => true,
						'parent'     => $category->term_id,
					] );

					$has_children = ! empty( $child_categories ) && ! is_wp_error( $child_categories );
					$child_slugs  = $has_children ? wp_list_pluck( $child_categories, 'slug' ) : [];

					$is_active           = in_array( $category->slug, $current_filters, true );
					$has_active_child    = (bool) array_intersect( $child_slugs, $current_filters );
					$is_current_category = $is_category_page && $category->slug === $current_category_slug;
					$is_current_parent   = false;

					if ( $is_category_page && $has_children ) {
						$current_term = get_queried_object();
						if ( $current_term && ! is_wp_error( $current_term ) && (int) $current_term->parent === (int) $category->term_id ) {
							$is_current_parent = true;
						}
					}

					$is_open           = $has_children && ( $is_active || $has_active_child || $is_current_category || $is_current_parent );
					$is_other_category = $is_category_page && ! $is_current_category && ! $is_current_parent;

					$parent_classes = [ 'filter-link', 'filter-parent-link' ];
					if ( $has_children ) {
						$parent_classes[] = 'has-children';
					}
					if ( $is_active || $is_current_category ) {
						$parent_classes[] = 'active';
					} elseif ( $is_open ) {
						$parent_classes[] = 'is-open';
					}
					?>
					<li class="filter-item <?php echo $has_children ? 'has-children' : ''; ?> <?php echo $is_open ? 'is-open' : ''; ?>">
						<button
							type="button"
							class="<?php echo esc_attr( implode( ' ', $parent_classes ) ); ?>"
							data-taxonomy="filter_category"
							data-term="<?php echo esc_attr( $category->slug ); ?>"
							<?php if ( $has_children ) : ?>
								aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
							<?php endif; ?>
							<?php echo ( ! $has_children && $is_current_category ) ? 'disabled' : ''; ?>
							<?php echo $is_other_category ? 'style="opacity: 0.5; pointer-events: none;"' : ''; ?>
						>
							<span class="filter-name"><?php echo esc_html( $category->name ); ?></span>
							<span class="filter-count">(<?php echo absint( $category->count ); ?>)</span>
							<?php if ( $has_children ) : ?>
								<span class="filter-chevron" aria-hidden="true">
									<?php get_template_part( 'assets/svgs/angle-down' ); ?>
								</span>
							<?php endif; ?>
						</button>

						<?php if ( $has_children ) : ?>
							<ul class="filter-sub-list" <?php echo $is_open ? '' : 'hidden'; ?>>
								<?php foreach ( $child_categories as $child_category ) : ?>
									<?php
									$is_child_active  = in_array( $child_category->slug, $current_filters, true );
									$is_current_child = $is_category_page && $child_category->slug === $current_category_slug;
									?>
									<li class="filter-sub-item">
										<button
											type="button"
											class="filter-link filter-sub-link <?php echo ( $is_child_active || $is_current_child ) ? 'active' : ''; ?>"
											data-taxonomy="filter_category"
											data-term="<?php echo esc_attr( $child_category->slug ); ?>"
											<?php echo $is_current_child ? 'disabled' : ''; ?>
										>
											<span class="filter-name"><?php echo esc_html( $child_category->name ); ?></span>
											<span class="filter-count">(<?php echo absint( $child_category->count ); ?>)</span>
										</button>
									</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>

			</ul>
		</div>
	<?php endif; ?>

	<?php if ( $has_active_filters ) : ?>
		<div class="clear-filters-wrapper">
			<a href="<?php echo esc_url( $clear_filters_url ); ?>" class="clear-filters-btn">
				<?php esc_html_e( 'Clear Filters', 'mosharaf-core' ); ?>
			</a>
		</div>
	<?php endif; ?>

</div>
