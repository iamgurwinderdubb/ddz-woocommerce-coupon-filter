<?php
/**
 * Plugin Name: WC Coupons Addition
 * Description: A Free Extension That Transforms Your WooCommerce coupon with filters. use [ddz_woo_coupons] shortcode to display all coupons
 * Author: Gurwinder Singh
 * Version: 0.1
 * Author URI: https://www.linkedin.com/in/iamgurwinderdubb/
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
ob_start();

$plugin_dir = dirname( __FILE__ );
$plugin_url = plugin_dir_url( __FILE__ );

define( 'DDZ_INC_DIR', $plugin_dir . '/inc/' );
define( 'DDZ_JS_URL', $plugin_url . 'assets/js/' );
define( 'DDZ_IMAGES_URL', $plugin_url . 'assets/images/' );

class ddz_woo_coupon
{
	
	function __construct()
	{
		
		add_action( 'admin_enqueue_scripts', array( $this, 'ddz_woo_coupon_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ddz_woo_coupon_assets' ) );
		add_action('admin_menu', array( $this, 'ddz_woo_coupon_menu' ));
		$this->ddz_woo_coupon_includes();
	}

	public function ddz_woo_coupon_assets() {
		
		
		wp_enqueue_media('media-upload');
        wp_enqueue_media('thickbox');
		wp_enqueue_style( 'ddz-woo-coupon', plugins_url( '/assets/css/style.css', __FILE__ ), array(), '0.1', false );
		wp_enqueue_script( 'ddz-woo-isotope', plugins_url( '/assets/js/isotope.js', __FILE__ ) , array(), '3.0.5', true );
		wp_enqueue_script( 'ddz-woo-isotope' );
		wp_enqueue_script( 'ddz-woo-coupon', plugins_url( '/assets/js/script.js', __FILE__ ) , array(), '0.1', true );
		
		
		
	}

	public function ddz_woo_coupon_menu() {
		
		add_submenu_page( 'woocommerce', 'Coupons Settings', 'Coupons Settings' , 'manage_options', 'ddz-options', array($this,'ddz_display') );
	}
	public function ddz_woo_coupon_includes()
	{
		
		require( DDZ_INC_DIR. 'ddz-woo-coupon-admin.php');
		
	}
	public function ddz_display()
	{
		require( DDZ_INC_DIR. 'ddz-woo-coupon-settings.php');
	}

}

new ddz_woo_coupon;