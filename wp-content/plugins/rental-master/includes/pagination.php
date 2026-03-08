<?php

/** 
*	Function:	abr_cf_num_pagination
*
*	@param:		$max_num_pages
*	@return:	$content
*/
	function abr_cf_num_pagination($max_num_pages = 0){
		ob_start();
		if( $max_num_pages <= 1 )
			return;
			$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
		if ( $paged >= 1 )
			$links[] = $paged;
		if ( $paged >= 3 ) {
			$links[] = $paged - 1;
			$links[] = $paged - 2;
		}
		if ( ( $paged + 2 ) <= $max_num_pages ) {
			$links[] = $paged + 2;
			$links[] = $paged + 1;
		}
		echo '<div class="navigation"><ul class="pager">' . "\n";
		if ( get_previous_posts_link() )
			printf( '<li class="page">%s</li>' . "\n", get_previous_posts_link() );
		/** Link to first page, plus ellipses if necessary */
		if ( ! in_array( 1, $links ) ) {
			$class = 1 == $paged ? ' class="active"' : '';
			printf( '<li%s class="page"><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );
			if ( ! in_array( 2, $links ) )
				echo '<li>…</li>';
		}
		/** Link to current page, plus 2 pages in either direction if necessary */
		sort( $links );
		foreach ( (array) $links as $link ) {
			$class = $paged == $link ? ' class="active"' : '';
			printf( '<li%s class="page"><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
		}
		/** Link to last page, plus ellipses if necessary */
		if ( ! in_array( $max_num_pages, $links ) ) {
			if ( ! in_array( $max_num_pages - 1, $links ) )
				echo '<li class="page">…</li>' . "\n";
			$class = $paged == $max_num_pages ? ' class="active"' : '';
			printf( '<li%s class="page"><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max_num_pages ) ), $max_num_pages );
		}
		/** Next Post Link */
		if ( get_next_posts_link() )
			printf( '<li%s class="page"></li>' . "\n", get_next_posts_link() );
		echo '</ul></div>' . "\n";
		$content=ob_get_contents();
		ob_end_clean();
		return $content;
	}