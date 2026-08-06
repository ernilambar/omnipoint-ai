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

delete_option( 'omnipoint_ai_endpoint_url' );
delete_option( 'omnipoint_ai_default_text_model' );
