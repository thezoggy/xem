<?
class Directrule extends DBObject{

	function __construct($oh, $id=0){
		$dataFields = array('origin_id','destination_id', 'element_id', 'name_id', 'origin_season', 'origin_episode', 'destination_season',	'destination_episode');
		
      	parent::__construct($oh, $dataFields, $id);
	}
	
	function reverse(){
		
		$values = $this->buildNameValueArray();
		
		$this->origin_id = $values['destination_id'];
		$this->destination_id = $values['origin_id'];
		$this->element_id = $values['element_id'];
		
		$this->origin_season = $values['destination_season'];
		$this->origin_episode = $values['destination_episode'];
		$this->destination_season = $values['origin_season'];
		$this->destination_episode = $values['origin_episode'];
		$this->id = 0;
	}
}

?>