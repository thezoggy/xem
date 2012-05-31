<?
class Location extends DBObject{

	function __construct($oh, $id=0){
		$dataFields = array("name", "description", "url", "show_url", "movie_url","status");

      	parent::__construct($oh, $dataFields, $id);
	}
}

?>