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
class Chart_tmp extends CI_Controller {



    public function index()
    {
        $config = [
            'pathToViewDir'     =>  'chart',
            'langArray_1'       =>  'chart_controller',
            'langArray_2'       =>  0,
            'authUser'          =>  true,
            'noRedirect'        =>  false, //false - редиректим, true - возвращаем ошибку
            'name_file'         =>  'content_tmp.php'
        ];
        $data = $this->common->allInit($config);

        $this->load->model('common_model');

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

/*
        //получаем все выполненые мной задания
        $allMyTask = $this->common_model->getResult('task', ['performer_id', 'status'], [$data['idUser'], 2], 'result_array', 'complexity_id, priority_id, project_id, pause, pause_for_complite', 'id_task');

        if(!empty($allMyTask))
        {
            //изначально время, которое потрачено на все проекты данным юзером, равно 0
            $data['myTime']['allWorkDays'] = $data['myTime']['allWorkHours'] = $data['myTime']['allWorkMinutes'] = $data['myTime']['allWorkSeconds'] = 0;

            foreach($allMyTask as $k=>$task)
            {
                $this->_computeTimeForEnd($allMyTask[$k], $data);

                //считаем сколько всего было затрачено времени на выполнение всех задач
                $data['myTime']['allWorkDays'] += $allMyTask[$k]['howWorkDay'];

                $data['myTime']['allWorkHours'] += $allMyTask[$k]['howHour'];
                if($data['myTime']['allWorkHours'] >= $data['hoursInDayToWork'])
                {
                    $data['myTime']['allWorkDays']++;
                    $data['myTime']['allWorkHours'] -= $data['hoursInDayToWork'];
                }

                $data['myTime']['allWorkMinutes'] += $allMyTask[$k]['howMinute'];
                if($data['myTime']['allWorkMinutes'] >= 60)
                {
                    $data['myTime']['allWorkHours']++;
                    $data['myTime']['allWorkMinutes'] -= 60;
                }

                $data['myTime']['allWorkSeconds'] += $allMyTask[$k]['howSecond'];
                if($data['myTime']['allWorkSeconds'] >= 60)
                {
                    $data['myTime']['allWorkMinutes']++;
                    $data['myTime']['allWorkSeconds'] -= 60;
                }

                //получаем сколько времени (в секундах) потрачено на каждый проект, в котором есть выполненые задания
                if(!isset($data['time']['by_project']['secondProject_'.$task['project_id']]))
                    $data['time']['by_project']['secondProject_'.$task['project_id']] = 0;

                $data['time']['by_project']['secondProject_'.$task['project_id']] += $allMyTask[$k]['howWorkDay'] * ($data['hoursInDayToWork'] * 3600);
                $data['time']['by_project']['secondProject_'.$task['project_id']] += $allMyTask[$k]['howHour'] * 3600;
                $data['time']['by_project']['secondProject_'.$task['project_id']] += $allMyTask[$k]['howMinute'] * 60;
                $data['time']['by_project']['secondProject_'.$task['project_id']] += $allMyTask[$k]['howSecond'];
            }



            $data['myTimeCompliteForTask'] = "";
            $data['time']['allSeconds'] = 0;
            //заносим ответ в массив
            if($data['myTime']['allWorkDays'] > 0)
            {
                $data['myTimeCompliteForTask'] = $data['myTime']['allWorkDays'].$data['task_controller'][19]." ";
                $data['time']['allSeconds'] += $data['myTime']['allWorkDays'] * ($data['hoursInDayToWork'] * 3600);
            }

            if($data['myTime']['allWorkHours'] > 0)
            {
                $data['myTimeCompliteForTask'] .= $data['myTime']['allWorkHours'].$data['task_controller'][18]." ";
                $data['time']['allSeconds'] += $data['myTime']['allWorkHours'] * 3600;
            }

            if($data['myTime']['allWorkMinutes'] > 0)
            {
                $data['myTimeCompliteForTask'] .= $data['myTime']['allWorkMinutes'].$data['task_controller'][17]." ";
                $data['time']['allSeconds'] += $data['myTime']['allWorkMinutes'] * 60;
            }

            if($data['myTime']['allWorkSeconds'] > 0)
            {
                $data['myTimeCompliteForTask'] .= $data['myTime']['allWorkSeconds'].$data['task_controller'][15];
                $data['time']['allSeconds'] += $data['myTime']['allWorkSeconds'];
            }
        }
*/


        //$data['hours__'] = ($data['myTime']['allWorkDays'] *1) + $data['myTime']['allWorkHours'] + $data['myTime']['allWorkMinutes']/60 + $data['myTime']['allWorkSeconds']/3600;
        //$data['hours__'] = $data['hours__'] ;
        //echo $data['hours__']." h<br>";
        //$timeline = range(0, $hours); // [1, 2, 3, ... $hours]




































        $this->display_lib->display($data, $config['pathToViewDir'], $config['name_file']);
    }





}