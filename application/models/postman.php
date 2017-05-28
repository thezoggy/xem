<?
class Postman{
	private $db = null;
	private $oh = null;
	private $master = null;

    public $originName;
    public $destinationNames;
    public $identifier;
    public $element = null;

    public $origin;
    public $destinations = array();

    public $locations = array();

	function __construct($oh, $identifier, $originName, $destinationNames=false){
		$this->db = $oh->db;
		$this->oh = $oh;

		$this->locations = buildLocations($this->oh);

		$this->identifier = $identifier;
		$this->originName = $originName;
        if($destinationNames){
            if(is_array($destinationNames))
                $this->destinationNames = $destinationNames;
            else {
                $this->destinationNames = array();
                $this->destinationNames[] = $destinationNames;
            }
        }
        $this->buildObejcs();
        log_message('debug', "new Postman(oh,$identifier,$originName,".implode('|', justNames($this->destinations)).")");
	}

	private function buildObejcs(){
	    $tmpDestinationNames = array();
	    foreach($this->locations as $locID=>$curLocation){
	        if($curLocation->name == 'master'){
	            $this->master = $curLocation;
	        }
	        if($curLocation->name == $this->originName){
	            $this->origin = $curLocation;
                continue;
	        }
	        if($this->destinationNames){
    	        foreach($this->destinationNames as $key=>$curName){
    	            if($curName == $curLocation->name && $curLocation->name != 'master'){
    	                unset($this->destinationNames[$key]);
    	                $this->destinationNames[$locID] = $curLocation->name;
    	                $this->destinations[$locID] = $curLocation;
    	                continue 2;
    	            }
    	        }
	        }else{ // if we did not get any destination names we use all
	            if($curLocation->name != 'master'){
                    $tmpDestinationNames[$locID] = $curLocation->name;
                    $this->destinations[$locID] = $curLocation;
	            }
	        }
	    }
        // handle when origin is not found or inactive (ex: tvrage)
        if(!$this->origin){
            log_message('debug', "Origin not found or inactive.. skipping");
            return;
        }
	    if($tmpDestinationNames)
	        $this->destinationNames = $tmpDestinationNames;
        if(!startswith($this->identifier, 'xem_')){
    	    $seasons = $this->db->get_where('seasons',array('location_id'=>$this->origin->id,'identifier'=>$this->identifier));
            if($seasons) {
                foreach ($seasons->result_array() as $curSeason) {
                    $cur_element = $this->db->get_where('elements',
                        array('id' => $curSeason['element_id'], 'parent' => 0));
                    if(rows($cur_element)) {
                        $cur_element = getFirst($cur_element);
                        if($cur_element['status'] > 0) {
                            $e = new Element($this->oh, $cur_element['id']);
                            $this->element = $e;
                        }
                    }
                }
            }

        }else{
            $i = explode('_', $this->identifier);
	        $this->element = new Element($this->oh, $i[1]);
        }

	}

