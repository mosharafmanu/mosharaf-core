<?php
/**
 * @package mosharaf-core
 */

get_header();

$post_slug = '';
if ( is_singular() ) {
	global $post;
	if ( $post ) {
		$post_type = get_post_type();
		$post_slug = $post_type . '-' . $post->post_name;
	}
}
?>

	<main id="primary" class="site-main <?php echo esc_attr( $post_slug ); ?>">

		<?php if ( is_singular( 'post' ) ) : ?>
			<div class="reading-progress" aria-hidden="true"><span class="reading-progress__bar"></span></div>
		<?php endif; ?>

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				if ( function_exists( 'have_rows' ) && have_rows( 'cms' ) ) :
					mosharaf_flexible_content( 'cms' );
				else :
					get_template_part( 'template-parts/content', get_post_type() );
				endif;

				// Previous / next article navigation (blog posts only).
				if ( is_singular( 'post' ) ) :
					$mc_prev = get_previous_post();
					$mc_next = get_next_post();

					if ( $mc_prev || $mc_next ) :
						?>
						<nav class="post-nav post-inner layout-padding mt-40 mt-lg-60" aria-label="<?php esc_attr_e( 'Article navigation', 'mosharaf-core' ); ?>">
							<?php if ( $mc_prev ) : ?>
								<a class="post-nav__link post-nav__link--prev" href="<?php echo esc_url( get_permalink( $mc_prev ) ); ?>" rel="prev">
									<span class="post-nav__label"><span aria-hidden="true">&larr;</span> <?php esc_html_e( 'Previous', 'mosharaf-core' ); ?></span>
									<span class="post-nav__title"><?php echo esc_html( get_the_title( $mc_prev ) ); ?></span>
								</a>
							<?php else : ?>
								<span class="post-nav__spacer"></span>
							<?php endif; ?>

							<?php if ( $mc_next ) : ?>
								<a class="post-nav__link post-nav__link--next" href="<?php echo esc_url( get_permalink( $mc_next ) ); ?>" rel="next">
									<span class="post-nav__label"><?php esc_html_e( 'Next', 'mosharaf-core' ); ?> <span aria-hidden="true">&rarr;</span></span>
									<span class="post-nav__title"><?php echo esc_html( get_the_title( $mc_next ) ); ?></span>
								</a>
							<?php endif; ?>
						</nav>
						<?php
					endif;
				endif;

				if ( function_exists( 'mosharaf_render_back_to_blogs_button' ) ) {
					mosharaf_render_back_to_blogs_button();
				}

				// Related Articles (blog posts only): same-category first, then most-recent
				// to backfill up to 3 so the row always reads as a deliberate set.
				if ( is_singular( 'post' ) && function_exists( 'mosharaf_render_post_card' ) ) :

					$mc_related_posts = [];
					$mc_exclude       = [ get_the_ID() ];
					$mc_post_cats     = wp_get_post_categories( get_the_ID() );

					if ( $mc_post_cats ) {
						$mc_cat_query = new WP_Query( [
							'post_type'           => 'post',
							'post_status'         => 'publish',
							'posts_per_page'      => 3,
							'post__not_in'        => $mc_exclude,
							'category__in'        => $mc_post_cats,
							'orderby'             => 'date',
							'order'               => 'DESC',
							'no_found_rows'       => true,
							'ignore_sticky_posts' => true,
						] );
						$mc_related_posts = $mc_cat_query->posts;
					}

					if ( count( $mc_related_posts ) < 3 ) {
						$mc_fill_query = new WP_Query( [
							'post_type'           => 'post',
							'post_status'         => 'publish',
							'posts_per_page'      => 3 - count( $mc_related_posts ),
							'post__not_in'        => array_merge( $mc_exclude, wp_list_pluck( $mc_related_posts, 'ID' ) ),
							'orderby'             => 'date',
							'order'               => 'DESC',
							'no_found_rows'       => true,
							'ignore_sticky_posts' => true,
						] );
						$mc_related_posts = array_merge( $mc_related_posts, $mc_fill_query->posts );
					}

					if ( $mc_related_posts ) :
						$mc_related_count = count( $mc_related_posts );
						// 1 item → full-width featured card; 2 → halves; 3 → thirds.
						$mc_related_cols    = 2 === $mc_related_count ? 'columns-2' : 'columns-3';
						$mc_related_variant = 1 === $mc_related_count ? 'featured' : 'default';
						?>
						<section class="related-posts mc-container layout-padding pt-50 pb-50 pt-lg-90 pb-lg-90">
							<header class="related-posts__header">
								<h2 class="related-posts__title"><?php esc_html_e( 'Related Articles', 'mosharaf-core' ); ?></h2>
							</header>

							<div class="blog-grid card-grid <?php echo esc_attr( $mc_related_cols ); ?> mt-30 mt-lg-50">
								<?php foreach ( $mc_related_posts as $mc_related_post ) : ?>
									<?php mosharaf_render_post_card( $mc_related_post->ID, [ 'variant' => $mc_related_variant ] ); ?>
								<?php endforeach; ?>
							</div>
						</section>
						<?php
					endif;

				endif;

				if ( comments_open() || get_comments_number() ) {
					echo '<div class="comments-wrap post-inner layout-padding mt-30 mt-md-40 mt-lg-50 pb-50 pb-md-70 pb-lg-100">';
					comments_template();
					echo '</div>';
				}

			endwhile;
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>

	</main>

<?php
get_footer();
