<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV')):

/**
 * Main Realtyna Provisioning Class.
 *
 * @class RTPROV
 * @version	1.0.0
 */
final class RTPROV
{
    /**
	 * RTPROV version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';
    
    /**
	 * The single instance of the class.
	 *
	 * @var RTPROV
	 * @since 1.0.0
	 */
	protected static $instance = null;
    
    /**
	 * Main RTPROV Instance.
	 *
	 * Ensures only one instance of RTPROV is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see RTPROV()
	 * @return RTPROV - Main instance.
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
	 * Un-serializing instances of this class is forbidden.
	 * @since 1.0.0
	 */
	public function __wakeup()
    {
		_doing_it_wrong(__FUNCTION__, __('Cheating huh?', 'realtyna-provisioning'), '1.0.0');
	}
    
    /**
	 * RTPROV Constructor.
	 */
	protected function __construct()
    {
        // Define Constants
        $this->define_constants();
        
        // Auto Loader
        spl_autoload_register(array($this, 'autoload'));
        
        // Initialize the RTPROV
        $this->init();
	}
    
    /**
	 * Define RTPROV Constants.
	 */
	private function define_constants()
    {
        // RTPROV Absolute Path
        if(!defined('RTPROV_ABSPATH')) define('RTPROV_ABSPATH', dirname(__FILE__));

        // RTPROV Directory Name
        if(!defined('RTPROV_DIRNAME')) define('RTPROV_DIRNAME', basename(RTPROV_ABSPATH));

        // RTPROV Plugin Base Name
        if(!defined('RTPROV_BASENAME')) define('RTPROV_BASENAME', plugin_basename(RTPROV_ABSPATH.'/realtyna-provisioning.php')); // realtyna-provisioning/realtyna-provisioning.php

        // RTPROV Version
        if(!defined('RTPROV_VERSION')) define('RTPROV_VERSION', $this->version);

        // WordPress Upload Directory
		$upload_dir = wp_upload_dir();

		// RTPROV Logs Directory
        if(!defined('RTPROV_LOG_DIR')) define('RTPROV_LOG_DIR', $upload_dir['basedir'] . '/rtprov-logs/');
	}
    
    /**
     * Initialize the RTPROV
     */
    private function init()
    {
        // Plugin Activation / Deactivation / Uninstall
        RTPROV_Plugin_Hooks::instance();
        
        // RTPROV Menus
        $Menus = new RTPROV_Menus();
        $Menus->init();

        // RTPROV Actions / Filters
        $Hooks = new RTPROV_Hooks();
        $Hooks->init();

        // RTPROV Assets
        $Assets = new RTPROV_Assets();
        $Assets->init();

        // RTPROV Internationalization
        $i18n = new RTPROV_i18n();
        $i18n->init();
    }
    
    /**
     * Automatically load RTPROV classes whenever needed.
     * @param string $class_name
     * @return void
     */
    private function autoload($class_name)
    {
        $class_ex = explode('_', strtolower($class_name));

        // It's not a RTPROV Class
        if($class_ex[0] != 'rtprov') return;
        
        // Drop 'RTPROV'
        $class_path = array_slice($class_ex, 1);
        
        // Create Class File Path
        $file_path = RTPROV_ABSPATH . '/app/includes/' . implode('/', $class_path) . '.php';

        // We found the class!
        if(file_exists($file_path)) require_once $file_path;
    }
    
    /**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	public function is_request($type)
    {
		switch($type)
        {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined('DOING_AJAX');
			case 'cron':
				return defined('DOING_CRON');
			case 'frontend':
				return (!is_admin() || defined('DOING_AJAX')) && !defined('DOING_CRON');
            default:
                return false;
		}
	}
}

endif;

/**
 * Main instance of RTPROV
 *
 * Returns the main instance of RTPROV to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return RTPROV
 */
function RTPROV()
{
	return RTPROV::instance();
}

// Init the Realtyna Provisioning :)
RTPROV();