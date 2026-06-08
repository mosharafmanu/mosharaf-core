<?php
/**
 * Video Autoplay Controls Component
 *
 * Renders custom play/pause and mute/unmute controls for autoplay videos.
 *
 * @package mosharaf-core
 */

if ( ! function_exists( 'mosharaf_render_video_autoplay_controls' ) ) {
	/**
	 * Render video autoplay controls
	 *
	 * @param array $args {
	 *     Optional customization.
	 *
	 *     @type string $class Additional CSS classes. Default ''.
	 *     @type bool $echo Echo or return. Default true.
	 * }
	 * @return string|void HTML for autoplay controls
	 */
	function mosharaf_render_video_autoplay_controls( $args = [] ) {
		$defaults = [
			'class' => '',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$class = $args['class'] ? ' ' . esc_attr( $args['class'] ) : '';

		$html = '<div class="video-autoplay-controls' . $class . '">';

		// Play/Pause Button - Both icons inside
		$html .= '<button class="video-control-btn video-play-pause-btn" aria-label="' . esc_attr__( 'Play/Pause', 'mosharaf-core' ) . '">';
		$html .= mosharaf_render_video_control_icon( 'play' );
		$html .= mosharaf_render_video_control_icon( 'pause' );
		$html .= '</button>';

		// Mute/Unmute Button - Both icons inside
		$html .= '<button class="video-control-btn video-mute-btn" aria-label="' . esc_attr__( 'Mute/Unmute', 'mosharaf-core' ) . '">';
		$html .= mosharaf_render_video_control_icon( 'unmute' );
		$html .= mosharaf_render_video_control_icon( 'mute' );
		$html .= '</button>';

		$html .= '</div>';

		if ( $args['echo'] ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'mosharaf_render_video_control_icon' ) ) {
	/**
	 * Render video control icon
	 *
	 * CUSTOMIZE: Replace SVG icons with your own custom icons. Keep these in
	 * sync with the matching icons built dynamically in assets/js/video-popup.js
	 * (createPopupControls) so the autoplay and popup controls look identical.
	 *
	 * @param string $icon Icon type: 'play', 'pause', 'mute', 'unmute'.
	 * @return string SVG icon HTML
	 */
	function mosharaf_render_video_control_icon( $icon ) {
		$icons = [
			'play'   => '<svg class="play-icon" xmlns="http://www.w3.org/2000/svg" width="22" height="26" viewBox="0 0 22 26" fill="none"><path d="M4.07991 0.394447C3.25275 -0.114144 2.21321 -0.130911 1.36928 0.344147C0.525358 0.819204 0 1.71343 0 2.6859V22.3589C0 23.3313 0.525358 24.2256 1.36928 24.7006C2.21321 25.1757 3.25275 25.1533 4.07991 24.6503L20.176 14.8138C20.9752 14.3276 21.4614 13.4613 21.4614 12.5224C21.4614 11.5835 20.9752 10.7228 20.176 10.2309L4.07991 0.394447Z" fill="black"/></svg>',
			'pause'  => '<svg class="pause-icon" xmlns="http://www.w3.org/2000/svg" width="21" height="25" viewBox="0 0 21 25" fill="none"><path d="M3.125 0C1.39974 0 0 1.39974 0 3.125V21.875C0 23.6003 1.39974 25 3.125 25H5.20833C6.93359 25 8.33333 23.6003 8.33333 21.875V3.125C8.33333 1.39974 6.93359 0 5.20833 0H3.125ZM15.625 0C13.8997 0 12.5 1.39974 12.5 3.125V21.875C12.5 23.6003 13.8997 25 15.625 25H17.7083C19.4336 25 20.8333 23.6003 20.8333 21.875V3.125C20.8333 1.39974 19.4336 0 17.7083 0H15.625Z" fill="black"/></svg>',
			'mute'   => '<svg class="mute-icon" xmlns="http://www.w3.org/2000/svg" width="25" height="20" viewBox="0 0 25 20" fill="none"><path d="M13.0697 0.120707C13.5689 0.346421 13.8901 0.841256 13.8901 1.38818V18.0563C13.8901 18.6032 13.5689 19.0981 13.0697 19.3238C12.5705 19.5495 11.9845 19.4583 11.5765 19.0937L5.72098 13.8893H2.77802C1.24577 13.8893 0 12.6435 0 11.1112V8.33323C0 6.80098 1.24577 5.55521 2.77802 5.55521H5.72098L11.5765 0.350762C11.9845 -0.0138529 12.5705 -0.100666 13.0697 0.120707ZM18.4478 5.85905L20.8351 8.24641L23.2225 5.85905C23.6305 5.45103 24.2903 5.45103 24.694 5.85905C25.0977 6.26707 25.102 6.92685 24.694 7.33053L22.3066 9.71789L24.694 12.1053C25.102 12.5133 25.102 13.1731 24.694 13.5767C24.286 13.9804 23.6262 13.9848 23.2225 13.5767L20.8351 11.1894L18.4478 13.5767C18.0398 13.9848 17.38 13.9848 16.9763 13.5767C16.5726 13.1687 16.5683 12.5089 16.9763 12.1053L19.3637 9.71789L16.9763 7.33053C16.5683 6.92251 16.5683 6.26273 16.9763 5.85905C17.3843 5.45537 18.0441 5.45103 18.4478 5.85905Z" fill="black"/></svg>',
			'unmute' => '<svg class="unmute-icon" xmlns="http://www.w3.org/2000/svg" width="25" height="21" viewBox="0 0 25 21" fill="none"><path d="M13.8373 0.127797C14.3658 0.366767 14.7059 0.890664 14.7059 1.46971V19.1168C14.7059 19.6958 14.3658 20.2197 13.8373 20.4587C13.3088 20.6977 12.6884 20.6011 12.2564 20.2151L6.05699 14.705H2.94118C1.31893 14.705 0 13.3861 0 11.7638V8.82265C0 7.20041 1.31893 5.88147 2.94118 5.88147H6.05699L12.2564 0.371363C12.6884 -0.0146665 13.3088 -0.106578 13.8373 0.127797ZM21.7417 3.44581C23.727 5.06346 25 7.53129 25 10.2932C25 13.0552 23.727 15.523 21.7417 17.1407C21.2684 17.5267 20.5744 17.4532 20.1884 16.9798C19.8024 16.5065 19.8759 15.8125 20.3493 15.4265C21.8428 14.2133 22.7941 12.3658 22.7941 10.2932C22.7941 8.22063 21.8428 6.3732 20.3493 5.15537C19.8759 4.76934 19.807 4.07541 20.1884 3.60206C20.5699 3.12872 21.2684 3.05978 21.7417 3.44122V3.44581ZM18.9614 6.86952C19.9494 7.67835 20.5882 8.90997 20.5882 10.2932C20.5882 11.6765 19.9494 12.9081 18.9614 13.717C18.4881 14.103 17.7941 14.0295 17.4081 13.5561C17.0221 13.0828 17.0956 12.3888 17.5689 12.0028C18.0653 11.5984 18.3824 10.9826 18.3824 10.2932C18.3824 9.6039 18.0653 8.98809 17.5689 8.57908C17.0956 8.19305 17.0267 7.49912 17.4081 7.02577C17.7895 6.55243 18.4881 6.4835 18.9614 6.86493V6.86952Z" fill="black"/></svg>',
		];

		return isset( $icons[ $icon ] ) ? $icons[ $icon ] : '';
	}
}
