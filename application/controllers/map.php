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
        // check for required params
        if(!isset($_REQUEST['id']) || !isset($_REQUEST['origin'])) {
            $this->_fullOut('failure', 'missing required parameter(s)');
            return;
        }
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
            $cachedMsg = ' this was a cached version';
        }

        $this->oh->dbcache->save('map_out', $p->element->id, $origin.'_'.$destination, 259200, $data); // save into db cache for 3 days (259200s)
        if(!$data){
            $this->_fullOut('failure', array(), 'no single connection');
        }else{
            // header('X-NotExtCached: this was not served up from the ext cache');
            $this->output->set_header('Cache-Control: max-age=3600, public'); // 1hr cache
            $this->output->cache(60); // let CI cache results for 1hr
            $this->_fullOut('success', $data, 'full mapping for '.$identifier.' on '.$origin.'.'.$cachedMsg);
        }

    }

    function single() {
        // check for required params
        if(!isset($_REQUEST['id']) || !isset($_REQUEST['origin'])) {
            $this->_fullOut('failure', 'missing required parameter(s)');
            return;
        }
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
        if(!$p->element){
            $this->_fullOut('failure', array(), 'no show with the '.$origin.'_id '.$identifier.' found');
            return false;
        }

        $data = $p->resolveAddress($season, $episode, $absolute);
        $this->_fullOut('success', $data, 'single mapping for '.$identifier.' on '.$origin.'.');
    }


    function names() {
        // check for required params
        if(!isset($_REQUEST['id']) || !isset($_REQUEST['origin'])) {
            $this->_fullOut('failure', 'missing required parameter(s)');
            return;
        }
        $identifier = $_REQUEST['id'];
        $origin = $_REQUEST['origin'];
        $destination = false;

        // provide way to just return name to track down id...
        $includeDefaultNames = false;
        if(isset($_REQUEST['defaultNames']))
            $includeDefaultNames = (bool) $_REQUEST['defaultNames'];

        if(isset($_REQUEST['destination']))
            $destination = $_REQUEST['destination'];
        // i dont need the postman but its an easy way wo get the element id... one might want to refactor that one day
        $p = new Postman($this->oh, $identifier, $origin, $destination);
        if(!$p->element){
            $this->_fullOut('failure', array(), 'no show with the '.$origin.'_id '.$identifier.' found');
            return false;
        }
        $e = new FullElement($this->oh, $p->element->id);
        $names = $e->groupedNames(true);

        if($includeDefaultNames) {
            $names[-1] = $e->main_name;
        }

        // make the season -1 nice for everyone else ... change it into 'all'
        if(isset($names[-1])){
			$names['all'] = $names[-1];
			unset($names[-1]);
        }

        if(!count($names)){
            $this->_fullOut('failure', array(), 'no extra names for '.$origin.'_id '.$identifier.' found');
            return false;
        }

        $this->output->set_header('Cache-Control: max-age=3600, public'); // 1hr cache
        $this->output->cache(60); // let CI cache results for 1hr
        $this->_fullOut('success', $names);
        return false;
    }


    function allNames() {
        //$locations = $this->db->get('locations');
        $locations = $this->db->get_where('locations',array('status'=>1));
        $origin = false;
        if(isset($_REQUEST['origin']))
            $origin = $_REQUEST['origin'];
        else{
            $this->_fullOut('failure', array(), 'please provide origin name');
            return false;
        }

        $language = 'all';
        if(isset($_REQUEST['language']))
            $language = $_REQUEST['language'];

        $includeDefaultNames = false;
        if(isset($_REQUEST['defaultNames']))
            $includeDefaultNames = (bool) $_REQUEST['defaultNames'];

        $includeSeasonNumbers = false;
        if(isset($_REQUEST['seasonNumbers']))
            $includeSeasonNumbers = (bool) $_REQUEST['seasonNumbers'];

        $seasonFilterType = false;
        $seasonFilterSeason = false;
        if(isset($_REQUEST['season'])){
            $seasonFilterTmp = $_REQUEST['season'];

            if(strlen($seasonFilterTmp) >= 3 && !is_numeric($seasonFilterTmp) && strpos($seasonFilterTmp, ',') === false){ // this will not account for 1;10;1,2
                $seasonFilterFirstTwo = substr( $seasonFilterTmp, 0, 2);
                switch ($seasonFilterFirstTwo) {
                    case 'ne': // is not equal to
                        $seasonFilterType = 'ne';
                        break;
                    case 'gt': // is greater than
                        $seasonFilterType = 'gt';
                        break;
                    case 'ge': // is greater than or equal to
                        $seasonFilterType = 'ge';
                        break;
                    case 'lt': // is less than
                        $seasonFilterType = 'lt';
                        break;
                    case 'le': // is less than or equal to
                        $seasonFilterType = 'le';
                        break;
                    case 'eq': // is equal to
                        $seasonFilterType = 'eq';
                        break;
                    default:
                        $this->_fullOut('failure', array(), 'did not understand season string');
                        return false;
                }
                $seasonFilterSeason = substr( $seasonFilterTmp, 2, strlen($seasonFilterTmp)-2);
            }elseif(is_numeric($seasonFilterTmp)){ // only a season number
                $seasonFilterSeason = (int)$seasonFilterTmp;
                $seasonFilterType = 'eq'; // is equal to
            }elseif(!(strpos($seasonFilterTmp, ',') === false)){ // if we find a "," assume we got a list of seasonnumbers
                 $seasonFilterSeason = explode(',', $seasonFilterTmp);
                 $seasonFilterType = 'eq'; // is equal to: for each seasonnumber in the array that is
            }else{ // i dont know how to deal with anything else
                $this->_fullOut('failure', array(), 'did not understand season string');
                return false;
            }
            if(!is_numeric($seasonFilterSeason) && !is_array($seasonFilterSeason)){
                $this->_fullOut('failure', array(), 'did not understand season string');
                return false;
            }
        }
		$origin_id = 0;
		foreach($locations->result() as $loc){
			if($loc->name == $origin){
				$origin_id = $loc->id;
				break;
			}
		}
		$out = array();

        // only process the parent (not drafts to prevent leaking of info)
		$elements = $this->db->get_where('elements', array('parent'=>0));
        foreach($elements->result() as $curElement){
            // only proceed if the show is not deleted (status 0)
            if($curElement->status < 1)
                continue;

		    $names = $this->db->get_where('names', array('element_id'=>$curElement->id));
			$namesStrings = array();
			if($includeDefaultNames)
                // TODO: respect season filter/language filter data?
    			$namesStrings[] = $curElement->main_name;
			if(rows($names)){
				foreach($names->result() as $name){
					if($language == $name->language || $language == 'all'){

                        if(is_array($seasonFilterSeason)){
                            if(in_array($name->season, $seasonFilterSeason)){
                                $namesStrings[] = $this->_allNames_helper_name_season($name, $includeSeasonNumbers);
                            }
                        }else{
                            switch ($seasonFilterType) {
                                case 'ne': // is not equal to
                                    if($name->season != $seasonFilterSeason)
                                        $namesStrings[] = $this->_allNames_helper_name_season($name, $includeSeasonNumbers);
                                    break;
                                case 'gt': // is greater than
                                    if($name->season > $seasonFilterSeason)
                                        $namesStrings[] = $this->_allNames_helper_name_season($name, $includeSeasonNumbers);
                                    break;
                                case 'ge': // is greater than or equal to
                                    if($name->season >= $seasonFilterSeason)
                                        $namesStrings[] = $this->_allNames_helper_name_season($name, $includeSeasonNumbers);
                                    break;
                                case 'lt': // is less than
                                    if($name->season < $seasonFilterSeason)
                                        $namesStrings[] = $this->_allNames_helper_name_season($name, $includeSeasonNumbers);
                                    break;
                                case 'le': // is less than or equal to
                                    if($name->season <= $seasonFilterSeason)
                                        $namesStrings[] = $this->_allNames_helper_name_season($name, $includeSeasonNumbers);
                                    break;
                                case 'eq': // is equal to
                                    if($name->season == $seasonFilterSeason)
                                        $namesStrings[] = $this->_allNames_helper_name_season($name, $includeSeasonNumbers);
                                    break;
                                default: // no filter set
                                    $namesStrings[] = $this->_allNames_helper_name_season($name, $includeSeasonNumbers);
                                    break;
                            }
                        }
					}
				}
			}
			if(count($namesStrings) == 0)
				continue;

		    // exclude out seasons with no identifier (db sets null but gui sets empty string)
            $seasons = $this->db->get_where('seasons', array('element_id'=>$curElement->id,'location_id'=>$origin_id,'`identifier` != '=>''));
            // $hasAnime = $this->db->get_where('seasons', array('element_id'=>$curElement->id,'location_id'=>'3'));
            // if(rows($hasAnime)) { continue; }
		    $cur_identifier = null;
		    if(rows($seasons)){
		        $cur_identifier = getFirst($seasons);
		        $cur_identifier = $cur_identifier['identifier'];
		    }
            if(!$cur_identifier)
                continue;
		    $out[$cur_identifier] = $namesStrings;
		}

        $this->output->set_header('Cache-Control: max-age=3600, public'); // 1hr cache
        $this->output->cache(60); // let CI cache results for 1hr
        $this->_fullOut('success', $out);
        return false;
    }

    function _allNames_helper_name_season($nameObj, $includeSeasonNumbers){
        if($includeSeasonNumbers){
            return array($nameObj->name=>(int)$nameObj->season);
        }else{
            return $nameObj->name;
        }

    }


    function havemap(){
        // check for required params
        if(!isset($_REQUEST['origin'])) {
            $this->_fullOut('failure', 'missing required parameter(s)');
            return;
        }
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
        // only process the parent (not drafts to prevent leaking of info)
        $elements = $this->db->get_where('elements', array('parent'=>0));
        foreach($elements->result() as $element){
            // only proceed if the show is not deleted (status 0)
            if($element->status < 1)
                continue;
            $seasons = $this->db->get_where('seasons', array('element_id'=>$element->id, 'location_id'=>$curLocationID));

            foreach($seasons->result() as $curRow){
                if($curRow->identifier && !in_array($curRow->identifier, $ids))
                    $ids[] = $curRow->identifier;
            }
        }
        sort($ids); // sort output so it is easier to find bad data
        $this->output->set_header('Cache-Control: max-age=10800, public'); // 3hr cache
        $this->output->cache(180); // let CI cache results for 3hr
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
        if(!$data) {
            $data = array();
        }
        $this->output->set_content_type('application/json')->set_output(json_encode(array('result'=>$status,'data'=>$data,'message'=>$message)));
	}
}

?>