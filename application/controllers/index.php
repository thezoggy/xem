<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends SuperController {

	function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->out['locations'] = $this->db->get_where('locations',array('status'=>1));

		$changelog = new Changelog($this->oh);
		$changelog->init(0,10);
		$this->out['events'] = $changelog->events;

		$this->_loadView('index');

		//$p = new Postman($this->oh,'110381','tvdb');
		//$p->resolveAddress(null,null,7);
        //$p->resolveAddress(2,13,null);
	}

	public function search(){

		$id = false;
		if($this->uri->segment(2))
			$id = $this->uri->segment(2);
		if(isset($_GET['q']))
			$id = $_GET['q'];

		$this->out['title'] = $id.' | Search';

		if($id){
			if(is_numeric($id)){
				redirect('xem/show/'.$id);
				return;
			}else{
				$shows = getShows($this->db,$id);
				if(count($shows) == 1){
					redirect('xem/show/'.$shows[0]->id);
				}else{
					//print_query($this->db);
					$this->out['searchQeuery'] = $id;
					$this->out['curShows'] = $shows;
					$this->_loadView('showList',false);
				}
			}
		}else{
			redirect('xem/shows/');
		}

		//$this->load->view('top', $this->out);
		//$this->load->view('bottom', $this->out);
	}

	function imprint(){
		$this->out['title'] = 'Imprint / Impression';
		$this->_loadView('imprint');
	}
	function faq(){
		$this->out['title'] = 'Frequently Asked Questions';
		$this->_loadView('faq');
	}
	function doc(){
		$this->out['title'] = 'Documentation';
		$this->_loadView('doc');
	}
	function Error_Four_ohh_Four(){
		header("HTTP/1.1 404 Not Found");
		$this->out['title'] = '404 | Nope nothing here';
		$this->_loadView('404');
	}

}
?>