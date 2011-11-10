<?
class DBObject{
	private $oh;
	private $db;
	private $cache;
	private $table = "";
	private $dataFields = array();
	private $className;
	private $objs = array();

	public $id = 0;
	public $initialData = array();

	function __construct($oh, $dataFields, $id){
		//print get_class($this)."<br>";
		$this->oh = $oh;
		$this->db = $oh->db;
		$this->cache = $oh->cache;
		$this->history = $oh->history;

		$this->className = strtolower(get_class($this));
		$this->table = $this->className."s";
		$this->dataFields = $dataFields;

		$this->id = $id;
		if($this->id)
			$this->load();
	}

	public function save(){
		if(!$this->id)
			$this->load();
		if($this->id){
		    $valueArray = $this->buildNameValueArray($this->dataFields);
		    $diff = array_diff($valueArray,$this->initialData);
            if($diff){
    			$this->history->createEvent('update',$this);
    			//print "updating ".$this->className." ".$this->id."</br>";
    			$this->db->update($this->table, $valueArray, array("id"=>$this->id));
            }
		}else{
			$this->history->createEvent('insert',$this);
			//print "inserting new ".$this->className."... ";
			$this->db->insert($this->table, $this->buildNameValueArray($this->dataFields));
			$this->id = $this->db->insert_id();
			//print_query($this->db);
			//print "new id: ".$this->id."<br>";
		}
		return $this->id;
	}

	public function load(){
		if(!$this->id){
			//print "loading a ".$this->className." without id<br>";
			$testRes = $this->db->get_where($this->table, $this->buildNameValueArray($this->dataFields));
			//print_query($this->db);
			if(rows($testRes)){
				$thisFromDb = $testRes->row_array();
				$this->id = $thisFromDb['id'];
				unset($thisFromDb['id']);
				$this->initialData = $thisFromDb;
			}
		}else{
			if($cachedObj = $this->cache->hasCache(get_class($this),$this->id)){

				//print "loading a ".$this->className." from cache with id: ".$this->id."<br>";
				$this->setAtributes($cachedObj->buildNameValueArray());
				return;
			}else{

				//print "loading a ".$this->className." with id: ".$this->id."<br>";
				$testRes = $this->db->get_where($this->table, array("id"=>$this->id));
				if(rows($testRes)){
					$this->setAtributes($testRes->row_array());
					$this->cache->add($this);
				}
			}
			$this->initialData = $this->buildNameValueArray();
		}
	}

	public function buildNameValueArray($sourceArray=false){
		if(!$sourceArray)
			$sourceArray = $this->dataFields;
		$result = array();
		foreach($sourceArray as $name){
			if(isset($this->$name))
				$result[$name] = $this->$name;
		}
		return $result;
	}

	private function setAtributes($array){
		foreach($array as $name=>$value){
			$this->$name = $value;

			if(endswith($name, "_id")){
				$name = explode("_",$name);
				$name = $name[0];
				if($name == "origin" || $name == "destination")
					$name = "location";
				$objName = ucfirst($name);
				$this->$name = new $objName($this->oh, $value);
				$this->objs[$name] =& $this->$name;
			}

		}
	}

	public function __toString(){
    	$out = $this->buildNameValueArray($this->dataFields);
    	$out['id'] = $this->id;
    	//$out = array();
    	foreach($this->objs as $name=>$obj){
    		$curString = (string)$obj;
    		$newString = "";
    		$count = 0;
    		foreach(preg_split("/(\r?\n)/", $curString) as $line){
    			if($count)
				    $newString .= "     ".$line."\n";
				$count++;
			}
    		$out[$name] = $newString;
    	}
        return str_replace("Array",$this->className."(".$this->id.")",print_r($out, true));
    }



    function delete(){
    	if(!$this->id){
			$this->load();
    	}
		if($this->id){
			$this->history->createEvent('delete',$this);
			$this->db->delete($this->table,array("id"=>$this->id));
			$this->id = 0;
			return true;
		}
		else
			return false;
	}
}




