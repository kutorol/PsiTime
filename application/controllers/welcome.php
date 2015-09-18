<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Обязательно класс называть с большой буквы, а файл с маленькой буквы!!!
 * Be sure to call the class with a capital letter, as a file with a small letter !!!
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
        //Получаем куки с текстом и ошибкой, и если они не пусты, то в итоге выводим их сразу на экране.
        $data = $this->common->getCookie($data);
        //если это false, то человек не вошел в аккаунт
        $data['auth_user'] = false;

        //если есть ошибка
        if($data['error'] != '')
        {
            $this->display_lib->display($data, 'login');
            return true;
        }


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
                $this->display_lib->display($data, 'login');
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

                if(isset($q['bad_status']))
                    $data['error'] = 'На данный момент вам нужно дождаться активации от администратора!';
                elseif($q == true)
                {
                    $this->session->set_userdata(['session_user'=> 'avtoriz|'.md5('02u4hash3894').'|'.$data['login']]);
                    setcookie ("login",$data['login'],time()+9999999999,"/");
                    setcookie ("chech_user",1,time()+9999999999,"/");
                    $this->common->redirect_to('task', 'Вы вошли под логином - '.$data['login'], 'text', 'success');
                }
                else
                    $data['error'] = 'Пользователя с таким логином и паролем не найдено!';
            }
            else
                $data['error'] = 'Введите все данные в форму!';
        }

        //показываем вид юзеру
        $this->display_lib->display($data, 'login');
	}


    /**
     * Функция выхода из ЛК
     * The output function of the LC
     * @param string $status - это класс (success, danger, warning ect.) чтобы цветом выделять надпись - bootstrap
     * (This class (success, danger, warning ect.) to highlight the color of the inscription - bootstrap)
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

    /**
     * Функция восстановления пароля
     * The function of password recovery
     */
    public function forgot()
    {
        $data = [];
        $data['title'] = 'Востановления пароля в TimeBig';
        //Получаем куки с текстом и ошибкой, и если они не пусты, то в итоге выводим их сразу на экране.
        $data = $this->common->getCookie($data);

        //если есть ошибку, то показываем вьюху
        if($data['error'] != '')
        {
            $this->display_lib->display($data, 'login/forgot');
            return true;
        }

        //проверка зашел или нет юзер уже
        $this->common->checkAuth();
        //если это false, то человек не вошел в аккаунт
        $data['auth_user'] = false;

        if(isset($_POST['forgot_btn']))
        {
            //правила валидации данных из полей
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[6]|valid_email|xss_clean');

            //если валидация не прошла проверку - показываем вьюху, а там ошибки покажут
            if($this->form_validation->run() == FALSE)
            {
                $this->display_lib->display($data, 'login/forgot');
                return true;
            }

            //получаем данные из формы и чистим сразу от шлака
            $data['email'] = $this->common->clear($this->input->post('email', true));

            if($data['email'] != '')
            {
                //подгружаем модель
                $this->load->model('welcome_model');
                //проверяем совпадения в базе
                $q = $this->welcome_model->checkEmail($data['email']);
                if(!empty($q))
                {
                    //загружаем библиотеку помощи для строк
                    $this->load->helper('string');
                    $new = [];
                    //получаем хэш для пароля
                    $new['hash'] = random_string('alnum', rand(6, 17));
                    //получаем новый пароль
                    $new['password'] = $new_pass = random_string('alnum', rand(7, 15));
                    $new['password'] = sha1(md5($new['password'].$new['hash']));

                    $check = $this->welcome_model->updateUser($new, 'id_user', $q['id_user']);
                    if($check > 0)
                    {
                        //компануем email на отправку
                        $content = "Вы востановили пароль в TimeBig. <br>Ваш логин: <b>".$q['login']."</b><br> Ваш новый пароль: <b><i>".$new_pass."</i></b>";
                        $header = "From: \"Admin\" <timebig@bigmsk.ru>\n";
                        $header .= 'Content-type: text/html; charset="utf-8"';

                        if(mail($data['email'], 'Востановления пароля в TimeBig', $content, $header))
                            $this->common->redirect_to('welcome/forgot', 'На указанную почту было отправленно письмо с новым паролем!', 'text', 'success');
                        else
                            $data['error'] = 'Ошибка! Не получилось отправить письмо на указанную почту.%s%'.$content;
                    }
                    else
                        $data['error'] = 'Ошибка! Не получилось обновить данные. Попробуйте еще раз.';
                }
                else
                    $data['error'] = 'Пользователя с таким email не найдено!';
            }
            else
                $data['error'] = 'Введите ваш email в форму!';
        }


        $this->display_lib->display($data, 'login/forgot');
    }


    /**
     * Функция регистрации нового юзера, он войти сразу не сможет - его нужно активировать вначале, а то левые придут какие нибудь
     * The function of registering a new user, it will not be able to enter directly - it must be activated first, and then the left will come any day
     */
    public function registration()
    {

    }

}

