<?php
/**
 * Uninstall handler.
 *
 * @package Nilambar\OmnipointAi
 */

declare( strict_types=1 );

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Deletes the plugin options for the current site.
 *
 * @since 1.0.0
 */
function omnipoint_ai_delete_options(): void {
	delete_option( 'omnipoint_ai_endpoint_url' );
	delete_option( 'omnipoint_ai_default_text_model' );
	delete_option( 'connectors_ai_omnipoint_api_key' );
}

if ( is_multisite() ) {
	$omnipoint_ai_site_ids = get_sites(
		[
			'fields' => 'ids',
			'number' => 0,
		]
	);

	foreach ( $omnipoint_ai_site_ids as $omnipoint_ai_site_id ) {
		switch_to_blog( (int) $omnipoint_ai_site_id );
		omnipoint_ai_delete_options();
		restore_current_blog();
	}
} else {
	omnipoint_ai_delete_options();
}
