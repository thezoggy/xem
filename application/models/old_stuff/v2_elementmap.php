<?php
class ElementMap{
	// property declaration
	public $id;
	public $name;
	public $element;
    public $ids = array();
    public $seasonIDs = array();
    
	function __construct($db, $id, $name, $element){
		$this->db = $db;
		$this->id = $id;
		$this->name = $name;
		$this->element = $element;
	}

	public function addID($id,$season_id=false){
		$season = $this->_resolvSeason($season_id);
		$this->ids[$season] = $id;
		$this->seasonIDs[$season] = $season_id;
		$this->idSeasons[$season_id] = $season;
	}
	
	public function getdirectLink($season_id=false){
		$season = $this->_resolvSeason($season_id);
		
		$res = $this->db->get("locations");
		$url = "";
		foreach($res->result() as $loc){
			if($loc->name == $this->name){
				$url = $loc->show_url;	
			}
		}
		//print "-".$this->name."-(".$url.")<br>";
		
		$urls = array();
		foreach($res->result() as $loc){
			$newUrl = str_replace("<?=$".$loc->name."?>","",$url);
			if($newUrl != $url){
				//print $newUrl;
				if($season==false)
					$otherMaps = $this->db->get_where("elementLocations",array("element_id"=>$this->element, "location_id"=>$loc->id));
				else
					$otherMaps = $this->db->get_where("elementLocations",array("element_id"=>$this->element, "location_id"=>$loc->id, "season_id"=>$season));
				
				if(!$otherMaps)
					continue;
				if($otherMaps->num_rows() == 0)
					continue;
				
				foreach($otherMaps->result() as $curOtherMap){
					$curSeason = $curOtherMap->season_id;
					$urls[$curSeason] = str_replace("<?=$".$loc->name."?>",$curOtherMap->identifier,$url);
				}
			}
		}
		if(count($urls)==0)
			return "";
		else if(count($urls)==1)
			return end($urls);
		else
			return $urls;
	}
	
	function _resolvSeason($season_id){		
		$season = $this->db->get_where('seasons',array('id' => $season_id));
		if($season)
			if($season->num_rows() == 1){
				$season = $season->result();
				$season = $season[0];
				return $season->season;
			}
		return -1;
	}
	
}

?>