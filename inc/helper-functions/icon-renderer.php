<?php
/**
 * @package mosharaf-core
 */

if ( ! function_exists( 'mosharaf_render_icon' ) ) {
	function mosharaf_render_icon( $icon, $args = [] ) {
		$defaults = [
			'class' => '',
			'alt'   => '',
			'echo'  => true,
		];

		$args = wp_parse_args( $args, $defaults );

		if ( is_array( $icon ) ) {
			$file_id = isset( $icon['ID'] ) ? intval( $icon['ID'] ) : 0;
			$url     = isset( $icon['url'] ) ? $icon['url'] : '';
			$alt     = ! empty( $args['alt'] ) ? $args['alt'] : ( $icon['alt'] ?? '' );
		} elseif ( is_numeric( $icon ) ) {
			$file_id = intval( $icon );
			$url     = wp_get_attachment_url( $file_id );
			$alt     = ! empty( $args['alt'] ) ? $args['alt'] : get_post_meta( $file_id, '_wp_attachment_image_alt', true );
		} else {
			return;
		}

		if ( empty( $file_id ) || empty( $url ) ) {
			return;
		}

		$file_path = get_attached_file( $file_id );

		if ( ! file_exists( $file_path ) ) {
			return;
		}

		$extension = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
		$mime_type = mime_content_type( $file_path );

		if ( $extension === 'svg' && in_array( $mime_type, [ 'image/svg+xml', 'text/plain' ], true ) ) {
			$svg_content = file_get_contents( $file_path );

			if ( strpos( $svg_content, '<svg' ) !== false ) {
				$aria_label = $alt ?: 'Icon';
				$class_attr = $args['class'] ? ' class="' . esc_attr( $args['class'] ) . '"' : '';

				$svg_tag = preg_replace(
					'/<svg/',
					'<svg' . $class_attr . ' role="img" aria-label="' . esc_attr( $aria_label ) . '"',
					$svg_content,
					1
				);

				$svg_tag = preg_replace( '/<\?xml.*?\?>/', '', $svg_tag );

				if ( $args['echo'] ) {
					echo $svg_tag; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					return;
				}

				return $svg_tag;
			}
		}

		$class_attr = $args['class'] ? ' class="' . esc_attr( $args['class'] ) . '"' : '';
		$html = sprintf(
			'<img src="%s" alt="%s"%s />',
			esc_url( $url ),
			esc_attr( $alt ),
			$class_attr
		);

		if ( $args['echo'] ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			return;
		}

		return $html;
	}
}
