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
        $this->load->model('chart_model');
    }

    /**
     * Расчитываем количество задач для круглых графиков (по сложности, приоритету). Если такой ячейки не существовало, то мы ее создаем и увеличиваем на единицу.
     * We expect the number of tasks for circular charts (complexity, priority). If a cell does not exist, we create it and incremented.
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
            //если это для круглых графиков считаем
            if($is_square === true)
            {
                //заносим название всех проектов в один массив
                if(!isset($data[$name][$task['project_id']]['title']['projectTitle']))
                    $data[$name][$task['project_id']]['title']['projectTitle'] = $task['title'];

                //если не существует, то создаем эту ячейку и заносим 0
                if(!isset($data[$name][$task['project_id']][$id_sort]))
                    $data[$name][$task['project_id']][$id_sort] = 0;
            }
            //если для квадратных
            else
            {
                //если не существует, то создаем эту ячейку и заносим 0
                if(!isset($data[$name][$id_sort]))
                    $data[$name][$id_sort] = 0;
            }
        }

        //если это для круглых графиков считаем
        if($is_square === true)
        {
            //увеличиваем число, если это просто задача
            $data[$nameArray[0]][$task['project_id']][$id_sort]++;

            //если задача решена, то и тут увеличиваем
            if($task['status'] == 2)
                $data[$nameArray[1]][$task['project_id']][$id_sort]++;
            //если задача еще выполняется, то тут увиличиваем
            else
                $data[$nameArray[2]][$task['project_id']][$id_sort]++;
        }
        else
        {
            //тоже самое как и выше...
            $data[$nameArray[0]][$id_sort]++;

            if($task['status'] == 2)
                $data[$nameArray[1]][$id_sort]++;
            else
                $data[$nameArray[2]][$id_sort]++;
        }
    }

    /**
     * Если такой ячейки не добавилось при первом цикле, то записываем 'null'
     * If such a cell is not added during the first cycle, the write 'null'
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
     * The findings add up to a json string to pitch in for js postraeniya round schedule
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

    /**
     * Складываем все названия проектов в один массив и все полученные данные, для квадратных графиков, тоже складываем в один массив
     * Put all the names of the projects into a single array and all of the data to the square plots also folds into a single array
     * @param $data
     * @param array $replace
     * @param array $whatSort
     */
    private function _concatDataSquare(&$data, $replace = [], $whatSort = [] )
    {
        $tempArray = [
            ['%r%Js', 'seriesForJs%r2%Project', 'titleForJs%r2%Project'],
            ['%r%JsComplete', 'seriesForJs%r2%ProjectComplete', 'titleForJs%r2%ProjectComplete'],
            ['%r%JsNeedDo', 'seriesForJs%r2%ProjectNeedDo', 'titleForJs%r2%ProjectNeedDo']
        ];

        //заменяем название ячеек, для дальнейшей работы
        foreach($tempArray as $key=>$aArray)
        {
            $tempArray[$key][0] = preg_replace("/%r%/iu",  $replace[0], $aArray[0]);
            $tempArray[$key][1] = preg_replace("/%r2%/iu", $replace[1], $aArray[1]);
            $tempArray[$key][2] = preg_replace("/%r2%/iu", $replace[1], $aArray[2]);
        }

        $countIter = count($whatSort[0]);
        foreach($tempArray as $names)
        {

            $count['allSquare'] = [];
            foreach($data[$names[0]] as $k=>$title)
            {
                if(is_numeric($k))
                {
                    for($l = 1; $l <= $countIter; $l++)
                    {
                        //если не существет количество задач, или равно 0, то записываем туда null, чтобы графики были красивыми
                        if(!isset($data[$names[0]][$k][$l]))
                            $data[$names[0]][$k][$l] = 'null';

                        if($data[$names[0]][$k][$l] == 0)
                            $data[$names[0]][$k][$l] = 'null';

                        $count['allSquare'][$l][] = $data[$names[0]][$k][$l];
                    }
                }
            }

            //собираем все в один массив
            for($l = 1; $l <= $countIter; $l++)
                $data[$names[1]][] = ['name'=>$whatSort[0][$l-1][$whatSort[1]], 'data'=> $count['allSquare'][$l]];

            //тут названия проектов
            $data[$names[2]] = $data[$names[0]]['allTitle'];
        }
    }

    /**
     * Конвертируем все в строку и заменяем "null" -> null
     * Convert all line and replace "null" -> null
     * @param $data
     * @param array $nameArray - в каких ячейках нужно заменить
     */
    private function _jsonConvert(&$data, $nameArray = ['seriesForJsComplexityAll', 'seriesForJsComplexityAllComplete', 'seriesForJsComplexityAllNeedDo'])
    {
        foreach($nameArray as $name)
        {
            $data[$name] = json_encode($data[$name]);
            $data[$name] = preg_replace('/\"null\"/iu', 'null', $data[$name]);
        }
    }

    /**
     * Для квадратных графиков, складываем по порядку название проектов в один массив
     * For square plots, down by order of the names of projects in one array
     * @param $data
     * @param array $namesArray
     */
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

    /**
     * Получаем все слова, которые используется в графиках и конвертируем их в строку
     * We get all the words that are used in graphs and convert them to a string
     * @param $data
     */
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

    /**
     * Чтобы на одном графике можно было понять где кто, я добавляю с каждым новым юзером (перед ним) null, чтобы график друг на друга не наседал
     * To schedule one could understand where someone, I add to each new user (to him) null, that the graph at each other were pressing
     * @param $k - итератор в цикле
     * @param $name - имя юзера
     * @param $timeInHour - время в часах
     * @param $data
     */
    private function _addNullsToSeries($k, $name, $timeInHour, &$data)
    {
        //если это самый первый проход цикла
        if($k == 0)
            $data['series'][] = ['name'=>$name, "data"=> [$timeInHour]];
        else
        {
            //добавляем количество null, равное количеству юзеров до данного прохода цикла
            $nulls = [];
            for($i = $k - 1; $i >= 0; $i--)
                $nulls[] = "null";

            $nulls[] = $timeInHour;
            $data['series'][] = ['name'=>$name, "data"=> $nulls];
        }
    }

    /**
     * Получаем полностью расчитаное время для всех проектов, чтобы потом просто подставить в график
     * Get fully calculated time for all projects, then to simply substitute the schedule
     * @param $data
     */
    private function _getTimeForAllUser(&$data)
    {
        //получаем все проекты, к которым я прикреплен и которыя я создал
        $myProjects = $this->_getProject($data['idUser']);
        if(!empty($myProjects))
        {
            //получаем все id юзеров, которые есть в моих проектах
            $allIdTeam[0] = [0=>$data['idUser']];
            foreach($myProjects as $project)
            {
                foreach(explode(",", $project['team_ids']) as $idOneUser)
                {
                    //если это мой ид, то заносим в мою ячейку ид проекта
                    if($idOneUser == $data['idUser'])
                        $allIdTeam[0]['id_project'][] = $project['id_project'];
                    else
                    {
                        //если в массиве уже есть такой юзер, то в $check[0] будет true
                        foreach($allIdTeam as $k=>$team)
                        {
                            if($team[0] == $idOneUser)
                            {
                                $check = [0=>true, 1=>$k];
                                break;
                            }
                            else
                                $check = [0=>false];
                        }

                        //если есть в массиве ищущийся юзер
                        if($check[0] === true)
                            $allIdTeam[$check[1]]['id_project'][] = $project['id_project'];
                        else
                        {
                            //если этого юзера не было в массиве и он не равен МНЕ
                            if($idOneUser != $data['idUser'])
                                $allIdTeam[] = [0=>$idOneUser, 'id_project'=> [$project['id_project']]];
                        }
                    }
                }
            }

            foreach($allIdTeam as $k=>$idOneUser)
            {
                //получаем все задачи для всех юзеров, которые есть в проектах данного юзера, которые получает эту страницу
                $taskUsers[$k] = $this->chart_model->getAllTaskForMyProjects($idOneUser[0], implode(',', $idOneUser['id_project']));
                if(!empty($taskUsers[$k]))
                {
                    //скидываем все начальные значения в 0
                    $data['timeForUser'][$idOneUser[0]]['allWorkDays'] = $data['timeForUser'][$idOneUser[0]]['allWorkHours'] = $data['timeForUser'][$idOneUser[0]]['allWorkMinutes'] = $data['timeForUser'][$idOneUser[0]]['allWorkSeconds'] = 0;
                    foreach($taskUsers[$k] as $key=>$task)
                    {
                        //получаем имя
                        $data['timeForUser'][$idOneUser[0]]['name'] = $task['name']." (".$task['login'].")";
                        //получаем сколько часов в день работает данный юзер
                        $data['timeForUser'][$idOneUser[0]]['hoursInDayToWork'] = $task['hoursInDayToWork'];
                        //получаем сколько дней, часов, минут, секунд он выполнял полученные задания
                        $this->_computeTimeForEnd($taskUsers[$k][$key], $data);

                        //считаем сколько всего было затрачено времени на выполнение всех задач
                        $data['timeForUser'][$idOneUser[0]]['allWorkDays'] += $taskUsers[$k][$key]['howWorkDay'];

                        $data['timeForUser'][$idOneUser[0]]['allWorkHours'] += $taskUsers[$k][$key]['howHour'];
                        if($data['timeForUser'][$idOneUser[0]]['allWorkHours'] >= $taskUsers[$k][$key]['hoursInDayToWork'])
                        {
                            $data['timeForUser'][$idOneUser[0]]['allWorkDays']++;
                            $data['timeForUser'][$idOneUser[0]]['allWorkHours'] -= $data['hoursInDayToWork'];
                        }

                        $data['timeForUser'][$idOneUser[0]]['allWorkMinutes'] += $taskUsers[$k][$key]['howMinute'];
                        if($data['timeForUser'][$idOneUser[0]]['allWorkMinutes'] >= 60)
                        {
                            $data['timeForUser'][$idOneUser[0]]['allWorkHours']++;
                            $data['timeForUser'][$idOneUser[0]]['allWorkMinutes'] -= 60;
                        }

                        $data['timeForUser'][$idOneUser[0]]['allWorkSeconds'] += $taskUsers[$k][$key]['howSecond'];
                        if($data['timeForUser'][$idOneUser[0]]['allWorkSeconds'] >= 60)
                        {
                            $data['timeForUser'][$idOneUser[0]]['allWorkMinutes']++;
                            $data['timeForUser'][$idOneUser[0]]['allWorkSeconds'] -= 60;
                        }
                    }

                    //высчитываем количество времени в часах, которое было потрачено на все выполненые задания в полученных проектах
                    $data['timeForUser'][$idOneUser[0]]['allTime'] = ($data['timeForUser'][$idOneUser[0]]['allWorkDays'] * $data['timeForUser'][$idOneUser[0]]['hoursInDayToWork']) + $data['timeForUser'][$idOneUser[0]]['allWorkHours'] + $data['timeForUser'][$idOneUser[0]]['allWorkMinutes']/60 + $data['timeForUser'][$idOneUser[0]]['allWorkSeconds']/3600;
                    //округляем до 3 цифр после запятой
                    $data['timeForUser'][$idOneUser[0]]['allTime'] = round($data['timeForUser'][$idOneUser[0]]['allTime'], 3);

                    //по порядку вставляем null для правильного отображения графика
                    $this->_addNullsToSeries($k, $data['timeForUser'][$idOneUser[0]]['name'], $data['timeForUser'][$idOneUser[0]]['allTime'], $data);

                    $temp = "";
                    if(!isset($data['allTimesUser']))
                        $data['allTimesUser'] = [];

                    //заносим ответ в массив, в виде "1д. 4ч. 3м. 23сек."
                    if( $data['timeForUser'][$idOneUser[0]]['allWorkDays'] > 0)
                        $temp = $data['timeForUser'][$idOneUser[0]]['allWorkDays'].$data['task_controller'][19]." ";

                    if($data['timeForUser'][$idOneUser[0]]['allWorkHours'] > 0)
                        $temp .= $data['timeForUser'][$idOneUser[0]]['allWorkHours'].$data['task_controller'][18]." ";

                    if($data['timeForUser'][$idOneUser[0]]['allWorkMinutes'] > 0)
                        $temp .= $data['timeForUser'][$idOneUser[0]]['allWorkMinutes'].$data['task_controller'][17]." ";

                    if($data['timeForUser'][$idOneUser[0]]['allWorkSeconds'] > 0)
                        $temp .= $data['timeForUser'][$idOneUser[0]]['allWorkSeconds'].$data['task_controller'][15];

                    $data['allTimesUser'][$idOneUser[0]] = $temp;
                }
                //если у юзера нет задач
                else
                {
                    //получаем логин и имя юзера
                    $query = $this->common_model->getResult('users', 'id_user', $idOneUser[0], 'row_array', 'name, login');
                    $data['timeForUser'][$idOneUser[0]]['name'] = $query['name']." (".$query['login'].")";

                    //по порядку вставляем null для правильного отображения графика
                    $this->_addNullsToSeries($k, $data['timeForUser'][$idOneUser[0]]['name'], 0, $data);

                    //говорим что юзер потратил на все проекты 0 секунд.
                    $data['allTimesUser'][$idOneUser[0]] = "0 ".$data['task_controller'][15];
                }
            }

            //конвертируем в json
            $this->_jsonConvert($data, ['series']);
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
     * Делал эту функцию за несколько дней перед армией!!! ОСТОРОЖНО - ГОВНОКОД
     * I make this function a few days before the army !!! CAUTION -  Govno Code (very poor quality of the code - It is the Russian word)
     *
     * В этой функции сразу достаются все задачи для расчета времени, затраченного на все проекты и для расчетов количества задач с сортировкой по сложности, приоритету.
     * In this function just gets all of the tasks for the calculation of time spent on all projects and for the calculation of the number of tasks sorted by difficulty priority.
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

        //получаем данные о юзере для отображения графика в 3д или в 2д
        $data['additionalInfoUser']  = $this->common_model->getResult('users', 'id_user', $data['idUser'], 'row_array', 'showOrNot3DChars, showOrNotExportChars');

        //получаем полностью расчитаное время для всех проектов, чтобы потом просто подставить в график
        $this->_getTimeForAllUser($data);

        //получаем все выполненые мои задания с названием проекта, к которому они принадлежат
        $this->chart_model->forMyTaskJoin();
        $allTasks = $this->common_model->getResult('task', 'task.performer_id', $data['idUser'], 'result_array', 'task.project_id, task.complexity_id, task.priority_id, projects.title, task.status, complexity.name_complexity_'.$data['segment'].", priority.title_".$data['segment']);

        //расчитываем количество задач с сортировкой по сложности и приоритету (иногда в функциях я использую сокращение "CP" - ComplexityPriority)
        $this->_getMyCharts($data, $allTasks);
        //получаем переведенные слова для графиков (иногда в функциях я использую сокращение "CP" - ComplexityPriority)
        $this->_getLangForCPChar($data);


        $this->display_lib->display($data, $config['pathToViewDir']);
    }

    /**
     * Проверяем на существование определенных ячеек массива и складываем все данные в один массив
     * Check for the existence of certain cells of the array and add up all the data in one array
     * @param $data
     * @param $whatSort - тут данные о сложности или о приоритете
     * @param string $nameArray - какая ячейка нужна из массива $whatSort
     * @param string $name - в какой ячейке лежит название данной сложности или приоритета
     * @param array $fArray - массив для круглых графиков
     * @param array $sArray - массив для квадратных графиков
     * @param array $replace - заменяем название ячеек на то, что в $replace[0] и $replace[1]
     */
    private function _sortTask(&$data, $whatSort, $nameArray = "id_priority", $name = "title_", $fArray = [], $sArray = [], $replace = [])
    {
        $i = 0;
        //сортировка по сложности
        foreach($whatSort as $k=>$one)
        {
            //проверяем, существуют ли данные, а если нет, то дописываем их
            $this->_checkExist($data, $one[$nameArray], false, $fArray);
            $this->_checkExist($data, $one[$nameArray], true, $sArray);

            //получаем название
            $tmpName = $one[$name.$data['segment']];
            $tempArray = [
                ['%r%JsAll', 'seriesForJs%r2%All'],
                ['%r%JsAllComplete', 'seriesForJs%r2%AllComplete'],
                ['%r%JsAllNeedDo', 'seriesForJs%r2%AllNeedDo']
            ];

            //заменяем название ячеек, для дальнейшей работы
            foreach($tempArray as $key=>$aArray)
            {
                $tempArray[$key][0] = preg_replace("/%r%/iu", $replace[0], $aArray[0]);
                $tempArray[$key][1] = preg_replace("/%r2%/iu", $replace[1], $aArray[1]);
            }

            //объединяем все данные для каждого графика в один массив
            $this->_concatDataCirckle($data, $tmpName, $one[$nameArray], $tempArray, $i);

            $i++;
        }
    }

    /**
     * Расчитываем количество задач с сортировкой по сложности и приоритету (иногда в функциях я использую сокращение "CP" - ComplexityPriority)\
     * We expect the number of tasks sorted by priority and complexity (sometimes in the functions I use the abbreviation "CP" - ComplexityPriority)
     * @param $data
     * @param $allTasks
     */
    private function _getMyCharts(&$data, $allTasks)
    {
        //названия сложностей
        $complexity = $this->common_model->getResult('complexity', '', '', 'result_array', 'id_complexity, name_complexity_'.$data['segment'], 'id_complexity', 'asc');
        //названия приоритетов
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

            //Проверяем на существование определенных ячеек массива и складываем все данные в один массив
            $this->_sortTask($data, $complexity, 'id_complexity', 'name_complexity_', ["complexityJsAll", "complexityJsAllComplete", "complexityJsAllNeedDo"], ['complexityJs', 'complexityJsComplete', 'complexityJsNeedDo'], ['complexity', 'Complexity']);
            $this->_sortTask($data, $priority, 'id_priority', 'title_', ['priorityJsAll', 'priorityJsAllComplete', 'priorityJsAllNeedDo'], ['priorityJs', 'priorityJsComplete', 'priorityJsNeedDo'], ['priority', 'Priority']);

            //получаем название проектов, для каждого из квадратных графиков
            $this->_nameForChartSquare($data);
            //объединяем все в 1 массив
            $this->_concatDataSquare($data, ['complexity', 'Complexity'], [$complexity, 'name_complexity_'.$data['segment']]);

            //получаем название проектов, для каждого из квадратных графиков
            $this->_nameForChartSquare($data, ['priorityJs', 'priorityJsComplete', 'priorityJsNeedDo']);
            //объединяем все в 1 массив
            $this->_concatDataSquare($data, ['priority', 'Priority'], [$priority, 'title_'.$data['segment']]);



            //преобразуем в строку, нужные нам данные (круглые графики)
            $this->_jsonConvert($data, ['seriesForJsPriorityAll', 'seriesForJsPriorityAllComplete', 'seriesForJsPriorityAllNeedDo', 'seriesForJsComplexityAll', 'seriesForJsComplexityAllComplete', 'seriesForJsComplexityAllNeedDo']);
            //данные о квадратных графиках складываем в строку
            $this->_jsonConvert($data, ['seriesForJsComplexityProject', 'seriesForJsComplexityProjectComplete', 'seriesForJsComplexityProjectNeedDo', 'seriesForJsPriorityProject', 'seriesForJsPriorityProjectComplete', 'seriesForJsPriorityProjectNeedDo']);
            //приводим названия проектов к строке
            $this->_jsonConvert($data, ['titleForJsPriorityProject', 'titleForJsPriorityProjectComplete', 'titleForJsPriorityProjectNeedDo', 'titleForJsComplexityProject', 'titleForJsComplexityProjectComplete', 'titleForJsComplexityProjectNeedDo']);
        }
        else
            $data['notTask'] = true;
    }

}