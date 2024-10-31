<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_Api')):

/**
 * RTPROV Api Class.
 *
 * @class RTPROV_Api
 * @version	1.0.0
 */
class RTPROV_Api extends RTPROV_Base
{
    private $endpoint = 'https://provisioning.realtyna.com/api';

    /**
	 * Constructor method
	 */
	public function __construct()
    {
	}

    public function register($args = array())
    {
        return $this->postRequest('users', $args, false);
	}

    public function login($args = array())
    {
        return $this->postRequest('users/login', $args, false);
    }

    public function forgotPassword($args = array())
    {
        return $this->postRequest('users/forgot', $args, false);
    }

    public function resetPassword($args = array())
    {
        return $this->postRequest('users/reset', $args, false);
    }

    public function packages($args = array())
    {
        $JSON = $this->getRequest('packages', $args, true);
        return $this->toArray($JSON);
    }

    public function package($id, $args = array())
    {
        $JSON = $this->getRequest('packages/'.$id, $args, true);
        return $this->toArray($JSON);
    }

    public function download($id, $args = array())
    {
        $JSON = $this->getRequest('packages/'.$id.'/download', $args, true);
        return $this->toArray($JSON);
    }

    public function types($args = array())
    {
        $JSON = $this->getRequest('types', $args, true);
        return $this->toArray($JSON);
    }

    public function token()
    {
        $JSON = $this->postRequest('users/token', array('auth_token' => $this->getAuthKey()), false);

        $response = $this->toArray($JSON);
        return isset($response['token']) ? $response['token'] : false;
	}

    public function postRequest($route, $args = array(), $auth = true)
    {
        return $this->call($route, $args, 'POST', $auth);
	}

    public function getRequest($route, $args = array(), $auth = true)
    {
        return $this->call($route, $args, 'GET', $auth);
    }

    public function call($route, $args = array(), $method = 'POST', $auth = true)
    {
        // API URL to Call
        $url = $this->url($route);

        // Generate the Request Headers
        $headers = array('Content-Type'=>'application/json');

        // Add Authentication Token to the Headers
        if($auth) $headers['Authorization'] = $this->getTokenHeader();

        // Request Method
        if($method == 'POST')
        {
            $args = json_encode($args);

            // Request Arguments
            $request = array(
                'body' => $args,
                'timeout' => '10',
                'redirection' => '10',
                'headers' => $headers,
            );

            // Execute the Request
            $response = wp_remote_post($url, $request);
        }
        else
        {
            $url = sprintf("%s?%s", $url, http_build_query($args));

            // Request Arguments
            $request = array(
                'body' => null,
                'timeout' => '10',
                'redirection' => '10',
                'headers' => $headers,
            );

            // Execute the Request
            $response = wp_remote_get($url, $request);
        }

        // Return the Results
        return wp_remote_retrieve_body($response);
	}

    public function url($route)
    {
        return rtrim($this->endpoint.'/'.$route, '/');
	}

    public function getTokenHeader()
    {
        return 'Bearer '.$this->token();
    }

    public function logout()
    {
        update_option('rtprov_token', NULL);
        update_option('rtprov_username', NULL);
        update_option('rtprov_wp_userid', NULL);
        update_option('rtprov_last_activity', NULL);

        return true;
    }

    public function getAuthKey()
    {
        return get_option('rtprov_token', NULL);
    }

    public function toArray($JSON)
    {
        return json_decode($JSON, true);
    }
}

endif;