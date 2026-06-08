<?php

if ( ! function_exists( 'mosharaf_render_back_to_blogs_button' ) ) {
	function mosharaf_render_back_to_blogs_button( $args = [] ) {
		if ( ! is_singular( 'post' ) ) {
			return;
		}

		$defaults = [
			'url'           => get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ),
			'text'          => __( '← Back to Blog', 'mosharaf-core' ),
			'class'         => 'site-btn btn-outline',
			'wrapper_class' => 'post-inner layout-padding pt-50 pt-md-70 pt-lg-70',
		];
		$args = wp_parse_args( $args, $defaults );
		?>

		<div class="<?php echo esc_attr( $args['wrapper_class'] ); ?>">
			<a href="<?php echo esc_url( $args['url'] ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>">
				<?php echo esc_html( $args['text'] ); ?>
			</a>
		</div>

		<?php
	}
}
