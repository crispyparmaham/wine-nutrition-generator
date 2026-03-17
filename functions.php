<?php
/**
 * Plugin Name:  Nährwerte QR Code Generator
 * Description:  Registers the "Nährwerte" CPT with ACF fields and generates a QR code linking to the post.
 * Version:      2.0.0
 * Author:       more than ads
 * Text Domain:  wine-nutrition
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Composer autoloader (endroid/qr-code and its dependencies).
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

define( 'WINE_NUTRITION_VERSION', '2.0.0' );
// CPT slug kept as "naehrwerte" for backward compat with existing posts in the database.
define( 'WINE_NUTRITION_CPT', 'naehrwerte' );
define( 'WINE_NUTRITION_DIR', plugin_dir_path( WINE_NUTRITION_FILE ) );
define( 'WINE_NUTRITION_URL', plugin_dir_url( WINE_NUTRITION_FILE ) );

require_once WINE_NUTRITION_DIR . 'includes/class-wine-nutrition-cpt.php';
require_once WINE_NUTRITION_DIR . 'includes/class-wine-nutrition-qr.php';
require_once WINE_NUTRITION_DIR . 'includes/class-wine-nutrition-shortcode.php';
require_once WINE_NUTRITION_DIR . 'includes/class-wine-nutrition.php';

Wine_Nutrition::init();
