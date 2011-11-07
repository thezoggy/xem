<?php
class OffsetRule{
	private $db;
	private $table = "offsetrules";
	
	public $id;
	public $map_id;
	public $season_from;
	public $season_to;
	public $season_offset;
	public $episode_from;
	public $episode_to;
	public $episode_offset;
	public $absolute_episode_offset;
	
	
	function __construct($db, $id=0){
		$this->db = $db;
		$this->id = $id;
		if($this->id)
			$this->load();
		
	}	
	function save(){
		if(!$this->id)
			$this->load();
		if($this->id){
			print "updating offsetrule ".$this->id."</br>";
			$this->db->update($this->table,array("season_from"=>$this->season_from,
													"season_to"=>$this->season_to,
													"season_offset"=>$this->season_offset,
													"episode_from"=>$this->episode_from,
													"episode_to"=>$this->episode_to,
													"episode_offset"=>$this->episode_offset,
													"absolute_episode_offset"=>$this->absolute_episode_offset), array("id"=>$this->id));
		}else{
			print "inserting new offsetrule... ";
			$this->db->insert($this->table,array("map_id"=>$this->map_id,
													"season_from"=>$this->season_from,
													"season_to"=>$this->season_to,
													"season_offset"=>$this->season_offset,
													"episode_from"=>$this->episode_from,
													"episode_to"=>$this->episode_to,
													"episode_offset"=>$this->episode_offset,
													"absolute_episode_offset"=>$this->absolute_episode_offset));
			$this->id = $this->db->insert_id();
			print_query($this->db);
			print "new id: ".$this->id."<br>";
		}
	}
	
	function load(){
		if(!$this->id){
			$testRes = $this->db->get_where($this->table,array("map_id"=>$this->map_id,
																"season_from"=>$this->season_from,
																"season_to"=>$this->season_to,
																"season_offset"=>$this->season_offset,
																"episode_from"=>$this->episode_from,
																"episode_to"=>$this->episode_to,
																"episode_offset"=>$this->episode_offset,
																"absolute_episode_offset"=>$this->absolute_episode_offset));
			if(rows($testRes)){
				$thisFromDb = $testRes->row_array();
				$this->id = $thisFromDb['id'];
			}
		}else{
			$testRes = $this->db->get_where($this->table,array("id"=>$this->id));
			if(rows($testRes)){
				$thisFromDb = $testRes->row_array();
					
				$this->map_id = $thisFromDb['map_id'];
				$this->season_from = $thisFromDb['season_from'];
				$this->season_to = $thisFromDb['season_to'];
				$this->season_offset = $thisFromDb['season_offset'];
				$this->episode_from = $thisFromDb['episode_from'];
				$this->episode_to = $thisFromDb['episode_to'];
				$this->episode_offset = $thisFromDb['episode_offset'];
				$this->absolute_episode_offset = $thisFromDb['absolute_episode_offset'];
			}
		}
	}
	
	function delete(){
		if($this->id){
			$this->db->delete($this->table,array("id"=>$this->id));
			$this->id = 0;
		}
	}
	
	
	
}
?>