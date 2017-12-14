<?

class Content extends DBObject {

    private $CI = null;
    private $editablePages = array('index/doc');

    function __construct($oh, $id=0) {
        $dataFields = array("content");
        $this->CI =& get_instance();

        parent::__construct($oh, $dataFields, $id);
    }

    function getEdit() {
        $editPageButtons = false;
        foreach ($this->editablePages as $item) {
            if(strtolower($this->id) == $item) {
                $editPageButtons = true;
            }
        }

        if($this->CI->session->userdata('user_lvl') >= 4 && $editPageButtons) {
            return $this->CI->load->view('cms_edit', array('page' => $this->id), true);
        }
        return '';
    }
}

?>