<?php
/**
 * @package mosharaf-core
 */

// Header Options

if ( ! function_exists( 'mosharaf_get_header_button' ) ) {
	function mosharaf_get_header_button() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'header_button', 'options' );
	}
}

if ( ! function_exists( 'mosharaf_render_header_button' ) ) {
	function mosharaf_render_header_button( $args = [] ) {
		$button = mosharaf_get_header_button();

		if ( ! $button || ! is_array( $button ) ) {
			return;
		}

		$defaults = [
			'class' => 'site-btn btn-primary',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$url    = $button['url'] ?? '#';
		$title  = $button['title'] ?? 'Get Started';
		$target = $button['target'] ?? '';

		ob_start();
		?>
		<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( $args['class'] ); ?>"<?php echo $target ? ' target="' . esc_attr( $target ) . '" rel="noopener noreferrer"' : ''; ?>>
			<?php echo esc_html( $title ); ?>
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


if ( ! function_exists( 'mosharaf_get_site_logo' ) ) {
	function mosharaf_get_site_logo() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'site_logo', 'options' );
	}
}

if ( ! function_exists( 'mosharaf_render_site_logo' ) ) {
	function mosharaf_render_site_logo( $args = [] ) {
		$logo = mosharaf_get_site_logo();

		if ( ! $logo ) {
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo-link site-name" rel="home">
					<?php bloginfo( 'name' ); ?>
				</a>
				<?php
			}
			return;
		}

		if ( ! function_exists( 'mosharaf_render_responsive_picture' ) ) {
			return;
		}

		$defaults = [
			'class'      => 'site-logo',
			'alt'        => get_bloginfo( 'name' ),
			'link_class' => 'site-logo-link',
		];
		$args = wp_parse_args( $args, $defaults );

		$home_url = home_url( '/' );
		?>
		<a href="<?php echo esc_url( $home_url ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			mosharaf_render_responsive_picture(
				$logo,
				[
					'class' => $args['class'],
					'alt'   => $args['alt'],
					'sizes' => '(max-width: 768px) 100px, 160px',
				]
			);
			?>
		</a>
		<?php
	}
}

// ─────────────────────────────────────────────────────────────────
// Footer Options
// ─────────────────────────────────────────────────────────────────

if ( ! function_exists( 'mosharaf_get_footer_tagline' ) ) {
	function mosharaf_get_footer_tagline() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}
		return get_field( 'footer_tagline', 'options' );
	}
}

if ( ! function_exists( 'mosharaf_render_footer_tagline' ) ) {
	function mosharaf_render_footer_tagline( $args = [] ) {
		$tagline = mosharaf_get_footer_tagline();
		if ( ! $tagline ) {
			return;
		}

		$defaults = [
			'class' => 'footer-tagline',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$output = '<p class="' . esc_attr( $args['class'] ) . '">' . wp_kses_post( nl2br( $tagline ) ) . '</p>';

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'mosharaf_get_footer_logo' ) ) {
	function mosharaf_get_footer_logo() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		$footer_logo = get_field( 'footer_logo', 'options' );

		if ( ! $footer_logo ) {
			$footer_logo = get_field( 'site_logo', 'options' );
		}

		return $footer_logo;
	}
}

if ( ! function_exists( 'mosharaf_render_footer_logo' ) ) {
	function mosharaf_render_footer_logo( $args = [] ) {
		if ( ! function_exists( 'mosharaf_render_responsive_picture' ) ) {
			return;
		}

		$logo = mosharaf_get_footer_logo();

		if ( ! $logo ) {
			return;
		}

		$defaults = [
			'class'      => 'footer-logo',
			'alt'        => get_bloginfo( 'name' ),
			'link_class' => 'footer-logo-link',
		];
		$args = wp_parse_args( $args, $defaults );

		$home_url = home_url( '/' );
		?>
		<a href="<?php echo esc_url( $home_url ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php
			mosharaf_render_responsive_picture(
				$logo,
				[
					'class' => $args['class'],
					'alt'   => $args['alt'],
					'sizes' => '(max-width: 768px) 100px, 160px',
				]
			);
			?>
		</a>
		<?php
	}
}

// Social Media Options

if ( ! function_exists( 'mosharaf_get_social_medias' ) ) {
	function mosharaf_get_social_medias() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'social_medias', 'options' );
	}
}

if ( ! function_exists( 'mosharaf_get_footer_copyright' ) ) {
	function mosharaf_get_footer_copyright() {
		if ( ! function_exists( 'get_field' ) ) {
			return false;
		}

		return get_field( 'footer_copyright', 'options' );
	}
}

