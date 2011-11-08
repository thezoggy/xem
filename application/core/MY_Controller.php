<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class SuperController extends CI_Controller {

	function __construct(){
		parent::__construct();
		
		// create a new user alpha beta testers

	
		$this->load->helper("url");
		$this->load->helper("form");
		$this->load->helper("html");
		$this->out['logedIn'] = $this->session->userdata('logged_in');
		
		$this->out['uri'] = $this->uri->segment(1).'/'.$this->uri->segment(2).'/'.$this->uri->segment(3);
		$this->out['uri2'] = $this->uri->segment(3).'/'.$this->uri->segment(4).'/'.$this->uri->segment(5);
		$this->out['shows'] = getShows($this->db);
		
		$this->user_nick = "";
		$this->user_email = "";
		$this->user_lvl = -1;
		
		if($this->session->userdata('logged_in')){
			$this->out['disabled'] = "";
			$this->out['logedInJS'] = "true";
			$this->user_nick = $this->session->userdata('user_nick');
			$this->user_email = $this->session->userdata('user_email');
			$this->user_lvl = $this->session->userdata('user_lvl');
			
			$this->out['user_nick'] = $this->user_nick;
			$this->out['user_email'] = $this->user_email;
			$this->out['user_lvl'] = $this->user_lvl;
		}else{
			$this->out['disabled'] = 'disabled="disabled"';
			$this->out['logedInJS'] = "false";
		}
		
		$this->out['title'] = 'Xross Entity Map';
		$this->load->model('dbobjectcache');
		$this->history = new History($this->db,$this->session);
		$this->oh = new Objectholder($this->db,$this->dbobjectcache,$this->history);

	}
	
	
	protected function _loadView($center='',$doPrefix=true){
		$prefix = "";
		if($doPrefix)
			$prefix = strtolower(get_class($this)).'/';
		if($center=="")
			$center = "index";

		$this->load->view('top', $this->out);
		$this->load->view($prefix.$center, $this->out);
		$this->load->view('bottom', $this->out);
	}
}
?>