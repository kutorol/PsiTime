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

        $this->load->model('chart_model');
        $this->load->model('common_model');

        //получаем все мои задания
        $countComplexity = $this->chart_model->getCountComplexity($data['idUser'], $data['segment']);
        //названия сложности задания
        $complexity = $this->common_model->getResult('complexity', '', '', 'result_array', 'id_complexity, name_complexity_'.$data['segment'], 'id_complexity', 'asc');

        //получаем все мои задания с названием проекта, к которому они принадлежат
        $this->db->join("projects", "projects.id_project = task.project_id", "left");
        $allTasks = $this->common_model->getResult('task', 'task.performer_id', $data['idUser'], 'result_array', 'task.project_id, task.complexity_id, projects.title, task.status');


        //цвета для графика. 1 - легкая, 2 - средняя, 3 - сложная задача
        $data['colorsForJsComplexity'] = "'#449D44', '#EC971F', '#C9302C'";

        //если есть вообще задания
        if(!empty($countComplexity))
        {
            foreach($countComplexity as $k=>$task)
            {
                if(!isset($data['complexityJsAll']))
                    $data['complexityJsAll'] = [1 => 0, 2=> 0, 3=> 0];

                $data['complexityJsAll'][$task['complexity_id']]++;

                //если задача выполнена
                if($task['status'] == 2)
                {
                    if(!isset($data['complexityJsAllComplete']))
                        $data['complexityJsAllComplete'] = [1 => 0, 2=> 0, 3=> 0];

                    $data['complexityJsAllComplete'][$task['complexity_id']]++;
                }
                else
                {
                    if(!isset($data['complexityJsAllNeedDo']))
                        $data['complexityJsAllNeedDo'] = [1 => 0, 2=> 0, 3=> 0];

                    $data['complexityJsAllNeedDo'][$task['complexity_id']]++;
                }
            }

            $i = 0;
            $data['seriesForJsComplexityAll'] = "";
            $data['seriesForJsComplexityAllComplete'] = "";
            $data['seriesForJsComplexityAllNeedDo'] = "";
            foreach($complexity as $k=>$count)
            {
                //если i = 0, то подставляем дополнительные параметры, которые выдвигают часть графика
                $additionalParam = "";
                if($i == 0)
                    $additionalParam = ", sliced: true, selected: true";

                //заносим json массив с данными, чтобы потом вставить в highcharts
                $data['seriesForJsComplexityAll'] .= "{name: '".$count['name_complexity_'.$data['segment']]."', y: ".$data['complexityJsAll'][$count['id_complexity']].$additionalParam."},";
                $data['seriesForJsComplexityAllComplete'] .= "{name: '".$count['name_complexity_'.$data['segment']]."', y: ".$data['complexityJsAllComplete'][$count['id_complexity']].$additionalParam."},";
                $data['seriesForJsComplexityAllNeedDo'] .= "{name: '".$count['name_complexity_'.$data['segment']]."', y: ".$data['complexityJsAllNeedDo'][$count['id_complexity']].$additionalParam."},";

                $i++;
            }

            //удаляем последний символ, которой равен запятой. !!! Можно вроде и не удалять !!!
            $data['seriesForJsComplexityAll'] = mb_substr($data['seriesForJsComplexityAll'], 0, -1, 'utf8');
            //удаляем последний символ, которой равен запятой. !!! Можно вроде и не удалять !!!
            $data['seriesForJsComplexityAllComplete'] = mb_substr($data['seriesForJsComplexityAllComplete'], 0, -1, 'utf8');
            $data['seriesForJsComplexityAllNeedDo'] = mb_substr($data['seriesForJsComplexityAllNeedDo'], 0, -1, 'utf8');
        }
        else
            $data['countComplexity'] = false;


        //если есть все задачи
        if(!empty($allTasks))
        {
            foreach($allTasks as $k=>$task)
            {
                if(!isset($data['complexityJs'][$task['project_id']]))
                    $data['complexityJs'][$task['project_id']] = [1 => 0, 2=> 0, 3=> 0, 'title'=> ['projectTitle' => $task['title']] ];

                $data['complexityJs'][$task['project_id']][$task['complexity_id']]++;

                //если задача выполнена
                if($task['status'] == 2)
                {
                    if(!isset($data['complexityJsComplete'][$task['project_id']]))
                        $data['complexityJsComplete'][$task['project_id']] = [1 => 0, 2=> 0, 3=> 0, 'title'=> ['projectTitle' => $task['title']] ];

                    $data['complexityJsComplete'][$task['project_id']][$task['complexity_id']]++;
                }
                else
                {
                    if(!isset($data['complexityJsNeedDo'][$task['project_id']]))
                        $data['complexityJsNeedDo'][$task['project_id']] = [1 => 0, 2=> 0, 3=> 0, 'title'=> ['projectTitle' => $task['title']] ];

                    $data['complexityJsNeedDo'][$task['project_id']][$task['complexity_id']]++;
                }
            }

            $i = 0;
            $data['seriesForJsComplexityProject'] = "";
            $data['seriesForJsComplexityProjectComplete'] = "";
            $data['seriesForJsComplexityProjectNeedDo'] = "";
            foreach($data['complexityJs'] as $k=>$num)
            {

                if($num[1] == 0) $data['complexityJs'][$k][1] = 'null';
                if($num[2] == 0) $data['complexityJs'][$k][2] = 'null';
                if($num[3] == 0) $data['complexityJs'][$k][3] = 'null';


                $tmp['easy'][$i] = $data['complexityJs'][$k][1];
                $tmp['middle'][$i] = $data['complexityJs'][$k][2];
                $tmp['hard'][$i] = $data['complexityJs'][$k][3];

                $tmp['title'][$i] = $num['title']['projectTitle'];
                $i++;
            }

            $i = 0;
            foreach($data['complexityJsComplete'] as $k=>$num)
            {
                if($num[1] == 0) $data['complexityJsComplete'][$k][1] = 'null';
                if($num[2] == 0) $data['complexityJsComplete'][$k][2] = 'null';
                if($num[3] == 0) $data['complexityJsComplete'][$k][3] = 'null';


                $tmpComplete['easy'][$i] = $data['complexityJsComplete'][$k][1];
                $tmpComplete['middle'][$i] = $data['complexityJsComplete'][$k][2];
                $tmpComplete['hard'][$i] = $data['complexityJsComplete'][$k][3];

                $tmpComplete['title'][$i] = $num['title']['projectTitle'];
                $i++;
            }

            $i = 0;
            foreach($data['complexityJsNeedDo'] as $k=>$num)
            {
                if($num[1] == 0) $data['complexityJsNeedDo'][$k][1] = 'null';
                if($num[2] == 0) $data['complexityJsNeedDo'][$k][2] = 'null';
                if($num[3] == 0) $data['complexityJsNeedDo'][$k][3] = 'null';


                $tmpNeedDo['easy'][$i] = $data['complexityJsNeedDo'][$k][1];
                $tmpNeedDo['middle'][$i] = $data['complexityJsNeedDo'][$k][2];
                $tmpNeedDo['hard'][$i] = $data['complexityJsNeedDo'][$k][3];

                $tmpNeedDo['title'][$i] = $num['title']['projectTitle'];
                $i++;
            }

            //если вы добавили в базу еще несколько штук "сложностей" для задач, то тут придется еще написать
            $data['seriesForJsComplexityProject'] = "{name: '".$complexity[0]['name_complexity_'.$data['segment']]."',  data: [".implode(', ', $tmp['easy'])."]}, {name: '".$complexity[1]['name_complexity_'.$data['segment']]."', data: [".implode(', ', $tmp['middle'])."]},{name: '".$complexity[2]['name_complexity_'.$data['segment']]."', data: [".implode(', ', $tmp['hard'])."] }";
            //записываем названия проектов в виде: 'проект 1', 'проект 2'
            $data['titleForJsComplexityProject'] = "'".implode("', '", $tmp['title'])."'";

            //если вы добавили в базу еще несколько штук "сложностей" для задач, то тут придется еще написать
            $data['seriesForJsComplexityProjectComplete'] = "{name: '".$complexity[0]['name_complexity_'.$data['segment']]."',  data: [".implode(', ', $tmpComplete['easy'])."]}, {name: '".$complexity[1]['name_complexity_'.$data['segment']]."', data: [".implode(', ', $tmpComplete['middle'])."]},{name: '".$complexity[2]['name_complexity_'.$data['segment']]."', data: [".implode(', ', $tmpComplete['hard'])."] }";
            //записываем названия проектов в виде: 'проект 1', 'проект 2'
            $data['titleForJsComplexityProjectComplete'] = "'".implode("', '", $tmpComplete['title'])."'";

            //если вы добавили в базу еще несколько штук "сложностей" для задач, то тут придется еще написать
            $data['seriesForJsComplexityProjectNeedDo'] = "{name: '".$complexity[0]['name_complexity_'.$data['segment']]."',  data: [".implode(', ', $tmpNeedDo['easy'])."]}, {name: '".$complexity[1]['name_complexity_'.$data['segment']]."', data: [".implode(', ', $tmpNeedDo['middle'])."]},{name: '".$complexity[2]['name_complexity_'.$data['segment']]."', data: [".implode(', ', $tmpNeedDo['hard'])."] }";
            //записываем названия проектов в виде: 'проект 1', 'проект 2'
            $data['titleForJsComplexityProjectNeedDo'] = "'".implode("', '", $tmpNeedDo['title'])."'";
        }
        else
            $data['allTasks'] = false;


































        $this->display_lib->display($data, $config['pathToViewDir']);
    }

}