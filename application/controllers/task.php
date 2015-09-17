<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Обязательно класс называть с большой буквы, а файл с маленькой буквы!!!
 * Class Task
 */
class Task extends CI_Controller {

    /**
     * страница авторизации
     * login page
     */
	public function index()
	{

        $data = [];
        $data['title'] = 'Личный кабинет в TimeBig пользователя: ';
        //получаем нужные куки
        $data = $this->common->getCookie($data);

        //проверка зашел или нет юзер уже (true для того, чтобы не редиректило если что)
        $check = $this->common->checkAuth(true);

        if(!$check)
            $this->common->redirect_to('welcome/logout/danger', 'Вам необходимо авторизироваться', 'error', 'danger');

        $this->display_lib->view_common($data);
	}


}
