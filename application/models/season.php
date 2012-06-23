<?
class Season extends DBObject{

	function __construct($oh, $id=0){
		$dataFields = array("location_id","element_id","identifier","season","season_size","absolute_start","episode_start");

      	parent::__construct($oh, $dataFields, $id);
	}

	function save($load=true) {
      	parent::save($load);
      	if(isset($this->diff['season_size'])){
	        if((int)$this->diff['season_size'] < $this->initialData['season_size']){
	            log_message('debug', 'Season size went down. Checking if we need to delete some direct connections');
	            $old_season = $this->initialData['season'];
	            $this->db->where('origin_episode >', $this->diff['season_size']);
	            $direct_cons_out = $this->db->get_where('directrules', array('origin_id'=>$this->location_id, 'element_id'=>$this->element_id, 'origin_season'=>$old_season));
	            if(rows($direct_cons_out)){
    	            foreach ($direct_cons_out->result() as $cur_direct_con_row) {
        	            $cur_direct_con_obj = new Directrule($this->oh, $cur_direct_con_row->id);
        	            if($cur_direct_con_obj->delete(true)){
            	            $cur_direct_con_obj->reverse();
            	            if($cur_direct_con_obj->delete(true)){
                                continue;
            	            }else
                  	            log_message('error', 'could not delete a direct con: '.print_r($this->buildNameValueArray(),true));
        	            }else
                  	        log_message('error', 'could not delete a direct con: '.print_r($this->buildNameValueArray(),true));

    	            }
	            }

	        }

      	}



      	if(isset($this->diff['season'])){
	        log_message('debug', 'Season number change moving direct connections');
	        $old_season = $this->initialData['season'];
	        $new_season = $this->diff['season'];
	        // first outgoing -> origin_id
	        $direct_cons_out = $this->db->get_where('directrules', array('origin_id'=>$this->location_id, 'element_id'=>$this->element_id, 'origin_season'=>$old_season));
	        if(rows($direct_cons_out)){
    	        foreach ($direct_cons_out->result() as $cur_direct_con_row) {
    	            $cur_direct_con_obj = new Directrule($this->oh, $cur_direct_con_row->id);
    	            if($cur_direct_con_obj->delete(true)){
        	            $cur_direct_con_obj->reverse();
        	            if($cur_direct_con_obj->delete(true)){
        	                //now both old direct connections are deleted
        	                // since we reversed it once we have to set the destination season now
            	            $cur_direct_con_obj->destination_season = $new_season;
            	            $cur_direct_con_obj->save(true,true);
            	            $cur_direct_con_obj->reverse();
            	            $cur_direct_con_obj->save(true,true);
        	            }else
                  	        log_message('error', 'could not delete a direct con: '.print_r($this->buildNameValueArray(),true));
    	            }else
              	        log_message('error', 'could not delete a direct con: '.print_r($this->buildNameValueArray(),true));

    	        }
	        }
      	}
	}
}

?>