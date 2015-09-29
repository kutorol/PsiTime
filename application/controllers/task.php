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
            echo json_encode(['users'=>[0=>['name'=>'notAjax_EX']]]);
    }



}
