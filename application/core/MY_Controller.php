<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SuperController extends CI_Controller {

	function __construct(){
		parent::__construct();
        // show report at end of page
        //$this->output->enable_profiler(TRUE);
        // disable saving sql queries in array for last_query
        //$this->db->save_queries = FALSE;

		// create a new user alpha beta testers
		//$this->simpleloginsecure->create('user_nick','email', 'pw');


		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("html");
		$this->out['logedIn'] = $this->session->userdata('logged_in');

		$this->out['uri'] = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3);
		$this->out['uri2'] = $this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(5);
		$this->out['shows'] = getShows($this->db);

		$this->user_id = 0;
		$this->user_nick = "";
		$this->user_email = "";
		$this->user_lvl = -1;

		if($this->session->userdata('logged_in')){
			$this->out['disabled'] = "";
			$this->out['logedIn'] = true;
			$this->user_id = $this->session->userdata('user_id');
			$this->user_nick = $this->session->userdata('user_nick');
			$this->user_email = $this->session->userdata('user_email');
			$this->user_lvl = $this->session->userdata('user_lvl');

			$this->config_email_new_account = $this->session->userdata('config_email_new_account');
			$this->config_email_new_show = $this->session->userdata('config_email_new_show');
			$this->config_email_public_request = $this->session->userdata('config_email_public_request');

			$this->out['user_nick'] = $this->user_nick;
			$this->out['user_email'] = $this->user_email;
			$this->out['user_lvl'] = $this->user_lvl;

			$this->out['config_email_new_account'] = $this->config_email_new_account;
			$this->out['config_email_new_show'] = $this->config_email_new_show;
			$this->out['config_email_public_request'] = $this->config_email_public_request;
		}else{
			$this->out['disabled'] = 'disabled="disabled"';
			$this->out['logedIn'] = false;
			$this->out['user_lvl'] = $this->user_lvl;
		}

		$this->out['title'] = 'Xross Entity Map';
		$this->load->model('dbobjectcache');
		$this->history = new History($this->db,$this->session);
		$this->dbcache = new DBCache($this->db);
		$this->oh = new Objectholder($this->db, $this->dbobjectcache, $this->history, $this->dbcache);

	}


	protected function _loadView($center='',$doPrefix=true){
		$prefix = "";
		if($doPrefix)
			$prefix = strtolower(get_class($this)).'/';
		if($center=="")
			$center = "index";
        $viewFile = $prefix.$center;

		$c = new Content($this->oh, $viewFile);
		$this->out['cmsc'] = 'Empty';
		if(isset($c->content))
		    $this->out['cmsc'] = $c->content;
		$this->out['cmse'] = $c->getEdit();

		$this->load->view('top', $this->out);
		$this->load->view($viewFile, $this->out);
		$this->load->view('bottom', $this->out);
	}
}
?>