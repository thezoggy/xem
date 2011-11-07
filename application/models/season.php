<?
class Season extends DBObject{

	function __construct($oh, $id=0){
		$dataFields = array("location_id","element_id","identifier","season","season_size","absolute_start","episode_start");
		
      	parent::__construct($oh, $dataFields, $id);
	}	
}

?>