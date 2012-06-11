<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpass-0.1/PasswordHash.php');

define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);

/**
 * SimpleLoginSecure Class
 *
 * Makes authentication simple and secure.
 *
 * Simplelogin expects the following database setup. If you are not using
 * this setup you may need to do some tweaking.
 *
 *
 *
CREATE  TABLE IF NOT EXISTS `u2361_xem`.`users` (
  `user_id` INT NOT NULL AUTO_INCREMENT ,
  `user_nick` VARCHAR(45) NULL ,
  `user_email` VARCHAR(255) NOT NULL ,
  `user_pass` VARCHAR(60) NOT NULL ,
  `user_lvl` INT NULL DEFAULT 0 ,
  `user_date` DATETIME NOT NULL ,
  `user_modified` DATETIME NOT NULL ,
  `user_last_login` DATETIME NULL DEFAULT NULL ,
  `user_activationcode` VARCHAR(32) NULL ,
  PRIMARY KEY (`user_id`) ,
  UNIQUE INDEX `user_email` (`user_email` ASC) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
 *
 * @package   SimpleLoginSecure
 * @version   1.0.1
 * @author    Alex Dunae, Dialect <alex[at]dialect.ca>
 * @copyright Copyright (c) 2008, Alex Dunae
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      http://dialect.ca/code/ci-simple-login-secure/
 */
class SimpleLoginSecure
{
	var $CI;
	var $user_table = 'users';
	var $last_error = null;

	/**
	 * Create a user account
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function create($user_nick = '', $user_email = '', $user_pass = '', $auto_login = true)
	{
		$this->CI =& get_instance();

		//Make sure account info was sent
		if($user_nick == '' OR $user_email == '' OR $user_pass == '') {
		    $this->last_error = 'insuficentData';
			return false;
		}

		//Check against user table
		$this->CI->db->where('user_email', $user_email);
		$query = $this->CI->db->get_where($this->user_table);
		if ($query->num_rows() > 0){ //user_email already exists
		    $this->last_error = 'emailInUse';
		    //print_query($this->CI->db);
			return false;
		}
		//Check against user table
		$this->CI->db->where('user_nick', $user_nick);
		$query = $this->CI->db->get_where($this->user_table);
		if ($query->num_rows() > 0){ //user_nick already exists
		    $this->last_error = 'nickInUse';
			return false;
		}
		//Hash user_pass using phpass
		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		$user_pass_hashed = $hasher->HashPassword($user_pass);

		//Insert account into the database
		$data = array(
					'user_nick' => $user_nick,
					'user_email' => $user_email,
					'user_pass' => $user_pass_hashed,
					'user_date' => date('c'),
					'user_modified' => date('c'),
					'user_activationcode' => md5(microtime())
				);

		$this->CI->db->set($data);

		if(!$this->CI->db->insert($this->user_table)) //There was a problem!
			return false;

		if($auto_login)
			$this->login($user_email, $user_pass);

		return $data;
	}

	/**
	 * Login and sets session variables
	 *
	 * @access	public
	 * @param	string
	 * @param	string
	 * @return	bool
	 */
	function login($user_identifier = '', $user_pass = '')
	{
		$this->CI =& get_instance();

		if($user_identifier == '' OR $user_pass == ''){
		    $this->last_error = 'insuficentData';
			return false;
		}


		//Check if already logged in
		if($this->CI->session->userdata('user_email') == $user_identifier || $this->CI->session->userdata('user_nick') == $user_identifier){
			return true;
		}

		//Check against user table
		$this->CI->db->where('user_email', $user_identifier);
		$query = $this->CI->db->get_where($this->user_table);

		if($query->num_rows() == 0){
			//Check against user table
			$this->CI->db->where('user_nick', $user_identifier);
			$query = $this->CI->db->get_where($this->user_table);
		}


		if ($query->num_rows() > 0){

			$user_data = $query->row_array();
			if($user_data['user_lvl'] < 1){
    		    $this->last_error = 'lvlZero';
				return false;
			}

			$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

			if(!$hasher->CheckPassword($user_pass, $user_data['user_pass'])){
    		    $this->last_error = 'usernameOrPasswordWrong';
    			return false;
			}

			//Destroy old session
			$this->CI->session->sess_destroy();

			//Create a fresh, brand new session
			$this->CI->session->sess_create();

			$this->CI->db->simple_query('UPDATE ' . $this->user_table  . ' SET user_last_login = NOW() WHERE user_id = ' . $user_data['user_id']);

			//Set session data
			$this->_updateUserData($user_data['user_id']);

			return true;
		}
		else{
		    $this->last_error = 'usernameOrPasswordWrong';
			return false;
		}

	}

