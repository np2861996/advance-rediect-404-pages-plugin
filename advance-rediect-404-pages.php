<?php
/**
 * Plugin Name: Advance Redirect 404 Pages 
 * Plugin URI: https://github.com/np2861996/Clone-Page-Or-Post-Plugin
 * Description: Quick, easy, advance plugin for redirect 404 pages. 
 * Author: BeyondN
 * Text Domain: advance-redirect-pages
 * Version: 1.0.0
 *
 * @package Advance_Redirect_Pages
 * @author BeyondN
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if (!defined("ARP_PLUGIN_DIR_PATH")){
	define("ARP_PLUGIN_DIR_PATH", plugin_dir_path(__FILE__));
}

require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );

define( 'ARP_VERSION', '1.0.0' );

add_action('admin_menu', 'arp_admin_menu_404_redirect');

add_action('wp', 'arp_404_redirect');

add_action( 'admin_enqueue_scripts', 'arp_enqueue_styles_scripts_404r' );

function arp_admin_menu_404_redirect() {
	add_options_page('Advance Redirect 404 Pages', 'Advance Redirect 404 Pages', 'manage_options', 'advance-redirect-404-pages', 'arp_options_menu_404r'  );
}

function arp_options_menu_404r() {
	
	if (!current_user_can('manage_options')){

		wp_die( __('You do not have sufficient permissions to access this page.') );
	}

	include( plugin_dir_path( __FILE__ ) . 'options.php' );

}

function arp_404_redirect(){

	if(is_404()) {
	 	
        $redirect_to	= arp_get_redirect_to_404r();
        $status			= arp_get_status_404r();
	    $link			= arp_current_link_404r();

	    if($link == $redirect_to){

	        echo "<b>All 404 Redirect to Homepage</b> has detected that the target URL is invalid, this will cause an infinite loop redirection, please go to the plugin settings and correct the traget link! ";
	        exit(); 
	    }

	 	if($status=='1' & $redirect_to!=''){

			global $wpdb;
			global $wp;
			$table_name = $wpdb->prefix."arp_links_lists";
			
			$link_date 	= date("Y-m-d H:i:s");
			$ip_address	= sanitize_text_field($_SERVER['REMOTE_ADDR']);
			$curr_url = home_url( $wp->request );
			
			$rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE url = '$curr_url' and ip_address = '$ip_address' ");
			
			if($rowcount == 0){
				if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {
	
					$charset_collate = $wpdb->get_charset_collate();
					$sql = "CREATE TABLE IF NOT EXISTS $table_name (
						id mediumint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
						ip_address varchar(90) DEFAULT '' NOT NULL,
						time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
						url varchar(300) DEFAULT '' NOT NULL
						) $charset_collate;";
					
					require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
					dbDelta( $sql );
				}
				$res = $wpdb->insert($table_name, array('url' => $curr_url, 'time' => $link_date, 'ip_address' => $ip_address) );				
			}else{
				$res =	$wpdb->update($table_name, array('time'=>$link_date), array('url'=>$curr_url));
			}

		 	header ('HTTP/1.1 301 Moved Permanently');
			header ("Location: " . $redirect_to);
			exit(); 

		}
	}
}

function arp_enqueue_styles_scripts_404r(){

    if( is_admin() ) {              

        $css= plugins_url() . '/'.  basename(dirname(__FILE__)) . "/style.css";               

        wp_enqueue_style( 'main-404-css', $css, '',ARP_VERSION);

    }

}

function arp_plugin_add_settings_link( $links ) { 
	$support_link = '<a href="#"  target="_blank" >' . __( 'Support' ) . '</a>'; 
	array_unshift( $links, $support_link );

	$settings_link = '<a href="options-general.php?page=advance-redirect-404-pages">' . __( 'Settings' ) . '</a>';
	array_unshift( $links, $settings_link );
	
	global $wpdb;
	$table_name = $wpdb->prefix . 'arp_links_lists';
	
	if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			ip_address varchar(90) DEFAULT '' NOT NULL,
			time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			url varchar(300) DEFAULT '' NOT NULL
			) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'arp_plugin_add_settings_link');

add_action( 'upgrader_process_complete', 'arp_upgrade_function',10, 2);
 
function arp_upgrade_function( $upgrader_object, $options ) {
	global $wpdb;
	$table_name = $wpdb->prefix . 'arp_links_lists';
	
	if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name) {

		$charset_collate = $wpdb->get_charset_collate();
		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
			id mediumint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
			ip_address varchar(90) DEFAULT '' NOT NULL,
			time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
			url varchar(300) DEFAULT '' NOT NULL
			) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}

register_activation_hook( __FILE__ , 'arp_plugin_active_404r' );

function arp_plugin_active_404r(){

	$redirect_to	= arp_get_redirect_to_404r();
	$status			= arp_get_status_404r();

	if(empty($redirect_to)){
		update_option('arp_redirect_to_404r',home_url());
	}

	if(empty($status)){ 
		update_option('arp_status_404r',0);
	}

}

