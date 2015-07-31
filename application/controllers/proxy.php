<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Proxy extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    public function index() {
        return false;
    }
    public function tbdv() {
        $this->output->set_content_type('application/json')->set_output('you made a typo...');
        return false;
    }

	public function tvdb() {
        $this->output->set_content_type('application/json')->set_output('decommissioned, please stop hitting this ip');
        return false;
	}

}