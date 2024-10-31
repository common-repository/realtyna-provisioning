<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_Menus')):

/**
 * RTPROV Menus Class.
 *
 * @class RTPROV_Menus
 * @version	1.0.0
 */
class RTPROV_Menus extends RTPROV_Base
{
    protected $dashboard;

    /**
	 * Constructor method
	 */
	public function __construct()
    {
	}
    
    public function init()
    {
        // Initialize menus
        $this->dashboard = new RTPROV_Menus_Dashboard();

        // Register RTPROV Menus
        add_action('admin_menu', array($this, 'register_menus'), 1);
    }
    
    public function register_menus()
    {
        add_menu_page(__('Realtyna Provisioning', 'realtyna-provisioning'), __('R. Provisioning', 'realtyna-provisioning'), 'manage_options', 'realtyna-provisioning', array($this->dashboard, 'output'), 'dashicons-awards', 4);
    }
}

endif;