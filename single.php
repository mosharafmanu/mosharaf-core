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

		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				if ( function_exists( 'have_rows' ) && have_rows( 'cms' ) ) :
					mosharaf_flexible_content( 'cms' );
				else :
					get_template_part( 'template-parts/content', get_post_type() );
				endif;

				if ( function_exists( 'mosharaf_render_back_to_blogs_button' ) ) {
					mosharaf_render_back_to_blogs_button();
				}

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
