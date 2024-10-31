<?php
// no direct access
defined('ABSPATH') or die();

if(!class_exists('RTPROV_Folder')):

/**
 * RTPROV Folder Class.
 *
 * @class RTPROV_Folder
 * @version	1.0.0
 */
class RTPROV_Folder extends RTPROV_Base
{
    /**
	 * Constructor method
	 */
	public function __construct()
    {
	}

    public static function files($path, $filter = '.')
    {
        // Path doesn't exists
        if(!self::exists($path)) return false;

        $files = array();
        if($handle = opendir($path))
        {
            while(false !== ($entry = readdir($handle)))
            {
                if($entry == '.' or $entry == '..' or is_dir($entry)) continue;
                if(!preg_match("/$filter/", $entry)) continue;

                $files[] = $entry;
            }

            closedir($handle);
        }

        return $files;
    }

    public static function exists($path)
    {
        return is_dir($path);
    }

    public static function create($path, $mode = 0755)
    {
        return mkdir($path, $mode);
    }

    public static function delete($path)
    {
        if(substr($path, strlen($path) - 1, 1) != '/') $path .= '/';

        $files = glob($path.'*', GLOB_MARK);
        foreach($files as $file)
        {
            if(is_dir($file)) RTPROV_Folder::delete($file);
            else unlink($file);
        }

        return rmdir($path);
    }

    public function getTempDirectory()
    {
        $directory = $this->rtprov_tmp_path().'/tmp_'.md5(microtime(true));

        // Create the Directory
        if($this->create($directory)) return $directory;
        else return false;
    }
}

endif;