<?php /*
Plugin Name: AntiHacker 
Plugin URI: http://antihackerplugin.com
Description: Improve security and prevent unauthorized access by restrict access to login to whitelisted IP, Firewall and much more.
Version: 2.51
Text Domain: antihacker
Domain Path: /language
Author: Bill Minozzi
Author URI: http://billminozzi.com
License:     GPL2
Copyright (c) 2015-2019 Bill Minozzi
Antihacker is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
Antihacker is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with Antihacker. If not, see {License URI}.
Permission is hereby granted, free of charge subject to the following conditions:
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/
//ob_start();
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
define('ANTIHACKERVERSION', '2.51' );
define('ANTIHACKERPATH', plugin_dir_path(__file__) );
define('ANTIHACKERURL', plugin_dir_url(__file__));
$antihackerserver = sanitize_text_field($_SERVER['SERVER_NAME']);
define('ANTIHACKERIMAGES', plugin_dir_url(__file__).'images');
define('ANTIHACKERHOMEURL',admin_url());
$antihacker_current_url = esc_url($_SERVER['REQUEST_URI']);
// Add settings link on plugin page
function antihacker_plugin_settings_link($links) { 
  // $settings_link = '<a href="options-general.php?page=anti-hacker">Settings</a>'; 
  $settings_link = '<a href="admin.php?page=anti-hacker">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'antihacker_plugin_settings_link' );
/* Begin Language */
if(is_admin())
    {
        function ah_localization_init_fail()
        {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<br />';
            echo __('Anti Hacker Plugin: Could not load the localization file (Language file)','antihacker');
            echo '.<br />';
            echo __('Please, take a look in our site, FAQ page, item => How can i translate this plugin?', 'antihacker');
            echo '<br /><br /></div>';
        }
      if (isset($_GET['page'])) {
        $page = sanitize_text_field($_GET['page']);
        if ($page == 'anti-hacker') 
        {
                  $path = dirname(plugin_basename( __FILE__ )) . '/language/';
                  $loaded = load_plugin_textdomain( 'antihacker', false, $path);
                  if (!$loaded AND get_locale() <> 'en_US') { 
                       //if( function_exists('ah_localization_init_fail'))
                        // add_action( 'admin_notices', 'ah_localization_init_fail' );
                  }
              }
        }
    } 
else
    {
         add_action( 'plugins_loaded', 'ah_localization_init' );
    }
function ah_localization_init() {
    $path = dirname(plugin_basename( __FILE__ )) . '/language/';
    $loaded = load_plugin_textdomain( 'antihacker', false, $path);
}
/* End language */
require_once (ANTIHACKERPATH . "settings/load-plugin.php");
if (is_admin()) {
    function antihacker_add_admstylesheet()
    {
        wp_enqueue_script('flot', ANTIHACKERURL .
            'js/jquery.flot.min.js', array('jquery'));

        wp_enqueue_script('flotpie', ANTIHACKERURL .
            'js/jquery.flot.pie.js', array('jquery'));

    }
    add_action('admin_enqueue_scripts', 'antihacker_add_admstylesheet', 1000);
}

