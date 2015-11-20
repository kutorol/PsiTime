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

        //получаем все выполненые мной задания
        $allMyTask = $this->common_model->getResult('task', ['performer_id', 'status'], [$data['idUser'], 2], 'result_array', 'complexity_id, priority_id, project_id, pause, pause_for_complite', 'id_task');

        $q = $this->db->query("SELECT id_task,complexity_id, COUNT(*) FROM task WHERE performer_id = ".$data['idUser']." AND status = 2 GROUP BY complexity_id")->result_array();

        $data['complexity'] = $this->common_model->getResult('task', ['performer_id', 'status'], [$data['idUser'], 2]);


        if(!empty($allMyTask))
        {
            //изначально все данные равны 0
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

                if(!isset($data['time']['by_project']['secondProject_'.$task['project_id']]))
                    $data['time']['by_project']['secondProject_'.$task['project_id']] = 0;

                $data['time']['by_project']['secondProject_'.$task['project_id']] += $allMyTask[$k]['howWorkDay'] * ($data['hoursInDayToWork'] * 3600);
                $data['time']['by_project']['secondProject_'.$task['project_id']] += $allMyTask[$k]['howHour'] * 3600;
                $data['time']['by_project']['secondProject_'.$task['project_id']] += $allMyTask[$k]['howMinute'] * 60;
                $data['time']['by_project']['secondProject_'.$task['project_id']] += $allMyTask[$k]['howSecond'];
            }




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


        echo "<pre>";
        print_r($data['myTime']['allWorkDays']." d <br>");
        print_r($data['myTime']['allWorkHours']." h <br>");
        print_r($data['myTime']['allWorkMinutes']." m <br>");
        print_r($data['myTime']['allWorkSeconds']." s <br>");
        echo "</pre>";

        $data['hours__'] = $data['myTime']['allWorkDays'] * 24 + $data['myTime']['allWorkHours'] + $data['myTime']['allWorkMinutes']/60 + $data['myTime']['allWorkSeconds']/3600;
        $data['hours__'] = round($data['hours__'],2) ;
        echo $data['hours__']." h<br>";
        //$timeline = range(0, $hours); // [1, 2, 3, ... $hours]







































        $this->display_lib->display($data, $config['pathToViewDir'], $config['name_file']);
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

        $data['hoursInDayToWork'] = (intval($data['hoursInDayToWork']) > 0) ? $data['hoursInDayToWork'] : 8;
        //получаем количество рабочих часов в секундах
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

        //в этой ячейки будет содержаться ответ - сколько времени было потрачено на задачу
        $data['myTimeCompliteForTask'] = "";

        $howHour = floor($howHour);
        $howMinute = floor($howMinute);
        $howSecond = round($howSecond);

        /**
         * Следующие три проверки нужны для того, что иногда высвечивается ответ в формате 1 час 60 секунд, но по факту это должно быть 1 час 1 минута
         * The following three checks are necessary for this purpose that the answer in a format is sometimes highlighted 1 hour 60 seconds, but upon it has to be 1 hour 1 minute
         */
        //если часов равно количеству часов в рабочем дне, то увеличиваем день, а часы обнуляем
        if($howHour == $data['hoursInDayToWork'])
        {
            $howWorkDay++;
            $howHour = 0;
        }

        //если минут равно 1 часу, то увеличиваем часы, а минуты обнуляем
        if($howMinute == 60)
        {
            $howHour++;
            $howMinute = 0;
        }

        //если количество секунд равно 1 минуте, то увеличиваем минуты, а секунды обнуляем
        if($howSecond == 60)
        {
            $howMinute++;
            $howSecond = 0;
        }


        $task['howWorkDay'] = $howWorkDay;
        $task['howHour']    = $howHour;
        $task['howMinute']  = $howMinute;
        $task['howSecond']  = $howSecond;
    }


}