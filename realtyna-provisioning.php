<?php
/*
    Plugin Name: Realtyna Provisioning
    Description: This is provisioning server of Realtyna products.
    Author: Realtyna
    Project Manager: howard@realtyna.com
    Version: 1.2.1
    Text Domain: realtyna-provisioning
    Domain Path: /i18n/languages
    Author URI: https://realtyna.com/
*/

// Initialize the RTPROV or not?!
$init = true;

// Check Minimum PHP version
if(version_compare(phpversion(), '5.3.10', '<'))
{
    $init = false;

    add_action('admin_notices', 'rtprov_admin_notice_php_min_version');
    function rtprov_admin_notice_php_min_version()
    {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo sprintf(__("%s needs at-least PHP 5.3.10 or higher while your server PHP version is %s. Please contact your host provider and ask them to upgrade PHP of your server or change your host provider completely.", 'realtyna-provisioning'), '<strong>Realtyna Provisioning</strong>', '<strong>'.phpversion().'</strong>'); ?></p>
        </div>
        <?php
    }
}

// Check Minimum WP version
global $wp_version;
if(version_compare($wp_version, '4.0.0', '<'))
{
    $init = false;

    add_action('admin_notices', 'rtprov_admin_notice_wp_min_version');
    function rtprov_admin_notice_wp_min_version()
    {
        global $wp_version;
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo sprintf(__("%s needs at-least WordPress 4.0.0 or higher while your WordPress version is %s. Please update your WordPress to latest version first.", 'realtyna-provisioning'), '<strong>Realtyna Provisioning</strong>', '<strong>'.$wp_version.'</strong>'); ?></p>
        </div>
        <?php
    }
}

// Check WPL Status
if(!class_exists('wpl_global'))
{
    $init = false;

    add_action('admin_notices', 'rtprov_admin_notice_wpl_activation');
    function rtprov_admin_notice_wpl_activation()
    {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo sprintf(__("%s plugin should be installed and activated otherwise you cannot use %s plugin.", 'realtyna-provisioning'), '<strong>WPL Real Estate</strong>', '<strong>Realtyna Provisioning</strong>'); ?></p>
        </div>
        <?php
    }
}

// Run the Realtyna Provisioning
if($init) require_once 'RTPROV.php';