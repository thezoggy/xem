<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once('phpass-0.4/PasswordHash.php');

define('PHPASS_HASH_STRENGTH', 8);
define('PHPASS_HASH_PORTABLE', false);

/**
 * SimpleLoginSecure Class
 * includes hacks/mods - caution on upgrading
 *
 * Makes authentication simple and secure.
 *
 * Simplelogin expects the following database setup. If you are not using
 * this setup you may need to do some tweaking.
 *
 *
 *   CREATE TABLE `users` (
 *     `user_id` int(10) unsigned NOT NULL auto_increment,
 *     `user_email` varchar(255) NOT NULL default '',
 *     `user_pass` varchar(60) NOT NULL default '',
 *     `user_date` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Creation date',
 *     `user_modified` datetime NOT NULL default '0000-00-00 00:00:00',
 *     `user_last_login` datetime NULL default NULL,
 *     PRIMARY KEY  (`user_id`),
 *     UNIQUE KEY `user_email` (`user_email`),
 *   ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
 *
 * @package   SimpleLoginSecure
 * @version   2.1.2
 * @author    Stéphane Bourzeix, Pixelmio <stephane[at]bourzeix.com>
 * @copyright Copyright (c) 2012-2013, Stéphane Bourzeix
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 * @link      https://github.com/DaBourz/SimpleLoginSecure
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
	 * Update a user account
	 *
	 * Only updates the email, just here for you can
	 * extend / use it in your own class.
	 *
	 * @access	public
	 * @param integer
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function update($user_id = null, $user_email = '', $auto_login = true)
	{
		$this->CI =& get_instance();

		//Make sure account info was sent
		if($user_id == null OR $user_email == '') {
			return false;
		}

		//Check against user table
		$this->CI->db->where('user_id', $user_id);
		$query = $this->CI->db->get_where($this->user_table);

		if ($query->num_rows() == 0){ // user don't exists
			return false;
		}

		//Update account into the database
		$data = array(
					'user_email' => $user_email,
					'user_modified' => date('c'),
				);

		$this->CI->db->where('user_id', $user_id);


		if(!$this->CI->db->update($this->user_table, $data)) //There was a problem!
			return false;

		if($auto_login){
			$user_data['user_email'] = $user_email;
			$user_data['user'] = $user_data['user_email']; // for compatibility with Simplelogin


			$this->CI->session->set_userdata($user_data);
			}
		return true;
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

		//Check email against user table
		$this->CI->db->where('user_email', $user_identifier);
		$query = $this->CI->db->get_where($this->user_table);

		if($query->num_rows() == 0){
			//Check username against user table
			$this->CI->db->where('user_nick', $user_identifier);
			$query = $this->CI->db->get_where($this->user_table);
		}


		if ($query->num_rows() > 0)
		{


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

			$this->CI->db->simple_query('UPDATE ' . $this->user_table  . ' SET user_last_login = "' . date('c') . '" WHERE user_id = ' . $user_data['user_id']);

			//Set session data
			$this->_updateUserData($user_data['user_id']);


			return true;
		}
		else
		{
			$this->last_error = 'usernameOrPasswordWrong';
			return false;
		}

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


	/**
	* Edit a user password
	* @author    Stéphane Bourzeix, Pixelmio <stephane[at]bourzeix.com>
	* @author    Diego Castro <castroc.diego[at]gmail.com>
	*
	* @access  public
	* @param  string
	* @param  string
	* @param  string
	* @return  bool
	*/
	function edit_password($user_email = '', $old_pass = '', $new_pass = '')
	{
		$this->CI =& get_instance();
		// Check if the password is the same as the old one
		$this->CI->db->select('user_pass');
		$query = $this->CI->db->get_where($this->user_table, array('user_email' => $user_email));
		$user_data = $query->row_array();

		$hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
		if (!$hasher->CheckPassword($old_pass, $user_data['user_pass'])){ //old_pass is the same
			$this->last_error = 'oldMatchesNew';
			return FALSE;
		}

		// Hash new_pass using phpass
		$user_pass_hashed = $hasher->HashPassword($new_pass);
		// Insert new password into the database
		$data = array(
			'user_pass' => $user_pass_hashed,
			'user_modified' => date('c')
		);

		$this->CI->db->set($data);
		$this->CI->db->where('user_email', $user_email);
		if(!$this->CI->db->update($this->user_table, $data)){ // There was a problem!
			$this->last_error = 'unknown';
			return FALSE;
		} else {
			return TRUE;
		}
	}

	/** XEM functions **/

	/**
	* Set session data
	 *
	 * override of: $this->CI->session->set_userdata($user_data);
	 *
	 * @access	private
	 * @return	void
	*/
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

	/**
	* Get user data based on params - used for notifier
	 *
	 * @access	public
	 * @param	integer
	 * @param	string
	 * @return	array
	*/
	function getUserBasedOn($lvl, $config, $value=1) {
		$this->CI =& get_instance();
		$this->CI->db->select('user_nick, user_email, user_lvl');
		$this->CI->db->where('config_'.$config, $value);
		$this->CI->db->where('user_lvl >=', $lvl);
		$query = $this->CI->db->get_where($this->user_table);
		if ($query->num_rows() > 0){
			return $query->result_array();
		} else {
			return array();
		}
	}

	/**
	* Set moderator notification options
	 *
	 * @access	public
	 * @return	void
	*/
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
	* Activate user
	 *
	 * @access	public
	 * @param	string
	 * @return	bool
	*/
	function activate($activationCode) {
		$this->CI =& get_instance();
		$this->CI->db->where('user_activationcode', $activationCode);
		$query = $this->CI->db->get_where($this->user_table);
		$userdata = null;
		if ($query->num_rows() > 0) {
			$userdata = $query->row_array();
			$data = array(
						'user_activationcode' => 'xxx',
						'user_modified' => date('c'),
						'user_lvl' => 1
					);
			$this->CI->db->where('user_activationcode', $activationCode);
			if($this->CI->db->update($this->user_table,$data)) {
				return $userdata;
			}
		}
		return false;
	}

}
?>
