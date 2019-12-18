<?php

/**
 * @author William Sergio Minozzi
 * @copyright 2017
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

  //  echo '<div style="float:right; margin-top:-120px; padding-bottom: 50px:">';
  //  echo '<img src="' . ANTIHACKERURL . '/images/no_cloud.png" style="text-align:center; max-width: 200px;margin: 20px 0 auto;"  />';
  //  echo '</div>';

?>

<div id="antihacker-steps3">
    <div class="antihacker-block-title">
        Anti Hacker Plugin Activated
    </div>
    <div class="antihacker-help-container1">
        <div class="antihacker-help-column antihacker-help-column-1">
            <h3>Memory Status</h3>
            <?php
            $ds = 256;
            $du = 60;
            $antihacker_memory = antihacker_check_memory();
            if ($antihacker_memory['msg_type'] == 'notok') {
                echo 'Unable to get your Memory Info';
            } else {
                $ds = $antihacker_memory['wp_limit'];
                $du = $antihacker_memory['usage'];
                if ($ds > 0)
                    $perc = number_format(100 * $du / $ds, 0);
                else
                    $perc = 0;
                if ($perc > 100)
                    $perc = 100;
                //die($perc);
                $color = '#e87d7d';
                $color = '#029E26';
                if ($perc > 50)
                    $color = '#e8cf7d';
                if ($perc > 70)
                    $color = '#ace97c';
                if ($perc > 50)
                    $color = '#F7D301';
                if ($perc > 70)
                    $color = '#ff0000';
                echo '<p><li style="max-width:50%;font-weight:bold;padding:5px 15px;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;background-color:#0073aa;margin-left:13px;color:white;">' .
                    'Memory Usage' . '<div style="border:1px solid #ccc;background:white;width:100%;margin:2px 5px 2px 0;padding:1px">' .
                    '<div style="width: ' . $perc . '%;background-color:' . $color .
                    ';height:6px"></div></div>' . $du . ' of ' . $ds . ' MB Usage' . '</li>'; ?>
                <br /> <br />
                For details, click the Memory Check Up Tab above.
                <br /> <br />
            <?php } ?>
        </div>
        <!-- "Column1">  -->


        <div class="antihacker-help-column antihacker-help-column-2">
            <?php
            $perc = antihacker_find_perc();
            // die($perc);
            if ($perc < 9) {
                $color = '#ff0000';
                //  echo '<img src="' . ANTIHACKERURL . '/images/unlock-icon-red-small.png" style="text-align:center; max-width: 20px;margin: 14px 0 auto;"  />';
                echo '<h3 style="margin-top: 20px; margin-left: 30px; color:' . $color . '; font-weight: bold;" >';
            } else {
                echo '<h3>';
            }
            $color = '#ff0000';
            if ($perc > 5)
                $color = '#e8cf7d'; //amarelo
            if ($perc > 7.5)
                $color = '#029E26'; // verde
            ?>
            Protection Status</h3>
            <?php
            echo '<p><li style="max-width:50%;font-weight:bold;padding:5px 15px;border-radius:4px;-moz-border-radius:4px;-webkit-border-radius:4px;background-color:#0073aa;margin-left:13px;color:white;">' .
                'Protection Level' .
                '<div style="border:1px solid #ccc;width:100%;background:white;margin:2px 5px 2px 0;padding:1px">' .
                '<div style="width: ' . ($perc * 10) . '%;background-color:' . $color .
                ';height:6px"></div></div>' . $perc . ' of 10  Protected' .
                '</li>'; ?>
            <br /> <br />
            <?php
            if ($perc == 10)
                echo 'Protection Enabled.';
            else
                echo 'Go to Anti Hacker Settings Page (General Settings Tab) and mark all with Yes.';
            ?>
            <br /> <br />
        </div> <!-- "columns 2">  -->


        
        <div class="antihacker-help-column antihacker-help-column-2">
 
       <?php
        if (!empty($antihacker_checkversion)) { ?>
                    <h3>Premium Protection Enabled</h3>
                    <br />
                   <?php $site = 'http://antihackerplugin.com'; ?>
                   <a href="<?php echo $site; ?>" class="button button-primary">Learn More</a>
                <?php } else {

                    echo '<center>';

                    echo '<img src="' . ANTIHACKERURL . '/images/unlock-icon-red-small.png" style="text-align:center; max-width: 40px;margin: 20px 0 auto;"  />';

                    echo '</center>';
                    ?>
                   <h3 style="color:red; margin-top:10px;">Only Partial Protection enabled!
                   </h3>
                   No matter if you are small or big. Hackers want to use your server to send spam, steal traffic and attack new computers.
        
                   <br />
                  <?php $site = 'http://antihackerplugin.com/premium/'; ?>
                  <a href="<?php echo $site; ?>" class="button button-primary">Learn More</a>
               <?php
                }
                ?>
                           
             <br /> <br /> 
        </div> 
        <!-- "Column 3">  -->
</div> <!-- "Container 1 " -->
</div> <!-- "antihacker-steps3"> -->
<div id="antihacker-services3">
    <!--
     <div class="antihacker-block-title">
      Help, Demo, Support, Troubleshooting:
    </div>
    -->
    <div class="antihacker-help-container1">
        <div class="antihacker-help-column antihacker-help-column-1">
            <img alt="aux" src="<?php echo ANTIHACKERURL ?>images/service_configuration.png" />
            <div class="bill-dashboard-titles">Start Up Guide and Settings</div>
            <br /><br />
            Just click Settings in the left menu (Anti Hacker).
            <br />
            Dashboard => Anti Hacker => Settings
            <br />
            <?php $site = ANTIHACKERADMURL . "admin.php?page=anti-hacker"; ?>
            <a href="<?php echo $site; ?>" class="button button-primary">Go</a>
            <br /><br />
        </div> <!-- "Column1">  -->
        <div class="antihacker-help-column antihacker-help-column-2">
            <img alt="aux" src="<?php echo ANTIHACKERURL ?>images/support.png" />
            <div class="bill-dashboard-titles">OnLine Guide, Support, Faq...</div>
            <br /><br />
            You will find our complete and updated OnLine guide, faqs page, link to support and more in our site.
            <br />
            <?php $site = 'http://antihackerplugin.com'; ?>
            <a href="<?php echo $site; ?>" class="button button-primary">Go</a>
        </div> <!-- "columns 2">  -->
        <div class="antihacker-help-column antihacker-help-column-3">
            <img alt="aux" src="<?php echo ANTIHACKERURL ?>images/system_health.png" />
            <div class="bill-dashboard-titles">Troubleshooting Guide</div>
            <br /><br />
            Use old WP version, Low memory, some plugin with Javascript error are some possible problems.
            <br />
            <a href="http://siterightaway.net/troubleshooting/" class="button button-primary">Troubleshooting Page</a>
        </div> <!-- "Column 3">  -->
    </div> <!-- "Container1 ">  -->
</div> <!-- "services"> -->

<div id="antihacker-services3">
    <div class="antihacker-help-container1">
        <div class="antihacker-help-2column antihacker-help-column-1">
          <h3>Total Attacks Blocked Last 15 days</h3>
          <?php require_once "attacksgraph.php";?>
        </div>
        <div class="antihacker-help-2column antihacker-help-column-2">
          <h3>Blocked Attacks by Type</h3>
          <?php require_once "attacksgraph_pie.php";?>
        </div>
    </div>
</div>

