<?php /**
 * @author William Sergio Minozzi
 * @copyright 2016
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
// if admin 
// global $antihacker_now;
// Many Pages
// Empty UA
$antihackerip = trim(ahfindip());
$antihacker_ua = antihacker_get_ua();
global $wp_query;
    if (version_compare(trim(ANTIHACKERVERSION), trim($antihacker_version)) > 0) {
    // Version was changed - Update made.
        antihacker_create_db_stats();
        antihacker_create_db_blocked();
        antihacker_create_db_visitors();
        antihacker_remove_index();
        antihacker_add_index();
        antihacker_upgrade_db();
        antihacker_populate_stats();
    if (!add_option('antihacker_version', ANTIHACKERVERSION)) {
        update_option('antihacker_version', ANTIHACKERVERSION);
    }
  }
function antihacker_gopro_callback9()
{
    $urlgopro = "http://antihackerplugin.com/premium/";
    ?>
    <script type="text/javascript">
    <!--
     window.location  = "<?php echo $urlgopro; ?>";
    -->
    </script>
<?php
}
function antihacker_add_menu_items9()
{
    global $antihacker_checkversion;
    if (empty($antihacker_checkversion)) {
        $antihacker_gopro_page = add_submenu_page('anti_hacker_plugin', // $parent_slug
            'Go Pro', // string $page_title
            '<font color="#FF6600">Go Pro</font>', // string $menu_title
            'manage_options', // string $capability
            'antihacker_my-custom-submenu-page9', 'antihacker_gopro_callback9');
    }
}
add_filter('plugin_row_meta', 'antihacker_custom_plugin_row_meta', 10, 2);
function antihacker_custom_plugin_row_meta($links, $file)
{
    global $antihacker_checkversion;
    if (strpos($file, 'antihacker.php') !== false) {
        $new_links = array(
            'OnLine Guide' => '<a href="http://antihackerplugin.com/help/" target="_blank">OnLine Guide</a>');
        if (empty($antihacker_checkversion)) {
            $new_links['Pro'] = '<a href="http://antihackerplugin.com/premium/" target="_blank"><b><font color="#FF6600">Go Pro</font></b></a>';
        }
        $links = array_merge($links, $new_links);
    }
    return $links;
}
$antihacker_first_time = antihacker_first_time2();
if ($antihacker_first_time) {
    add_action('wp_enqueue_scripts', 'antihacker_include_jquery2');
}
if (!empty($antihacker_checkversion))
{
    $ah_cookie = 'antihacker_cookie';
    if (isset($_COOKIE[$ah_cookie])) {
        $antihacker_cookie = $_COOKIE[$ah_cookie];
        if ($antihacker_cookie == '0') {
            if (!$antihacker_first_time) {
                add_action('wp_enqueue_scripts', 'antihacker_include_jquery2');
            } else {
                // 1a. vez e nao tem cookie...
                $antihacker_cookie = '?';
            }
        }
    } else {
        if (!$antihacker_first_time) {
            add_action('wp_enqueue_scripts', 'antihacker_include_jquery2');
            $antihacker_cookie = '0';
        } else {
            // 1a. vez e nao tem cookie...
            $antihacker_cookie = '?';
        }
    }
}
else
{
    $antihacker_cookie = '1';
}
if ($antihacker_cookie != '1') {
    echo '<script type="text/javascript">';
    echo 'document.cookie = "billjavascript=true";';
    echo '</script>';
    if (isset($_COOKIE['billjavascript']))
        $antihacker_javascript = 1;
    else
        $antihacker_javascript = 0;
} else
    $antihacker_javascript = 1;
function antihacker_include_jquery2()
{
    wp_enqueue_script("jquery");
    wp_enqueue_script('jquery-ui-core');
    wp_register_script('bill-cookies', ANTIHACKERURL .
        'antihacker_cookies.js', array('jquery'), null, true);
    wp_enqueue_script('bill-cookies');
}
function antihacker_first_time2()
{
    global $wpdb;
    global $antihackerip;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    $table_name = $wpdb->prefix . "ah_visitorslog";
    $query = "select ip FROM " . $table_name .
        " WHERE ip = '" . $antihackerip . "'
        AND `date` >= CURDATE() - interval 7 day ORDER BY `date` DESC";
    if ($wpdb->get_var($query) > 0)
        return false;
    else
        return true;
}
function antihacker_gravalog()
{
    global $wpdb;
    global $logplugin_ip;
    global $logplugin_cookie;
    global $logplugin_response;
    global $antihacker_checkversion;
    global $antihacker_version;
    if (is_admin() or is_super_admin() or empty($antihacker_checkversion) ) 
         return;
    if (@is_404()) {
        $logplugin_response = '404';
    } else {
        $logplugin_response = http_response_code();
    }
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_visitorslog";
    if (version_compare(trim(ANTIHACKERVERSION), trim($antihacker_version)) > 0) {
        antihacker_remove_index();
    }
    $query = "INSERT INTO " . $table_name .
        " (ip, cookie, response)
        VALUES ('" . $logplugin_ip . "',
        '" . $logplugin_cookie . "',
        '" . $logplugin_response . "')";
    $r = $wpdb->get_results($query);
    return;
}
if (!empty($antihacker_checkversion))
{
    add_action('init', 'antihacker_create_schedule');
    add_action('antihacker_cron_job', 'antihacker_cron_function');
}
function antihacker_create_schedule()
{
    //check if event scheduled before
    if (!wp_next_scheduled('antihacker_cron_job'))
        //shedule event to run after 1 day
        wp_schedule_single_event(time() + (24 * 3600), 'antihacker_cron_job');
}
function antihacker_cron_function()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_blockeds";
    $sql = "delete from " . $table_name . " WHERE `date` <  CURDATE() - interval 5 day";
    dbDelta($sql);
}
function antihacker_add_blocklist($ip)
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_blockeds";
    $query = "select * from " . $table_name . " WHERE ip = '" . $ip .
        "' LIMIT 1";
    if ($wpdb->get_var($query) > 0)
        return true;
    $query = "INSERT INTO " . $table_name .
        " (ip)
    VALUES ('" . $ip . "')";
    $r = $wpdb->get_results($query);
}
function antihacker_check_blocklist($ip)
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_blockeds";
    $query = "select * from " . $table_name . " WHERE ip = '" . $ip .
        "' LIMIT 1";
    if ($wpdb->get_var($query) > 0)
        return true;
    else
        return false;
}
function antihacker_bill_ask_for_upgrade2()
{
    global $antihacker_checkversion;
    if (!empty($antihacker_checkversion)) {
        return;
    }
    echo '<script type="text/javascript">';
    echo 'jQuery(document).ready(function() {';
    echo 'jQuery("#antihacker_block_search_themes_1").attr("disabled", true);';
    echo 'jQuery("#antihacker_block_search_plugins_1").attr("disabled", true);';
    echo 'jQuery("#antihacker_block_falsegoogle_1").attr("disabled", true);';
    echo '});';
    echo '</script>';
}
function antihacker_bill_ask_for_upgrade()
{
    global $antihacker_checkversion;
    if (!empty($antihacker_checkversion)) {
        return;
    }
    $time = date('Ymd');
    if ($time == '20191129') {
        $x = 4; // rand(0, 3);
        // $x = 4;
    } else {
        $x = rand(0, 3);
    }
    
    // $x = 3;

    if ($x == 0) {
        $banner_image = ANTIHACKERIMAGES . '/eating.png';
        $bill_banner_bkg_color = 'orange';
        $banner_txt = __('Hackers can do all sorts of nasty stuff and destroy your site and online reputation.', 'antihacker');
    } elseif ($x == 1) {
        $banner_image = ANTIHACKERIMAGES . '/monitor-com-maca3.png';
        $bill_banner_bkg_color = 'orange';
        $banner_txt = __('Hackers don’t play by the rules.', 'antihacker');
    } elseif ($x == 2) {
        $banner_image = ANTIHACKERIMAGES . '/unlock-icon-red-small.png';
        $bill_banner_bkg_color = 'turquoise';
        $banner_txt = __('Hackers stresses your Web servers.', 'antihacker');
    } elseif ($x == 3) {
        $banner_image = ANTIHACKERIMAGES . '/5stars.png';
        $bill_banner_bkg_color = 'turquoise';
        $banner_txt = __('Show support with a 5-star rating.', 'antihacker');
    } elseif ($x == 4) {
        $banner_image = ANTIHACKERIMAGES . '/special-offer.png';
        $bill_banner_bkg_color = 'turquoise';
        $banner_txt = __('BLACK FRIDAY 30% OFF! Use the coupon code: special-black_2019. Limited time!', 'antihacker');
    } else {
        $banner_image = ANTIHACKERIMAGES . '/keys_from_left.png';
        $bill_banner_bkg_color = 'orange';
        $banner_txt = __('Become Pro: Increase your protection.', 'antihacker');
    }
    $banner_tit = __('Anti Hackers Plugin. Its time to Get Pro Protection!', 'antihacker');
    echo '<script type="text/javascript" src="' . ANTIHACKERURL .
        'js/c_o_o_k_i_e.js' . '"></script>';
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function() {
            <?php
                if (empty($antihacker_checkversion)) {
                    echo 'jQuery("#antihacker_block_search_themes_1").attr("disabled", true);';
                    echo 'jQuery("#antihacker_block_search_plugins_1").attr("disabled", true);';
                    echo 'jQuery("#antihacker_block_falsegoogle_1").attr("disabled", true);';
            }
           ?>
        	var hide_message = jQuery.cookie('antihacker_bill_go_pro_hide');
        /*	 hide_message = false; */
        	if (hide_message == "true") {
        		jQuery(".antihacker_bill_go_pro_container").css("display", "none");
        	} else {
                   setTimeout( function(){
                   //  jQuery(".bill_go_pro_container").slideDown("slow");
          		   jQuery(".antihacker_bill_go_pro_container").css("display", "block");
                  }  , 2000 );
        	};
        	jQuery(".antihacker_bill_go_pro_close_icon").click(function() {
        		jQuery(".antihacker_bill_go_pro_message").css("display", "none");
        		jQuery.cookie("antihacker_bill_go_pro_hide", "true", {
        			expires: 7
        		});
        		jQuery(".antihacker_bill_go_pro_container").css("display", "none");
        	});
        	jQuery(".antihacker_bill_go_pro_dismiss").click(function(event) {
        		jQuery(".antihacker_bill_go_pro_message").css("display", "none");
        		jQuery.cookie("antihacker_bill_go_pro_hide", "true", {
        			expires: 7
        		});
        		event.preventDefault()
        		jQuery(".antihacker_bill_go_pro_container").css("display", "none");
            });
        }); // end (jQuery);
    </script>
    <style type="text/css">
            .antihacker_bill_go_pro_close_icon {
            width:31px;
            height:31px;
            border: 0px solid red;
            box-shadow:none;
            float:right;
            margin:8px;
            margin:60px 40px 8px 8px;
            }
            .antihacker_bill_hide_settings_notice:hover,.antihacker_bill_hide_premium_options:hover {
            cursor:pointer;
            }
            .antihacker_bill_hide_premium_options {
            position:relative;
            }
            .antihacker_bill_go_pro_image {
            float:left;
            margin-right:20px;
            max-height:90px !important;
            }
            .antihacker_bill_image_go_pro {
            max-width:200px;
            max-height:88px;
            }
            .antihacker_bill_go_pro_text {
            font-size:18px;
            padding:10px;
            margin-bottom: 5px;
            }
            .antihacker_bill_go_pro_button_primary_container {
            float:left;
            margin-top: 0px;
            }
            .antihacker_bill_go_pro_dismiss_container
            {
              margin-top: 0px;
            }
            .antihacker_bill_go_pro_buttons {
              display: flex;
              max-height: 30px;
              margin-top: -10px;
            }
            .antihacker_bill_go_pro_container {
                border:1px solid darkgray;
                height:88px;
                padding: 0;
                margin: 10px 0px 15px 0px;
                  background: <?php echo $bill_banner_bkg_color; ?>
            }
            .antihacker_bill_go_pro_dismiss {
              margin-left:15px !important;
            }
             .button {
                vertical-align: top;
            }
            @media screen and (max-width:900px) {
                .antihacker_bill_go_pro_text {
                  font-size:16px;
                  padding:5px;
                  margin-bottom: 10px;
                }
            }
            @media screen and (max-width:800px) {
                .antihacker_bill_go_pro_container {
                  display:none !important;
                }
            }
	</style>
    <div class="notice notice-success antihacker_bill_go_pro_container" style="display: none;">
    	<div class="antihacker_bill_go_pro_message antihacker_bill_banner_on_plugin_page antihacker_bill_go_pro_banner">
    		<div class="antihacker_bill_go_pro_image">
    			<img class="antihacker_bill_image_go_pro" title="" src="<?php echo $banner_image; ?>" alt="" />
    		</div>
    		<div class="antihacker_bill_go_pro_text">
							<!-- <strong>
								Weekly Updates!
							</strong> -->
    						<span>
								<strong>
 						  	    <?php echo $banner_txt; ?>
 						  	    </strong>
    						</span>
    					    <br />
                             <?php 
                             
                             if($x != '3')
                                 echo $banner_tit; 
                             else 
                                 echo __('Help keep Anti Hacker Plugin going strong!','antihacker'); 
                             
                             ?>
    		</div>
            <div class="antihacker_bill_go_pro_buttons">
        		<div class="antihacker_bill_go_pro_button_primary_container">

                 <?php  if($x != '3')
                 {
                    echo '<a class="button button-primary" target="_blank" href="http://antihackerplugin.com/premium/">';
                    echo  __("Learn More", "antihacker");
                    echo '</a>';
                 }
                 else {
                    echo '<a class="button button-primary" target="_blank" href="https://wordpress.org/support/plugin/antihacker/reviews/#new-post">';
                    echo  __("Go to WordPress", "antihacker");
                    echo '</a>';
             
                 } ?>
        		</div>
        		<div class="antihacker_bill_go_pro_dismiss_container">
        			<a class="button button-secondary antihacker_bill_go_pro_dismiss" target="_blank" href="http://antihackerplugin.com/premium/"><?php echo __('Dismiss',
        'antihacker');?></a>
        		</div>
            </div>
    	</div>
    </div>
