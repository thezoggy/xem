<?
class TvdbProxy extends Baseproxy {
    protected $proxyAdress = 'http://www.thetvdb.com'; #http://www.thetvdb.com
    private $apiKey = '9DAF49C96CBF8DAC';

    private $callType = "";

    function __construct(Objectholder $oh, $url, $destination, $delimiter=" - "){
        parent::__construct($oh, $url, $destination);
    }

    public function contentType($url){
        if($this->is_api($url)){
            $this->callType = 'api';
        }elseif ($this->is_banner($url)){
            $this->callType = 'banner';
        }else{
            $this->validUrl = false;
        }
    }

    public function call(){
        if($this->callType == 'api'){
            header('Content-Type:text'); #text/xml
            return $this->call_api($this->url);
        }if($this->callType == 'banner'){
            header('Content-Type:image/jpeg');
            return $this->passthruCall($this->url);
        }else
            return false;
    }

    private function call_api($url){
        $api_regex = '/\/api\/([0-9A-F]+)\/series\/(\d+)\/?(.*)/';
    	$result = preg_match($api_regex, $url, $matches);
    	if (!$result)
    		$this->passthruCall($url);
    	$api_key = $matches[1];
    	$tvdb_id = $matches[2];
    	$rest = $matches[3];
    	if(!startswith($rest, 'all')){ // this is only a call for the default information
    		$this->passthruCall($url);
    		return;
    	}
    	$api_language_regex = '/all\/(.*)\.xml/';
    	$lang_result = preg_match($api_language_regex, $rest, $lang_matches);
    	$requested_language = 'xx';
    	if(!$result)
    		$requested_language = $lang_matches[1];

    	// for shows that don't need a transform just go to TVDB
    	//print $this->destination;
    	$p = new Postman($this->oh, $tvdb_id, 'tvdb', $this->destination);
    	if (!$p->element)
    		$this->passthruCall($url);
    	else{
    	    $cacheData = $this->oh->dbcache->load('proxy_out', $p->element->id, 'tvdb_'.$this->destination.'_'.$requested_language);
    	    if($cacheData)
    	        return $cacheData;
            // don't print warnings about the awful XML that TVDB gives
            //error_reporting(E_ERROR | E_PARSE);
            //print "calling now: ".$this->proxyAdress.$url;
            $this->rawData = simplexml_load_file($this->proxyAdress.$url);
            //print_r($this->rawData->asXML());
            //print  $this->transform($this->rawData, $p);
            $out = $this->transform($this->rawData, $p);
            $this->oh->dbcache->save('proxy_out', $p->element->id, 'tvdb_'.$this->destination.'_'.$requested_language, 259200, $out); // save into db cache for 3 days (259200s)
    	    return $out;
    	}
    }


    private function is_api($url) {
        return substr($url, 0, 4) == '/api';
    }

    private function is_banner($url) {
        return substr($url, 0, 7) == '/banner';
    }

    private function transform($xml, $postman){
        $newAdressHistory = array();
        $new_xml = new SimpleXMLElement("<?xml version='1.0' encoding='utf-8'?><Data></Data>");

		$series = $this->duplicate_series($xml->Series);

		$this->xml_join($new_xml, $series);

        for ($i = 0; $i < count($xml->Episode); $i++){
            $ep = $this->duplicate_ep($xml->Episode[$i]);

            /*
            print "#######################\n";
		    print "current index: ".$i;
		    print "\n";

		    print "ep:  ";
            print $ep->SeasonNumber."|".$ep->EpisodeNumber."|".$ep->absolute_number;
            print "\n";
            */
		    $newAdress = $postman->resolveAddress((int)$ep->SeasonNumber, (int)$ep->EpisodeNumber, (int)$ep->absolute_number);



		    if(!$newAdress){
		        //print "\n";
                $this->xml_join($new_xml, $ep); // just add the normal unmodified version
		        continue;
		    }

		    $newAdress = $newAdress[$this->destination];
		    //log_message('debug',print_r($newAdress, true));
		    if($newAdress['season'] == 0 && $newAdress['episode'] == 0 &&  $newAdress['absolute'] != 0){
		        //print "\n";
                $this->xml_join($new_xml, $ep); // just add the normal unmodified version
		        continue;
		    }

		    //print_r($newAdress);
		    $newAdressKey = implode("|", $newAdress);
            /*print $this->destination.": ".$newAdress['season'];
            print "|".$newAdress['episode'];
            print "|".$newAdress['absolute']."\n";*/

		    if(isset($newAdressHistory[$newAdressKey])){

		        $otherEpIndexs = $newAdressHistory[$newAdressKey];
		        $otherEp = $this->duplicate_ep($new_xml->Episode[$otherEpIndexs["new"]]);

                /*print "adress ".$newAdressKey." already in use. combining (current) ".(int)$ep->SeasonNumber."|". (int)$ep->EpisodeNumber."|". (int)$ep->absolute_number." and ".(int)$otherEp->SeasonNumber."|". (int)$otherEp->EpisodeNumber."|". (int)$otherEp->absolute_number."\n";
		        print "otherEpIndexs: old:".$otherEpIndexs['old']." new:".$otherEpIndexs['old']."\n";*/
                $otherEp->Overview .= $this->delimiter.$ep->Overview;
		        $otherEp->EpisodeName .= $this->delimiter.$ep->EpisodeName;

                $this->xml_join($new_xml, $otherEp);
		        unset($new_xml->Episode[$otherEpIndexs['new']]); // remove old ep
		        //print "\n";
		        continue;
		    }

		    $ep->SeasonNumber = $newAdress['season'];
		    $ep->EpisodeNumber = $newAdress['episode'];
			$ep->absolute_number = $newAdress['absolute'];

			$ep->Overview = $ep->Overview;
            $this->xml_join($new_xml, $ep);

		    $newAdressHistory[$newAdressKey] = array("new"=>count($new_xml->Episode)-1,"old"=>$i);
	        //print "\n";
		}
        //print_r($newAdressHistory);

        unset($new_xml->Episode[0]["Episode"]); // simplexml can be weird sometimes

        return $new_xml->asXML();


    }


