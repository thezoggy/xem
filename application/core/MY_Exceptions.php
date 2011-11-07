<?php
class MY_Exceptions extends CI_Exceptions{
    function MY_Exceptions(){
        parent::__construct();
    }

    function show_404($page=''){

        $this->config =& get_config();
        $base_url = $this->config['base_url'];

        // could redirect to known controller - or redirect to home page
        //header("location: ".$base_url.'/404');
        header("location: ".$base_url.'Error_Four_ohh_Four');
        exit;
    }
}
?>