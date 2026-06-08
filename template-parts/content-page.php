<?php
/**
 * Page content fallback — used when no flexible content layouts are set.
 *
 * @package mosharaf-core
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'layout-padding pt-50 pt-md-70 pt-lg-100 pb-50 pb-md-70 pb-lg-100' ); ?>>

	<?php
	$show_title = true;
	if ( function_exists( 'get_field' ) ) {
		$show_title = get_field( 'show_page_title' ) ?? true;
	}
	?>

	<div class="post-inner">

		<?php if ( $show_title ) : ?>
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</header>
		<?php endif; ?>

		<div class="entry-content mt-50 mt-md-60">
			<?php
			the_content();

			wp_link_pages( [
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'mosharaf-core' ),
				'after'  => '</div>',
			] );
			?>
		</div>

	</div>

</article>
