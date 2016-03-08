<?
class FullElement{

	public $db;

	public $id;
	public $main_name;
	public $status;
	public $parent;
	private $objArrays = array();

	public $element;
	public $isDraft = false;
	public $parentElement = null;

	public $seasons = array();
	public $names = array();
	public $locations = array();

	public $entity_order = array();
	public $directrules = array();
	public $passthrus = array();

	public $cacheSize = 0;


	function __construct($oh, $element_id){
		$this->oh = $oh;
		$this->db = $oh->db;
		$this->cache = $oh->cache;
		$this->history = $oh->history;

		$elementRow = $this->db->get_where('elements',array('id' => $element_id));
		if(rows($elementRow)){
			$elementRow = $elementRow->row_array();

			$this->element = new Element($this->oh, $elementRow['id']);
            $this->isDraft = $this->element->isDraft();
            if($this->isDraft)
                $this->parentElement = new Element($this->oh, $this->element->parent);

			$this->locations = buildLocations($this->oh);

			$this->id = $elementRow['id'];
			$this->main_name = $elementRow['main_name'];
			$this->entity_order = $elementRow['entity_order'];
			$this->status = (int)$elementRow['status'];
			$this->parent = (int)$elementRow['parent'];
            $this->last_modified = $elementRow['last_modified'];
            $this->created = $elementRow['created'];

			$this->seasons = $this->build("seasons",$this->id);
			$this->names = $this->build("names",$this->id);
			$this->directrules = $this->build("directrules",$this->id);
			$this->passthrus = $this->build("passthrus",$this->id);

			$this->cacheSize = $this->oh->dbcache->getNamspaceSize($this->id);
			//$this->getJSONDirectrules();
		}
	}

	private function build($type, $id){

		if(endswith($type,"s")){
			$table = $type;
			$name =  substr($type,0,-1);
		}else{
			$table = $type."s";
			$name = $type;
		}
        if($type == 'seasons')
    		$this->db->order_by("season", "asc");
		$rows = $this->db->get_where($table,array("element_id"=>$id));
		if(rows($rows)){
			$out = array();
			foreach($rows->result_array() as $curRow){
				$out[$curRow['id']] = new $name( $this->oh, $curRow['id']);
			}
			$this->objArrays[$table] = $out;
			return $out;
		}else
			return array();

	}


	function seasonForLocationId($id){
		$result = array();
		if($this->seasons)
			foreach($this->seasons as $curElementLocation){
				if($curElementLocation->location->id == $id){
					$result[] = $curElementLocation;
				}
			}
		usort($result, "seasonSort");
		return $result;
	}

	function namesForSeason($season){
		$result = array();
		foreach($this->names as $curName){
			if($curName->season == $season){
				$result[] = $curName;
			}
		}
		return $result;
	}
	function groupedNames($onlyName=false){
		$result = array();
		foreach($this->names as $curNames){
			if(!isset($result[$curNames->season])){
				$result[$curNames->season] = array();
			}
			if ($onlyName){
    			if(!isset($result[$curNames->season][$curNames->language])){
    				$result[$curNames->season][$curNames->language] = array();
    			}
			    $result[$curNames->season][$curNames->language][] = $curNames->name;
			}else
			    $result[$curNames->season][] = $curNames;
		}
		uksort($result, "seasonKeySort");
		return $result;
	}


	public function __toString(){
    	$out = array();
    	$out['id'] = $this->id;
    	$out['main_name'] = $this->main_name;

    	foreach($this->objArrays as $name=>$objs){
    		$out[$name] = array();
    		foreach($objs as $curName=>$obj){
	    		$curString = (string)$obj;
	    		$newString = "";
	    		foreach(preg_split("/(\r?\n)/", $curString) as $line){
				    $newString .= "     ".$line."\n";
				}

 		   		$out[$name][$curName] = $newString;
			}
    	}
        return print_r($out, true);
    }
    /*
     * this was a try to incorporate urls that use identifiers from different locations then itself but it has an error
     * when the last current season identifier is not set it will use the identifier from the previous location
	public function getdirectLink_old($location_id, $season){
		$url = $this->locations[$location_id]->show_url;

	    $lastIdentifier = '';
		foreach($this->seasons as $curSeason){
		    if($curSeason->identifier)
		        $lastIdentifier = $curSeason->identifier;
            log_message('debug', $curSeason->season.'-'.$lastIdentifier);
			$newUrl = str_replace("{".$curSeason->location->name."}","",$url);
			if($newUrl != $url && $curSeason->season == $season){
				return str_replace("{".$curSeason->location->name."}", $lastIdentifier,$url);
			}
		}
	}
	*/
	public function getdirectLink($location_id, $identifier) {
		$url = $this->locations[$location_id]->show_url;

		return str_replace("{".$this->locations[$location_id]->name."}", $identifier,$url);
	}


