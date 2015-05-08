<?
class Changelog{
	private $oh;
	private $db;
	private $element_id;

	public $events = array();

    function __construct($oh,$element_id=0){
		$this->oh = $oh;
		$this->db = $oh->db;
		$this->element_id = $element_id;
		$this->locations = buildLocations($this->oh, true);

		$this->events = array();
		if($this->element_id)
    		$this->init($this->element_id);
	}

	function init($element_id=0, $limit=5){

	    if($element_id)
    		$result = $this->db->query("SELECT * FROM `history` WHERE `element_id` = '".$element_id."' ORDER BY `time` DESC");
		else
		    $result = $this->db->query("SELECT * FROM `history` WHERE `element_id` IS NOT NULL AND `silent` = 0 ORDER BY `time` DESC LIMIT 0,".$limit);

        log_message('debug',$this->db->last_query());
    	if(rows($result)){
            $silent_counter = 0;
			foreach($result->result() as $curRevsion){
			    $cur_element_id = 0;
			    if($element_id)
    			    $cur_element_id = $element_id;

				$newRaw = json_decode($curRevsion->new_data,true);
				$oldRaw = json_decode($curRevsion->old_data,true);

				$diff = array_diff_assoc($newRaw,$oldRaw);

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
								"new"=>$new,
				                "obj_id"=>$curRevsion->obj_id,
				                "element_id"=>$curRevsion->element_id);
				/*
				if($curRevsion->silent){
			        $silent_counter++;
			        continue;
			    }else if($silent_counter > 0){
			        // fake message event
                    $this->events[] = $this->getSupressedEventCountEvent($silent_counter);
			        $silent_counter = 0;
			    }*/
		        $event['human_form'] = $this->createHumanform($event, $element_id);
		        if($curRevsion->silent){
		           $event['human_form'] .= '<span class="suppressed"></span>';
		        }

				$this->events[] = $event;

			}
		}

	}

	private function getFakeMessageEvent($msg){
	    $event = array("id"=>0,
								"time"=>"--",
								"revision"=>0,
								"type"=>"system",
								"action"=>"none",
								"user_nick"=>"",
								"user_id"=>0,
				                "diff"=>array(),
								"old"=>array(),
								"new"=>array(),
				                "obj_id"=>0,
				                "element_id"=>0);
	    $event['human_form'] = $msg;
	    return $event;
	}
	private function getSupressedEventCountEvent($silent_counter) {
	    return $this->getFakeMessageEvent("Suppressed output of ".$silent_counter.' changes.<span class="suppressed"></span>');
	}

	private function createHumanform($event, $cur_element_id){
	    switch ($event['action']) {
	        case 'insert':
	            return $this->createHumanformInsert($event);
	        case 'update':
	            return $this->createHumanformUpdate($event);
	        case 'delete':
	            return $this->createHumanformDelete($event);
	        case 'create_draft':
	            return $this->createHumanformDraftCreate($event, $cur_element_id);
	        case 'draft_accepted':
	            return $this->createHumanformDraftAccepted($event, $cur_element_id);
	        case 'public_request':
	            return 'send a public request';
	        default:
	            return 'unknown event: '.$event['action'];
    	        break;
	    }
	}

	private function createHumanformInsert($event){
	    $old = $event['old'];
	    $new = $event['new'];
	    switch ($event['type']) {
	        case 'Element':
                return 'created <b>'.$new['main_name'].'</b>';
	        case 'Name':
	            return 'created a new alias <b>'.$new['name'].'</b> in language '.img(array('src'=>'images/flags/'.$new['language'].'.png','title'=>$new['language'],'alt'=>$new['language']));
	        case 'Directrule':
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'connected <span class="'.$or.'">'.$or.'\'s</span> s'.zero_pad($new['origin_season']).'e'.zero_pad($new['origin_episode']).' with <span class="'.$des.'">'.$des.'\'s</span> s'.zero_pad($new['destination_season']).'e'.zero_pad($new['destination_episode']);
	        case 'Passthru':
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'created <span class="'.$new['type'].'">'.$new['type'].'</span> passthru from <span class="'.$des.'">'.$des.'</span> to <span class="'.$or.'">'.$or.'</span>';
	        case 'Season':
	            $loc = $this->locN($new['location_id']);
    			$seasonNumber = $new['season'];
    			if($seasonNumber == -1)
    				$seasonNumber = "*";
	            $out = 'created season <b>'.$seasonNumber.'</b> for <span class="'.$loc.'">'.$loc.'</span> with <b>'.$new['season_size'].'</b> episodes';
	            if(isset($new['identifier']) && $new['identifier'] != '')
	                $out.= ' and identifier <b>'.$new['identifier'].'</b>';
	            return $out;
	        case 'Content':
	            return 'updated the content of <b>'.str_replace('/index', '',anchor($event['obj_id'])).'</b>';
	    }
	}

	private function createHumanformUpdate($event){
	    $old = $event['old'];
	    $new = $event['new'];
	    switch ($event['type']) {
	        case 'Element':
	            if($old['main_name'] != $new['main_name'])
	                return 'renamed <b>'.$old['main_name'].'</b> to <b>'.$new['main_name'].'</b>';
	            elseif((int)$old['status'] != (int)$new['status'] && (int)$old['status'] > 0 && (int)$new['status'] > 0 )
	                return 'changed the level from <b>'.$old['status'].'</b> to <b>'.$new['status'].'</b>';
	            elseif((int)$old['status'] != (int)$new['status'] && (int)$old['status'] > 0 && (int)$new['status'] == 0 )
	                return 'deleted the show <b>'.$new['main_name'].'</b> which had a level of <b>'.$old['status'].'</b>';
	            elseif((int)$old['status'] != (int)$new['status'] && (int)$old['status'] == 0 && (int)$new['status'] > 0 )
	                return 'undeleted the show <b>'.$new['main_name'].'</b>';
	            elseif($old['entity_order'] != $new['entity_order'])
	                return 'changed the entity order from <strong>'.$old['entity_order'].'</strong> to <strong>'.$new['entity_order'].'</strong>';
	            else
	                return "saved without changing data"; // ."<!-- old:".print_r($old, true).' vs new:'.print_r($new, true)."-->";
	        case 'Name':
	            if($old['name'] != $new['name'])
	                return 'changed alias name from <b>'.$old['name'].'</b> to <b>'.$new['name'].'</b>';
	            else
	                return 'changed the language of <b>'.$old['name'].'</b> from '.img(array('src'=>'images/flags/'.$old['language'].'.png','title'=>$old['language'],'alt'=>$old['language'])).' to '.img(array('src'=>'images/flags/'.$new['language'].'.png','title'=>$new['language'],'alt'=>$new['language']));
	        case 'Directrule':
	            return 'i dont think this can happen';
	        case 'Passthru':
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'changed passthru between <span class="'.$des.'">'.$des.'</span> and <span class="'.$or.'">'.$or.'</span> from <span class="'.$old['type'].'">'.$old['type'].'</span> to <span class="'.$new['type'].'">'.$new['type'].'</span>';
	        case 'Season':
	            $loc = $this->locN($new['location_id']);
	            $diff = $event['diff'];
    			$seasonNumber = $old['season'];
    			if($seasonNumber == -1)
    				$seasonNumber = "*";
	            return 'updated season <b>'.$seasonNumber.'</b> of <span class="'.$loc.'">'.$loc.'</span> ... '.$this->buildChange($old,$new,$diff);
	        case 'Content':
	            return 'updated the content of <b>'.str_replace('/index', '',anchor($event['obj_id'])).'</b>';
	    }
	}

	private function createHumanformDelete($event){
	    $old = $event['old'];
	    $new = $event['new'];
	    switch ($event['type']) {
	        case 'Element':
                return 'elements do not get deleted';
	        case 'Name':
                return 'deleted the alias <b>'.$new['name'].'</b> in language '.img(array('src'=>'images/flags/'.$new['language'].'.png','title'=>$new['language'],'alt'=>$new['language']));
	        case 'Directrule':
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'disconnected <span class="'.$or.'">'.$or.'\'s</span> s'.zero_pad($new['origin_season']).'e'.zero_pad($new['origin_episode']).' from <span class="'.$des.'">'.$des.'\'s</span> s'.zero_pad($new['destination_season']).'e'.zero_pad($new['destination_episode']);
	        case 'Passthru':
	            $des = $this->locN($new['destination_id']);
                $or = $this->locN($new['origin_id']);
	            return 'removed passthru <span class="'.$des.'">'.$des.'</span> and <span class="'.$or.'">'.$or.'</span>';
	        case 'Season':
	            $loc = $this->locN($new['location_id']);
    			$seasonNumber = $new['season'];
    			if($seasonNumber == -1)
    				$seasonNumber = "*";
	            return 'deleted season <b>'.$seasonNumber.'</b> of <span class="'.$loc.'">'.$loc.'</span>';
	    }
	}

	private function createHumanformDraftCreate($event, $cur_element_id){
        if($cur_element_id == $event['obj_id'])
            return '<strong>created this draft</strong><span class="draft_bottom"></span>';
        else
            return '<strong>created draft '.$event['obj_id'].'</strong>';
	}
	private function createHumanformDraftAccepted($event, $cur_element_id){
        if($cur_element_id == $event['obj_id'])
            return '<strong>made this draft public</strong><span class="draft_top"></span>';
        else
            return '<strong>made draft '.$event['obj_id'].' public</strong>';
	}


	private function locN($id){
	   return $this->locations[$id]->name;

	}


	private function buildChange($old,$new,$diff){
	    $out = array();
	    foreach($diff as $key=>$value){
	        if($old[$key] && $new[$key]){
	            if($key == 'season'){
	                if($old[$key] == -1)
	                   $old[$key] = '*';
	                if($new[$key] == -1)
	                   $new[$key] = '*';
	            }
	            $out[] = 'changed '.str_replace('_', ' ', $key).' from <b>'.$old[$key].'</b> to <b>'.$new[$key].'</b>';
	        }elseif ($old[$key] && !$new[$key])
	            $out[] = 'removed '.str_replace('_', ' ', $key).' <b>'.$old[$key].'</b>';
	        elseif (!$old[$key] && $new[$key])
	            $out[] = 'added '.str_replace('_', ' ', $key).' <b>'.$new[$key].'</b>';
	    }
	    if($out)
	        return join(', ', $out);
	    else
	        return 'nothing changed';
	}
}

?>