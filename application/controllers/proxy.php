<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Proxy extends CI_Controller {

	function __construct(){
		parent::__construct();

		$this->load->helper("url");
		$this->load->model('dbobjectcache');
		$this->history = new History($this->db,$this->session);
		$this->dbcache = new DBCache($this->db);
		$this->oh = new Objectholder($this->db, $this->dbobjectcache, $this->history, $this->dbcache);
	}

	public function index(){
		redirect('xem');
	}

	public function tvdb(){

	    //print $this->uri->segment(3)."<br>";
	    //print $this->uri->segment(4)."<br>";

        $api_regex = '/\/api\/([0-9A-F]+)\/series\/(\d+)\/?(.*)/';
    	$result = preg_match($api_regex, current_url(), $matches);
    	if($matches){
            $proxy = new TvdbProxy($this->oh, $matches[0], $this->uri->segment(3));    
            if($proxy->validUrl){
                ob_start();
                $xml = $proxy->call();
                ob_end_clean();
                print $xml;
                return;
            }
        }
        print "invalid url structure";
	}


}