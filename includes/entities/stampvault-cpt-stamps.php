<?php
/**
 * Register the 'Stamps' custom post type for StampVault.
 *
 * @package StampVault
 */

stampvault_security_check();

/**
 * Registers the 'stamps' custom post type for stamp collections.
 */
function stampvault_register_post_type_stamps() {
	$labels = array(
		'name'               => __( 'Stamps', 'stampvault' ),
		'singular_name'      => __( 'Stamp', 'stampvault' ),
		'menu_name'          => __( 'Stamps', 'stampvault' ),
		'name_admin_bar'     => __( 'Stamp', 'stampvault' ),
		'add_new'            => __( 'Add New', 'stampvault' ),
		'add_new_item'       => __( 'Add New Stamp', 'stampvault' ),
		'new_item'           => __( 'New Stamp', 'stampvault' ),
		'edit_item'          => __( 'Edit Stamp', 'stampvault' ),
		'view_item'          => __( 'View Stamp', 'stampvault' ),
		'all_items'          => __( 'All Stamps', 'stampvault' ),
		'search_items'       => __( 'Search Stamps', 'stampvault' ),
		'parent_item_colon'  => __( 'Parent Stamps:', 'stampvault' ),
		'not_found'          => __( 'No stamps found.', 'stampvault' ),
		'not_found_in_trash' => __( 'No stamps found in Trash.', 'stampvault' ),
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'has_archive'        => true,
		'show_in_menu'       => true,
		'supports'           => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
		'show_in_rest'       => true,
		'menu_icon'          => 'dashicons-tickets-alt',
	);

	register_post_type( 'stamps', $args );
}

add_action( 'init', 'stampvault_register_post_type_stamps' );
