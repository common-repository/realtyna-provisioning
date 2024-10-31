<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_Menus_Dashboard')):

/**
 * RTPROV Dashboard Menu Class.
 *
 * @class RTPROV_Menus_Dashboard
 * @version	1.0.0
 */
class RTPROV_Menus_Dashboard extends RTPROV_Menus
{
    protected $step;

    /**
	 * Constructor method
	 */
	public function __construct()
    {
        // Initialize the menu
        $this->init();
	}
    
    public function init()
    {
        add_action('wp_ajax_rtprov_register', array($this, 'register'));
        add_action('wp_ajax_rtprov_login', array($this, 'login'));
        add_action('wp_ajax_rtprov_forgot', array($this, 'forgot'));
        add_action('wp_ajax_rtprov_reset', array($this, 'reset'));
        add_action('wp_ajax_rtprov_download', array($this, 'download'));
        add_action('wp_ajax_rtprov_install', array($this, 'install'));
    }
    
    public function output()
    {
        // Log Last Activity
        update_option('rtprov_last_activity', time());

        $API = new RTPROV_Api();

        if(!$API->getAuthKey())
        {
            if(isset($_GET['forgot']) and is_numeric($_GET['forgot']) and $_GET['forgot']) $this->step = 'forgot';
            else $this->step = 'authentication';
        }
        else
        {
            if(isset($_GET['install']) and is_numeric($_GET['install']) and $_GET['install']) $this->step = 'install';
            elseif(isset($_GET['logout']) and is_numeric($_GET['logout']) and $_GET['logout']) $this->step = 'logout';
            else $this->step = 'search';
        }

        // Generate output
        $path = $this->get_rtprov_path().'/app/html/menus/dashboard/tpl.php';

        // Start buffering
        ob_start();

        // Include the TPL File
        include $path;

        // Get Buffer
        $output = ob_get_clean();

        // Print the output
        echo $output;
    }

    public function welcome()
    {
        $API = new RTPROV_Api();

        $welcome = '';
        if($API->getAuthKey())
        {
            $name = get_option('rtprov_username');
            if(!trim($name)) $name = '';

            $welcome = sprintf(__('Welcome %s! %s', 'realtyna-provisioning'), $name, '<a class="rtprov-bold" href="'.wp_nonce_url($this->get_admin_url('realtyna-provisioning', array('logout'=>1)), 'rtprov-logout').'">'.__('logout', 'realtyna-provisioning').'</a>');
        }

        return $welcome;
    }

