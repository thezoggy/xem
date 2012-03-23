<?
class History{
	public $db = null;
	private $session = null;
	function __construct($db,$session){
		$this->db = $db;
		$this->session = $session;
	}

	function createEvent($action,$obj){
		$data = array();
		$data['obj_id'] = $obj->id;
		$data['user_id'] = $this->session->userdata('user_id');
		$data['user_lvl'] = $this->session->userdata('user_lvl');
		$data['obj_type'] = get_class($obj);

		if($data['obj_type']!= "Element"){
			if(isset($obj->element_id))
				if($obj->element_id)
					$data['element_id'] = $obj->element_id;
		}else{
			$data['element_id'] = $obj->id;
		}

		$data['old_data'] = json_encode($obj->initialData);
		$data['new_data'] = json_encode($obj->buildNameValueArray());
		$data['revision'] = $this->getNewRevisionNumber($data['obj_id'], $data['obj_type']);
		$data['action'] = $action;
		$this->db->set('time', 'NOW()', FALSE); // the special "NOW()" value has to be set like this blame codeignigter
		$this->db->insert('history',$data);
		//print_query($this->db);
		//print $user_id.'-'.json_encode($obj->buildNameValueArray());
		//id, user_id, user_lvl, obj_id, obj_type, action, time, revision, old_data, new_data

	}

	private function getNewRevisionNumber($obj_id,$obj_type){
		$rev = 1;
		$result = $this->db->query("SELECT `revision` FROM `history` WHERE `obj_id` = '".$obj_id."' AND `obj_type` =  '".$obj_type."' ORDER BY `revision` DESC");
		if(rows($result)){
			$row = getFirst($result);
			$rev = (int)$row['revision'] + 1;
		}
		return $rev;
	}
}
?>