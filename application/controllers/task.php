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

        $data = [];
        $data['title'] = 'Личный кабинет в TimeBig пользователя: ';
        //получаем нужные куки
        $data = $this->common->getCookie($data);

        //проверка зашел или нет юзер уже (true для того, чтобы не редиректило если что)
        $check = $this->common->checkAuth();
/*
        if($check['check'] == false)
        {
            if($check['title_error'] == '')
                $this->common->dropCookie(true, '', 'Вам необходимо авторизоваться!');
            else
                $this->common->dropCookie(true, '', $check['title_error']);


        }
*/

        print_r($check);


        $this->display_lib->display($data, 'common');
	}


}
