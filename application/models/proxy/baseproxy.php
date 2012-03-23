<?
class Baseproxy{
    protected $proxyAdress = "";

    protected $db = null;
	protected $oh = null;

	protected $destination = "";

    protected $url = "";
    protected $rawData = null;

    protected $delimiter = " - ";

    public $validUrl = true;

    function __construct(Objectholder $oh, $url, $destination, $delimiter=" - "){
   		$this->db = $oh->db;
		$this->oh = $oh;

		$this->url = $url;
		$this->destination = $destination;
		$this->delimiter = $delimiter;
		//print "<br>new proxy<br>";
		$this->contentType($this->url);
    }

    public function contentType($url) {
        $this->contentType = 'Content-Type:text/xml';
    }
    public function call() {
        passthruCall($this->url);
    }

    protected function passthruCall($url) {
    	header('Cache-Control: max-age=900');
    	header("Location: ".$this->proxyAdress.$url);
    	exit;
    }
    protected function xml_join($root, $append) {
        
        //print '|'.$append->getName().'('.$append->count().')|';
        if ($append) {
            if (strlen(trim((string) $append))==0 && $append->count() > 0) {
                
                //print '+'.$append->getName().'+';
                $xml = $root->addChild($append->getName());
                
                foreach($append->children() as $child) {
                    $this->xml_join($xml, $child);
                }
            } else {
                //print '*'.$append->getName().'*';
                $xml = $root->addChild($append->getName(), $this->my_encode((string) $append));
            }
            foreach($append->attributes() as $n => $v) {
                $xml->addAttribute($n, $v);
            }
        }else{
            //print 'NO';
            $xml = $root->addChild($append->getName(), $this->my_encode((string) $append));
        }
    }
    protected function my_encode($str){
    	$str = str_replace("&", "&amp;", $str);
    	$str = str_replace("<", "&lt;", $str);
    	$str = str_replace(">", "&gt;", $str);
    	return $str;
    }

}
?>