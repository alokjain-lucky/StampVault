<?php
/**
 * Custom columns and filters for Stamps CPT in admin.
 */

stampvault_security_check();

/**
 * Add custom columns to the Stamps CPT admin table.
 */
function stampvault_add_stamps_columns( $columns ) {
	$columns['stamp_types'] = __( 'Stamp Type', 'stampvault' );
	$columns['countries'] = __( 'Country', 'stampvault' );
	$columns['themes'] = __( 'Themes', 'stampvault' );
	return $columns;
}
add_filter( 'manage_stamps_posts_columns', 'stampvault_add_stamps_columns' );

/**
 * Populate custom columns in the Stamps CPT admin table.
 */
function stampvault_render_stamps_columns( $column, $post_id ) {
	switch ( $column ) {
		   case 'stamp_types':
			   $types = get_the_term_list( $post_id, 'stamp_types', '', ', ' );
			   echo $types ? wp_kses_post( $types ) : '—';
			   break;
		   case 'countries':
			   $countries = get_the_term_list( $post_id, 'countries', '', ', ' );
			   echo $countries ? wp_kses_post( $countries ) : '—';
			   break;
		   case 'themes':
			   $themes = get_the_term_list( $post_id, 'themes', '', ', ' );
			   echo $themes ? wp_kses_post( $themes ) : '—';
			   break;
	}
}
add_action( 'manage_stamps_posts_custom_column', 'stampvault_render_stamps_columns', 10, 2 );

/**
 * Make custom columns sortable.
 */
function stampvault_stamps_sortable_columns( $columns ) {
	$columns['stamp_types'] = 'stamp_types';
	$columns['countries'] = 'countries';
	$columns['themes'] = 'themes';
	return $columns;
}
add_filter( 'manage_edit-stamps_sortable_columns', 'stampvault_stamps_sortable_columns' );

/**
 * Add dropdown filters for Stamp Type, Country, and Themes in admin list.
 */
function stampvault_stamps_filters() {
	global $typenow;
	if ( $typenow !== 'stamps' ) {
		return;
	}
	$taxonomies = [
		'stamp_types' => __( 'Stamp Types', 'stampvault' ),
		'countries'   => __( 'Countries', 'stampvault' ),
		'themes'      => __( 'Themes', 'stampvault' ),
	];
	foreach ( $taxonomies as $tax => $label ) {
		$terms = get_terms( [ 'taxonomy' => $tax, 'hide_empty' => false ] );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			echo '<select name="' . esc_attr( $tax ) . '" class="postform">';
			echo '<option value="">' . esc_html( $label ) . '</option>';
			   foreach ( $terms as $term ) {
				   $selected = ( isset( $_GET[ $tax ] ) && $_GET[ $tax ] == $term->slug ) ? ' selected="selected"' : '';
				   echo wp_kses_post( '<option value="' . esc_attr( $term->slug ) . '"' . $selected . '>' . esc_html( $term->name ) . '</option>' );
			   }
			echo '</select>';
		}
	}
}
add_action( 'restrict_manage_posts', 'stampvault_stamps_filters' );

/**
 * Filter posts by taxonomy in admin list.
 */
function stampvault_stamps_filter_query( $query ) {
	global $pagenow;
	$typenow = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
	if ( $pagenow == 'edit.php' && $typenow == 'stamps' ) {
		$taxonomies = [ 'stamp_types', 'countries', 'themes' ];
		foreach ( $taxonomies as $tax ) {
			if ( ! empty( $_GET[ $tax ] ) ) {
				$query->query_vars[ $tax ] = $_GET[ $tax ];
			}
		}
	}
}
add_filter( 'parse_query', 'stampvault_stamps_filter_query' );
