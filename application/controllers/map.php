<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Map extends CI_Controller {

	function __construct(){
		parent::__construct();

		$this->load->helper("url");
		$this->load->model('dbobjectcache');
		$this->history = new History($this->db,$this->session);
		$this->dbcache = new DBCache($this->db);
		$this->oh = new Objectholder($this->db, $this->dbobjectcache, $this->history, $this->dbcache);

		$_REQUEST = array_merge($_POST, $_GET);
	}

	public function index(){
        $this->_fullOut('success', array(), 'You reached the guiding service of xem. you can find help at http://thexem.de/doc');
	}

    function all() {
        $identifier = $_REQUEST['id'];
        $origin = $_REQUEST['origin'];
        $destination = false;
        if(isset($_REQUEST['destination']))
            $destination = $_REQUEST['destination'];
        $p = new Postman($this->oh, $identifier, $origin, $destination);
        if(!$p->element){
            $this->_fullOut('failure', array(), 'no show with the '.$origin.'_id '.$identifier.' found');
            return;
        }
        $data = array();
        if(!$destination)
            $destination = 'all';
        $cacheData = $this->oh->dbcache->load('map_out', $p->element->id, $origin.'_'.$destination);
        $cachedMsg = '';
        $absolute = 1;
        if(!$cacheData){
            $seasons = $p->getSeasons();
            foreach ($seasons as $season) {
                if($season->absolute_start > 0)
        			$absolute = $season->absolute_start;

                for ($i = 0; $i < $season->season_size; $i++) {
                    $curEpNumber = $i + $season->episode_start;
                    $new = $p->resolveAddress($season->season, $curEpNumber);
                    $originData = array('season'=>(int)$season->season, 'episode'=>$curEpNumber);
                    if($season->season != 0){
                        $originData['absolute'] = (int)$absolute;
                        $absolute++;
                    }else {
                        $originData['absolute'] = 0;
                    }
                    $new[$origin] = $originData;
                    $data[] = $new;
                }
            }
        }else{
            $data = $cacheData;
            $cachedMsg = 'this was a cached version';
        }

        $this->oh->dbcache->save('map_out', $p->element->id, $origin.'_'.$destination, 259200, $data); // save into db cache for 3 days (259200s)
        $this->_fullOut('success', $data, 'full mapping for '.$identifier.' on '.$origin.'. '.$cachedMsg);
    }

    function single() {
        $identifier = $_REQUEST['id'];
        $origin = $_REQUEST['origin'];

        $season = null;
        $episode = null;
        $absolute = null;
        if(isset($_REQUEST['season']))
            $season = $_REQUEST['season'];
        if(isset($_REQUEST['episode']))
            $episode = $_REQUEST['episode'];
        if(isset($_REQUEST['absolute']))
            $absolute = $_REQUEST['absolute'];

        $destination = false;
        if(isset($_REQUEST['destination']))
            $destination = $_REQUEST['destination'];
        $p = new Postman($this->oh, $identifier, $origin, $destination);
        $data = $p->resolveAddress($season, $episode, $absolute);
        $this->_fullOut('success', $data, 'single mapping for '.$identifier.' on '.$origin.'.');
    }


    function names() {
        $identifier = $_REQUEST['id'];
        $origin = $_REQUEST['origin'];
        $destination = false;
        if(isset($_REQUEST['destination']))
            $destination = $_REQUEST['destination'];
        // i dont need the postman but its an easy way wo get the element id... one might want to refactor that one day
        $p = new Postman($this->oh, $identifier, $origin, $destination);
        $e = new FullElement($this->oh, $p->element->id);
        $names = $e->groupedNames(true);

        // make the season -1 nice for everyone else ... change it into 'all'
        if(isset($names[-1])){
			$names['all'] = $names[-1];
			unset($names[-1]);
        }
        $this->_fullOut('success', $names);
    }


    function sbLegacy() {
        $locations = $this->db->get('locations');
		$tvdb_id = 0;
		foreach($locations->result() as $loc){
			if($loc->name == "tvdb"){
				$tvdb_id = $loc->id;
				break;
			}
		}

        $fullString = '';
		$elements = $this->db->get_where('elements');
		foreach($elements->result() as $curElement){
		    $names = $this->db->get_where('names', array('element_id'=>$curElement->id));
			$namesStrings = array();
			$namesStrings[] = $curElement->main_name;
			if(rows($names)){
				foreach($names->result() as $name){
					if($name->season == -1 && $name->language == 'us')
						$namesStrings[] = str_replace("'", "\'",$name->name);
				}
			}
			if(count($namesStrings) == 0)
				continue;

		    $seasons = $this->db->get_where('seasons', array('element_id'=>$curElement->id,'location_id'=>$tvdb_id));
		    $curTvdb_identifier = null;
		    if(rows($seasons)){
		        $curTvdb_identifier = getFirst($seasons);
		        $curTvdb_identifier = $curTvdb_identifier['identifier'];
		    }
            if(!$curTvdb_identifier)
                continue;
		    $fullString .= $curTvdb_identifier.": ";
			$fullString .= "'".implode("','", $namesStrings)."',\r\n";
		}
		//$this->output->set_content_type('text/plain');

		$this->output->set_header("Content-type: text/html; charset=utf-8\r\n");
		$this->output->set_output($fullString);
    }
    function havemap(){
        $origin = $_REQUEST['origin'];
        $curLocationID = 0;
        $locations = $this->db->get('locations');
		foreach($locations->result() as $loc){
			if($loc->name == $origin){
				$curLocationID = $loc->id;
				break;
			}
		}
        $ids = array();
        $elements = $this->db->get('elements');
        foreach($elements->result() as $element){
            $seasons = $this->db->get_where('seasons', array('element_id'=>$element->id, 'location_id'=>$curLocationID));


            foreach($seasons->result() as $curRow){
                if($curRow->identifier && !in_array($curRow->identifier, $ids))
                    $ids[] = $curRow->identifier;
            }
        }
        $this->_fullOut('success', $ids, 'These shows have some kind of mapping');
    }
    /*
    {	RESULT_SUCCESS:"success",
        RESULT_FAILURE:"failure",
        RESULT_TIMEOUT:"timeout",
        RESULT_ERROR:"error",
        RESULT_DENIED:"denied"
    }*/
	function _fullOut($status, $data ,$message=''){
		if(!$data)
			$data=array();
		$this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$status,'data'=>$data,'message'=>$message)));
	}
}

?>