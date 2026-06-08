<?php
/**
 * @package mosharaf-core
 */

if ( ! function_exists( 'mosharaf_render_video' ) ) {
	function mosharaf_render_video( $video_field, $args = [] ) {
		$defaults = [
			'behavior'           => 'autoplay',
			'autoplay'           => true,
			'autoplay_on_scroll' => false,
			'class'              => '',
			'container_class'    => '',
			'controls'           => true,
			'popup_autoplay'     => true,
			'popup_controls'     => true,
			'muted'              => false,
			'loop'               => false,
			'width'              => '100%',
			'height'             => 'auto',
			'echo'               => true,
		];
		$args = wp_parse_args( $args, $defaults );

		if ( is_string( $video_field ) ) {
			$video_data = get_field( $video_field );
		} else {
			$video_data = $video_field;
		}

		if ( empty( $video_data ) || ! is_array( $video_data ) ) {
			return '';
		}

		$video_source = $video_data['video_source'] ?? '';

		if ( empty( $video_source ) ) {
			return '';
		}

		$video_html = '';
		switch ( $video_source ) {
			case 'self_host':
				$video_html = mosharaf_render_self_hosted_video( $video_data, $args );
				break;
			case 'youtube':
				$video_html = mosharaf_render_youtube_video( $video_data, $args );
				break;
			case 'vimeo':
				$video_html = mosharaf_render_vimeo_video( $video_data, $args );
				break;
			case 'cdn':
				$video_html = mosharaf_render_cdn_video( $video_data, $args );
				break;
		}

		if ( empty( $video_html ) ) {
			return '';
		}

		$container_class = 'video-container';
		if ( $args['container_class'] ) {
			$container_class .= ' ' . esc_attr( $args['container_class'] );
		}

		$html = '<div class="' . $container_class . '" data-behavior="' . esc_attr( $args['behavior'] ) . '">';
		$html .= $video_html;

		if ( 'onclick-popup' === $args['behavior'] ) {
			$html .= '<div class="video-play-overlay">';
			$html .= '<button class="video-play-button" aria-label="' . esc_attr__( 'Play Video', 'mosharaf-core' ) . '">';
			$html .= '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="26" viewBox="0 0 22 26" fill="none">';
			$html .= '<path d="M4.07991 0.394447C3.25275 -0.114144 2.21321 -0.130911 1.36928 0.344147C0.525358 0.819204 0 1.71343 0 2.6859V22.3589C0 23.3313 0.525358 24.2256 1.36928 24.7006C2.21321 25.1757 3.25275 25.1533 4.07991 24.6503L20.176 14.8138C20.9752 14.3276 21.4614 13.4613 21.4614 12.5224C21.4614 11.5835 20.9752 10.7228 20.176 10.2309L4.07991 0.394447Z" fill="#fff"/>';
			$html .= '</svg>';
			$html .= '</button>';
			$html .= '</div>';
		}

		if ( 'autoplay' === $args['behavior'] && $args['controls'] && 'youtube' !== $video_source ) {
			if ( $args['autoplay_on_scroll'] || $args['autoplay'] ) {
				$html .= '<div class="video-low-power-overlay">';
				$html .= '<button class="video-play-button low-power-play-btn" aria-label="' . esc_attr__( 'Play Video', 'mosharaf-core' ) . '">';
				$html .= '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="26" viewBox="0 0 22 26" fill="none">';
				$html .= '<path d="M4.07991 0.394447C3.25275 -0.114144 2.21321 -0.130911 1.36928 0.344147C0.525358 0.819204 0 1.71343 0 2.6859V22.3589C0 23.3313 0.525358 24.2256 1.36928 24.7006C2.21321 25.1757 3.25275 25.1533 4.07991 24.6503L20.176 14.8138C20.9752 14.3276 21.4614 13.4613 21.4614 12.5224C21.4614 11.5835 20.9752 10.7228 20.176 10.2309L4.07991 0.394447Z" fill="black"/>';
				$html .= '</svg>';
				$html .= '</button>';
				$html .= '</div>';
			}

			if ( function_exists( 'mosharaf_render_video_autoplay_controls' ) ) {
				$html .= mosharaf_render_video_autoplay_controls( [ 'echo' => false ] );
			}
		}

		$html .= '</div>';

		if ( $args['echo'] ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			return $html;
		}
	}
}

