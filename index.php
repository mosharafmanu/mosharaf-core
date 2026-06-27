<?php
/**
 * @package mosharaf-core
 */

get_header();
?>

	<main id="primary" class="site-main blog-page">

		<?php get_template_part( 'template-parts/blog', 'hero' ); ?>


		<header class="blog-page-header mc-container layout-padding">
			<div>
				<h1 class="blog-page-title"><?php
					$blog_page_id = get_option( 'page_for_posts' );
					if ( $blog_page_id ) {
						echo esc_html( get_the_title( $blog_page_id ) );
					} else {
						esc_html_e( 'Blog', 'mosharaf-core' );
					}
				?></h1>
			</div>
			<?php
			$post_counts = wp_count_posts();
			$total       = isset( $post_counts->publish ) ? (int) $post_counts->publish : 0;
			if ( $total > 0 ) :
			?>
				<span class="blog-page-count"><?php printf(
					esc_html( _n( '%s article', '%s articles', $total, 'mosharaf-core' ) ),
					number_format_i18n( $total )
				); ?></span>
			<?php endif; ?>
		</header>

		<div class="blog-search-bar mc-container layout-padding pt-30 pb-30">
			<?php get_search_form(); ?>
		</div>

		<section class="blog-grid-section mc-container layout-padding pt-20">
			<?php if ( have_posts() ) : ?>

				<div class="blog-grid card-grid columns-3">
					<?php
					$post_card_index = 0;

					while ( have_posts() ) :
						the_post();
						$post_card_index++;

						if ( function_exists( 'mosharaf_render_post_card' ) ) {
							mosharaf_render_post_card( null, [
								'variant'       => 'default',
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
