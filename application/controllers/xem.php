<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Xem extends SuperController {

	function __construct(){
		parent::__construct();
		$this->db->order_by("name", "asc");
		$this->out['languages'] = $this->db->get('languages');

		$this->out['languagesJS'] = json_encode(buildSimpleLanguageArray($this->out['languages']));
		$this->out['is_draft'] = false;

		//email stuff
	    $this->load->library('email');
        $this->email->from('info@thexem.de', 'XEM');
	}

	public function index(){
		redirect('xem/shows');

	}

	public function show(){
		$fullElement = null;
		if($id = $this->uri->segment(3)){
			if(is_numeric($id)){
				$fullElement = new FullElement($this->oh,$id);
    			if($fullElement->isDraft){
    			    redirect('xem/draft/'.$fullElement->parentElement->id);
    			    return false;
    			}
			}else{
				$id = urldecode($id);
				$tmp = $this->db->get_where('elements',array('main_name'=>$id));
				if(rows($tmp)){
					$tmp = getFirst($tmp);
					$fullElement = new FullElement($this->oh, $tmp['id']);
				}
			}
		}
		if(!$fullElement){
			redirect('xem/shows');
			return false;
		}else{
			$this->out['fullelement'] = $fullElement;

    		$this->out['editRight'] = ($this->session->userdata('logged_in') && ($this->user_lvl >=  $fullElement->status) && ($this->user_lvl >= 5 || $fullElement->isDraft));
		}

		$this->out['title'] = $fullElement->main_name.' | Mapping';
		$this->_loadView('show');
	}

	public function adminShow(){
		if(!grantAccess(4)) {
			redirect('xem/shows');
			return false;
		}
        $id = $this->uri->segment(3);
        $fullElement = new FullElement($this->oh,$id);
		$this->out['fullelement'] = $fullElement;
		$this->out['editRight'] = ($this->session->userdata('logged_in') && ($this->user_lvl >=  $fullElement->status) && ($this->user_lvl >= 5 || $fullElement->isDraft));
		$this->out['title'] = $fullElement->main_name.' | Mapping';
		$this->_loadView('show');
	}

	public function draft(){
	    $fullElement = null;

	    # we have to get the draft id
		if($id = $this->uri->segment(3)){
			if(is_numeric($id)){
			    $drafts = $this->db->get_where('elements',array('parent'=>$id));
				if(rows($drafts)){
				    foreach ($drafts->result_array() as $cur_draft) {
				        if($cur_draft['status'] > 0){
        					$fullElement = new FullElement($this->oh, $cur_draft['id']);
				        }
				    }
				}
	            if(!$fullElement){
            		if(grantAccess(1)) {
        	    		redirect('xem/createDraft/'.$id);
            		}else{
            			redirect('xem/show/'.$id);
            		}
        	    	return false;
				}
			}
		}
	    if(!$fullElement){
			redirect('xem/shows');
			return false;
		}else{
			$this->out['fullelement'] = $fullElement;

    		$this->out['editRight'] = ($this->session->userdata('logged_in') && ($this->user_lvl >=  $fullElement->status));
		}

		$this->out['title'] = $fullElement->main_name.' | Mapping';
		$this->_loadView('show');
	}

	public function createDraft() {
		if(!grantAccess(1)) {
			redirect('user/login');
			return false;
		}

		if($id = $this->uri->segment(3)){
			if(is_numeric($id)){
			    $e = new Element($this->oh, $id);
			    if(!$e->status > 0){ // dont allow creation of drafts for deleted stuff
    				redirect('xem/shows');
			    }
			    if($e->parent){ // check if this is already a draft
    				redirect('xem/draft/'.$e->parent);
    				return false;
			    }
                log_message('debug', "Initiating create draft for ".$id);
				$fullElement = new FullElement($this->oh, $id);
				$new_draft = $fullElement->createDraft();
				redirect('xem/draft/'.$id);
				return false;
		    }
		}else{
			redirect('xem/shows');
		}
	}
    public function makePublic(){
		if(!grantAccess(4)) {
			redirect('user/login');
			return false;
		}
        if($id = $this->uri->segment(3)){
			if(is_numeric($id)){
				$fullElement = new FullElement($this->oh, $id);
				$oldPublic = $fullElement->parentElement;
				if($oldPublic){
    				$oldPublic->status = -1;
    				$oldPublic->parent = $id;
    		        $oldPublic->save();
                    // newPublic is pretty much "this"
    		        $newPublic = $fullElement->element;
    		        $newPublic->parent = 0;
    		        $newPublic->save();

                    $this->oh->history->createEvent('draft_accepted', $newPublic);
    				redirect('xem/show/'.$id);
    				return true;
				}else{ // this was not a draft
    				redirect('xem/show/'.$id);
    				return false;
				}
		    }
		}else{
			redirect('xem/shows');
		}
    }

    public function requestPublic() {
        if(!grantAccess(1)) {
			redirect('user/login');
			return false;
		}
        if($id = $this->uri->segment(3)){
		    if(is_numeric($id)){
		        $e = new Element($this->oh, $id);
		        if(!$e->parent){ // this is no draft !
            		redirect('xem/show/'.$id);
            		return false;
		        }
		        $this->oh->history->createEvent('public_request', $e);
		        $e->status = 4;
		        $e->save();

                $emailBody = $this->load->view('email/draft_request', array('show'=>$e,'user_nick'=>$this->out['user_nick']), true);
                foreach ($this->simpleloginsecure->getUserBasedOn(4, 'email_public_request') as $cur_user) {
    		        // info mail
                    $this->email->to($cur_user['user_email']);
                    $this->email->subject('Draft Public Request | '.$e->main_name);
                    $this->email->message($emailBody);
                    $this->email->send();
                    log_message('debug', 'Sending email_public_request to '. $cur_user['user_email']);
                    //log_message('debug', $this->email->print_debugger());
                }

        		redirect('xem/draft/'.$e->parent);
        		return true;
		    }
		}
		redirect('xem/shows');

    }

	public function shows(){
		$this->out['title'] = 'Shows';
		$this->out['curShows'] = $this->out['shows'];
		$this->_loadView('showList',false);
	}

	public function adminShows(){
        if(!grantAccess(4)) {
			redirect('xem/shows');
			return false;
		}
		$shows = $this->db->get('elements');
		$finalShows = array();
		if(rows($shows)){
		    foreach ($shows->result() as $cur_show) {
		        $id = (int)$cur_show->id;
		        $parent = (int)$cur_show->parent;
		        if($parent){
		            if(!isset($finalShows[$parent])){
		                $finalShows[$parent] = array();
		            }
		            if(!isset($finalShows[$parent]['draft'])){
		                $finalShows[$parent]['draft'] = array();
		            }
		            $finalShows[$parent]['draft'][$id] = $cur_show;
		        }else{
		            if(!isset($finalShows[$id])){
		                $finalShows[$id] = array();
		            }
		            $finalShows[$id]['public'] = $cur_show;
		        }
		    }
		}

		$this->out['title'] = 'Shows';
		$this->out['curShows'] = $finalShows;
		$this->_loadView('adminShowList');
	}

    function newAlternativeName(){
        if(!$this->session->userdata('logged_in')) {
            redirect('user/login');
        }
        // trim all post data
        if(!empty($_POST)) {
            $_POST = array_map("trim", $_POST);
        }

        if(isset($_POST['name']) && $_POST['name'] != '') {
            $name = new Name($this->oh);
            $name->name = $_POST['name'];
            $name->language = $_POST['language'];
            $name->element_id = $_POST['element_id'];

            $season = $_POST['season'];
            if(strtolower($season) == "all" || $season == "*" || $season == '') {
                $season = -1;
            }
            $name->season = $season;
            $name->save();
        }

        $e = new Element($this->oh, $_POST['element_id']);
        if($e->isDraft()) {
            redirect('xem/draft/' . $e->parent);
        } else {
            redirect('xem/show/' . $_POST['element_id']);
        }
    }

    public function newSeason() {
        if(!$this->session->userdata('logged_in')) {
            redirect('user/login');
        }
        // trim all post data
        if(!empty($_POST)) {
            $_POST = array_map("trim", $_POST);
        }
        $valid = true;

        $newSeason = new Season($this->oh);
        $newSeason->element_id = $_POST['element_id'];
        $newSeason->location_id = $_POST['location_id'];

        $season = $_POST['season'];
        if(strtolower($season) == "all" || $season == "*" || $season == '') {
            $season = -1;
        }
        $newSeason->season = $season;

        $newSeason->season_size = $_POST['season_size'];
        if(!preg_match('/^\d+$/', $_POST['season_size'])) {
            // if value is not a number, just set to 0
            $newSeason->season_size = 0;
        }
        if(isset($_POST['identifier'])) {
            $newSeason->identifier = $_POST['identifier'];
            if(($_POST['identifier'] != "") && (!preg_match('/^\d+$/', $_POST['identifier']))) {
                $valid = false;
            }
        }
        // make sure our data is safe to safe
        if($valid) {
            $newSeason->save();
        }

        $e = new Element($this->oh, $_POST['element_id']);
        if($e->isDraft()) {
            redirect('xem/draft/' . $e->parent);
        } else {
            redirect('xem/show/' . $_POST['element_id']);
        }
    }


    public function editSeason() {
        if(!$this->session->userdata('logged_in')) {
            redirect('user/login');
        }
        // trim all post data
        if(!empty($_POST)) {
            $_POST = array_map("trim", $_POST);
        }
        $valid = true;

        $season = new Season($this->oh, $_POST['season_id']);

        if($_POST['delete'] != true) {
            $seasonNumber = $_POST['season'];
            if(strtolower($seasonNumber) == "all" || $seasonNumber == "*" || $seasonNumber == '') {
                $seasonNumber = -1;
            }
            $season->season = $seasonNumber;

            $season->season_size = $_POST['size'];
            if(!preg_match('/^\d+$/', $_POST['size'])) {
                $valid = false;
            }
            if(isset($_POST['identifier'])) {
                $season->identifier = $_POST['identifier'];
                if(($_POST['identifier'] != "") && (!preg_match('/^\d+$/', $_POST['identifier']))) {
                    $valid = false;
                }
            }
            $absolute_start = $_POST['absolute_start'];
            if($absolute_start == "auto" || $seasonNumber == 0) {
                $absolute_start = 0;
            }
            $season->absolute_start = $absolute_start;
            $season->episode_start = $_POST['episode_start'];

            // make sure our data is safe to safe
            if($valid) {
                $season->save();
            }

        } else {
            if($_POST['delete'] == true) {
                $season->delete();
            }
        }

        // print_o($season);
        $e = new Element($this->oh, $_POST['element_id']);
        if($e->isDraft()) {
            redirect('xem/draft/' . $e->parent);
        } else {
            redirect('xem/show/' . $_POST['element_id']);
        }
    }

	function deleteShow(){
		if(!grantAccess(4)) {
			redirect('user/login');
            return false;
		}

        if($id = $this->uri->segment(3)){
            log_message('debug', 'Deleting show/draft '.$id);
    		$element = new Element($this->oh, $id);
    		$element->status = 0;
    		$element->save();
            if($element->parent){
                log_message('debug', 'Deleted draft '.$id.' going back to show'.$element->parent);
                redirect('xem/show/'.$element->parent);
                return true;
            }else{
                log_message('debug', 'Deleted show '.$id);
                $this->oh->dbcache->clear_all_cache(); // purge external cache
                redirect('xem/show/'.$id);
                return true;
            }
        }else
            redirect('');
	}
	function unDeleteShow(){
		if(!grantAccess(4)) {
			redirect('user/login');
            return false;
		}

        if($id = $this->uri->segment(3)){
    		$element = new Element($this->oh, $id);
    		$element->status = 1;
    		$element->save();
            $this->oh->dbcache->clear_all_cache(); // purge external cache
    		redirect('xem/show/'.$id);
        }else
            redirect('');
	}

    function setLockLevel() {
		if(!grantAccess(3)) {
			redirect('user/login');
            return false;
		}

		$element = new Element($this->oh, $_POST['element_id']);
		$element->status = (int)$_POST['lvl'];
		$element->save();

		redirect('xem/show/'.$_POST['element_id']);
    }

    function clearCache() {
        if(!grantAccess(3)) {
			redirect('user/login');
            return false;
		}
        if($id = $this->uri->segment(3)){
    		$this->oh->dbcache->clearNamespace($id);
            $this->oh->dbcache->clear_all_cache(); // purge external cache
    		redirect('xem/show/'.$id);
        }else
            redirect('');
    }

    function addShow() {
        if(!$this->session->userdata('logged_in')) {
            redirect('user/login');
            return false;
        }
        $newName = trim($_POST['main_name']);
        if($newName != "") {
            $show = getShows($this->db, $newName);
            if(count($show) > 0 && $show != false && !isset($_POST['forceAdd'])) { // we already have a show with that name
                $this->out['searchQeuery'] = $newName;
                $this->out['curShows'] = $show;
                $this->out['forceAdd'] = true;
                $this->_loadView('showList', false);
                return false;
            } else {
                $element = new Element($this->oh);
                $element->status = 1;
                $element->main_name = $newName;
                $element->created = date('c');
                $element->save();

                $emailBody = $this->load->view('email/show_new', array('show' => $element, 'user_nick' => $this->out['user_nick']), true);
                foreach ($this->simpleloginsecure->getUserBasedOn(4, 'email_new_show') as $cur_user) {
                    // info mail
                    $this->email->to($cur_user['user_email']);
                    $this->email->subject('New Show | ' . $element->main_name);
                    $this->email->message($emailBody);
                    $this->email->send();
                    log_message('debug', 'Sending email_new_show to ' . $cur_user['user_email']);
                }
                redirect('xem/show/' . $element->id);
                return true;
            }
        } else {
            redirect('xem/shows/');
            return true;
        }

    }

	function changelog(){

		$out = array();

		$obj_id = $this->uri->segment(3);
		if(!is_numeric($obj_id)){
			redirect('xem/shows/');
		}

		$changelog = new Changelog($this->oh, $obj_id);
		$element = new Element($this->oh, $obj_id);

		$this->out['element'] = $element;
		$this->out['events'] = $changelog->events;
		$this->_loadView('changelog');
	}


}
