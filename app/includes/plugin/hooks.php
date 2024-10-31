<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_Plugin_Hooks')):

/**
 * RTPROV Plugin Hooks Class.
 *
 * @class RTPROV_Plugin_Hooks
 * @version	1.0.0
 */
class RTPROV_Plugin_Hooks
{
    /**
	 * The single instance of the class.
	 *
	 * @var RTPROV_Plugin_Hooks
	 * @since 1.0.0
	 */
	protected static $instance = null;

    /**
	 * RTPROV Plugin Hooks Instance.
	 *
	 * @since 1.0.0
	 * @static
	 * @return RTPROV_Plugin_Hooks
	 */
	public static function instance()
    {
        // Get an instance of Class
		if(is_null(self::$instance)) self::$instance = new self();
        
        // Return the instance
		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0.0
	 */
	public function __clone()
    {
		_doing_it_wrong(__FUNCTION__, __('Cheating huh?', 'realtyna-provisioning'), '1.0.0');
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup()
    {
		_doing_it_wrong(__FUNCTION__, __('Cheating huh?', 'realtyna-provisioning'), '1.0.0');
	}
    
    /**
	 * Constructor method
	 */
	protected function __construct()
    {
        register_activation_hook(RTPROV_BASENAME, array($this, 'activate'));
		register_deactivation_hook(RTPROV_BASENAME, array($this, 'deactivate'));
		register_uninstall_hook(RTPROV_BASENAME, array('RTPROV_Plugin_Hooks', 'uninstall'));
	}
    
    /**
     * Runs on plugin activation
     * @param boolean $network
     */
    public function activate($network = false)
	{
	}
    
    /**
     * Runs on plugin deactivation
     * @param boolean $network
     */
    public function deactivate($network = false)
	{
	}
    
    /**
     * Runs on plugin uninstallation
     */
    public static function uninstall()
	{
	    delete_option('rtprov_token');
	    delete_option('rtprov_username');
	    delete_option('rtprov_wp_userid');
	    delete_option('rtprov_last_activity');
	}
}

endif;