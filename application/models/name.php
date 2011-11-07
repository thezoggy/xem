<?
class Name extends DBObject{
	
	function __construct($oh, $id=0){
		$dataFields = array("element_id","season","name","language");

      	parent::__construct($oh, $dataFields, $id);
	}
}
?>