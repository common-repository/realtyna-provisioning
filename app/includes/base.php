<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_Base')):

/**
 * RTPROV Base Class.
 *
 * @class RTPROV_Base
 * @version	1.0.0
 */
class RTPROV_Base
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
	}

    public static function get_admin_url($page, $params, $path = 'admin.php')
    {
        $url = admin_url($path).'?page='.$page;
        foreach($params as $key=>$value) $url .= '&'.$key.'='.$value;

        return $url;
    }

    public function get_rtprov_path()
    {
        return RTPROV_ABSPATH;
    }

    public function rtprov_url()
    {
        return plugins_url().'/'.RTPROV_DIRNAME;
    }

    public function rtprov_asset_url($asset)
    {
        return $this->rtprov_url().'/assets/'.trim($asset, '/ ');
    }

    public function rtprov_asset_path($asset)
    {
        return $this->get_rtprov_path().'/assets/'.trim($asset, '/ ');
    }

    public function rtprov_tmp_path()
    {
        return $this->get_rtprov_path().'/assets/tmp';
    }

    public function response(Array $response)
    {
        echo json_encode($response);
        exit;
	}
}

endif;