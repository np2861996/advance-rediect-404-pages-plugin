<?php

function arp_current_link_404r()
{
	$prt = sanitize_text_field($_SERVER['SERVER_PORT']);
	$sname = sanitize_text_field($_SERVER['SERVER_NAME']);
	$sREQUEST_URI = sanitize_text_field($_SERVER['SERVER_NAME']);
	
	if (array_key_exists('HTTPS',$_SERVER) && $_SERVER['HTTPS'] != 'off' && $_SERVER['HTTPS'] != '')
	$sname = "https://" . $sname; 
	else
	$sname = "http://" . $sname; 
	
	if($prt !=80)
	{
	$sname = $sname . ":" . $prt;
	} 
	
	$path = $sname . $sREQUEST_URI;
	
	return $path ;

}
function arp_get_redirect_to_404r()
{
	return $redirect_to=get_option('arp_redirect_to_404r');
}
function arp_get_status_404r()
{
	return $status= get_option('arp_status_404r');
}

// Error message
function arp_failure_option_msg_404r($msg)
{	
	echo  '<div class="notice notice-error arp-error-msg is-dismissible"><p>' . esc_html($msg) . '</p></div>';	
}

// Success message
function  arp_success_option_msg_404r($msg)
{
	
	echo ' <div class="notice notice-success arp-success-msg is-dismissible"><p>'. esc_html($msg) . '</p></div>';		
	
}