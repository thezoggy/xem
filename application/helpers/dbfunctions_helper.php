<?

function userNameByID($db,$id){
	$user = $db->get_where('users',array('user_id'=>$id));
	if(rows($user)){
		$user = getFirst($user);
		return $user['user_nick'];
	}
	return 'unknown';
}

function grantAccess($lvl=0){
	if(!$lvl)
		return true;
	$CI =& get_instance();
	if($CI->session->userdata('logged_in'))
		if($CI->session->userdata('user_lvl')>=$lvl)
			return true;
	return false;
}

function getShows($db,$term=false){
    $query = "SELECT `id`, `main_name`, `last_modified`, `created` FROM `elements` WHERE `type` = 'show' AND `status` > 0 AND `parent` = 0 ORDER BY `main_name`";
    if($term) {
        $query = "SELECT  e.id, e.main_name, e.last_modified, e.created, n.name FROM `elements` AS e LEFT JOIN `names` AS n ON n.element_id = e.id WHERE (n.name LIKE '%" . $term . "%' OR e.main_name LIKE '%" . $term . "%' OR n.name SOUNDS LIKE '" . $term . "' AND n.language = 'us' OR e.main_name SOUNDS LIKE '" . $term . "' AND n.language = 'us' ) AND `status` > 0  AND `parent` = 0 GROUP BY e.main_name ORDER BY e.main_name";
    }

    $shows = $db->query($query);
    // log_message('debug', $db->last_query());
    if(!$shows) {
        return array();
    }
    $shows = $shows->result();
    if($shows) {
        return $shows;
    } else {
        return array();
    }
}

function rows($db_result){
	if($db_result)
		if($db_result->num_rows())
			return $db_result->num_rows();
	return 0;
}
function buildLocations($oh, $all=false){
    if($all)
    	$locations = $oh->db->get('locations');
    else
    	$locations = $oh->db->get_where('locations',array('status'=>1));
	$locationsArray = array();
	if(rows($locations))
		foreach($locations->result_array() as $curLocation){
			$locationsArray[$curLocation['id']] = new Location($oh, $curLocation['id']);
		}
	return $locationsArray;
}

function buildSimpleLanguageArray($langs){
    $out = array();
    // i will let this crash when no languages are set a check does not make sense in running env
    foreach ($langs->result() as $curLang) {
        $out[$curLang->id] = $curLang->name;
    }
    return $out;
}

function getFirst($row){
	$row = $row->result_array();
	return $row[0];
}

function print_o($obj){
	print "<pre>".$obj."</pre>";
}

function print_query($db){
	print "<pre>";
	print_r($db->last_query());
	print "</pre>";
}

function pretty_locations($locations){
	$spans = array();
	foreach($locations as $location){
		$spans[] = '<span class="entityname '.$location->name.'">'.$location->name.'</span>';
	}
	return implode(",",$spans);
}

function startswith($haystack, $needle){
    return strpos($haystack, $needle) === 0;
}

function endswith($haystack, $needle){
    return startswith(strrev($haystack), strrev($needle));
}

function imgLazy($arg,$bool=false){
	$path = $arg;
	if(is_array($arg))
		$path = $arg['src'];
	$fullpath = substr(BASEPATH, 0, strlen(BASEPATH)-strlen('system/'))."/".$path;
	if(file_exists($fullpath))
		return img($arg,$bool);
	else
		return "<!-- no image found at ".$fullpath." -->";
}
function anchorEncode($url,$toLink=false,$attr=false){
	return anchor(htmlentities($url), $toLink, $attr);
}


function zero_pad($input, $length=2){
	return str_pad($input, $length , "0", STR_PAD_LEFT);
}

function curPageURL() {
	$pageURL = 'http';
	if (isset($_SERVER["HTTPS"]))
		if ($_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}

	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function seasonSort($seasonA,$seasonB) {
   	return $seasonA->season > $seasonB->season;
}

function showSort($showA,$showB) {
   	return $showA->main_name > $showB->main_name;
}

function seasonKeySort($sa,$sb){
	return $sa > $sb;
}

function hasEditRight($oh, $element_id){
    $e = new FullElement($oh, $element_id);
    return  userHasLvl($e->status);
}

function userHasLvl($level){
    $CI =& get_instance();
    if (!$CI->session->userdata('logged_in')) {
        return false;
    }
    return  ($CI->session->userdata('user_lvl') >= $level && $level > 0);
}

function justNames($objs) {
    $out = array();
    foreach ($objs as $obj)
        $out[] = $obj->name;
   return $out;
}

?>