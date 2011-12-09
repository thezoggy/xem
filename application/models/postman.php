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

	function __construct($oh, $identifier, $originName, $destinationNames=false){
		$this->db = $oh->db;
		$this->oh = $oh;

		$this->locations = buildLocations($this->oh);

		$this->identifier = $identifier;
		$this->originName = $originName;
        if($destinationNames)
            $this->destinationNames = $destinationNames;
        $this->buildObejcs();

        print $this->element->main_name.'<br/>';
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
    	                $this->destination[$locID] = $curLocation;
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
	    if($tmpDestinationNames)
	        $this->destinationNames = $tmpDestinationNames;

	    $seasons = $this->db->get_where('seasons',array('location_id'=>$this->origin->id,'identifier'=>$this->identifier));
	    if(rows($seasons) >= 1){
	        $season = getFirst($seasons);
	        $this->element = new Element($this->oh, $season['element_id']);
	    }

	}

	function resolveAddress($season=null, $episode=null, $absolute=null){

	    if($season == null && $episode == null && $absolute == null){
	        return false;
	    }

	    if($season == null && $episode == null){
	        $seasonEpisode = $this->getSeasonAndEpisode($this->origin, $absolute);
	        if(!$seasonEpisode)// absolute out of range
	            return false;
	        $season = $seasonEpisode[0];
	        $episode = $seasonEpisode[1];
	    }
	    print($season."x".$episode."a".$absolute);

	    foreach ($this->destinations as $curDestination){
	        print "<br>".$curDestination->name.": ";
	        $toMaster = $this->getDirectConObj($this->origin,$this->master,$season,$episode);
	        if($toMaster){ // direct con to master
	            // now get master values
	            $mSeason = (int)$toMaster['destination_season'];
	            $mEpisode = (int)$toMaster['destination_episode'];

	            // check if we have an direct con to destination
	            $fromMaster = $this->getDirectConObj($this->master,$curDestination,$mSeason,$mEpisode);
	            if($fromMaster){ // yes we do
	                $finalSeason = $fromMaster['destination_season'];
	                $finalEpisode = $fromMaster['destination_episode'];
	                $finalAbsolute = $this->getAbsolute($curDestination, $finalSeason, $finalEpisode);
	                //TODO: collect in out array and not print -.-
	                print_r($this->buildFinals($finalSeason,$finalEpisode,$finalAbsolute));
                    continue;
	            }
	            // if we are here this means there was no direct con from the master to the destination
	            //check for a passthru
	            $passthru = $this->getPassthruType($this->master,$curDestination);
	            if($passthru){
	                $mAbsolute = (int)$this->getAbsolute($this->master, $mSeason, $mEpisode);
    	            $finals = $this->passthruResolver($this->master, $curDestination, $passthru, $mSeason, $mEpisode, $mAbsolute);
	                //TODO: collect in out array and not print -.-
                    print_r($finals);
                    continue;
	            }
	            // if we are here there was no connection to the destination
	        }


	        // if we are here the there was no direct con
            $passthru = $this->getPassthruType($this->origin,$this->master);
            if($passthru){
                // check if we have an direct con to destination
	            $fromMaster = $this->getDirectConObj($this->master,$curDestination,$season,$episode);
	            if($fromMaster){ // yes we do
	                $finalSeason = $fromMaster['destination_season'];
	                $finalEpisode = $fromMaster['destination_episode'];
	                $finalAbsolute = $this->getAbsolute($curDestination, $finalSeason, $finalEpisode);
	                //TODO: collect in out array and not print -.-
	                print_r($this->buildFinals($finalSeason,$finalEpisode,$finalAbsolute));
                    continue;
	            }
	            // if we are here this means there was no direct con from the master to the destination
	            //check for a passthru
	            $passthru = $this->getPassthruType($this->master,$curDestination);
	            if($passthru){
	                $mAbsolute = (int)$this->getAbsolute($this->master, $season, $episode);
    	            $finals = $this->passthruResolver($this->master, $curDestination, $passthru, $season, $episode, $mAbsolute);
	                //TODO: collect in out array and not print -.-
                    print_r($finals);
                    continue;
	            }
	            // if we are here there was no connection to the destination
            }
	        //to master maybe a direct passthru
	        //check for a passthru
            $passthru = $this->getPassthruType($this->origin,$curDestination);
            if($passthru){
                if($absolute == null)
                    $absolute = (int)$this->getAbsolute($this->origin, $season, $episode);
	            $finals = $this->passthruResolver($this->origin, $curDestination, $passthru, $season, $episode, $absolute);
                //TODO: collect in out array and not print -.-
                print_r($finals);
                continue;
            }



            /*

	        $fromMaster = $this->getDirectConObj($this->master,$curDestination,$season,$episode);
	        // direct con
	        if($toMaster && $fromMaster){
	            $dAbsolute = $this->getAbsolute($curDestination, (int)$fromMaster['destination_season'], (int)$fromMaster['destination_episode']);
	            print_r(array($fromMaster['destination_season'],$fromMaster['destination_episode'],$dAbsolute));
	            continue;
	        }

	        // direct passthru
	        $directPassthru = $this->getPassthruType($this->origin,$curDestination);
	        if($directPassthru){
    	        $finals = $this->passthruResolver($this->origin, $curDestination, $directPassthru, $season, $episode, $absolute);
    	        print_r($finals);
    	        continue;
	        }

	        // first a passthru then a direct con
	        $toMasterPassthru = $this->getPassthruType($this->origin,$this->master);
	        if($toMasterPassthru && $fromMaster){
	            print "to master with pass and then a direct con<br/>";
	            $finals = $this->passthruResolver($this->origin, $this->master, $toMasterPassthru, $season, $episode, $absolute);
	            $fromMaster = $this->getDirectConObj($this->master,$curDestination,$finals[0],$finals[1]);

	            continue;
	        }
	        // first a direct con then a passthru
	        $fromMasterPassthru = $this->getPassthruType($this->master,$curDestination);
	        if($toMaster && $fromMasterPassthru){
	            $mSeason = (int)$toMaster['destination_season'];
	            $mEpisode = (int)$toMaster['destination_episode'];
	            $mAbsolute = (int)$this->getAbsolute($this->master, $mSeason, $mEpisode);
	            $finals = $this->passthruResolver($this->master, $curDestination, $fromMasterPassthru, $mSeason, $mEpisode, $mAbsolute);
                print_r($finals);
	            continue;
	        }

	        // both passthru
            if($toMasterPassthru && $fromMasterPassthru){
	            print "to master with pass and another pass<br/>";
	            continue;
	        }
			*/

            print "no con !";

	    }
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
	    if(rows($directConnections)){
	        $directConnection = getFirst($directConnections);
	        return $directConnection;
	    }else{
	        return false;
	    }
	}

    private function getSeasonAndEpisode($location,$absolute) {
		$this->db->order_by("season", "asc");
        $seasons = $this->db->get_where('seasons',array('location_id'=>$location->id, 'element_id'=>$this->element->id));
        foreach($seasons->result() as $curSeason){
            if($curSeason->season_size >= $absolute){
                return array($curSeason->season, $absolute);
            }else{
                $absolute = $absolute - $curSeason->season_size;
            }
        }
    }

    private function getAbsolute($location, $season, $episode) {
        $absolute = 0;
		$this->db->order_by("season", "asc");
        $seasons = $this->db->get_where('seasons',array('location_id'=>$location->id, 'element_id'=>$this->element->id));
        foreach($seasons->result() as $curSeason){
            if($curSeason->season = $season){
                return $absolute + $episode;
            }else{
                $absolute = $absolute + $curSeason->season_size;
            }
        }
    }

    private function getPassthruType($origin,$destination) {
        //array("origin_id","destination_id","element_id","type");

        $passthru = $this->db->get_where('passthrus',array('origin_id'=>$origin->id, 'destination_id'=>$destination->id, 'element_id'=>$this->element->id));
        if(rows($passthru)==1){
            $passthru = getFirst($passthru);
            return $passthru['type'];
        }else{
            return false;
        }
    }

    private function passthruResolver($from, $to, $type, $season, $episode, $absolute){
        if($type == 'sxxexx'){
            $dAbsolute = $this->getAbsolute($to, $season, $episode);
            return $this->buildFinals($season, $episode, $dAbsolute);
        }else{
            if($absolute == null)
	            $asolute = $this->getAbsolute($from, $season, $episode);
	        $seasonEpisode = $this->getSeasonAndEpisode($from, $absolute);
	        if(!$seasonEpisode)// absolute out of range
	            return false;
	        $dSeason = $seasonEpisode[0];
	        $dEpisode = $seasonEpisode[1];
            return $this->buildFinals($dSeason, $dEpisode, $absolute);
        };
    }

    // TODO: implement
    private function recursivePassthru($origin, $destination, $finalDestination){
         if($pType = getPassthruType($origin, $destination)){
            if($finalDestination.name == $destination.name){
                return true;
            }else{


            }

         }
         return false;
    }


    private function buildFinals($finalSeason,$finalEpisode,$finalAbsolute){
        return array('season'=>$finalSeason,'episode'=>$finalEpisode,'absolute'=>$finalAbsolute);
    }

}

?>