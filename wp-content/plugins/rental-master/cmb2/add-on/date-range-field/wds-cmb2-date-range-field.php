<?php
class WDS_CMB2_Date_Range_Field {
	const VERSION = '0.1.1';
	protected $url      = '';
	protected $path     = '';
	protected $basename = '';
	public static function get_instance() {
		static $instance = null;
		if ( null === $instance ) {
			$instance = new self();
		}
		$instance->hooks();
		return $instance;
	}
	
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );
	}
	public function hooks() {
		register_activation_hook( __FILE__, array( $this, '_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'cmb2_render_date_range', array( $this, 'render' ), 10, 5 );
		add_filter( 'cmb2_sanitize_date_range', array( $this, 'sanitize' ), 10, 2 );
	}
	function _activate() {}
	function _deactivate() {}
	public function init() {
		//$class = __CLASS__;
        //new $class;
		load_plugin_textdomain( 'wds-cmb2-date-range-field', false, dirname($this->basename ) . '/languages/' );
	}
	function render( $field, $escaped_value, $field_object_id, $field_object_type, $field_type ) {
		wp_enqueue_style('jquery-ui-daterangepicker',$this->url.'/assets/jquery-ui-daterangepicker/jquery.comiseo.daterangepicker.css',array(),'0.4.0');
		wp_register_script('moment', $this->url . '/assets/moment.min.js', array(), '2.10.3' );
		wp_register_script('jquery-ui-daterangepicker',$this->url.'/assets/jquery-ui-daterangepicker/jquery.comiseo.daterangepicker.js',array( 'jquery', 'jquery-ui-core', 'jquery-ui-button', 'jquery-ui-menu', 'jquery-ui-datepicker', 'moment' ), '0.4.0' );
		wp_enqueue_script( 'cmb2-daterange-picker', $this->url . '/assets/cmb2-daterange-picker.js', array( 'jquery-ui-daterangepicker' ), self::VERSION, true );
		// CMB2_Types::parse_args allows arbitrary attributes to be added
		$a = $field_type->parse_args( array(), 'input', array(
			'type'  => 'text',
			'class' => 'date-range button-secondary',
			'name'  => $field_type->_name(),
			'id'    => $field_type->_id(),
			'desc'  => $field_type->_desc( true ),
			'data-daterange' => json_encode( array(
				'id' => '#' . $field_type->_id(),
				'buttontext' => esc_attr( $field_type->_text( 'button_text', __( 'Select date range...' ) ) ),
				'format' => $field->args( 'date_format' ) ? $field->args( 'date_format' ) : 'mm/dd/yy',
			) ),
		) );

		printf( '<div class="cmb2-element"><input%s value=\'%s\'/></div>%s', $field_type->concat_attrs( $a, array( 'desc' ) ), json_encode( $escaped_value ), $a['desc'] );
	}
	function sanitize( $override_value, $value ){
		$value = json_decode( $value, true );
		if ( is_array( $value ) ) {
			$value = array_map( 'sanitize_text_field', $value );
		} else {
			sanitize_text_field( $value );
		}
		return $value;
	}
}
function wds_cmb2_date_range_field() {
	return WDS_CMB2_Date_Range_Field::get_instance();
}
add_action('plugins_loaded','wds_cmb2_date_range_field');