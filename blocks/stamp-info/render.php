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
			[ 'label' => 'Stamp Set',        'taxonomy' => 'stamp_sets' ],
			[ 'label' => 'Date of Issue',    'taxonomy' => null ],
			[ 'label' => 'Denomination',     'taxonomy' => null ],
			[ 'label' => 'Quantity',         'taxonomy' => null ],
			[ 'label' => 'Perforation',      'taxonomy' => null ],
			[ 'label' => 'Printer',          'taxonomy' => null ],
			[ 'label' => 'Printing Process', 'taxonomy' => 'printing_process' ],
			[ 'label' => 'Watermark',        'taxonomy' => null ],
			[ 'label' => 'Colors',           'taxonomy' => null ],
			[ 'label' => 'Credits',          'taxonomy' => 'credits' ],
			[ 'label' => 'Catalog Codes',    'taxonomy' => null ],
			[ 'label' => 'Themes',           'taxonomy' => 'themes' ],
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
								echo $term_list( $row['taxonomy'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped (already escaped in helper)
							} else {
								echo '<span class="sv-placeholder">&mdash; ' . esc_html__( 'value', 'stampvault' ) . ' &mdash;</span>';
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
