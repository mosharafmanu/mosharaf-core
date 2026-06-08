<?php
/**
 * @package mosharaf-core
 */

if ( ! function_exists( 'mosharaf_render_button' ) ) {
	function mosharaf_render_button( $button_link, $args = [] ) {
		if ( empty( $button_link ) || ! is_array( $button_link ) || empty( $button_link['url'] ) ) {
			return;
		}

		$defaults = [
			'style'     => 'btn-primary',
			'show_icon' => true,
			'class'     => '',
			'echo'      => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$link_url    = $button_link['url'] ?? '';
		$link_title  = $button_link['title'] ?? '';
		$link_target = $button_link['target'] ?? '_self';

		if ( empty( $link_title ) ) {
			return;
		}

		$button_classes = 'site-btn ' . esc_attr( $args['style'] );
		if ( ! empty( $args['class'] ) ) {
			$button_classes .= ' ' . esc_attr( $args['class'] );
		}

		ob_start();
		?>
		<a href="<?php echo esc_url( $link_url ); ?>"
			class="<?php echo esc_attr( $button_classes ); ?>"
			target="<?php echo esc_attr( $link_target ); ?>">
			<span class="btn-text"><?php echo esc_html( $link_title ); ?></span>
			<?php if ( $args['show_icon'] ) : ?>
				<span class="btn-icon">
					<?php get_template_part( 'assets/svgs/double-angle-right' ); ?>
				</span>
			<?php endif; ?>
		</a>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'mosharaf_render_buttons' ) ) {
	function mosharaf_render_buttons( $buttons, $args = [] ) {
		if ( empty( $buttons ) || ! is_array( $buttons ) ) {
			return;
		}

		$defaults = [
			'wrapper_class' => 'btns',
			'default_style' => 'btn-primary',
			'show_icon'     => true,
			'echo'          => true,
		];
		$args = wp_parse_args( $args, $defaults );

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['wrapper_class'] ); ?>">
			<?php foreach ( $buttons as $button ) : ?>
				<?php
				$button_link  = $button['button_link'] ?? [];
				$button_style = $button['button_style'] ?? $args['default_style'];

				if ( empty( $button_link ) || empty( $button_link['url'] ) ) {
					continue;
				}

				if ( function_exists( 'mosharaf_render_button' ) ) {
					mosharaf_render_button(
						$button_link,
						[
							'style'     => $button_style,
							'show_icon' => $args['show_icon'],
							'echo'      => true,
						]
					);
				}
				?>
			<?php endforeach; ?>
		</div>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}
