<?php
/**
 * Uninstall script for StampVault plugin.
 *
 * @package StampVault
 */

// Security check to prevent direct access
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove all 'stamps' custom post type posts
$stamps = get_posts( array(
	'post_type'      => 'stamps',
	'numberposts'    => -1,
	'post_status'    => 'any',
	'fields'         => 'ids',
) );

if ( ! empty( $stamps ) ) {
	foreach ( $stamps as $stamp_id ) {
		wp_delete_post( $stamp_id, true );
	}
}

// Remove all terms from custom taxonomies
$taxonomies = array( 'stamp_sets', 'themes', 'stamp_types', 'printing_process', 'countries', 'credits' );
foreach ( $taxonomies as $taxonomy ) {
	$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			wp_delete_term( $term->term_id, $taxonomy );
		}
	}
}

// Flush rewrite rules to remove CPT and taxonomy permalinks
if ( function_exists( 'flush_rewrite_rules' ) ) {
	flush_rewrite_rules();
}
