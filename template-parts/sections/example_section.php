<?php
/**
 * @package mosharaf-core
 */

$title            = get_sub_field( 'title' );
$description      = get_sub_field( 'description' );
$button_link      = get_sub_field( 'button_link' );
$image            = get_sub_field( 'image' );
$background_color = get_sub_field( 'background_color' ) ?: 'light';

if ( ! $title && ! $description && ! $image ) {
	return;
}

$section_classes = [
	'example-section',
	'layout-padding',
	'bg-mc-' . $background_color,
];

if ( $image ) {
	$section_classes[] = 'has-image';
}
?>

<section class="<?php echo esc_attr( implode( ' ', $section_classes ) ); ?>">
	<div class="container">

		<div class="example-section__content">

			<?php if ( $title ) : ?>
				<h2 class="example-section__title">
					<?php echo esc_html( $title ); ?>
				</h2>
			<?php endif; ?>

			<?php if ( $description ) : ?>
				<div class="example-section__description">
					<?php echo wp_kses_post( $description ); ?>
				</div>
			<?php endif; ?>

			<?php
			if ( $button_link && function_exists( 'mosharaf_render_button' ) ) {
				mosharaf_render_button(
					$button_link,
					[
						'style' => 'primary',
						'arrow' => true,
					]
				);
			}
			?>

		</div>

		<?php if ( $image ) : ?>
			<div class="example-section__media">
				<?php
				if ( function_exists( 'mosharaf_render_responsive_picture' ) ) {
					mosharaf_render_responsive_picture(
						$image,
						[
							'size'           => 'mc-900',
							'mobile_size'    => 'mc-600',
							'class'          => 'example-section__image',
							'lazy'           => true,
							'fetch_priority' => 'auto',
						]
					);
				}
				?>
			</div>
		<?php endif; ?>

	</div>
</section>
