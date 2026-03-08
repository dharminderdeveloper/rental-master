<?php
/**
 * Frontend layer.
 *
 * @package RentalMaster
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RM_Frontend {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'template_include', array( $this, 'template_include' ), 99 );
	}

	/**
	 * Template fallback passthrough for Phase 2.
	 *
	 * @param string $template Template path.
	 *
	 * @return string
	 */
	public function template_include( $template ) {
		return $template;
	}
}
