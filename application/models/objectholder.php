<?
class Objectholder{
	public $db = null;
	public $cache = null;
	public $history = null;
	public $dbcache = null;

	function __construct($db, $cache, $history, $dbcache){
		$this->db = $db; // database (non persistent)
		$this->cache = $cache; // obj cache
		$this->history = $history; // history obj
		$this->dbcache = $dbcache; // database / persistent cache
	}
}
?>