<?php
/**
 * Single post template — used when no flexible content layouts are set.
 *
 * @package mosharaf-core
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>

	<?php if ( has_post_thumbnail() ) :
		$thumbnail_id = get_post_thumbnail_id();
	?>
		<div class="post-thumbnail">
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
						'class'         => 'post-thumbnail-image',
					]
				);
			else :
				the_post_thumbnail( 'mc-1200', [ 'class' => 'post-thumbnail-image' ] );
			endif; ?>
		</div>
	<?php endif; ?>

	<div class="post-inner layout-padding">

		<header class="entry-header pt-50 pt-md-70 pt-lg-100">

			<?php $categories = get_the_category();
			if ( $categories ) : ?>
				<div class="entry-categories mb-20">
					<?php foreach ( $categories as $category ) : ?>
						<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="entry-category">
							<?php echo esc_html( $category->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<div class="entry-meta mt-20">
				<time class="entry-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
					<?php echo esc_html( get_the_date() ); ?>
				</time>
				<span class="entry-author">
					<?php
					printf(
						/* translators: %s: Author name */
						esc_html__( 'By %s', 'mosharaf-core' ),
						esc_html( get_the_author() )
					);
					?>
				</span>
			</div>

		</header>

		<div class="entry-content mt-50 mt-md-60">
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
			<footer class="entry-footer pb-30">
				<div class="post-tags">
					<?php foreach ( $tags as $tag ) : ?>
						<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="post-tag">
							<?php echo esc_html( $tag->name ); ?>
						</a>
					<?php endforeach; ?>
				</div>
			</footer>
		<?php endif; ?>

	</div>

</article>
