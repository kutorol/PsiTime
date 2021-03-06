<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Display_lib
{

    /**
     * Показываем вьюху юзеру.
     * Show Vyuha user.
     * @param $data - данные (data)
     * @param string $name_folder - название папки, в которой находится контент (the name of the folder that contains the content)
     * @param string $name_file - название файла, в котором находится контент (name of the file that contains the content)
     */
    public function display($data, $name_folder = 'login', $name_file = 'content.php')
    {
        $CI =& get_instance();

        $CI->load->view(DEFAULT_VIEW.'/all/header.php',$data);
        $CI->load->view(DEFAULT_VIEW.'/all/header_menu.php',$data);
        $CI->load->view(DEFAULT_VIEW.'/all/title.php',$data);
        $CI->load->view(DEFAULT_VIEW.'/'.$name_folder.'/'.$name_file,$data);
        $CI->load->view(DEFAULT_VIEW.'/all/footer.php',$data);
    }
		
}