if ( ! function_exists( 'mosharaf_render_self_hosted_video' ) ) {
	function mosharaf_render_self_hosted_video( $video_data, $args ) {
		$video_file = $video_data['video_self_host_file'] ?? '';
		$poster     = $video_data['video_self_host_poster'] ?? '';

		if ( empty( $video_file ) || ! isset( $video_file['url'] ) ) {
			return '';
		}

		$video_url  = esc_url( $video_file['url'] );
		$poster_url = '';
		if ( $poster && isset( $poster['url'] ) ) {
			$poster_url = esc_url( $poster['url'] );
		}

		if ( empty( $poster_url ) && $args['autoplay_on_scroll'] ) {
			global $product;
			if ( $product && method_exists( $product, 'get_image_id' ) ) {
				$image_id = $product->get_image_id();
				if ( $image_id ) {
					$poster_url = wp_get_attachment_image_url( $image_id, 'full' );
				}
			}
		}

		$autoplay = false;
		$muted    = $args['muted'];

		if ( 'autoplay' === $args['behavior'] ) {
			// Always add autoplay attribute for 'autoplay' behavior to load first frame/poster.
			// JavaScript will pause immediately if autoplay parameter is false.
			$autoplay = true;

			if ( $args['autoplay_on_scroll'] || $args['autoplay'] ) {
				$muted = true; // Browsers require muted for autoplay
			}
		} elseif ( 'hover' === $args['behavior'] ) {
			$autoplay = false;
			$muted    = true; // Browsers require muted for hover autoplay
		} elseif ( 'onclick-popup' === $args['behavior'] ) {
			$autoplay = false;
			if ( $args['autoplay'] ) {
				$muted = true; // Browsers require muted for autoplay
			}
		}

		$html = '<video ';
		$html .= 'width="' . esc_attr( $args['width'] ) . '" ';
		$html .= 'height="' . esc_attr( $args['height'] ) . '" ';
		if ( $poster_url ) {
			$html .= 'poster="' . $poster_url . '" ';
		}
		if ( $args['controls'] && 'autoplay' !== $args['behavior'] ) {
			$html .= 'controls ';
		}
		if ( $autoplay ) {
			$html .= 'autoplay ';
		}
		if ( $muted ) {
			$html .= 'muted ';
		}
		if ( $args['loop'] ) {
			$html .= 'loop ';
		}
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'preload="metadata" ';
		}
		$html .= 'playsinline '; // Required for inline playback on iOS
		$html .= 'data-behavior="' . esc_attr( $args['behavior'] ) . '" ';
		$html .= 'data-desired-muted="' . ( $args['muted'] ? 'true' : 'false' ) . '" ';
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'data-autoplay-on-scroll="true" ';
		}
		if ( 'autoplay' === $args['behavior'] && ! $args['autoplay'] && ! $args['autoplay_on_scroll'] ) {
			$html .= 'data-pause-on-load="true" ';
		}
		if ( 'onclick-popup' === $args['behavior'] ) {
			if ( $args['popup_autoplay'] ) {
				$html .= 'data-popup-autoplay="true" ';
			}
			if ( $args['popup_controls'] ) {
				$html .= 'data-popup-controls="true" ';
			}
		}
		if ( $args['class'] ) {
			$html .= 'class="' . esc_attr( $args['class'] ) . '" ';
		}
		$html .= '>';
		$html .= '<source src="' . $video_url . '" type="' . esc_attr( $video_file['mime_type'] ) . '">';
		$html .= esc_html__( 'Your browser does not support the video tag.', 'mosharaf-core' );
		$html .= '</video>';

		return $html;
	}
}

