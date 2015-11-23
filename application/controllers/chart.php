<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Обязательно класс называть с большой буквы, а файл с маленькой буквы!!!
 * Be sure to call the class with a capital letter, as a file with a small letter !!!
 * Class Chart
 * @property Chart_model $chart_model
 * @property Common $common - общая библиотека с авторизацией юзера и проверками (shared library user and authorization checks)
 * @property Common_model $common_model - общая модель для работы с бд (general model for working with database)
 * @property Display_lib $display_lib - достает нужные вьюхи (It lacks the necessary view file)
 */
class Chart extends CI_Controller {


    public function __construct()
    {
        parent::__construct();
        $this->load->model('common_model');
    }

    /**
     * Если такой ячейки не существовало, то мы ее создаем и увеличиваем на единицу.
     * @param $data
     * @param $task
     * @param int $id_sort
     * @param $is_square
     * @param $nameArray
     */
    private function _countTaskForFilter(&$data, $task, $id_sort = 0, $is_square = false, $nameArray = ['complexityJsAll', 'complexityJsAllComplete', 'complexityJsAllNeedDo'])
    {
        foreach($nameArray as $name)
        {
            if($is_square === true)
            {
                if(!isset($data[$name][$task['project_id']]['title']['projectTitle']))
                    $data[$name][$task['project_id']]['title']['projectTitle'] = $task['title'];

                if(!isset($data[$name][$task['project_id']][$id_sort]))
                    $data[$name][$task['project_id']][$id_sort] = 0;
            }
            else
            {
                if(!isset($data[$name][$id_sort]))
                    $data[$name][$id_sort] = 0;
            }
        }

        if($is_square === true)
        {
            $data[$nameArray[0]][$task['project_id']][$id_sort]++;

            if($task['status'] == 2)
                $data[$nameArray[1]][$task['project_id']][$id_sort]++;
            else
                $data[$nameArray[2]][$task['project_id']][$id_sort]++;
        }
        else
        {
            $data[$nameArray[0]][$id_sort]++;

            if($task['status'] == 2)
                $data[$nameArray[1]][$id_sort]++;
            else
                $data[$nameArray[2]][$id_sort]++;
        }
    }

    /**
     * Если такой ячейки не добавилось при первом цикле, то записываем 'null'
     * @param $data
     * @param int $id_sort
     * @param $is_square
     * @param $namesArray
     */
    private function _checkExist(&$data, $id_sort = 0, $is_square = false, $namesArray = ["complexityJsAll", "complexityJsAllComplete", "complexityJsAllNeedDo"])
    {
        foreach($namesArray as $name)
        {
            if($is_square === true)
            {
                foreach($data[$name] as $k=>$val)
                {
                    if(!isset($data[$name][$k][$id_sort]))
                        $data[$name][$k][$id_sort] = 0;
                }

            }
            else
            {
                if(!isset($data[$name][$id_sort]))
                    $data[$name][$id_sort] = 0;
            }
        }

    }

    /**
     * Полученные данные складываем в одну json строку, чтобы передать в js для постраения круглого графика
     * @param $data
     * @param $name
     * @param int $id_sort
     * @param int $i
     */
    private function _concatDataCirckle(&$data, $name, $id_sort = 0, $nameArray = [], $i = 0)
    {
        foreach($nameArray as $names)
        {
            //заносим json массив с данными, чтобы потом вставить в highcharts
            $data[$names[1]][$i] = ['name'=>$name, 'y'=>$data[$names[0]][$id_sort]];
            //если i = 0, то подставляем дополнительные параметры, которые выдвигают часть графика
            if($i == 0)
                $data[$names[1]][$i]['sliced'] = $data[$names[1]][$i]['selected'] = true;
        }
    }