	function sortedEntitys(){
		if($this->entity_order){
			$newOrder = array();
			foreach(explode(",",$this->entity_order) as $locID){
			    if(isset($this->locations[$locID]))
    				$newOrder[$locID] = $this->locations[$locID];
			}
			return $newOrder;
		}else
			return $this->locations;
	}

	function getJSONDirectrules(){
		/*
		"tvdb_1_1_anidb_1_2":{"fid":"tvdb","fs":1,"fe":1,"tid":"anidb","ts":1,"te":2}
		*/
		$out = array();
		if($this->directrules){
			foreach($this->directrules as $curRule){
			    if(isset($this->locations[$curRule->origin_id]) && isset($this->locations[$curRule->destination_id])){
    				$fID = $this->locations[$curRule->origin_id]->name;
    				$tID = $this->locations[$curRule->destination_id]->name;
    				$fs = $curRule->origin_season;
    				$fe = $curRule->origin_episode;

    				$ts = $curRule->destination_season;
    				$te = $curRule->destination_episode;

    				$key = $fID."_".$fs."_".$fe."_".$tID."_".$ts."_".$te;
    				$out[$key] = array("fid"=>$fID,
    									"fs"=>(int)$fs,
    									"fe"=>(int)$fe,
    									"tid"=>$tID,
    									"ts"=>(int)$ts,
    									"te"=>(int)$te);
			    }

			}
			return json_encode($out);
		}else{
			return "{}";
		}

	}
	function getJSONPassthrus(){
		/*
		"passthru_xmaster_scene":{"fid":"xmaster","tid":"scene"}
		*/
		$out = array();
		if($this->passthrus){
			foreach($this->passthrus as $curRule){
			    if(isset($this->locations[$curRule->origin_id]) && isset($this->locations[$curRule->destination_id])){
    				$fID = $this->locations[$curRule->origin_id]->name;
    				$tID = $this->locations[$curRule->destination_id]->name;
    				$type = $curRule->type;
    				$pid = $curRule->id;

    				$key = "passthru_".$fID."_".$tID;
    				$out[$key] = array("fid"=>$fID,
    									"tid"=>$tID,
    									"type"=>$type,
    									"id"=>$pid);
			    }

			}
			return json_encode($out);
		}else{
			return "{}";
		}

	}

	function createDraft(){
        log_message('debug', "Creating draft for ".$this->id);
	    # TODO: first check if we already have a draft
		$draft_id = $this->element->createCopy($this->id);

	    foreach($this->seasons as $id=>$cur_obj){
	        $cur_obj->createCopy($draft_id);
	    }
	    foreach($this->names as $id=>$cur_obj){
	        $cur_obj->createCopy($draft_id);
	    }
	    foreach($this->directrules as $id=>$cur_obj){
	        $cur_obj->createCopy($draft_id);
	    }
	    foreach($this->passthrus as $id=>$cur_obj){
	        $cur_obj->createCopy($draft_id);
	    }
        log_message('debug', "Draft id for ".$this->id." is ".$draft_id);

        $this->history->deleteHistoryForElement($draft_id);
        $this->history->copyHistoryFromTo($this->id, $draft_id);
        $this->history->createEvent('create_draft', $this->element);

        return $draft_id;

	}

	private function getDraft(){
	    $id = 0;
	    $drafts = $this->db->get_where('elements',array('parent'=>$this->id));
		if(rows($drafts)){
		    foreach ($drafts->result_array() as $cur_draft) {
		        if($cur_draft['status'] > 0){
					$id = $cur_draft['id'];
					break;
		        }
		    }
		}
		return $id;
	}

    function draftChangesCount() {
        if(!$this->isDraft) {
            $draft_id = $this->getDraft();
        } else {
            $draft_id = $this->id;
        }

        # get id latest create_draft
        $query = $this->db->query("SELECT `id` FROM `history` WHERE `element_id` = '".$draft_id."' AND `action` = 'create_draft' ORDER BY `time` DESC LIMIT 1");
        $data = $query->result_array();
        if ($query->num_rows() == 1) {
            # query for entries since create_draft id
            $query = "SELECT * FROM `history` WHERE `element_id` = '".$draft_id."' AND `id` > ".$data[0]['id'];
            $history_entries = $this->db->query($query);
            $count = $history_entries->num_rows();
        } else {
            $count = 0;
        }
        return $count;
    }

}
?>