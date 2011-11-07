<?
class ElementLocation{
	private $db;
	private $table = "elementLocations";
	
	public $id = 0;
	public $element;
	public $identifier;
	public $location;
	public $season;
	public $seasonsize;
	
	function __construct($db, $id=0){
		$this->db = $db;
		$this->id = $id;
		if($this->id)
			$this->load();
	}
	
	public function save(){
		if(!$this->id)
			$this->load();
		if($this->id){
			print "updating elementlocation ".$this->id."</br>";
			$this->db->update($this->table,array("location_id"=>$this->location->id,
														"element_id"=>$this->element->id,
														"season_id"=>$this->season->id,
														"identifier"=>$this->identifier,
														"seasonsize"=>$this->seasonsize), array("id"=>$this->id));
		}else{
			print "inserting new elementlocation</br>";
			$this->db->insert($this->table,array("location_id"=>$this->location->id,
														"element_id"=>$this->element->id,
														"season_id"=>$this->season->id,
														"identifier"=>$this->identifier,
														"seasonsize"=>$this->seasonsize));
			$this->id = $this->db->insert_id();
		}
	}
			
	function load(){
		if(!$this->id){
			$testRes = $this->db->get_where($this->table,array("element_id"=>$this->element->id,"season_id"=>$this->season->id,"location_id"=>$this->location->id,"identifier"=>$this->identifier));
			if(rows($testRes)){
				$thisFromDb = $testRes->row_array();
				$this->id = $thisFromDb['id'];
			}
		}else{
			$testRes = $this->db->get_where($this->table,array("id"=>$this->id));
			if(rows($testRes)){
				$thisFromDb = $testRes->row_array();
				$this->identifier = $thisFromDb['identifier'];
				$this->seasonsize = $thisFromDb['seasonsize'];
				$this->element = new SimpleElement($this->db, $thisFromDb['element_id']);
				$this->season = new Season($this->db, $thisFromDb['season_id']);
				$this->location = new Location($this->db, $thisFromDb['location_id']);
			}
		}
	}
	
	function delete(){
		if($this->id){
			$this->db->delete($this->table,array("id"=>$this->id));
			$this->id = 0;
		}
	}
}

?>