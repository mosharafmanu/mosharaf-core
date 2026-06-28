<?php
/**
 * Single post template — used when no flexible content layouts are set.
 *
 * Layout: an overlay hero (featured image with breadcrumb, category, title and
 * meta on top), followed by the constrained article body with tags and an
 * author card.
 *
 * @package mosharaf-core
 */

$mc_word_count = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', get_the_ID() ) ) );
$mc_read_time  = max( 1, (int) ceil( $mc_word_count / 220 ) );
$mc_author_id  = (int) get_post_field( 'post_author', get_the_ID() );
$mc_has_image  = has_post_thumbnail();
$mc_hero_class = $mc_has_image ? 'single-post__hero--has-image' : 'single-post__hero--plain';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>

	<header class="single-post__hero <?php echo esc_attr( $mc_hero_class ); ?>">
		<?php if ( $mc_has_image ) :
			$thumbnail_id = get_post_thumbnail_id();
			?>
			<div class="single-post__hero-media" aria-hidden="true">
				<?php if ( function_exists( 'mosharaf_render_responsive_picture' ) ) :
					mosharaf_render_responsive_picture(
						[
							'ID'  => $thumbnail_id,
							'url' => wp_get_attachment_url( $thumbnail_id ),
							'alt' => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ?: get_the_title(),
						],
						[
							'sizes'         => '100vw',
							'fetchpriority' => 'high',
							'lazy'          => false,
							'class'         => 'single-post__hero-image',
						]
					);
				else :
					the_post_thumbnail( 'mc-1200', [ 'class' => 'single-post__hero-image' ] );
				endif; ?>
			</div>
			<span class="single-post__hero-overlay" aria-hidden="true"></span>
		<?php endif; ?>

		<div class="single-post__hero-inner mc-container layout-padding">
			<?php if ( function_exists( 'mosharaf_breadcrumb' ) ) { mosharaf_breadcrumb( false, '', '' ); } ?>

			<?php $categories = get_the_category();
			if ( $categories ) : ?>
				<div class="entry-categories">
					<?php foreach ( $categories as $category ) : ?>
						<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="entry-category">
							<?php echo esc_html( $category->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<div class="entry-meta">
				<span class="entry-author">
					<?php echo get_avatar( $mc_author_id, 36, '', get_the_author(), [ 'class' => 'entry-author__avatar' ] ); ?>
					<span class="entry-author__name">
						<?php
						printf(
							/* translators: %s: Author name */
							esc_html__( 'By %s', 'mosharaf-core' ),
							esc_html( get_the_author() )
						);
						?>
					</span>
				</span>
				<time class="entry-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
				<span class="entry-read-time">
					<?php
					printf(
						/* translators: %s: estimated reading time in minutes */
						esc_html( _n( '%s min read', '%s min read', $mc_read_time, 'mosharaf-core' ) ),
						number_format_i18n( $mc_read_time )
					);
					?>
				</span>
			</div>
		</div>
	</header>

	<div class="post-inner layout-padding">

		<div class="entry-content mt-40 mt-md-50">
			<?php
			the_content( sprintf(
				wp_kses(
					/* translators: %s: Post title */
					__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'mosharaf-core' ),
					[ 'span' => [ 'class' => [] ] ]
				),
				wp_kses_post( get_the_title() )
			) );

			wp_link_pages( [
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'mosharaf-core' ),
				'after'  => '</div>',
			] );
			?>
		</div>

		<?php $tags = get_the_tags(); if ( $tags ) : ?>
			<footer class="entry-footer">
				<div class="post-tags">
					<?php foreach ( $tags as $tag ) : ?>
						<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="post-tag">
							<?php echo esc_html( $tag->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</footer>
		<?php endif; ?>

		<?php get_template_part( 'template-parts/author-card' ); ?>

	</div>

</article>
