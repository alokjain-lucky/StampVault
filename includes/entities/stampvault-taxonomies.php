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
	register_taxonomy(
		'stamp_sets',
		'stamps',
		array(
			'label'        => __( 'Stamp Sets', 'stampvault' ),
			'description'  => __( 'Group stamps that belong to the same set or series.', 'stampvault' ),
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Themes: Thematic topics depicted on the stamps (e.g., birds, sports, history).
	register_taxonomy(
		'themes',
		'stamps',
		array(
			'label'        => __( 'Themes', 'stampvault' ),
			'description'  => __( 'Thematic topics depicted on the stamps (e.g., birds, sports, history).', 'stampvault' ),
			'hierarchical' => false,
			'show_in_rest' => true,
		)
	);

	// Stamp Types: Classification such as definitive, commemorative, airmail, etc.
	register_taxonomy(
		'stamp_types',
		'stamps',
		array(
			'label'        => __( 'Stamp Types', 'stampvault' ),
			'description'  => __( 'Classification such as definitive, commemorative, airmail, etc.', 'stampvault' ),
			'hierarchical' => false,
			'show_in_rest' => true,
		)
	);

	// Printing Process: The printing technique used (e.g., lithography, engraving).
	register_taxonomy(
		'printing_process',
		'stamps',
		array(
			'label'        => __( 'Printing Process', 'stampvault' ),
			'description'  => __( 'The printing technique used for the stamp (e.g., lithography, engraving).', 'stampvault' ),
			'hierarchical' => false,
			'show_in_rest' => true,
		)
	);

	// Countries: The country or territory that issued the stamp.
	register_taxonomy(
		'countries',
		'stamps',
		array(
			'label'        => __( 'Countries', 'stampvault' ),
			'description'  => __( 'The country or territory that issued the stamp.', 'stampvault' ),
			'hierarchical' => true,
			'show_in_rest' => true,
		)
	);

	// Credits: Designers, engravers, or other contributors to the stamp.
	register_taxonomy(
		'credits',
		'stamps',
		array(
			'label'        => __( 'Credits', 'stampvault' ),
			'description'  => __( 'Designers, engravers, or other contributors to the stamp.', 'stampvault' ),
			'hierarchical' => false,
			'show_in_rest' => true,
		)
	);
}

// Register the taxonomies on the 'init' action.
add_action( 'init', 'stampvault_register_stamp_taxonomies' );