$my_whitelist = trim(sanitize_text_field(get_site_option('my_whitelist','')));
$amy_whitelist = explode(" ", $my_whitelist);
$ah_admin_email = trim(sanitize_text_field(get_option( 'my_email_to' ))); 
$my_radio_all_logins =  sanitize_text_field(get_site_option('my_radio_all_logins', 'No')); // Alert me All Logins
$my_checkbox_all_failed =  sanitize_text_field(get_site_option('my_checkbox_all_failed', '0')); // Alert me all Failed Login Attempts
$anti_hacker_firewall = sanitize_text_field(get_option('antihacker_firewall','yes'));
$antihacker_Blocked_Firewall = sanitize_text_field(get_option('antihacker_Blocked_Firewall','no'));
$antihacker_hide_wp = sanitize_text_field(get_option('antihacker_hide_wp','yes'));
$antihacker_block_enumeration = sanitize_text_field(get_option('antihacker_block_enumeration','no'));
$antihacker_version = trim(sanitize_text_field(get_site_option('antihacker_version', '')));
$antihacker_Blocked_else_email = trim(sanitize_text_field(get_site_option('antihacker_Blocked_else', 'no')));
$antihacker_block_all_feeds = trim(sanitize_text_field(get_site_option('antihacker_block_all_feeds', 'no')));
$antihacker_new_user_subscriber = trim(sanitize_text_field(get_site_option('antihacker_new_user_subscriber', 'no')));
$antihacker_block_falsegoogle = sanitize_text_field(get_option('antihacker_block_falsegoogle','yes'));
$antihacker_checkversion = trim(sanitize_text_field(get_option('antihacker_checkversion','')));
$antihacker_block_false_google = trim(sanitize_text_field(get_site_option('antihacker_block_false_google', 'no')));
$antihacker_block_search_plugins = trim(sanitize_text_field(get_site_option('antihacker_block_search_plugins', 'no')));
$antihacker_block_search_themes = trim(sanitize_text_field(get_site_option('antihacker_block_search_themes', 'no')));
if(!empty($antihacker_checkversion))
{
  $antihacker_block_false_google = trim(sanitize_text_field(get_site_option('antihacker_block_false_google', 'no')));
  $antihacker_block_search_plugins = trim(sanitize_text_field(get_site_option('antihacker_block_search_plugins', 'no')));
  $antihacker_block_search_themes = trim(sanitize_text_field(get_site_option('antihacker_block_search_themes', 'no')));
}
else{
  $antihacker_block_false_google = 'no';
  $antihacker_block_search_plugins = 'no';
  $antihacker_block_search_themes = 'no';
}
require_once (ANTIHACKERPATH . "includes/functions/functions.php");
if(!empty($_POST["myemail"]))
  {$myemail = sanitize_text_field($_POST["myemail"]);}
else
  {$myemail = '';}
require_once (ANTIHACKERPATH . 'dashboard/main.php');
require_once (ANTIHACKERPATH . "settings/options/plugin_options_tabbed.php");
if(is_admin())
require_once (ANTIHACKERPATH . 'includes/functions/health.php');
$ah_admin_email = trim(sanitize_text_field(get_option( 'my_email_to' )));
if( ! empty($ah_admin_email)) {
    if ( ! is_email($ah_admin_email)) {
        $ah_admin_email = '';
        update_option('my_email_to', '');
    }
}
if(empty($ah_admin_email))
     $ah_admin_email = sanitize_email(get_option( 'admin_email' )); 
