<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   ticketsys
 * @author    EgyFirst <mail@amreha.com>
 * @license   GPL-2.0+
 * @link      http://www.amreha.com
 * @copyright 2014 EgyFirst
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
$sql="DROP TABLE ACCOUNT";

// @TODO: Define uninstall functionality here
