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
        $data['attachUploadSripts'] = true;
        if($data['statusUser'] == 1)
        {
            $this->load->model('common_model');
            $data['myProjects'] = $this->_getProject($data['idUser']);
        }
        else
            $data['myProjects'] = [];

        $data['complexity'] = $this->common_model->getResult('complexity');
        //проверяем, не оставил ли юзер файлы в папке, при создании задачи
        //это может быть тогда, когда он не добавил задачу, а просто все загрузил!
        $data['filesAttach'] = $this->_getAllAttach(null, $data['login'], $data['task_controller'][9]);
        if(isset($data['filesAttach']['status']) || @empty($data['filesAttach']))
            unset($data['filesAttach']);

        $this->display_lib->display($data, $config['pathToViewDir']);
	}

    /**
     * Получаем проекты для данного юзера
     * @param $idIser
     * @param string $select
     * @param string $return
     * @return mixed
     */
    private function _getProject($idIser, $select = 'id_project, title, team_ids', $return = 'result_array')
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

        $this->load->model('common_model');
        //получаем все проекты для данного юзера
        $data['myProjects'] = $this->_getProject($data['idUser']);

        //получаем всех прикрепленных юзеров к проекту и вставляем в ячейку, чтобы потом их отобразить во вьюхе
        foreach($data['myProjects'] as $val)
        {
            $allUsers = explode(',', $val['team_ids']);
            //если в проекте только сам проект менеджер
            if(count($allUsers) == 1)
            {
                $data['userForProject'][$val['id_project']] = "";
                continue;
            }

            //если ид юзера совпадает с ид в комаде, то удаляем его, чтобы не отображалось это во вьюхе
            foreach($allUsers as $key=>$v)
                if($v == $data['idUser'])
                    unset($allUsers[$key]);


            $data['userForProject'][$val['id_project']] = $this->common_model->getResult('users', 'id_user', $allUsers, 'result_array', 'login', null, 'desc', true);
            if(empty($data['userForProject'][$val['id_project']]))
                $data['userForProject'][$val['id_project']] = "";
            else
            {
                $temp = '';
                foreach( $data['userForProject'][$val['id_project']] as $k=>$login)
                {
                    if(isset( $data['userForProject'][$val['id_project']][$k+1]))
                        $temp .= $login['login'].',';
                    else
                        $temp .= $login['login'];
                }

                $data['userForProject'][$val['id_project']] = $temp;
                unset($temp);
            }
        }

        if(isset($data['return_notification']))
            $this->display_lib->display($data, $config['pathToViewDir']);

        if(isset($_POST['addProject_btn']))
        {
            //если чувак нажал чекбокс, то при ошибке мы его снова нажмем и сделаем инпут закрытым
            $data['iAdminCheck'] = (isset($_POST['iAdmin'])) ? true : false;
            $this->form_validation->set_rules('nameProject', $data['task_views'][30], 'trim|required|min_length[3]|max_length[255]|xss_clean|is_unique[projects.title]');
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
                $q = explode("|", $this->common_model->insertData('projects', $new, true, true));
                //в $q[1] содержится количество выполненых операций
                if($q[1] > 0)
                {
                    //в проекте нет добавленных юзеров еще, для отображения тегов  нужно это. в $q[0] содержиться последний вставленный ид в бд
                    $data['userForProject'][$q[0]] = "";
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
            unset($response['data']);
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
            unset($response['data']);
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
            unset($response['data']);
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
     * (AJAX)
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
            unset($response['data']);
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
     * (AJAX)
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
            unset($response['data']);
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

    /**
     * Узнаем, является ли расширение файла одним из этих, если нет, то возвращаем 'zip_pack', чтобы потом заархивировать документ
     * To know whether the file extension by one of these, if not, return 'zip_pack', then to the document archive
     * @param $type - тип определяемого файла
     * @param bool $getExt - если true, то возвращаем массив всех доступных расширений
     * @return array|int|string
     */
    private function _findExt($type, $getExt = false)
    {
        $ext = [
            'word'      =>  ['doc', 'docx', 'docm', 'dotx', "dotm"],
            'exel'      =>  ['xlsx', 'xls', 'xlsm', 'xltx', "xltm", "xlsb", "xlam", "xlt", "xla", "csv", 'xml'],
            'pPoint'    =>  ['pptx', 'pptm', 'ppsx', 'ppsm', "potx", "potm", "ppam", "ppt", "pps", "pot", "ppa"],
            'zip'       =>  ['7z', 'arj', 'bin', 'cab', 'cbr', 'deb', 'gz', 'gzip', 'one', 'pak', 'rar', 'sit', 'sitx', 'tar', 'tar-gz', 'tgz', 'xar', 'zip', 'zipx'],
            'img'       =>  ['gif','jpg','png','jpeg','bmp'],
            'pdf'       =>  ['pdf'],
            'text'      =>  ['txt'],
            'video'     =>  ['avi', 'mov', 'mp4', 'mpeg', 'mpg', 'wm', 'wmv'],
            'audio'     =>  ['m4a', 'm4b', 'm4r', 'mid', 'midi', 'mp3', 'ogg', 'ra', 'wav', 'wma']
        ];

        if($getExt === true)
            return $ext;

        $extention = 'zip_pack';
        foreach($ext as $k=>$v)
        {
            $key = array_search(mb_strtolower($type), $v);
            if($key !== false) //если расширение нашлось, возвращаем его ключ
                return $k;
        }

        return $extention;
    }

    /**
     * Архивируем загружаемый файл
     * Archiving the downloaded file
     * @param $path - путь к папке с файлом (path to the file) - Ex: './img/'
     * @param $name - название файла (file name) - Ex: 'noimg--fds'
     * @param $pathWithName - путь к самому файлу (the path to the file itself) - Ex: './img/noimg--fds.png'
     * @param $endExt - расширение загружаемого файла (extension of the uploaded file) - Ex: 'png'
     * @param $data - все параметры
     * @return array
     */
    private function _attachToZip($path, $name, $pathWithName, $endExt, &$data)
    {
        $response = ['status'=>'success'];
        //создание zip архива
        $zip = new ZipArchive();
        //имя файла архива с путем
        $fileName = $path.$name.".zip";
        //получаем имя без хеша
        $name = explode("--", $name);

        //если удалось создать архив, записываем в него файл
        if ($zip->open($fileName, ZIPARCHIVE::CREATE) === true)
            $zip->addFile($pathWithName, $name[0].'.'.$endExt); //2nd param - новое имя файла в архиве
        else
            $response = ['status'=>'error', 'title'=> $data['task_controller'][5]];

        //закрываем архив
        $zip->close();

        return $response;
    }

    /**
     * Скачиваем audio, video и другие документы
     * Download audio, video and other documents
     * @param $src - название документа с его расширением (document title with its expansion)
     * @return bool
     */
    public function download($src)
    {
        $config = [
            'pathToViewDir'     =>  'common',
            'langArray_1'       =>  'task_controller',
            'langArray_2'       =>  0,
            'authUser'          =>  true,
            'noRedirect'        =>  false, //false - редиректим, true - возвращаем ошибку
        ];
        $data = $this->common->allInit($config);

        $src = $this->common->clear($src);
        $filePath = './img/temp/'.$data['login'].'/'.$src;
        if(file_exists($filePath))
        {
            //заголовок, что сейчас будем скачивать данный файл
            header('Content-Disposition: attachment; filename=' . $src);
            //чтение файла, чтобы отдать его на скачивание
            readfile($filePath);
            return true;
        }
        else
            echo $data['task_controller'][4];
    }

    /**
     * (AJAX)
     * Удаляем прикрепленный к проекту файл
     * Remove attached to the project file
     */
    public function delAttach()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["src",'str']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];
            unset($response['data']);

            $src = $this->common->clear($this->input->post('src'));

            $src = explode('/', $src);
            $src = $src[count($src)-1];
            $path = './img/temp/'.$data['login'].'/'.$src;

            $response = ['status' => 'success'];
            if(file_exists($path))
            {
                unlink($path);

                //если папка пуста, то удаляем ее
                $tempAttachFileDir = './img/temp/'.$data['login'].'/';
                if(file_exists($tempAttachFileDir))
                {
                    $files = [];
                    $descriptor = opendir($tempAttachFileDir);
                    while($v = readdir($descriptor))
                    {
                        if($v == '.' || $v == '..')
                            continue;

                        $files[] = $v;
                    }

                    //удаляем папку
                    if(empty($files))
                        rmdir($tempAttachFileDir);
                }
            }
            else
                $response = ['status' => 'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=> $data['task_controller'][3]];
        }

        echo json_encode($response);
    }

    /**
     * (AJAX)
     * Когда еще создаем задачу, и решили добавить к задаче какой нибудь документ, то сработает эта функция
     * When will create a task, and decided to add to the problem of some sort of document, this feature will work
     */
    public function addTaskAttachFile()
    {
        //если файл к нам пришел, то для прохождения проверки $this->common->isAjax, нужно сделать в массиве post ячейку
        if(isset($_FILES['userfile']))
            $_POST['userfile'] = "yes";

        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["userfile",'str']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];
            unset($response['data']);

            if($_FILES['userfile']['size'] <= 62914560)//60Mb // 10485760) //10Mb
            {
                //получаем имя файла и обрабатываем
                $fileName = explode(".", $_FILES['userfile']['name']);
                $endExt = $fileName[count($fileName)-1];
                unset($fileName[count($fileName)-1]);
                $fileName = $this->common->sms_translit(implode('-', $fileName));
                $hash = substr(md5(time().rand(1,500000000)), 0, 8);
                $fileName = $fileName.'--'.$hash;

                //создаем папку временную, если ее не было
                $tempPath = 'img/temp/'.$data['login'].'/';
                $this->_createFolder($tempPath);

                //если расширение неизвестное, то запаковываем в zip архив
                $ext = $this->_findExt($endExt);


                $config = [];
                $config['upload_path'] = './'.$tempPath;
                $config['file_name'] = $fileName.'.'.$endExt;
                $config['allowed_types'] = '*'; //все типы файлов
                $config['remove_spaces']  = TRUE;
                $this->load->library('upload', $config);

                if($this->upload->do_upload())
                {
                    //если файл загрузился, то либо архивируем его, или ничего не делаем
                    $pathWithName = base_url().$tempPath.$fileName;
                    $response = ['status' => 'success', 'resultTitle' => $data['task_views'][22], 'resultText' => $data['task_views'][22],  'id'=> 'delete_'.$hash, 'fileSrc'=> $pathWithName, 'titleFile'=>$fileName];

                    if($ext == 'zip_pack')
                    {
                        //архивируем
                        //$this->_attachToZip('./img/', 'noimg--fds', './img/noimg--fds.png', 'png');
                        $answer = $this->_attachToZip('./'.$tempPath, $fileName, './'.$tempPath.$config['file_name'], $endExt, $data);

                        //когда архивация пройдет хорошо, тогда удаляем прежний файл, чтобы место не занимал
                        if(file_exists('./'.$tempPath.$fileName.'.'.$endExt))
                            unlink('./'.$tempPath.$fileName.'.'.$endExt);

                        $ext = 'zip';
                        $response['fileSrc'] .= '.zip';

                        if($answer['status'] == 'error')
                            $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' => $answer['title']];
                    }
                    else
                        $response['fileSrc'] .= '.'.$endExt;

                    $response['extension'] = $ext;
                }
                else
                    $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $this->upload->display_errors()];
            }
            else
                $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_controller'][2]];

        }

        echo json_encode($response);
    }


    /**
     * Получаем все файлы с их расширениями содержащиеся во временной папке или уже в полноценной
     * We get all the files with their extensions contained in the temporary folder or already in full
     * @param null $id - если не null, то получаем данные из папки уже созданной задачи (if not null, you get the data from a folder already created task)
     * @param null $login - если не null, то получаем данные из временной папки (if not null, you get the data from the temporary folder)
     * @param $textLang - тут ошибка на определенном языке (then the error in a particular language)
     * @return array
     */
    private function _getAllAttach($id = null, $login = null, $textLang)
    {
        if($login !== null)
            $tempAttachFileDir = 'img/temp/'.$login.'/';
        elseif($id !== null)
            $tempAttachFileDir = 'file/tasks/'.$id.'/';
        else
            return ['status'=> 'error', 'title'=> $textLang];

        $files = [];
        $array = [];
        if(file_exists('./'.$tempAttachFileDir))
        {
            $descriptor = opendir('./'.$tempAttachFileDir);
            while($v = readdir($descriptor))
            {
                if($v == '.' || $v == '..')
                    continue;

                $files[] = $v;
            }
        }

        if(!empty($files))
        {
            foreach($files as $k=>$v)
            {
                $array[$k]['src'] = base_url().$tempAttachFileDir.$v;
                $array[$k]['src_'] = './'.$tempAttachFileDir.$v;
                $array[$k]['title'] = $v;
                $ext = explode('.', $v);
                $ext = $ext[count($ext)-1];
                $ext = $this->_findExt($ext);
                $array[$k]['ext'] = ($ext != 'zip_pack') ? $ext : 'zip';
            }
        }

        return $array;
    }

    /**
     * Создаем папку, если она не существовала
     * Create the folder if it does not exist
     * @param $path
     */
    private function _createFolder($path)
    {
        if(!file_exists('./'.$path))
            @mkdir('./'.$path, 0777);
    }

    /**
     * Удаляем папку
     * delete the folder
     * @param $path
     * @return bool
     */
    private function _delFolder($path)
    {
        if(file_exists($path))
        {
            $files = array_diff(scandir($path), array('.','..'));
            foreach ($files as $file)
            {
                (is_dir("$path/$file")) ? $this->_delFolder("$path/$file") : unlink("$path/$file");
            }

            return rmdir($path);
        }

        return false;
    }

    /**
     * (AJAX)
     * Добавляем задачу в бд
     * Add tasks to the database
     */
    public function addTask()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["titleTask", 'str'], ["descTask", 'str'], ["taskLevel", 'int', 'notZero'], ["startDay", 'int', 'notZero'], ["endDay", 'int', 'notZero'], ["estimatedTimeForTask", 'int'], ["measurementTime", 'int', 'notZero'], ["idProject", 'int']);
        if($response['status'] != 'error')
        {
            $this->load->model('common_model');
            $data = $response['data'];
            unset($response['data']);

            $q = $this->common_model->getResult('users', 'id_user', $data['idUser'], 'row_array', 'time_start_day, time_end_day');

            $titleTask = $this->common->clear($this->input->post('titleTask', true));
            if(preg_match("/^[а-яА-ЯёЁa-zA-Z0-9\-_ ]{3,256}$/iu", $titleTask))
            {
                $updateUser = null;
                //если не установленные рамки рабочего времени, то ставим их
                if(!is_numeric($q['time_start_day']) || !is_numeric($q['time_end_day']))
                {
                    $new = [
                        'time_start_day'    =>  $this->common->clear($this->input->post('startDay', true)),
                        'time_end_day'      =>  $this->common->clear($this->input->post('endDay', true))
                    ];

                    if(is_numeric($new['time_start_day']) && is_numeric($new['time_end_day']))
                    {
                        //я живу в двадцатичетырех часовом формате, поэтому вычисления делаю в нем же
                        //I live in a twenty-four hour format, so the calculations do it well
                        if($new['time_start_day'] < 0 || $new['time_start_day'] > 24 || $new['time_end_day'] < 0 || $new['time_end_day'] > 24)
                            $updateUser = 0;
                        else
                            $updateUser = $this->common_model->updateData($new, 'id_user', $data['idUser'], 'users', true);
                    }
                    else
                        $updateUser = 0;
                }

                //проверяем, есть ли такой проект у человека
                $idProject = intval($this->common->clear($this->input->post('idProject', true)));
                $checkProject = $this->common_model->getResult('projects', ['id_project', 'responsible'], [$idProject, $data['idUser']], 'row_array');
                if(empty($checkProject))
                {
                    $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_controller'][10]];
                    echo json_encode($response);
                    return true;
                }

                $new = [];
                $new['title']           =   $titleTask;
                $new['status']          =   '0'; // 0 - пока еще только добавленна
                $new['text']            =   $this->common->clear($this->input->post('descTask', true));
                $new['time_add']        =   time();
                $new['day_start']       =   date('d');
                $new['month_start']     =   date('m');
                $new['year_start']      =   date('Y');
                $new['complexity_id']   =   intval($this->common->clear($this->input->post('taskLevel', true)));
                $new['user_id']         =   $data['idUser'];
                $new['project_id']                  =   $idProject;
                $new['time_for_complete']           =   intval($this->common->clear($this->input->post('estimatedTimeForTask', true)));
                $new['time_for_complete_value']     =   intval($this->common->clear($this->input->post('measurementTime', true)));


                $myResponse = explode('|', $this->common_model->insertData('task', $new, true, true));
                if($myResponse[1] > 0)
                {
                    $response = ['status'=> 'success', 'resultTitle'=> $data['task_views'][22], 'resultText'=> $data['task_views'][60]];
                    $answer = $this->_getAllAttach(null, $data['login'], $data['task_controller'][9]);
                    if(!isset($answer['status']))
                    {
                        if(!empty($answer))
                        {
                            $newFolderForTask = 'file/tasks/'.$myResponse[0].'/';
                            $this->_createFolder($newFolderForTask);

                            $failCopy = ['text'=>'', 'fail'=>0];
                            foreach($answer as $v)
                            {
                                if(copy($v['src_'], './'.$newFolderForTask.$v['title']) === false)
                                {
                                    if($failCopy['fail'] == 0)
                                        $failCopy['text'] = $data['task_controller'][6];

                                    $errorAttach[] = $v['title'];
                                    $failCopy['fail']++;
                                }
                            }

                            if($failCopy['fail'] > 0)
                                $response['error']['copyText'] =  $failCopy['text'].implode(', ', $errorAttach);

                            $this->_delFolder('./img/temp/'.$data['login']);
                        }
                    }
                    else
                        $response['error']['attach'] = $answer['title'];
                }
                else
                    $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_controller'][7]];

                if($updateUser !== null)
                {
                    if($updateUser <= 0)
                        $response['error']['updateWorkDay'] = $data['task_controller'][8];
                    else
                        $response['hideTimeBlock'] = true;
                }
            }
            else
                $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_views'][19]." ".$data['task_views'][20]];
        }

        echo json_encode($response);
    }

}