    private function _concatDataSquare(&$data, $nameArray = [], $whatSort = [] )
    {
        $countIter = count($whatSort[0]);
        foreach($nameArray as $names)
        {

            $count['allComplexitySquare'] = [];
            foreach($data[$names[0]] as $k=>$title)
            {
                if(is_numeric($k))
                {
                    for($l = 1; $l <= $countIter; $l++)
                    {
                        if(!isset($data[$names[0]][$k][$l]))
                            $data[$names[0]][$k][$l] = 'null';

                        if($data[$names[0]][$k][$l] == 0)
                            $data[$names[0]][$k][$l] = 'null';

                        $count['allComplexitySquare'][$l][] = $data[$names[0]][$k][$l];
                    }
                }
            }

            for($l = 1; $l <= $countIter; $l++)
                $data[$names[1]][] = ['name'=>$whatSort[0][$l-1][$whatSort[1]], 'data'=> $count['allComplexitySquare'][$l]];

            //тут названия проектов
            $data[$names[2]] = $data[$names[0]]['allTitle'];
        }
    }

    private function _jsonConvert(&$data, $nameArray = ['seriesForJsComplexityAll', 'seriesForJsComplexityAllComplete', 'seriesForJsComplexityAllNeedDo'])
    {
        foreach($nameArray as $name)
        {
            $data[$name] = json_encode($data[$name]);
            $data[$name] = preg_replace('/\"null\"/iu', 'null', $data[$name]);
        }
    }


    private function _nameForChartSquare(&$data, $namesArray = ['complexityJs', 'complexityJsComplete', 'complexityJsNeedDo'])
    {
        foreach($namesArray as $name)
        {
            if(!isset($data[$name]['allTitle']))
                $data[$name]['allTitle'] = [];

            $i = 0;
            foreach($data[$name] as $tmp_data)
            {
                if(!empty($tmp_data['title']))
                {
                    $data[$name]['allTitle'][$i] = $tmp_data['title']['projectTitle'];
                    $i++;
                }
            }
        }
    }

    private function _getLangForCPChar(&$data)
    {
        $data['titleForJsCPCircle'] = [
            'all'       =>  [
                'main'      =>  $data['chart_controller']['circleChar'][0],
                "subtitle"  =>  $data['chart_controller']['circleChar'][1]
            ],
            'complete'  =>  [
                'main'      =>  $data['chart_controller']['circleChar'][2]
            ],
            'needDo'    =>  [
                'main'      =>  $data['chart_controller']['circleChar'][3]
            ]
        ];
        $data['titleForJsCPCircle']['complete']['subtitle'] = $data['titleForJsCPCircle']['needDo']['subtitle'] = $data['titleForJsCPCircle']['all']['subtitle'];

        $data['titleForJsCPSquare'] = [
            'all'       =>  [
                'main'      =>  $data['chart_controller']['squareChar'][0],
                "subtitle"  =>  $data['chart_controller']['circleChar'][1]
            ],
            'complete'  =>  [
                'main'      =>  $data['chart_controller']['squareChar'][1]
            ],
            'needDo'    =>  [
                'main'      =>  $data['chart_controller']['squareChar'][2]
            ]
        ];
        $data['titleForJsCPSquare']['complete']['subtitle'] = $data['titleForJsCPSquare']['needDo']['subtitle'] = $data['titleForJsCPSquare']['all']['subtitle'];

        $this->_jsonConvert($data, ['titleForJsCPCircle', 'titleForJsCPSquare']);
    }

