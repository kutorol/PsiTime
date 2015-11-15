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

        $this->load->model('common_model');
        $data['imFromIndexFunction'] = true;
        $this->_additionalGetTask($data);



        $this->display_lib->display($data, $config['pathToViewDir']);
    }



}