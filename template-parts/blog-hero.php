<?php
/**
 * Blog Archive Hero
 *
 * Reads from the Blog Options ACF options page.
 * Renders nothing if no content is set — safe to include on every blog load.
 *
 * @package mosharaf-core
 */

if ( ! function_exists( 'get_field' ) ) {
	return;
}

$title       = get_field( 'blog_hero_title', 'options' );
$description = get_field( 'blog_hero_description', 'options' );
$media_type  = get_field( 'blog_hero_media_type', 'options' ) ?: 'image';
$image       = get_field( 'blog_hero_image', 'options' );
$video       = get_field( 'blog_hero_video', 'options' );

if ( ! $title && ! $description && ! $image && ! $video ) {
	return;
}
?>

<section class="blog-hero layout-padding">

	<div class="blog-hero-content">
		<?php if ( $title ) : ?>
			<h1 class="blog-hero-title"><?php echo wp_kses_post( $title ); ?></h1>
		<?php endif; ?>

		<?php if ( $description ) : ?>
			<div class="blog-hero-description"><?php echo wp_kses_post( $description ); ?></div>
		<?php endif; ?>
	</div>

	<?php if ( 'video' === $media_type && $video && function_exists( 'mosharaf_render_video' ) ) : ?>
		<div class="blog-hero-media">
			<?php mosharaf_render_video( $video, [ 'behavior' => 'autoplay', 'lazy' => false ] ); ?>
		</div>
	<?php elseif ( 'image' === $media_type && $image && function_exists( 'mosharaf_render_responsive_picture' ) ) : ?>
		<div class="blog-hero-media">
			<?php
			mosharaf_render_responsive_picture( $image, [
				'lazy'          => false,
				'fetchpriority' => 'high',
				'class'         => 'blog-hero-image',
			] );
			?>
		</div>
	<?php endif; ?>

</section>
