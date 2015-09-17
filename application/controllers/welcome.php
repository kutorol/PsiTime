<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Обязательно класс называть с большой буквы, а файл с маленькой буквы!!!
 * Class Welcome
 */
class Welcome extends CI_Controller {

    /**
     * страница авторизации
     * login page
     */
	public function index()
	{
        $data = [];
        $data['title'] = 'Добро пожаловать в TimeBig';
        $data = $this->common->getCookie($data);

        if($data['error'] == '')
        {
            //проверка зашел или нет юзер уже
            $this->common->checkAuth();

            //если нажата кнопка входа в форме
            if(isset($_POST['enter_to_time']))
            {
                //правила валидации данных из полей
                $this->form_validation->set_rules('login', 'Логин', 'trim|alpha_numeric|required|min_length[5]|max_length[20]|xss_clean');
                $this->form_validation->set_rules('pass', 'Пароль', 'trim|alpha_dash|required|min_length[5]|max_length[20]|xss_clean');

                //если валидация не прошла проверку - показываем вьюху, а там ошибки покажут
                if($this->form_validation->run() == FALSE)
                {
                    $this->display_lib->login_view($data);
                    return true;
                }

                //получаем данные из формы и чистим сразу от шлака
                $data['login'] = $this->common->clear($this->input->post('login', true));
                $data['pass'] = $this->common->clear($this->input->post('pass', true));

                if($data['login'] != '' && $data['pass'] != '')
                {
                    //подгружаем модель
                    $this->load->model('welcome_model');
                    //проверяем совпадения в базе
                    $q = $this->welcome_model->checkUser($data['login'], $data['pass']);
                    if($q)
                    {
                        $this->session->set_userdata(['session_user'=> 'avtoriz|'.md5('02u4hash3894').'|'.$data['login']]);
                        set_cookie('login', $data['login'], time()+9999999999);
                        set_cookie('chech_user', '1', time()+9999999999);
                        $this->common->redirect_to('task', 'Вы вошли под логином - '.$data['login'], 'text', 'success');
                    }
                    else
                        $data['error'] = 'Пользователя с таким логином и паролем не найдено!';
                }
                else
                    $data['error'] = 'Введите все данные в форму!';
            }
        }




        $this->display_lib->login_view($data);
	}


    /**
     * Функция выхода из ЛК
     * @param string $status
     */
    public function logout($status = '')
    {
        $title = $this->common->clear($this->input->cookie('error', true));
        $status = $this->common->clear($status);

        $this->session->unset_userdata('session_user');

        if($title != '' && $status != '')
            $this->common->dropCookie(true, '', $title, 'text', $status);
        else
            $this->common->dropCookie(true, '', 'Вы успешно вышли!', 'text', 'success');
    }


}

