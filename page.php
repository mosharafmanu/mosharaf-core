<?php
/**
 * @package mosharaf-core
 */

get_header();

global $post;
$page_slug = $post ? 'page-' . $post->post_name : '';
?>

	<main id="primary" class="site-main <?php echo esc_attr( $page_slug ); ?>">
		<?php
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();

				if ( function_exists( 'have_rows' ) && have_rows( 'cms' ) ) :
					mosharaf_flexible_content( 'cms' );
				else :
					get_template_part( 'template-parts/content', 'page' );
				endif;
			endwhile;
		else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>

	</main><!-- #main -->

<?php
get_footer();



?>


