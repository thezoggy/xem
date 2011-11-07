<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config = array(
  'contact/index' => array(
    array(
      'field' => 'recaptcha_response_field',
      'label' => 'lang:recaptcha_field_name',
      'rules' => 'required|callback_check_captcha'
    )
  )
);