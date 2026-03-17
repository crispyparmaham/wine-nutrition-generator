<?php
/**
 * Generates and stores SVG QR codes for wine nutrition posts.
 * Also provides the admin meta box for preview and download.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\SvgWriter;

class Wine_Nutrition_QR {

	public function __construct() {
		add_action( 'save_post',      [ $this, 'generate' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_box' ] );
	}

	/**
	 * Generate and persist the QR code SVG whenever the post URL changes.
	 */
	public function generate( int $post_id ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( get_post_type( $post_id ) !== WINE_NUTRITION_CPT ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$post_url = get_permalink( $post_id );

		// Skip regeneration when the target URL has not changed.
		$stored_hash  = get_post_meta( $post_id, '_wine_nutrition_url_hash', true );
		$current_hash = md5( (string) $post_url );
		if ( $stored_hash === $current_hash ) {
			return;
		}

		$upload  = wp_upload_dir();
		$qr_dir  = trailingslashit( $upload['basedir'] ) . 'wine-nutrition-qrcodes';

		if ( ! file_exists( $qr_dir ) ) {
			wp_mkdir_p( $qr_dir );
		}

		// Use post ID + slug so filenames are unique even when two posts share a title.
		$slug      = sanitize_file_name( get_post_field( 'post_name', $post_id ) );
		$filename  = 'qr_' . $post_id . '_' . $slug . '.svg';
		$file_path = trailingslashit( $qr_dir ) . $filename;
		$file_url  = trailingslashit( $upload['baseurl'] ) . 'wine-nutrition-qrcodes/' . $filename;

		$qr_code = new QrCode( (string) $post_url );
		$writer  = new SvgWriter();
		$writer->write( $qr_code )->saveToFile( $file_path );

		update_post_meta( $post_id, '_qr_code_url', $file_url );
		update_post_meta( $post_id, '_wine_nutrition_url_hash', $current_hash );
	}

	public function add_meta_box(): void {
		add_meta_box(
			'wine_nutrition_qr_preview',
			'QR-Code Vorschau & Download',
			[ $this, 'render_meta_box' ],
			WINE_NUTRITION_CPT,
			'side'
		);
	}

	public function render_meta_box( WP_Post $post ): void {
		$qr_code_url = get_post_meta( $post->ID, '_qr_code_url', true );

		if ( $qr_code_url ) {
			echo '<p><strong>QR-Code Vorschau:</strong></p>';
			echo '<img src="' . esc_url( $qr_code_url ) . '" alt="QR-Code Vorschau" style="max-width:100%;height:auto;border:1px solid #ddd;padding:5px;margin-bottom:10px;">';
			echo '<p><a href="' . esc_url( $qr_code_url ) . '" download="qr_' . esc_attr( sanitize_file_name( get_the_title( $post->ID ) ) ) . '.svg" class="button button-primary">QR-Code herunterladen</a></p>';
		} else {
			echo '<p>Der QR-Code wird generiert, wenn der Beitrag veröffentlicht oder aktualisiert wird.</p>';
		}
	}
}
