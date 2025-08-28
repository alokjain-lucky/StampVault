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
			[ 'label' => __( 'Sub Title', 'stampvault' ),        'taxonomy' => null,                'meta' => 'sub_title' ],
			[ 'label' => __( 'Stamp Set', 'stampvault' ),        'taxonomy' => 'stamp_sets',        'meta' => null ],
			[ 'label' => __( 'Date of Issue', 'stampvault' ),    'taxonomy' => null,                'meta' => 'date_of_release' ],
			[ 'label' => __( 'Denomination', 'stampvault' ),     'taxonomy' => null,                'meta' => 'denomination' ],
			[ 'label' => __( 'Quantity', 'stampvault' ),         'taxonomy' => null,                'meta' => 'quantity' ],
			[ 'label' => __( 'Perforation', 'stampvault' ),      'taxonomy' => null,                'meta' => 'perforations' ],
			[ 'label' => __( 'Printer', 'stampvault' ),          'taxonomy' => null,                'meta' => 'printer' ],
			[ 'label' => __( 'Printing Process', 'stampvault' ), 'taxonomy' => 'printing_process',  'meta' => null ],
			[ 'label' => __( 'Watermark', 'stampvault' ),        'taxonomy' => null,                'meta' => 'watermark' ],
			[ 'label' => __( 'Colors', 'stampvault' ),           'taxonomy' => null,                'meta' => 'colors' ],
			[ 'label' => __( 'Credits', 'stampvault' ),          'taxonomy' => 'credits',           'meta' => null ],
			[ 'label' => __( 'Catalog Codes', 'stampvault' ),    'taxonomy' => null,                'meta' => 'catalog_codes' ],
			[ 'label' => __( 'Themes', 'stampvault' ),           'taxonomy' => 'themes',            'meta' => null ],
		];

		$post_id = get_the_ID();
		// Preload catalog codes once
		$catalog_codes_raw = get_post_meta( $post_id, 'catalog_codes', true );
		$catalog_codes_list = [];
		if ( ! empty( $catalog_codes_raw ) ) {
			if ( is_array( $catalog_codes_raw ) ) {
				$catalog_codes_list = $catalog_codes_raw;
			} else {
				$decoded_tmp = json_decode( $catalog_codes_raw, true );
				if ( is_array( $decoded_tmp ) ) {
					$catalog_codes_list = $decoded_tmp;
				}
			}
		}

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
		// Build only rows that have data (skip empty meta / taxonomy / catalog codes)
		$rendered_rows = [];
		foreach ( $rows as $row ) {
			$label = esc_html( $row['label'] );
			$value_html = '';
			$has_value = false;

			if ( $row['taxonomy'] ) {
				$terms = get_the_terms( $post_id, $row['taxonomy'] );
				if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
					$names = wp_list_pluck( $terms, 'name' );
					$value_html = esc_html( implode( ', ', $names ) );
					$has_value = $value_html !== '';
				}
			} elseif ( $row['meta'] ) {
				if ( 'catalog_codes' === $row['meta'] ) {
					$list_items_html = '';
					foreach ( $catalog_codes_list as $item ) {
						$cat  = isset( $item['catalog'] ) ? trim( (string) $item['catalog'] ) : '';
						$code = isset( $item['code'] ) ? trim( (string) $item['code'] ) : '';
						if ( $cat || $code ) {
							$list_items_html .= '<li class="sv-catalog-codes-item">' . esc_html( $cat ) . ( $cat && $code ? ': ' : '' ) . esc_html( $code ) . '</li>';
						}
					}
					if ( $list_items_html ) {
						$value_html = '<ul class="sv-catalog-codes-list">' . $list_items_html . '</ul>';
						$has_value = true;
					}
				} else {
					$val = get_post_meta( $post_id, $row['meta'], true );
					if ( '' !== $val && null !== $val ) {
						$value_html = esc_html( $val );
						$has_value = true;
					}
				}
			}

			if ( $has_value ) {
				$rendered_rows[] = '<tr><th scope="row">' . $label . '</th><td class="sv-value">' . $value_html . '</td></tr>';
			}
		}

		if ( empty( $rendered_rows ) ) {
			return '';
		}

		ob_start();
		// Output in one echo without closing PHP to avoid stray newlines. Use empty string glue.
	echo '<div class="wp-block-stampvault-stamp-info"><table class="stampvault-stamp-info-table"><tbody>' . esc_html( implode( '', $rendered_rows ) ) . '</tbody></table></div>';
		$html = ob_get_clean();
		// Trim leading/trailing whitespace/newlines completely.
		return trim( $html );
	}
}
