<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Xem extends SuperController {

	function __construct(){
		parent::__construct();
		$this->db->order_by("name", "asc");
		$this->out['languages'] = $this->db->get('languages');

		$this->out['languagesJS'] = json_encode(buildSimpleLanguageArray($this->out['languages']));

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
			if(is_numeric($id))
				$fullElement = new FullElement($this->oh,$id);
			else{
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

    		$this->out['editRight'] = ($this->session->userdata('logged_in') && ($this->user_lvl >=  $fullElement->status));
		}

		$this->out['title'] = $fullElement->main_name.' | Maping';
		$this->_loadView('show');
	}

	public function shows(){
		$this->out['title'] = 'Shows';
		$this->out['curShows'] = $this->out['shows'];
		$this->_loadView('showList',false);
	}

	function newAlternativeName(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		}
		if($_POST['name']){
    		$name = new Name($this->oh);
    		$name->name = $_POST['name'];
    		$name->language = $_POST['language'];
    		$name->element_id = $_POST['element_id'];

    		$season = $_POST['season'];
    		if($season == "all" || $season == "All" || $season == "*" || $season == '')
    			$season = -1;
    		$name->season = $season;
    		$name->save();
		}


		redirect('xem/show/'.$_POST['element_id']);
	}

	public function newSeason(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		}
		$newSeason = new Season($this->oh);
		$newSeason->element_id = $_POST['element_id'];
		$newSeason->location_id = $_POST['location_id'];
		$season = $_POST['season'];
		if($season == "all")
			$season = -1;
		$newSeason->season = $season;
		$newSeason->season_size = $_POST['season_size'];
		$newSeason->identifier = $_POST['identifier'];
		$newSeason->save();

		redirect('xem/show/'.$newSeason->element_id);
	}


	public function editSeason(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		}
		/*
		print "<pre>";
		print_r($_POST);
		print "</pre>";
		*/
		$season = new Season($this->oh, $_POST['season_id']);

		if($_POST['delete'] != true){
			$seasonNumber = $_POST['season'];
			if($seasonNumber == "all" || $seasonNumber == "*")
				$seasonNumber = -1;
			$season->season = $seasonNumber;
			$season->season_size = $_POST['size'];
			if(isset($_POST['identifier'])) // is not set on master
    			$season->identifier = $_POST['identifier'];
			$absolute_start = $_POST['absolute_start'];
			if($absolute_start == "auto")
				$absolute_start = 0;
			$season->absolute_start = $absolute_start;
			$season->episode_start = $_POST['episode_start'];
			$season->save();
		}else if($_POST['delete'] == true){
			$season->delete();
		}
		// print_o($season);
		redirect('xem/show/'.$_POST['element_id']);
	}

	function deleteShow(){
		if(!grantAcces(4)) {
			redirect('');
		}

		$element = new Element($this->oh, $_POST['element_id']);
		$element->status = 0;
		$element->save();

		redirect('xem/show/'.$_POST['element_id']);
	}
	function unDeleteShow(){
		if(!grantAcces(4)) {
			redirect('');
		}

		$element = new Element($this->oh, $_POST['element_id']);
		$element->status = 1;
		$element->save();

		redirect('xem/show/'.$_POST['element_id']);
	}

    function setLockLevel() {
		if(!grantAcces(3)) {
			redirect('');
		}

		$element = new Element($this->oh, $_POST['element_id']);
		$element->status = (int)$_POST['lvl'];
		$element->save();

		redirect('xem/show/'.$_POST['element_id']);
    }

    function clearCache() {
        if(!grantAcces(3)) {
			redirect('');
		}

		$this->oh->dbcache->clearNamespace($_POST['element_id']);

		redirect('xem/show/'.$_POST['element_id']);
    }

	function addShow(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		}
		$newName = $_POST['main_name'];
		if($newName != ""){
		    $show = getShows($this->db, $newName);
		    if(count($show) > 0 && $show != false){ // we allready have a show with that name
		        redirect('xem/show/'.$show[0]->id);
		    }else{
		        $element = new Element($this->oh);
    			$element->status = 1;
    			$element->main_name = $newName;
    			$element->save();

                // info mail
                $this->email->to('info@thexem.de');
                $this->email->subject('New Show | '.$element->main_name);
                $emailBody = $this->load->view('email/show_new', array('show'=>$element,'user_nick'=>$this->out['user_nick']), true);
                $this->email->message($emailBody);
                $this->email->send();

    			redirect('xem/show/'.$element->id);
    			return true;
		    }
		}else{
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

	//old
	public function _editElementProcces(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		}
		$element_id = $_POST['element_id'];

		// change main name
		$element = new SimpleElement($this->oh,$element_id);
		$element->main_name = $_POST['main_name'];
		$element->save();
		unset($_POST['main_name']);

		//new name and season
		if($_POST['newName']){
			$season = -1;
			if($_POST['newNameSeason'] && $_POST['newNameSeason'] != -1)
				$season = $_POST['newNameSeason'];

			$newSeason = new Season($this->db);
			$newSeason->element_id = $element_id;
			$newSeason->season = $season;
			$newSeason->save();

			$newName = new Name($this->db);
			$newName->element_id = $element_id;
			$newName->name = $_POST['newName'];
			$newName->season = $newSeason;
			$newName->save();
			unset($_POST['newName']);
			unset($_POST['newNameSeason']);
		}

		foreach($_POST as $key=>$value){
			if(strpos($key, "elementLocationNew_") !== false){

				$key = explode("_",$key);
				$location_id = $key[1];

				$season = -1;
				if($_POST["elementLocationSeasonNew_".$location_id] && $_POST["elementLocationSeasonNew_".$location_id] != "all")
					$season = $_POST["elementLocationSeasonNew_".$location_id];


				$seasonSize = -1;
				if($_POST["elementLocationSeasonNew_".$location_id] && $_POST["elementLocationSeasonNew_".$location_id] != "infinite")
					$seasonSize = $_POST["elementLocationSeasonNew_".$location_id];


				$newSeason = new Season($this->db);
				$newSeason->element_id = $element_id;
				$newSeason->season = $season;
				$newSeason->save();

				$newElementLocation = new ElementLocation($this->db);
				$newElementLocation->location = new Location($this->db, $location_id);
				$newElementLocation->element = new Element($this->db, $element_id);
				$newElementLocation->season = $newSeason;
				$newElementLocation->identifier = $value;
				$newElementLocation->seasonsize = $seasonSize;
				$newElementLocation->save();

				unset($_POST[$key]);
				unset($_POST["elementLocationSeasonNew_".$location_id]);
				continue;
			}
			if(strpos($key, "location_") !== false){
				print "----------------<br>";
				$key = explode("_",$key);
				$elementLocation_id = $key[1];
				$oldSeason = $key[2];

				if($value || ($_POST["locationSize_".$elementLocation_id] && $_POST["locationSeason_".$elementLocation_id])){
					$season = -1;
					if($_POST["locationSeason_".$elementLocation_id] && $_POST["locationSeason_".$elementLocation_id] != "all")
						$season = $_POST["locationSeason_".$elementLocation_id];

					$seasonSize = -1;
					if($_POST["locationSize_".$elementLocation_id] && $_POST["locationSize_".$elementLocation_id] != "infinite")
						$seasonSize = $_POST["locationSize_".$elementLocation_id];

					$newSeason = new Season($this->db);
					$newSeason->element_id = $element_id;
					$newSeason->season = $season;
					$newSeason->save();
					$newElementLocation = new ElementLocation($this->db, $elementLocation_id);
					print $newElementLocation->location->name.": ".$newElementLocation->identifier."<br>";
					$newElementLocation->season = $newSeason;
					$newElementLocation->identifier = $value;
					$newElementLocation->seasonsize = $seasonSize;
					$newElementLocation->save();
				}else{
					$newElementLocation = new ElementLocation($this->db, $elementLocation_id);
					$newElementLocation->delete();
				}
				continue;
			}
			if(strpos($key, "name_") !== false){
				print "----------------<br>";
				$key = explode("_",$key);
				$name_id = $key[1];
				if($value){
					$season = -1;
					if($_POST["nameSeason_".$name_id] && $_POST["nameSeason_".$name_id] != "all")
						$season = $_POST["nameSeason_".$name_id];

					$newSeason = new Season($this->db);
					$newSeason->element_id = $element_id;
					$newSeason->season = $season;
					$newSeason->save();

					$newName = new Name($this->db, $name_id);
					$newName->name = $value;
					$newName->season = $newSeason;
					$newName->save();

				}else{
					$newName = new Name($this->db, $name_id);
					$newName->delete();
				}
				continue;
			}

		}
		if(!isset($_POST['debug']))
			redirect('xem/editElement/'.$element_id);
	}
	function _addShowRule(){
		$this->out['locations'] = $this->db->get('locations');
		$this->out['shows'] = $this->db->get_where('elements',array('type'=>'show'));

		$this->load->view('top', $this->out);
		$this->load->view('addShowRule',$this->out);
		$this->load->view('bottom', $this->out);

	}

	function _editShowrule(){
		$rule_map = $this->db->get('maps');
		$this->out['rule_maps'] = array();
		foreach($rule_map->result() as $rule){
			$this->out['rule_maps'][$rule->id] = new Map($this->db, $rule->id);
		}

		if($id = $this->uri->segment(3)){
			$map = $this->out['rule_maps'][$id];
			$map->buildOffsetrules();
			$this->out['rule_map'] = $map;
		}

		$this->load->view('top', $this->out);
		$this->load->view('editShowRule',$this->out);
		$this->load->view('bottom', $this->out);
	}

	function _editShowRuleProcess(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		}
		print "<pre>";
		print_r($_POST);
		print "</pre>";

		// change human values(end,start) to machine values(-1)
		foreach($_POST as $key=>$value){
			if(($value == "end" || $value == "start" )&& (strpos($key, "from") !== false || strpos($key, "to") !== false)){
				$_POST[$key] = -1;
			}
		}

		$map_id = 0;
		if(isset($_POST['rule_map_id']))
			$map_id = $_POST['rule_map_id'];

		$newMap = new Simplemap($this->db, $map_id);
		if(isset($_POST["element_id"]))
			$newMap->element_id = $_POST["element_id"];
		if(isset($_POST["name_id"]))
			$newMap->name_id = $_POST["name_id"];
		$newMap->save();

		if(isset($_POST["origin_id"]) && isset($_POST["destination_id"])){
			$newMapLocation = new Maplocation($this->db);
			$newMapLocation->map_id = $newMap->id;
			$newMapLocation->origins = $_POST["origin_id"];
			$newMapLocation->destinations = $_POST["destination_id"];
			$newMapLocation->save();
		}
		$rule_id = 0;
		if(isset($_POST['offset_rule_id']))
			$rule_id = $_POST['offset_rule_id'];

		$newOffsetrule = new Offsetrule($this->db, $rule_id);
		$newOffsetrule->map_id = $newMap->id;

		$newOffsetrule->season_from = $_POST["season_from"];
		$newOffsetrule->season_to = $_POST["season_to"];
		$newOffsetrule->season_offset = $_POST["season_offset"];

		$newOffsetrule->episode_from = $_POST["episode_from"];
		$newOffsetrule->episode_to = $_POST["episode_to"];
		$newOffsetrule->episode_offset = $_POST["episode_offset"];
		$newOffsetrule->absolute_episode_offset = $_POST["absolute_ep_offset"];
		$newOffsetrule->save();


		if(isset($_POST["delete"])){
			$offset_rule_id = $_POST['offset_rule_id'];
			$this->db->delete("offsetrules", array("id"=>$offset_rule_id));
		}

		redirect('xem/editShowRule/'.$newMap->id);

	}



}




/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */