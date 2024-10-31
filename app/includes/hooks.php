<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_Hooks')):

/**
 * RTPROV General Hooks Class.
 *
 * @class RTPROV_Hooks
 * @version	1.0.0
 */
class RTPROV_Hooks extends RTPROV_Base
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
	}
    
    public function init()
    {
        // Register Actions
        $this->actions();

        // Register Filters
        $this->filters();
    }

    public function actions()
    {
        add_action('admin_notices', array('RTPROV_Flash', 'show'));
        add_action('clear_auth_cookie', array($this, 'logout'));

        add_action('admin_init', array($this, 'inactivity'));
    }

    public function filters()
    {
    }

    public function logout()
    {
        $wp_user_id = get_current_user_id();
        $rtprov_user_id = get_option('rtprov_wp_userid');

        if((int) $wp_user_id == (int) $rtprov_user_id)
        {
            // Init the API
            $API = new RTPROV_Api();
            $API->logout();
        }
    }

    public function inactivity()
    {
        $last_activity_time = get_option('rtprov_last_activity', 0);

        // Activity Time is not Available
        if(!$last_activity_time) return;

        // More than 6 Hours Inactivity
        if(time() - $last_activity_time > 21600) // 6 Hours
        {
            // Init the API
            $API = new RTPROV_Api();
            $API->logout();
        }
    }
}

endif;