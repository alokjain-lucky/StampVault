<?php
/**
 * Register taxonomies for the 'Stamps' custom post type for StampVault.
 *
 * @package StampVault
 *
 * This file contains the registration logic for all custom taxonomies attached to the 'stamps' post type.
 * Each taxonomy helps organize and classify stamp entries in the StampVault plugin.
 *
 * - Registered on every request via the 'init' action.
 * - Exposed in the REST API for integration with Gutenberg and external apps.
 */

// Security check to prevent direct access to this file.
stampvault_security_check();

/**
 * Registers all taxonomies related to the 'stamps' post type.
 *
 * @see https://developer.wordpress.org/reference/functions/register_taxonomy/
 */
function stampvault_register_stamp_taxonomies() {
	// Stamp Sets: Group stamps that belong to the same set or series.
	$labels_stamp_sets = array(
		'name'                       => __( 'Stamp Sets', 'stampvault' ),
		'singular_name'              => __( 'Stamp Set', 'stampvault' ),
		'search_items'               => __( 'Search Stamp Sets', 'stampvault' ),
		'all_items'                  => __( 'All Stamp Sets', 'stampvault' ),
		'parent_item'                => __( 'Parent Stamp Set', 'stampvault' ),
		'parent_item_colon'          => __( 'Parent Stamp Set:', 'stampvault' ),
		'edit_item'                  => __( 'Edit Stamp Set', 'stampvault' ),
		'view_item'                  => __( 'View Stamp Set', 'stampvault' ),
		'update_item'                => __( 'Update Stamp Set', 'stampvault' ),
		'add_new_item'               => __( 'Add Stamp Set', 'stampvault' ),
		'new_item_name'              => __( 'New Stamp Set Name', 'stampvault' ),
		'menu_name'                  => __( 'Stamp Sets', 'stampvault' ),
		'not_found'                  => __( 'No stamp sets found', 'stampvault' ),
	);
	register_taxonomy(
		'stamp_sets',
		'stamps',
		array(
			'labels'       => $labels_stamp_sets,
			'description'  => __( 'Group stamps that belong to the same set or series.', 'stampvault' ),
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Themes: Thematic topics depicted on the stamps (e.g., birds, sports, history).
	$labels_themes = array(
		'name'          => __( 'Themes', 'stampvault' ),
		'singular_name' => __( 'Theme', 'stampvault' ),
		'search_items'  => __( 'Search Themes', 'stampvault' ),
		'all_items'     => __( 'All Themes', 'stampvault' ),
		'edit_item'     => __( 'Edit Theme', 'stampvault' ),
		'view_item'     => __( 'View Theme', 'stampvault' ),
		'update_item'   => __( 'Update Theme', 'stampvault' ),
		'add_new_item'  => __( 'Add Theme', 'stampvault' ),
		'new_item_name' => __( 'New Theme Name', 'stampvault' ),
		'menu_name'     => __( 'Themes', 'stampvault' ),
		'not_found'     => __( 'No themes found', 'stampvault' ),
	);
	register_taxonomy(
		'themes',
		'stamps',
		array(
			'labels'       => $labels_themes,
			'description'  => __( 'Thematic topics depicted on the stamps (e.g., birds, sports, history).', 'stampvault' ),
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Stamp Types: Classification such as definitive, commemorative, airmail, etc.
	$labels_stamp_types = array(
		'name'          => __( 'Stamp Types', 'stampvault' ),
		'singular_name' => __( 'Stamp Type', 'stampvault' ),
		'search_items'  => __( 'Search Stamp Types', 'stampvault' ),
		'all_items'     => __( 'All Stamp Types', 'stampvault' ),
		'edit_item'     => __( 'Edit Stamp Type', 'stampvault' ),
		'view_item'     => __( 'View Stamp Type', 'stampvault' ),
		'update_item'   => __( 'Update Stamp Type', 'stampvault' ),
		'add_new_item'  => __( 'Add Stamp Type', 'stampvault' ),
		'new_item_name' => __( 'New Stamp Type Name', 'stampvault' ),
		'menu_name'     => __( 'Stamp Types', 'stampvault' ),
		'not_found'     => __( 'No stamp types found', 'stampvault' ),
	);
	register_taxonomy(
		'stamp_types',
		'stamps',
		array(
			'labels'       => $labels_stamp_types,
			'description'  => __( 'Classification such as definitive, commemorative, airmail, etc.', 'stampvault' ),
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Printing Process: The printing technique used (e.g., lithography, engraving).
	$labels_printing_process = array(
		'name'          => __( 'Printing Processes', 'stampvault' ),
		'singular_name' => __( 'Printing Process', 'stampvault' ),
		'search_items'  => __( 'Search Printing Processes', 'stampvault' ),
		'all_items'     => __( 'All Printing Processes', 'stampvault' ),
		'edit_item'     => __( 'Edit Printing Process', 'stampvault' ),
		'view_item'     => __( 'View Printing Process', 'stampvault' ),
		'update_item'   => __( 'Update Printing Process', 'stampvault' ),
		'add_new_item'  => __( 'Add Printing Process', 'stampvault' ),
		'new_item_name' => __( 'New Printing Process Name', 'stampvault' ),
		'menu_name'     => __( 'Printing Processes', 'stampvault' ),
		'not_found'     => __( 'No printing processes found', 'stampvault' ),
	);
	register_taxonomy(
		'printing_process',
		'stamps',
		array(
			'labels'       => $labels_printing_process,
			'description'  => __( 'The printing technique used for the stamp (e.g., lithography, engraving).', 'stampvault' ),
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Countries: The country or territory that issued the stamp.
	$labels_countries = array(
		'name'               => __( 'Countries', 'stampvault' ),
		'singular_name'      => __( 'Country', 'stampvault' ),
		'search_items'       => __( 'Search Countries', 'stampvault' ),
		'all_items'          => __( 'All Countries', 'stampvault' ),
		'parent_item'        => __( 'Parent Country', 'stampvault' ),
		'parent_item_colon'  => __( 'Parent Country:', 'stampvault' ),
		'edit_item'          => __( 'Edit Country', 'stampvault' ),
		'view_item'          => __( 'View Country', 'stampvault' ),
		'update_item'        => __( 'Update Country', 'stampvault' ),
		'add_new_item'       => __( 'Add Country', 'stampvault' ),
		'new_item_name'      => __( 'New Country Name', 'stampvault' ),
		'menu_name'          => __( 'Countries', 'stampvault' ),
		'not_found'          => __( 'No countries found', 'stampvault' ),
	);
	register_taxonomy(
		'countries',
		'stamps',
		array(
			'labels'       => $labels_countries,
			'description'  => __( 'The country or territory that issued the stamp.', 'stampvault' ),
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Credits: Designers, engravers, or other contributors to the stamp.
	$labels_credits = array(
		'name'          => __( 'Credits', 'stampvault' ),
		'singular_name' => __( 'Credit', 'stampvault' ),
		'search_items'  => __( 'Search Credits', 'stampvault' ),
		'all_items'     => __( 'All Credits', 'stampvault' ),
		'edit_item'     => __( 'Edit Credit', 'stampvault' ),
		'view_item'     => __( 'View Credit', 'stampvault' ),
		'update_item'   => __( 'Update Credit', 'stampvault' ),
		'add_new_item'  => __( 'Add Credit', 'stampvault' ),
		'new_item_name' => __( 'New Credit Name', 'stampvault' ),
		'menu_name'     => __( 'Credits', 'stampvault' ),
		'not_found'     => __( 'No credits found', 'stampvault' ),
	);
	register_taxonomy(
		'credits',
		'stamps',
		array(
			'labels'       => $labels_credits,
			'description'  => __( 'Designers, engravers, or other contributors to the stamp.', 'stampvault' ),
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);
}

// Register the taxonomies on the 'init' action.
add_action( 'init', 'stampvault_register_stamp_taxonomies' );