if ( ! function_exists( 'mosharaf_render_youtube_video' ) ) {
	function mosharaf_render_youtube_video( $video_data, $args ) {
		$youtube_url = $video_data['video_youtube_url'] ?? '';

		if ( ! $youtube_url ) {
			return '';
		}

		$video_id = '';
		if ( preg_match( '/youtube\.com\/watch\?v=([^\&\?\/]+)/', $youtube_url, $matches ) ) {
			$video_id = $matches[1];
		} elseif ( preg_match( '/youtube\.com\/embed\/([^\&\?\/]+)/', $youtube_url, $matches ) ) {
			$video_id = $matches[1];
		} elseif ( preg_match( '/youtu\.be\/([^\&\?\/]+)/', $youtube_url, $matches ) ) {
			$video_id = $matches[1];
		}

		if ( ! $video_id ) {
			return '';
		}

		if ( 'onclick-popup' === $args['behavior'] ) {
			$thumbnail_url = 'https://img.youtube.com/vi/' . $video_id . '/maxresdefault.jpg';
			$html = '<img src="' . esc_url( $thumbnail_url ) . '" alt="' . esc_attr__( 'Video Thumbnail', 'mosharaf-core' ) . '" ';
			$html .= 'data-youtube-id="' . esc_attr( $video_id ) . '" ';
			if ( $args['class'] ) {
				$html .= 'class="' . esc_attr( $args['class'] ) . '" ';
			}
			$html .= 'style="width: 100%; height: auto; display: block;">';
			return $html;
		}

		$embed_url = 'https://www.youtube.com/embed/' . $video_id;
		$params    = [];

		if ( $args['autoplay'] ) {
			$params[] = 'autoplay=1';
		}
		if ( $args['muted'] ) {
			$params[] = 'mute=1';
		}
		if ( $args['loop'] ) {
			$params[] = 'loop=1';
			$params[] = 'playlist=' . $video_id; // Required for YouTube loop
		}
		$params[] = 'playsinline=1';
		$params[] = 'rel=0'; // Don't show related videos from other channels

		if ( ! empty( $params ) ) {
			$embed_url .= '?' . implode( '&', $params );
		}

		$html = '<iframe ';
		$html .= 'width="' . esc_attr( $args['width'] ) . '" ';
		$html .= 'height="' . esc_attr( $args['height'] ) . '" ';
		$html .= 'src="' . esc_url( $embed_url ) . '" ';
		$html .= 'frameborder="0" ';
		$html .= 'allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" ';
		$html .= 'allowfullscreen>';
		$html .= '</iframe>';

		return $html;
	}
}

if ( ! function_exists( 'mosharaf_render_vimeo_video' ) ) {
	function mosharaf_render_vimeo_video( $video_data, $args ) {
		$vimeo_url = $video_data['video_vimeo_url'] ?? '';
		$poster    = $video_data['video_vimeo_poster'] ?? '';

		if ( ! $vimeo_url ) {
			return '';
		}

		$poster_url = '';
		if ( $poster && isset( $poster['url'] ) ) {
			$poster_url = esc_url( $poster['url'] );
		}

		if ( empty( $poster_url ) && $args['autoplay_on_scroll'] ) {
			global $product;
			if ( $product && method_exists( $product, 'get_image_id' ) ) {
				$image_id = $product->get_image_id();
				if ( $image_id ) {
					$poster_url = wp_get_attachment_image_url( $image_id, 'full' );
				}
			}
		}

		$autoplay = false;
		$muted    = $args['muted'];

		if ( 'autoplay' === $args['behavior'] ) {
			// Always add autoplay attribute for 'autoplay' behavior to load first frame/poster.
			// JavaScript will pause immediately if autoplay parameter is false.
			$autoplay = true;

			if ( $args['autoplay_on_scroll'] || $args['autoplay'] ) {
				$muted = true; // Browsers require muted for autoplay
			}
		} elseif ( 'hover' === $args['behavior'] ) {
			$autoplay = false;
			$muted    = true; // Browsers require muted for hover autoplay
		} elseif ( 'onclick-popup' === $args['behavior'] ) {
			$autoplay = false;
			if ( $args['autoplay'] ) {
				$muted = true; // Browsers require muted for autoplay
			}
		}

		$html = '<video ';
		$html .= 'width="' . esc_attr( $args['width'] ) . '" ';
		$html .= 'height="' . esc_attr( $args['height'] ) . '" ';
		if ( $poster_url ) {
			$html .= 'poster="' . $poster_url . '" ';
		}
		if ( $args['controls'] && 'autoplay' !== $args['behavior'] ) {
			$html .= 'controls ';
		}
		if ( $autoplay ) {
			$html .= 'autoplay ';
		}
		if ( $muted ) {
			$html .= 'muted ';
		}
		if ( $args['loop'] ) {
			$html .= 'loop ';
		}
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'preload="metadata" ';
		}
		$html .= 'playsinline '; // Required for inline playback on iOS
		$html .= 'data-behavior="' . esc_attr( $args['behavior'] ) . '" ';
		$html .= 'data-desired-muted="' . ( $args['muted'] ? 'true' : 'false' ) . '" ';
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'data-autoplay-on-scroll="true" ';
		}
		if ( 'autoplay' === $args['behavior'] && ! $args['autoplay'] && ! $args['autoplay_on_scroll'] ) {
			$html .= 'data-pause-on-load="true" ';
		}
		if ( 'onclick-popup' === $args['behavior'] ) {
			if ( $args['popup_autoplay'] ) {
				$html .= 'data-popup-autoplay="true" ';
			}
			if ( $args['popup_controls'] ) {
				$html .= 'data-popup-controls="true" ';
			}
		}
		if ( $args['class'] ) {
			$html .= 'class="' . esc_attr( $args['class'] ) . '" ';
		}
		$html .= '>';
		$html .= '<source src="' . esc_url( $vimeo_url ) . '" type="video/mp4">';
		$html .= esc_html__( 'Your browser does not support the video tag.', 'mosharaf-core' );
		$html .= '</video>';

		return $html;
	}
}