<?php
} // end Bill ask for upgrade
$antihacker_now = strtotime("now");
$antihacker_after = strtotime("now") + (3600);
function antihacker_gocom()
{
    global $antihacker_now;
    $antihacker_con = get_option('$antihacker_con', $antihacker_now);
    if ($antihacker_con > $antihacker_now) {
        return false;
    } else {
        return true;
    }
}
function antihacker_confail()
{
    global $antihacker_after;
    global $antihacker_checkversion;
    add_option('$antihacker_con', $antihacker_after);
    update_option('$antihacker_con', $antihacker_after);
}
if (empty($antihacker_checkversion)) {
    add_action('admin_notices', 'antihacker_bill_ask_for_upgrade');
}
function antihacker_update()
{
    global $antihacker_checkversion;
    if (!antihacker_gocom()) {
       return;
    }
    $last_checked = get_option('antihacker_last_checked2', '0');
    $days = 3;
    $write = time() - (4 * 24 * 3600);
    if ($last_checked == '0') {
        if (!add_option('antihacker_last_checked2', $write)) {
            update_option('antihacker_last_checked2', $write);
        }
        return;
    } elseif (($last_checked + ($days * 24 * 3600)) > time()) {
        return;
    }
    ob_start();
    $domain_name = get_site_url();
    $urlParts = parse_url($domain_name);
    $domain_name = preg_replace('/^www\./', '', $urlParts['host']);
    $myarray = array(
        'domain_name' => $domain_name,
    );
    $url = "http://antihackerplugin.com/api/httpapi.php";
    $response = wp_remote_post($url, array(
        'method' => 'POST',
        'timeout' => 5,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array(),
        'body' => $myarray,
        'cookies' => array()
    ));
    if (is_wp_error($response)) {
        antihacker_confail();
        ob_end_clean();
        return;
    }
    $r = trim($response['body']);
    $r = json_decode($r, true);
    $q = count($r);
    if ($q == 1) {
        $botip = trim($r[0]['ip']);
        if ($botip == '-9') {
            update_option('antihacker_checkversion', '');
            update_option('antihacker_block_false_google', '');
            update_option('antihacker_block_search_plugins', '');
            update_option('antihacker_block_search_themes', '');
        }
    }
    if (!add_option('antihacker_last_checked2', time())) {
        update_option('antihacker_last_checked2', time());
    }
    ob_end_clean();
}
if ($antihacker_block_falsegoogle == 'yes') {
    if (antihacker_check_false_googlebot()) {
        if ($antihacker_Blocked_else_email == 'yes')
            antihacker_alertme7();
        antihacker_stats_moreone('qfalseg');
        antihacker_gravalog();
        antihacker_response();
    }
}
function antihacker_check_false_googlebot()
{
    global $antihacker_checkversion;
    //  or is_super_admin()
    if (is_admin() or empty($antihacker_checkversion) ) 
      return false;
    // crawl-66-249-73-151.googlebot.com
    // msnbot-157-55-39-204.search.msn.com
    // msnbot-157-55-39-143.search.msn.com
    global $antihackerip;
    global $antihacker_ua;
    $mysearch = array(
        'googlebot',
        'bingbot',
        'msn.com',
    );
    $mysearch1 = array(
        'googlebot',
        'msnbot',
        'msnbot'
    );
    for ($i = 0; $i < count($mysearch); $i++) {
        if (stripos($antihacker_ua, $mysearch[$i]) !== false) {
            $host = strip_tags(gethostbyaddr($antihackerip));
            if (stripos($host, $mysearch1[$i]) === false) {
                return true;
            }
        }
    }
    return false;
}
function antihacker_get_ua()
{
    if (!isset($_SERVER['HTTP_USER_AGENT'])) {
        return "mozilla compatible";
    }
    $ua = trim(sanitize_text_field($_SERVER['HTTP_USER_AGENT']));
    $ua = antihacker_clear_extra($ua);
    return $ua;
}
function antihacker_clear_extra($mystring)
{
    $mystring = str_replace('$', 'S;', $mystring);
    $mystring = str_replace('{', '!', $mystring);
    $mystring = str_replace('shell', 'chell', $mystring);
    $mystring = str_replace('curl', 'kurl', $mystring);
    $mystring = str_replace('<', '&lt;', $mystring);
    return $mystring;
}
function antihacker_maybe_searchengine()
{
    global $antihacker_ua;
    $mysearch = array(
        'AOL',
        'Baidu',
        'Bingbot',
        'msn',
        'DuckDuck',
        'Google',
        'Teoma',
        'Yahoo',
        'slurp',
        'seznam',
        'Yandex'
    );
    for ($i = 0; $i < count($mysearch); $i++) {
        if (stripos($antihacker_ua, $mysearch[$i]) !== false) {
            return true;
        }
    }
    return false;
}
function antihacker_final_step()
{
    global $antihacker_ua;
    global $antihacker_block_search_plugins;
    global $antihacker_block_search_themes;
    global $antihacker_Blocked_else_email;
    global $antihackerip;
    global $antihacker_cookie;
    global $antihacker_checkversion;
    if (is_admin() or is_super_admin() or empty($antihacker_checkversion))  
      return;
    if (is_404()) {
        $antihacker_response = '404';
    } else {
        $antihacker_response = http_response_code();
    }
    if ($antihacker_response == '404' and !antihacker_maybe_searchengine() and $antihacker_cookie == '0') {
        //  Plugins ...
        if ($antihacker_block_search_plugins == 'yes') {
            if (antihacker_looking_for_plugin()) {
                $plugin_name = antihacker_plugin_name();
                if (!antihacker_valid_plugin($plugin_name)) {
                    if (!antihacker_maybe_searchengine($antihacker_ua)) {
                        antihacker_stats_moreone('qplugin');
                        antihacker_add_blocklist($antihackerip);
                        if ($antihacker_Blocked_else_email == 'yes')
                            antihacker_alertme5();
                        antihacker_gravalog();
                        antihacker_response();
                    }
                }
            }
        }
        //  Temas ...
        if ($antihacker_block_search_themes == 'yes') {
            if (antihacker_looking_for_tema()) {
                $tema_name = antihacker_tema_name();
                if (!antihacker_valid_tema($tema_name)) {
                    if (!antihacker_maybe_searchengine($antihacker_ua)) {
                        antihacker_stats_moreone('qtema');
                        antihacker_add_blocklist($antihackerip);
                        if ($antihacker_Blocked_else_email == 'yes')
                            antihacker_alertme6();
                        antihacker_response();
                    }
                }
            }
        }
    }
    antihacker_gravalog();
}
function antihacker_valid_tema($tema_procurado)
{
    $all_temas = wp_get_themes();
    $loopCtr = 0;
    foreach ($all_temas as $tema_item) {
        $tema_title = trim(strtolower($tema_item['Name']));
        $tema_procurado = trim(strtolower($tema_procurado));
        if ($tema_title == $tema_procurado) {
            return true;
        }
        $loopCtr++;
    }
    return false;
}
function antihacker_valid_plugin($plugin_procurado)
{
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $all_plugins = get_plugins();
    $plugin_procurado = trim(strtolower($plugin_procurado));
    foreach ($all_plugins as $plugin_item) {
        $plugin_title = trim(strtolower($plugin_item['TextDomain']));
        $pos = stripos($plugin_title, $plugin_procurado);
        if ($pos !== false) {
            return true;
        }
    }
    return false;
}
function antihacker_looking_for_plugin()
{
    global $antihacker_current_url;
    $plugins_url = plugins_url();
    $pos =   antihacker_rstrpos2($plugins_url, '/', 2);
    $plugins_url = substr($plugins_url, $pos) . '/';
    if (stripos($antihacker_current_url, $plugins_url) !== false) {
        return true;
    } else {
        return false;
    }
}
function antihacker_plugin_name()
{
    // nome plugin procurado.
    global $antihacker_current_url;
    $plugins_url = plugins_url();
    $wpos =   antihacker_rstrpos2($plugins_url, '/', 2);
    $plugins_url = substr($plugins_url, $wpos) . '/';
    $wsize =  strlen($plugins_url);
    $xwork = substr($antihacker_current_url, $wsize);
    $wpos = strpos($xwork, '/');
    return substr($xwork, 0, $wpos);
}
function antihacker_looking_for_tema()
{
    global $antihacker_current_url;
    $themes_url = get_template_directory();
    $pos =   antihacker_rstrpos2($themes_url, '/', 3);
    $themes_url = substr($themes_url, $pos) . '/';
    $wsize = strlen($themes_url);
    $themes_url = substr($themes_url, 0, $wsize - 1);
    $wpos = strrpos($themes_url, '/');
    $themes_url = substr($themes_url, 0, $wpos);
    if (stripos($antihacker_current_url, $themes_url) !== false) {
        return true;
    } else {
        return false;
    }
}
function antihacker_tema_name()
{
    global $antihacker_current_url;
    $themes_url = get_template_directory();
    $pos =   antihacker_rstrpos2($themes_url, '/', 3);
    $themes_url = substr($themes_url, $pos) . '/';
    $wsize = strlen($themes_url);
    $themes_url = substr($themes_url, 0, $wsize - 1);
    $wpos = strrpos($themes_url, '/');
    $wstring = substr($themes_url, 0, $wpos + 1);
    $wpos = strpos($antihacker_current_url, $wstring);
    $wlen = strlen($wstring);
    $tema_name = substr($antihacker_current_url, $wpos + $wlen);
    $wpos = strpos($tema_name, '/');
    $tema_name = substr($tema_name, 0, $wpos);
    return $tema_name;
}
//search backwards for needle in haystack, and return its position
function antihacker_rstrpos2($haystack, $needle, $num)
{
    for ($i = 1; $i <= $num; $i++) {
        # first loop return position of needle
        if ($i == 1) {
            $pos = strrpos($haystack, $needle);
        }
        # subsequent loops trim haystack to pos and return needle's new position
        if ($i != 1) {
            $haystack = substr($haystack, 0, $pos);
            $pos = strrpos($haystack, $needle);
        }
    }
    return $pos;
}
///////////////////////////////////////////////////////////////////
if ($antihacker_new_user_subscriber == 'yes') {
    add_action('user_register', 'antihacker_new_user_subscriber', 10, 1);
}
function antihacker_new_user_subscriber($user_id)
{
    $user = new WP_User($user_id);
    $user->set_role('subscriber');
}
if (is_admin()) {
    // Report new plugin installed...
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    function antihacker_save_name_plugins()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $all_plugins_keys = array_keys($all_plugins);
        if (count($all_plugins) < 1)
            return;
        $my_plugins = '';
        $loopCtr = 0;
        foreach ($all_plugins as $plugin_item) {
            if ($my_plugins != '')
                $my_plugins .= PHP_EOL;
            $plugin_title = $plugin_item['Name'];
            $my_plugins .= $plugin_title;
            $loopCtr++;
        }
        if (!update_site_option('antihacker_name_plugins', $my_plugins))
            add_site_option('antihacker_name_plugins', $my_plugins);
    }
    function antihacker_q_plugins_now()
    {
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $all_plugins_keys = array_keys($all_plugins);
        return count($all_plugins);
    }
    function antihacker_q_plugins()
    {
        // $nplugins = sanitize_text_field(get_site_option('antihacker_name_plugins', ''));
        $nplugins = get_site_option('antihacker_name_plugins', '');
        $nplugins = explode(PHP_EOL, $nplugins);
        return count($nplugins);
    }
    function antihacker_alert_plugin()
    {
        global $ah_admin_email, $antihacker_new_plugin;
        $dt = date("Y-m-d H:i:s");
        $dom = sanitize_text_field($_SERVER['SERVER_NAME']);
        $url = esc_url($_SERVER['PHP_SELF']);
        $msg = __('Alert: New Plugin was installed.', "antihacker");
        $msg .= '<br>';
        $msg .= __('New Plugin Name: ', "antihacker");
        $msg .= $antihacker_new_plugin;
        $msg .= '<br>';
        $msg .= __('Date', "antihacker");
        $msg .= ': ';
        $msg .= $dt;
        $msg .= '<br>';
        $msg .= __('Domain', "antihacker");
        $msg .= ': ';
        $msg .= $dom;
        $msg .= '<br>';
        $msg .= '<br>';
        $msg .= __('This email was sent from your website', "antihacker");
        $msg .= ': ';
        $msg .= $dom . ' ';
        $msg .= __('by Anti Hacker plugin.', "antihacker");
        $msg .= '<br>';
        $email_from = 'wordpress@' . $dom;
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $ah_admin_email .
            "\r\n" . 'X-Mailer: PHP/' . phpversion();
        $to = $ah_admin_email;
        $subject = __('Alert: New Plugin was installed at: ', "antihacker") . $dom;
        wp_mail($to, $subject, $msg, $headers, '');
        return 1;
    }
    $qpluginsnow = antihacker_q_plugins_now();
    $qplugins = antihacker_q_plugins();
    if (($qplugins == 0 and $qpluginsnow > 0) or ($qplugins > $qpluginsnow)) {
        antihacker_save_name_plugins();
        $qplugins = antihacker_q_plugins();
    }
    if ($qpluginsnow > $qplugins) {
        $nplugins = get_site_option('antihacker_name_plugins', '');
        $nplugins = explode(PHP_EOL, $nplugins);
        if (!function_exists('get_plugins')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = get_plugins();
        $all_plugins_keys = array_keys($all_plugins);
        if (count($all_plugins) < 1)
            return;
        $my_plugins_now = array();
        $loopCtr = 0;
        foreach ($all_plugins as $plugin_item) {
            $plugin_title = $plugin_item['Name'];
            $my_plugins_now[$loopCtr] = $plugin_title;
            $loopCtr++;
        }
        $antihacker_new_plugin = '';
        for ($i = 0; $i < $qpluginsnow; $i++) {
            $plugin_name = $my_plugins_now[$i];
            if (!in_array($plugin_name, $nplugins)) {
                $antihacker_new_plugin = $plugin_name;
                break;
            }
        }
        add_action('plugins_loaded', 'antihacker_alert_plugin');
        antihacker_save_name_plugins();
    }  //  if ($qpluginsnow > $qplugins)  
    if ($qpluginsnow < $qplugins) {
        antihacker_save_name_plugins();
    }
}     // End  Report new plugin installed...
if (is_admin()) {
    if (isset($_GET['page'])) {
        if (sanitize_text_field($_GET['page']) == 'anti-hacker') {
            add_filter('contextual_help', 'ah_contextual_help', 10, 3);
            function ah_contextual_help($contextual_help, $screen_id, $screen)
            {
                $myhelp = '<br><big>';
                $myhelp .= __('Improve system security and help prevent unauthorized access to your account.', "antihacker");
                $myhelp .= '<br>';
                $myhelp .= __('Read the StartUp guide at Anti Hacker Settings page.', "antihacker");
                $myhelp .= '<br>';
                $myhelp .= __('Visit the', "antihacker");
                $myhelp .= ' <a href="http://antihackerplugin.com" target="_blank">';
                $myhelp .= __('plugin site', "antihacker");
                $myhelp .= ' </a>';
                $myhelp .= __('for more details.', "antihacker");
                $myhelp .= '</big>';
                $screen->add_help_tab(array(
                    'id' => 'wptuts-overview-tab',
                    'title' => __('Overview', 'plugin_domain'),
                    'content' => '<p>' . $myhelp . '</p>',
                ));
                return $contextual_help;
            }
        }
    }
}
function ahfindip()
{
    $ip = '';
    $headers = array(
        'HTTP_CLIENT_IP',        // Bill
        'HTTP_X_REAL_IP',        // Bill
        'HTTP_X_FORWARDED',      // Bill
        'HTTP_FORWARDED_FOR',    // Bill 
        'HTTP_FORWARDED',        // Bill
        'HTTP_X_CLUSTER_CLIENT_IP', //Bill
        'HTTP_CF_CONNECTING_IP', // CloudFlare
        'HTTP_X_FORWARDED_FOR',  // Squid and most other forward and reverse proxies
        'REMOTE_ADDR',           // Default source of remote IP
    );
    for ($x = 0; $x < 8; $x++) {
        foreach ($headers as $header) {
            if (!isset($_SERVER[$header]))
                continue;
            $myheader = trim(sanitize_text_field($_SERVER[$header]));
            if (empty($myheader))
                continue;
            $ip = trim(sanitize_text_field($_SERVER[$header]));
            if (empty($ip)) {
                continue;
            }
            if (false !== ($comma_index = strpos(sanitize_text_field($_SERVER[$header]), ','))) {
                $ip = substr($ip, 0, $comma_index);
            }
            // First run through. Only accept an IP not in the reserved or private range.
            if ($ip == '127.0.0.1') {
                $ip = '';
                continue;
            }
            if (0 === $x) {
                $ip = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE);
            } else {
                $ip = filter_var($ip, FILTER_VALIDATE_IP);
            }
            if (!empty($ip)) {
                break;
            }
        }
        if (!empty($ip)) {
            break;
        }
    }
    if (!empty($ip))
        return $ip;
    else
        return 'unknow';
}
function ah_whitelisted($antihackerip, $amy_whitelist)
{
    for ($i = 0; $i < count($amy_whitelist); $i++) {
        if (trim($amy_whitelist[$i]) == $antihackerip)
            return 1;
    }
    return 0;
}
function ah_successful_login($user_login)
{
    global $amy_whitelist;
    global $my_radio_all_logins;
    global $antihackerip;
    global $ah_admin_email;
    if (ah_whitelisted($antihackerip, $amy_whitelist) and $my_radio_all_logins <> 'Yes') {
        return 1;
    }
    $dt = date("Y-m-d H:i:s");
    $dom = sanitize_text_field($_SERVER['SERVER_NAME']);
    $msg = __('This email was sent from your website', "antihacker") . ' ';
    $msg .= $dom . '&nbsp; ' . __('by the AntiHacker plugin.', "antihacker");
    $msg .= '<br>';
    $msg .= __('Date', "antihacker") . ': ' . $dt . '<br>';
    $msg .= __('Ip', "antihacker") . ': ' . $antihackerip . '<br>';
    $msg .= __('Domain', "antihacker") . ': ' . $dom . '<br>';
    $msg .= __('User', "antihacker") . ': ' . $user_login;
    $msg .= '<br>';
    $msg .= __('Add this IP to your withelist to stop this email and change your Notification Settings.', "antihacker");
    $email_from = 'wordpress@' . $dom;
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $user_login . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $to = $ah_admin_email;
    $subject = __('Login Successful at', "antihacker") . ': ' . $dom;
    wp_mail($to, $subject, $msg, $headers, '');
    return 1;
}
function ah_activ_message()
{
    echo '<div class="updated"><p>';
    $bd_msg = '<img src="' . ANTIHACKERURL . '/images/infox350.png" />';
    $bd_msg .= '<h2>';
    $bd_msg .= __('Anti Hacker Plugin was activated!', "antihacker");
    $bd_msg .= '</h2>';
    $bd_msg .= '<h3>';
    $bd_msg .= __('For details and help, take a look at Anti Hacker at your left menu', "antihacker");
    $bd_msg .= '<br />';
    $bd_msg .= ' <a class="button button-primary" href="admin.php?page=anti-hacker">';
    $bd_msg .= __('or click here', "antihacker");
    $bd_msg .= '</a>';
    echo $bd_msg;
    echo "</p></h3></div>";
}
function ah_my_deactivation()
{
    // require_once (ANTIHACKERPATH . "includes/feedback/feedback.php");
    global $ah_admin_email, $antihackerip;
    $current_user = wp_get_current_user();
    $user_login = $current_user->user_login;
    $dt = date("Y-m-d H:i:s");
    $dom = sanitize_text_field($_SERVER['SERVER_NAME']);
    $url = esc_url($_SERVER['PHP_SELF']);
    $msg = __('Alert: the Anti Hacker plugin was been deactivated from plugins page.', "antihacker");
    $msg .= '<br>';
    $msg .= __('Date', "antihacker") . ': ' . $dt . '<br>';
    $msg .= __('Ip', "antihacker") . ': ' . $antihackerip . '<br>';
    $msg .= __('Domain', "antihacker") . ': ' . $dom . '<br>';
    $msg .= __('User', "antihacker") . ': ' . $user_login;
    $msg .= '<br>';
    $msg .= __('This email was sent from your website', "antihacker") . ' ' . $dom . ' ';
    $msg .= __('by Anti Hacker plugin.', "antihacker") . '<br>';
    $email_from = 'wordpress@' . $dom;
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $user_login . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $to = $ah_admin_email;
    $subject = __('Plugin Deactivated at', "antihacker") . ': ' . $dom;
    wp_mail($to, $subject, $msg, $headers, '');
    return 1;
}
function ah_user_enumeration_email()
{
    global $ah_admin_email, $antihackerip;
    $current_user = wp_get_current_user();
    $user_login = $current_user->user_login;
    $dt = date("Y-m-d H:i:s");
    $dom = sanitize_text_field($_SERVER['SERVER_NAME']);
    $url = esc_url($_SERVER['PHP_SELF']);
    $msg = __('Alert: User Enumeration attempt was blocked.', "antihacker");
    $msg .= '<br>';
    $msg .= __('Date', "antihacker") . ': ' . $dt . '<br>';
    $msg .= __('Ip', "antihacker") . ': ' . $antihackerip . '<br>';
    $msg .= __('Domain', "antihacker") . ': ' . $dom . '<br>';
    $msg .= '<br>';
    $msg .= __('This email was sent from your website', "antihacker") . ' ' . $dom . ' ';
    $msg .= __('by Anti Hacker plugin.', "antihacker") . '<br>';
    $msg .= '<br>';
    $msg .= __('You can disable it in Dashboard => Anti Hacker => Settings => Notifications', "antihacker");
    $msg .= '<br>';
    $email_from = 'wordpress@' . $dom;
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $user_login . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $to = $ah_admin_email;
    $subject = __('User Enumeration attempt was blocked', "antihacker") . ': ' . $dom;
    wp_mail($to, $subject, $msg, $headers, '');
    return 1;
}
function ah_email_display()
{ ?>
    <!-- <INPUT TYPE=CHECKBOX NAME="my_captcha">Yes, i'm a human! -->
    <? echo __('My Wordpress user email:', "antihacker"); ?>
    <br />
    <input type="text" id="myemail" name="myemail" value="" placeholder="" size="100" />
    <br />
<?php
}
function ah_failed_login($user_login)
{
    global $amy_whitelist;
    global $my_checkbox_all_failed;
    global $antihackerip;
    global $ah_admin_email;
    antihacker_stats_moreone('qlogin');
    if (ah_whitelisted($antihackerip, $amy_whitelist) or $my_checkbox_all_failed <> '1') {
        return;
    }
    $dt = date("Y-m-d H:i:s");
    $dom = sanitize_text_field($_SERVER['SERVER_NAME']);
    $msg =  __('This email was sent from your website', "antihacker");
    $msg .= ': ' . $dom . ' ';
    $msg .=  __('by the AntiHacker plugin.', "antihacker");
    $msg .= '<br> ';
    $msg .= __('Date', "antihacker");
    $msg .= ': ' . $dt . '<br>';
    $msg .= __('Ip', "antihacker") . ': ' . $antihackerip . '<br>';
    $msg .= __('Domain', "antihacker") . ': ' . $dom . '<br>';
    $msg .= __('User', "antihacker") . ': ' . $user_login;
    $msg .= '<br>';
    $msg .= __('Failed login', "antihacker");
    $msg .= '<br>';
    $msg .= '<br>';
    $msg .= __('You can stop emails at the Notifications Settings Tab.', "antihacker");
    $msg .= '<br>';
    $msg .= __('Dashboard => Anti Hacker => Notifications Settings.', "antihacker");
    $email_from = 'wordpress@' . $dom;
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    $headers .= "From: " . $email_from . "\r\n" . 'Reply-To: ' . $user_login . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $to = $ah_admin_email;
    $subject = __('Failed Login at:', "antihacker") . ' ' . $dom;
    wp_mail($to, $subject, $msg, $headers, '');
    return;
}
if (get_site_option('my_radio_xml_rpc', 'No') == 'Yes')
    add_filter('xmlrpc_enabled', '__return_false');
