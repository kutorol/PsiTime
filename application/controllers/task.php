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
        $data = $this->common->initApp('task_controller', 0, $folderView, true, true, ['pattern'=>['title', 'login', '%login%']]);
        if(isset($data['return_notification']))
            return true;

        if($data['checkAuth']['check'] === false)
            $this->common->dropCookie(true, '', ($data['checkAuth']['title_error'] != '') ? $data['checkAuth']['title_error'] : $data['languages_desc'][0]['errorAuth'][$data['segment']]);


        $this->display_lib->display($data, $folderView);
	}


}
