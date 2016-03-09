<?
class DBCache{
	public $db = null;
	function __construct($db){
		$this->db = $db;
	}

	function save($type, $namespace, $name, $bestBefore, $cacheData){
		$data = array();
		$data['type'] = $type;
		$data['namespace'] = $namespace;
		$data['name'] = $name;

		if(is_numeric($bestBefore)){ // time offset in seconds
		    $data['best_before'] = date("c", time()+$bestBefore);
		}else{ // we assume a datetime string in something the datetime field in the database will understand (2012-02-29 14:24:52)
		    $data['best_before'] = $bestBefore;
		}

        if(!is_string($cacheData)){
    		$data['data'] = json_encode($cacheData);
		    $data['encoded'] = 1;
        }else{
    		$data['data'] = $cacheData;
        }
		$this->db->set('creation_date', 'NOW()', FALSE); // the special "NOW()" value has to be set like this blame codeignigter
		$success = $this->db->insert('cache', $data);
		if(!$success){ // if insert didnt work delete old record and insert "again"
            $this->delete($type, $namespace, $name);
            $this->db->set('creation_date', 'NOW()', FALSE); // the special "NOW()" value has to be set like this blame codeignigter
		    $this->db->insert('cache', $data);
		}
	}

	function load($type, $namespace, $name){
	    //TODO: refactor this to use the CI where functions see $this->getNamspaceSize()
		$result = $this->db->query("SELECT * FROM `cache` WHERE `type` = '".$type."' AND `namespace` =  '".$namespace."' AND `name` =  '".$name."'");

		if(rows($result)){
			$cache = getFirst($result);
			$best_before = strtotime($cache['best_before']);
			if ($best_before >= time() || $best_before == 0){
			    if((int)$cache['encoded'])
			        $cache['data'] = json_decode($cache['data']);
			    return $cache['data'];
			}else{
                $this->delete($type, $namespace, $name);
			}
		}
		return false;
	}

	private function delete($type, $namespace, $name) {
		$this->db->delete('cache',array("type"=>$type, "namespace"=>$namespace, "name"=>$name));
	}

	function clearNamespace($namespace) {
		return $this->db->delete('cache',array("namespace"=>$namespace));
	}

	function getNamspaceSize($namspace) {
		$this->db->where('namespace', $namspace);
		$result = $this->db->get_where('cache');
		return rows($result);
	}

    /**
     * Clears all cache files from the cache directory + cache table
     */
    public function clear_all_cache() {
        $CI =& get_instance();
        $path = $CI->config->item('cache_path');

        $cache_path = ($path == '') ? APPPATH.'cache/' : $path;

        if ( ! is_dir($cache_path) OR ! is_really_writable($cache_path)) {
            log_message('error', "Unable to access cache path: ".$cache_path);
            return;
        }

        $handle = opendir($cache_path);

        while (($file = readdir($handle)) !== FALSE) {
            //Leave the directory protection alone
            if ($file != '.htaccess' && $file != 'index.html' && $file != '.' && $file != '..') {
                @unlink($cache_path.'/'.$file);
            }
        }

        closedir($handle);
        log_message('info', "Cache cleared.");
        $this->db->truncate('cache');
        log_message('info', "Cache table cleared.");
    }// clear_all_cache

}
?>