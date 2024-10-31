<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_i18n')):

/**
 * RTPROV i18n Class.
 *
 * @class RTPROV_i18n
 * @version	1.0.0
 */
class RTPROV_i18n extends RTPROV_Base
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
	}

    public function init()
    {
        // Register Language Files
        add_action('plugins_loaded', array($this, 'load_languages'));
	}

    public function load_languages()
    {
        // RTPROV File library
        $file = new RTPROV_File();

        // Get current locale
        $locale = apply_filters('plugin_locale', get_locale(), 'realtyna-provisioning');

        // WordPress language directory /wp-content/languages/realtyna-provisioning-en_US.mo
        $language_filepath = WP_LANG_DIR.'/realtyna-provisioning-'.$locale.'.mo';

        // If language file exists on WordPress language directory use it
        if($file->exists($language_filepath))
        {
            load_textdomain('realtyna-provisioning', $language_filepath);
        }
        // Otherwise use RTPROV plugin directory /path/to/plugin/languages/realtyna-provisioning-en_US.mo
        else
        {
            load_plugin_textdomain('realtyna-provisioning', false, dirname(RTPROV_BASENAME).'/i18n/languages/');
        }
    }
}

endif;