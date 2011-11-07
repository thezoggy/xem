<?
class Maplocation{
	private $db;
	private $origin_table = "origins";
	private $destination_table = "destinations";
	
	public $map_id = 0;
	public $origins = array();
	public $destinations = array();
	
	function __construct($db, $map_id=0){
		$this->db = $db;
		$this->map_id = $map_id;
		if($this->map_id)
			$this->load();
	}
	
	function save(){
		
		$this->deleteAllOther();
		
		print "inserting new origin ids (".count($this->origins).")</br>";
		foreach($this->origins as $curID){
			$this->db->insert($this->origin_table,array("map_id"=>$this->map_id,"location_id"=>$curID));
		}
		print "inserting new destination ids (".count($this->destinations).")</br>";
		foreach($this->destinations as $curID){
			$this->db->insert($this->destination_table,array("map_id"=>$this->map_id,"location_id"=>$curID));
		}
	
	}
	
	private function deleteAllOther(){
		$this->db->delete($this->origin_table,array("map_id"=>$this->map_id));
		$this->db->delete($this->destination_table,array("map_id"=>$this->map_id));	
		
	}
	function load(){
		if($this->map_id){
			$testRes = $this->db->get_where($this->origin_table,array("map_id"=>$this->map_id));
			if(rows($testRes)){
				foreach($testRes->result_array() as $curRow){
					$this->origins[] = $curRow['location_id'];
				}
			}
			$testRes = $this->db->get_where($this->destination_table,array("map_id"=>$this->map_id));
			if(rows($testRes)){
				foreach($testRes->result_array() as $curRow){
					$this->destinations[] = $curRow['location_id'];
				}
			}
		}
	}
	
	
}

?>