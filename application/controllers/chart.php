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

            $data[$name]['allTitle'] = "'".implode("', '", $data[$name]['allTitle'])."'";
        }
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

        $this->load->model('chart_model');
        $this->load->model('common_model');


        //названия сложности задания
        $complexity = $this->common_model->getResult('complexity', '', '', 'result_array', 'id_complexity, name_complexity_'.$data['segment'], 'id_complexity', 'asc');

        //получаем все мои задания с названием проекта, к которому они принадлежат
        $this->db->join("projects", "projects.id_project = task.project_id", "left");
        $this->db->join("complexity", "complexity.id_complexity = task.complexity_id", "left");
        $allTasks = $this->common_model->getResult('task', 'task.performer_id', $data['idUser'], 'result_array', 'task.project_id, task.complexity_id, projects.title, task.status, complexity.name_complexity_'.$data['segment']);

        //цвета для графика. 1 - легкая, 2 - средняя, 3 - сложная задача
        $data['colorsForJsComplexity'] = "'#449D44', '#EC971F', '#C9302C'";

        //если есть вообще задания
        if(!empty($allTasks))
        {
            //считаем количество задач с сортировкой по сложности
            foreach($allTasks as $k=>$task)
            {
                //количество всех задач для круглых графиков
                $this->_countTaskForFilter($data, $task, $task['complexity_id']);
                //количество всех задач для квадратных графиков
                $this->_countTaskForFilter($data, $task, $task['complexity_id'], true, ['complexityJs', 'complexityJsComplete', 'complexityJsNeedDo']);
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

            //получаем название проектов, для каждого из квадратных графиков
            $this->_nameForChartSquare($data);
            $tempArray = [
                ['complexityJs', 'seriesForJsComplexityProject', 'titleForJsComplexityProject'],
                ['complexityJsComplete', 'seriesForJsComplexityProjectComplete', 'titleForJsComplexityProjectComplete'],
                ['complexityJsNeedDo', 'seriesForJsComplexityProjectNeedDo', 'titleForJsComplexityProjectNeedDo']
            ];
            //объединяем все в 1 массив
            $this->_concatDataSquare($data, $tempArray, [$complexity, 'name_complexity_'.$data['segment']]);

            //преобразуем в строку, нужные нам данные
            $this->_jsonConvert($data);
            $this->_jsonConvert($data, ['seriesForJsComplexityProject', 'seriesForJsComplexityProjectComplete', 'seriesForJsComplexityProjectNeedDo']);
        }
        else
            $data['notTask'] = true;



        $this->display_lib->display($data, $config['pathToViewDir']);
    }

}