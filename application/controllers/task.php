<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Task
 * Обязательно класс называть с большой буквы, а файл с маленькой буквы!!!
 * Be sure to call the class with a capital letter, as a file with a small letter !!!
 * @property Task_model $task_model
 * @property Common_model $common_model - общая модель для работы с бд (general model for working with database)
 * @property Common $common - общая библиотека с авторизацией юзера и проверками (shared library user and authorization checks)
 * @property Display_lib $display_lib - достает нужные вьюхи (It lacks the necessary view file)
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
            'noRedirect'        =>  false, //false - редиректим, true - возвращаем ошибку
            'pattern'           =>  ['pattern'=>['title', 'login', '%login%']]
        ];
        $data = $this->common->allInit($config);
        if(isset($data['return_notification']))
            return true;


        $this->display_lib->display($data, $config['pathToViewDir']);
	}


    /**
     * Получаем проекты для данного юзера
     * @param $idIser
     * @param string $select
     * @param string $return
     * @return mixed
     */
    private function _getProject($idIser, $select = 'id_project, title', $return = 'result_array')
    {
        return $this->common_model->getResult('projects', 'responsible', $idIser, $return, $select, 'id_project');
    }


    /**
     * Функция добавляет проект на сайт, в который потом добавляется задачи
     * The function adds the project to the site, which is then added to the task
     *
     * @var $fail - распределяет ошибка эта или нет (This distributes the error or not)
     */
    public function addProject()
    {
        $config = [
            'pathToViewDir'     =>  'common/add_project',
            'langArray_1'       =>  'task_controller',
            'langArray_2'       =>  1,
            'authUser'          =>  true, //true - авторизирован, false - неавторизирован
            'noRedirect'        =>  false, //false - редиректим, true - возвращаем ошибку
            'pattern'           =>  ['pattern'=>['title', 'login', '%login%']]
        ];
        $data = $this->common->allInit($config);
        if(isset($data['return_notification']))
            return true;


        $this->load->model('common_model');

        //узнаем ид данного юзера
        $userData = $this->common_model->getResult('users', 'login', $data['login'], 'row_array', 'id_user');
        if(empty($userData))
            $this->common->dropCookie(true, '', $data['common_library'][4]);

        //получаем все проекты для данного юзера
        $data['myProjects'] = $this->_getProject($userData['id_user']);

        if(isset($_POST['addProject_btn']))
        {
            $this->form_validation->set_rules('nameProject', $data['welcome_controller'][1], 'trim|required|min_length[3]|max_length[20]|xss_clean|is_unique[projects.title]');
            $this->form_validation->set_rules('mainUser', $data['welcome_controller'][2], 'trim|alpha_dash|min_length[2]|max_length[20]|xss_clean');

            //если валидация не прошла проверку - показываем вьюху, а там ошибки покажут
            if($this->form_validation->run() == FALSE)
            {
                $this->display_lib->display($data, $config['pathToViewDir']);
                return true;
            }

            //распределяет ошибка эта или нет (This distributes the error or not)
            $fail = false;
            $new = [];
            //название проекта
            $new['title'] = $this->common->clear($this->input->post('nameProject', true));

            //если нажат чекбокс
            if(isset($_POST['iAdmin']))
            {
                if($this->input->post('iAdmin') == 'yes')
                {
                    $new['responsible'] = $userData['id_user'];
                    $fail = true;
                }
            }
            //если чекбокс не нажат и выбран чел из автокомплита
            else
            {
                if(isset($_POST['mainUser']))
                {
                    $login = $this->common->clear($this->input->post('mainUser'));
                    $userOtherData = $this->common_model->getResult('users', 'login', $login, 'row_array', 'id_user');
                    if(empty($userOtherData))
                        $this->common->redirect_to('task/addProject', $data['js'][1]);

                    $new['responsible'] = $userOtherData['id_user'];
                    $fail = true;
                }
            }

            //если все хорошо прошло
            if($fail === true)
            {
                $q = $this->common_model->insertData('projects', $new, true);
                if($q > 0)
                {
                    $data['error'] = $data['task_views'][4];
                    $data['status_text'] = 'success';
                    //получаем все проекты для данного юзера
                    $data['myProjects'] = $this->_getProject($userData['id_user']);
                }
                else
                    $this->common->redirect_to('task/addProject', $data['task_views'][5]);
            }
            //ошибка
            else
                $data['error'] = $data['task_views'][6];
        }


        $this->display_lib->display($data, $config['pathToViewDir']);
    }

    /**
     * (AJAX)
     * Получаем доступные имена по логину или имени
     * Get accessible by login name or name
     */
    public function getName()
    {
        //если это аякс запрос
        if($this->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest')
        {
            $data = $this->common->initApp('welcome_controller', 0, 'login', true, true);
            if($data['checkAuth']['title_error'] != '')
                echo json_encode(['status'=>'error', 'result'=> $data['checkAuth']['title_error']]);
            else
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
                            $data['users'][] = array('name'     => '(#'.$v['id_user'].') '.$data['input_form_lang'][1][$data['segment']].': '.$v['name']." ".$data['input_form_lang'][0][$data['segment']].": ",
                                'login'    =>  $v['login']);

                        echo json_encode($data);
                    }
                    else
                        echo json_encode(['users'=>[0=>['name'=>'notMatch_EX']]]);
                }
                else
                    echo json_encode(['users'=>[0=>['name'=>'notPostData_EX']]]);
            }
        }
        else
            echo "NU NIXUYA SEBE TI CHEGO SDELAL";
    }


    /**
     * (AJAX)
     * Удаляем проект вместе со всеми задачами
     * Remove the project together with all tasks
     */
    public function deleteProject()
    {
        //если это аякс запрос
        if($this->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest')
        {
            $data = $this->common->initApp('welcome_controller', 0, 'login', true, true);
            if($data['checkAuth']['title_error'] != '')
                echo json_encode(['status'=>'error', 'result'=> $data['checkAuth']['title_error']]);
            else
            {
                $this->load->model('common_model');
                //проверяем данные
                if($this->common->checkData($_POST['id'], true) === true)
                {
                    $idProject = $this->common->clear(intval($_POST['id']));

                    $q = $this->common_model->deleteData('projects', ['id_project', 'responsible'], [$idProject, $data['idUser']], true);
                    if($q > 0)
                    {
                        $this->common_model->deleteData('task', 'project_id', $idProject);
                        echo json_encode(['status'=>'success', 'result'=> $data['task_views'][13]]);
                    }
                    else
                        echo json_encode(['status'=>'error', 'result'=> $data['task_views'][14]]);
                }
                else
                    echo json_encode(['status'=>'error', 'result'=> $data['js'][0]]);
            }
        }
        else
            echo "NU NIXUYA SEBE TI CHEGO SDELAL";
    }


}
