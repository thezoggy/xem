<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Output extends CI_Output {

    function __construct() {
        parent::__construct();
    }// __construct

    /**
     * Write a Cache File
     *
     * @access    public
     * @return    void
     */
    function _write_cache($output)
    {
        $CI =& get_instance();
        $path = $CI->config->item('cache_path');

        $cache_path = ($path == '') ? APPPATH.'cache/' : $path;

        if ( ! is_dir($cache_path) OR ! is_really_writable($cache_path))
        {
            log_message('error', "Unable to write cache file: ".$cache_path);
            return;
        }

        $uri =  $CI->config->item('base_url').
                $CI->config->item('index_page').
                $CI->uri->uri_string();

        // build query strings
        $querystrings = $_SERVER['QUERY_STRING'];
        if ( $querystrings != null ) {$querystrings = "?".$querystrings;}
        $uri = $uri.$querystrings;

        $cache_path .= md5($uri);

        if ( ! $fp = @fopen($cache_path, FOPEN_WRITE_CREATE_DESTRUCTIVE))
        {
            log_message('error', "Unable to write cache file: ".$cache_path);
            return;
        }

        $expire = time() + ($this->cache_expiration * 60);

        if (flock($fp, LOCK_EX))
        {
            fwrite($fp, serialize(array('exp' => $expire, 'headers' => $this->headers, 'output' => $output)));
            flock($fp, LOCK_UN);
        }
        else
        {
            log_message('error', "Unable to secure a file lock for file at: ".$cache_path);
            return;
        }
        fclose($fp);
        @chmod($cache_path, FILE_WRITE_MODE);

        log_message('debug', "Cache file written: ".$cache_path);
    }// _write_cache

    /**
     * Update/serve a cached file
     *
     * @access    public
     * @return    void
     */
    function _display_cache(&$CFG, &$URI)
    {
        $cache_path = ($CFG->item('cache_path') == '') ? APPPATH.'cache/' : $CFG->item('cache_path');

        // Early detect if cache is even there
        if ( ! is_dir($cache_path) OR ! is_really_writable($cache_path))
        {
            log_message('error', "Unable to access or write cache file: ".$cache_path);
            return FALSE;
        }

        // Build the file path.  The file name is an MD5 hash of the full URI
        $uri =  $CFG->item('base_url').
                $CFG->item('index_page').
                $URI->uri_string;

        // build query strings
        $querystrings = $_SERVER['QUERY_STRING'];
        if ( $querystrings != null ) {$querystrings = "?".$querystrings;}
        $uri = $uri.$querystrings;

        $filepath = $cache_path.md5($uri);

        if ( ! @file_exists($filepath))
        {
            return FALSE;
        }

        if ( ! $fp = @fopen($filepath, FOPEN_READ))
        {
            return FALSE;
        }

        flock($fp, LOCK_SH);

        $cache = '';
        if (filesize($filepath) > 0)
        {
            $cache = fread($fp, filesize($filepath));
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        // Restore the cache array
        $cache = unserialize($cache);

        // Validate cache file
        if ( ! isset($cache['exp'] ) OR ! isset($cache['headers']) OR ! isset($cache['output']))
        {
            return FALSE;
        }

        // Has the file expired? If so we'll delete it.
        if (time() >= trim($cache['exp']))
        {
            @unlink($filepath);
            log_message('debug', "Cache file has expired. File deleted");
            return FALSE;
        }

        // Restore headers
        $this->headers = $cache['headers'];

        // Display the cache
        $this->_display($cache['output']);
        log_message('debug', "Cache file is current. Sending it to browser.");
        return TRUE;
    }// _display_cache

}
/* End of file MY_Output.php */
/* Location: ./application/core/MY_Output.php */