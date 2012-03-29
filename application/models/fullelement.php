<?
class FullElement{

	public $db;

	public $id;
	public $main_name;
	public $status;
	private $objArrays = array();


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

			$this->locations = buildLocations($this->oh);

			$this->id = $elementRow['id'];
			$this->main_name = $elementRow['main_name'];
			$this->entity_order = $elementRow['entity_order'];
			$this->status = (int)$elementRow['status'];

			$this->seasons = $this->build("seasons",$this->id);
			$this->names = $this->build("names",$this->id);
			$this->directrules = $this->build("directrules",$this->id);
			$this->passthrus = $this->build("passthrus",$this->id);

			$this->cacheSize = $this->oh->dbcache->getNamspaceSize($this->id);
			//$this->getJSONDirectrules();
		}
	}

	private function build($type,$id){

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

	public function getdirectLink($location_id, $season){
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

	function sortedEntitys(){
		if($this->entity_order){
			$newOrder = array();
			foreach(explode(",",$this->entity_order) as $locID){
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
			return json_encode($out);
		}else{
			return "{}";
		}

	}

}
?>