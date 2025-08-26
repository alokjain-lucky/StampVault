<?php
/**
 * Register meta fields for the 'Stamps' custom post type.
 *
 * @package StampVault
 *
 * This file contains the registration logic for custom meta fields attached to the 'stamps' post type.
 * These fields store additional details about each stamp, such as denomination, release date, and more.
 *
 * - Registered on every request via the 'init' action.
 * - Exposed in the REST API for integration with Gutenberg and external apps.
 */

// Security check to prevent direct access to this file.
stampvault_security_check();

/**
 * Register custom meta fields for stamps.
 *
 * @see https://developer.wordpress.org/reference/functions/register_post_meta/
 */
function stampvault_register_stamp_meta_fields() {
	$fields = [
		'sub_title' => [
			'description' => 'Sub Title / Note',
			'single'      => true,
			'show_in_rest'=> true,
			'type'        => 'string',
		],
		'date_of_release' => [
			'description' => 'Date of release',
			'single'      => true,
			'show_in_rest'=> true,
			'type'        => 'string',
		],
		'denomination' => [
			'description' => 'Denomination',
			'single'      => true,
			'show_in_rest'=> true,
			'type'        => 'string',
		],
		'quantity' => [
			'description' => 'Quantity',
			'single'      => true,
			'show_in_rest'=> true,
			'type'        => 'string',
		],
		'perforations' => [
			'description' => 'Perforations',
			'single'      => true,
			'show_in_rest'=> true,
			'type'        => 'string',
		],
		'printer' => [
			'description' => 'Printer',
			'single'      => true,
			'show_in_rest'=> true,
			'type'        => 'string',
		],
		'watermark' => [
			'description' => 'Watermark',
			'single'      => true,
			'show_in_rest'=> true,
			'type'        => 'string',
		],
		'colors' => [
			'description' => 'Colors',
			'single'      => true,
			'show_in_rest'=> true,
			'type'        => 'string',
		],
	];

	foreach ( $fields as $key => $args ) {
		register_post_meta( 'stamps', $key, $args );
	}
}
// Register the meta fields on the 'init' action.
add_action( 'init', 'stampvault_register_stamp_meta_fields' );