if (get_site_option('my_radio_xml_rpc', 'No') == 'Pingback')
    add_filter('xmlrpc_methods', 'ahpremove_xmlrpc_pingback_ping');
function ahpremove_xmlrpc_pingback_ping($methods)
{
    unset($methods['pingback.ping']);
    return $methods;
};
/////////////////////////////////////////
// Disable Json WordPress Rest API (also embed from WordPress 4.7). 
// Take a look our faq page (at our site) for details.'
function antihacker_after_inic()
{
    $ah_current_WP_version = get_bloginfo('version');
    function ah_Force_Auth_Error()
    {
        add_filter('rest_authentication_errors', 'ah_only_allow_logged_in_rest_access');
    }
    function ah_Disable_Via_Filters()
    {
        // Filters for WP-API version 1.x
        add_filter('json_enabled', '__return_false');
        add_filter('json_jsonp_enabled', '__return_false');
        // Filters for WP-API version 2.x
        add_filter('rest_enabled', '__return_false');
        add_filter('rest_jsonp_enabled', '__return_false');
        // Remove REST API info from head and headers
        remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('template_redirect', 'rest_output_link_header', 11);
        // 2019-04-23
        add_filter('rest_authentication_errors', function ($result) {
            if (!empty($result)) {
                return $result;
            }
            if (!is_user_logged_in()) {
                return new WP_Error('rest_not_logged_in', 'You are not currently logged in.', array('status' => 401));
            }
            return $result;
        });
    }
    function ah_only_allow_logged_in_rest_access($access)
    {
        if (!is_user_logged_in()) {
            return new WP_Error('rest_cannot_access', __('Only authenticated users can access API.', 'disable-json-api'), array('status' => rest_authorization_required_code()));
        }
        return $access;
    }
    if (version_compare($ah_current_WP_version, '4.7', '>=')) {
        ah_Force_Auth_Error();
    } else {
        ah_Disable_Via_Filters();
    }
}
$antihacker_rest_api = trim(get_site_option('antihacker_rest_api', 'No'));
if ($antihacker_rest_api <> 'No')
    add_action('plugins_loaded', 'antihacker_after_inic');
