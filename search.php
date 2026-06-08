<?php
/**
 * @package mosharaf-core
 */

get_header();
?>

	<main id="primary" class="site-main search-results-page">

		<?php if ( function_exists( 'mosharaf_breadcrumb' ) ) : ?>
			<?php mosharaf_breadcrumb( true ); ?>
		<?php endif; ?>

		<?php
		global $wp_query;
		$result_count = (int) $wp_query->found_posts;
		?>

		<header class="search-results-header layout-padding pt-50 pt-md-70 pt-lg-100">
			<span class="archive-label"><?php esc_html_e( 'Search', 'mosharaf-core' ); ?></span>
			<h1 class="archive-title">
				<?php printf(
					/* translators: %s: search query */
					esc_html__( 'Results for "%s"', 'mosharaf-core' ),
					esc_html( get_search_query() )
				); ?>
			</h1>
			<?php if ( $result_count > 0 ) : ?>
				<p class="search-result-count"><?php printf(
					esc_html( _n( '%s result found', '%s results found', $result_count, 'mosharaf-core' ) ),
					number_format_i18n( $result_count )
				); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) :

			// Group mixed results by post type so each renders with its native
			// card — products get the WC product card (price/rating/add-to-cart),
			// everything else gets the blog post card. Buffered per-group so we
			// can wrap each in its own labelled section, in one pass over the
			// single relevance-ranked search query (no duplicate queries).
			$products_html  = '';
			$articles_html  = '';
			$product_count  = 0;
			$article_count  = 0;
			$article_index  = 0;

			while ( have_posts() ) :
				the_post();

				if ( 'product' === get_post_type() && function_exists( 'wc_get_template_part' ) ) {
					$product_count++;
					ob_start();
					wc_get_template_part( 'content', 'product' );
					$products_html .= ob_get_clean();
					continue;
				}

				$article_count++;
				$article_index++;

				ob_start();
				if ( function_exists( 'mosharaf_render_post_card' ) ) {
					mosharaf_render_post_card( null, [
						'variant'       => 1 === $article_index ? 'featured' : 'default',
						'fetchpriority' => 1 === $article_index ? 'high' : 'auto',
					] );
				} else {
					get_template_part( 'template-parts/content', 'search' );
				}
				$articles_html .= ob_get_clean();

			endwhile;
			?>

			<?php if ( $products_html ) : ?>
				<section class="search-results-group search-results-group--products layout-padding pt-40 pt-md-50">
					<h2 class="search-results-group__title">
						<?php esc_html_e( 'Products', 'mosharaf-core' ); ?>
						<span class="search-results-group__count"><?php echo esc_html( number_format_i18n( $product_count ) ); ?></span>
					</h2>
					<div class="products card-grid columns-3">
						<?php echo $products_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</section>
			<?php endif; ?>

			<?php if ( $articles_html ) : ?>
				<section class="search-results-group search-results-group--articles layout-padding pt-40 pt-md-50 pb-50 pb-md-70 pb-lg-100">
					<h2 class="search-results-group__title">
						<?php esc_html_e( 'Articles', 'mosharaf-core' ); ?>
						<span class="search-results-group__count"><?php echo esc_html( number_format_i18n( $article_count ) ); ?></span>
					</h2>
					<div class="blog-grid card-grid columns-3">
						<?php echo $articles_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</section>
			<?php endif; ?>

			<?php
			if ( function_exists( 'mosharaf_render_pagination' ) ) {
				mosharaf_render_pagination();
			}
			?>

		<?php else : ?>

			<div class="search-no-results layout-padding pt-50 pb-50 pb-md-70 pb-lg-100">
				<p class="search-no-results__text"><?php printf(
					/* translators: %s: search query */
					esc_html__( 'Sorry, nothing matched "%s". Try a different search term.', 'mosharaf-core' ),
					esc_html( get_search_query() )
				); ?></p>
				<?php get_search_form(); ?>
			</div>

		<?php endif; ?>

	</main>

<?php
get_footer();