    private function duplicate_ep($ep){
        $ep_clone = clone $ep;
        /*
    	$ep->Combined_episodenumber = $ep->Combined_episodenumber;
    	$ep->Combined_season = $ep->Combined_season;
    	$ep->DVD_chapter = $ep->DVD_chapter;
    	$ep->DVD_discid = $ep->DVD_discid;
    	$ep->DVD_episodenumber = $ep->DVD_episodenumber;
    	$ep->DVD_season = $ep->DVD_season;
    	$ep->Director = $ep->Director;
    	$ep->EpImgFlag = $ep->EpImgFlag;
    	$ep->EpisodeName = $ep->EpisodeName;
    	$ep->FirstAired = $ep->FirstAired;
    	$ep->GuestStars = $ep->GuestStars;
    	$ep->IMDB_ID = $ep->IMDB_ID;
    	$ep->Language = $ep->Language;
    	$ep->Overview = $ep->Overview;
    	$ep->ProductionCode = $ep->ProductionCode;
    	$ep->Rating = $ep->Rating;
    	$ep->RatingCount = $ep->RatingCount;
    	$ep->Writer = $ep->Writer;
    	$ep->absolute_number = $ep->absolute_number;
    	$ep->filename = $ep->filename;
    	$ep->lastupdated = $ep->lastupdated;
    	$ep->seasonid = $ep->seasonid;
    	$ep->seriesid = $ep->seriesid;




        if($ep_clone->FirstAired != ''){
            print 'its empty-';
        }

        if(!$ep_clone->FirstAired){
            print "its not here-";
        }

        if($ep_clone->FirstAired){
            print "its here: ".$ep_clone->FirstAired.'-';
        }*/
    	return $ep_clone;
    }
    private function duplicate_series($series){

        $series_clone = clone $series;
        /*
    	$series->id = $series->id;
    	$series->Actors = $series->Actors;
    	$series->Airs_DayOfWeek = $series->Airs_DayOfWeek;
    	$series->Airs_Time = $series->Airs_Time;
    	$series->ContentRating = $series->ContentRating;
    	$series->FirstAired = $series->FirstAired;
    	$series->Genre = $series->Genre;
    	$series->IMDB_ID = $series->IMDB_ID;
    	$series->Language = $series->Language;
    	$series->Network = $series->Network;
    	$series->NetworkID = $series->NetworkID;
    	$series->Overview = $series->Overview;
    	$series->Rating = $series->Rating;
    	$series->RatingCount = $series->RatingCount;
    	$series->Runtime = $series->Runtime;
    	$series->SeriesID = $series->SeriesID;
    	$series->SeriesName = $series->SeriesName;
    	$series->Status = $series->Status;
    	$series->added = $series->added;
    	$series->addedBy = $series->addedBy;
    	$series->banner = $series->banner;
    	$series->fanart = $series->fanart;
    	$series->lastupdated = $series->lastupdated;
    	$series->poster = $series->poster;
    	$series->zap2it_id = $series->zap2it_id;
        */
    	return $series_clone;
    }

}
?>