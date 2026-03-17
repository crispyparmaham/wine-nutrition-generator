<?php
/**
 * Registers the "Nährwerte" custom post type and its ACF field group.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wine_Nutrition_CPT {

	public function __construct() {
		add_action( 'init',               [ $this, 'register_cpt' ] );
		add_action( 'acf/include_fields', [ $this, 'register_fields' ] );
	}

	public function register_cpt(): void {
		register_post_type( WINE_NUTRITION_CPT, [
			'labels' => [
				'name'               => 'Nährwerte',
				'singular_name'      => 'Nährwert',
				'add_new'            => 'Neu anlegen',
				'add_new_item'       => 'Neuen Nährwert anlegen',
				'edit_item'          => 'Nährwert bearbeiten',
				'new_item'           => 'Neuer Nährwert',
				'view_item'          => 'Nährwert ansehen',
				'search_items'       => 'Nährwerte suchen',
				'not_found'          => 'Keine Nährwerte gefunden',
				'not_found_in_trash' => 'Keine Nährwerte im Papierkorb',
				'menu_name'          => 'Nährwerte',
			],
			'public'      => true,
			'has_archive' => true,
			'menu_icon'   => 'dashicons-chart-bar',
			'supports'    => [ 'title', 'thumbnail' ],
			'rewrite'     => [ 'slug' => 'naehrwerte' ],
		] );
	}

	public function register_fields(): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		acf_add_local_field_group( [
			'key'    => 'group_6715f43aa8126',
			'title'  => 'Nährwerte',
			'fields' => [
				[
					'key'           => 'field_6715f4a92682a',
					'label'         => 'Produktbild',
					'name'          => 'produktbild',
					'type'          => 'image',
					'return_format' => 'array',
				],
				[
					'key'   => 'field_67174a8cd2739',
					'label' => 'Beschreibung',
					'name'  => 'beschreibung',
					'type'  => 'textarea',
				],
				[
					'key'           => 'field_volumen',
					'label'         => 'Menge (ml)',
					'name'          => 'volumen',
					'type'          => 'number',
					'instructions'  => 'Bitte die Menge des Inhaltes in ml eingeben.',
					'default_value' => 750,
					'placeholder'   => 'z.B. 750',
				],
				[
					'key'           => 'field_alkoholgehalt',
					'label'         => 'Alkoholgehalt (%vol)',
					'name'          => 'alkoholgehalt',
					'type'          => 'number',
					'instructions'  => 'Bitte den Alkoholgehalt in %vol eingeben.',
					'default_value' => '',
					'placeholder'   => 'z.B. 11.5',
					'step'          => '0.1',
				],
				[
					'key'           => 'field_restzucker',
					'label'         => 'Restzucker (g/l)',
					'name'          => 'restzucker',
					'type'          => 'number',
					'instructions'  => 'Bitte den Restzucker in g/l eingeben.',
					'default_value' => '',
					'placeholder'   => 'z.B. 5.2',
					'step'          => '0.1',
				],
				[
					'key'           => 'field_gesamtsaeure',
					'label'         => 'Gesamtsäure (g/l)',
					'name'          => 'gesamtsaeure',
					'type'          => 'number',
					'instructions'  => 'Bitte die Gesamtsäure in g/l eingeben.',
					'default_value' => '',
					'placeholder'   => 'z.B. 6.8',
					'step'          => '0.1',
				],
				[
					'key'           => 'field_glyzerin',
					'label'         => 'Glyzerin (g/l)',
					'name'          => 'glyzerin',
					'type'          => 'number',
					'instructions'  => 'Standardwert: 8 g/l. Kann produktspezifisch angepasst werden.',
					'default_value' => 8,
					'placeholder'   => '8',
					'step'          => '0.1',
				],
				[
					'key'           => 'field_zutaten',
					'label'         => 'Zutaten',
					'name'          => 'zutaten',
					'type'          => 'textarea',
					'instructions'  => 'Zutaten des Produkts. Der vorausgefüllte Standardwert kann überschrieben werden.',
					'default_value' => 'Trauben, Saccharose, Kohlendioxid, Konservierungsstoff: Sulfite, Säureregulatoren: Weinsäure, Milchsäure, Äpfelsäure, Stabilisatoren: CMC+',
					'rows'          => 3,
				],
			],
			'location' => [
				[
					[
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => WINE_NUTRITION_CPT,
					],
				],
			],
		] );
	}
}
