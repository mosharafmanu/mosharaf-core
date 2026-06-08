<?php
/**
 * Post card component.
 *
 * @package mosharaf-core
 */

if ( ! function_exists( 'mosharaf_render_post_card' ) ) {
	function mosharaf_render_post_card( $post_id = null, $args = [] ) {
		$post_id = $post_id ? absint( $post_id ) : get_the_ID();

		if ( ! $post_id ) {
			return;
		}

		$defaults = [
			'image_size'     => 'mc-900',
			'image_sizes'    => '(max-width: 767px) 100vw, (max-width: 1199px) 50vw, 33vw',
			'excerpt_length' => 24,
			'variant'        => 'default',
			'class'          => '',
			'fetchpriority'  => 'auto',
			'echo'           => true,
		];

		$args       = wp_parse_args( $args, $defaults );
		$variant    = sanitize_key( $args['variant'] );
		$variant    = in_array( $variant, [ 'default', 'featured', 'compact' ], true ) ? $variant : 'default';
		$permalink  = get_permalink( $post_id );
		$title      = get_the_title( $post_id );
		$title      = $title ? $title : __( 'Untitled', 'mosharaf-core' );
		$categories = get_the_category( $post_id );
		$category   = ! empty( $categories ) ? $categories[0] : null;
		$excerpt    = wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post_id ) ), absint( $args['excerpt_length'] ) );
		$word_count = str_word_count( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ) );
		$read_time  = max( 1, (int) ceil( $word_count / 220 ) );

		$card_classes = [ 'post-card', 'post-card--' . $variant ];
		if ( $args['class'] ) {
			$card_classes[] = $args['class'];
		}

		// Build ACF-compatible image array from the WP post thumbnail.
		$thumbnail_id   = get_post_thumbnail_id( $post_id );
		$thumbnail_data = null;
		if ( $thumbnail_id ) {
			$thumbnail_data = [
				'ID'  => $thumbnail_id,
				'url' => wp_get_attachment_url( $thumbnail_id ),
				'alt' => get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true ) ?: $title,
			];
		}

		ob_start();
		?>

		<article id="post-<?php echo esc_attr( $post_id ); ?>" <?php post_class( implode( ' ', array_map( 'sanitize_html_class', $card_classes ) ), $post_id ); ?>>
			<a class="post-card__media" href="<?php echo esc_url( $permalink ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Read %s', 'mosharaf-core' ), $title ) ); ?>">
				<?php if ( $thumbnail_data && function_exists( 'mosharaf_render_responsive_picture' ) ) : ?>
					<?php
					mosharaf_render_responsive_picture(
						$thumbnail_data,
						[
							'class'         => 'post-card__image',
							'lazy'          => true,
							'fetchpriority' => $args['fetchpriority'],
							'sizes'         => $args['image_sizes'],
						]
					);
					?>
				<?php else : ?>
					<span class="post-card__placeholder" aria-hidden="true">
						<span><?php echo esc_html( $category ? $category->name : __( 'Article', 'mosharaf-core' ) ); ?></span>
					</span>
				<?php endif; ?>
			</a>

			<div class="post-card__content">
				<div class="post-card__meta">
					<?php if ( $category ) : ?>
						<a class="post-card__category" href="<?php echo esc_url( get_category_link( $category ) ); ?>">
							<?php echo esc_html( $category->name ); ?>
						</a>
					<?php endif; ?>

					<span class="post-card__date-wrap">
						<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>">
							<?php echo esc_html( get_the_date( 'M j, Y', $post_id ) ); ?>
						</time>
						<span aria-hidden="true">/</span>
						<span><?php echo esc_html( sprintf( __( '%s min read', 'mosharaf-core' ), number_format_i18n( $read_time ) ) ); ?></span>
					</span>
				</div>

				<h2 class="post-card__title">
					<a href="<?php echo esc_url( $permalink ); ?>" rel="bookmark">
						<?php echo esc_html( $title ); ?>
					</a>
				</h2>

				<?php if ( $excerpt ) : ?>
					<p class="post-card__excerpt"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<a class="post-card__link" href="<?php echo esc_url( $permalink ); ?>" aria-hidden="true" tabindex="-1">
					<span><?php esc_html_e( 'Read article', 'mosharaf-core' ); ?></span>
					<span class="post-card__link-icon" aria-hidden="true">&rarr;</span>
				</a>
			</div>
		</article>

		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}
