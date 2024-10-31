<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_Assets')):

/**
 * RTPROV Assets Class.
 *
 * @class RTPROV_Assets
 * @version	1.0.0
 */
class RTPROV_Assets extends RTPROV_Base
{
    /**
     * @static
     * @var array
     */
    public static $params = array();

    /**
	 * Constructor method
	 */
	public function __construct()
    {
	}
    
    public function init()
    {
        // Include needed assets (CSS, JavaScript etc) in the WordPress backend
        add_action('admin_enqueue_scripts', array($this, 'admin'), 0);
    }
    
    public function admin()
    {
        // Include RTPROV backend script file
        wp_enqueue_script('RTPROV-admin-script', $this->RTPROV_asset_url('js/backend.js'), array('jquery'), false, true);

        // Include RTPROV backend CSS file
        wp_enqueue_style('RTPROV-admin-style', $this->RTPROV_asset_url('css/backend.css'));
    }
}

endif;