	function resolveAddress($season=null, $episode=null, $absolute=null){
	    log_message('debug', "running resolveAddress for ".$season."x".$episode."a".$absolute);
	    if($season == null && $episode == null && $absolute == null){
	        return false;
	    }

	    if($season == null && $episode == null){
	        $seasonEpisode = $this->getSeasonAndEpisode($this->origin, $absolute);
	        if(!$seasonEpisode){ // absolute out of range
	            $conTypes = $this->getAllConTypesFor($this->origin);
	            foreach ($conTypes as $destinationKey=>$contype) {
	                if($contype['passthru'] == 'full' OR $contype['passthru'] == 'absolute'){
	                    $resolved = $this->passthruResolver($this->origin, $this->destinations[$destinationKey], $contype['passthru'], null, null, $absolute);
	                    if ($resolved) {
                	        $season = $resolved['season'];
                	        $episode = $resolved['episode'];
                	        break;
	                    }
	                }
	            }
	            if(!$resolved)
	                return false;
	        }else{
    	        $season = $seasonEpisode[0];
    	        $episode = $seasonEpisode[1];
	        }
	    }
	    log_message('debug', "getting new address for ".$season."x".$episode."a".$absolute);
        if(!$this->isThereInfoForSeason($season))
            return false;

        $out = false;
	    foreach ($this->destinations as $curDestination){
            log_message('debug', 'resolving address on '.$curDestination->name);
	        $route = $this->getRoute($this->origin, $curDestination);

            if(!$route)
                continue;
	        log_message('debug', "final route from ".$this->originName." to ".$curDestination->name." is: ".$this->originName."->".implode("->", justNames($route)));



	        $newSeason = array();
	        $newEpisode = array();
	        $newAbsolute = array();

	        $newSeason[1] = $season;
	        $newEpisode[1] = $episode;
	        $newAbsolute[1] = $absolute;

	        $lastWaypoint = $this->origin;

            log_message('debug', "start: ".$this->origin->name.": ".$newSeason[1]."x".$newEpisode[1]."a".$newAbsolute[1]);

            foreach ($route as $curWaypoint) {
                for($i = 1; $i <= count($newSeason); $i++){
                    //log_message('debug', 'current index '.$i);

                    $beforeSeason = $newSeason[$i];
                    $beforeEpisode = $newEpisode[$i];
                    $beforeAbsolute = $newAbsolute[$i];

                    $directMaster = false;
                    if ($curWaypoint->name == 'master') {
    	                $directMaster = $this->getDirectConObj($lastWaypoint, $this->master, $newSeason[$i], $newEpisode[$i]);
                    }elseif ($lastWaypoint->name == 'master'){
    	                $directMaster = $this->getDirectConObj($this->master, $curWaypoint, $newSeason[$i], $newEpisode[$i]);
                    }

                    $createdNewIndex = false;
                    if($directMaster){
                        $first = true;
                        log_message('debug', 'found '.count($directMaster).' direct connection(s)');
                        $first = true;
                        foreach ($directMaster as $curDirectMaster) {

                            if($first)
                                $n = $i;
                            else{
                                $n++;
                                log_message('debug', 'creating new address index '.$n);
    	                        $createdNewIndex = true;
                            }
            	            $newSeason[$n] = (int)$curDirectMaster['destination_season'];
            	            $newEpisode[$n] = (int)$curDirectMaster['destination_episode'];
        	                $newAbsolute[$n] = (int)$this->getAbsolute($curWaypoint, $newSeason[$n], $newEpisode[$n]);
        	                $first = false;
                        }
                    }else{
        	            $passthru = $this->getPassthruType($lastWaypoint, $curWaypoint);

    	                //$newAbsolute = (int)$this->getAbsolute($curWaypoint, $newSeason, $newEpisode);
        	            $finals = $this->passthruResolver($lastWaypoint, $curWaypoint, $passthru, $newSeason[$i], $newEpisode[$i], $newAbsolute[$i]);
                        $newSeason[$i] = $finals['season'];
                        $newEpisode[$i] = $finals['episode'];
                        $newAbsolute[$i] = $finals['absolute'];
                    }
                    log_message('debug', "waypoint: ".$curWaypoint->name."[".$i."] lastAdress(".$beforeSeason."x".$beforeEpisode."a".$beforeAbsolute.") now: ".$newSeason[$i]."x".$newEpisode[$i]."a".$newAbsolute[$i]);
                    if($createdNewIndex)
                        break;

                }

                log_message('debug', 'next waypoint...');
                $lastWaypoint = $curWaypoint;
            }
            for($i = 1; $i <= count($newSeason); $i++){

                $final = $this->buildFinals($newSeason[$i], $newEpisode[$i], $newAbsolute[$i]);
                if($i > 1)
                    $out[$curDestination->name.'_'.$i] = $final;
                else
                    $out[$curDestination->name] = $final;
            }

            //log_message('debug', "address at ".$curDestination->name.": ".$newSeason."x".$newEpisode."a".$newAbsolute);
            continue;
	    }
        /*
        if (count($this->destinations) == 1) {
            $out = $out[$this->destinations[0]->name];
        }
        */
        return $out;
	}

	public function getSeasons() {
	    log_message('debug','searching for seasons');
	    $s = $this->dbSeason($this->origin);
	    if($s){
            log_message('debug', 'using seasons from '.$this->originName);
	        return $s;
	    }
	    $cons = $this->getAllConTypesFor($this->origin);
        foreach($cons as $destinationKey=>$contype) {
            if($contype['passthru'] == 'full'){
                $curLocation = $this->locations[$destinationKey];
                $s = $this->dbSeason($curLocation);
                if($s){
                    log_message('debug', 'using seasons from '.$curLocation->name);
                    return $s;
                }
            }
        }
        return array();
	}