if ( ! function_exists( 'mosharaf_render_footer_menu' ) ) {
	function mosharaf_render_footer_menu( $args = [] ) {
		$defaults = [
			'location'        => '',
			'container_class' => 'footer-menu-column',
			'title_class'     => 'footer-menu-title',
			'menu_class'      => 'footer-menu-list',
			'show_title'      => true,
			'echo'            => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( empty( $args['location'] ) ) {
			return;
		}

		if ( ! has_nav_menu( $args['location'] ) ) {
			return;
		}

		$locations = get_nav_menu_locations();
		$menu_id   = $locations[ $args['location'] ] ?? 0;
		$menu_obj  = wp_get_nav_menu_object( $menu_id );
		$menu_name = $menu_obj->name ?? '';

		ob_start();
		?>
		<div class="<?php echo esc_attr( $args['container_class'] ); ?>">
			<?php if ( $args['show_title'] && ! empty( $menu_name ) ) : ?>
				<p class="<?php echo esc_attr( $args['title_class'] ); ?>"><?php echo esc_html( $menu_name ); ?></p>
			<?php endif; ?>
			<?php
			wp_nav_menu(
				[
					'theme_location' => $args['location'],
					'container'      => false,
					'menu_class'     => $args['menu_class'],
					'depth'          => 1,
					'fallback_cb'    => false,
				]
			);
			?>
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

if ( ! function_exists( 'mosharaf_render_social_medias' ) ) {
	function mosharaf_render_social_medias( $args = [] ) {
		$defaults = [
			'list_class' => 'social-media-list',
			'item_class' => 'social-media-item',
			'link_class' => 'social-media-link',
			'icon_class' => 'social-media-icon',
			'echo'       => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$social_medias = mosharaf_get_social_medias();

		if ( ! $social_medias || ! is_array( $social_medias ) ) {
			return;
		}

		ob_start();
		?>
		<ul class="<?php echo esc_attr( $args['list_class'] ); ?>">
			<?php foreach ( $social_medias as $social ) : ?>
				<?php
				$icon = isset( $social['social_icon'] ) ? $social['social_icon'] : null;
				$link = isset( $social['social_link'] ) ? $social['social_link'] : '';

				if ( ! $icon || ! $link ) {
					continue;
				}

				$icon_url  = isset( $icon['url'] ) ? $icon['url'] : '';
				$icon_alt  = isset( $icon['alt'] ) ? $icon['alt'] : '';
				$icon_id   = isset( $icon['ID'] ) ? $icon['ID'] : 0;
				$icon_mime = $icon_id ? get_post_mime_type( $icon_id ) : '';

				$is_svg = ( 'image/svg+xml' === $icon_mime ) || ( pathinfo( $icon_url, PATHINFO_EXTENSION ) === 'svg' );

				if ( ! empty( $icon_alt ) ) {
					$aria_label = $icon_alt;
				} else {
					$url_lower      = strtolower( $link );
					$filename_lower = strtolower( basename( $icon_url ) );

					if ( strpos( $url_lower, 'facebook' ) !== false || strpos( $filename_lower, 'facebook' ) !== false ) {
						$aria_label = __( 'Facebook', 'mosharaf-core' );
					} elseif ( strpos( $url_lower, 'twitter' ) !== false || strpos( $url_lower, 'x.com' ) !== false || strpos( $filename_lower, 'twitter' ) !== false ) {
						$aria_label = __( 'Twitter', 'mosharaf-core' );
					} elseif ( strpos( $url_lower, 'linkedin' ) !== false || strpos( $url_lower, 'linkdin' ) !== false || strpos( $filename_lower, 'linkedin' ) !== false ) {
						$aria_label = __( 'LinkedIn', 'mosharaf-core' );
					} elseif ( strpos( $url_lower, 'instagram' ) !== false || strpos( $filename_lower, 'instagram' ) !== false ) {
						$aria_label = __( 'Instagram', 'mosharaf-core' );
					} elseif ( strpos( $url_lower, 'youtube' ) !== false || strpos( $filename_lower, 'youtube' ) !== false ) {
						$aria_label = __( 'YouTube', 'mosharaf-core' );
					} else {
						$aria_label = __( 'Social Media', 'mosharaf-core' );
					}
				}
				?>

				<li class="<?php echo esc_attr( $args['item_class'] ); ?>">
					<a href="<?php echo esc_url( $link ); ?>" class="<?php echo esc_attr( $args['link_class'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $aria_label ); ?>">
						<?php if ( $is_svg ) : ?>
							<?php
							$svg_path = get_attached_file( $icon_id );
							if ( $svg_path && file_exists( $svg_path ) ) {
								$svg_content = file_get_contents( $svg_path );
								$svg_content = str_replace( '<svg', '<svg class="' . esc_attr( $args['icon_class'] ) . ' social-media-svg"', $svg_content );
								$svg_content = preg_replace( '/<\?xml.*?\?>/', '', $svg_content );
								echo $svg_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} else {
								?>
								<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $icon_alt ); ?>" class="<?php echo esc_attr( $args['icon_class'] ); ?>" />
								<?php
							}
							?>
						<?php else : ?>
							<img src="<?php echo esc_url( $icon_url ); ?>" alt="<?php echo esc_attr( $icon_alt ); ?>" class="<?php echo esc_attr( $args['icon_class'] ); ?>" />
						<?php endif; ?>
					</a>
				</li>

			<?php endforeach; ?>
		</ul>
		<?php
		$output = ob_get_clean();

		if ( $args['echo'] ) {
			echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $output;
		}
	}
}

if ( ! function_exists( 'mosharaf_render_footer_copyright' ) ) {
	function mosharaf_render_footer_copyright( $args = [] ) {
		$defaults = [
			'class' => 'footer-copyright-text',
			'echo'  => true,
		];
		$args = wp_parse_args( $args, $defaults );

		$copyright = mosharaf_get_footer_copyright();

		if ( ! $copyright ) {
			return;
		}

		$copyright = str_replace( '{year}', gmdate( 'Y' ), $copyright );

		ob_start();
		?>

		<div class="<?php echo esc_attr( $args['class'] ); ?>">
			<p><?php echo esc_html( $copyright ); ?></p>
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
