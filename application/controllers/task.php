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
        //получаем все проекты для данного юзера
        $data['myProjects'] = $this->_getProject($data['idUser']);

        if(isset($_POST['addProject_btn']))
        {
            //если чувак нажал чекбокс, то при ошибке мы его снова нажмем и сделаем инпут закрытым
            $data['iAdminCheck'] = (isset($_POST['iAdmin'])) ? true : false;
            $this->form_validation->set_rules('nameProject', $data['welcome_controller'][1], 'trim|required|min_length[3]|max_length[255]|xss_clean|is_unique[projects.title]');
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

            if(!preg_match("/^[а-яА-ЯёЁa-zA-Z0-9\-_ ]{3,256}$/iu", $new['title']))
            {
                $data['error'] = $data['task_views'][19]." ".$data['task_views'][20];
                $this->display_lib->display($data, $config['pathToViewDir']);
                return true;
            }


            //если нажат чекбокс
            if(isset($_POST['iAdmin']))
            {
                if($this->input->post('iAdmin') == 'yes')
                {
                    //обновляем у данного юзера статус, чтобы показывать на главной странице его проекты и таски
                    if($data['statusUser'] == 0)
                        $data['statusUser'] = $this->common_model->updateData(['status'=>'1', 'count_projects' => ($data['count_projectsUser'] + 1)], 'id_user', $data['idUser'], 'users', true);
                    else
                        $data['statusUser'] = $this->common_model->updateData(['count_projects' => ($data['count_projectsUser'] + 1)], 'id_user', $data['idUser'], 'users', true);

                    $new['responsible'] = $data['idUser'];
                    $new['team_ids'] = $new['responsible'];
                    $fail = true;
                }
            }
            //если чекбокс не нажат и выбран чел из автокомплита
            else
            {
                if(isset($_POST['mainUser']))
                {
                    $login = $this->common->clear($this->input->post('mainUser', true));
                    $userOtherData = $this->common_model->getResult('users', 'login', $login, 'row_array', 'id_user, status, count_projects');
                    if(empty($userOtherData))
                        $this->common->redirect_to('task/addProject', $data['js'][1]);


                    //обновляем у данного юзера статус, чтобы показывать на главной странице его проекты и таски
                    if($userOtherData['status'] == 0)
                        $this->common_model->updateData(['status'=>'1', 'count_projects' => ($userOtherData['count_projects'] + 1)], 'id_user', $userOtherData['id_user'], 'users');
                    else
                        $this->common_model->updateData(['count_projects' => ($userOtherData['count_projects'] + 1)], 'id_user', $userOtherData['id_user'], 'users');

                    $new['responsible'] = $userOtherData['id_user'];
                    $new['team_ids'] = $new['responsible'];
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
                    $data['myProjects'] = $this->_getProject($data['idUser']);
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
     * Удаляем проект вместе со всеми задачами
     * Remove the project together with all tasks
     */
    public function deleteProject()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["id", 'int']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];

            $this->load->model('common_model');
            $idProject = $this->common->clear(intval($_POST['id']));
            $infoProject = $this->common_model->getResult('projects', ['id_project', 'responsible'], [$idProject, $data['idUser']], 'row_array', 'responsible, team_ids');
            if(!empty($infoProject))
            {
                $q = $this->common_model->deleteData('projects', ['id_project', 'responsible'], [$idProject, $data['idUser']], true);
                if($q > 0)
                {
                    //обновляем количество проектов у юзера
                    $new = [];
                    $new['count_projects'] = $data['count_projectsUser'] - 1;
                    if($new['count_projects']  == 0)
                        $new['status'] = '0';

                    $this->common_model->updateData($new, 'id_user', $data['idUser'], 'users');

                    //также уменьшаем количество проектов у юзеров, которые находятся в этой команде (прикреплены к проекту)
                    $q = $this->common_model->getResult('users', 'id_user', explode(',', $infoProject['team_ids']), 'result_array', 'count_projects, id_user', null, 'desc', true);
                    foreach($q as $v)
                    {
                        if($v['id_user'] == $data['idUser'])
                            continue;

                        $new = [];
                        $new['count_projects'] = intval($v['count_projects']) - 1;
                        if($new['count_projects']  == 0)
                            $new['status'] = '0';

                        $this->common_model->updateData($new, 'id_user', $v['id_user'], 'users');
                    }

                    $this->common_model->deleteData('task', 'project_id', $idProject);
                    $response = ['status'=>'success', 'resultTitle' => $data['task_views'][13]];
                }
                else
                    $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' => $data['task_views'][14]];
            }
            else
                $response = ['status' => 'error', 'resultTitle' => $data['task_views'][19], 'resultText' => $data['task_views'][16]];
        }

        echo json_encode($response);
    }


    /**
     * (AJAX)
     * Обновляем имя проекта и делаем разного рода проверки
     * We update the name of the project and do all sorts of checks
     */
    public function updateProject()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["id", 'int'], ['title', 'str']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];

            $this->load->model('common_model');
            $idProject = $this->common->clear(intval($_POST['id']));
            $titleProject  = $this->common->clear($_POST['title']);
            if(preg_match("/^[а-яА-ЯёЁa-zA-Z0-9\-_ ]{3,256}$/iu", $titleProject))
            {
                $ckeckUniq = $this->common_model->getResult('projects', 'title', $titleProject, 'result_array', 'responsible');
                if(!empty($ckeckUniq))
                    $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' => $data['task_views'][23]];
                else
                {
                    $infoProject = $this->common_model->getResult('projects', ['id_project', 'responsible'], [$idProject, $data['idUser']], 'row_array', 'responsible');
                    if(!empty($infoProject))
                    {
                        $q = $this->common_model->updateData(['title'=>$titleProject], ['id_project', 'responsible'], [$idProject, $data['idUser']], 'projects', true);
                        if($q > 0)
                            $response = ['status'=>'success', 'resultTitle' => $data['task_views'][22]];
                        else
                            $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' => $data['welcome_controller'][13]];
                    }
                    else
                        $response = ['status' => 'error', 'resultTitle' => $data['task_views'][19], 'resultText' => $data['task_views'][16]];
                }
            }
            else
                $response = ['status' => 'error', 'resultTitle' => $data['task_views'][19], 'resultText' => $data['task_views'][20]];
        }

        echo json_encode($response);
    }



    /**
     * (AJAX)
     * Получаем доступные имена по логину или имени
     * Get accessible by login name or name
     */
    public function getUsersProject()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["query", 'str']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];

            $this->load->model('task_model');
            $nameUser = $this->common->clear($_POST['query']);
            $maxRows = 10;
            $q = $this->task_model->getUser($nameUser, $maxRows);
            if (!empty($q))
            {
                $response['status'] = 'success';
                foreach ($q as $v)
                    $response['users'][] = array('name' => '(#' . $v['id_user'] . ') ' . $data['input_form_lang'][1][$data['segment']] . ': ' . $v['name'] . " " . $data['input_form_lang'][0][$data['segment']] . ": ",
                        'login' => $v['login']);
            }
            else
                $response = ['status' => 'error', 'resultTitle' => $data['js'][1]];
        }

        echo json_encode($response);
    }

    /**
     * Прикрепляем разных юзеров к проекту
     * Attach the different users to the project
     */
    public function attachUserProject()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["names",'str'], ['id', 'int']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];

            $this->load->model('common_model');

            $idProject = $this->common->clear(intval($this->input->post('id', true)));
            $namesUsers = explode(',', $this->common->clear($this->input->post('names', true)));
            $q = $this->common_model->getResult('projects', ['id_project', 'responsible'], [$idProject, $data['idUser']], 'row_array', 'team_ids');
            if(empty($q))
                $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_views'][16]];
            else
            {
                //все прикрепленные люди
                $allIdUsers = explode(',', $q['team_ids']);
                $new['team_ids'] = $q['team_ids'];
                //получаем все id людей, полученых в форме
                $idUsers = $this->common_model->getResult('users', 'login', $namesUsers, 'result_array', 'id_user, login, count_projects', null, 'desc', true);
                if(!empty($idUsers))
                {
                    $newAddUser = [];
                    //проверяем на совпадение
                    foreach($idUsers as $v)
                    {
                        //проверяем, не добавлен ли человек уже
                        if(array_search($v['id_user'], $allIdUsers) === false)
                        {
                            $new['team_ids'] .= ",".$v['id_user'];
                            $newAddUser[] = ['id_user'=>$v['id_user'], 'count_projects'=>$v['count_projects']];
                        }
                    }

                    //если хотя бы один был новый, то обновляем проект
                    if(!empty($newAddUser))
                    {
                        $check = $this->common_model->updateData($new, ['id_project', 'responsible'], [$idProject, $data['idUser']], 'projects', true);
                        if($check > 0)
                        {
                            foreach($newAddUser as $v)
                            {
                                $new = [];
                                $new['status'] = '1'; //status обязательно должен быть в ковычках
                                $new['count_projects'] = $v['count_projects'] + 1;

                                $this->common_model->updateData($new, 'id_user', $v['id_user']);
                            }
                            $response = ['status' => 'success', 'resultTitle'=> $data['task_views'][22], 'resultText'=>  $data['task_views'][27]];
                        }
                        else
                            $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['welcome_controller'][13]];
                    }
                    else
                        $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_views'][26]];
                }
                else
                    $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['js'][1]];

            }
        }


        echo json_encode($response);
    }


    /**
     * Удаляем юзеров из проекта
     * Remove users from the project
     */
    public function delUserProject()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["names",'str'], ['id', 'int']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];

            $this->load->model('common_model');

            $idProject = $this->common->clear(intval($this->input->post('id', true)));
            $namesUsers = $this->common->clear($this->input->post('names', true));
            $q = $this->common_model->getResult('projects', ['id_project', 'responsible'], [$idProject, $data['idUser']], 'row_array', 'team_ids');
            if(empty($q))
                $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_views'][16]];
            else
            {
                $new['team_ids'] = $data['idUser'];
                //все прикрепленные люди
                $allIdUsers = explode(',', $q['team_ids']);//были раньше
                if($namesUsers != '')
                {
                    //получаем все id людей, полученых в форме
                    $idUsers = $this->common_model->getResult('users', 'login', explode(",", $namesUsers), 'result_array', 'id_user, login, count_projects', null, 'desc', true);
                    if(!empty($idUsers))
                    {
                        //проверяем на совпадение
                        foreach($idUsers as $v)
                        {
                            //проверяем, не добавлен ли человек уже
                            if(array_search($v['id_user'], $allIdUsers) !== false)
                            {
                                $new['team_ids'] .= ",".$v['id_user'];
                                foreach($allIdUsers as $key=>$val)
                                {
                                    if($v['id_user'] == $val)
                                        unset($allIdUsers[$key]);
                                }
                            }
                        }

                        //если хотя бы один был новый, то обновляем проект
                        if(!empty($allIdUsers))
                            $response =  $this->_additionalDelUserProject($new, $data, $idProject, $allIdUsers);
                        else
                            $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_views'][28]];
                    }
                    else
                        $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['js'][1]];
                }
                else
                    $response = $this->_additionalDelUserProject($new, $data, $idProject, $allIdUsers);
            }
        }


        echo json_encode($response);
    }


    /**
     * Удаляем юзеров из проекта, дополнительная функция
     * Remove users from the project, an additional function
     * @param $new
     * @param $data
     * @param $idProject
     * @param $allIdUsers
     * @return array
     */
    private function _additionalDelUserProject($new, &$data, $idProject, $allIdUsers)
    {
        $check = $this->common_model->updateData($new, ['id_project', 'responsible'], [$idProject, $data['idUser']], 'projects', true);
        if($check > 0)
        {
            $newDelUser = [];
            foreach($allIdUsers as $v)
            {
                if($v == $data['idUser'])
                    continue;

                $oneInfoUser = $this->common_model->getResult('users', 'id_user', $v, 'row_array', 'count_projects, login');
                $new = [];
                $new['count_projects'] = $oneInfoUser['count_projects'] - 1;
                if($new['count_projects'] <= 0)
                {
                    $new['count_projects'] = 0;
                    $new['status'] = '0'; //status обязательно должен быть в ковычках
                }

                $newDelUser['logins'][] = $oneInfoUser['login'];
                $this->common_model->updateData($new, 'id_user', $v);
            }


                return ['status' => 'success', 'resultTitle'=> $data['task_views'][22], 'resultText'=>  $data['task_views'][27]." ".$data['task_views'][29].implode(", ", $newDelUser['logins'])];
        }
        else
            return ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['welcome_controller'][13]];
    }
}
