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
        <?php _e('Please register or login to Realtyna Provisioning server then you can search between all packages and install desired packages on your website!', 'realtyna-provisioning'); ?>
    </div>
    <div class="rtprov-content">
        <div class="rtprov-row rtprov-authentication-wrapper rtprov-mb-4">
            <div class="rtprov-col-6 rtprov-pr-4">
                <h2><?php _e('Register', 'realtyna-provisioning'); ?></h2>
                <form id="rtprov_registration_form" method="post">
                    <div class="rtprov-form-row">
                        <label for="rtprov_register_name"><?php _e('Name', 'realtyna-provisioning'); ?> <span class="required">*</span></label>
                        <input type="text" name="name" id="rtprov_register_name" class="widefat" required="required">
                    </div>
                    <div class="rtprov-form-row">
                        <label for="rtprov_register_email"><?php _e('Email', 'realtyna-provisioning'); ?> <span class="required">*</span></label>
                        <input type="email" name="email" id="rtprov_register_email" class="widefat" required="required">
                    </div>
                    <div class="rtprov-form-row rtprov-form-submit-row">
                        <?php wp_nonce_field('rtprov_register'); ?>
                        <input class="button-primary" type="submit" value="<?php _e('Register', 'realtyna-provisioning'); ?>">
                    </div>
                </form>
                <div class="rtprov-alert-danger rtprov-mt-2" id="rtprov-register-error"></div>
            </div>
            <div class="rtprov-col-6 rtprov-pl-4">
                <h2><?php _e('Login', 'realtyna-provisioning'); ?></h2>
                <form id="rtprov_login_form" method="post">
                    <div class="rtprov-form-row">
                        <label for="rtprov_login_email"><?php _e('Email', 'realtyna-provisioning'); ?> <span class="required">*</span></label>
                        <input type="email" name="email" id="rtprov_login_email" class="widefat" required="required">
                    </div>
                    <div class="rtprov-form-row">
                        <label for="rtprov_login_password"><?php _e('Password', 'realtyna-provisioning'); ?> <span class="required">*</span></label>
                        <input type="password" name="password" id="rtprov_login_password" class="widefat" required="required">
                    </div>
                    <div class="rtprov-form-row rtprov-form-submit-row">
                        <?php wp_nonce_field('rtprov_login'); ?>
                        <input class="button-primary" type="submit" value="<?php _e('Login', 'realtyna-provisioning'); ?>">
                    </div>
                    <div class="rtprov-form-row">
                        <a class="rtprov-bold" href="<?php echo $this->get_admin_url('realtyna-provisioning', array('forgot'=>1)); ?>"><?php _e('Forgot your password?', 'realtyna-provisioning'); ?></a>
                    </div>
                </form>
                <div class="rtprov-alert-danger rtprov-mt-2" id="rtprov-login-error"></div>
            </div>
        </div>
        <div class="rtprov-row">
            <div class="rtprov-alert-success" id="rtprov-success-message"></div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery('#rtprov_registration_form').on('submit', function(event)
{
    event.preventDefault();

    // Add loading style
    jQuery(".rtprov-content").fadeTo('slow', 0.5);

    // Remove Previous Error
    jQuery('#rtprov-register-error').html('');

    var register = jQuery("#rtprov_registration_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=rtprov_register&" + register,
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
                jQuery('#rtprov-register-error').html(response.error);
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

jQuery('#rtprov_login_form').on('submit', function(event)
{
    event.preventDefault();

    // Add loading style
    jQuery(".rtprov-content").fadeTo('slow', 0.5);

    // Remove Previous Error
    jQuery('#rtprov-login-error').html('');

    var login = jQuery("#rtprov_login_form").serialize();
    jQuery.ajax(
    {
        type: "POST",
        url: ajaxurl,
        data: "action=rtprov_login&" + login,
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
                jQuery('#rtprov-login-error').html(response.error);
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