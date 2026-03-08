<?php
/**
 * Rental listings template.
 *
 * @var array<string,mixed> $data
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$query           = isset( $data['query'] ) ? $data['query'] : null;
$show_pagination = ! empty( $data['show_pagination'] );

if ( ! ( $query instanceof WP_Query ) ) {
	return;
}
?>
<div class="rental-plugin-wrapper rm-shortcode rm-rental-listings tw-font-sans tw-text-slate-800">
	<?php do_action( 'rm_before_rental_listings_loop', $data ); ?>

	<?php if ( $query->have_posts() ) : ?>
		<div class="tw-grid tw-gap-5 tw-grid-cols-1 sm:tw-grid-cols-2 lg:tw-grid-cols-3">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<article class="tw-bg-white tw-border tw-border-slate-200 tw-rounded-2xl tw-overflow-hidden tw-shadow-sm hover:tw-shadow-md tw-transition-shadow" id="listing-<?php the_ID(); ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="tw-aspect-[16/10] tw-overflow-hidden rm-listing-thumb"><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large', array( 'class' => 'tw-w-full tw-h-full tw-object-cover hover:tw-scale-105 tw-transition-transform', 'loading' => 'lazy', 'decoding' => 'async', 'fetchpriority' => 'low' ) ); ?></a></div>
					<?php endif; ?>
					<div class="tw-p-4">
						<h3 class="tw-text-lg tw-font-semibold tw-leading-snug rm-listing-title"><a class="tw-no-underline hover:tw-underline" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<div class="tw-mt-2 rm-listing-meta">
							<?php
							$price = get_post_meta( get_the_ID(), 'price', true );
							if ( '' === (string) $price ) {
								$price = get_post_meta( get_the_ID(), 'rm_apartment_rent_month', true );
							}
							?>
							<?php if ( '' !== (string) $price ) : ?>
								<span class="tw-inline-flex tw-items-center tw-rounded-full tw-bg-emerald-50 tw-text-emerald-700 tw-text-sm tw-font-medium tw-px-3 tw-py-1 rm-listing-price"><?php echo esc_html( '$' . $price ); ?></span>
							<?php endif; ?>
						</div>
						<div class="tw-mt-3 tw-text-sm tw-text-slate-600 tw-line-clamp-3 rm-listing-excerpt"><?php the_excerpt(); ?></div>
						<div class="tw-mt-4">
							<a href="<?php the_permalink(); ?>" class="tw-inline-flex tw-items-center tw-justify-center tw-bg-slate-900 tw-text-white tw-text-sm tw-font-medium tw-rounded-lg tw-px-4 tw-py-2 hover:tw-bg-slate-700 tw-no-underline"><?php esc_html_e( 'View Details', 'ar' ); ?></a>
						</div>
					</div>
				</article>
			<?php endwhile; ?>
		</div>

		<?php if ( $show_pagination && $query->max_num_pages > 1 ) : ?>
			<?php
			$links = paginate_links(
				array(
					'total'   => $query->max_num_pages,
					'current' => max( 1, (int) get_query_var( 'paged', 1 ) ),
					'type'    => 'list',
				)
			);
			?>
			<?php if ( $links ) : ?>
				<nav class="rm-pagination tw-mt-8" aria-label="<?php esc_attr_e( 'Listings Pagination', 'ar' ); ?>">
					<?php echo wp_kses_post( $links ); ?>
				</nav>
			<?php endif; ?>
		<?php endif; ?>
	<?php else : ?>
		<p class="rm-listing-empty tw-bg-slate-50 tw-border tw-border-dashed tw-border-slate-300 tw-rounded-xl tw-p-8 tw-text-center"><?php esc_html_e( 'No rental listings found.', 'ar' ); ?></p>
	<?php endif; ?>

	<?php do_action( 'rm_after_rental_listings_loop', $data ); ?>
</div>
