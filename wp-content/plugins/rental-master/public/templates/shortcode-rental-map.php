<?php
/**
 * Rental map template.
 *
 * @var array<string,mixed> $data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$atts    = isset( $data['atts'] ) ? (array) $data['atts'] : array();
$markers = isset( $data['markers'] ) ? (array) $data['markers'] : array();
$map_id  = 'rm-map-' . wp_rand( 1000, 999999 );
$height  = isset( $atts['height'] ) ? sanitize_text_field( (string) $atts['height'] ) : '420px';
$zoom    = isset( $atts['zoom'] ) ? absint( $atts['zoom'] ) : 10;
$active_placeholder = __( 'Click a marker to preview listing details.', 'ar' );
?>
<div class="rental-plugin-wrapper rm-shortcode rm-rental-map tw-font-sans tw-text-slate-800 tw-my-4">
	<div class="rm-map-layout tw-grid tw-grid-cols-1 lg:tw-grid-cols-3 tw-gap-4">
		<div class="lg:tw-col-span-2">
			<div id="<?php echo esc_attr( $map_id ); ?>" class="rm-map-canvas tw-rounded-2xl tw-overflow-hidden tw-border tw-border-slate-200" style="height: <?php echo esc_attr( $height ); ?>;"></div>
		</div>
		<div class="rm-map-panel">
			<div class="rm-map-panel-card tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-p-4 tw-shadow-sm" id="<?php echo esc_attr( $map_id ); ?>-card">
				<p class="tw-text-sm tw-text-slate-500"><?php echo esc_html( $active_placeholder ); ?></p>
			</div>
		</div>
	</div>
	<script>
	(function(){
		var mapRoot = document.getElementById('<?php echo esc_js( $map_id ); ?>');
		var cardRoot = document.getElementById('<?php echo esc_js( $map_id ); ?>-card');
		if (!mapRoot) { return; }
		var markers = <?php echo wp_json_encode( $markers ); ?>;
		var zoom = <?php echo (int) $zoom; ?>;
		if (!window.google || !window.google.maps) {
			mapRoot.innerHTML = '<p class="tw-p-4"><?php echo esc_js( __( 'Google Maps is not available. Please configure your API key.', 'ar' ) ); ?></p>';
			return;
		}

		var center = {lat: 0, lng: 0};
		if (markers.length > 0) {
			center.lat = markers[0].lat;
			center.lng = markers[0].lng;
		}

		var map = new google.maps.Map(mapRoot, {
			zoom: zoom,
			center: center
		});

		var infoWindow = new google.maps.InfoWindow();
		var bounds = new google.maps.LatLngBounds();

		markers.forEach(function(markerItem){
			var position = {lat: markerItem.lat, lng: markerItem.lng};
			var marker = new google.maps.Marker({
				position: position,
				map: map,
				title: markerItem.title
			});
			bounds.extend(position);
			marker.addListener('click', function(){
				var cardHtml = '';
				if (markerItem.image) {
					cardHtml += '<div class="rm-map-card-image tw-mb-3"><img class="tw-w-full tw-rounded-xl" src="' + markerItem.image + '" alt="' + markerItem.title + '"></div>';
				}
				cardHtml += '<h4 class="tw-text-lg tw-font-semibold tw-leading-tight"><a class="tw-no-underline hover:tw-underline" href="' + markerItem.url + '">' + markerItem.title + '</a></h4>';
				if (markerItem.price) {
					cardHtml += '<p class="tw-mt-2 tw-text-sm"><strong><?php echo esc_js( __( 'Price:', 'ar' ) ); ?></strong> $' + markerItem.price + '</p>';
				}
				if (markerItem.address) {
					cardHtml += '<p class="tw-mt-1 tw-text-sm"><strong><?php echo esc_js( __( 'Address:', 'ar' ) ); ?></strong> ' + markerItem.address + '</p>';
				}
				cardHtml += '<p class="tw-mt-4"><a class="tw-inline-flex tw-items-center tw-justify-center tw-bg-slate-900 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 hover:tw-bg-slate-700 tw-no-underline" href="' + markerItem.url + '"><?php echo esc_js( __( 'View Listing', 'ar' ) ); ?></a></p>';

				if (cardRoot) {
					cardRoot.innerHTML = cardHtml;
				}

				infoWindow.setContent('<div class="rm-map-infowindow"><strong>' + markerItem.title + '</strong></div>');
				infoWindow.open(map, marker);
			});
		});

		if (markers.length > 1) {
			map.fitBounds(bounds);
		}
	})();
	</script>
</div>
