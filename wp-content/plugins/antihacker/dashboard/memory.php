<?php
/**
 * @author William Sergio Minozzi
 * @copyright 2017
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly 

 ///////////// Fix Config ///////////////// 
$ANTIHACKERkey = urlencode(substr(NONCE_KEY,0,10));
$antihackermypath = ANTIHACKERURL.'/dashboard/fixconfig.php';
$antihackermyrestore = ANTIHACKERURL.'/public/restore-config.php?key='.$ANTIHACKERkey;
?>
    <div id="pluginfix-wpconfig" style="display: none;">
    <div class="bill-fix-antihacker-wrap" style="">
    <div class="pluginfix-message" style="">
    If your server allow us, we can try to fix your file wp-config.php to release more memory.
    <br />
    <strong>Please, copy the url blue below to safe place before to proceed.</strong>
    <br />  
    Use the url only to undo this operation if you've problem accessing your site after the update.
    <br />
    <br />
    After Copy the URL, click UPDATE to proceed or Cancel to abort.
    <br />   <br />
    <textarea rows="3" id="restore_wpconfig" name="restore_wpconfig" style="width:100%; color: blue;"><?php echo $antihackermyrestore;?></textarea>
    <textarea rows="6" id="feedback_wpconfig" name="feedback_wpconfig" style="width:100%; font-weight: bold;" ></textarea>
    <br /><br /> 			
                        <a href="#" class="button button-primary button-close-wpconfig"><?php _e("Update","kardealer");?></a>
                        <a href="#" class="button button-primary button-cancell-wpconfig"><?php _e("Cancel","kardealer");?></a>
                        <img alt="aux" src="/wp-admin/images/wpspin_light-2x.gif" id="bill-imagewait20" />
                        <input type="hidden" id="email" name="email" value="<?php echo $email;?>" />
                        <input type="hidden" id="url_config" name="url_config" value="<?php echo $antihackermypath;?>" />
                        <input type="hidden" id="ANTIHACKERURLkey" name="ANTIHACKERURLkey" value="<?php echo $ANTIHACKERURLkey;?>" />
                        <input type="hidden" id="server_memory" name="server_memory" value="<?php echo (int) ini_get('memory_limit')?>" />
    </div>
    </div>
</div>
<!-- ///////////// End Fix config /////////////////  -->

<?php    
$antihacker_memory = antihacker_check_memory();
echo '<div id="antihacker-memory-page">';
echo '<div class="antihacker-block-title">';
    if ( $antihacker_memory['msg_type'] == 'notok')
       {
        echo 'Unable to get your Memory Info';
        echo '</div>';
    }
    else
    {
echo 'Memory Info';
echo '</div>';
echo '<div id="memory-tab">';
echo '<br />';
if($antihacker_memory['msg_type']  == 'ok')
 $mb = 'MB';
else
 $mb = '';
echo 'Current memory WordPress Limit: ' . $antihacker_memory['wp_limit'] . $mb .
    '&nbsp;&nbsp;&nbsp;  |&nbsp;&nbsp;&nbsp;';
$perc = $antihacker_memory['usage'] / $antihacker_memory['wp_limit'];
if ($perc > .7)
   echo '<span style="'.$antihacker_memory['color'].';">';
echo 'Your usage now: ' . $antihacker_memory['usage'] .
    'MB &nbsp;&nbsp;&nbsp;';
if ($perc > .7)
   echo '</span>';    
echo '|&nbsp;&nbsp;&nbsp;   Total Server Memory: ' . $antihacker_memory['limit'] .
    'MB';
// echo 'Current memory WordPress Limit: '.$antihacker_memory['wp_limit'].$mb.'&nbsp;&nbsp;&nbsp;  |&nbsp;&nbsp;&nbsp;   Your usage: '.$antihacker_memory['usage'].'MB of '.$antihacker_memory['limit'];
   echo '<br />';    
   echo '<br />'; 
   echo '<br />';
?>  
   </strong>
<!-- <div id="memory-tab"> -->

<?php


///////////////////////////
// Fix it...
if (! is_bill_theme())
{
    $wplimit = $antihacker_memory['wp_limit'];
    if (!is_multisite() and $antihacker_memory['limit'] >= 128 and $wplimit < 128) {
        echo 'WordPress memory limit looks low. <br />We can try to fix and increase the memory with just one click:';
        echo '<br />';
        echo '<a href="#" id="pluginfix-wpconfig-button" class="button button-primary">Fix it Now!</a>';
        echo '<br />';
        echo '<br />';
        echo '<hr>';
        echo 'Follow this instructions to do it manually:';
        echo '<br />';
    }
}
//////////////////////////////
?>


    <br />
    To increase the WordPress memory limit, add this info to your file wp-config.php (located at root folder of your server)
    <br />
    (just copy and paste)
    <br />    <br />
<strong>    
define('WP_MEMORY_LIMIT', '128M');
</strong>
    <br />    <br />
    before this row:
    <br />
    /* That's all, stop editing! Happy blogging. */
    <br />
    <br />
    If you need more, just replace 128 with the new memory limit.
    <br /> 
    To increase your total server memory, talk with your hosting company.
    <br />   <br />
    <hr />
    <br />    
<strong>    How to Tell if Your Site Needs a Shot of more Memory:</strong>
        <br />    <br />
    If your site is behaving slowly, or pages fail to load, you 
    get random white screens of death or 500 
    internal server error you may need more memory. 
Several things consume memory, such as WordPress itself, the plugins installed, the 
theme you're using and the site content.
     <br />  
Basically, the more content and features you add to your site, 
the bigger your memory limit has to be.
if you're only running a small 
site with basic functions without a Page Builder and Theme 
Options (for example the native Twenty Sixteen). However, once 
you use a Premium WordPress theme and you start encountering 
unexpected issues, it may be time to adjust your memory limit 
to meet the standards for a modern WordPress installation.
     <br /> <br />    
    Increase the WP Memory Limit is a standard practice in 
WordPress and you find instructions also in the official 
WordPress documentation (Increasing memory allocated to PHP).
    <br /><br />
Here is the link:    
<br />
<a href="https://codex.wordpress.org/Editing_wp-config.php" target="_blank">https://codex.wordpress.org/Editing_wp-config.php</a>
<br /><br />
</div>
</div>
<?php
}
?>