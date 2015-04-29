=== Plugin Name ===
Contributors: glen_scott
Tags: twitter, list, users, sync, social
Requires at least: 3.0.1
Tested up to: 4.2
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Keep a Twitter list up to date with users that have Twitter screenames in your WordPress instance.

== Description ==

A Twitter list is a curated group of Twitter users.  This plugin will add and remove users that have filled in their Twitter screen name to a specified list on a regular basis.

Icons made by <a href="http://www.flaticon.com/authors/pavel-kozlov" title="Pavel Kozlov">Pavel Kozlov</a> from <a href="http://www.flaticon.com" title="Flaticon">www.flaticon.com</a> is licensed by <a href="http://creativecommons.org/licenses/by/3.0/" title="Creative Commons BY 3.0">CC BY 3.0</a>

== Installation ==

* Sign up for a Twitter dev account.
* Create a new app
* Ensure the app has read/write permissions
* Generate an access token for your app
* Install and enable the Twitter List Sync plugin
* Under Settings -> Twitter List Sync, fill in your Consumer key, consumer secret, access token and access token secret listed on your Twitter app page
* Also fill in the slug of your list, and owner.  E.g. if you can access your list at https://twitter.com/glenscott/lists/twitter-list-sync then the list slug is "twitter-list-sync" and the owner slug is "glenscott"
* The plugin will automatically run every hour -- the first run will synchronise your users with the list

== Changelog ==

= 1.0.1 =
* Use case-insensitive username comparison

= 1.0 =
* Initial release
