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

    public function getName()
    {
        if($this->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest')
        {
            echo 'fasd';


            if(isset($_POST['nameUser']))
            {

                $this->load->model('task_model');
                $nameUser = $this->common->clear($_POST['nameUser']);
                $maxRows  = $this->common->clear($_POST['maxRows']);
                $q = $this->task_model->getUser($nameUser, $maxRows);
                if(!empty($q))
                {

                }
                else
                    echo json_encode(['response'=>[0=>['name'=>'notMatch']]]);
            }

            $aee = array(
                'geonames'=>array(
                    0=>array('name'=>'das'),
                    1=>array('name'=>'dagkds'),
                    2=>array('name'=>'dghd'))
            );
            echo json_encode($aee);
        }
    }

}
