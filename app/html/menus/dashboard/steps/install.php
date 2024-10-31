<?php
// no direct access
defined('ABSPATH') or die();

$package_id = isset($_GET['install']) ? sanitize_text_field($_GET['install']) : 0;
$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : NULL;

$API = new RTPROV_Api();
$response = $API->package($package_id);

// Package
$package = isset($response['data']) ? $response['data'] : array();
?>
<div class="wrap about-wrap rtprov-wrap">
    <div class="rtprov-row">
        <div class="rtprov-col-9">
            <h1><?php _e('Realtyna Provisioning', 'realtyna-provisioning'); ?></h1>
        </div>
        <div class="rtprov-col-3 rtprov-text-right rtprov-pt-4">
            <?php echo $this->welcome(); ?>
        </div>
    </div>
    <div class="about-text">
        <?php _e("Let's install the package!", 'realtyna-provisioning'); ?>
    </div>
    <div class="rtprov-content">

        <?php if(!$package_id or !wp_verify_nonce($nonce, 'rtprov-install-'.$package_id)): ?>
        <div class="rtprov-mt-4 rtprov-mb-4 rtprov-alert-danger"><?php _e('Request is not valid so we cannot install the package!', 'realtyna-provisioning'); ?></div>
        <?php elseif(!count($package)): ?>
        <div class="rtprov-mt-4 rtprov-mb-4 rtprov-alert-danger"><?php _e('No package found with the given ID!', 'realtyna-provisioning'); ?></div>
        <?php else: ?>
        <div class="rtprov-install">
            <div class="rtprov-row">
                <div class="rtprov-col-12">
                    <a class="rtprov-bold" href="<?php echo $this->get_admin_url('realtyna-provisioning', array()); ?>"><?php _e('Back to Packages', 'realtyna-provisioning'); ?></a>
                </div>
            </div>
            <div class="rtprov-package-info rtprov-row">
                <div class="rtprov-col-10">
                    <h3><?php echo $package['name']; ?></h3>
                    <p><?php echo nl2br($package['description']); ?></p>
                </div>
                <div class="rtprov-col-2">
                    <h3 class="rtprov-type"><?php echo $package['type']['name']; ?></h3>
                </div>
            </div>
            <div class="rtprov-installation-form rtprov-mt-4 rtprov-text-right">
                <form id="rtprov_install_form">
                    <input type="hidden" name="id" value="<?php echo $package_id; ?>">
                    <?php wp_nonce_field('rtprov-install-do-'.$package_id); ?>
                    <button id="rtprov_install_button" type="submit" class="button-primary" data-reinstall="0"><?php _e('Install', 'realtyna-provisioning'); ?></button>
                </form>
            </div>
            <div class="rtprov-logs"></div>
        </div>
        <?php endif; ?>

    </div>
</div>
<script type="text/javascript">
jQuery('#rtprov_install_form').on('submit', function(event)
{
    event.preventDefault();

    var $button = jQuery('#rtprov_install_button');
    var reinstall = $button.data('reinstall');

    // Add a separator Log
    if(reinstall) rtprov_log('&nbsp;');

    // Log
    rtprov_log("<?php echo esc_js(__('Installation Started ....', 'realtyna-provisioning')); ?>");
    rtprov_log("<?php echo esc_js(__('Please do not navigate to other pages untill you see success or error message.', 'realtyna-provisioning')); ?>");
    rtprov_log("<?php echo esc_js(__("Now we're downloading the package ....", 'realtyna-provisioning')); ?>");

    // Disable the Button
    $button.attr('disabled', 'disabled').text("<?php echo esc_js(__('Installing ...', 'realtyna-provisioning')); ?>");

    var install = jQuery("#rtprov_install_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=rtprov_download&" + install,
        dataType: 'json',
        success: function(response)
        {
            // Show All Messages
            var i;
            for(i in response.messages)
            {
                var message = response.messages[i];
                rtprov_log(message.text, message.type);
            }

            if(response.success)
            {
                rtprov_install(response.path);
            }
            else
            {
                // Enable the button again
                jQuery('#rtprov_install_button').removeAttr('disabled').text("<?php echo esc_js(__('Install', 'realtyna-provisioning')); ?>");
            }
        },
        error: function()
        {
        }
    });
});

function rtprov_install(path)
{
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=rtprov_install&package=" + path + '&id=<?php echo $package_id; ?>&_wpnonce=<?php echo wp_create_nonce('rtprov-install-do-'.$package_id); ?>',
        dataType: 'json',
        success: function(response)
        {
            // Show All Messages
            var i;
            for(i in response.messages)
            {
                var message = response.messages[i];
                rtprov_log(message.text, message.type);
            }

            if(response.success)
            {
                // Enable Re-install Button
                jQuery('#rtprov_install_button').removeAttr('disabled').text("<?php echo esc_js(__('Re-install', 'realtyna-provisioning')); ?>").data('reinstall', 1);
            }
            else
            {
            }
        },
        error: function()
        {
        }
    });
}

function rtprov_log(message, type)
{
    if(typeof type === 'undefined') type = 'normal';
    jQuery('.rtprov-logs').prepend('<div class="rtprov-alert-'+type+' rtprov-mt-3">'+message+'</div>');
}
</script>