<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends SuperController {
    var $humanReadableErrors = array('insuficentData'=>'You missed to provide important information.',
                                     'emailInUse'=>'This email is associated with another account.',
                                     'nickInUse'=>'This username is already in use.',
                                     'lvlZero'=>'Account not activated or is banned!',
                                     'usernameOrPasswordWrong'=>'Wrong combination of username and password.',
                                     'oldMatchesNew'=>'New password matches current password.',
                                     'unknown'=>'Unknown error, contact an admin to investigate.');

	function __construct(){
		parent::__construct();
	    $this->load->library('recaptcha');
	    $this->load->library('form_validation');
	    $this->load->helper('form');

	    //email stuff
        $this->load->helper('email'); // only needed to validate the email address
	    $this->load->library('email');
        $this->email->from('info@thexem.de', 'XEM');
	}

	public function index(){
		if(!$this->session->userdata('logged_in')) {
			redirect('user/login');
		}
		$this->out['title'] = 'User Center';
		$this->_loadView(); // nothing means load the index for the current controller
	}

	public function login(){
		$this->out['title'] = 'Login and Registration';

		if(isset($_POST['user'])&&isset($_POST['pw'])){
			if($this->simpleloginsecure->login($_POST['user'],$_POST['pw'])){
			    if(!$this->session->userdata('user_last_login')) // if there is no last login data this is the first login -> redirect to faq
			        redirect('/faq');

				redirect($this->out['uri2']);
			}else
				$this->out['login_unsuccessfull'] = true;
		}

		if(!$this->session->userdata('logged_in')){
		    if (isset($this->out['login_unsuccessfull'])) {
		        $this->out['reason'] = $this->humanReadableErrors[$this->simpleloginsecure->last_error];
		    }


			$this->_loadView('login');
		}else{
			redirect($this->out['uri2']);
		}
	}

    function check_captcha($recaptcha) {
        if(!empty($recaptcha)) {
            $response = $this->recaptcha->verifyResponse($recaptcha);
            if(isset($response['success']) and $response['success'] === true) {
                return true;
            } else {
                $this->form_validation->set_message('check_captcha', 'The reCaptcha is invalid.');
                return false;
            }
        }
    }

    function register() {
        $this->out['title'] = 'Registration';
        $this->form_validation->set_rules('user', 'Username', 'trim|required|min_length[2]|max_length[20]|xss_clean');
        $this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email');
        $this->form_validation->set_rules('pw', 'Password', 'trim|required');
        $this->form_validation->set_rules('pw_check', 'Password Confirm', 'trim|required|matches[pw]');
        $this->form_validation->set_rules('g-recaptcha-response', 'reCaptcha', 'required|callback_check_captcha');

        $recaptcha = $this->input->post('g-recaptcha-response');
        if($this->form_validation->run()) {
            if(valid_email($_POST['email'])) {
                $userdata = $this->simpleloginsecure->create($_POST['user'], $_POST['email'], $_POST['pw']);
                if($userdata) {
                    // user activation code
                    $this->email->to($_POST['email']);
                    $this->email->subject('Registration at XEM');
                    $emailBody = $this->load->view('email/registration_code', $userdata, true);
                    $this->email->message($emailBody);
                    $this->email->send();
                    //echo $this->email->print_debugger();
                    $this->_loadView('registerConfirm');
                    return true;
                } else {
                    $this->out['register_unsuccessfull'] = true;
                    $this->out['reason'] = $this->humanReadableErrors[$this->simpleloginsecure->last_error];
                }
            } else {
                $this->out['register_unsuccessfull'] = true;
                $this->out['reason'] = '<p>That is not a valid email.</p>';
            }
        } elseif(isset($_POST['user']) && isset($_POST['email']) && isset($_POST['pw'])) {
            $this->out['register_unsuccessfull'] = true;
            $this->out['reason'] = '<p>Required fields are incomplete.</p>';
        }

        $this->_loadView('register');
        return false;
    }

    function activate() {
        $userdata = $this->simpleloginsecure->activate($this->uri->segment(3));
        if($userdata){
            // info mail
            $emailBody = $this->load->view('email/registration_info', $userdata, true);
            foreach ($this->simpleloginsecure->getUserBasedOn(4, 'email_new_account') as $cur_user) {
                $this->email->to($cur_user['user_email']);
                $this->email->subject('New Account | '.$userdata['user_nick']);
                $this->email->message($emailBody);
                $this->email->send();
                log_message('debug', 'Sending email_new_account to '. $cur_user['user_email']);
            }

            redirect('user/login');
        } else {
            $this->_loadView('register');
        }
    }

	function logout(){
		if(!$this->session->userdata('logged_in')){
			$this->_loadView('login');
		}else{
			$this->simpleloginsecure->logout();
			redirect($this->out['uri2']);
		}
	}

    function changePW() {
        $this->form_validation->set_rules('old_pw', 'Current Password', 'trim|required');
        $this->form_validation->set_rules('new_pw', 'New Password', 'trim|required');
        $this->form_validation->set_rules('new_pw_check', 'New Password Confirm', 'trim|required|matches[new_pw]');
        // if user is not logged in, take them to login
        if(!$this->session->userdata('logged_in')) {
            $this->_loadView('login');
        } else {
            if($this->form_validation->run()) {
                if($this->simpleloginsecure->edit_password($this->session->userdata('user_email'), $_POST['old_pw'], $_POST['new_pw'])) {
                    // upon changing pw, we redirect them to homepage and kill their session
                    $this->simpleloginsecure->logout();
                    redirect('user/login');
                    return true;
                } else {
                    $this->out['error'] = true;
                    $this->out['reason'] = $this->humanReadableErrors[$this->simpleloginsecure->last_error];
                }
            } else {
                $this->out['error'] = true;
                $this->out['reason'] = ''; // not needed due to validation errors being used
            }
            $this->_loadView();
            return false;
        }
    }

	function emailSettings() {
	    if (grantAccess(4)) {
	        $config = array();

            $config['email_new_account'] = 0;
	        if(isset($_POST['email_new_account']) && $_POST['email_new_account'] == "1")
	            $config['email_new_account'] = 1;

            $config['email_new_show'] = 0;
	        if(isset($_POST['email_new_show']) && $_POST['email_new_show'] == "1")
	            $config['email_new_show'] = 1;

            $config['email_public_request'] = 0;
	        if(isset($_POST['email_public_request']) && $_POST['email_public_request'] == "1")
	            $config['email_public_request'] = 1;

            log_message('debug', 'saving email settings for user id '. $this->user_id. ' values: '.print_r($config, true));
	        $this->simpleloginsecure->setUserConfig($this->user_id, $config);
	    }
        redirect('user');

	}

}
?>