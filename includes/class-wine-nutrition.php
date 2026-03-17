<?php
/**
 * Main bootstrap class.
 * Wires up all sub-components, checks for the ACF dependency,
 * and provides the static uninstall callback.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wine_Nutrition {

	/**
	 * Instantiate all sub-components and register shared hooks.
	 * Called once from functions.php.
	 */
	public static function init(): void {
		new Wine_Nutrition_CPT();
		new Wine_Nutrition_QR();
		new Wine_Nutrition_Shortcode();

		add_action( 'admin_notices', [ __CLASS__, 'check_acf' ] );
		register_uninstall_hook( WINE_NUTRITION_FILE, [ __CLASS__, 'uninstall' ] );
	}

	// Show an admin notice when ACF Pro is not active.
	public static function check_acf(): void {
		if ( ! class_exists( 'ACF' ) ) {
			echo '<div class="notice notice-error"><p><strong>Nährwerte Plugin:</strong> Advanced Custom Fields Pro ist nicht installiert oder aktiviert!</p></div>';
		}
	}

	// Remove the QR code upload directory on plugin uninstall.
	public static function uninstall(): void {
		$upload = wp_upload_dir();
		$qr_dir = trailingslashit( $upload['basedir'] ) . 'wine-nutrition-qrcodes';

		if ( is_dir( $qr_dir ) ) {
			$files = glob( trailingslashit( $qr_dir ) . '*.svg' ) ?: [];
			array_map( 'unlink', $files );
			rmdir( $qr_dir );
		}
	}
}
