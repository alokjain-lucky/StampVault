<?php
/**
 * Catalogs Settings Tab
 *
 * Provides UI to manage list of stamp catalogs used by the catalog_codes meta.
 */
// Catalogs settings (option + tab renderer).

if ( ! defined( 'STAMPVAULT_OPTION_CATALOGS' ) ) {
	define( 'STAMPVAULT_OPTION_CATALOGS', 'stampvault_catalog_list' );
}

/**
 * Return the plugin's default catalog names.
 *
 * Filter: 'stampvault_default_catalogs'
 * Allows third-party code to change the initial / fallback catalog list.
 *
 * Example:
 * add_filter( 'stampvault_default_catalogs', function( $defaults ) {
 *     $defaults[] = 'Custom Cat';
 *     return $defaults;
 * } );
 *
 * @return string[]
 */
function stampvault_get_default_catalogs() {
	$defaults = [ 'Scott', 'Michel', 'Stanley Gibbons', 'Yvert et Tellier', 'Other' ];
	/**
	 * Filter the default catalog names used when no option is saved.
	 *
	 * @param string[] $defaults Default catalog names.
	 */
	return apply_filters( 'stampvault_default_catalogs', $defaults );
}

/**
 * Retrieve the saved catalog list from the options table.
 * Falls back to filtered defaults if option missing/not array.
 * Empty strings are stripped earlier during sanitization.
 *
 * @return string[]
 */
function stampvault_get_catalogs_option() {
	$value = get_option( STAMPVAULT_OPTION_CATALOGS );
	if ( ! is_array( $value ) ) {
		$value = stampvault_get_default_catalogs();
	}
	return array_values( array_filter( array_map( 'sanitize_text_field', $value ) ) );
}

/**
 * Register the option storing the catalog list.
 */
function stampvault_register_catalogs_option() {
	register_setting( 'stampvault_catalogs', STAMPVAULT_OPTION_CATALOGS, [
		'sanitize_callback' => 'stampvault_sanitize_catalogs_list',
		'default' => stampvault_get_default_catalogs(),
	] );
}
add_action( 'admin_init', 'stampvault_register_catalogs_option' );

/**
 * Handle Reset to Defaults request for catalog list.
 */
function stampvault_handle_catalogs_reset() {
	if ( ! is_admin() ) return;
	if ( ! current_user_can( 'manage_options' ) ) return;
	if ( empty( $_POST['stampvault_reset_catalogs'] ) ) return;
	check_admin_referer( 'stampvault_reset_catalogs_action', 'stampvault_reset_catalogs_nonce' );
	update_option( STAMPVAULT_OPTION_CATALOGS, stampvault_get_default_catalogs() );
	add_settings_error( 'stampvault_catalogs', 'catalogs_reset', __( 'Catalog list reset to defaults.', 'stampvault' ), 'updated' );
	wp_safe_redirect( add_query_arg( [ 'post_type' => 'stamps', 'page' => STAMPVAULT_SETTINGS_PAGE, 'tab' => 'catalogs', 'reset' => '1' ], admin_url( 'edit.php' ) ) );
	exit;
}
add_action( 'admin_init', 'stampvault_handle_catalogs_reset' );

/**
 * Sanitize the submitted catalog list.
 * - Force array
 * - Strip empties
 * - Trim & sanitize
 * - De-duplicate case-insensitively (first occurrence wins)
 *
 * @param mixed $value Raw option value from request.
 * @return string[] Clean catalog names (may be empty)
 */
function stampvault_sanitize_catalogs_list( $value ) {
	if ( ! is_array( $value ) ) return [];
	$clean = [];
	foreach ( $value as $catalog ) {
		$catalog = trim( wp_unslash( $catalog ) );
		if ( $catalog === '' ) continue;
		$clean[] = sanitize_text_field( $catalog );
	}
	$seen = [];
	$deduped = [];
	foreach ( $clean as $c ) {
		$key = mb_strtolower( $c );
		if ( isset( $seen[ $key ] ) ) continue;
		$seen[ $key ] = true;
		$deduped[] = $c;
	}
	return $deduped;
}

