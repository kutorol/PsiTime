<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Обязательно класс называть с большой буквы, а файл с маленькой буквы!!!
 * Be sure to call the class with a capital letter, as a file with a small letter !!!
 * Class Welcome
 */
class Welcome extends CI_Controller {

    /**
     * Сюда перекидывает при срабатывание Hooka, который добавит базу данных и после в этой функции активируются миграции
     * It throws in the operation Hooka, add a database and then this function is activated migration
     */
    public function install()
    {
        $filePath = APPPATH . 'migrations/install.log';
        if(file_exists($filePath))
        {
            //устанавливаем значение языка человека
            $this->common->setDefaultLang(YOUR_LANG);

            $this->load->library('migration');
            if (!$this->migration->current())
            {
                // Если произошла ошибка - выводим сообщение
                show_error($this->migration->error_string());
                exit;
            }
        }
        $this->common->redirect_to('welcome', "TO DO NEW SUCCESS", 'text', 'success');
    }
    /**
     * страница авторизации
     * login page
     */
	public function index()
	{
        $config = [
            'pathToViewDir'   =>  'login',
            'langArray_1'   =>  'welcome_controller',
            'langArray_2'   =>  0,
            'authUser'   =>  false,
            'noRedirect'   =>  false
        ];
        $data = $this->common->allInit($config);
        if(isset($data['return_notification']))
            return true;


        //если нажата кнопка входа в форме
        if(isset($_POST['enter_to_time']))
        {
            //правила валидации данных из полей
            $this->form_validation->set_rules('login', $data['welcome_controller'][1], 'trim|alpha_numeric|required|min_length[5]|max_length[20]|xss_clean');
            $this->form_validation->set_rules('pass', $data['welcome_controller'][2], 'trim|alpha_dash|required|min_length[5]|max_length[20]|xss_clean');

            //если валидация не прошла проверку - показываем вьюху, а там ошибки покажут
            if($this->form_validation->run() == FALSE)
            {
                $this->display_lib->display($data, $config['pathToViewDir']);
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
                    $data['error'] = $data['welcome_controller'][3];
                elseif($q === true)
                {
                    $this->session->set_userdata(['session_user'=> 'avtoriz|'.md5('02u4hash3894').'|'.$data['login']]);
                    setcookie ("login",$data['login'],time()+9999999999,"/");
                    setcookie ("chech_user",1,time()+9999999999,"/");
                    $this->common->redirect_to('task', $data['welcome_controller'][4].$data['login'], 'text', 'success');
                }
                else
                    $data['error'] = $data['welcome_controller'][5];
            }
            else
                $data['error'] = $data['welcome_controller'][6];
        }

        //показываем вид юзеру
        $this->display_lib->display($data, $config['pathToViewDir']);
	}


    /**
     * Функция выхода из ЛК
     * The output function of the LC
     * @param string $status - это класс (success, danger, warning ect.) чтобы цветом выделять надпись - bootstrap
     * (This class (success, danger, warning ect.) to highlight the color of the inscription - bootstrap)
     */
    public function logout($status = '')
    {
        $this->load->library('language/lang_controller');
        $data['segment'] = $this->common->getInfoSegment();
        $data = $this->lang_controller->getLang($data['segment']);

        $title = $this->common->clear($this->input->cookie('error', true));
        $status = $this->common->clear($status);

        $this->session->unset_userdata('session_user');

        if($title != '' && $status != '')
            $this->common->dropCookie(true, '', $title, 'text', $status);
        else
            $this->common->dropCookie(true, '', $data['welcome_controller'][7], 'text', 'success');
    }

