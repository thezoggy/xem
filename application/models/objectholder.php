<?
class Objectholder{
	public $db = null;
	public $cache = null;
	public $history = null;
	
	function __construct($db, $cache, $history){
		$this->db = $db;
		$this->cache = $cache;
		$this->history = $history;
	}
}
?>