// Firewall
if( ! is_admin()) 
 { 
    if( $anti_hacker_firewall != 'no')
     {
    	$antihacker_request_uri_array  = array('@eval', 'eval\(', 'UNION(.*)SELECT', '\(null\)', 'base64_', '\/localhost', '\%2Flocalhost', '\/pingserver', 'wp-config\.php', '\/config\.', '\/wwwroot', '\/makefile', 'crossdomain\.', 'proc\/self\/environ', 'usr\/bin\/perl', 'var\/lib\/php', 'etc\/passwd', '\/https\:', '\/http\:', '\/ftp\:', '\/file\:', '\/php\:', '\/cgi\/', '\.cgi', '\.cmd', '\.bat', '\.exe', '\.sql', '\.ini', '\.dll', '\.htacc', '\.htpas', '\.pass', '\.asp', '\.jsp', '\.bash', '\/\.git', '\/\.svn', ' ', '\<', '\>', '\/\=', '\.\.\.', '\+\+\+', '@@', '\/&&', '\/Nt\.', '\;Nt\.', '\=Nt\.', '\,Nt\.', '\.exec\(', '\)\.html\(', '\{x\.html\(', '\(function\(', '\.php\([0-9]+\)', '(benchmark|sleep)(\s|%20)*\(', 'indoxploi', 'xrumer');
    	$antihacker_query_string_array = array('@@', '\(0x', '0x3c62723e', '\;\!--\=', '\(\)\}', '\:\;\}\;', '\.\.\/', '127\.0\.0\.1', 'UNION(.*)SELECT', '@eval', 'eval\(', 'base64_', 'localhost', 'loopback', '\%0A', '\%0D', '\%00', '\%2e\%2e', 'allow_url_include', 'auto_prepend_file', 'disable_functions', 'input_file', 'execute', 'file_get_contents', 'mosconfig', 'open_basedir', '(benchmark|sleep)(\s|%20)*\(', 'phpinfo\(', 'shell_exec\(', '\/wwwroot', '\/makefile', 'path\=\.', 'mod\=\.', 'wp-config\.php', '\/config\.', '\$_session', '\$_request', '\$_env', '\$_server', '\$_post', '\$_get', 'indoxploi', 'xrumer');
        $antihacker_user_agent_array   = array('drivermysqli', 'acapbot', '\/bin\/bash', 'binlar', 'casper', 'cmswor', 'diavol', 'dotbot', 'finder', 'flicky', 'md5sum', 'morfeus', 'nutch', 'planet', 'purebot', 'pycurl', 'semalt', 'shellshock', 'skygrid', 'snoopy', 'sucker', 'turnit', 'vikspi', 'zmeu');
    	$antihacker_request_uri_string  = false;
    	$antihacker_query_string_string = false;
    	if (isset($_SERVER['REQUEST_URI'])     && !empty($_SERVER['REQUEST_URI']))     $antihacker_request_uri_string  = $_SERVER['REQUEST_URI'];
    	if (isset($_SERVER['QUERY_STRING'])    && !empty($_SERVER['QUERY_STRING']))    $antihacker_query_string_string = $_SERVER['QUERY_STRING'];
    	if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) $antihacker_user_agent_string   = $_SERVER['HTTP_USER_AGENT'];
        if ($antihacker_request_uri_string || $antihacker_query_string_string || $antihacker_user_agent_screen_string) {
        	 if (
        			preg_match('/'. implode('|', $antihacker_request_uri_array)  .'/i', $antihacker_request_uri_string, $matches)  || 
                 	preg_match('/'. implode('|', $antihacker_query_string_array) .'/i', $antihacker_query_string_string, $matches2) ||
        			preg_match('/'. implode('|', $antihacker_user_agent_array)   .'/i', $antihacker_user_agent_string,$matches3) 
        	  ) {
                    if( $antihacker_Blocked_Firewall == 'yes')
                    {   
                        if(isset($matches))
                         {
                           if (is_array($matches))
                             {
                               if(count($matches) > 0)
                               {
                                 antihacker_alertme3($matches[0]);
                               }
                             }
                         }
                         if(isset($matches2))
                         {
                           if (is_array($matches2))
                             {
                               if(count($matches2) > 0)
                                  antihacker_alertme3($matches2[0]);
                             }
                         }
                         if(isset($matches3))
                         {
                           if (is_array($matches3))
                             {
                               if(count($matches3) > 0)
                                  antihacker_alertme4($matches3[0]);
                             }
                         }
                     }
                     antihacker_stats_moreone('qfire');
                     antihacker_response();
            } // Endif match...     
   	} // endif if ($antihacker_query_string_string || $user_agent_string) 
  	} // firewall <> no
} 
// End Firewall
if (! ah_whitelisted($antihackerip, $amy_whitelist)) {
     add_action('login_form', 'ah_email_display');
     add_action('wp_authenticate_user', 'ah_validate_email_field', 10, 2);
    function ah_validate_email_field($user, $password)
    {
        global $myemail;
        if (!is_email($myemail))
            return new WP_Error('wrong_email', 'Please, fill out the email field!');
        else
           {
                // The Query
                $user_query = new WP_User_Query( array ( 'orderby' => 'registered', 'order' => 'ASC' ) );
                // User Loop
                if ( ! empty( $user_query->results ) ) {
                	foreach ( $user_query->results as $user ) {
                        if(strtolower(trim($user->user_email)) == $myemail )
                                 return $user;
                	}
                } else {
                	// echo 'No users found.';
                }
                    return new WP_Error( 'wrong_email', 'email not found!');
           } 
            return $user;
    }
} /* endif if (! ah_whitelisted($antihackerip, $my_whitelist)) */
add_action('wp_login', 'ah_successful_login');
add_action('wp_login_failed', 'ah_failed_login');
register_deactivation_hook(__FILE__, 'ah_my_deactivation');
register_activation_hook( __FILE__, 'ah_activated' );
if (sanitize_text_field(get_site_option('antihacker_automatic_plugins', 'no')) == 'yes') 
  add_filter( 'auto_update_plugin', '__return_true' ); 