    private function _getTimeForAllUser(&$data)
    {
        //получаем все проекты, к которым я прикреплен и которыя я создал
        $myProjects = $this->_getProject($data['idUser']);
        if(!empty($myProjects))
        {
            //получаем все id юзеров, которые есть в моих проектах
            $allIdTeam = [$data['idUser']];
            foreach($myProjects as $project)
            {
                foreach(explode(",", $project['team_ids']) as $idOneUser)
                {
                    if($idOneUser != $data['idUser'] && array_search($idOneUser, $allIdTeam) === false)
                        $allIdTeam[] = $idOneUser;
                }
            }

            foreach($allIdTeam as $k=>$idOneUser)
            {
                $this->db->join("users", "users.id_user = task.performer_id", "left");
                $taskUsers[$k] = $this->common_model->getResult('task', ['task.performer_id', 'task.status'], [$idOneUser, 2], 'result_array', 'task.project_id, task.pause, task.pause_for_complite, users.hoursInDayToWork, users.name, users.login', 'id_task');

                if(!empty($taskUsers[$k]))
                {
                    $data['myTimeCompliteForTask'] = 0;
                    $data['timeForUser'][$idOneUser]['allWorkDays'] = $data['timeForUser'][$idOneUser]['allWorkHours'] = $data['timeForUser'][$idOneUser]['allWorkMinutes'] = $data['timeForUser'][$idOneUser]['allWorkSeconds'] = 0;
                    foreach($taskUsers[$k] as $key=>$task)
                    {
                        $data['timeForUser'][$idOneUser]['hoursInDayToWork'] = $task['hoursInDayToWork'];
                        $data['timeForUser'][$idOneUser]['name'] = $task['name']." (".$task['login'].")";

                        $this->_computeTimeForEnd($taskUsers[$k][$key], $data);

                        //считаем сколько всего было затрачено времени на выполнение всех задач
                        $data['timeForUser'][$idOneUser]['allWorkDays'] += $taskUsers[$k][$key]['howWorkDay'];

                        $data['timeForUser'][$idOneUser]['allWorkHours'] += $taskUsers[$k][$key]['howHour'];
                        if($data['timeForUser'][$idOneUser]['allWorkHours'] >= $taskUsers[$k][$key]['hoursInDayToWork'])
                        {
                            $data['timeForUser'][$idOneUser]['allWorkDays']++;
                            $data['timeForUser'][$idOneUser]['allWorkHours'] -= $data['hoursInDayToWork'];
                        }

                        $data['timeForUser'][$idOneUser]['allWorkMinutes'] += $taskUsers[$k][$key]['howMinute'];
                        if($data['timeForUser'][$idOneUser]['allWorkMinutes'] >= 60)
                        {
                            $data['timeForUser'][$idOneUser]['allWorkHours']++;
                            $data['timeForUser'][$idOneUser]['allWorkMinutes'] -= 60;
                        }

                        $data['timeForUser'][$idOneUser]['allWorkSeconds'] += $taskUsers[$k][$key]['howSecond'];
                        if($data['timeForUser'][$idOneUser]['allWorkSeconds'] >= 60)
                        {
                            $data['timeForUser'][$idOneUser]['allWorkMinutes']++;
                            $data['timeForUser'][$idOneUser]['allWorkSeconds'] -= 60;
                        }

                    }


                    $data['timeForUser'][$idOneUser]['allTime'] = ($data['timeForUser'][$idOneUser]['allWorkDays'] * $data['timeForUser'][$idOneUser]['hoursInDayToWork']) + $data['timeForUser'][$idOneUser]['allWorkHours'] + $data['timeForUser'][$idOneUser]['allWorkMinutes']/60 + $data['timeForUser'][$idOneUser]['allWorkSeconds']/3600;
                    $data['timeForUser'][$idOneUser]['allTime'] = round($data['timeForUser'][$idOneUser]['allTime'], 4);

                    if($k == 0)
                        $data['series'][] = ['name'=>$data['timeForUser'][$idOneUser]['name'], "data"=> [$data['timeForUser'][$idOneUser]['allTime']]];
                    else
                    {
                        $nulls = [];
                        for($i = $k - 1; $i >= 0; $i--)
                            $nulls[] = "null";

                        $nulls[] = $data['timeForUser'][$idOneUser]['allTime'];
                        $data['series'][] = ['name'=>$data['timeForUser'][$idOneUser]['name'], "data"=> $nulls];
                    }

                    if(!isset($data['myTimeCompliteForTask']))
                        $data['myTimeCompliteForTask'] = [];

                    $temp = "";
                    if(!isset($data['allTimesUser']))
                        $data['allTimesUser'] = [];

                    //заносим ответ в массив
                    if( $data['timeForUser'][$idOneUser]['allWorkDays'] > 0)
                        $temp = $data['timeForUser'][$idOneUser]['allWorkDays'].$data['task_controller'][19]." ";

                    if($data['timeForUser'][$idOneUser]['allWorkHours'] > 0)
                        $temp .= $data['timeForUser'][$idOneUser]['allWorkHours'].$data['task_controller'][18]." ";

                    if($data['timeForUser'][$idOneUser]['allWorkMinutes'] > 0)
                        $temp .= $data['timeForUser'][$idOneUser]['allWorkMinutes'].$data['task_controller'][17]." ";

                    if($data['timeForUser'][$idOneUser]['allWorkSeconds'] > 0)
                        $temp .= $data['timeForUser'][$idOneUser]['allWorkSeconds'].$data['task_controller'][15];

                    $data['allTimesUser'][$idOneUser] = $temp;
                }
                else
                {

                }
            }

            $data['series'] = json_encode($data['series']);
            $data['series'] = preg_replace('/\"null\"/iu', 'null', $data['series']);


        }
        else
            $data['notProject'] = true;
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
     * Считаем сколько времени было потрачено на задачу, с учетом паузы и то, сколько длиться рабочий день у юзера
     * We consider how much time was spent on the task, taking into account the pauses and how much last time at the user
     * @param $task
     * @return int
     */
    private function _getWorkTimeInSeconds(&$task)
    {
        //получаем все время, которая показывает когда начинали задачу и заканчивали
        $num_pause_complite = 0;
        if($task['pause_for_complite'] != "")
        {
            $task['pause_for_complite'] = unserialize($task['pause_for_complite']);

            foreach($task['pause_for_complite'] as $k=>$v)
                $num_pause_complite += $v['time_end'] - $v['time_start'];
        }

        //получаем все паузы и считаем ответ в секундах, который содержит точное время бездействия данной задачи
        $num_pause = 0;
        if($task['pause'] != "")
        {
            $task['pause'] = unserialize($task['pause']);

            foreach($task['pause'] as $k=>$v)
                $num_pause += $v['end'] - $v['start'];

            //получаем всю паузу в секундах
            $task['howMuchPause'] = $num_pause;
        }

        //получаем время в секундах, которое потребовалось на выполнение задачи
        if($num_pause_complite > 0)
        {
            if($num_pause > 0)
                $howInSecond = $num_pause_complite - $num_pause;
            else
                $howInSecond = $num_pause_complite;
        }
        //вот тут ошибка, хотя не может быть, чтобы поле "pause_for_complite" будет пустым, поэтому вероятность мала, что $howInSecond будет равен 1 секунде.
        else
            $howInSecond = 1;

        return $howInSecond;
    }

    /**
     * Считаем реальное количество времени, потраченное на выполнение задачи
     * We consider the real amount of time spent for performance of a task
     * @param $task - данные по задаче (data on a task)
     * @param $user - данные по юзеру (data on a user)
     * @param $data
     */
    private function _computeTimeForEnd(&$task, &$data)
    {
        $howInSecond = $this->_getWorkTimeInSeconds($task);

        //получаем количество рабочих часов юзера в день в секундах
        $data['hoursInDayToWork'] = (intval($data['hoursInDayToWork']) > 0) ? $data['hoursInDayToWork'] : 8;
        $data['hoursInDayToWork'] = $data['hoursInDayToWork'] * 3600;

        //получаем количество рабочих дней, которое потратили на выполнение задания
        $howWorkDay = $howInSecond / $data['hoursInDayToWork'];
        $howWorkDay = floor($howWorkDay); //сколько рабочих дней

        //сколько осталось еще времени в секундах
        $howLeft = $howInSecond - ($howWorkDay * $data['hoursInDayToWork']);

        //узнаем количество часов
        $howHour = $howLeft / 3600;
        $howMinute = $howHour - floor($howHour);

        //узнаем количество минут
        $howMinute = $howMinute * 60;
        $howSecond = $howMinute - floor($howMinute);

        //узнаем количество секунд
        $howSecond = $howSecond * 60;


        $howHour = floor($howHour);
        $howMinute = floor($howMinute);
        $howSecond = round($howSecond);

        /**
         * Следующие три проверки нужны для того, что иногда высвечивается ответ в формате 1 час 60 секунд, но по факту это должно быть 1 час 1 минута
         * The following three checks are necessary for this purpose that the answer in a format is sometimes highlighted 1 hour 60 seconds, but upon it has to be 1 hour 1 minute
         */
        //если часов равно количеству часов в рабочем дне, то увеличиваем день, а часы обнуляем
        if($howHour >= $data['hoursInDayToWork']){ $howWorkDay++; $howHour -= $data['hoursInDayToWork'];}

        //если минут равно 1 часу, то увеличиваем часы, а минуты обнуляем
        if($howMinute >= 60){ $howHour++; $howMinute -= 60;}

        //если количество секунд равно 1 минуте, то увеличиваем минуты, а секунды обнуляем
        if($howSecond >= 60){ $howMinute++; $howSecond -= 60;}


        $task['howWorkDay'] = $howWorkDay;
        $task['howHour']    = $howHour;
        $task['howMinute']  = $howMinute;
        $task['howSecond']  = $howSecond;
    }

    /**
     * Делал эту функцию за несколько дней перед армией!!! ГОВНОКОД
     */
    public function index()
    {
        $config = [
            'pathToViewDir'     =>  'chart',
            'langArray_1'       =>  'chart_controller',
            'langArray_2'       =>  0,
            'authUser'          =>  true,
            'noRedirect'        =>  false //false - редиректим, true - возвращаем ошибку
        ];
        $data = $this->common->allInit($config);

        //разрешаем подключение скриптов, чтобы сделать красивые checkbox
        $data['useCheckbox'] = true;

        $data['additionalInfoUser']  = $this->common_model->getResult('users', 'id_user', $data['idUser'], 'row_array', 'showOrNot3DChars, showOrNotExportChars');

        $this->_getTimeForAllUser($data);

        //получаем все мои задания с названием проекта, к которому они принадлежат
        $this->db->join("projects", "projects.id_project = task.project_id", "left");
        $this->db->join("complexity", "complexity.id_complexity = task.complexity_id", "left");
        $this->db->join("priority", "priority.id_priority = task.priority_id", "left");
        $allTasks = $this->common_model->getResult('task', 'task.performer_id', $data['idUser'], 'result_array', 'task.project_id, task.complexity_id, task.priority_id, projects.title, task.status, complexity.name_complexity_'.$data['segment'].", priority.title_".$data['segment']);

        $this->_getMyCharts($data, $allTasks);

        $this->_getLangForCPChar($data);

        $this->display_lib->display($data, $config['pathToViewDir']);
    }

    private function _getMyCharts(&$data, $allTasks)
    {
        //названия сложности задания
        $complexity = $this->common_model->getResult('complexity', '', '', 'result_array', 'id_complexity, name_complexity_'.$data['segment'], 'id_complexity', 'asc');
        $priority = $this->common_model->getResult('priority', '', '', 'result_array', 'id_priority, title_'.$data['segment'], 'id_priority', 'asc');

        //цвета для графика. 1 - легкая, 2 - средняя, 3 - сложная задача
        $data['colorsForJsComplexity'] = ['#449D44', '#EC971F', '#C9302C'];
        $data['colorsForJsPriority'] = ['#B0B0B0', '#777777', '#337AB7', '#F0AD4E', '#D9534F'];
        $this->_jsonConvert($data, ['colorsForJsComplexity', 'colorsForJsPriority']);


        //если есть вообще задания
        if(!empty($allTasks))
        {
            //считаем количество задач с сортировкой по сложности
            foreach($allTasks as $k=>$task)
            {
                //количество всех задач для круглых графиков
                $this->_countTaskForFilter($data, $task, $task['complexity_id']);
                $this->_countTaskForFilter($data, $task, $task['priority_id'], false, ['priorityJsAll', 'priorityJsAllComplete', 'priorityJsAllNeedDo']);

                //количество всех задач для квадратных графиков
                $this->_countTaskForFilter($data, $task, $task['complexity_id'], true, ['complexityJs', 'complexityJsComplete', 'complexityJsNeedDo']);
                $this->_countTaskForFilter($data, $task, $task['priority_id'], true, ['priorityJs', 'priorityJsComplete', 'priorityJsNeedDo']);
            }


            $i = 0;
            //сортировка по сложности
            foreach($complexity as $k=>$oneComplexity)
            {
                //проверяем, существуют ли данные, а если нет, то дописываем их
                $this->_checkExist($data, $oneComplexity['id_complexity']);
                $this->_checkExist($data, $oneComplexity['id_complexity'], true, ['complexityJs', 'complexityJsComplete', 'complexityJsNeedDo']);

                $tmpName = $oneComplexity['name_complexity_'.$data['segment']];
                $tempArray = [
                    ['complexityJsAll', 'seriesForJsComplexityAll'],
                    ['complexityJsAllComplete', 'seriesForJsComplexityAllComplete'],
                    ['complexityJsAllNeedDo', 'seriesForJsComplexityAllNeedDo']
                ];
                //объединяем все данные для каждого графика в один массив
                $this->_concatDataCirckle($data, $tmpName, $oneComplexity['id_complexity'], $tempArray, $i);

                $i++;
            }

            $i = 0;
            //сортировка по сложности
            foreach($priority as $k=>$onePriority)
            {
                //проверяем, существуют ли данные, а если нет, то дописываем их
                $this->_checkExist($data, $onePriority['id_priority'], false, ['priorityJsAll', 'priorityJsAllComplete', 'priorityJsAllNeedDo']);
                $this->_checkExist($data, $onePriority['id_priority'], true, ['priorityJs', 'priorityJsComplete', 'priorityJsNeedDo']);

                $tmpName = $onePriority['title_'.$data['segment']];
                $tempArray = [
                    ['priorityJsAll', 'seriesForJsPriorityAll'],
                    ['priorityJsAllComplete', 'seriesForJsPriorityAllComplete'],
                    ['priorityJsAllNeedDo', 'seriesForJsPriorityAllNeedDo']
                ];
                //объединяем все данные для каждого графика в один массив
                $this->_concatDataCirckle($data, $tmpName, $onePriority['id_priority'], $tempArray, $i);

                $i++;
            }

            //получаем название проектов, для каждого из квадратных графиков
            $this->_nameForChartSquare($data);
            $tempArray = [
                ['complexityJs', 'seriesForJsComplexityProject', 'titleForJsComplexityProject'],
                ['complexityJsComplete', 'seriesForJsComplexityProjectComplete', 'titleForJsComplexityProjectComplete'],
                ['complexityJsNeedDo', 'seriesForJsComplexityProjectNeedDo', 'titleForJsComplexityProjectNeedDo']
            ];
            //объединяем все в 1 массив
            $this->_concatDataSquare($data, $tempArray, [$complexity, 'name_complexity_'.$data['segment']]);


            //получаем название проектов, для каждого из квадратных графиков
            $this->_nameForChartSquare($data, ['priorityJs', 'priorityJsComplete', 'priorityJsNeedDo']);
            $tempArray = [
                ['priorityJs', 'seriesForJsPriorityProject', 'titleForJsPriorityProject'],
                ['priorityJsComplete', 'seriesForJsPriorityProjectComplete', 'titleForJsPriorityProjectComplete'],
                ['priorityJsNeedDo', 'seriesForJsPriorityProjectNeedDo', 'titleForJsPriorityProjectNeedDo']
            ];
            //объединяем все в 1 массив
            $this->_concatDataSquare($data, $tempArray, [$priority, 'title_'.$data['segment']]);



            //преобразуем в строку, нужные нам данные
            $this->_jsonConvert($data);
            $this->_jsonConvert($data, ['seriesForJsPriorityAll', 'seriesForJsPriorityAllComplete', 'seriesForJsPriorityAllNeedDo']);
            $this->_jsonConvert($data, ['seriesForJsComplexityProject', 'seriesForJsComplexityProjectComplete', 'seriesForJsComplexityProjectNeedDo']);
            $this->_jsonConvert($data, ['seriesForJsPriorityProject', 'seriesForJsPriorityProjectComplete', 'seriesForJsPriorityProjectNeedDo']);

            $this->_jsonConvert($data, ['titleForJsPriorityProject', 'titleForJsPriorityProjectComplete', 'titleForJsPriorityProjectNeedDo']);
            $this->_jsonConvert($data, ['titleForJsComplexityProject', 'titleForJsComplexityProjectComplete', 'titleForJsComplexityProjectNeedDo']);
        }
        else
            $data['notTask'] = true;
    }

}