if ( ! function_exists( 'mosharaf_render_cdn_video' ) ) {
	function mosharaf_render_cdn_video( $video_data, $args ) {
		$cdn_url = $video_data['video_cdn_url'] ?? '';
		$poster  = $video_data['video_cdn_poster'] ?? '';

		if ( ! $cdn_url ) {
			return '';
		}

		$poster_url = '';
		if ( $poster && isset( $poster['url'] ) ) {
			$poster_url = esc_url( $poster['url'] );
		}

		if ( empty( $poster_url ) && $args['autoplay_on_scroll'] ) {
			global $product;
			if ( $product && method_exists( $product, 'get_image_id' ) ) {
				$image_id = $product->get_image_id();
				if ( $image_id ) {
					$poster_url = wp_get_attachment_image_url( $image_id, 'full' );
				}
			}
		}

		$autoplay = false;
		$muted    = $args['muted'];

		if ( 'autoplay' === $args['behavior'] ) {
			// Always add autoplay attribute for 'autoplay' behavior to load first frame/poster.
			// JavaScript will pause immediately if autoplay parameter is false.
			$autoplay = true;

			if ( $args['autoplay_on_scroll'] || $args['autoplay'] ) {
				$muted = true; // Browsers require muted for autoplay
			}
		} elseif ( 'hover' === $args['behavior'] ) {
			$autoplay = false;
			$muted    = true; // Browsers require muted for hover autoplay
		} elseif ( 'onclick-popup' === $args['behavior'] ) {
			$autoplay = false;
			if ( $args['autoplay'] ) {
				$muted = true; // Browsers require muted for autoplay
			}
		}

		$html = '<video ';
		$html .= 'width="' . esc_attr( $args['width'] ) . '" ';
		$html .= 'height="' . esc_attr( $args['height'] ) . '" ';
		if ( $poster_url ) {
			$html .= 'poster="' . $poster_url . '" ';
		}
		if ( $args['controls'] && 'autoplay' !== $args['behavior'] ) {
			$html .= 'controls ';
		}
		if ( $autoplay ) {
			$html .= 'autoplay ';
		}
		if ( $muted ) {
			$html .= 'muted ';
		}
		if ( $args['loop'] ) {
			$html .= 'loop ';
		}
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'preload="metadata" ';
		}
		$html .= 'playsinline '; // Required for inline playback on iOS
		$html .= 'data-behavior="' . esc_attr( $args['behavior'] ) . '" ';
		$html .= 'data-desired-muted="' . ( $args['muted'] ? 'true' : 'false' ) . '" ';
		if ( $args['autoplay_on_scroll'] ) {
			$html .= 'data-autoplay-on-scroll="true" ';
		}
		if ( 'autoplay' === $args['behavior'] && ! $args['autoplay'] && ! $args['autoplay_on_scroll'] ) {
			$html .= 'data-pause-on-load="true" ';
		}
		if ( 'onclick-popup' === $args['behavior'] ) {
			if ( $args['popup_autoplay'] ) {
				$html .= 'data-popup-autoplay="true" ';
			}
			if ( $args['popup_controls'] ) {
				$html .= 'data-popup-controls="true" ';
			}
		}
		if ( $args['class'] ) {
			$html .= 'class="' . esc_attr( $args['class'] ) . '" ';
		}
		$html .= '>';
		$html .= '<source src="' . esc_url( $cdn_url ) . '" type="video/mp4">';
		$html .= esc_html__( 'Your browser does not support the video tag.', 'mosharaf-core' );
		$html .= '</video>';

		return $html;
	}
}
