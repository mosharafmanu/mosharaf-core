<?php
/**
 * @package mosharaf-core
 */

if ( ! function_exists( 'mosharaf_flexible_content' ) ) {
	function mosharaf_flexible_content( $field_name = 'cms', $post_id = null ) {
		if ( ! function_exists( 'have_rows' ) ) {
			return;
		}

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! have_rows( $field_name, $post_id ) ) {
			return;
		}

		// Track previous layout and section index for conditional styling
		$previous_layout = '';
		$section_index   = 0;

		while ( have_rows( $field_name, $post_id ) ) {
			the_row();

			$layout = get_row_layout();

			if ( empty( $layout ) ) {
				continue;
			}

			$GLOBALS['mosharaf_previous_layout'] = $previous_layout;
			$GLOBALS['mosharaf_section_index']   = $section_index;

			$template_path = 'template-parts/sections/' . $layout;
			$template_file = locate_template( $template_path . '.php' );

			if ( $template_file ) {
				get_template_part( $template_path );
			} elseif ( current_user_can( 'manage_options' ) && WP_DEBUG ) {
				echo '<!-- Missing template: ' . esc_html( $template_path ) . '.php -->';
			}

			$previous_layout = $layout;
			$section_index++;
		}

		$GLOBALS['mosharaf_last_layout'] = $previous_layout;

		unset( $GLOBALS['mosharaf_previous_layout'] );
		unset( $GLOBALS['mosharaf_section_index'] );
	}
}

if ( ! function_exists( 'mosharaf_get_last_flexible_layout' ) ) {
	function mosharaf_get_last_flexible_layout( $field_name = 'cms', $post_id = null ) {
		if ( ! function_exists( 'have_rows' ) ) {
			return false;
		}

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! have_rows( $field_name, $post_id ) ) {
			return false;
		}

		$last_layout = '';

		while ( have_rows( $field_name, $post_id ) ) {
			the_row();
			$last_layout = get_row_layout();
		}

		reset_rows();

		return $last_layout;
	}
}

if ( ! function_exists( 'mosharaf_get_first_flexible_layout' ) ) {
	function mosharaf_get_first_flexible_layout( $field_name = 'cms', $post_id = null ) {
		if ( ! function_exists( 'have_rows' ) ) {
			return false;
		}

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! have_rows( $field_name, $post_id ) ) {
			return false;
		}

		$first_layout = '';

		if ( have_rows( $field_name, $post_id ) ) {
			the_row();
			$first_layout = get_row_layout();
		}

		reset_rows();

		return $first_layout;
	}
}

if ( ! function_exists( 'mosharaf_has_hero_first_section' ) ) {
	function mosharaf_has_hero_first_section( $field_name = 'cms', $post_id = null ) {
		// Blog page uses inner_hero from options, not flexible content
		if ( is_home() ) {
			return true;
		}

		$first_layout = mosharaf_get_first_flexible_layout( $field_name, $post_id );

		return in_array( $first_layout, [ 'hero_section', 'inner_hero' ], true );
	}
}

if ( ! function_exists( 'mosharaf_queried_cms_has_layout' ) ) {
	/**
	 * Whether the queried page's `cms` flexible content contains any of the given
	 * layouts — a real scan of the page's rows, not a guess. Lets a child theme
	 * scope per-feature assets (Slick, Contact Form 7) to the pages that use them.
	 *
	 * @param string[] $layouts Layout names to look for.
	 * @param int|null  $post_id Defaults to the queried object.
	 * @return bool
	 */
	function mosharaf_queried_cms_has_layout( $layouts, $post_id = null ) {
		if ( ! function_exists( 'have_rows' ) ) {
			return false;
		}

		if ( null === $post_id ) {
			$post_id = get_queried_object_id();
		}

		if ( ! $post_id || ! have_rows( 'cms', $post_id ) ) {
			return false;
		}

		$layouts = (array) $layouts;
		$found   = false;

		while ( have_rows( 'cms', $post_id ) ) {
			the_row();
			if ( in_array( get_row_layout(), $layouts, true ) ) {
				$found = true;
				break;
			}
		}
		reset_rows();

		return $found;
	}
}

if ( ! function_exists( 'mosharaf_page_needs_slick' ) ) {
	/**
	 * Whether the page being rendered actually contains a Slick carousel.
	 *
	 * Base core renders no carousels of its own, so this defaults to false and
	 * Slick's CSS + JS are skipped everywhere. A child theme / site that adds
	 * carousel markup opts in via the `mosharaf_page_needs_slick` filter (e.g.
	 * `return mosharaf_queried_cms_has_layout( [ 'my_carousel_layout' ] );`),
	 * or enqueues the registered `slick-carousel` handle at render time.
	 *
	 * @return bool
	 */
	function mosharaf_page_needs_slick() {
		return (bool) apply_filters( 'mosharaf_page_needs_slick', false );
	}
}

if ( ! function_exists( 'mosharaf_page_needs_contact_form' ) ) {
	/**
	 * Whether the page being rendered actually outputs a Contact Form 7 form.
	 *
	 * Detects a [contact-form-7] shortcode in the queried object's content (the
	 * generic case). A child theme can extend this via the
	 * `mosharaf_page_needs_contact_form` filter (e.g. to also match a flexible
	 * layout that renders the form via do_shortcode()). CF7 otherwise enqueues
	 * its CSS + JS on every page; gating on this skips them where no form exists.
	 *
	 * @return bool
	 */
	function mosharaf_page_needs_contact_form() {
		$needs   = false;
		$queried = get_queried_object();

		if ( $queried instanceof WP_Post && has_shortcode( (string) $queried->post_content, 'contact-form-7' ) ) {
			$needs = true;
		}

		return (bool) apply_filters( 'mosharaf_page_needs_contact_form', $needs );
	}
}
