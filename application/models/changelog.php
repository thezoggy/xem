<?
class Changelog{
	private $oh;
	private $db;
	private $element_id;

	public $events = array();

    function __construct($oh,$element_id){
		$this->oh = $oh;
		$this->db = $oh->db;
		$this->element_id = $element_id;
		$this->locations = buildLocations($this->oh);

		$this->init();
		//print count($this->events);
	}

	private function init(){

		$result = $this->db->query("SELECT * FROM `history` WHERE `element_id` = '".$this->element_id."' ORDER BY `time` DESC");
		if(rows($result)){
			foreach($result->result() as $curRevsion){
				$newRaw = json_decode($curRevsion->new_data,true);
				$oldRaw = json_decode($curRevsion->old_data,true);

				$diff = array_diff($newRaw,$oldRaw);
				$old = array();
				$new = array();
				/*
				if(count($diff))
					foreach($diff as $changedKey=>$newValue){
						$new[$changedKey] = $newRaw[$changedKey];
						if(isset($oldRaw[$changedKey]))
							$old[$changedKey] = $oldRaw[$changedKey];
						else
							$old[$changedKey] = "-";
					}
				else{
					$old = $oldRaw;
					$new = $newRaw;
				}*/

				$old = $oldRaw;
				$new = $newRaw;
				$userName = userNameByID($this->db,$curRevsion->user_id); // this might make this very slow
				$event = array("time"=>$curRevsion->time,
								"revision"=>$curRevsion->revision,
								"type"=>$curRevsion->obj_type,
								"action"=>$curRevsion->action,
								"user_nick"=>$userName,
								"user_id"=>$curRevsion->user_id,
				                "diff"=>$diff,
								"old"=>$old,
								"new"=>$new);
				$event['human_form'] = $this->createHumanform($event);
				$this->events[] = $event;

			}
		}

	}

	private function createHumanform($event){
	    switch ($event['action']) {
	        case 'insert':
	            return $this->createHumanformInsert($event);
	        case 'update':
	            return $this->createHumanformUpdate($event);
	        case 'delete':
	            return $this->createHumanformDelete($event);
	        default:
	            return '??';
    	        break;
	    }
	}

	private function createHumanformInsert($event){
	    $old = $event['old'];
	    $new = $event['new'];
	    switch ($event['type']) {
	        case 'Element':
               return 'created '.$new['name'];
	        case 'Name':
	           return 'created a new alias '.$new['name'].' in language '.$new['language'];
	        case 'Directrule':
	           return 'connected s'.zero_pad($new['origin_season']).'e'.zero_pad($new['origin_episode']).' with s'.zero_pad($new['destination_season']).'e'.zero_pad($new['destination_episode']);
	        case 'Passthru':
	           return 'connected '.$this->locN($new['destination_id']).' and '.$this->locN($new['origin_id']).' with an '.$new['type'].' passthru';
	        case 'Season':
	            return 'created season '.$new['season'].' for '.$this->locN($new['location_id']).' with '.$new['season_size'].' episodes';
	    }
	}

	private function createHumanformUpdate($event){
	    $old = $event['old'];
	    $new = $event['new'];
	    switch ($event['type']) {
	        case 'Element':
	           return 'renamed '.$old['main_name'].' to '.$new['main_name'];
	        case 'Name':
	           return 'changed alias name from '.$old['name'].' to '.$new['name'];
	        case 'Directrule':
	           return 'i dont thing this can happen';
	        case 'Passthru':
	           return 'changed the passtruhe '.$this->locN($new['destination_id']).' and '.$this->locN($new['origin_id']).' from '.$old['type'].' to '.$new['type'];
	        case 'Season':
	            return 'updated season '.$new['season'].' of '.$this->locN($new['location_id']).' what happend will come later';
	    }
	}

	private function createHumanformDelete($event){
	    $old = $event['old'];
	    $new = $event['new'];
	    switch ($event['type']) {
	        case 'Element':
               return 'elements dont get deleted';
	        case 'Name':
	           return 'deleted the alias '.$new['name'];
	        case 'Directrule':
	           return 'disconected s'.zero_pad($new['origin_season']).'e'.zero_pad($new['origin_episode']).' from s'.zero_pad($new['destination_season']).'e'.zero_pad($new['destination_episode']);
	        case 'Passthru':
	           return 'removed the passthru '.$this->locN($new['destination_id']).' and '.$this->locN($new['origin_id']);
	        case 'Season':
	            return 'deleted season '.$new['season'].' of '.$this->locN($new['location_id']);
	    }
	}

	private function locN($id){
	   return $this->locations[$id]->name;

	}

}

?>