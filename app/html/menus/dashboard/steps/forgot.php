<?php
// no direct access
defined('ABSPATH') or die();
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
        <?php _e('Please insert your email to reset your password!', 'realtyna-provisioning'); ?>
    </div>
    <div class="rtprov-content">
        <div class="rtprov-row rtprov-forgot-wrapper rtprov-mb-4">
            <div class="rtprov-row">
                <a class="rtprov-bold" href="<?php echo $this->get_admin_url('realtyna-provisioning', array()); ?>"><?php _e('Back to Login / Register', 'realtyna-provisioning'); ?></a>
            </div>
            <div id="rtprov_forgot_password_form_wrapper" class="rtprov-col-12 rtprov-pr-4">
                <h2><?php _e('Reset Password', 'realtyna-provisioning'); ?></h2>
                <form id="rtprov_forgot_password_form" method="post">
                    <div class="rtprov-form-row">
                        <label for="rtprov_forgot_password_email"><?php _e('Email', 'realtyna-provisioning'); ?> <span class="required">*</span></label>
                        <input type="email" name="email" id="rtprov_forgot_password_email" required="required">
                        <?php wp_nonce_field('rtprov_forgot_password'); ?>
                        <input class="button-primary" type="submit" value="<?php _e('Send', 'realtyna-provisioning'); ?>">
                    </div>
                </form>
                <div class="rtprov-alert-danger rtprov-mt-2" id="rtprov-forgot-password-error"></div>
            </div>
            <div id="rtprov_reset_password_form_wrapper" class="rtprov-col-12 rtprov-pr-4" style="display: none;">
                <h2><?php _e('Reset Password', 'realtyna-provisioning'); ?></h2>
                <form id="rtprov_reset_password_form" method="post">
                    <div class="rtprov-form-row">
                        <label for="rtprov_reset_password_token"><?php _e('Security Code', 'realtyna-provisioning'); ?> <span class="required">*</span></label>
                        <input type="text" name="token" id="rtprov_reset_password_token" class="widefat" required="required">
                    </div>
                    <div class="rtprov-form-row">
                        <label for="rtprov_reset_password_password"><?php _e('Password', 'realtyna-provisioning'); ?> <span class="required">*</span></label>
                        <input type="password" name="password" id="rtprov_reset_password_password" class="widefat" required="required">
                    </div>
                    <div class="rtprov-form-row">
                        <label for="rtprov_reset_password_password_confirmation"><?php _e('Password Confirmation', 'realtyna-provisioning'); ?> <span class="required">*</span></label>
                        <input type="password" name="password_confirmation" id="rtprov_reset_password_password_confirmation" class="widefat" required="required">
                    </div>
                    <div class="rtprov-form-row rtprov-form-submit-row">
                        <input type="hidden" name="email" value="" id="rtprov_reset_password_email">
                        <?php wp_nonce_field('rtprov_reset_password'); ?>
                        <input class="button-primary" type="submit" value="<?php _e('Reset Password', 'realtyna-provisioning'); ?>">
                    </div>
                </form>
                <div class="rtprov-alert-danger rtprov-mt-2" id="rtprov-reset-password-error"></div>
            </div>
        </div>
        <div class="rtprov-row">
            <div class="rtprov-alert-success" id="rtprov-success-message"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery('#rtprov_forgot_password_form').on('submit', function(event)
{
    event.preventDefault();

    // Add loading style
    jQuery(".rtprov-content").fadeTo('slow', 0.5);

    // Remove Previous Error
    jQuery('#rtprov-forgot-password-error').html('');

    var forgot = jQuery("#rtprov_forgot_password_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=rtprov_forgot&" + forgot,
        dataType: 'json',
        success: function(response)
        {
            if(response.success)
            {
                jQuery('#rtprov_reset_password_email').val(jQuery('#rtprov_forgot_password_email').val());
                jQuery('#rtprov_forgot_password_form_wrapper').slideUp();
                jQuery('#rtprov_reset_password_form_wrapper').slideDown();
                jQuery('#rtprov-success-message').html(response.message);
            }
            else
            {
                jQuery('#rtprov-forgot-password-error').html(response.error);
            }

            // Remove loading style
            jQuery(".rtprov-content").fadeTo('slow', 1);
        },
        error: function()
        {
            // Remove loading style
            jQuery(".rtprov-content").fadeTo('slow', 1);
        }
    });
});

jQuery('#rtprov_reset_password_form_wrapper').on('submit', function(event)
{
    event.preventDefault();

    // Add loading style
    jQuery(".rtprov-content").fadeTo('slow', 0.5);

    // Remove Previous Error
    jQuery('#rtprov-reset-password-error').html('');

    var reset = jQuery("#rtprov_reset_password_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=rtprov_reset&" + reset,
        dataType: 'json',
        success: function(response)
        {
            if(response.success)
            {
                jQuery('.rtprov-authentication-wrapper').slideUp();
                jQuery('#rtprov-success-message').html(response.message);

                setTimeout(function()
                {
                    location.reload();
                }, 3000);
            }
            else
            {
                jQuery('#rtprov-reset-password-error').html(response.error);
            }

            // Remove loading style
            jQuery(".rtprov-content").fadeTo('slow', 1);
        },
        error: function()
        {
            // Remove loading style
            jQuery(".rtprov-content").fadeTo('slow', 1);
        }
    });
});
</script>