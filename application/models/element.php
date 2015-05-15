<?
class Element extends DBObject{

	function __construct($oh, $id=0){
		$dataFields = array("main_name","entity_order","status","parent","last_modified","created");

      	parent::__construct($oh, $dataFields, $id);
	}

	function isDraft(){
	    return ($this->parent != 0);
	}
}

?>