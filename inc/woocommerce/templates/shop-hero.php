<?php
/**
 * Shop hero
 *
 * Title + optional description + optional image, shown above the
 * filter/grid layout on the shop page and product category/tag archives.
 *
 * Image source: the Shop page's featured image on the main shop page,
 * or the term's `hero_image` ACF field (falling back to its taxonomy
 * thumbnail) on category/tag archives — both optional, the hero still
 * renders title-only when no image is set. Deliberately scoped to its
 * own `.shop-hero-*` classes rather than a project's site-wide hero
 * system, so this stays portable as part of the shop-archive module.
 *
 * @package mosharaf-core
 */

defined( 'ABSPATH' ) || exit;

$hero_image = null;

if ( is_shop() ) {
	$shop_page_id = wc_get_page_id( 'shop' );
	if ( $shop_page_id && has_post_thumbnail( $shop_page_id ) ) {
		$image_id   = get_post_thumbnail_id( $shop_page_id );
		$hero_image = [
			'ID'  => $image_id,
			'url' => wp_get_attachment_url( $image_id ),
			'alt' => get_post_meta( $image_id, '_wp_attachment_image_alt', true ),
		];
	}
} elseif ( is_product_taxonomy() ) {
	$queried_object = get_queried_object();

	if ( $queried_object && isset( $queried_object->term_id ) ) {
		if ( function_exists( 'get_field' ) ) {
			$hero_image_acf = get_field( 'hero_image', $queried_object );
			if ( $hero_image_acf && is_array( $hero_image_acf ) ) {
				$hero_image = [
					'ID'  => $hero_image_acf['ID'],
					'url' => $hero_image_acf['url'],
					'alt' => $hero_image_acf['alt'],
				];
			}
		}

		if ( ! $hero_image ) {
			$thumbnail_id = get_term_meta( $queried_object->term_id, 'thumbnail_id', true );
			if ( $thumbnail_id ) {
				$hero_image = [
					'ID'  => $thumbnail_id,
					'url' => wp_get_attachment_url( $thumbnail_id ),
					'alt' => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ),
				];
			}
		}
	}
}

$page_title = is_product_taxonomy() ? single_term_title( '', false ) : woocommerce_page_title( false );

$page_description = '';
if ( is_shop() ) {
	$shop_page_id = wc_get_page_id( 'shop' );
	if ( $shop_page_id ) {
		$shop_page = get_post( $shop_page_id );
		if ( $shop_page && ! empty( $shop_page->post_content ) ) {
			$page_description = apply_filters( 'the_content', $shop_page->post_content );
		}
	}
} elseif ( is_product_taxonomy() ) {
	$page_description = term_description();
}

$hero_classes = [ 'shop-hero', 'layout-padding' ];
if ( $hero_image ) {
	$hero_classes[] = 'has-image';
}
?>
<?php if ( function_exists( 'mosharaf_breadcrumb' ) ) : ?>
	<?php mosharaf_breadcrumb( true ); ?>
<?php endif; ?>

<div class="<?php echo esc_attr( implode( ' ', $hero_classes ) ); ?>">
	<div class="shop-hero-grid">

		<div class="shop-hero-content">
			<?php if ( ! empty( $page_title ) ) : ?>
				<h1 class="shop-hero-title"><?php echo esc_html( $page_title ); ?></h1>
			<?php endif; ?>

			<?php if ( ! empty( $page_description ) ) : ?>
				<div class="shop-hero-description">
					<?php echo wp_kses_post( $page_description ); ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $hero_image ) : ?>
			<div class="shop-hero-media">
				<?php
				if ( function_exists( 'mosharaf_render_responsive_picture' ) ) {
					mosharaf_render_responsive_picture(
						[
							'ID'  => $hero_image['ID'],
							'url' => $hero_image['url'],
							'alt' => $hero_image['alt'] ?? $page_title,
						],
						[
							'class' => 'shop-hero-image',
							'sizes' => '(min-width: 768px) 50vw, 100vw',
						]
					);
				}
				?>
			</div>
		<?php endif; ?>

	</div>
</div>
