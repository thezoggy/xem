<?
class Passthru extends DBObject{

	function __construct($oh, $id=0){
		$dataFields = array("origin_id","destination_id","element_id","type");
		
      	parent::__construct($oh, $dataFields, $id);
	}
	
	function reverse(){
		
		$values = $this->buildNameValueArray();
		
		$this->origin_id = $values['destination_id'];
		$this->destination_id = $values['origin_id'];
		$this->element_id = $values['element_id'];
		$this->id = 0;
	}
		
}

?>