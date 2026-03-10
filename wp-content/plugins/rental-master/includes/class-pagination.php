<?php
/**
 * Pagination helper.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Pagination {
	/**
	 * Render numeric pagination markup.
	 *
	 * @param int $max_num_pages Total pages.
	 *
	 * @return string
	 */
	public static function render( $max_num_pages = 0 ) {
		$max_num_pages = absint( $max_num_pages );
		if ( $max_num_pages <= 1 ) {
			return '';
		}

		$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
		$links = array();

		if ( $paged >= 1 ) {
			$links[] = $paged;
		}
		if ( $paged >= 3 ) {
			$links[] = $paged - 1;
			$links[] = $paged - 2;
		}
		if ( ( $paged + 2 ) <= $max_num_pages ) {
			$links[] = $paged + 2;
			$links[] = $paged + 1;
		}

		ob_start();
		echo '<div class="navigation"><ul class="pager">' . "\n";
		if ( get_previous_posts_link() ) {
			printf( '<li class="page">%s</li>' . "\n", get_previous_posts_link() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		if ( ! in_array( 1, $links, true ) ) {
			$class = 1 === $paged ? ' class="active"' : '';
			printf( '<li%s class="page"><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );
			if ( ! in_array( 2, $links, true ) ) {
				echo '<li>…</li>';
			}
		}

		sort( $links );
		foreach ( (array) $links as $link ) {
			$class = $paged === $link ? ' class="active"' : '';
			printf( '<li%s class="page"><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), (int) $link );
		}

		if ( ! in_array( $max_num_pages, $links, true ) ) {
			if ( ! in_array( $max_num_pages - 1, $links, true ) ) {
				echo '<li class="page">…</li>' . "\n";
			}
			$class = $paged === $max_num_pages ? ' class="active"' : '';
			printf( '<li%s class="page"><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max_num_pages ) ), $max_num_pages );
		}

		if ( get_next_posts_link() ) {
			printf( '<li class="page">%s</li>' . "\n", get_next_posts_link() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</ul></div>' . "\n";

		$content = ob_get_clean();
		return (string) $content;
	}
}
