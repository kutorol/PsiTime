<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Display_lib
{
    /**
     * Страница авторизации
     * @param $data
     */
	public function login_view($data)
	{
		$CI =& get_instance();

        $CI->load->view('default/all/header.php',$data);
        $CI->load->view('default/login/content.php',$data);
        $CI->load->view('default/all/footer.php',$data);

	}


    /**
     * Страница авторизации
     * @param $data
     */
    public function view_common($data)
    {
        $CI =& get_instance();

        $CI->load->view('default/all/header.php',$data);
        $CI->load->view('default/common/content.php',$data);
        $CI->load->view('default/all/footer.php',$data);

    }

		
}