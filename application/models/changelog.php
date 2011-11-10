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
				$event = array("id"=>$curRevsion->id,
								"time"=>$curRevsion->time,
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
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'connected <span class="'.$or.'">'.$or.'\'s</span> s'.zero_pad($new['origin_season']).'e'.zero_pad($new['origin_episode']).' with <span class="'.$des.'">'.$des.'\'s</span> s'.zero_pad($new['destination_season']).'e'.zero_pad($new['destination_episode']);
	        case 'Passthru':
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'connected <span class="'.$des.'">'.$des.'</span> and <span class="'.$or.'">'.$or.'</span> with an '.$new['type'].' passthru';
	        case 'Season':
	            $loc = $this->locN($new['location_id']);
	            return 'created season '.$new['season'].' for <span class="'.$loc.'">'.$loc.'</span> with '.$new['season_size'].' episodes';
	    }
	}

	private function createHumanformUpdate($event){
	    $old = $event['old'];
	    $new = $event['new'];
	    switch ($event['type']) {
	        case 'Element':
	            return 'renamed <strong>'.$old['main_name'].'</strong> to <strong>'.$new['main_name'];
	        case 'Name':
	            return 'changed alias name from <strong>'.$old['name'].'</strong> to <strong>'.$new['name'];
	        case 'Directrule':
	            return 'i dont thing this can happen';
	        case 'Passthru':
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'changed the passtruhe between <span class="'.$des.'">'.$des.'</span> and <span class="'.$or.'">'.$or.'</span> from <strong>'.$old['type'].'</strong> to <strong>'.$new['type'].'</strong>';
	        case 'Season':
	            $loc = $this->locN($new['location_id']);
	            $diff = $event['diff'];
	            return 'updated season '.$new['season'].' of <span class="'.$loc.'">'.$loc.'</span> ... '.$this->buildChange($old,$new,$diff);
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
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'disconected <span class="'.$or.'">'.$or.'\'s</span> s'.zero_pad($new['origin_season']).'e'.zero_pad($new['origin_episode']).' from <span class="'.$des.'">'.$des.'\'s</span> s'.zero_pad($new['destination_season']).'e'.zero_pad($new['destination_episode']);
	        case 'Passthru':
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'removed the passthru <span class="'.$des.'">'.$des.'</span> and <span class="'.$or.'">'.$or.'</span>';
	        case 'Season':
	            $loc = $this->locN($new['location_id']);
	            return 'deleted season '.$new['season'].' of <span class="'.$loc.'">'.$loc.'</span>';
	    }
	}

	private function locN($id){
	   return $this->locations[$id]->name;

	}


	private function buildChange($old,$new,$diff){
	    $out = array();
	    foreach($diff as $key=>$value){
	        if($old[$key] && $new[$key])
	            $out[] = '<strong>'.$old[$key].'</strong> to <strong>'.$new[$key].'</strong>';
	        elseif ($old[$key] && !$new[$key])
	            $out[] = 'removed '.$key.' <strong>'.$old[$key].'</strong>';
	        elseif (!$old[$key] && $new[$key])
	            $out[] = 'added '.$key.' <strong>'.$new[$key].'</strong>';
	    }
	    if($out)
	        return join(', ', $out);
	    else
	        return 'nothing changed';
	}
}

?>