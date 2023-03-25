<?php
/*
Plugin Name: Basta Smoelenboek
Plugin URI: https://github.com/usbpaul/basta-smoelenboek
Description: Toont een smoelenboek voor leden van Basta!
Version: 1.0.0
Author: Paul Bakker
Author URI: https://github.com/usbpaul
License: GPL2

Copyright 2014  Paul Bakker

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$db_prefix = 'wp_';
//$db_prefix = 'yl3k_';

function vgbsm_get_userids_query( $selectfield, $selectvalues ) {
	$groups       = explode( ',', $selectvalues );
	$group_string = implode( "','", $groups );
	$group_string = "'" . $group_string . "'";
	global $db_prefix;
	$query = "SELECT user_id FROM `${db_prefix}usermeta` " .
	         "where meta_key = '" . $selectfield . "' AND meta_value in (" . $group_string . ")";

	return $query;
}

function vgbsm_flatten($userData) {
	$returnArray = [];
	if ( $userData ) {
		foreach ( $userData as $userStdObj ) {
			foreach ( $userStdObj as $key => $userID ) {
				$user_data              = get_user_meta( $userID );
				$userArray = array(
					'id'         => $userID,
					'bio'        => $user_data['description'][0],
					'first_name' => $user_data['first_name'][0],
					'last_name'  => $user_data['last_name'][0],
					'avatar_url' => get_avatar_url( $userID )
				);
				$returnArray[ $userID ] = $userArray;
			}
		}
	}
	return $returnArray;
}

function vgbsm_smoelenboek_grid( $attrs ) {
	extract( shortcode_atts(
		array(
			'selectfield'  => 'Unkown_field',
			'selectvalues' => 'Unknown Group',
			'display'      => 'table',
			'fields'       => ''
		), $attrs
	) );
	global $wpdb;
	$query = vgbsm_get_userids_query( $selectfield, $selectvalues );
	$userIDs = $wpdb->get_results( $query );

	$output = <<<GRIDSTART
<div class="smoelenboek">
GRIDSTART;

	$userIDs = vgbsm_flatten($userIDs);
	usort($userIDs, "compare_first_name");
	foreach ($userIDs as $userID => $user_data) {
		$avatar_url = $user_data['avatar_url'];
		$first_name = $user_data['first_name'];
		$last_name = $user_data['last_name'];
		$user_id = $user_data['id'];
		$output .= "<div class='user'>";
		if ($user_data["bio"]) {
			$output .= "<div class='avatar'><a href='/koorlid?uid=$user_id'><img alt='avatar' src='$avatar_url'/></a></div>";
		} else {
			$output .= "<div class='avatar'><img alt='avatar' src='$avatar_url'/></div>";
		}
		$output .= "<div class='name'>$first_name $last_name</div>";
		$output .= "</div>";
	}

	$output .= <<<GRIDEND
</div>
GRIDEND;

	return $output;
}

function compare_first_name($a, $b) {
	return strcmp($a["first_name"], $b["first_name"]);
}

add_shortcode( 'smoelenboek-grid', 'vgbsm_smoelenboek_grid' );

function vgbsm_smoelenboek_detail( $attrs ) {
	$uid = $_GET['uid'];
	$user_data = get_user_meta($uid);
	$bio = $user_data['description'][0];
	$first_name = $user_data['first_name'][0];
	$last_name = $user_data['last_name'][0];
	$avatar_url = get_avatar_url($uid);
	$output = <<<USERDETAIL
<div class="userdetail">
	<div class="username">$first_name $last_name</div>
	<div class='avatar'><img alt='avatar' src='$avatar_url'/></div>
	<div class='bio'>$bio</div>
	<div class="backlink"><a href="/smoelenboek">Terug naar het smoelenboek.</a></div>
</div>
USERDETAIL;
	return $output;
}

add_shortcode( 'smoelenboek-detail', 'vgbsm_smoelenboek_detail' );

add_action( 'init', 'vgbsm_register_script' );
function vgbsm_register_script() {
//	wp_register_script( 'vgbsm_script', plugins_url('/js/vgbsm-script.js', __FILE__), array('jquery'), '2.5.1' );
	wp_register_style( 'vgbsm_style', plugins_url( '/css/vgbsm-style.css', __FILE__ ), false, '1.0.0', 'all' );
}
add_action( 'wp_enqueue_scripts', 'vgbsm_enqueue_style' );
function vgbsm_enqueue_style() {
//	wp_enqueue_script('vgbsm_script');
	wp_enqueue_style( 'vgbsm_style' );
}