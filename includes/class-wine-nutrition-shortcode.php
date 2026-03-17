<?php
/**
 * Frontend shortcode [wine_nutrition_fields] / [naehrwerte_felder].
 * Also enqueues the plugin stylesheet on singular nutrition pages.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Wine_Nutrition_Shortcode {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		// Old shortcode name kept for pages already using it.
		add_shortcode( 'naehrwerte_felder',    [ $this, 'render' ] );
		add_shortcode( 'wine_nutrition_fields', [ $this, 'render' ] );
	}

	public function enqueue_styles(): void {
		if ( ! is_singular( WINE_NUTRITION_CPT ) ) {
			return;
		}
		wp_enqueue_style(
			'wine-nutrition-style',
			WINE_NUTRITION_URL . 'assets/css/style.css',
			[],
			WINE_NUTRITION_VERSION
		);
	}

	public function render( array $_atts = [] ): string {
		$post = get_queried_object();

		if ( ! ( $post instanceof WP_Post ) || get_post_type( $post->ID ) !== WINE_NUTRITION_CPT ) {
			return '';
		}

		// Retrieve field values; fall back to sensible defaults when empty.
		$alkohol_prozent = floatval( get_field( 'alkoholgehalt', $post->ID ) );
		$restzucker      = floatval( get_field( 'restzucker', $post->ID ) );
		$gesamtsaeure    = floatval( get_field( 'gesamtsaeure', $post->ID ) );
		$glyzerin        = floatval( get_field( 'glyzerin', $post->ID ) ?: 8 );
		$zutaten         = get_field( 'zutaten', $post->ID )
			?: 'Trauben, Saccharose, Kohlendioxid, Konservierungsstoff: Sulfite, Säureregulatoren: Weinsäure, Milchsäure, Äpfelsäure, Stabilisatoren: CMC+';
		$produktbild     = get_field( 'produktbild', $post->ID );
		$beschreibung    = get_field( 'beschreibung', $post->ID );

		// Convert all values to g/100 ml for the energy calculation.
		// Alcohol: %vol × 0.789 g/ml (ethanol density) = g per 100 ml.
		$alkohol_g      = $alkohol_prozent * 0.789;
		$restzucker_g   = $restzucker   / 10; // g/l → g/100 ml
		$gesamtsaeure_g = $gesamtsaeure / 10;
		$glyzerin_g     = $glyzerin     / 10;

		// Energy factors (kJ / kcal per gram): alcohol 29/6.96, sugars 17/4.08,
		// organic acids 13/3.12, glycerol 10/2.4.
		$brennwert_kj   = $alkohol_g * 29   + $restzucker_g * 17   + $gesamtsaeure_g * 13   + $glyzerin_g * 10;
		$brennwert_kcal = $alkohol_g * 6.96 + $restzucker_g * 4.08 + $gesamtsaeure_g * 3.12 + $glyzerin_g * 2.4;

		// Carbohydrates = residual sugar + glycerol (both per 100 ml).
		$kohlenhydrate = $restzucker_g + $glyzerin_g;

		ob_start();
		?>
		<div class="wine-nutrition-container">

			<div class="wine-nutrition-wrap">
				<h1 class="h-l"><?php echo esc_html( get_the_title( $post->ID ) ); ?></h1>

				<?php if ( $produktbild ) : ?>
				<div class="wine-nutrition-image">
					<img src="<?php echo esc_url( $produktbild['url'] ); ?>" alt="<?php echo esc_attr( $produktbild['alt'] ); ?>">
				</div>
				<?php endif; ?>

				<?php if ( $beschreibung ) : ?>
				<div class="wine-nutrition-description">
					<h2 class="h-xs">Beschreibung</h2>
					<p><?php echo esc_html( $beschreibung ); ?></p>
				</div>
				<?php endif; ?>
			</div>

			<div class="wine-nutrition-wrap">
				<table class="wine-nutrition-table">
					<thead>
						<tr>
							<th></th>
							<th>pro 100 ml</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Brennwert</td>
							<td><?php echo esc_html( round( $brennwert_kj, 1 ) ); ?> kJ / <?php echo esc_html( round( $brennwert_kcal, 1 ) ); ?> kcal</td>
						</tr>
						<tr>
							<td>Kohlenhydrate</td>
							<td><?php echo esc_html( round( $kohlenhydrate, 1 ) ); ?> g</td>
						</tr>
						<tr>
							<td>- davon Zucker</td>
							<td><?php echo esc_html( round( $restzucker_g, 1 ) ); ?> g</td>
						</tr>
					</tbody>
				</table>

				<table class="wine-nutrition-extras-table">
					<thead>
						<tr>
							<th>Zusatzangaben</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Enthält geringe Mengen von: Fett, gesättigte Fettsäuren, Eiweiß, Salz</td>
						</tr>
						<tr>
							<td>Zutaten: <?php echo esc_html( $zutaten ); ?></td>
						</tr>
					</tbody>
				</table>

				<div class="wine-nutrition-details">
					<h2 class="h-xs">Weitere Angaben</h2>
					<p>Alkoholgehalt: <?php echo esc_html( round( $alkohol_prozent, 1 ) ); ?>%</p>
					<p>Restzucker: <?php echo esc_html( round( $restzucker, 1 ) ); ?> g/l</p>
					<p>Gesamtsäure: <?php echo esc_html( round( $gesamtsaeure, 1 ) ); ?> g/l</p>
				</div>
			</div>

		</div>
		<?php
		return (string) ob_get_clean();
	}
}