if (is_admin()) {
    if (get_option('ah_was_activated', '0') == '1') {
        antihacker_create_db_stats();
        antihacker_create_db_blocked();
        antihacker_create_db_visitors();
        antihacker_upgrade_db();
        antihacker_populate_stats();
        add_action('admin_notices', 'ah_activ_message');
        $r =  update_option('ah_was_activated', '0');
        if (!$r)
            add_option('ah_was_activated', '0');
    }
}
function ah_debug_enabled()
{
    echo '<div class="notice notice-warning is-dismissible">';
    echo '<br /><b>';
    echo __('Message from Anti Hacker Plugin', 'antihacker');
    echo ':</b><br />';
    echo __('Looks like Debug mode is enabled. (WP_DEBUG is true)', 'antihacker');
    echo '.<br />';
    echo __('if enabled on a production website, it might cause information disclosure, allowing malicious users to view errors and additional logging information', 'antihacker');
    echo '.<br />';
    echo __('Please, take a look in our site, FAQ page, item => Wordpress Debug Mode or disable this message at General Settings Tab. ', 'antihacker');
    echo '<br /><br /></div>';
}
function antihacker_alertme3($antihacker_string)
{
    global $antihackerip, $amy_whitelist, $ah_admin_email;
    global $antihacker_Blocked_Firewall, $antihackerserver;
    if (ah_whitelisted($antihackerip, $amy_whitelist) or $antihacker_Blocked_Firewall <> 'yes') {
        return;
    }
    $subject = __("Detected Bot on ", "antihacker") . $antihackerserver;
    $message[] = __("Malicious bot was detected and blocked by firewall.", "antihacker");
    $message[] = "";
    $message[] = __('Date', 'antihacker') . "..............: " . date("F j, Y, g:i a");
    $message[] = __('Robot IP Address', 'antihacker') . "..: " . $antihackerip;
    $message[] = __('Malicious String Found:', 'antihacker') . " " . $antihacker_string;
    $message[] = "";
    $message[] = __('eMail sent by Anti Hacker Plugin.', 'antihacker');
    $message[] = __(
        'You can stop emails at the Notifications Settings Tab.',
        'antihacker'
    );
    $message[] = __('Dashboard => Anti Hacker => Settings.', 'antihacker');
    $message[] = "";
    $msg = join("\n", $message);
    mail($ah_admin_email, $subject, $msg);
    return;
}
function antihacker_alertme4($antihacker_string)
{
    global $antihackerip, $amy_whitelist, $ah_admin_email;
    global $antihacker_Blocked_Firewall, $antihackerserver;
    if (ah_whitelisted($antihackerip, $amy_whitelist) or $antihacker_Blocked_Firewall <> 'yes') {
        return;
    }
    $subject = __("Detected Bot on ", "antihacker") . $antihackerserver;
    $message[] = __("Malicious bot was detected and blocked by firewall.", "antihacker");
    $message[] = "";
    $message[] = __('Date', 'antihacker') . "..............: " . date("F j, Y, g:i a");
    $message[] = __('Robot IP Address', 'antihacker') . "..: " . $antihackerip;
    $message[] = __('Malicious User Agent Found:', 'antihacker') . " " . $antihacker_string;
    $message[] = "";
    $message[] = __('eMail sent by Anti Hacker Plugin.', 'antihacker');
    $message[] = __(
        'You can stop emails at the Notifications Settings Tab.',
        'antihacker'
    );
    $message[] = __('Dashboard => Anti Hacker => Settings.', 'antihacker');
    $message[] = "";
    $msg = join("\n", $message);
    mail($ah_admin_email, $subject, $msg);
    return;
}
function antihacker_alertme5()
{
    global $antihackerip, $amy_whitelist, $ah_admin_email;
    global $antihacker_Blocked_Firewall, $antihackerserver;
    global $antihacker_current_url;
    $subject = __("Detected Bot on ", "antihacker") . $antihackerserver;
    $message[] = __("Malicious bot was detected and blocked Looking for Plugin Vulnerabilities.", "antihacker");
    $message[] = "";
    $message[] = __('Date', 'antihacker') . "..............: " . date("F j, Y, g:i a");
    $message[] = __('Robot IP Address', 'antihacker') . "..: " . $antihackerip;
    $message[] = "";
    $message[] = __('URL requested:', 'antihacker') . " " . $antihacker_current_url;
    $message[] = "";
    $message[] = __('eMail sent by Anti Hacker Plugin.', 'antihacker');
    $message[] = __(
        'You can stop emails at the Notifications Settings Tab.',
        'antihacker'
    );
    $message[] = __('Dashboard => Anti Hacker => Settings.', 'antihacker');
    $message[] = "";
    $msg = join("\n", $message);
    mail($ah_admin_email, $subject, $msg);
    return;
}
function antihacker_alertme6()
{
    global $antihackerip, $amy_whitelist, $ah_admin_email;
    global $antihacker_Blocked_Firewall, $antihackerserver;
    global  $antihacker_current_url;
    $subject = __("Detected Bot on ", "antihacker") . $antihackerserver;
    $message[] = __("Malicious bot was detected and blocked Looking for Theme Vulnerabilities.", "antihacker");
    $message[] = "";
    $message[] = __('Date', 'antihacker') . "..............: " . date("F j, Y, g:i a");
    $message[] = __('Robot IP Address', 'antihacker') . "..: " . $antihackerip;
    $message[] = "";
    $message[] = __('URL requested:', 'antihacker') . " " . $antihacker_current_url;
    $message[] = "";
    $message[] = __('eMail sent by Anti Hacker Plugin.', 'antihacker');
    $message[] = __(
        'You can stop emails at the Notifications Settings Tab.',
        'antihacker'
    );
    $message[] = __('Dashboard => Anti Hacker => Settings.', 'antihacker');
    $message[] = "";
    $msg = join("\n", $message);
    mail($ah_admin_email, $subject, $msg);
    return;
}
function antihacker_alertme7()
{
    global $antihackerip, $amy_whitelist, $ah_admin_email;
    global $antihacker_Blocked_Firewall, $antihackerserver;
    global $antihacker_ua;
    $subject = __("Detected Bot on ", "antihacker") . $antihackerserver;
    $message[] = __("Malicious bot was detected and blocked because is false Search Engine Google/Bing-Microsoft/Slurp.", "antihacker");
    $message[] = "";
    $message[] = __('Date', 'antihacker') . "..............: " . date("F j, Y, g:i a");
    $message[] = __('Robot IP Address', 'antihacker') . "..: " . $antihackerip;
    $message[] = "";
    $message[] = __('User Agent:', 'antihacker') . " " . $antihacker_ua;
    $message[] = "";
    $message[] = __('eMail sent by Anti Hacker Plugin.', 'antihacker');
    $message[] = __(
        'You can stop emails at the Notifications Settings Tab.',
        'antihacker'
    );
    $message[] = __('Dashboard => Anti Hacker => Settings.', 'antihacker');
    $message[] = "";
    $msg = join("\n", $message);
    mail($ah_admin_email, $subject, $msg);
    return;
}
function antihacker_alertme8()
{
    global $antihackerip, $amy_whitelist, $ah_admin_email;
    global $antihacker_Blocked_Firewall, $antihackerserver;
    global $antihacker_ua;
    global  $antihacker_current_url;
    $subject = __("Detected Bot on ", "antihacker") . $antihackerserver;
    $message[] = __("Malicious bot was detected and blocked because previously it is looking for vulnerabilities in this site.", "antihacker");
    $message[] = "";
    $message[] = __('Date', 'antihacker') . "..............: " . date("F j, Y, g:i a");
    $message[] = __('Robot IP Address', 'antihacker') . "..: " . $antihackerip;
    $message[] = "";
    $message[] = __('URL requested:', 'antihacker') . " " . $antihacker_current_url;
    $message[] = "";
    $message[] = __('User Agent:', 'antihacker') . " " . $antihacker_ua;
    $message[] = "";
    $message[] = __('eMail sent by Anti Hacker Plugin.', 'antihacker');
    $message[] = __(
        'You can stop emails at the Notifications Settings Tab.',
        'antihacker'
    );
    $message[] = __('Dashboard => Anti Hacker => Settings.', 'antihacker');
    $message[] = "";
    $msg = join("\n", $message);
    mail($ah_admin_email, $subject, $msg);
    return;
}
function antihacker_change_note_submenu_order($menu_ord)
{
    global $submenu;
    function antihacker_str_replace_json($search, $replace, $subject)
    {
        return json_decode(str_replace($search, $replace, json_encode($subject)), true);
    }
    $key = 'Anti Hacker';
    $val = 'Dashboard';
    $submenu = antihacker_str_replace_json($key, $val, $submenu);
}
add_filter('custom_menu_order', 'antihacker_change_note_submenu_order');
function antihacker_populate_stats()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_stats";
    $query = "SELECT * FROM $table_name";
    $wpdb->query($query);
    if ($wpdb->num_rows > 359)
      return;
    for ($i = 01; $i < 13; $i++) {
        for ($k = 01; $k < 32; $k++) {
            // insert in table iikk
            //$intval = (int) $string;
            //$string = (string) $intval;
            $year = 2020;
            if (!checkdate($i, $k,  $year))
                continue;
            $mdata = (string) $i;
            if (strlen($mdata) < 2)
                $mdata = '0' . $mdata;
            $ddata = (string) $k;
            if (strlen($ddata) < 2)
                $ddata = '0' . $ddata;
            $data = $mdata . $ddata;
            $query = "select *  from " . $table_name . " WHERE date = '" . $data .
                "' LIMIT 1";
           // if ($wpdb->get_var($query) > 0)
          //     continue;
            $wpdb->query($query);
                if ($wpdb->num_rows > 0)
                  continue;
            $query = "INSERT INTO " . $table_name .
                " (date)
                  VALUES ('" . $data . "')";
            $r = $wpdb->get_results($query);
        }
    }
}
function antihacker_stats_moreone($qtype)
{
    global $wpdb;
    // $qtype = qlogin or qfire
    if ($qtype != "qlogin" and $qtype != "qfire" and $qtype != "qenum")
        return;
    $qtoday = date("m") + date("d");
    $mdata = date("m");
    $ddata = date("d");
    $mdata = (string) $mdata;
    if (strlen($mdata) < 2)
        $mdata = '0' . $mdata;
    $ddata = (string) $ddata;
    if (strlen($ddata) < 2)
        $ddata = '0' . $ddata;
    $qtoday = $mdata . $ddata;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_stats";
    $query = "UPDATE " . $table_name .
        " SET " . $qtype . " = " . $qtype . " + 1, qtotal = qtotal+1 WHERE date = '" . $qtoday . "'";
    $wpdb->query($query);
}
function antihacker_create_db_stats()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // creates my_table in database if not exists
    $table = $wpdb->prefix . "ah_stats";
    global $wpdb;
    if (antihacker_tablexist($table))
        return;
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE " . $table . " (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `date` varchar(4) NOT NULL,
        `qlogin` text NOT NULL,
        `qfire` text NOT NULL,
        `qenum` text NOT NULL, 
        `qplugin` text NOT NULL, 
        `qtema` text NOT NULL, 
        `qfalseg` text NOT NULL, 
        `qblack` text NOT NULL, 
        `qtotal` varchar(100) NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`date`)
    ) $charset_collate;";
    dbDelta($sql);
}
function antihacker_create_db_blocked()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // creates my_table in database if not exists
    $table = $wpdb->prefix . "ah_blockeds";
    global $wpdb;
    if (antihacker_tablexist($table))
        return;
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE " . $table . " (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `ip` varchar(30) NOT NULL,
    UNIQUE (`id`),
    UNIQUE (`ip`)
    // UNIQUE (`date`)
    ) $charset_collate;";
    dbDelta($sql);

 //   $sql = "CREATE INDEX ip2 ON ". $table . " (ip)";
 //   dbDelta($sql);
    
}
function antihacker_create_db_visitors()
{
    // CREATE INDEX ip3 ON wp_ah_visitorslog2 (ip)
    //CREATE INDEX index_name ON table_name (column_list)
    /*
    ALTER TABLE `wp_ah_visitorslog2`
    ADD UNIQUE KEY `id` (`id`),
    ADD UNIQUE KEY `ip` (`ip`),
    ADD UNIQUE KEY `date` (`date`),
    ADD KEY `ip3` (`ip`);
    */
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    // creates my_table in database if not exists
    $table = $wpdb->prefix . "ah_visitorslog";
    global $wpdb;
    if (antihacker_tablexist($table))
        return;
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE " . $table . " (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `ip` varchar(30) NOT NULL,
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `cookie` varchar(1) NOT NULL,
        `response` varchar(5) NOT NULL,
    UNIQUE (`id`),
    ) $charset_collate;";
    dbDelta($sql);
    $sql = "CREATE INDEX ip2 ON ". $table . " (ip)";
    dbDelta($sql);
}
function ah_activated()
{
    ob_start();
    global $my_whitelist;
    global $ah_admin_email;
    antihacker_create_db_stats();
    antihacker_create_db_blocked();
    antihacker_create_db_visitors();
    antihacker_populate_stats();
    add_option('ah_was_activated', '1');
    update_option('ah_was_activated', '1');
    $antihackerip = ahfindip();
    if (is_admin()) {
        if (empty($my_whitelist)) {
            if (get_site_option('my_whitelist') !== false) {
                $return = update_site_option('my_whitelist', $antihackerip);
            } else {
                $return = add_site_option('my_whitelist', $antihackerip);
            }
        }
    }
    $antihacker_installed = trim(get_option('antihacker_installed', ''));
    if (empty($antihacker_installed)) {
        add_option('antihacker_installed', time());
        update_option('antihacker_installed', time());
    }
    ob_end_clean();
}
function antihacker_response()
{
    if (is_admin() or is_super_admin() ) 
      return;
    header('HTTP/1.1 403 Forbidden');
    header('Status: 403 Forbidden');
    header('Connection: Close');
    exit();
}
function antihacker_tablexist($table)
{
    global $wpdb;
    $table_name = $table;
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name)
        return true;
    else
        return false;
}
function antihacker_check_memory()
{
    global $antihacker_memory;
    $antihacker_memory['limit'] = (int) ini_get('memory_limit');
    $antihacker_memory['usage'] = function_exists('memory_get_usage') ? round(memory_get_usage() / 1024 / 1024, 0) : 0;
    if (!defined("WP_MEMORY_LIMIT")) {
        $antihacker_memory['msg_type'] = 'notok';
        return;
    }
    $antihacker_memory['wp_limit'] =  trim(WP_MEMORY_LIMIT);
    if ($antihacker_memory['wp_limit'] > 9999999)
        $antihacker_memory['wp_limit'] = ($antihacker_memory['wp_limit'] / 1024) / 1024;
    if (!is_numeric($antihacker_memory['usage'])) {
        $antihacker_memory['msg_type'] = 'notok';
        return;
    }
    if (!is_numeric($antihacker_memory['limit'])) {
        $antihacker_memory['msg_type'] = 'notok';
        return;
    }
    if ($antihacker_memory['usage'] < 1) {
        $antihacker_memory['msg_type'] = 'notok';
        return;
    }
    $wplimit = $antihacker_memory['wp_limit'];
    $wplimit = substr($wplimit, 0, strlen($wplimit) - 1);
    $antihacker_memory['wp_limit'] = $wplimit;
    $antihacker_memory['percent'] = $antihacker_memory['usage'] / $antihacker_memory['wp_limit'];
    $antihacker_memory['color'] = 'font-weight:normal;';
    if ($antihacker_memory['percent'] > .7) $antihacker_memory['color'] = 'font-weight:bold;color:#E66F00';
    if ($antihacker_memory['percent'] > .85) $antihacker_memory['color'] = 'font-weight:bold;color:red';
    $antihacker_memory['msg_type'] = 'ok';
    return $antihacker_memory;
}
function anti_hacker_message_low_memory()
{
    echo '<div class="notice notice-warning">
                     <br />
                     <b>
                     Anti Hacker Plugin Warning: You need increase the WordPress memory limit!
                     <br />
                     Please, check 
                     <br />
                     Dashboard => Anti Hacker => (tab) Memory Checkup
                     <br /><br />
                     </b>
                     </div>';
}
function anti_hacker_control_availablememory()
{
    $anti_hacker_memory = antihacker_check_memory();
    if ($anti_hacker_memory['msg_type'] == 'notok')
        return;
    if ($anti_hacker_memory['percent'] > .7)
        add_action('admin_notices', 'anti_hacker_message_low_memory');
}
add_action('wp_loaded', 'anti_hacker_control_availablememory');
function antihacker_find_perc()
{
    global $antihacker_checkversion;
    $antihacker_option_name[] = 'my_radio_xml_rpc';
    $antihacker_option_name[] = 'antihacker_rest_api';
    $antihacker_option_name[] = 'antihacker_automatic_plugins';
    $antihacker_option_name[] = 'antihacker_automatic_themes';
    $antihacker_option_name[] = 'antihacker_replace_login_error_msg';
    $antihacker_option_name[] = 'antihacker_disallow_file_edit';
    $antihacker_option_name[] = 'antihacker_debug_is_true';
    $antihacker_option_name[] = 'antihacker_firewall';
    $antihacker_option_name[] = 'antihacker_hide_wp';
    $antihacker_option_name[] = 'antihacker_block_enumeration';
    $antihacker_option_name[] = 'antihacker_block_all_feeds';
    $antihacker_option_name[] = 'antihacker_new_user_subscriber';
    $antihacker_option_name[] = 'antihacker_block_falsegoogle';
    $antihacker_option_name[] = 'antihacker_block_search_plugins';
    $antihacker_option_name[] = 'antihacker_block_search_themes';
    $perc = 1;
    $wnum = count($antihacker_option_name);
    for ($i = 0; $i < $wnum; $i++) {
        $yes_or_not = trim(sanitize_text_field(get_site_option($antihacker_option_name[$i], '')));
        if (strtoupper($yes_or_not) == 'YES')
            $perc = $perc + (10 / ($wnum + 1));
    }
    if(empty($antihacker_checkversion) and $perc > 7)
      $perc = 7;
    $perc = round($perc, 0, PHP_ROUND_HALF_UP);
    if ($perc > 10)
        $perc = 10;
    return $perc;
}
function antihacker_filter_rest_endpoints($endpoints)
{
    global $antihacker_Blocked_userenum_email;
    ///wp-json/contact-form-7/v1/contact-forms/571/feedback
    $workurl = esc_url($_SERVER['REQUEST_URI']);
    if (stripos($workurl, 'contact-form-7') !== false)
        return $endpoints;
    if (isset($endpoints['/wp/v2/users'])) {
        unset($endpoints['/wp/v2/users']);
        if ($antihacker_Blocked_userenum_email == 'yes')
            ah_user_enumeration_email();
        antihacker_stats_moreone('qenum');
        antihacker_gravalog();
        antihacker_response();
    }
    if (isset($endpoints['/wp/v2/users/(?P<id>[\d]+)'])) {
        unset($endpoints['/wp/v2/users/(?P<id>[\d]+)']);
        if ($antihacker_Blocked_userenum_email == 'yes')
            ah_user_enumeration_email();
        antihacker_stats_moreone('qenum');
        antihacker_gravalog();
        antihacker_response();
    }
    if (isset($endpoints['/wp/v2/posts'])) {
        unset($endpoints['/wp/v2/posts']);
        if ($antihacker_Blocked_userenum_email == 'yes')
            ah_user_enumeration_email();
        antihacker_stats_moreone('qenum');
        antihacker_gravalog();
        antihacker_response();
    }
    return $endpoints;
}
function antihacker_block_enumeration()
{
    global $antihacker_Blocked_userenum;
    if (isset($_SERVER['REQUEST_URI'])) {
        if (!preg_match('/(wp-comments-post)/', $_SERVER['REQUEST_URI']) && !empty($_REQUEST['author']) && (int) $_REQUEST['author']) { {
                if ($antihacker_Blocked_userenum == 'yes')
                    ah_user_enumeration_email();
                antihacker_stats_moreone('qenum');
                antihacker_gravalog();
                antihacker_response();
            }
        }
    }
}
function antihacker_remove_index()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_visitorslog";
    if (! $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name)
        return;
    $query = 'show index from '.$table_name;
    $result = $wpdb->get_results($query);
    $result = json_decode(json_encode($result), true);
    foreach ($result as $results) {
        if ($results['Column_name'] == 'ip') {
            $query = "ALTER TABLE " . $table_name . " DROP INDEX ip";
            ob_start();
            $wpdb->query($query);
            ob_end_clean();
        }
        if ($results['Column_name'] == 'date') {
            $query = "ALTER TABLE " . $table_name . " DROP INDEX date";
            ob_start();
            $wpdb->query($query);
            ob_end_clean();
        }
    }
}
function antihacker_add_index()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_visitorslog";
    if (! $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name)
        return;
    $query = 'show index from '.$table_name;
    $result = $wpdb->get_results($query);
    $result = json_decode(json_encode($result), true);
    foreach ($result as $results) {
        if ($results['Column_name'] == 'ip2') {
           return;
        }
    }
    $sql = "CREATE INDEX ip2 ON ". $table . " (ip)";
    dbDelta($sql);

}
function antihacker_upgrade_db()
{
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $table_name = $wpdb->prefix . "ah_stats";
    if (!antihacker_tablexist($table_name))
        return;
    $query = "SHOW COLUMNS FROM " . $table_name . " LIKE 'qenum'";
    $wpdb->query($query);
    if (empty($wpdb->num_rows)) {
        $alter = "ALTER TABLE " . $table_name . " ADD qenum text NOT NULL";
        ob_start();
        $wpdb->query($alter);
        ob_end_clean();
    }
    $query = "SHOW COLUMNS FROM " . $table_name . " LIKE 'qplugin'";
    $wpdb->query($query);
    if (empty($wpdb->num_rows)) {
        $alter = "ALTER TABLE " . $table_name . " ADD qplugin text NOT NULL";
        ob_start();
        $wpdb->query($alter);
        ob_end_clean();
    }
    $query = "SHOW COLUMNS FROM " . $table_name . " LIKE 'qtema'";
    $wpdb->query($query);
    if (empty($wpdb->num_rows)) {
        $alter = "ALTER TABLE " . $table_name . " ADD qtema text NOT NULL";
        ob_start();
        $wpdb->query($alter);
        ob_end_clean();
    }
    $query = "SHOW COLUMNS FROM " . $table_name . " LIKE 'qfalseg'";
    $wpdb->query($query);
    if (empty($wpdb->num_rows)) {
        $alter = "ALTER TABLE " . $table_name . " ADD qfalseg text NOT NULL";
        ob_start();
        $wpdb->query($alter);
        ob_end_clean();
    }
    $query = "SHOW COLUMNS FROM " . $table_name . " LIKE 'qblack'";
    $wpdb->query($query);
    if (empty($wpdb->num_rows)) {
        $alter = "ALTER TABLE " . $table_name . " ADD qblack text NOT NULL";
        ob_start();
        $wpdb->query($alter);
        ob_end_clean();
    }
   // $sql = "CREATE INDEX ip2 ON ". $table . " (ip)";
   // dbDelta($sql);
}
function anti_hacker_dangerous_file()
{
    global $ah_dangerous_file;
    echo '<div class="notice notice-warning is-dismissible">';
    echo '<br /><b>';
    echo __('Message from Anti Hacker Plugin', 'antihacker');
    echo ':</b><br />';
    echo __('Looks like you have this file in your server', 'antihacker') . ': ' . $ah_dangerous_file;
    echo '.<br />';
    echo __('We suggest you to remove it ASAP.', 'antihacker');
    echo '<br /><br /></div>';
} ?>