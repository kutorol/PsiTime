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
     * Главная страница личного кабинета. Тут же добавляется задача и тут же их можно просматривать
     * Home private office. Immediately add tasks and then you can view them
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
        //разрешаем загрузку файлов на сайт через ajax (если isset attachUploadSripts, то подгружаем нужные скрипты)
        $data['attachUploadSripts'] = true;
        $this->load->model('common_model');
        $this->_additionalGetTask($data);

        //приоритет задания
        $data['priority'] =  $this->common_model->getResult('priority', '', '', 'result_array', 'id_priority, icon, title_'.$data['segment'], 'id_priority', 'asc');
        //сложность задания
        $data['complexity'] = $this->common_model->getResult('complexity', '', '', 'result_array', 'id_complexity, color, name_complexity_'.$data['segment'], 'id_complexity', 'asc');
        //проверяем, не оставил ли юзер файлы в папке, при создании задачи
        //это может быть тогда, когда он не добавил задачу, а просто все загрузил!
        $data['filesAttach'] = $this->_getAllAttach(null, $data['login'], $data['task_controller'][9]);
        if(isset($data['filesAttach']['status']) || @empty($data['filesAttach']))
            unset($data['filesAttach']);

        $this->display_lib->display($data, $config['pathToViewDir']);
	}


    /**
     * Создаем постраничную навигацию. Результат храниться в $data['pagination']
     * Create a page navigation.  The result is stored in $data['pagination']
     * @param $data
     * @param $idProject - если 0, то навигацию делаем для всех задач. Если > 1, то для конкретного проекта делаем
     */
    private function _getNavigation(&$data, $idProject)
    {
        $data['pagination'] = ['status'=>'error'];
        //текущая страница. Если она равна 5, то в виде она будет обозначена как 6.
        if($data['curent_page'] < 1)
            $data['curent_page'] = 0;


        //если выборка происходило по конкретному проекту, то и количество задач у него другое.
        $allCountTask = (isset($data['countTaskInProject_all'])) ? $data['countTaskInProject_all'] : $data['countProject_all'];

        //если нет задач вообще, то это ошибка
        if($allCountTask <= 0)
            return;

        //узнаем сколько страниц навигации будет
        $countPage = 0;
        if($allCountTask != 1)
        {
            $countPage = (int)($allCountTask/COUNT_OBJECT_PER_PAGE);
            if($countPage == 0)
                $countPage = 1;
            else
            {
                if(($allCountTask % COUNT_OBJECT_PER_PAGE) != 0)
                    $countPage++;
            }

            //количество ссылок по бокам от текущей страницы
            $countLinkLeft = (COUNT_LINK_FOR_PAGINATION % 2 == 0) ? COUNT_LINK_FOR_PAGINATION / 2 : COUNT_LINK_FOR_PAGINATION - ceil(COUNT_LINK_FOR_PAGINATION/2);
            $countLinkRight = COUNT_LINK_FOR_PAGINATION - $countLinkLeft;

            //если текущая страница больше того что есть
            if($data['curent_page'] > $countPage)
                $data['curent_page'] = $countPage - 1;


            $pervPage = $nextPage = $last = $main = '';
            //do navigation
            if($countPage > 1)
            {
                //делаем ссылку "Последняя"
                if((($countPage-1) - $data['curent_page']) > 2)
                    $last = $this->_paginHtmlComment(($countPage-1), "Последняя", $idProject);

                //делаем ссылку "Первая"
                if($data['curent_page'] > 2)
                    $main = $this->_paginHtmlComment(0,"Первая", $idProject);

                //делаем ссылку "Предыдущая"
                if ($data['curent_page'] != 0)
                    $pervPage = $this->_paginHtmlComment(($data['curent_page']-1),"Предыдущая", $idProject);

                //делаем ссылку "Следующая"
                if ($data['curent_page'] != ($countPage - 1))
                    $nextPage = $this->_paginHtmlComment(($data['curent_page']+1),"Следующая", $idProject);

                //получаем все ссылки слева от текущей страницы
                $page2left = [];
                for($i = 0; $i < $countLinkLeft; $i++)
                {
                    $j = $i+1;
                    // Находим две ближайшие станицы с левого края
                    if(($data['curent_page'] - $j) > -1)
                        $page2left[] = $this->_paginHtmlComment(($data['curent_page']-$j), ($data['curent_page']-$i), $idProject);
                }
                //последнюю ячейку массива делаем первой, а то нумерация навигации будет вида 3 2 1 4 5 6 7 и т.д., а если сделать array_reverse, то будет 1 2 3 4 5 6 7 и т.д.
                if(!empty($page2left))
                    $page2left = array_reverse($page2left);

                //получаем все ссылки справа от текущей страницы
                $page2right = [];
                for($i = 1; $i <= $countLinkRight; $i++)
                {
                    if($data['curent_page'] + $i <= ($countPage - 1))
                        $page2right[] = $this->_paginHtmlComment(($data['curent_page']+$i),($data['curent_page']+$i+1), $idProject);
                }
                //текущая активная ссылка, которую нельзя нажать
                $currentPage = $this->_paginHtmlComment(null, $data['curent_page']+1);

                // Вывод навигации
                $data['pagination'] = ['status'=>'success', 'pagination'=> "<div class='clearfix'><div class='pagination no-margin'><ul>".$main.$pervPage.implode('',$page2left).$currentPage.implode('',$page2right).$nextPage.$last."</ul></div></div>"];
            }
        }
    }

    /**
     * Возвращает строку с постраничной навигацией
     * Returns a string with pagination
     * @param int $num - номер страницы. Начиная с 0. (page number. Beginning with 0.)
     * @param string $title - название той самой кнопки навигации (the very name of the navigation buttons)
     * @param int $idProject - если 0, то навигацию делаем для всех задач. Если > 1, то для конкретного проекта делаем
     * @return string
     */
    private function _paginHtmlComment($num = 0, $title = 'notTitle', $idProject = 0)
    {
        if(is_null($num))
            return "<li class='active'><a  onClick='return false;'>".$title."</a></li>";
        else
            return "<li ><a href='' id='' onClick='getAllTask(".$idProject.", ".$num."); return false;'>".$title."</a></li>";
    }

    /**
     * Получаем некоторые данные, нужные для добавления задачи и в переменной $data['renderViewTask'] содержится вид всех задач
     * We get some data necessary to add tasks and in the variable $data['renderViewTask'] view shows all tasks
     * @param $data - ссылка  на сам массив, поэтому нет смысла его возвращать (a reference to the array, so it makes no sense to return)
     * @param int $idProject
     */
    private function _additionalGetTask(&$data, $idProject = 0)
    {
        if($data['statusUser'] == 1)
        {
            $data['myProjects'] = $this->_getProject($data['idUser']);
            if(!empty($data['myProjects']))
            {
                //c какой страницы начинать (отсчет начинается с 0)
                $data['from'] = intval($this->input->post('from'));
                //текущая страница
                $data['curent_page'] =  $data['from'];//intval($this->input->post('current_page'));
                //с какой записи начинать выборку (выбранную страницу умножаем на количество задач на странице)
                $data['from'] *= COUNT_OBJECT_PER_PAGE;

                //получаем всех юзеров, которые прикрепленны к самому первому проекту, чтобы при добавлении задачи их показать
                $data['myProjects'][0]['userInProject'] = $this->common_model->getResult('users', 'id_user', explode(',', $data['myProjects'][0]['team_ids']), 'result_array', 'id_user, name, login', null, '', true);

                $allIdProjects = [];
                foreach($data['myProjects'] as $project)
                    $allIdProjects[] = $project['id_project'];

                $this->load->model('task_model');

                //считаем общее количество задач и количество задач для каждого проекта
                //тупанул и написал вместо Task, Project
                $data['countProject_all'] = 0;
                $data['allIdProjects'] = $allIdProjects;
                foreach($allIdProjects as $id)
                {
                    $data['countTask']['countProject_'.$id] = $this->task_model->getAllTasks([$id], $data['segment']);
                    $data['countProject_all'] += $data['countTask']['countProject_'.$id];
                }

                //общее количество задач для конкретного проекта, а не для всех проектов
                if($idProject > 0)
                {
                    if(isset($data['countTask']['countProject_'.$idProject]))
                        $data['countTaskInProject_all'] = $data['countTask']['countProject_'.$idProject];
                }

                //если через ajax пытаемся достать задания по какому либо проекту, то вот это...
                if($idProject > 0)
                {
                    $checkProject = $this->_checkProject($idProject, $data);
                    if($checkProject['status'] == 'error')
                    {
                        $data['status_project'] = $data['task_views'][16];
                        return true;
                    }

                    //[15, 0]: 15 - по сколько записей выводить на страницу. 0 - с первой страницы начинать
                    $data['allTasks'] = $this->task_model->getAllTasks([$idProject], $data['segment'], [COUNT_OBJECT_PER_PAGE, $data['from']]);
                }
                //получаем все задания для всех проектов
                else
                    $data['allTasks'] = $this->task_model->getAllTasks($allIdProjects, $data['segment'], [COUNT_OBJECT_PER_PAGE, $data['from']]);


                //делаем так, что вначале нет навигации
                $data['pagination']['status'] = 'error';
                if(!empty( $data['allTasks'] ))
                {
                    $this->_getNavigation($data, $idProject);
                    foreach($data['allTasks'] as $k=>$task)
                    {
                        switch($task['time_for_complete_value'])
                        {
                            case '0':     $data['allTasks'][$k]['time_for_complete_value'] = mb_substr($data['task_views'][47], 0, 1, 'utf8'); break;
                            case '1':     $data['allTasks'][$k]['time_for_complete_value'] = mb_substr($data['task_views'][48], 0, 1, 'utf8'); break;
                            case '2':     $data['allTasks'][$k]['time_for_complete_value'] = mb_substr($data['task_views'][49], 0, 1, 'utf8'); break;
                            case '3':     $data['allTasks'][$k]['time_for_complete_value'] = mb_substr($data['task_views'][50], 0, 1, 'utf8'); break;
                            case '4':     $data['allTasks'][$k]['time_for_complete_value'] = mb_substr($data['task_views'][51], 0, ($data['segment'] == 'ru') ? 3 : 4, 'utf8'); break;
                        }

                    }

                    //$data['renderNavigationTask'] = $this->load->view('default/common/task/navigation.php', $data, true);
                }

                $data['renderViewTask'] = $this->load->view('default/common/task/content.php', $data, true);
                unset($data['allTasks']);
            }
            else
                $data['countProject_all'] = 0;
        }
        else
            $data['myProjects'] = [];
    }

    /**
     * (AJAX)
     * Получаем все задачи для всех проектов
     * We get all the tasks for all projects
     */
    public function getAllTask()
    {
        $response = $this->common->isAjax(["idProject", 'int', 'notZero']);
        if($response['status'] != 'error')
        {
            $idProject = intval($this->common->clear($this->input->post('idProject', true)));
            $data = $response['data'];
            unset($response['data']);
            $this->load->model('common_model');

            //получаем вид всех задач
            $this->_additionalGetTask($data, $idProject);
            //если существует вид задач
            if(isset($data['renderViewTask']))
            {
                $response = ['status'=>'success', 'content'=> $data['renderViewTask'], 'countProject_all' => $data['countProject_all']];

                //если существует больше 0 задач во всех проектах, то ведем их подсчет
                if($data['countProject_all'] > 0)
                {
                    foreach($data['allIdProjects'] as $k=>$id)
                    {
                        if(isset($data['countTask']['countProject_'.$id]))
                            $response['countProject_'.$id] = $data['countTask']['countProject_'.$id];
                    }

                    $response['idProjects'] = implode('|', $data['allIdProjects']);
                }
            }
            else
                $response = ['status'=>'error', 'resultTitle'=>$data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=>''];

            //если ранее была ошибка
            if(isset($data['status_project']))
                $response['resultText'] = $data['status_project'];
        }

        echo json_encode($response);
    }

    /**
     * Получаем все проекты для данного юзера
     * We get all the projects for a given user
     * @param $idIser
     * @param string $select
     * @param string $return
     * @param $getMy - если false, то возвращаем и мои проекты и те, к которым я прикреплен, если true, то возвращаем только мои проекты (if false, the return and my projects and those to which I have attached, if true, return only my projects)
     * @return mixed
     */
    private function _getProject($idIser, $select = 'id_project, title, team_ids', $return = 'result_array', $getMy = false)
    {
        if($getMy === false)
        {
            //ищем нужный нам id регулярным выражением, или же просто проверяем на то, является ли юзер создателем этого проекта
            //We are looking for the right id regular expression, or just to check whether a user creator of this project
            return $this->db->query("SELECT ".$select." FROM projects  WHERE responsible = ".$idIser." OR team_ids REGEXP '^{$idIser},|,{$idIser},|,{$idIser}$|^{$idIser}$'")->$return();
        }
        else
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
        $data['myProjects'] = $this->_getProject($data['idUser'], 'id_project, title, team_ids', 'result_array', true);

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
                    $data['myProjects'] = $this->_getProject($data['idUser'], 'id_project, title, team_ids', 'result_array', true);
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
    public function download($idTask = 'undefined', $src)
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
        if(is_numeric($idTask))
            $filePath = './file/tasks/'.intval($idTask).'/'.$src;
        else
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
        $response = $this->common->isAjax(["src",'str'], ['idTask', 'int', 'noZero']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];
            unset($response['data']);

            $src = $this->common->clear($this->input->post('src'));
            $idTask = intval($this->common->clear($this->input->post('idTask')));

            $src = explode('/', $src);
            $src = $src[count($src)-1];
            if($idTask <= 0)
                $path = './img/temp/'.$data['login'].'/';
            else
                $path = './file/tasks/'.$idTask.'/';
            log_message('error', $path.$src);
            $response = ['status' => 'success'];
            if(file_exists($path.$src))
            {
                unlink($path.$src);
                log_message('error', $path.$src);

                //если папка пуста, то удаляем ее
                if(file_exists($path))
                {
                    log_message('error', $path.$src);

                    $files = [];
                    $descriptor = opendir($path);
                    while($v = readdir($descriptor))
                    {
                        if($v == '.' || $v == '..')
                            continue;

                        $files[] = $v;
                    }

                    //удаляем папку
                    if(empty($files))
                        rmdir($path);
                }
            }
            else
                $response = ['status' => 'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=> $data['task_controller'][3]." ".$data['task_controller'][4], 'deleteView'=>true];
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
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["userfile",'int'], ['avatarOrNot', 'int', 'notZero'], ['idTask', 'int', 'notZero']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];
            unset($response['data']);

            /**
             * Если равно 0, то это добавляем картинку к задаче. Если 1 - изменяем аватар у юзера
             */
            $avatarOrNot = intval($this->input->post('avatarOrNot', true));
            $idTask = intval($this->input->post('idTask', true));


            $fileSize = ($avatarOrNot == 0 || $avatarOrNot == 2) ? 62914560 : 10485760;
            if($_FILES['userfile']['size'] <= $fileSize)//60Mb // 10485760) //10Mb
            {
                //получаем имя файла и обрабатываем
                $fileName = explode(".", $_FILES['userfile']['name']);
                $endExt = $fileName[count($fileName)-1];
                unset($fileName[count($fileName)-1]);
                $fileName = $this->common->sms_translit(implode('-', $fileName));
                $hash = substr(md5(time().rand(1,500000000)), 0, 8);
                $fileName = $fileName.'--'.$hash;

                $tempPath = 'img/';
                if($avatarOrNot == 0 || $avatarOrNot == 2)
                {
                    //создаем папку временную, если ее не было
                    if($avatarOrNot == 2)
                        $tempPath = 'file/tasks/'.$idTask.'/';
                    else
                        $tempPath .= 'temp/'.$data['login'].'/';

                    //если расширение неизвестное, то запаковываем в zip архив
                    $ext = $this->_findExt($endExt);
                }

                $this->_createFolder($tempPath);



                $config = [];
                $config['upload_path'] = './'.$tempPath;
                $config['file_name'] = $fileName.'.'.$endExt;
                $config['allowed_types'] = ($avatarOrNot == 0 || $avatarOrNot == 2) ? '*' : "gif|jpg|png|jpeg|bmp"; //все типы файлов
                $config['remove_spaces']  = TRUE;
                $this->load->library('upload', $config);

                if($this->upload->do_upload())
                {
                    //если обновляем аватар
                    if($avatarOrNot == 1)
                    {
                        $this->load->model('common_model');
                        $q = $this->common_model->getResult('users', 'id_user', $data['idUser'], 'row_array', 'img');
                        $updateAvatar = $this->common_model->updateData(['img'=>$config['file_name']], 'id_user', $data['idUser'], 'users', true);
                        if($updateAvatar > 0)
                        {
                            if($q['img'] != "noimg.png")
                            {
                                if(file_exists('./img/'.$q['img']))
                                    unlink('./img/'.$q['img']);
                            }

                            $response = ['status'=>'success', 'src'=> base_url().'img/'.$config['file_name']];
                        }
                        else
                        {
                            if(file_exists('./img/'.$config['file_name']))
                                unlink('./img/'.$config['file_name']);

                            $response = ['status'=>'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=> $data['task_controller'][7]];
                        }
                    }
                    //если прикрепляем файл к задаче
                    else
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
                }
                else
                    $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $this->upload->display_errors()];
            }
            else
            {
                $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_controller'][2]];
                if($avatarOrNot >= 1)
                    $response['resultText'] = $data['welcome_controller'][33];
            }

        }

        echo json_encode($response);
    }

    /**
     * Проверяем, есть ли у человека такой проект, или же он прикреплен к нему!
     * We check whether a person has such a project, or whether it is attached to it!
     * @param $idProject
     * @param $data
     * @return array
     */
    private function _checkProject($idProject, &$data)
    {
        $checkProject = $this->common_model->getResult('projects', 'id_project', $idProject, 'row_array');
        $fail = false;
        if(empty($checkProject))
            $fail = true;
        else
        {
            if($checkProject['responsible'] != $data['idUser'])
                if(array_search($data['idUser'], explode(',', $checkProject['team_ids'])) === false)
                    $fail = true;
        }

        if($fail === true)
        {
            $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_controller'][10], 'remove'=>true];
            return $response;
        }

        return ['status'=>'success', 'team_ids' => $checkProject['team_ids']];
    }

    /**
     * Получаем все логины, которые привязаны к конкретному проекту
     * We get all the logins that are tied to a specific project
     */
    public function getAllUsersProject()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["idProject",'int']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];
            unset($response['data']);

            $this->load->model('common_model');

            //проверяем, есть ли такой проект у человека
            $idProject = intval($this->common->clear($this->input->post('idProject', true)));
            $errorOrNot = $this->_checkProject($idProject, $data);
            if($errorOrNot['status'] == 'error')
            {
                echo json_encode($errorOrNot);
                return true;
            }

            //получаем всех юзеров, которые прикрепленны к самому первому проекту
            $allUsers = $this->common_model->getResult('users', 'id_user', explode(',', $errorOrNot['team_ids']), 'result_array', 'id_user, name, login', null, '', true);
            if(!empty($allUsers))
            {
                foreach($allUsers as $k=>$v)
                {
                    $response['users'][$k]['name'] = $v['name'];
                    $response['users'][$k]['id_user'] = $v['id_user'];
                    $response['users'][$k]['login'] = $v['login'];
                }
                $response['status'] = 'success';
            }
            else
                $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  "У проекта вообще НИКТО не прикреплен!"];
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
     * Функция обновления рабочего времени для юзеров
     * Update feature of working time for users
     * @param $checkPerformer
     * @param $q
     * @param $data
     * @return int|null
     */
    private function _updateUsersTime($checkPerformer, $q, $data)
    {
        $updateUser = null;
        $new = ['hoursInDayToWork'    =>  $this->common->clear($this->input->post('hoursInDayToWork', true))];
        //если не число
        if(!is_numeric($new['hoursInDayToWork']))
        {
            //если у меня изначально не установленно значение, то это ошибка
            if(!is_numeric($q['hoursInDayToWork']))
                $updateUser = 0;
            else
            {
                //если я ставлю задачу не себе, а другому юзеру
                if($data['idUser'] != $checkPerformer['id_user'])
                {
                    //если у выполняющего задание, еще не заданы временные границы, то задаем их такие же, как и у его "коллеги"
                    if(!is_numeric($checkPerformer['hoursInDayToWork']) || $checkPerformer['hoursInDayToWork'] <= 0 || $checkPerformer['hoursInDayToWork'] > 20)
                    {
                        $new = ['hoursInDayToWork'    => $q['hoursInDayToWork']];
                        $this->common_model->updateData($new, 'id_user', $checkPerformer['id_user'], 'users', true);
                    }
                }
            }
        }
        //если число
        else
        {
            $new['hoursInDayToWork'] = intval($new['hoursInDayToWork']);
            //если не входит в указанные рамки, то это ошибка
            if($new['hoursInDayToWork'] <= 0 || $new['hoursInDayToWork'] > 20)
            {
                if(!is_numeric($q['hoursInDayToWork']))
                    $updateUser = 0;
            }
            else
            {
                //если не установленные рамки рабочего времени, то ставим их
                if(!is_numeric($q['hoursInDayToWork']))
                    $updateUser = $this->common_model->updateData($new, 'id_user', $data['idUser'], 'users', true);
                else
                {
                    if($q['hoursInDayToWork'] != $new['hoursInDayToWork'])
                        $updateUser = $this->common_model->updateData($new, 'id_user', $data['idUser'], 'users', true);
                }

                //если я ставлю задачу не себе, а другому юзеру
                if($data['idUser'] != $checkPerformer['id_user'])
                {
                    //если у выполняющего задание, еще не заданы временные границы, то задаем их такие же, как и у его "коллеги", который только что задал их
                    if(!is_numeric($checkPerformer['hoursInDayToWork']) || $checkPerformer['hoursInDayToWork'] <= 0 || $checkPerformer['hoursInDayToWork'] > 20)
                        $this->common_model->updateData($new, 'id_user', $checkPerformer['id_user'], 'users', true);
                }
            }
        }

       return $updateUser;
    }

    /**
     * (AJAX)
     * Добавляем задачу в бд
     * Add tasks to the database
     */
    public function addTask()
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["titleTask", 'str'], ["priorityLevel", 'int'], ["descTask", 'str'], ["taskLevel", 'int', 'notZero'], ["perfomerUser", 'int'], ["hoursInDayToWork", 'str'], ["estimatedTimeForTask", 'int'], ["measurementTime", 'int', 'notZero'], ["idProject", 'int']);
        if($response['status'] != 'error')
        {
            $this->load->model('common_model');
            $data = $response['data'];
            unset($response['data']);

            $q = $this->common_model->getResult('users', 'id_user', $data['idUser'], 'row_array', 'hoursInDayToWork');

            $titleTask = $this->common->clear($this->input->post('titleTask', true));
            if(preg_match("/^[а-яА-ЯёЁa-zA-Z0-9\-_ ]{3,256}$/iu", $titleTask))
            {
                //проверяем, есть ли такой проект у человека
                $idProject = intval($this->common->clear($this->input->post('idProject', true)));
                $errorOrNot = $this->_checkProject($idProject, $data);
                if($errorOrNot['status'] == 'error')
                {
                    echo json_encode($errorOrNot);
                    return true;
                }

                //приоритет
                $priorityLevel = intval($this->common->clear($this->input->post('priorityLevel', true)));
                if(empty($this->common_model->getResult('priority', 'id_priority', $priorityLevel, 'row_array')))
                    $priorityLevel = 1;

                //проверяем, есть ли такой юзер
                $idPerformer = intval($this->common->clear($this->input->post('perfomerUser', true)));
                $checkPerformer = $this->common_model->getResult('users', 'id_user', $idPerformer, 'row_array');
                if(empty($checkPerformer))
                {
                    $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_controller'][11]];
                    echo json_encode($response);
                    return true;
                }


                $updateUser = $this->_updateUsersTime($checkPerformer, $q, $data);

                $new = [];
                $new['title']           =   $titleTask;
                $new['priority_id']     =   $priorityLevel;
                $new['status']          =   '0'; // 0 - пока еще только добавленна
                $new['text']            =   $this->common->clear($this->input->post('descTask', true));
                $new['time_add']        =   time();
                $new['day_start']       =   date('d');
                $new['month_start']     =   date('m');
                $new['year_start']      =   date('Y');
                $new['complexity_id']   =   intval($this->common->clear($this->input->post('taskLevel', true)));
                $new['performer_id']    =   intval($this->common->clear($this->input->post('perfomerUser', true)));
                $new['user_id']         =   $data['idUser'];
                $new['project_id']                  =   $idProject;
                $new['time_for_complete']           =   intval($this->common->clear($this->input->post('estimatedTimeForTask', true)));
                $new['time_for_complete_value']     =   intval($this->common->clear($this->input->post('measurementTime', true)));


                $myResponse = explode('|', $this->common_model->insertData('task', $new, true, true));
                if($myResponse[1] > 0)
                {
                    $response = ['status'=> 'success', 'resultTitle'=> $data['task_views'][22], 'resultText'=> $data['task_views'][60]];

                    //если прикрепляли файлы к заданию, то переносим их в новую деррикторию
                    //If you attach files to the task, then transfer them to a new derriktoriyu
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
                else
                    $response['hideTimeBlock'] = true;
            }
            else
                $response = ['status' => 'error', 'resultTitle' => $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText' =>  $data['task_views'][19]." ".$data['task_views'][20]];
        }

        echo json_encode($response);
    }

    private function _computeTimeWork($data, $task)
    {
        if(!empty($task))
        {
            //TODO вычислить время, ушедшее на работу этой задачи
        }
        else
            $this->common->errorCodeLog($data['error_code'][2], [__CLASS__, __METHOD__, __LINE__]);
    }


    /**
     * Показываем полную информацию по заданию. Так сказать внутренности
     * Shows the full information on the instructions. So to say the insides
     * @param $idTask
     * TODO translate
     */
    public function view($idTask)
    {
        $config = [
            'pathToViewDir'     =>  'common/task/view',
            'langArray_1'       =>  'task_controller',
            'langArray_2'       =>  12,
            'authUser'          =>  true,
            'noRedirect'        =>  false, //false - редиректим, true - возвращаем ошибку
        ];
        $data = $this->common->allInit($config);

        $fail = true;
        $idTask = intval($this->common->clear($idTask));
        $fail = $this->common->checkData($idTask, true);
        if($fail === false)
            $this->common->redirect_to('task', $data['task_views'][6]);

        $this->load->model('task_model');
        $this->load->model('common_model');
        //получаем информацию по одной задаче
        $data['infoTask'] = $this->task_model->getInfoTask($idTask, $data['segment']);
        if(empty($data['infoTask']))
            $this->common->redirect_to('task', "Такой задачи не существует");

        //проверям, что это проект юзера
        $check = $this->_checkProject($data['infoTask']['project_id'], $data);
        if($check['status'] == 'error')
            $this->common->redirect_to('task', "Вы трогаете чужие проекты");

        $data['infoTask']['time_add'] = date("Y-m-d H:i:s", $data['infoTask']['time_add']);
        if(is_numeric($data['infoTask']['time_start']))
            $data['infoTask']['time_start'] = date("Y-m-d H:i:s", $data['infoTask']['time_start']);

        if(is_numeric($data['infoTask']['time_end']))
            $data['infoTask']['time_end'] = date("Y-m-d H:i:s", $data['infoTask']['time_end']);

        switch($data['infoTask']['time_for_complete_value'])
        {
            case '0':     $data['infoTask']['time_for_complete_value'] = mb_substr($data['task_views'][47], 0, 1, 'utf8'); break;
            case '1':     $data['infoTask']['time_for_complete_value'] = mb_substr($data['task_views'][48], 0, 1, 'utf8'); break;
            case '2':     $data['infoTask']['time_for_complete_value'] = mb_substr($data['task_views'][49], 0, 1, 'utf8'); break;
            case '3':     $data['infoTask']['time_for_complete_value'] = mb_substr($data['task_views'][50], 0, 1, 'utf8'); break;
            case '4':     $data['infoTask']['time_for_complete_value'] = mb_substr($data['task_views'][51], 0, ($data['segment'] == 'ru') ? 3 : 4, 'utf8'); break;
        }

        //разрешаем загрузку файлов на сайт через ajax (если isset attachUploadSripts, то подгружаем нужные скрипты)
        $data['attachUploadSripts'] = true;
        $data['filesAttach'] = $this->_getAllAttach($data['infoTask']['id_task'], null, $data['task_controller'][9]);
        if(isset($data['filesAttach']['status']) || @empty($data['filesAttach']))
            unset($data['filesAttach']);

        //приоритет задания
        $data['priority'] =  $this->common_model->getResult('priority', '', '', 'result_array', 'id_priority, color, icon, title_'.$data['segment'], 'id_priority', 'asc');
        //сложность задания
        $data['complexity'] = $this->common_model->getResult('complexity', '', '', 'result_array', 'id_complexity, color, name_complexity_'.$data['segment'], 'id_complexity', 'asc');
        //доделываем название страницы
        $data['title'] .= ' "'.$data['infoTask']['title'].'"';

        $this->display_lib->display($data, $config['pathToViewDir']);
    }




    /**
     * (AJAX)
     * Обновляем задание, но только вываливающиеся списки
     * Update task, but just select into html
     * @param null $whatUpdate - ид html элемента, который отвечает за какой либо параметр (id html element that is responsible for any parameter)
     * todo выводить данные о том, для js, "начало работы", задача выполнена, и сколько ушло времени на все это.
     */
    public function updateTask($whatUpdate = null)
    {
        //проверяем на ajax и его параметры
        $response = $this->common->isAjax(["num",'int', 'notZero'], ['idTask', 'int']);
        if($response['status'] != 'error')
        {
            $data = $response['data'];
            unset($response['data']);

            //проверяем то, какой из параметров нам стоит обновить в бд. На данный момент передаются id html элементов, которые прикреплены к определенному select в html
            //check which of the options we should update the database. Currently transmitted id html elements, which are secured to select a certain html
            $fail = false;
            $whatUpdate = $this->common->clear($whatUpdate);
            switch($whatUpdate)
            {
                case 'taskLevelInfo':       $row = 'complexity_id';     break;
                case 'statusLevelInfo':     $row = 'status';            break;
                case 'priorityLevelInfo':   $row = 'priority_id';       break;
                default:
                    $this->common->errorCodeLog($data['error_code'][1], [__CLASS__, __METHOD__, __LINE__]);
                    $fail = true;
            }


            if($fail === false)
            {
                $this->load->model('common_model');
                $idTask = intval($this->common->clear($this->input->post('idTask')));
                $num = intval($this->common->clear($this->input->post('num')));

                $check = $this->common_model->getResult('task', 'id_task', $idTask, 'row_array', 'performer_id, pause, status, time_start');
                if(!empty($check))
                {
                    $new = [];
                    //если пытаются изменить статус у задачи, которая присвоена другому юзеру
                    if($whatUpdate == 'statusLevelInfo')
                    {
                        if($data['idUser'] != $check['performer_id'])
                        {
                            echo json_encode(['status' => 'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=> $data['task_controller'][10]]);
                            return;
                        }

                        //если задача выполнена, но ее хотят поставить на паузу
                        if($check['status'] == 2 && $num == 3)
                        {
                            echo json_encode(['status' => 'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=> $data['task_controller'][14]]);
                            return;
                        }

                        //если изменяем статус с "поставленно" на что то другое
                        if($check['status'] == 0 && $num > 0)
                        {
                            if($check['time_start'] == '')
                                $new['time_start'] = time();
                        }

                        //если задача "выполнена"
                        if($num == 2)
                            $new['time_end'] = time();

                        //если задача на "pause"
                        if($num == 3 && $check['status'] != 3)
                        {
                            if($check['pause'] != '')
                                $new['pause'] = unserialize($check['pause']);

                            $new['pause'][]['start'] = time();
                        }
                        //если снимаем с паузы
                        elseif($num < 3 && $check['status'] == 3)
                        {
                            if($check['pause'] != '')
                                $new['pause'] = unserialize($check['pause']);
                            else
                                $new['pause'][]['start'] = time();

                            //находим последнюю паузу, и у нее ставим конечное время
                            $new['pause'][count($new['pause'])-1]['end'] = time();
                        }

                        if(isset($new['pause']))
                            if(!empty($new['pause']))
                                $new['pause'] = serialize($new['pause']);

                    }

                    $new[$row] = $num;

                    //TODO сделать время начало работы таким: вначале показывать сколько секунд прошло, потом минут, потом часов, потом дней и часов, если больше 2 дней, то выводить простое date('...', data);
                    //TODO сделать поменять исполнителя!

                    $q = $this->common_model->updateData($new, 'id_task', $idTask, 'task', true);
                    if($q > 0)
                        $response = ['status' => 'success'];
                    else
                        $response = ['status' => 'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=> $data['welcome_controller'][13]];
                }
                else
                    $response = ['status' => 'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=> $data['task_controller'][13]];
            }
            else
                $response = ['status' => 'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=> $data['task_views'][6]];
        }

        echo json_encode($response);
    }
}
