<?php
/**
 * @package mosharaf-core
 */

function mosharaf_render_pagination() {
	ob_start();
	get_template_part( 'assets/svgs/angle-left-pagination' );
	$prev_arrow = ob_get_clean();

	ob_start();
	get_template_part( 'assets/svgs/angle-right-pagination' );
	$next_arrow = ob_get_clean();

	$args = [
		'mid_size'  => 1, // Reduced from 2 to 1 for better mobile display
		'end_size'  => 1,
		'prev_text' => '<span class="pagination-arrow">' . $prev_arrow . '</span>',
		'next_text' => '<span class="pagination-arrow">' . $next_arrow . '</span>',
		'type'      => 'list',
	];

	$pagination = paginate_links( $args );

	if ( ! $pagination ) {
		return;
	}

	$allowed_tags = [
		'nav'  => [
			'class'      => [],
			'aria-label' => [],
		],
		'ul'   => [ 'class' => [] ],
		'li'   => [ 'class' => [] ],
		'a'    => [
			'class' => [],
			'href'  => [],
		],
		'span' => [
			'class'        => [],
			'aria-current' => [],
		],
		'svg'  => [
			'width'   => [],
			'height'  => [],
			'viewbox' => [],
			'fill'    => [],
			'xmlns'   => [],
		],
		'path' => [
			'd'            => [],
			'stroke'       => [],
			'stroke-width' => [],
			'fill'         => [],
		],
	];
	?>
	<nav class="blog-pagination pagination" aria-label="<?php esc_attr_e( 'Blog pagination', 'mosharaf-core' ); ?>">
		<?php echo wp_kses( $pagination, $allowed_tags ); ?>
	</nav>
	<?php
}
