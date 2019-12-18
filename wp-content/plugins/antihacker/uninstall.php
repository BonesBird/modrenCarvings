<?php
/**
 * @author William Sergio Minossi
 * @copyright 2016
 */
// If uninstall is not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}
$antihacker_option_name[] = 'my_radio_xml_rpc';
$antihacker_option_name[] = 'antihacker_rest_api';
$antihacker_option_name[] = 'antihacker_automatic_plugins';
$antihacker_option_name[] = 'antihacker_automatic_themes';
$antihacker_option_name[] = 'antihacker_replace_login_error_msg';
$antihacker_option_name[] = 'antihacker_disallow_file_edit';
$antihacker_option_name[] = 'antihacker_debug_is_true';
$antihacker_option_name[] = 'antihacker_firewall';
$antihacker_option_name[] = 'my_whitelist';
$antihacker_option_name[] = 'my_email_to';
$antihacker_option_name[] = 'my_checkbox_all_failed';
$antihacker_option_name[] = 'my_radio_all_logins';
$antihacker_option_name[] = 'antihacker_Blocked_Firewall';
$antihacker_option_name[] = 'antihacker_hide_wp';
$antihacker_option_name[] = 'antihacker_block_enumeration';
$antihacker_option_name[] = 'antihacker_Blocked_else';
$antihacker_option_name[] = 'antihacker_block_all_feeds';
$antihacker_option_name[] = 'antihacker_new_user_subscriber';
$antihacker_option_name[] = 'antihacker_checkversion';
$antihacker_option_name[] = 'antihacker_block_falsegoogle';
$antihacker_option_name[] = 'antihacker_block_search_plugins';
$antihacker_option_name[] = 'antihacker_block_search_themes';
$antihacker_option_name[] = 'antihacker_version';
$wnum = count($antihacker_option_name);
for ($i = 0; $i < $wnum; $i++)
{
 delete_option( $antihacker_option_name[$i] );
 // For site options in Multisite
 delete_site_option( $antihacker_option_name[$i] );    
}
// Drop a custom db table
global $wpdb;
$current_table = $wpdb->prefix . 'ah_stats';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
$current_table = $wpdb->prefix . 'ah_blockeds';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
$current_table = $wpdb->prefix . 'ah_visitorslog';
$wpdb->query( "DROP TABLE IF EXISTS $current_table" );
?>