<?php
/**
 * No results template
 *
 * @package mosharaf-core
 */

?>

<div class="no-results not-found">

	<h2 class="page-title"><?php esc_html_e( 'Nothing Found', 'mosharaf-core' ); ?></h2>

	<div class="page-content">
		<?php if ( is_search() ) : ?>

			<p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'mosharaf-core' ); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php esc_html_e( 'It seems we cannot find what you are looking for. Perhaps searching can help.', 'mosharaf-core' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div>

</div>