if (get_site_option('antihacker_automatic_themes', 'no') == 'yes')
  add_filter( 'auto_update_theme', '__return_true' );
if (sanitize_text_field(get_site_option('antihacker_replace_login_error_msg', 'no')) == 'yes') 
add_filter( 'login_errors', function( $error ) {
     return '<strong>'.__('Wrong Username or Password', 'antihacker') .'</strong>';
} );
if (sanitize_text_field(get_site_option('antihacker_disallow_file_edit', 'yes')) == 'yes') 
  {
    if( ! defined('DISALLOW_FILE_EDIT'))
       define('DISALLOW_FILE_EDIT', true);
  }
if (WP_DEBUG and get_site_option('antihacker_debug_is_true', 'yes') == 'yes')
     add_action( 'admin_notices', 'ah_debug_enabled' );
function antihacker_load_feedback()
{
    if(is_admin())
    {
       // ob_start();
        require_once (ANTIHACKERPATH . "includes/feedback/feedback.php");
        if( sanitize_text_field(get_option('bill_last_feedback', '')) != '1')
           require_once (ANTIHACKERPATH . "includes/feedback/feedback-last.php");
    }  // ob_end_clean();
}
add_action( 'wp_loaded', 'antihacker_load_feedback' );
function antihackerplugin_load_activate()
{
    if (is_admin()) {
        require_once (ANTIHACKERPATH . 'includes/feedback/activated-manager.php');
    }
}
add_action('in_admin_footer', 'antihackerplugin_load_activate'); 
add_action('admin_menu', 'antihacker_add_menu_items9');
function antihacker_custom_dashboard_help()
{
  global $antihacker_checkversion;
  $perc = antihacker_find_perc()*10;
  if ($perc < 71)
     $color = '#ff0000';
  else 
     $color = '#000000';
    echo '<img src="' . ANTIHACKERURL . '/images/logo.png" style="text-align:center; max-width: 300px;margin: 0px 0 auto;"  />';
    echo '<br />';
    if ($perc < 71)
    {
         echo '<img src="' . ANTIHACKERURL . '/images/unlock-icon-red-small.png" style="text-align:center; max-width: 20px;margin: 0px 0 auto;"  />';
         echo '<h2 style="margin-top: -39px; margin-left: 30px; color:'.$color.'; font-weight: bold;" >';
    }
    else
    {
        echo '<h2 style="margin-top: -10px; margin-left: update0px; color:'.$color.';">';
   } 
    echo 'Protection rate: '.$perc.'%';
    echo '</h2>';
    $site = ANTIHACKERHOMEURL . "admin.php?page=anti_hacker_plugin";
    echo  '<a href="' . $site . '">For details, visit the plugin dashboard</a>';
    echo '<br />';
    echo '<br />';
	  echo '<h3 style="font-size:18px; text-align:center;">Total Attacks Blocked Last 15 days</h3>';
    echo '<br />';

    echo '<div style="max-width: 100%;">';
    require_once ("dashboard/attacksgraph.php");
    echo '</div>';

	  echo '<br />';
    echo '<hr>';
    
    echo '<br />';
	  echo '<h3 style="font-size:18px; text-align:center;">Blocked Attacks by Type</h3>';
    echo '<br />';
    
	  echo '<br />';
	  require_once ("dashboard/attacksgraph_pie.php");
	  echo '<br />';


      echo '<hr>';  
	    echo '<br />';
    $bd_msg = '<a href="' . $site . '" class="button button-primary">Dashboard</a>';
    $bd_msg .= '<br /><br />';
    echo $bd_msg;
    echo "</p>";
}
function antihacker_add_dashboard_widgets()
{
    wp_add_dashboard_widget('antihacker-dashboard', 'Anti Hacker  Activities', 'antihacker_custom_dashboard_help', 'dashboardsbb', 'normal', 'high');
}
function anti_hacker_show_dashboard()
{
    global $wpdb;
    $table_name = $wpdb->prefix . "ah_stats";
    $query = "SELECT date,qtotal FROM " . $table_name;
    $results9 = $wpdb->get_results($query);
    $results8 = json_decode(json_encode($results9), true);
    unset($results9);
    $x = 0; 
    $d = 15;
    for ($i = $d ; $i > 0; $i--)
    {
        $timestamp = time();
        $tm = 86400 * ($x); // 60 * 60 * 24 = 86400 = 1 day in seconds
        $tm = $timestamp - $tm;
        $the_day = date("d", $tm);
        $this_month = date('m', $tm);
        $array30d[$x] = $this_month.$the_day ;
        $mykey = array_search(trim($array30d[$x]), array_column($results8, 'date'));
        if($mykey)
        {
            $awork = $results8[$mykey]['qtotal'];
            $array30[$x] = $awork;
        }
        else
          $array30[$x] = 0;
        $x++;
    }
  if(count($array30) > 1)
  {
    for ($i = 0; $i < count($array30); $i++)
    {
        if($array30[$i] > 0)
          {
             return true;
          }
    }
    return false;
  }
}
if( is_admin() and anti_hacker_show_dashboard() )
     add_action("wp_dashboard_setup", "antihacker_add_dashboard_widgets");
