<?
class Element{
	
	public $db;
	
	public $id;
	public $main_name;
	public $seasons = array();
	public $names = array();
	public $locations = array();
	public $elementlocations = array();

	function __construct($db, $element_id){
		$this->db = $db;
		$elementRow = $this->db->get_where('elements',array('id' => $element_id));
		if(rows($elementRow)){
			$elementRow = $elementRow->row_array();
			
			$this->locations = buildLocations($this->db);
			
			$this->id = $elementRow['id'];
			$this->main_name = $elementRow['main_name'];
			$this->buildSeasons();
			$this->buildNames();
			$this->buildElementLocations();
		}
	}
	
	
	private function buildSeasons(){
		$seasons = $this->db->get_where("seasons",array("element_id"=>$this->id));
		if(rows($seasons)){
			foreach($seasons->result_array() as $curSeason){
				$this->seasons[$curSeason['id']] = new Season( $this->db, $curSeason['id']); 	
			}	
		}
	}

	private function buildNames(){
		$names = $this->db->get_where("names",array("element_id"=>$this->id));
		if(rows($names)){
			foreach($names->result_array() as $curName){
				$this->names[$curName['id']] = new Name( $this->db, $curName['id']); 	
			}	
		}
	}

	private function buildElementLocations(){
		$elementLocations = $this->db->get_where("elementLocations",array("element_id"=>$this->id));
		if(rows($elementLocations)){
			foreach($elementLocations->result_array() as $curElementLocation){
				$this->elementlocations[$curElementLocation['id']] = new ElementLocation( $this->db,$curElementLocation['id']); 	
			}	
		}
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
	
	function save(){
		$simpleElement = new SimpleElement($this->db,array("id"=>$this->id, "main_name"=>$this->main_name)); 
		$simpleElement->save();
		foreach($this->elementlocations as $curElementlocation){
			$curElementlocation->save();
		}
		foreach($this->names as $curName){
			$curName->save();
		}
		foreach($this->seasons as $curSeason){
			$curSeason->save();
		}
	}
	
	
}


?>
