<?php
// no direct access
defined('ABSPATH') or die();

// Search Term
$term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : NULL;

// Package Types
$type = isset($_GET['type']) ? sanitize_text_field($_GET['type']) : NULL;

// Search Query
$query = array('s' => $term, 'limit' => 500);
if($type) $query['types'] = array($type);

$API = new RTPROV_Api();
$response = $API->packages($query);
$packages = isset($response['data']) ? $response['data'] : array();

$response = $API->types();
$types = isset($response['data']) ? $response['data'] : array();
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
        <?php _e('You can search in repository and install the packages that you like.', 'realtyna-provisioning'); ?>
    </div>
    <div class="rtprov-content">

        <form class="rtprov-search" method="GET" action="<?php echo admin_url('admin.php'); ?>">
            <div class="rtprov-row">
                <div class="rtprov-col-12">
                    <input type="hidden" name="page" value="realtyna-provisioning">
                    <input type="search" name="s" value="<?php echo $term; ?>" placeholder="<?php esc_attr_e('Keyword ...', 'realtyna-provisioning'); ?>">
                    <select name="type" title="<?php esc_attr_e('Category', 'realtyna-provisioning'); ?>">
                        <option value="">-----</option>
                        <?php foreach($types as $t): ?>
                            <option value="<?php echo $t['id']; ?>" <?php echo ($t['id'] == $type ? 'selected="selected"' : ''); ?>><?php echo $t['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="button-primary"><?php _e('Search', 'realtyna-provisioning'); ?></button>
                </div>
            </div>
        </form>

        <?php if(count($packages)): ?>
        <div class="rtprov-mt-4 rtprov-mb-4 rtprov-alert-success"><?php _e('Click on the package name for installation.', 'realtyna-provisioning'); ?></div>
        <ul class="rtprov-packages">
            <?php foreach($packages as $package): ?>
            <li>
                <a href="<?php echo wp_nonce_url($this->get_admin_url('realtyna-provisioning', array('install'=>$package['id'])), 'rtprov-install-'.$package['id']); ?>"><?php echo $package['name']; ?></a>
                <p><?php echo $package['description']; ?></p>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?>
        <div class="rtprov-mt-4 rtprov-mb-4 rtprov-alert-danger"><?php _e('No package found!', 'realtyna-provisioning'); ?></div>
        <?php endif; ?>

    </div>
</div>