    /**
     * Функция восстановления пароля
     * The function of password recovery
     */
    public function forgot()
    {
        $config = [
            'pathToViewDir'   =>  'login/forgot',
            'langArray_1'   =>  'welcome_controller',
            'langArray_2'   =>  8,
            'authUser'   =>  false,
            'noRedirect'   =>  false
        ];
        $data = $this->common->allInit($config);
        if(isset($data['return_notification']))
            return true;


        if(isset($_POST['forgot_btn']))
        {
            //правила валидации данных из полей
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[6]|valid_email|xss_clean');

            //если валидация не прошла проверку - показываем вьюху, а там ошибки покажут
            if($this->form_validation->run() == FALSE)
            {
                $this->display_lib->display($data, $config['pathToViewDir']);
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
                        $content = $data['welcome_controller'][9][0].$q['login'].$data['welcome_controller'][9][1].$new_pass.$data['welcome_controller'][9][2];
                        $header = "From: \"Admin\" <".$data['emailAdminSite'].">\n";
                        $header .= 'Content-type: text/html; charset="utf-8"';

                        if(mail($data['email'], $data['welcome_controller'][8], $content, $header))
                            $this->common->redirect_to('welcome/forgot', $data['welcome_controller'][11], 'text', 'success');
                        else
                            $data['error'] = $data['welcome_controller'][12].$content;
                    }
                    else
                        $data['error'] = $data['welcome_controller'][13];
                }
                else
                    $data['error'] = $data['welcome_controller'][14];
            }
            else
                $data['error'] = $data['welcome_controller'][15];
        }


        $this->display_lib->display($data, $config['pathToViewDir']);
    }


    /**
     * Функция регистрации нового юзера, он войти сразу не сможет - его нужно активировать вначале, а то левые придут какие нибудь
     * The function of registering a new user, it will not be able to enter directly - it must be activated first, and then the left will come any day
     */
    public function registration()
    {
        $config = [
            'pathToViewDir'   =>  'login/register',
            'langArray_1'   =>  'welcome_controller',
            'langArray_2'   =>  16,
            'authUser'   =>  false,
            'noRedirect'   =>  false
        ];
        $data = $this->common->allInit($config);
        if(isset($data['return_notification']))
            return true;


        //print_r($data);
		if(isset($_POST['registration_btn']))
        {
            //правила валидации данных из полей
            $this->form_validation->set_rules('name', $data['welcome_controller'][17], 'trim|required|min_length[2]|max_length[20]|xss_clean');
			$this->form_validation->set_rules('login', $data['welcome_controller'][1], 'trim|alpha_numeric|required|min_length[5]|max_length[20]|xss_clean|is_unique[users.login]');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[6]|valid_email|xss_clean|is_unique[users.email]');
			
			$validateRulePass = 'trim|alpha_dash|required|min_length[5]|max_length[20]|xss_clean|';
            $this->form_validation->set_rules('pass', $data['welcome_controller'][2], $validateRulePass.'matches[pass_too]');
			$this->form_validation->set_rules('pass_too', $data['welcome_controller'][18], $validateRulePass.'matches[pass]');

            //если валидация не прошла проверку - показываем вьюху, а там ошибки покажут
            if($this->form_validation->run() === FALSE)
            {
                $this->display_lib->display($data, $config['pathToViewDir']);
                return true;
            }

            //получаем данные из формы и чистим сразу от шлака
            $data['name'] = $this->common->clear($this->input->post('name', true));
			$data['login'] = $this->common->clear($this->input->post('login', true));
			$data['email'] = $this->common->clear($this->input->post('email', true));
			$data['pass'] = $this->common->clear($this->input->post('pass', true));

			//загружаем библиотеку помощи для строк
			$this->load->helper('string');
			$new = [];
			$new['name'] = $data['name'];
			$new['login'] = $data['login'];
			$new['email'] = $data['email'];
			//получаем хэш для пароля
			$new['hash'] = random_string('alnum', rand(6, 17));
			//получаем пароль
			$new['password'] = $pass = $data['pass'];
			$new['password'] = sha1(md5($new['password'].$new['hash']));
				
			//подгружаем модель
            $this->load->model('welcome_model');	
			$check = $this->welcome_model->insertUser($new);
			if($check > 0)
			{
				//компануем email на отправку
				$content = $data['name'].$data['welcome_controller'][19][0].$data['login'].$data['welcome_controller'][19][1].$pass.$data['welcome_controller'][19][2];
                $header = "From: \"Admin\" <".$data['emailAdminSite'].">\n";
				$header .= 'Content-type: text/html; charset="utf-8"';

				if(mail($data['email'], $data['name'].$data['welcome_controller'][20], $content, $header))
					$this->common->redirect_to('welcome', $data['welcome_controller'][21], 'text', 'success');
				else
					$data['error'] = $data['welcome_controller'][12].$content;
			}
			else
				$data['error'] = $data['welcome_controller'][22];
		   
        }

		
		
		$this->display_lib->display($data, $config['pathToViewDir']);
    }


    /**
     * Изменяем пароль тогда, когда человек вошел в ЛК
     */
    public function changePassword()
    {
        $config = [
            'pathToViewDir'   =>  'login/change_pass',
            'langArray_1'   =>  'welcome_controller',
            'langArray_2'   =>  23,
            'authUser'   =>  true,
            'noRedirect'   =>  true
        ];
        $data = $this->common->allInit($config);
        if(isset($data['return_notification']))
            return true;


        if(isset($_POST['change_pass']))
        {
            //правила валидации данных из полей
            $this->form_validation->set_rules('passOld', $data['welcome_controller'][17], 'trim|required|min_length[2]|max_length[20]|xss_clean');
            $this->form_validation->set_rules('passNew', $data['welcome_controller'][1], 'trim|alpha_numeric|required|min_length[5]|max_length[20]|xss_clean|is_unique[users.login]');
            $this->form_validation->set_rules('passNewRepeat', 'Email', 'trim|required|min_length[6]|valid_email|xss_clean|is_unique[users.email]');

            $validateRulePass = 'trim|alpha_dash|required|min_length[5]|max_length[20]|xss_clean|';
            $this->form_validation->set_rules('pass', $data['welcome_controller'][2], $validateRulePass.'matches[pass_too]');
            $this->form_validation->set_rules('pass_too', $data['welcome_controller'][18], $validateRulePass.'matches[pass]');

            //если валидация не прошла проверку - показываем вьюху, а там ошибки покажут
            if($this->form_validation->run() === FALSE)
            {
                $this->display_lib->display($data, $config['pathToViewDir']);
                return true;
            }

            //получаем данные из формы и чистим сразу от шлака
            $data['name'] = $this->common->clear($this->input->post('name', true));
            $data['login'] = $this->common->clear($this->input->post('login', true));
            $data['email'] = $this->common->clear($this->input->post('email', true));
            $data['pass'] = $this->common->clear($this->input->post('pass', true));
        }



        $this->display_lib->display($data, $config['pathToViewDir']);
    }
	
}