    public function register()
    {
        $wpnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : NULL;

        // Check if nonce is not set
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING', 'message'=>__('Security Nonce is Missed!', 'realtyna-provisioning')));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'rtprov_register')) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID', 'message'=>__('Security Nonce is Invalid!', 'realtyna-provisioning')));

        $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : NULL;
        $name = isset($_POST['name']) ? sanitize_text_field($_POST['name']) : NULL;

        // Init the API
        $API = new RTPROV_Api();

        // Send the Register Request to Server
        $JSON = $API->register(array(
            'email' => $email,
            'name' => $name,
            'site' => trim(get_home_url(), '/ '),
        ));

        $results = json_decode($JSON, true);
        $auth_token = (isset($results['data']) and isset($results['data']['auth_token'])) ? $results['data']['auth_token'] : NULL;

        if($auth_token)
        {
            // Save the Auth Token
            update_option('rtprov_token', $auth_token);

            $this->response(array(
                'success' => 1,
                'message' => __("You're successfully registered in the Realtyna Provisioning Server.", 'realtyna-provisioning'),
            ));
        }
        else
        {
            $errors = isset($results['errors']) ? $results['errors'] : array();

            $error = '<ul>';
            foreach($errors as $err) foreach($err as $er) $error .= '<li>'.$er.'</li>';
            $error .= '</ul>';

            $this->response(array(
                'success' => 0,
                'error' => $error,
            ));
        }
    }

    public function login()
    {
        $wpnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : NULL;

        // Check if nonce is not set
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING', 'message'=>__('Security Nonce is Missed!', 'realtyna-provisioning')));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'rtprov_login')) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID', 'message'=>__('Security Nonce is Invalid!', 'realtyna-provisioning')));

        $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : NULL;
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : NULL;

        // Init the API
        $API = new RTPROV_Api();

        // Send the Login Request to Server
        $JSON = $API->login(array(
            'email' => $email,
            'password' => $password,
            'site' => trim(get_home_url(), '/ '),
        ));

        $results = json_decode($JSON, true);
        $auth_token = (isset($results['data']) and isset($results['data']['auth_token'])) ? $results['data']['auth_token'] : NULL;
        $username = (isset($results['data']) and isset($results['data']['name'])) ? $results['data']['name'] : NULL;

        if($auth_token)
        {
            // Save the Auth Token
            update_option('rtprov_token', $auth_token);

            // Save the Name
            update_option('rtprov_username', $username);

            // Save the Current User ID
            update_option('rtprov_wp_userid', get_current_user_id());

            $this->response(array(
                'success' => 1,
                'message' => __("You're successfully logged in to the Realtyna Provisioning Server.", 'realtyna-provisioning'),
            ));
        }
        else
        {
            $errors = isset($results['errors']) ? $results['errors'] : array();

            $error = '<ul>';
            foreach($errors as $err) foreach($err as $er) $error .= '<li>'.$er.'</li>';
            $error .= '</ul>';

            $this->response(array(
                'success' => 0,
                'error' => $error,
            ));
        }
    }

    public function forgot()
    {
        $wpnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : NULL;

        // Check if nonce is not set
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING', 'message'=>__('Security Nonce is Missed!', 'realtyna-provisioning')));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'rtprov_forgot_password')) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID', 'message'=>__('Security Nonce is Invalid!', 'realtyna-provisioning')));

        $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : NULL;

        // Init the API
        $API = new RTPROV_Api();

        // Send the Forgot Password Request to Server
        $JSON = $API->forgotPassword(array(
            'email' => $email,
        ));

        $results = json_decode($JSON, true);
        $success = (isset($results['success']) and $results['success']) ? true : false;

        if($success)
        {
            $this->response(array(
                'success' => 1,
                'message' => __("Please check your inbox and insert the code that you received in the form. Also insert and confirm your new password.", 'realtyna-provisioning'),
            ));
        }
        else
        {
            $errors = isset($results['errors']) ? $results['errors'] : array();

            $error = '<ul>';
            foreach($errors as $err) foreach($err as $er) $error .= '<li>'.$er.'</li>';
            $error .= '</ul>';

            $this->response(array(
                'success' => 0,
                'error' => $error,
            ));
        }
    }

    public function reset()
    {
        $wpnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : NULL;

        // Check if nonce is not set
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING', 'message'=>__('Security Nonce is Missed!', 'realtyna-provisioning')));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'rtprov_reset_password')) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID', 'message'=>__('Security Nonce is Invalid!', 'realtyna-provisioning')));

        $email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : NULL;
        $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : NULL;
        $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : NULL;
        $password_confirmation = isset($_POST['password_confirmation']) ? sanitize_text_field($_POST['password_confirmation']) : NULL;

        // Init the API
        $API = new RTPROV_Api();

        // Send the Reset Password Request to Server
        $JSON = $API->resetPassword(array(
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password_confirmation,
            'site' => trim(get_home_url(), '/ '),
        ));

        $results = json_decode($JSON, true);
        $auth_token = (isset($results['data']) and isset($results['data']['auth_token'])) ? $results['data']['auth_token'] : NULL;

        if($auth_token)
        {
            // Save the Auth Token
            update_option('rtprov_token', $auth_token);

            $this->response(array(
                'success' => 1,
                'message' => __("Your new password is set successfully and also you logged in automatically to the system.", 'realtyna-provisioning'),
            ));
        }
        else
        {
            $errors = isset($results['errors']) ? $results['errors'] : array();

            $error = '<ul>';
            foreach($errors as $err) foreach($err as $er) $error .= '<li>'.$er.'</li>';
            $error .= '</ul>';

            $this->response(array(
                'success' => 0,
                'error' => $error,
            ));
        }
    }

    public function download()
    {
        $wpnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : NULL;
        $package_id = isset($_POST['id']) ? $_POST['id'] : 0;

        // Check if nonce is not set
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING', 'message'=>__('Security Nonce is Missed!', 'realtyna-provisioning')));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'rtprov-install-do-'.$package_id)) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID', 'message'=>__('Security Nonce is Invalid!', 'realtyna-provisioning')));

        // Init the API
        $API = new RTPROV_Api();

        // Send the Download Request to Server
        $response = $API->download($package_id, array(
            'site' => trim(get_home_url(), '/ '),
        ));

        if(isset($response['download']))
        {
            $file = new RTPROV_File();
            $folder = new RTPROV_Folder();

            $buffer = $file->download($response['download']);
            $destination = $folder->getTempDirectory().'/package.zip';

            $wrote = $file->write($destination, $buffer);

            if($wrote)
            {
                $messages = array(
                    array('text' => __("Download was successful!", 'realtyna-provisioning'), 'type' => 'info'),
                    array('text' => __("We are still working to install it. Thanks for your patience ....", 'realtyna-provisioning'), 'type' => 'normal'),
                );

                $this->response(array(
                    'success' => 1,
                    'messages' => $messages,
                    'path' => $destination
                ));
            }
            else
            {
                $messages = array(
                    array('text' => __("We're not able to save the file in your server due to an error!", 'realtyna-provisioning'), 'type' => 'danger'),
                );

                $this->response(array(
                    'success' => 0,
                    'messages' => $messages
                ));
            }
        }
        else
        {
            $messages = array(
                array('text' => __("An unknown error occurred during downloading the package!", 'realtyna-provisioning'), 'type' => 'danger'),
            );

            $this->response(array(
                'success' => 0,
                'messages' => $messages,
            ));
        }
    }

    public function install()
    {
        $wpnonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : NULL;
        $package_id = isset($_POST['id']) ? $_POST['id'] : 0;

        // Check if nonce is not set
        if(!trim($wpnonce)) $this->response(array('success'=>0, 'code'=>'NONCE_MISSING', 'message'=>__('Security Nonce is Missed!', 'realtyna-provisioning')));

        // Verify that the nonce is valid.
        if(!wp_verify_nonce($wpnonce, 'rtprov-install-do-'.$package_id)) $this->response(array('success'=>0, 'code'=>'NONCE_IS_INVALID', 'message'=>__('Security Nonce is Invalid!', 'realtyna-provisioning')));

        $package = isset($_POST['package']) ? $_POST['package'] : NULL;
        $destination = str_replace('package.zip', '', $package);

        $file = new RTPROV_File();
        $extracted = $file->extract($package, $destination);

        // Extract Failed
        if(!$extracted)
        {
            // Remove the Package
            RTPROV_Folder::delete($destination);

            $this->response(array(
                'success' => 0,
                'messages' => array(
                    array('text' => __("We couldn't extract the package content. Please make sure ZipArchive PHP Extension is enabled on your server!", 'realtyna-provisioning'), 'type' => 'danger'),
                ),
            ));
        }

        // Installer File Couldn't Find!
        if(!$file->exists($destination.'installer.php'))
        {
            // Remove the Package
            RTPROV_Folder::delete($destination);

            $this->response(array(
                'success' => 0,
                'messages' => array(
                    array('text' => __("We couldn't find the package installer file!", 'realtyna-provisioning'), 'type' => 'danger'),
                ),
            ));
        }

        // Include the Installer
        include_once $destination.'installer.php';

        // Installer Class Couldn't Find!
        if(!class_exists('RTPROV_Installer'))
        {
            // Remove the Package
            RTPROV_Folder::delete($destination);

            $this->response(array(
                'success' => 0,
                'messages' => array(
                    array('text' => __("We couldn't find the installer class!", 'realtyna-provisioning'), 'type' => 'danger'),
                ),
            ));
        }

        $installer = new RTPROV_Installer();
        if($installer->run())
        {
            // Remove the Package
            RTPROV_Folder::delete($destination);

            $this->response(array(
                'success' => 1,
                'messages' => $installer->getLogs(),
            ));
        }
        else
        {
            // Remove the Package
            RTPROV_Folder::delete($destination);

            $this->response(array(
                'success' => 0,
                'messages' => $installer->getLogs(),
            ));
        }
    }
}

endif;