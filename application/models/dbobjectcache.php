<?
class DBObjectCache{
	private $obj = array();
	
	function __construct(){
	}
	
	function hasCache($type,$id){
		if(isset($this->obj[$type][$id])){
			return $this->obj[$type][$id];
		}
		return false;
	}
	
	function add($obj){
		$this->obj[get_class($obj)][$obj->id] = $obj;
	}
}


?>