    private function dbSeason($location) {
        $fullElement = new FullElement($this->oh, $this->element->id);
	    $allZero = true;

	    $locationSeasons = array();
	    if($fullElement->seasons){
	        foreach ($fullElement->seasons as $curSeason) {
	            if($curSeason->season_size > 0 AND $curSeason->location_id == $location->id){
	                $allZero = false;
	            }
	            if($curSeason->location_id == $location->id){
    	            $locationSeasons[$curSeason->id] = $curSeason;
	            }
	        }
	    }
	    if(!$allZero)
	        return $locationSeasons;
	    else
            return false;
    }

	private function getDirectConObj($oringin, $destination, $season, $episode){
	    // array('origin_id','destination_id', 'element_id', 'name_id', 'origin_season', 'origin_episode', 'destination_season',	'destination_episode');

        $params = array();
        $params['origin_id'] = $oringin->id;
        $params['destination_id'] = $destination->id;
        $params['element_id'] = $this->element->id;
        $params['origin_season'] = $season;
        $params['origin_episode'] = $episode;
        $directConnections = $this->db->get_where('directrules',$params);
        //log_message('debug', 'direct con sql: '.$this->db->last_query());
	    if(rows($directConnections)){
	        //$directConnection = getFirst($directConnections); //TODO: handle multiple addresses
	        $directConnections = $directConnections->result_array();
	        return $directConnections;
	    }else{
	        return false;
	    }
	}
    private function getDirectConCount($oringin) {
        $params = array();
        $params['origin_id'] = $oringin->id;
        $params['destination_id'] = $this->master->id;
        $params['element_id'] = $this->element->id;
        $directConnections = $this->db->get_where('directrules',$params);
        return rows($directConnections);
    }

    private function isThereInfoForSeason($season){
        $seasons = $this->db->get_where('seasons',array('location_id'=>$this->origin->id, 'element_id'=>$this->element->id, 'season'=>$season));
        if(rows($seasons) > 0) {
            return true;
        }
        return false;
    }

    private function getSeasonAndEpisode($location,$absolute) {
		$this->db->order_by("season", "asc");
        $seasons = $this->db->get_where('seasons',array('location_id'=>$location->id, 'element_id'=>$this->element->id));
        foreach($seasons->result() as $curSeason){
            if($curSeason->season == 0)
                continue;
            log_message('debug',$curSeason->absolute_start);
            if($curSeason->season_size + $curSeason->absolute_start >= $absolute && $curSeason->absolute_start <= $absolute){
                if($curSeason->absolute_start == 0)
                    return array($curSeason->season, $absolute - $curSeason->absolute_start + $curSeason->episode_start -1);
                else
                    return array($curSeason->season, $absolute - $curSeason->absolute_start + $curSeason->episode_start);

            }else{
                if($curSeason->season >= 1)
                    $absolute = $absolute - $curSeason->season_size;
            }
        }
        return false;
    }

    private function getAbsolute($location, $season, $episode) {
        log_message('debug', 'getAbsolute('.$location->name.', '.$season.', '.$episode.')');
        $absolute = 1;
        if($season == 0)
            return 0;

		$this->db->order_by("season", "asc");
        $seasons = $this->db->get_where('seasons',array('location_id'=>$location->id, 'element_id'=>$this->element->id));
        //log_message('debug', $this->db->last_query());
        foreach($seasons->result() as $curSeason){

            //$absolute = $absolute - $curSeason->episode_start + 1;

            if ($curSeason->season > 0 AND $curSeason->absolute_start > 0){
                $absolute = ($curSeason->absolute_start);
                log_message('debug', 'absolute_start '.$absolute);
            }

            if((int)$curSeason->season == (int)$season){
                $final = (int)$absolute + ((int)$episode - $curSeason->episode_start);
                log_message('debug', 'absolute return '.$final);
                return $final;
            }
            if($curSeason->season > 0)
                $absolute = $absolute + $curSeason->season_size;

            log_message('debug', 'season: '.$curSeason->season.' absolute: '.$absolute);
        }
    }

