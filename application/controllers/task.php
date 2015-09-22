<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Обязательно класс называть с большой буквы, а файл с маленькой буквы!!!
 * Be sure to call the class with a capital letter, as a file with a small letter !!!
 * Class Task
 */
class Task extends CI_Controller {

    /**
     * Главная страница личного кабинета
     * Home private office
     */
	public function index()
	{

        $folderView = 'common';
        $data = $this->common->initApp('Личный кабинет в TimeBig пользователя: <span class="label label-success">%login%</span>', 0, $folderView, true, false, ['pattern'=>['title', 'login', '%login%']]);
        if(isset($data['return_notification']))
            return true;

        //echo $this->uri->segment(1);

        if($data['checkAuth']['check'] === false)
            $this->common->dropCookie(true, '', ($data['checkAuth']['title_error'] != '') ? $data['checkAuth']['title_error'] : 'Вам необходимо авторизоваться!');

        echo "<pre>";
        print_r($data);
exit;

        $this->display_lib->display($data, $folderView);
	}


}
