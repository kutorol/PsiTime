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
        $config = [
            'pathToViewDir'     =>  'common',
            'langArray_1'       =>  'task_controller',
            'langArray_2'       =>  0,
            'authUser'          =>  true,
            'noRedirect'        =>  true, //true - редиректим, false - возвращаем ошибку
            'pattern'           =>  ['pattern'=>['title', 'login', '%login%']]
        ];
        $data = $this->common->allInit($config);
        if(isset($data['return_notification']))
            return true;

        if($data['checkAuth']['check'] === false)
            $this->common->dropCookie(true, '', ($data['checkAuth']['title_error'] != '') ? $data['checkAuth']['title_error'] : $data['languages_desc'][0]['errorAuth'][$data['segment']]);


        $this->display_lib->display($data, $config['pathToViewDir']);
	}


    /**
     * Функция добавляет проект на сайт, в который потом добавляется задачи
     * The function adds the project to the site, which is then added to the task
     */
    public function addProject()
    {
        $config = [
            'pathToViewDir'     =>  'common/add_project',
            'langArray_1'       =>  'task_controller',
            'langArray_2'       =>  1,
            'authUser'          =>  true, //true - авторизирован, false - неавторизирован
            'noRedirect'        =>  true, //true - редиректим, false - возвращаем ошибку
            'pattern'           =>  ['pattern'=>['title', 'login', '%login%']]
        ];
        $data = $this->common->allInit($config);
        if(isset($data['return_notification']))
            return true;

        if($data['checkAuth']['check'] === false)
            $this->common->dropCookie(true, '', ($data['checkAuth']['title_error'] != '') ? $data['checkAuth']['title_error'] : $data['languages_desc'][0]['errorAuth'][$data['segment']]);




        //if(isset($_POST['iAdmin'])) берем логин

        if(isset($_POST['addProject_btn']))
        {
            $this->form_validation->set_rules('nameProject', $data['welcome_controller'][1], 'trim|required|min_length[3]|max_length[20]|xss_clean');
            $this->form_validation->set_rules('mainUser', $data['welcome_controller'][2], 'trim|alpha_dash|min_length[2]|max_length[20]|xss_clean');

            //если валидация не прошла проверку - показываем вьюху, а там ошибки покажут
            if($this->form_validation->run() == FALSE)
            {
                $this->display_lib->display($data, $config['pathToViewDir']);
                return true;
            }

            //TODO сделать добавление проекта
            if(isset($_POST['iAdmin']))
            {
                if($this->input->post('iAdmin') == 'yes')
                {
                    //TODO тут узнать ид чувака или сразу по логину добавить проект
                }
                else
                {
                    //TODO translate
                    $data['iAdminError'] = 'bad request';
                }
            }
            else
            {
                //TODO тут если чел не себя главным выбрал
            }
        }


        $this->display_lib->display($data, $config['pathToViewDir']);
    }

    /**
     * Получаем доступные имена по логину или имени
     * Get accessible by login name or name
     */
    public function getName()
    {
        //если это аякс запрос
        if($this->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest')
        {
            if(isset($_POST['nameUser']) && isset($_POST['maxRows']))
            {
                $this->load->model('task_model');
                $nameUser = $this->common->clear($_POST['nameUser']);
                $maxRows  = $this->common->clear(intval($_POST['maxRows']));
                $q = $this->task_model->getUser($nameUser, $maxRows);
                if(!empty($q))
                {
                    $data['users'] = array();
                    foreach($q as $v)
                        $data['users'][] = array('name'     => '(#'.$v['id_user'].') Имя: '.$v['name']." Логин: ",
                                                 'login'    =>  $v['login']);

                    echo json_encode($data);
                }
                else
                    echo json_encode(['users'=>[0=>['name'=>'notMatch_EX']]]);
            }
            else
                echo json_encode(['users'=>[0=>['name'=>'notPostData_EX']]]);
        }
        else
            echo "NU NIXUYA SEBE TI CHEGO SDELAL";
    }



}