/**
 * Public helper for other components (meta UI, block rendering, etc.).
 *
 * @return string[]
 */
function stampvault_get_catalog_list() { return stampvault_get_catalogs_option(); }

// Tab renderer hookup.
add_action( 'stampvault_render_settings_tab_catalogs', 'stampvault_render_settings_tab_catalogs' );
function stampvault_render_settings_tab_catalogs() {
	settings_errors();
	?>
	<form action="options.php" method="post" style="max-width:680px;">
		<?php settings_fields( 'stampvault_catalogs' ); ?>
		<table class="form-table" role="presentation">
			<tr valign="top">
				<th scope="row"><?php esc_html_e( 'Catalog List', 'stampvault' ); ?></th>
				<td>
					<p class="description" style="margin-top:0;"><?php esc_html_e( 'Manage catalog names. These names power the catalog code inputs.', 'stampvault' ); ?></p>
					<ul id="stampvault-catalogs-list" style="margin:0 0 12px 0; padding:0; list-style:none;">
						<?php $__catalogs = stampvault_get_catalogs_option(); if ( empty( $__catalogs ) ) { $__catalogs = ['']; } foreach ( $__catalogs as $catalog ) : ?>
							<li style="margin:4px 0;">
								<input type="text" name="<?php echo esc_attr( STAMPVAULT_OPTION_CATALOGS ); ?>[]" value="<?php echo esc_attr( $catalog ); ?>" style="min-width:260px;" />
								<button type="button" class="button link-button-remove" onclick="this.closest('li').remove();" aria-label="<?php esc_attr_e( 'Remove catalog', 'stampvault' ); ?>" style="vertical-align:middle;">&times;</button>
							</li>
						<?php endforeach; ?>
					</ul>
					<button type="button" class="button" id="stampvault-add-catalog"><?php esc_html_e( 'Add Catalog', 'stampvault' ); ?></button>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>

	<form method="post" style="margin-top:28px; max-width:680px;">
		<?php wp_nonce_field( 'stampvault_reset_catalogs_action', 'stampvault_reset_catalogs_nonce' ); ?>
		<input type="hidden" name="stampvault_reset_catalogs" value="1" />
		<button type="submit" class="button button-secondary button-link-delete" onclick="return confirm('<?php echo esc_js( __( 'Reset catalog list to defaults? This will overwrite the current list.', 'stampvault' ) ); ?>');" style="margin-top:4px;">
			<?php esc_html_e( 'Reset to Defaults', 'stampvault' ); ?>
		</button>
		<p class="description" style="margin:8px 0 0;">
			<?php printf( esc_html__( 'Default catalogs: %s', 'stampvault' ), esc_html( implode( ', ', stampvault_get_default_catalogs() ) ) ); ?>
		</p>
	</form>
	<script>
	(function(){
		const addBtn = document.getElementById('stampvault-add-catalog');
		if(!addBtn) return;
		addBtn.addEventListener('click', function(){
			const ul = document.getElementById('stampvault-catalogs-list');
			const li = document.createElement('li');
			li.style.margin = '4px 0';
			li.innerHTML = '<input type="text" name="<?php echo esc_js( STAMPVAULT_OPTION_CATALOGS ); ?>[]" value="" style="min-width:260px;" /> <button type="button" class="button link-button-remove" aria-label="<?php esc_attr_e( 'Remove catalog', 'stampvault' ); ?>" style="vertical-align:middle;">&times;</button>';
			ul.appendChild(li);
			li.querySelector('button').addEventListener('click', function(){ li.remove(); });
		});
		document.querySelectorAll('#stampvault-catalogs-list .link-button-remove').forEach(btn => {
			btn.addEventListener('click', function(){ this.closest('li').remove(); });
		});
	})();
	</script>
	<?php
}
