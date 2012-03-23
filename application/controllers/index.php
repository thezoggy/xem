<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Index extends SuperController {

	function __construct(){
		parent::__construct();
	}

	public function index(){
		$this->out['locations'] = $this->db->get('locations');
		$this->_loadView('index');

		//$p = new Postman($this->oh,'110381','tvdb');
		//$p->resolveAddress(null,null,7);
        //$p->resolveAddress(2,13,null);
	}

	public function search(){
		$this->out['title'] = 'Search';

		$id = false;
		if($this->uri->segment(2))
			$id = $this->uri->segment(2);
		if(isset($_GET['q']))
			$id = $_GET['q'];


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
		$this->out['title'] = 'Imprint / Impressum';
		$this->_loadView('imprint');
	}
	function faq(){
		$this->out['title'] = 'Frequently Asked Quesions';
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