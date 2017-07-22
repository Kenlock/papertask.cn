<?php
/*
Plugin Name: Disable Google Maps
Plugin URI: http://www.brunoxu.com/disable-google-maps.html
Description: Stop loading google maps for not necessary or local development.
Author: Bruno Xu
Author URI: http://www.brunoxu.com/
Version: 1.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

add_action("wp_loaded", 'disable_google_maps_ob_start');

function disable_google_maps_ob_start(){
	ob_start('disable_google_maps_ob_end');
}

function disable_google_maps_ob_end($html){
	$html = preg_replace('/<script[^<>]*\/\/maps.(googleapis|google|gstatic).com\/[^<>]*><\/script>/i', '', $html);
	return $html;
}
