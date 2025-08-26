<?php
/**
 * Server-side render callback for Stamp Info block.
 */

if ( ! function_exists( 'stampvault_render_stamp_info_block' ) ) {
	function stampvault_render_stamp_info_block( $attributes, $content, $block ) {
		// For now static output; later can pull meta by post ID.
		if ( get_post_type() !== 'stamps' ) {
			return '';
		}
		// Row configuration: label => taxonomy slug (or null for placeholder rows not yet implemented).
		$rows = [
			[ 'label' => 'Sub Title',        'taxonomy' => null,                'meta' => 'sub_title' ],
			[ 'label' => 'Stamp Set',        'taxonomy' => 'stamp_sets',        'meta' => null ],
			[ 'label' => 'Date of Issue',    'taxonomy' => null,                'meta' => 'date_of_release' ],
			[ 'label' => 'Denomination',     'taxonomy' => null,                'meta' => 'denomination' ],
			[ 'label' => 'Quantity',         'taxonomy' => null,                'meta' => 'quantity' ],
			[ 'label' => 'Perforation',      'taxonomy' => null,                'meta' => 'perforations' ],
			[ 'label' => 'Printer',          'taxonomy' => null,                'meta' => 'printer' ],
			[ 'label' => 'Printing Process', 'taxonomy' => 'printing_process',  'meta' => null ],
			[ 'label' => 'Watermark',        'taxonomy' => null,                'meta' => 'watermark' ],
			[ 'label' => 'Colors',           'taxonomy' => null,                'meta' => 'colors' ],
			[ 'label' => 'Credits',          'taxonomy' => 'credits',           'meta' => null ],
			[ 'label' => 'Catalog Codes',    'taxonomy' => null,                'meta' => null ],
			[ 'label' => 'Themes',           'taxonomy' => 'themes',            'meta' => null ],
		];

		$post_id = get_the_ID();

		/**
		 * Helper: Fetch term names for a taxonomy for current post.
		 * Returns an HTML-escaped, comma-separated string or an em dash if none.
		 */
		$term_list = function( $taxonomy ) use ( $post_id ) {
			$terms = get_the_terms( $post_id, $taxonomy );
			if ( is_wp_error( $terms ) || empty( $terms ) ) {
				return '&mdash;';
			}
			$names = wp_list_pluck( $terms, 'name' );
			return esc_html( implode( ', ', $names ) );
		};
		ob_start();
		?>
		<div class="wp-block-stampvault-stamp-info">
			<table class="stampvault-stamp-info-table">
				<tbody>
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<th scope="row"><?php echo esc_html( translate( $row['label'], 'stampvault' ) ); ?></th>
						<td class="sv-value">
							<?php
							if ( $row['taxonomy'] ) {
								echo $term_list( $row['taxonomy'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							} elseif ( $row['meta'] ) {
								$val = get_post_meta( $post_id, $row['meta'], true );
								if ( '' === $val || null === $val ) {
									echo '<span class="sv-placeholder">&mdash;</span>';
								} else {
									echo esc_html( $val );
								}
							} else {
								echo '<span class="sv-placeholder">&mdash;</span>';
							}
							?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}
}
