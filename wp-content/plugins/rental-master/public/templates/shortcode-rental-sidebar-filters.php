<?php
/**
 * Rental sidebar filters template.
 *
 * @var array<string,mixed> $data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$atts     = isset( $data['atts'] ) ? (array) $data['atts'] : array();
$selected = isset( $data['selected'] ) ? (array) $data['selected'] : array();
$terms    = isset( $data['terms'] ) ? (array) $data['terms'] : array();
?>
<aside class="rental-plugin-wrapper rm-shortcode rm-rental-sidebar-filters tw-font-sans tw-text-slate-800">
	<div class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-p-4 tw-shadow-sm">
		<h3 class="tw-text-lg tw-font-semibold tw-mb-3"><?php echo esc_html( $atts['title'] ?? __( 'Filter Listings', 'ar' ) ); ?></h3>
		<form method="get" action="<?php echo esc_url( get_permalink() ); ?>" class="tw-space-y-3">
			<?php do_action( 'rm_before_rental_sidebar_filters', $data ); ?>

			<p class="tw-m-0">
				<label class="tw-text-sm tw-font-medium" for="rm-filter-location"><?php esc_html_e( 'Location', 'ar' ); ?></label>
				<select class="tw-mt-1 tw-w-full tw-rounded-lg tw-border tw-border-slate-300 tw-px-3 tw-py-2" id="rm-filter-location" name="location">
					<option value=""><?php esc_html_e( 'All', 'ar' ); ?></option>
					<?php foreach ( (array) ( $terms['location'] ?? array() ) as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected['location'] ?? '', $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="tw-m-0">
				<label class="tw-text-sm tw-font-medium" for="rm-filter-property-type"><?php esc_html_e( 'Property Type', 'ar' ); ?></label>
				<select class="tw-mt-1 tw-w-full tw-rounded-lg tw-border tw-border-slate-300 tw-px-3 tw-py-2" id="rm-filter-property-type" name="property_type">
					<option value=""><?php esc_html_e( 'All', 'ar' ); ?></option>
					<?php foreach ( (array) ( $terms['property_type'] ?? array() ) as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected['property_type'] ?? '', $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="tw-m-0">
				<label class="tw-text-sm tw-font-medium" for="rm-filter-rental-category"><?php esc_html_e( 'Rental Category', 'ar' ); ?></label>
				<select class="tw-mt-1 tw-w-full tw-rounded-lg tw-border tw-border-slate-300 tw-px-3 tw-py-2" id="rm-filter-rental-category" name="rental_category">
					<option value=""><?php esc_html_e( 'All', 'ar' ); ?></option>
					<?php foreach ( (array) ( $terms['rental_category'] ?? array() ) as $term ) : ?>
						<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected['rental_category'] ?? '', $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="tw-m-0">
				<button type="submit" class="tw-w-full tw-bg-slate-900 tw-text-white tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 hover:tw-bg-slate-700"><?php esc_html_e( 'Apply Filters', 'ar' ); ?></button>
			</p>

			<?php do_action( 'rm_after_rental_sidebar_filters', $data ); ?>
		</form>
	</div>
</aside>
