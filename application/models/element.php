<?
class Element extends DBObject{

	function __construct($oh, $id=0){
		$dataFields = array("main_name","entity_order","status");
		
      	parent::__construct($oh, $dataFields, $id);
	}
}

?>