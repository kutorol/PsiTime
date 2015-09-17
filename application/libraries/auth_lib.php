<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth_lib
{

	private function _my_redirect_on_vhod()
	{
		set_cookie('error','Вы ввели не правильные данные',2);
		redirect(base_url().'index.php/admini/login_a');
	}
	

	public function do_login($login = '',$pass = '')
	{
		$CI =& get_instance();
		if($login == '' || $pass == ''){$this->_my_redirect();}
		else
		{
			$pass = sha1(md5($pass));
			
			if(is_numeric($login)){$this->_my_redirect_on_vhod();}
			
			$CI->db->where('login',$login);
			$CI->db->where('pass',$pass);
			$q = $CI->db->get('tut_admin');
			$admin = $q->row_array();
			
			if(empty($admin)){$this->_my_redirect_on_vhod();}
			
			$ses = array();
			$ses['admin_logined'] = 'yes_s';
			$CI->session->set_userdata($ses);
			
			redirect(base_url().'index.php/admini/vhod');
		}
	}
	
	/*
	*	������� ������ �� �������. 
	* ������� ������ �� ������ @$ses['admin_logined'].
	*/
	public function do_logout()
	{
		$CI =& get_instance();
		$ses = array();
		$ses['admin_logined'] = '';
		$CI->session->unset_userdata($ses);
		redirect(base_url().'index.php/admini/login_a');
	}
	
	/*
	* ������� �������� ������ (��� ������ ��������� � �������� ��������)
	* ������� �� ������ ������ 'admin_logined', ���� ��� ����� 'yes_s', �� ��������� true(������ �� ������).
	* ����� �������� �� �������� �����
	*/
	public function check_admin()
	{
		$CI =& get_instance();
		$data['log_a'] = sha1(md5($CI->session->userdata('admin_logined')));
		if($data['log_a'] == sha1(md5('yes_s'))){return true;}
		else{$this->_my_redirect_on_vhod();}
	}
	
	
}