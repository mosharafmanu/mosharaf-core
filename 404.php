<?php
/**
 * @package mosharaf-core
 */

get_header();

nocache_headers();
?>

	<main id="primary" class="site-main">

		<section class="error-404 not-found layout-padding pt-50 pt-md-70 pt-lg-100 pb-50 pb-md-70 pb-lg-100">
			<div class="error-404-wrapper">

				<div class="error-404-number">
					<span>4</span>
					<span>0</span>
					<span>4</span>
				</div>

				<h1 class="error-404-title"><?php esc_html_e( 'Page Not Found', 'mosharaf-core' ); ?></h1>

				<p class="error-404-text"><?php esc_html_e( "The page you're looking for doesn't exist or has been moved.", 'mosharaf-core' ); ?></p>

				<div class="error-404-actions btns">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-btn btn-primary">
						<?php esc_html_e( 'Back to Home', 'mosharaf-core' ); ?>
					</a>
				</div>

			</div>
		</section>

	</main>

<?php
get_footer();
