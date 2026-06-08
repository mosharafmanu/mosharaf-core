<?php
/**
 * Mobile Navigation Component
 * @package mosharaf-core
 */

if ( ! function_exists( 'mosharaf_render_mobile_navigation' ) ) {
	function mosharaf_render_mobile_navigation() {
		$header_cta    = function_exists( 'mosharaf_get_header_button' ) ? mosharaf_get_header_button() : false;
		$social_medias = function_exists( 'mosharaf_get_social_medias' ) ? mosharaf_get_social_medias() : false;
		?>

		<div class="mobile-menu-overlay" aria-hidden="true"></div>

		<nav id="mobile-navigation" class="mobile-navigation" aria-label="<?php esc_attr_e( 'Mobile navigation', 'mosharaf-core' ); ?>" aria-hidden="true">
			<div class="mobile-nav-inner">

				<div class="mobile-nav-header">
					<div class="site-branding mobile-nav-logo">
						<?php mosharaf_render_site_logo(); ?>
					</div>
					<button class="mobile-menu-close" aria-label="<?php esc_attr_e( 'Close menu', 'mosharaf-core' ); ?>">
						<span></span>
						<span></span>
					</button>
				</div>

				<div class="mobile-nav-menu">
					<?php
					wp_nav_menu( [
						'theme_location' => 'mainMenu',
						'container'      => false,
						'menu_class'     => 'mobile-menu',
						'fallback_cb'    => false,
						'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					] );
					?>
				</div>

				<?php if ( $header_cta ) : ?>
				<div class="mobile-nav-cta">
					<a href="<?php echo esc_url( $header_cta['url'] ?? '#' ); ?>" class="site-btn btn-primary mobile-cta-btn"<?php echo isset( $header_cta['target'] ) && $header_cta['target'] ? ' target="' . esc_attr( $header_cta['target'] ) . '" rel="noopener noreferrer"' : ''; ?>>
						<?php echo esc_html( $header_cta['title'] ?? 'Get Started' ); ?>
					</a>
				</div>
				<?php endif; ?>

				<?php if ( $social_medias && is_array( $social_medias ) && function_exists( 'mosharaf_render_social_medias' ) ) : ?>
				<div class="mobile-nav-social">
					<?php
					// Reuse the same helper the footer calls (mosharaf_render_social_medias)
					// rather than re-deriving SVG-vs-image handling and aria-labels here —
					// it already injects an icon class onto each SVG's root element, which
					// is what lets CSS size them; the hand-rolled markup this replaced
					// echoed raw, unconstrained file_get_contents() output with no class
					// to hook into, rendering each icon at its native (huge) viewBox size
					// in a bare bulleted <ul>.
					mosharaf_render_social_medias( [
						'list_class' => 'mobile-social-list',
						'item_class' => 'mobile-social-item',
						'link_class' => 'mobile-social-link',
						'icon_class' => 'mobile-social-icon',
					] );
					?>
				</div>
				<?php endif; ?>

			</div>
		</nav>

		<?php
	}
}
