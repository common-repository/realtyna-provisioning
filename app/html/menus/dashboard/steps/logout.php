<?php
// no direct access
defined('ABSPATH') or die();

$nonce = isset($_GET['_wpnonce']) ? sanitize_text_field($_GET['_wpnonce']) : NULL;

$API = new RTPROV_Api();
?>
<div class="wrap about-wrap rtprov-wrap">
    <h1><?php _e('Realtyna Provisioning', 'realtyna-provisioning'); ?></h1>
    <div class="about-text">
        <?php _e("Logging out from the server ...", 'realtyna-provisioning'); ?>
    </div>
    <div class="rtprov-content">
        <?php if(!wp_verify_nonce($nonce, 'rtprov-logout')): ?>
        <div class="rtprov-mt-4 rtprov-mb-4 rtprov-alert-danger"><?php _e('Logout request is not valid!', 'realtyna-provisioning'); ?></div>
        <?php else: $API->logout(); ?>
        <div class="rtprov-mt-4 rtprov-mb-4 rtprov-alert-success"><?php _e('Successfully logged out from the Realtyna Provisioning Server!', 'realtyna-provisioning'); ?></div>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
jQuery(document).ready(function()
{
    setTimeout(function()
    {
        window.location.href = "<?php echo $this->get_admin_url('realtyna-provisioning', array()); ?>";
    }, 6000);
});
</script>