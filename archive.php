<?php
/**
 * @package mosharaf-core
 */

get_header();
?>

	<main id="primary" class="site-main blog-archive-page">

		<?php if ( function_exists( 'mosharaf_breadcrumb' ) ) : ?>
			<?php mosharaf_breadcrumb( true ); ?>
		<?php endif; ?>

		<header class="archive-header layout-padding pt-50 pt-md-70 pt-lg-100">
			<?php
			if ( is_category() ) {
				$archive_label = __( 'Category', 'mosharaf-core' );
				$archive_title = single_cat_title( '', false );
			} elseif ( is_tag() ) {
				$archive_label = __( 'Tag', 'mosharaf-core' );
				$archive_title = single_tag_title( '', false );
			} elseif ( is_author() ) {
				$archive_label = __( 'Author', 'mosharaf-core' );
				$archive_title = esc_html( get_the_author() );
			} elseif ( is_year() ) {
				$archive_label = __( 'Year', 'mosharaf-core' );
				$archive_title = get_the_date( 'Y' );
			} elseif ( is_month() ) {
				$archive_label = __( 'Month', 'mosharaf-core' );
				$archive_title = get_the_date( 'F Y' );
			} elseif ( is_day() ) {
				$archive_label = __( 'Date', 'mosharaf-core' );
				$archive_title = get_the_date();
			} else {
				$archive_label = '';
				$archive_title = get_the_archive_title();
			}
			?>
			<?php if ( $archive_label ) : ?>
				<span class="archive-label"><?php echo esc_html( $archive_label ); ?></span>
			<?php endif; ?>
			<h1 class="archive-title"><?php echo esc_html( $archive_title ); ?></h1>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		</header>

		<section class="blog-grid-section layout-padding pt-40 pt-md-50 pb-50 pb-md-70 pb-lg-100">
			<?php if ( have_posts() ) : ?>

				<div class="blog-grid card-grid columns-3">
					<?php
					$post_card_index = 0;

					while ( have_posts() ) :
						the_post();
						$post_card_index++;

						if ( function_exists( 'mosharaf_render_post_card' ) ) {
							mosharaf_render_post_card( null, [
								'variant'       => 1 === $post_card_index ? 'featured' : 'default',
								'fetchpriority' => 1 === $post_card_index ? 'high' : 'auto',
							] );
						} else {
							get_template_part( 'template-parts/content' );
						}
					endwhile;
					?>
				</div>

				<?php
				if ( function_exists( 'mosharaf_render_pagination' ) ) {
					mosharaf_render_pagination();
				}
				?>

			<?php else : ?>

				<?php get_template_part( 'template-parts/content', 'none' ); ?>

			<?php endif; ?>

		</section>

	</main>

<?php
get_footer();
