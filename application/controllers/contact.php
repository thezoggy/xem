<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Contact extends SuperController {

	function __construct(){
		parent::__construct();
	    $this->load->library('recaptcha');
	    $this->load->library('form_validation');
	    $this->load->helper('form');
	    $this->lang->load('recaptcha');
	}

	public function index(){	
		$this->out['locations'] = $this->db->get('locations');


	    if ($this->form_validation->run()) {
	    	$this->out['recaptcha'] = 'Yay! You got it right!';
	    }
	    else{
			//the desired language code string can be passed to the get_html() method
			//"en" is the default if you don't pass the parameter
			//valid codes can be found here:http://recaptcha.net/apidocs/captcha/client.html
			$this->out['recaptcha'] = $this->recaptcha->get_html();
	    }
		$this->_loadView('index');
	}
		
}
?>