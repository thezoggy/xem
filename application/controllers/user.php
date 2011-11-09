<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends SuperController {

	function __construct(){
		parent::__construct();
	}

	public function index(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		} 
		$this->out['title'] = 'User Center';
		$this->_loadView(); // nothing means load the index for the current controller
	}
	
	public function login(){
		$this->out['title'] = 'Login and Registration';
		
		if(isset($_POST['user'])&&isset($_POST['pw'])){
			if($this->simpleloginsecure->login($_POST['user'],$_POST['pw'])){
				redirect($this->out['uri2']);
			}else
				print "login unsuccessfull!";
		}
		if(!$this->session->userdata('logged_in')){
			$this->_loadView('login');
		}else{
			redirect($this->out['uri2']);	
		}	
	}
	
	function logout(){
		if(!$this->session->userdata('logged_in')){
			$this->_loadView('login');
		}else{
			$this->simpleloginsecure->logout();
			redirect($this->out['uri2']);	
		}
	}
	
	function changePW(){
		if(!$this->session->userdata('logged_in')){
			$this->_loadView('login');
		}else{
			if($_POST['new_pw']==$_POST['new_pw_check']){
				if($this->simpleloginsecure->changePassword($_POST['old_pw'],$_POST['new_pw'])){
					$this->simpleloginsecure->logout();
					$this->_loadView('login');	
				}else{
					$this->_loadView();
				}
			}else{
				$this->_loadView();		
			}
		}
	}
}
?>