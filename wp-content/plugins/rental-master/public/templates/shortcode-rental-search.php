<?php
/**
 * Rental search template.
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
<div class="rental-plugin-wrapper rm-shortcode rm-rental-search tw-font-sans tw-text-slate-800">
	<form method="get" action="<?php echo esc_url( get_permalink() ); ?>" class="rm-search-form tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-p-4 tw-shadow-sm" data-rm-fast-search="1">
		<?php do_action( 'rm_before_rental_search_fields', $data ); ?>

		<label class="tw-flex tw-flex-col tw-gap-1">
			<span class="tw-text-sm tw-font-medium"><?php esc_html_e( 'Rental name', 'ar' ); ?></span>
			<input class="tw-rounded-lg tw-border tw-border-slate-300 tw-px-3 tw-py-2" type="text" name="name" value="<?php echo isset( $_GET['name'] ) ? esc_attr( sanitize_text_field( wp_unslash( $_GET['name'] ) ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'Search by rental name', 'ar' ); ?>" />
		</label>

		<label class="tw-flex tw-flex-col tw-gap-1">
			<span class="tw-text-sm tw-font-medium"><?php esc_html_e( 'Location', 'ar' ); ?></span>
			<select class="tw-rounded-lg tw-border tw-border-slate-300 tw-px-3 tw-py-2" name="location">
				<option value=""><?php esc_html_e( 'Any location', 'ar' ); ?></option>
				<?php foreach ( (array) ( $terms['location'] ?? array() ) as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected['location'] ?? '', $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label class="tw-flex tw-flex-col tw-gap-1">
			<span class="tw-text-sm tw-font-medium"><?php esc_html_e( 'Property type', 'ar' ); ?></span>
			<select class="tw-rounded-lg tw-border tw-border-slate-300 tw-px-3 tw-py-2" name="property_type">
				<option value=""><?php esc_html_e( 'Any type', 'ar' ); ?></option>
				<?php foreach ( (array) ( $terms['property_type'] ?? array() ) as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected['property_type'] ?? '', $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label class="tw-flex tw-flex-col tw-gap-1">
			<span class="tw-text-sm tw-font-medium"><?php esc_html_e( 'Category', 'ar' ); ?></span>
			<select class="tw-rounded-lg tw-border tw-border-slate-300 tw-px-3 tw-py-2" name="rental_category">
				<option value=""><?php esc_html_e( 'Any category', 'ar' ); ?></option>
				<?php foreach ( (array) ( $terms['rental_category'] ?? array() ) as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected['rental_category'] ?? '', $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<?php if ( 'yes' === strtolower( (string) ( $atts['show_button'] ?? 'yes' ) ) ) : ?>
			<div class="tw-flex tw-items-end">
				<button type="submit" class="tw-w-full tw-bg-slate-900 tw-text-white tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 hover:tw-bg-slate-700"><?php echo esc_html( $atts['button_text'] ?? __( 'Search', 'ar' ) ); ?></button>
			</div>
		<?php endif; ?>

		<?php do_action( 'rm_after_rental_search_fields', $data ); ?>
	</form>
	<div class="rm-fast-search-status tw-mt-3 tw-text-sm tw-text-slate-500" aria-live="polite"></div>
	<div class="rm-fast-search-results tw-mt-2"></div>
</div>
