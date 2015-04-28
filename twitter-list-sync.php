<?php

/**
 * Plugin Name: Twitter List Sync
 * Description: Keep a Twitter list up to date with users from your site.
 * Version: 1.0.1
 * Author: Glen Scott
 * Author URI: http://www.glenscott.co.uk
 * License: GPL2
 */

/*  Copyright 2015  Glen Scott  (email : glen@glenscott.co.uk)

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

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require_once dirname( __FILE__ ) . '/vendor/autoload.php';

register_activation_hook( __FILE__, 'twitter_list_sync_activation' );
/**
 * On activation, set a time, frequency and name of an action hook to be scheduled.
 */
function twitter_list_sync_activation() {
	wp_schedule_event( time(), 'hourly', 'twitter_list_sync_hourly_event_hook' );
}

add_action( 'twitter_list_sync_hourly_event_hook', 'twitter_list_sync_do_this_hourly' );
/**
 * On the scheduled action hook, run the function.
 */
function twitter_list_sync_do_this_hourly() {
	$options = get_option( 'twitter_list_sync_option_name' );

	if ( ! $options ) {
		return;
	}

	if ( ! isset( $options['oauth_access_token'] ) ||
	     ! isset( $options['oauth_access_token_secret'] ) ||
	     ! isset( $options['consumer_key'] ) ||
	     ! isset( $options['consumer_secret'] )
	     ) {
		return;
	}
	$settings = array(
		'oauth_access_token' => $options['oauth_access_token'],
		'oauth_access_token_secret' => $options['oauth_access_token_secret'],
		'consumer_key' => $options['consumer_key'],
		'consumer_secret' => $options['consumer_secret'],
	);

	/*
	* Get users from Twitter list - list1
	* Get users from DB with Twitter handle - list2
	*/
	$url = 'https://api.twitter.com/1.1/lists/members.json';
	$getField = '?slug=' . $options['list_slug'] . '&owner_screen_name=' . $options['list_owner_screen_name'] . '&count=5000';
	$requestMethod = 'GET';

	$twitter = new TwitterAPIExchange( $settings );
	$json = $twitter->setGetField( $getField )
				    ->buildOauth( $url, $requestMethod )
				    ->performRequest();

	$users = json_decode( $json );

	$twitterUsers = array();

	if ( isset( $users->users ) ) {
		foreach ( $users->users as $user ) {
			$twitterUsers[] = strtolower( $user->screen_name );
		}
	}

	$args = array('meta_key' => 'twitter');
	$blogusers = get_users( $args );

	$wordpressUsers = array();

	foreach ( $blogusers as $user ) {
		$twitterScreenName = get_user_meta( $user->ID, 'twitter', true );

		if ( $twitterScreenName ) {
			// it seems adding a @ before the screenname is a common issue, so let's strip it off
			$twitterScreenName = preg_replace( '/^@/', '', $twitterScreenName );
			$wordpressUsers[] = strtolower( $twitterScreenName );
		}
	}

	// add new users
	$newUsers = array_diff( $wordpressUsers, $twitterUsers );

	foreach ( array_chunk( $newUsers, 100 ) as $wpUsers ) {
		$url = 'https://api.twitter.com/1.1/lists/members/create_all.json';
		$requestMethod = 'POST';

		$twitter = new TwitterAPIExchange( $settings );
		$postFields = array(
			'slug'              => $options['list_slug'],
	        'owner_screen_name' => $options['list_owner_screen_name'],
	        'screen_name'       => implode( ',', $wpUsers ),
		);

		$json = $twitter->setPostfields( $postFields )
		                ->buildOauth( $url, $requestMethod )
						->performRequest();
	}

	// remove users
	$removeUsers = array_diff( $twitterUsers, $wordpressUsers );

	foreach ( array_chunk( $removeUsers, 100 ) as $tUsers ) {
		$url = 'https://api.twitter.com/1.1/lists/members/destroy_all.json';
		$requestMethod = 'POST';

		$twitter = new TwitterAPIExchange( $settings );
		$postFields = array(
			'slug'              => $options['list_slug'],
			'owner_screen_name' => $options['list_owner_screen_name'],
			'screen_name'       => implode( ',', $tUsers ),
		);

		$json = $twitter->setPostfields( $postFields )
						->buildOauth( $url, $requestMethod )
						->performRequest();
	}
}

register_deactivation_hook( __FILE__, 'twitter_list_sync_deactivation' );
/**
 * On deactivation, remove all functions from the scheduled action hook.
 */
function twitter_list_sync_deactivation() {
	wp_clear_scheduled_hook( 'twitter_list_sync_hourly_event_hook' );
}

add_option( 'twitter_list_sync_option_name' );
include  dirname( __FILE__ ) . '/options.php';
