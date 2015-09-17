<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Captcha 
{
	public function get_captcha()
	{
		$CI = & get_instance();
		$CI->load->helper('captcha');
		$CI->load->helper('string');
		
		$string = random_string('numeric',5);
		$ses_captcha['captcha'] = $string;
		
		$CI->session->set_userdata($ses_captcha);
		
		$vals = array(
		'word'	 => $string,
		'img_path'	 => './img/captcha/',
		'img_url'	 => base_url().'img/captcha/',
		'font_path'	 => './system/fonts/texb.ttf',
		'img_width'	 => '130',
		'img_height' => 40,
		'expiration' => 20
		);

		$cap = create_captcha($vals);
		return $cap['image'];
	}
}
?>