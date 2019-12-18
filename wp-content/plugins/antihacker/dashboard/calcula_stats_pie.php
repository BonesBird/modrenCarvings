<?php
/**
 * @author William Sergio Minossi
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
global $wpdb;
$table_name = $wpdb->prefix . "ah_stats";
$month_day = date('md');

$query = "SELECT
date, 
qlogin as brute, 
qfire as firewall,
qenum as enumeration,
qplugin as plugin,
qtema as theme,
qfalseg as false_se,
qblack as blacklisted
FROM " . $table_name. "
WHERE date <= ".$month_day. "
ORDER BY date DESC  limit 15" ;


$antihacker_results8 = $wpdb->get_results($query);
$antihacker_results9 = json_decode(json_encode($antihacker_results8), true);
unset($antihacker_results8);

$antihacker_results10[0]['brute'] = 0;
$antihacker_results10[0]['firewall'] = 0;
$antihacker_results10[0]['enumeration'] = 0;
$antihacker_results10[0]['plugin'] = 0;
$antihacker_results10[0]['theme'] = 0;
$antihacker_results10[0]['false_se'] = 0;

for($i = 0; $i < count($antihacker_results9); $i++)
{
    $antihacker_results10[0]['brute'] = $antihacker_results10[0]['brute'] + intval( $antihacker_results9[$i]['brute']);
    $antihacker_results10[0]['firewall'] = $antihacker_results10[0]['firewall'] + intval( $antihacker_results9[$i]['firewall']);
    $antihacker_results10[0]['enumeration'] = $antihacker_results10[0]['enumeration'] + intval(  $antihacker_results9[$i]['enumeration']);
    $antihacker_results10[0]['plugin'] = $antihacker_results10[0]['plugin'] + intval( $antihacker_results9[$i]['plugin']);
    $antihacker_results10[0]['theme'] =  $antihacker_results10[0]['theme'] + intval( $antihacker_results9[$i]['theme']);
    $antihacker_results10[0]['false_se'] = $antihacker_results10[0]['false_se'] + intval( $antihacker_results9[$i]['false_se']);
}
 //print_r($antihacker_results10);
 //die();


return;