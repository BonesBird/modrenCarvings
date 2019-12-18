<?php 
/**
 * @author William Sergio Minozzi
 * @copyright 2017
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly
// ob_start();
define('ANTIHACKERADMURL',admin_url());
$antihacker_urlsettings = ANTIHACKERADMURL."/admin.php?page=antihacker_settings33";
add_action( 'admin_init', 'antihacker_settings_init' );
add_action( 'admin_menu', 'antihacker_add_admin_menu' );

function antihacker_enqueue_scripts() {
      	wp_enqueue_style( 'bill-help-dashboard-ah' , ANTIHACKERURL.'/dashboard/css/help.css');

        if(! is_bill_theme())
             wp_register_script( 'fix-config-manager', ANTIHACKERURL.'/dashboard/js/fix-config-manager.js' , array( 'jquery' ), ANTIHACKERVERSION, true );
    }
add_action('admin_init', 'antihacker_enqueue_scripts');

function antihacker_add_admin_menu(  ) {
    Global $menu;
    add_menu_page(
    'Anti Hacker22', 
    'Anti Hacker', 
    'manage_options', 
    'anti_hacker_plugin', // slug 
    'antihacker_options_page', 
    ANTIHACKERIMAGES.'/protect.png' , 
     '100' 
    );
include_once(ABSPATH . 'wp-includes/pluggable.php');
$link_our_new_CPT = urlencode('edit.php?post_type=antihackerfields');
}
function antihacker_settings_init(  ) { 
	register_setting( 'antihacker', 'antihacker_settings' );
}
function antihacker_options_page(  ) { 
     global $activated, $antihacker_update_theme;
     global $anti_hacker_active; 
     global $anti_hacker_ip_active; 
     global $antihacker_checkversion;
     global $anti_hacker_firewall;
            $wpversion = get_bloginfo('version');
            $current_user = wp_get_current_user();
            $plugin = plugin_basename(__FILE__); 
            $email = $current_user->user_email;
            $username =  trim($current_user->user_firstname);
            $user = $current_user->user_login;
            $user_display = trim($current_user->display_name);
            if(empty($username))
               $username = $user;
            if(empty($username))
               $username = $user_display;
            $theme = wp_get_theme( );
            $themeversion = $theme->version ; 
  ?>

    <!-- Begin Page -->
<div id = "antihacker-theme-help-wrapper">   
     <div id="antihacker-not-activated"></div>

     <div id="antihacker_header">

        <div id="antihacker-logo">
        <img alt="logo" src="<?php echo ANTIHACKERIMAGES;?>/logo.png" width="250px" />
        </div>



        <div id="antihacker-nocloud">
        <img alt="No Cloud" src="<?php echo ANTIHACKERIMAGES;?>/no_cloud.png" width="200px" />
        </div>


        <div id="antihacker_help_title">
            Help and Support Page
        </div> 

       <div id="antihacker-social">
       <a href="http://antihackerplugin.com/share/"><img alt="social bar" src="<?php echo ANTIHACKERIMAGES;?>/social-bar.png" width="250px" /></a>
       </div>


     </div> 

 <?php
if( isset( $_GET[ 'tab' ] ) ) 
    $active_tab = sanitize_text_field($_GET[ 'tab' ]);
else
   $active_tab = 'dashboard';
?>
    <h2 class="nav-tab-wrapper">
    <a href="?page=anti_hacker_plugin&tab=memory&tab=dashboard" class="nav-tab">Dashboard</a>
    <a href="?page=anti_hacker_plugin&tab=memory" class="nav-tab">Memory Check Up</a>
    </h2>
<?php  
if($active_tab == 'memory') {     
   require_once (ANTIHACKERPATH . 'dashboard/memory.php');
} 
else
{ 
    require_once (ANTIHACKERPATH . 'dashboard/dashboard.php');
}
 echo '</div> <!-- "antihacker-theme_help-wrapper"> -->';
} // end Function antihacker_options_page
     require_once(ABSPATH . 'wp-admin/includes/screen.php');
// ob_end_clean();
include_once(ABSPATH . 'wp-includes/pluggable.php');

if(! function_exists('is_bill_theme'))
{
    function is_bill_theme()
    {
        $my_theme = wp_get_theme();
        $theme = trim($my_theme->get( 'Name' ));
       // die($theme);
        $mythemes = array (
        'boatdealer',
        'KarDealer',
        'verticalmenu',
        'fordummies',
        'Real Estate Right Now');
        // boatseller
    
        $count = count( $mythemes);
        $theme =  strtolower(trim($theme));

        for($i=0; $i < $count; $i++)
        {
        if ($theme == strtolower(trim($mythemes[$i])))
            return true;
        }
        return false;
    }
 }?>
