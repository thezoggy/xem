<?
class Simplemap extends DBObject{
	private $db;
	private $table = "maps";
	
	public $id = 0;
	public $element_id;
	public $name_id;
	
	function __construct($db, $id=0){
		$this->db = $db;
		$this->id = $id;
		if($this->id)
			$this->load();
	}
	
	function save(){
		if(!$this->id)
			$this->load();
		if($this->id){
			print "updating map ".$this->id."</br>";
			$this->db->update($this->table,array("element_id"=>$this->element_id,
												"name_id"=>$this->name_id), array("id"=>$this->id));
		}else{
			print "inserting new map... ";
			$this->db->insert($this->table,array("element_id"=>$this->element_id,
												"name_id"=>$this->name_id));
			$this->id = $this->db->insert_id();
			print "new id: ".$this->id."<br>";
		}
	}
	
	function load(){
		if(!$this->id){
			$testRes = $this->db->get_where($this->table,array("element_id"=>$this->element_id,"name_id"=>$this->name_id));
			if(rows($testRes)){
				$thisFromDb = $testRes->row_array();
				$this->id = $thisFromDb['id'];
			}
		}else{
			$testRes = $this->db->get_where($this->table,array("id"=>$this->id));
			if(rows($testRes)){
				$thisFromDb = $testRes->row_array();
				$this->element_id = $thisFromDb['element_id'];
				$this->name_id = $thisFromDb['name_id'];
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