<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Session extends CI_Session {

    // --------------------------------------------------------------------

    /**
     * sess_update()
     *
     * Do not update an existing session on ajax or xajax calls
     *
     * @access    public
     * @return    void
     */
	public function sess_update()
	{
		$CI =& get_instance();

		if ( ! $CI->input->is_ajax_request())
		{
			parent::sess_update();
		}
	}
}

// ------------------------------------------------------------------------
/* End of file MY_Session.php */
/* Location: ./application/libraries/MY_Session.php */