	function _updateUserData($user_id) {

		//Set session data
	    $this->CI->db->where('user_id', $user_id);
		$query = $this->CI->db->get_where($this->user_table);
		if ($query->num_rows() > 0){
		    $user_data = $query->row_array();

			unset($user_data['user_pass']);
			$user_data['user_lvl'] = (int)$user_data['user_lvl'];
			$user_data['logged_in'] = true;
			$this->CI->session->set_userdata($user_data);
		}
	}

	function changePassword($user_id, $oldPassword='', $newPassword='', $oneTimeCode=''){

		if(($oldPassword == '' AND $oneTimeCode='') OR $newPassword == '')
			return false;

		$this->CI =& get_instance();

		$this->CI->db->where('user_activationcode', $oneTimeCode);
		$query = $this->CI->db->get_where($this->user_table);
		if ($query->num_rows() != 1){
		    return false;
		}else{
		    $user_data = $query->row_array();
		    if((int)$user_data['user_id'] != $user_id)
		        return false;
		}

		if($this->CI->session->userdata('logged_in') OR $oneTimeCode){
			//Check against user table
			$this->CI->db->where('user_id', $user_id);
			$query = $this->CI->db->get_where($this->user_table);

			if ($query->num_rows() > 0){

				$user_data = $query->row_array();
				if((int)$user_data['user_lvl'] < 1)
					return false;

				$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);

				if(!$hasher->CheckPassword($oldPassword, $user_data['user_pass']))
					return false;

				$user_pass_hashed = $hasher->HashPassword($newPassword);

				$data = array(
							'user_pass' => $user_pass_hashed,
							'user_modified' => date('c'),
						);

				$this->CI->db->where('user_id',$user_data['user_id']);
				if(!$this->CI->db->update($this->user_table,$data)){ //There was a problem!
					return false;
				}else{
				    if($oneTimeCode){
        				$this->CI->db->where('user_id',$user_data['user_id']);
        				$this->CI->db->update($this->user_table, array('user_activationcode'=>'xxx'));
				    }
					return true;
				}
			}else
				return false;
		}
		return false;


	}

	/**
	 * Logout user
	 *
	 * @access	public
	 * @return	void
	 */
	function logout() {
		$this->CI =& get_instance();

		$this->CI->session->sess_destroy();
	}

	/**
	 * get user data based on params
	 *
	 */
	function getUserBasedOn($lvl, $config, $value=1) {
		$this->CI =& get_instance();
        $this->CI->db->select('user_nick, user_email, user_lvl');
		$this->CI->db->where('config_'.$config, $value);
        $this->CI->db->where('user_lvl <=', $lvl);

		$query = $this->CI->db->get_where($this->user_table);
		if ($query->num_rows() > 0){
		    return $query->result_array();
		}else{
		    return array();
		}
	}

	function setUserConfig($user_id, $configArray) {
		$this->CI =& get_instance();

		$db_configArray = array();
		foreach ($configArray as $key => $value) {
		    $db_configArray['config_'.$key] = $value;
		}

		$this->CI->db->where('user_id', $user_id);
	    $this->CI->db->update($this->user_table, $db_configArray);

        log_message('debug',$this->CI->db->last_query());

		$this->_updateUserData($user_id);

	}



	/**
	 * Delete user
	 *
	 * @access	public
	 * @param integer
	 * @return	bool
	 */
	function delete($user_id)
	{
		$this->CI =& get_instance();

		if(!is_numeric($user_id))
			return false;

		return $this->CI->db->delete($this->user_table, array('user_id' => $user_id));
	}

	function activate($activationCode){
		$this->CI =& get_instance();
		$this->CI->db->where('user_activationcode', $activationCode);
		$query = $this->CI->db->get_where($this->user_table);
		$userdata = null;
		if ($query->num_rows() > 0){
		    $userdata = $query->row_array();
            $data = array(
    					'user_activationcode' => 'xxx',
    					'user_modified' => date('c'),
    					'user_lvl' => 1
    				);
    		$this->CI->db->where('user_activationcode', $activationCode);
    		if($this->CI->db->update($this->user_table,$data)){
    		    return $userdata;
    		}
		}
	    return false;
	}

}
?>
