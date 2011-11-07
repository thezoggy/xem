<?php
class Map{

	public $db;
	
	public $id;
	public $element;
	public $maplocations;
	public $name;
	public $offsetrules = array();
	public $locations = array();

	function __construct($db, $id, $buildRules=false){
		$this->db = $db;
		$elementRow = $this->db->get_where('maps',array('id' => $id));
		if(rows($elementRow)){
			$elementRow = $elementRow->row_array();
			
			$this->locations = buildLocations($this->db);
			
			$this->id = $elementRow['id'];
			$this->name_id = $elementRow['name_id'];
			
			$this->maplocations = new Maplocation($this->db, $this->id);
			$this->element = new Simpleelement($this->db, $elementRow['element_id']);
			$this->name = new Name($this->db, $elementRow['name_id']);
			if($buildRules)
				$this->buildOffsetrules();
		}
	}
	
	
   function buildOffsetrules(){
		$rules = $this->db->get_where("offsetrules",array("map_id"=>$this->id));
		if(rows($rules)){
			foreach($rules->result_array() as $curRule){
				$this->offsetrules[$curRule['id']] = new Offsetrule($this->db, $curRule['id']); 	
			}	
		}
		$this->offsetrules[] = new Offsetrule($this->db);
	}
	
	function elementLocationsForLocationId($id){
		$result = array();
		foreach($this->elementlocations as $curElementLocation){
			if($curElementLocation->location->id == $id){
				$result[] = $curElementLocation;
			}
		}
		return $result;
	}
	
	function originNames(){
		$names = array();
		foreach($this->maplocations->origins as $curMapLocation){
			$tmp = $this->locations[$curMapLocation];
			$names[] = $tmp;	
		}
		return $names;
	}	
	function destinationNames(){
		$names = array();
		foreach($this->maplocations->destinations as $curMapLocation){
			$tmp = $this->locations[$curMapLocation];
			$names[] = $tmp;
		}
		return $names;
	}
	
	function isLocationAOrigin($location_id){
		foreach($this->maplocations->origins as $curOrigin){
			if($curOrigin == $location_id)
				return true;
		}
		return false;
	}
	
	function isLocationADestination($location_id){
		foreach($this->maplocations->destinations as $curOrigin){
			if($curOrigin == $location_id)
				return true;
		}
		return false;
	}
	
}

?>