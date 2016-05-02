<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Api extends CI_Controller {

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


	public function saveCon(){
		if(!hasEditRight($this->oh, $_POST['elementID'])) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}

		/*
		Reg successful for: ?cmd=newCon&fromLiID=tvdb_1_5&toLiID=anidb_1_3&conID=3&paperID=paper_tvdb_anidb&fID=2&fName=tvdb&fSeason=1&fEpisode=1&tID=3&tName=anidb&tEpisode=3
		*/
		$origin = new Location($this->oh, $_POST['fID']);
		$destination = new Location($this->oh, $_POST['tID']);

		$directrule = new Directrule($this->oh);
		array('origin_id','destination_id', 'element_id', 'name_id', 'origin_season', 'origin_episode', 'destination_season',	'destination_episode');
		$directrule->origin_id = $_POST['fID'];
		$directrule->destination_id = $_POST['tID'];
		$directrule->element_id = $_POST['elementID'];
		$directrule->name_id = null;

		$directrule->origin_season = $_POST['fSeason'];
		$directrule->origin_episode = $_POST['fEpisode'];
		$directrule->destination_season = $_POST['tSeason'];
		$directrule->destination_episode = $_POST['tEpisode'];
		$directrule->save();
		if($directrule->id){
			$directrule->reverse();
			$directrule->save();
			if($directrule->id){
				$this->_fullOut('success',$_POST);
				return true;
			}
		}
		$this->_fullOut('failure',$_POST);


	}
	public function deleteCon(){
		if(!hasEditRight($this->oh, $_POST['elementID'])) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}
		$directrule = new Directrule($this->oh);
		$directrule->origin_id = $_POST['fID'];
		$directrule->destination_id = $_POST['tID'];
		$directrule->element_id = $_POST['elementID'];
		$directrule->name_id = null;

		$directrule->origin_season = $_POST['fSeason'];
		$directrule->origin_episode = $_POST['fEpisode'];
		$directrule->destination_season = $_POST['tSeason'];
		$directrule->destination_episode = $_POST['tEpisode'];


		if($directrule->delete()){
			$directrule->reverse();
			if($directrule->delete()){
				$this->_fullOut('success',$_POST);
				return true;
			}
		}
		$this->_fullOut('failure',$_POST);
	}

	public function savePassthru(){
		if(!hasEditRight($this->oh, $_POST['element_id'])) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}
		$orign = new Location($this->oh);
		$orign->name = $_POST['origin'];
		$orign->load();
		$destination = new Location($this->oh);
		$destination->name = $_POST['destination'];
		$destination->load();
		if($orign->id && $destination->id){

			$newPass = new Passthru($this->oh, (int)$_POST['id']);
			$newPass->origin_id = $orign->id;
			$newPass->destination_id = $destination->id;
			$newPass->element_id = $_POST['element_id'];
			$newPass->load();
			$newPass->type = $_POST['type'];
			if($newPass->save()){
				$newPass->reverse();
				unset($newPass->type);
				$newPass->load();
				$newPass->type = $_POST['type'];
				if($newPass->save()){
					$this->_fullOut('success',$_POST);
					return true;
				}
			}
		}

		$this->_fullOut('failure',$_POST);
	}

	public function deletePassthru(){
		if(!$this->session->userdata('logged_in')) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}

		$orign = new Location($this->oh);
		$orign->name = $_POST['origin'];
		$orign->load();
		$destination = new Location($this->oh);
		$destination->name = $_POST['destination'];
		$destination->load();

		if($orign->id && $destination->id){
			$newPass = new Passthru($this->oh);

			$newPass->origin_id = $orign->id;
			$newPass->destination_id = $destination->id;
			$newPass->element_id = $_POST['element_id'];

			if($newPass->delete()){

				$newPass->reverse();
				if($newPass->delete()){
					$this->_fullOut('success',$_POST);
					return true;
				}
			}


		}

		$this->_fullOut('failure',$_POST);
	}
	function saveEntityOrder(){
		if(!hasEditRight($this->oh, $_POST['element_id'])) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}
		$element = new Element($this->oh, $_POST['element_id']);
		$element->entity_order = $_POST['order'];
		$element->save();

		$this->_fullOut('success',$_POST);

	}

	function saveNewName(){
		if(!hasEditRight($this->oh, $_POST['element_id'])) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}
		$element = new Element($this->oh, $_POST['element_id']);
		$element->main_name = $_POST['name'];
		$element->save();

		$this->_fullOut('success',$_POST);
	}

	function saveAltenativeName(){
		if(!hasEditRight($this->oh, $_POST['element_id'])) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}
		$name = new Name($this->oh, $_POST['name_id']);
		$name->name = $_POST['name'];
		$name->save();

		$this->_fullOut('success',$_POST);
	}

	function deleteAltenativeName(){
		if(!hasEditRight($this->oh, $_POST['element_id'])) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}
		$name = new Name($this->oh, $_POST['name_id']);
		$name->delete();

		$this->_fullOut('success',$_POST);
	}

	function showRevision(){
		if(!hasEditRight($this->oh, $_POST['element_id'])) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		}
		$out = array();

		$obj_id = $_POST['element_id'];

		$result = $this->db->query("SELECT * FROM `history` WHERE `element_id` = '".$obj_id."' ORDER BY `time` DESC");
		if(rows($result)){
			foreach($result->result() as $curRevsion){
				$newRaw = json_decode($curRevsion->new_data,true);
				$oldRaw = json_decode($curRevsion->old_data,true);
				$diff = array_diff($newRaw,$oldRaw);
				$old = array();
				$new = array();
				if(count($diff))
					foreach($diff as $changedKey=>$newValue){
						$new[$changedKey] = $newRaw[$changedKey];
						if(isset($oldRaw[$changedKey]))
							$old[$changedKey] = $oldRaw[$changedKey];
						else
							$old[$changedKey] = "-";
					}
				else{
					$old = $oldRaw;
					$new = $newRaw;
				}


				$userName = userNameByID($this->db,$curRevsion->user_id); // this might make it very slow
				$out[] = array("time"=>$curRevsion->time,
								"revision"=>$curRevsion->revision,
								"type"=>$curRevsion->obj_type,
								"action"=>$curRevsion->action,
								"user"=>$userName,
								"diff"=>json_encode($diff),
								"old"=>json_encode($old),
								"new"=>json_encode($new));
			}
		}

		$this->_fullOut('success',$out);
	}

	function autocomplete(){
		if(!isset($_POST['term'])){
			$this->_fullOut('failure',array(),'no search term');
			return false;
		}
		$term = $_POST['term'];
		// like
		$out = array();

		$tmp = $this->db->query('SELECT `main_name` FROM `elements` WHERE (`main_name` LIKE "%'.$term.'%") AND `status` > 0  AND `parent` = 0 ORDER BY `main_name`');
		if(rows($tmp))
			foreach($tmp->result() as $curRow){
				array_push($out,$curRow->main_name);
			}
		$tmp = $this->db->query('SELECT DISTINCT `name` FROM `names` JOIN `elements` AS p ON names.element_id=p.id AND p.status > 0 AND p.parent = 0 WHERE name LIKE "%'.$term.'%" ORDER BY `name`');
		if(rows($tmp))
			foreach($tmp->result() as $curRow){
				array_push($out,$curRow->name);
			}

		$this->_fullOut('success',$out);
		return;

	}

	function getLanguagesForSelect(){
	   $out = buildSimpleLanguageArray();
	   $out['selected'] = 'gb';
	   $this->output->set_content_type('application/json')->set_output(json_encode($out));
    }

	function getLanguages(){
	    $langs = $this->db->get('languages');
	    // i will let this crash when no languages are set a check does not make sence in running env
	    $out = $langs->result_array();
		$this->_fullOut('success',$out);
    }

    function nameUpdate(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		}
        $name = new Name($this->oh, $_POST['name_id']);
        if(isset($_POST['name']) && $_POST['name'] != '') {
            $name->name = $_POST['name'];
        }
        if(isset($_POST['language']))
            $name->language = $_POST['language'];
        $name->save();

		$this->_fullOut('success',$_POST);

    }
    function savePage() {
        if(!userHasLvl(4)) {
			$this->_fullOut('failure',array(),"not enough rights");
			return false;
		};

		$page = $_POST['page'];
		$content = $_POST['content'];
        $c = new Content($this->oh, $page);
        $c->content = $content;
        $c->save();

		$this->_fullOut('success',$_POST);
    }

    /*
    {RESULT_SUCCESS:"success",
        RESULT_FAILURE:"failure",
        RESULT_TIMEOUT:"timeout",
        RESULT_ERROR:"error",
        RESULT_DENIED:"denied",
    }
	*/

	function _fullOut($status, $data ,$message=''){
		if(!$data)
			$data=array();
		$this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$status,'data'=>$data,'message'=>$message)));
	}



	//old
	function episodeAdress(){
		$out = array();

		$show = $_GET['show'];
		$originName = $_GET['origin'];
		$destinationName = $_GET['destination'];
		$oSeason = $_GET['season'];
		$oEpisode = $_GET['episode'];
		$oAbEpisode = false;
		if(isset($_GET['absoluteEp']))
			$oAbEpisode = $_GET['absoluteEp'];

		try {
			$originRes = $this->db->get_where("location",array("name"=>$originName));
			$originRes = $originRes->result();
			if(!isset($originRes[0]))
				throw new Exception("no origin");
			$origin = $originRes[0]->id	;

			$destinationRes = $this->db->get_where("location",array("name"=>$destinationName));
			$destinationRes = $destinationRes->result();
			if(!isset($destinationRes[0]))
				throw new Exception("no destination");
			$destination = $destinationRes[0]->id;

			$element = $this->db->get_where("element-map",array("location_id"=>$origin,"id"=>$show));
			$element = $element->result();
			if(!isset($element[0]))
				throw new Exception("no show");
			$elementID = $element[0]->element_id;

			$sRuleID = 0;

			$out['names'] = array();
			$seNames = $this->db->get_where("names",array("element"=>$elementID));
			if($seNames)
				if($seNames->num_rows()){
					foreach($seNames->result() as $name){
						if($name->season == $oSeason)
							$out['names'][] = $name->name;
					}
				}
			$rule = false;
			$rule_map = $this->db->get_where("rule-map",array("origin"=>$origin,"destination"=>$destination,"element"=>$elementID));
			$rule_map = $rule_map->result();
			if(!isset($rule_map[0]))
				throw new Exception("no rule for origin destination combination for thsi show");
			$rule_mapID = $rule_map[0]->id;

			$rules = $this->db->get_where("offset-rule",array("rule_map"=>$rule_mapID));
			if($rules){
				if($rules->num_rows()){
					$rule = $this->_getBestRule($rules->result(), $oSeason, $oEpisode);
				}
			}
			if(!isset($rule)||!$rule){
				$rule = new OffsetRule($this->db);
				$out['note'] = "default|no rule found";
			}
			$out['season'] = $rule->sOff + $oSeason;
			$out['episode'] = $rule->eOff + $oEpisode;


		} catch (Exception $e) {
		    echo 'Exception abgefangen: ',  $e->getMessage(), "\n";
		}

		print json_encode($out);


	}
	function sceneExcpetions(){
		$locations = $this->db->get('locations');
		$tvdb_id = 0;
		foreach($locations->result() as $loc){
			if($loc->name == "tvdb"){
				$tvdb_id = $loc->id;
			}
		}

		$this->output->set_header("Content-Type: text/plain charset=UTF-8\r\n");

		$el_loc_map = $this->db->get_where('element-map',array("location_id"=>$tvdb_id));
		foreach($el_loc_map->result() as $curMap){
			$names = $this->db->get_where('names',array('element'=>$curMap->element_id));
			$namesStrings = array();
			if($names){
				if($names->num_rows()){
					foreach($names->result() as $name){
						if($name->season == -1)
							$namesStrings[] = str_replace("'", "\'",$name->name);
					}
				}
			}
			if(count($namesStrings) == 0)
				continue;
			echo $curMap->id.": ";
			echo "'".implode("','", $namesStrings)."',\n";
		}
	}


	private function _getBestRule($rules, $season=-1, $episode=-1, $ab_episode=-1){

		$bestRule = false;
		$bestScore = 0;
		foreach($rules as $curRule){
			$curScore = 0;
			$curRule = new OffsetRule($this->db, $curRule);
			if($this->_checkRange($curRule->sFrom, $curRule->sTo, $season))
				$curScore += $this->_getScore($curRule->sFrom, $curRule->sTo, $season);
				if($this->_checkRange($curRule->eFrom, $curRule->eTo, $episode))
					$curScore += $this->_getScore($curRule->sFrom, $curRule->sTo, $season);
			if($curScore > $bestScore){
				$bestScore = $curScore;
				$bestRule = $curRule;
			}
		}

		return $bestRule;
	}

	private function _checkRange($from,$to,$pivot){
		return (($from <= $pivot && $pivot <= $to)||($from == -1 && $pivot <= $to)||($from <= $pivot && -1 == $to)||($pivot == -1));
	}

	private function _getScore($from,$to,$pivot){
		$score = 0;
		if($from = $pivot && $pivot = $to) // excat match
			$score += 1;
		if(($from == $pivot)||($pivot == $to)) // equal to one side
			$score += 1;
		if($this->_checkRange($from,$to,$pivot)) // every other match
			$score += 1;
		return $score;

	}


}
