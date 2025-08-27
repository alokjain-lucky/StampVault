<?php
/**
 * Meta box interface for Stamps meta fields (Classic Editor).
 *
 * @package StampVault
 *
 * This file contains the UI logic for displaying and saving custom meta fields for the 'stamps' post type in the Classic Editor.
 *
 * - Only shown if the Classic Editor is active (not Gutenberg).
 * - Provides a form for entering additional stamp details.
 * - Handles saving and sanitizing meta field values.
 */

// Security check to prevent direct access to this file.
stampvault_security_check();

// Catalog list now provided by settings (includes/settings/catalogs-settings.php)

/**
 * Add meta box for Stamps meta fields (Classic Editor only).
 *
 * @see https://developer.wordpress.org/reference/functions/add_meta_box/
 */
function stampvault_add_stamps_meta_box() {
	// Skip entirely when block editor is active for stamps post type
	if ( function_exists( 'use_block_editor_for_post_type' ) && use_block_editor_for_post_type( 'stamps' ) ) {
		return;
	}
	add_meta_box(
		'stampvault_stamps_meta',
		'Additional Stamp Details',
		'stampvault_stamps_meta_box_callback',
		'stamps',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'stampvault_add_stamps_meta_box' );

/**
 * Render the meta box form fields for stamp meta data, including catalog codes.
 *
 * @param WP_Post $post The current post object.
 */
function stampvault_stamps_meta_box_callback( $post ) {
	$fields = [
		'sub_title' => 'Sub Title / Note',
		'date_of_release' => 'Date of release',
		'denomination' => 'Denomination',
		'quantity' => 'Quantity',
		'perforations' => 'Perforations',
		'printer' => 'Printer',
		'watermark' => 'Watermark',
		'colors' => 'Colors',
	];
	wp_nonce_field( 'stampvault_stamps_meta_box', 'stampvault_stamps_meta_box_nonce' );
	foreach ( $fields as $key => $label ) {
		$value = get_post_meta( $post->ID, $key, true );
		if ( $key === 'date_of_release' ) {
			echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . ':</strong></label><br />';
			echo '<input type="date" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="widefat" /></p>';
		} else {
			echo '<p><label for="' . esc_attr( $key ) . '"><strong>' . esc_html( $label ) . ':</strong></label><br />';
			echo '<input type="text" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" value="' . esc_attr( $value ) . '" class="widefat" /></p>';
		}
	}

	// Catalog Codes UI
	$catalog_codes = get_post_meta( $post->ID, 'catalog_codes', true );
	$catalog_codes = is_array( $catalog_codes ) ? $catalog_codes : json_decode( $catalog_codes, true );
	$catalogs = stampvault_get_catalog_list();
	if ( ! is_array( $catalog_codes ) ) {
		$catalog_codes = [];
	}
	echo '<hr><strong>Catalog Codes</strong><br><small>Add codes from various stamp catalogs.</small>';
	echo '<table id="stampvault-catalog-codes-table" style="width:100%; max-width:none; margin-top:8px; border-collapse:collapse; background:#fafbfc; border:1px solid #e5e5e5;">';
	echo '<thead><tr style="background:#f1f1f1;"><th style="padding:8px 6px; text-align:left; width:40%;">Catalog</th><th style="padding:8px 6px; text-align:left; width:40%;">Code</th><th style="width:20%;"></th></tr></thead><tbody>';
	if ( empty( $catalog_codes ) ) {
		$catalog_codes[] = [ 'catalog' => '', 'code' => '' ];
	}
	foreach ( $catalog_codes as $i => $row ) {
		echo '<tr>';
		echo '<td style="padding:6px 4px;"><select name="catalog_codes['.$i.'][catalog]" style="width:100%;">';
		foreach ( $catalogs as $cat ) {
			$selected = ( isset($row['catalog']) && $row['catalog'] === $cat ) ? 'selected' : '';
			echo '<option value="'.esc_attr($cat).'" '.$selected.'>'.esc_html($cat).'</option>';
		}
		echo '</select></td>';
		echo '<td style="padding:6px 4px;"><input type="text" name="catalog_codes['.$i.'][code]" value="'.esc_attr($row['code'] ?? '').'" class="widefat" style="width:100%;" /></td>';
		echo '<td style="padding:6px 4px; text-align:center;"><button type="button" class="button stampvault-remove-catalog-row" style="background:#e74c3c; color:#fff; border:none;">Remove</button></td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
	echo '<button type="button" class="button" id="stampvault-add-catalog-row" style="margin-top:8px; background:#0073aa; color:#fff;">Add Catalog Code</button>';
}

/**
 * Save handler for Stamps meta box fields.
 *
 * @param int $post_id The ID of the post being saved.
 */
function stampvault_save_stamps_meta_box( $post_id ) {
	if ( ! isset( $_POST['stampvault_stamps_meta_box_nonce'] ) ) {
		return;
	}
	if ( ! wp_verify_nonce( $_POST['stampvault_stamps_meta_box_nonce'], 'stampvault_stamps_meta_box' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( isset( $_POST['post_type'] ) && 'stamps' === $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	$fields = [
		'sub_title',
		'date_of_release',
		'denomination',
		'quantity',
		'perforations',
		'printer',
		'watermark',
		'colors',
	];
	foreach ( $fields as $key ) {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta( $post_id, $key, sanitize_text_field( $_POST[ $key ] ) );
		}
	}
	// Save catalog codes as JSON
	if ( isset( $_POST['catalog_codes'] ) && is_array( $_POST['catalog_codes'] ) ) {
		$cleaned = array();
		foreach ( $_POST['catalog_codes'] as $row ) {
			if ( ! empty( $row['catalog'] ) && ! empty( $row['code'] ) ) {
				$cleaned[] = [
					'catalog' => sanitize_text_field( $row['catalog'] ),
					'code'    => sanitize_text_field( $row['code'] ),
				];
			}
		}
		update_post_meta( $post_id, 'catalog_codes', wp_json_encode( $cleaned ) );
	} else {
		delete_post_meta( $post_id, 'catalog_codes' );
	}
}
add_action( 'save_post', 'stampvault_save_stamps_meta_box' );

/**
 * Enqueue CSS and JS for the catalog codes meta box UI in the Classic Editor.
 */
function stampvault_enqueue_meta_box_assets( $hook ) {
	global $post;
	if ( $hook === 'post-new.php' || $hook === 'post.php' ) {
		if ( isset( $post ) && $post->post_type === 'stamps' ) {
			wp_enqueue_style( 'stampvault-meta-stamps-ui', plugin_dir_url( __FILE__ ) . 'stampvault-meta-stamps-ui.css' );
			wp_enqueue_script( 'stampvault-meta-stamps-ui', plugin_dir_url( __FILE__ ) . 'stampvault-meta-stamps-ui.js', array( 'jquery' ), false, true );
			wp_localize_script( 'stampvault-meta-stamps-ui', 'stampvaultCatalogList', stampvault_get_catalog_list() );
		}
	}
}
add_action( 'admin_enqueue_scripts', 'stampvault_enqueue_meta_box_assets' );

/**
 * Remove the default "Custom Fields" meta box for the 'stamps' post type to avoid duplicate UI.
 * We provide a tailored meta box instead, so the generic one is unnecessary.
 */
function stampvault_remove_stamps_default_custom_fields_box() {
	remove_meta_box( 'postcustom', 'stamps', 'normal' );
	remove_meta_box( 'postcustom', 'stamps', 'advanced' );
	remove_meta_box( 'postcustom', 'stamps', 'side' );
}
add_action( 'admin_menu', 'stampvault_remove_stamps_default_custom_fields_box', 20 );