    private function getPassthruType($origin,$destination) {
        //array("origin_id","destination_id","element_id","type");

        $passthru = $this->db->get_where('passthrus',array('origin_id'=>$origin->id, 'destination_id'=>$destination->id, 'element_id'=>$this->element->id));
        if(rows($passthru) == 1){
            $passthru = getFirst($passthru);
            return $passthru['type'];
        }else{
            return false;
        }
    }

    private function getAllConTypesFor($origin) {

        $conections = array();
        foreach ($this->locations as $key=>$curDestination) {
            $conections[$key] = array();
            $passthru = $this->getPassthruType($origin, $curDestination);
            $conections[$key]['passthru'] = $passthru;
            $directConCount = $this->getDirectConCount($origin);
            $conections[$key]['direct'] = $directConCount;
        }
        return $conections;
    }

    private function areConnected($origin, $destination){
        $passthru = $this->getPassthruType($origin, $destination);
        if($passthru)
            return true;

        $directConCount = 0;
        if($destination->name == 'master'){
            $directConCount = $this->getDirectConCount($origin);
        }elseif($origin->name == 'master'){
            $directConCount = $this->getDirectConCount($destination);
        }

        if($directConCount){
            return true;
        }

        return false;
    }

    private function passthruResolver($from, $to, $type, $season, $episode, $absolute){
        log_message('debug',"passthruResolver called with: ".$from->name.", ".$to->name.", ".$type.", ".$season.", ".$episode.", ".(string)$absolute);
        if($type == 'sxxexx'){
            $dAbsolute = $this->getAbsolute($to, $season, $episode);
            log_message('debug',$to->name."-".$dAbsolute);
            return $this->buildFinals($season, $episode, $dAbsolute);
        }else if($type == 'absolute'){
            if($absolute == null){
	            $absolute = $this->getAbsolute($from, $season, $episode);
	            log_message('debug','absolute passthru. calculating absolute: '.$absolute);
            }
	        $seasonEpisode = $this->getSeasonAndEpisode($to, $absolute);
	        if(!$seasonEpisode)// absolute out of range
	            $seasonEpisode = $this->getSeasonAndEpisode($from, $absolute);
	        $dSeason = $seasonEpisode[0];
	        $dEpisode = $seasonEpisode[1];
            return $this->buildFinals($dSeason, $dEpisode, $absolute);
        }else if($type == 'full'){ //full

            if($season == null && $episode == null){
	            $seasonEpisode = $this->getSeasonAndEpisode($from, $absolute);
    	        if(!$seasonEpisode)// absolute out of range
    	            return false;
	            $season = $seasonEpisode[0];
	            $episode = $seasonEpisode[1];
            }

            if($absolute == null){
	            $absolute = $this->getAbsolute($from, $season, $episode);
            }
            return $this->buildFinals($season, $episode, $absolute);
        }else{
            return false;
        }

    }


    private function getRoute($curOrigin, $finalDestination, $toCheck=null) {
        if($this->areConnected($curOrigin, $finalDestination)){
            return array($finalDestination);
        }else{
            if ($toCheck == null) {
                $toCheck = $this->locations;
            }

            unset($toCheck[$curOrigin->id]);
            unset($toCheck[$finalDestination->id]);
            foreach ($toCheck as $key => $curDestination) {
                //print "checking if it is worth from ".$curOrigin->name." to ".$curDestination->name."<br>";
                if(!$this->areConnected($curOrigin, $curDestination))
                    continue;

               // print "calling: getRoute(".$curDestination->name.", ".$finalDestination->name.")<br>";
                $route = $this->getRoute($curDestination, $finalDestination, $toCheck);
                if($route){
                    $combinedArray = array();
                    $combinedArray[] = $curDestination;
                    foreach ($route as $curWaypoint){
                        $combinedArray[] = $curWaypoint;
                    }
                    return $combinedArray;
                }
            }
        }
        return false;
    }

    private function buildFinals($finalSeason,$finalEpisode,$finalAbsolute){
        return array('season'=>(int)$finalSeason,'episode'=>(int)$finalEpisode,'absolute'=>(int)$finalAbsolute);
    }

}

?>