if ($antihacker_hide_wp == 'yes') 
   remove_action('wp_head', 'wp_generator');
if( is_admin() and anti_hacker_show_dashboard() )
       add_action("wp_dashboard_setup", "antihacker_add_dashboard_widgets");
  if ($antihacker_hide_wp == 'yes') 
     remove_action('wp_head', 'wp_generator');
  if (!is_admin() and $antihacker_block_enumeration == 'yes')
  {
      antihacker_block_enumeration(); 
      add_filter( 'rest_endpoints', 'antihacker_filter_rest_endpoints', 10, 1 ); 
  }
      // Dangerous files...
      $anti_hacker_dangerous_files = array ('wp-config.bak', 'wp-config.old', 'wp-config.txt');
      if(is_admin())
      {
        for ($i=0; $i < count($anti_hacker_dangerous_files); $i++)
        {
          $ah_dangerous_file =  ABSPATH. $anti_hacker_dangerous_files[$i];
          if(file_exists( $ah_dangerous_file))
          add_action('admin_notices', 'anti_hacker_dangerous_file');
          break;
        }
      }
      // End Dangerous ...
if($antihacker_block_all_feeds == 'yes')
      {
          function antihacker_disable_feed() {
              wp_die( __('No feed available,please visit our <a href="'. get_bloginfo('url') .'">homepage</a>!') );
              }
              add_action('do_feed', 'antihacker_disable_feed', 1);
              add_action('do_feed_rdf', 'antihacker_disable_feed', 1);
              add_action('do_feed_rss', 'antihacker_disable_feed', 1);
              add_action('do_feed_rss2', 'antihacker_disable_feed', 1);
              add_action('do_feed_atom', 'antihacker_disable_feed', 1);
              add_action('do_feed_rss2_comments', 'antihacker_disable_feed', 1);
              add_action('do_feed_atom_comments', 'antihacker_disable_feed', 1);
}
$antihacker_block_media_comments = trim(sanitize_text_field(get_site_option('antihacker_block_media_comments', 'yes')));
if($antihacker_block_media_comments == 'yes')
{
  function anti_hacker_filter_media_comment( $open, $post_id ) {
    $post = get_post( $post_id );
    if( $post->post_type == 'attachment' ) {
        return false;
    }
    return $open;
  }
add_filter( 'comments_open', 'anti_hacker_filter_media_comment', 10 , 2 ); 
add_action('template_redirect', 'antihacker_final_step'); }
if (!empty($antihacker_checkversion))
   antihacker_update();
if(antihacker_check_blocklist($antihackerip))
{
  if ($antihacker_Blocked_else_email == 'yes')
  {
      antihacker_alertme8();
  }
  antihacker_stats_moreone('qblack');
  antihacker_response();
}
$out = trim(ob_get_clean());
