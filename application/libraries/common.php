<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common
{
    /**
     * @var - В переменной лежит инстанс
     * The variable is an instance
     */
    private $CI_in;
    /**
     * @var - все начальные данные будут тут
     */
    private $data = [];

    /**
     * Получаем инстанс
     * We get the instance
     */
    public function __construct()
    {
        $this->CI_in =& get_instance();
        $this->CI_in->load->library('language/lang_controller');

        //получаем язык страницы (ru|en etc)
        $lang = $this->getInfoSegment();
        //получаем все слова на нужном нам языке
        $this->data = $this->CI_in->lang_controller->getLang($lang);
        $this->data['segment'] = $lang;
        //адрес урл для ссылок с уже вписанным сегментом
        $this->data['startUrl'] = base_url().$this->data['segment'];
    }


    /**
     * Получаем куки с текстом и ошибкой, и если они не пусты, то в итоге выводим их сразу на экране.
     * Get the cookie text and error, and if they are not empty, it is a way to display them all at once on the screen.
     * @return array
     */
    public function getCookie()
    {
        //получаем логин из сессии
        $session_user = $this->clear($this->CI_in->session->userdata('session_user'));
        $session_user = explode('|', $session_user);
        if(count($session_user) == 3)
            $this->data['login'] = (strlen($session_user[2]) > 20 || $session_user[2] == '') ? $this->data['common_library'][0] : $session_user[2];
        else
        {
            //получаем логин если он есть в куках
            $this->data['login'] = $this->clear($this->CI_in->input->cookie('login', true));
            if($this->data['login'] != '')
                $this->data['login'] = (strlen($this->data['login']) > 20) ? $this->data['common_library'][0] : $this->data['login'];
        }

        //куки об ошибках или просто сообщения
        $this->data['error'] = $this->clear($this->CI_in->input->cookie('error', true));
        $this->data['text'] = $this->clear($this->CI_in->input->cookie('text', true));
        if($this->data['text'] != '')
            $this->data['error'] = $this->data['text'];

        delete_cookie('error');
        delete_cookie('text');
        /**
         * В этом значении находится часть класса (чтобы показывать нужным цветом ошибки или успешные операции) для bootstrap
         */
        $this->data['status_text'] = $this->clear($this->CI_in->input->cookie('status_text', true));
        delete_cookie('status_text');
        switch($this->data['status_text'])
        {
            case 'danger':case 'success':case 'info':case 'alert':case 'primary':case 'default':case 'warning': break;
            default:
                $this->data['status_text'] = 'danger';
        }

        return $this->data;
    }

    /**
     * Перекидываем на страницу любую, с добавлением куки (название ошибки, статуса или прочее)
     * Spread to any page, with the addition of the cookie (the name of the error status, or other)
     *
     * @$title - текст
     * @$url - куда кидаем
     */
    public function redirect_to($url = '', $title = '', $name_cookie = 'error', $status = 'danger')
    {
        set_cookie($name_cookie,$title, 2);
        set_cookie('status_text',$status, 2);
        redirect(base_url().$this->data['segment']."/".$url);
    }

    /**
     * Епт, удаляем куки..
     * EPT, delete cookies ..
     * @param bool $check
     * @param string $url
     * @param string $title
     * @param string $name_cookie
     * @param string $status
     * @return string
     */
    public function dropCookie($check = false, $url = '', $title = '', $name_cookie = 'error', $status = 'danger')
    {
        delete_cookie('login');
        delete_cookie('chech_user');
        $this->CI_in->session->unset_userdata('session_user');

        if($check === true)
            $this->redirect_to($url, $title, $name_cookie, $status);

        return $title;
    }

    /**
     * Общая функция для проверки авторизации. Если это аякс, то редирект не делать
     * The general function for checking authorization. If it is Ajax, the redirect do
     * @param bool $ajax
     * @return bool
     */
    public function checkAuth($ajax = false, $auth_user = true)
    {
        //проверка зашел или нет юзер уже
        $check = $this->checkCookieSession($ajax);

        if($check['login'] != '' && $auth_user === false && $ajax === false)
        {
            $this->redirect_to(
                'task',
                ($check['login'] != '') ? $this->data['common_library'][1].$check['login'] : $this->data['common_library'][2]
            );
        }


        return $check;
    }


    /**
     * Проверяем куки на авторизацию юзера
     * Check the cookies on the user authorization
     */
    public function checkCookieSession($ajax = false)
    {
        $error = ['title_error'=>'', 'login'=>''];

        //переменная отвечает за редирект (при аяксе он нах не нужен)
        $redirect = ($ajax) ? false : true;

        $login = $check_user = '';
        $login = $this->clear($this->CI_in->input->cookie('login', TRUE));
        $check_user = $this->clear($this->CI_in->input->cookie('chech_user', true));



        //проверяем сессию
        $session_user = $this->clear($this->CI_in->session->userdata('session_user'));
        $session_user = explode('|', $session_user);
        if(count($session_user) == 3)
        {
            if($session_user[0] == 'avtoriz' && $session_user[1] == md5('02u4hash3894'))
                return ['login'=>$session_user[2], 'title_error'=>'']; // в $session_user[2] находится логин
        }
        //иначе удаляем сессию
        else
            $this->CI_in->session->unset_userdata('session_user');

        //если нужные куки существуют
        if($login != '' && is_string($login) && ($check_user == '' || sha1(md5($check_user)) == sha1(md5(1))))
        {
            //получаем ид юзера из базы
            $user = $this->CI_in->db
                                ->where('login', $login)
                                ->select('id_user')
                                ->get('users')
                                ->row_array(); //"row_array" возвращает массив для одной строки, "result_array" - возвращает массив если в выборке не одна строка

            if(!empty($user))
            {
                if(isset($user['id_user']))
                {
                    if($check_user == '')
                        set_cookie('chech_user', '1', time()+9999999999); //если ее изначально не существовало, то ставим

                    //ставим сессию
                    $this->CI_in->session->set_userdata(['session_user'=> 'avtoriz|'.md5('02u4hash3894').'|'.$login]);
                    return ['login'=>$login, 'title_error'=>''];
                }
                else
                    $error['title_error'] = $this->dropCookie($redirect, '', $this->data['common_library'][4]);
            }
            else
                $error['title_error'] = $this->dropCookie($redirect, '', $this->data['common_library'][5]);
        }

        return $error;
    }


    /**
     * Функция находит в тексте урл и преобразует их в ссылки
     * The function is the text URL and converts them into links
     * @param string $text
     * @return mixed
     */
    public function changeTextToLink($text = '')
    {
        $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" target='_blank'>$3</a>", $text);
        $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" target='_blank'>$3</a>", $text);
        return $text;
    }

    /**
     * Подставляем нужный язык ссылок в навигации
     * Substitute the desired language in the navigation links
     * @param $config
     */
    public function getConfigPerPage(&$config)
    {
        $config['first_link'] = $this->data['languages_desc'][0]['perPageLang'][$this->data['segment']]['first_link'];
        $config['last_link'] = $this->data['languages_desc'][0]['perPageLang'][$this->data['segment']]['last_link'];
    }

    /**
     * Приватная функция замены каждого символа на латиницу.
	 * Если уже латиница, то оставляет как есть (всякую хуйню тоже отсекает)
	 *
     * Privacy replacement function of each character in the Roman alphabet.
     * If you already Latin, then leave as is (also cuts off all garbage)
     *
	 * @$str - строка на русском или английском языке
	 */
    public function sms_translit($str = '')
    {
        $translit = array(
            " "=>'-',"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
            "Д"=>"D","Е"=>"E", "Ё"=>"E", "Ж"=>"J","З"=>"Z","И"=>"I",
            "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
            "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
            "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
            "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
            "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
            "в"=>"v","г"=>"g","д"=>"d","е"=>"e", "ё"=>"e","ж"=>"j",
            "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
            "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
            "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
            "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
            "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya","."=> "",
            "/"=> "_",","=>"_", "-"=>"_","("=>"",")"=>"","["=>"",
            "]"=>"", "="=>"_","+"=>"_","*"=>"","?"=>"","\""=>"",
            "'"=>"","&"=>"", "%"=>"","#"=>"","@"=>"","!"=>"",
            ";"=>"","№"=>"","^"=>"",":"=>"","~"=>"","\\"=>""
        );
        return strtr($str,$translit);
    }


    /**
     * чистит данные
     * clean data
     * @param string $input
     * @return string
     */
    public function clear($input = '')
    {
        return trim(strip_tags(stripslashes(mysql_real_escape_string(htmlspecialchars($input)))));
    }

    /**
     * Получаем часть url, которая идет после указателя языка, чтобы человека перекинуть на ту же страницу
     * We get a piece of url, which comes after the sign language that would throw the man to the same page
     * @param bool $test - нужна, пока работаю на разных локалках
     */
    public function getLangUrl($test = false)
    {
        $this->data['currentUrl'] = $_SERVER['REQUEST_URI'];
        if(trim($this->data['currentUrl']) != '')
        {
            $this->data['currentUrl'] = preg_replace('/'.$this->data['segment'].'\/|\/'.$this->data['segment'].'/i', '', $this->data['currentUrl']);

            //FIXME УДАЛИТЬ ДОМА ЭТОТ ИФ
            /*if($test === true)
                $this->data['currentUrl'] = preg_replace('/time.log\/|\/time.log|time.log/i', '', $this->data['currentUrl']);*/

            $url = explode('/', $this->data['currentUrl']);

            foreach($url as $k=>$v)
                if(trim($v) == '')
                    unset($url[$k]);

            if(!empty($url))
                $this->data['currentUrl'] = implode('/', $url);
            else
                $this->data['currentUrl'] = '';
        }
    }

    /**
     * Функция первоначальных проверок (на регистрацию и получение кук)
     * The function of initial checks (for registration and reception of cookies)
     *
     * @param string $controller_lang
     * @param int $name_lang
     * @param string $folder
     * @param bool $auth_user - изначально ставим true (вошел в ЛК), но где нужно - изменяем на false (типо не авторизирован) для показа некоторых частей в виде
     * @param bool $ajax - если false, то разрешен редирект, если true, то возвращается сообщение ошибки
     * @param array $what_replace - в массиве указан паттерн замены. [0] - какую ячейку заменить, [1] - на что заменить, [2] - какой паттерн искать для замены
     * @return array
     */
    public function initApp($controller_lang = 'welcome_controller', $name_lang = 0,  $folder = 'login', $auth_user = true, $ajax = false, $what_replace = ['pattern'=>''])
    {
        //тут получаем нужный нам кусок url
        //FIXME УБРАТЬ ДОМА ЭТОТ ПАРАМЕТР
        //$this->getLangUrl(true);
        $this->getLangUrl();

        //название получаем из языкового файла
        $this->data['title'] = $this->data[$controller_lang][$name_lang];

        //Получаем куки с текстом и ошибкой, и если они не пусты, то в итоге выводим их сразу на экране.
        $this->data = $this->getCookie();

        if(is_array($what_replace['pattern']))
            $this->data[$what_replace['pattern'][0]] = preg_replace('/'.$what_replace['pattern'][2].'/i', $this->data[$what_replace['pattern'][1]], $this->data[$what_replace['pattern'][0]]);


        //если это false, то человек не вошел в аккаунт
        $this->data['auth_user'] = $auth_user;

        //проверка зашел или нет юзер уже
        $this->data['checkAuth'] = $this->checkAuth($ajax, $auth_user);

        $this->setDefaultLang($this->data['segment']);

        if(isset($this->data['login']))
        {
            if(trim($this->data['login']) != "")
            {
                $q = $this->CI_in->db->where('login', $this->data['login'])->select('id_user, status, count_projects, hoursInDayToWork')->get('users')->row_array();
                $this->data['idUser'] = (!empty($q)) ? $q['id_user'] : 0;
                $this->data['statusUser'] = (!empty($q)) ? $q['status'] : 0;
                $this->data['count_projectsUser'] = (!empty($q)) ? $q['count_projects'] : 0;
                $this->data['hoursInDayToWork'] = $q['hoursInDayToWork'];
            }
        }

        //если есть ошибку, то даем понять, что есть ошибка
        if($this->data['error'] != '')
            $this->data['return_notification'] = true;




        return $this->data;
    }

    /**
     * Просто объединил общую инициализацию еще раз
     * Just combine general initialization again
     *
     * @param array $config
     * @return array
     */
    public function allInit($config = [])
    {
        $folderView = $config['pathToViewDir'];
        $this->data = $this->initApp($config['langArray_1'], $config['langArray_2'], $folderView, $config['authUser'], $config['noRedirect'], (isset($config['pattern'])) ? $config['pattern'] : ['pattern'=>'']);
        if(isset($this->data['return_notification']))
            return $this->data;


        /**
         * Если сказано что юзер авторизирован, а логин пустой
         */
        if($config['authUser'] === true)
            if($this->data['checkAuth']['login'] == '')
                $this->data['checkAuth']['title_error'] = $this->dropCookie(($config['noRedirect']) ? false : true, '', ($this->data['checkAuth']['title_error'] != '') ? $this->data['checkAuth']['title_error'] : $this->data['languages_desc'][0]['errorAuth'][$this->data['segment']]);


        return $this->data;
    }


    /**
     * Устанавливаем язык, который отображает ошибки и прочие сообщения (это стандартные папки CodeIgniter)
     * Set the language that displays errors and other messages (this is the default folders CodeIgniter)
     * @param string $segment
     */
    public function setDefaultLang($segment = 'ru')
    {
        $lang = 'russian';
        switch($segment)
        {
            case 'en': $lang = 'english'; break;
            //add next lang, who name is name folder into "application/language"
        }

        $this->CI_in->config->set_item('language', $lang);
    }


    /**
     * Получаем язык из поисковой строки браузера
     * We get the language from the browser search box
     * @return string
     */
    public function getInfoSegment()
    {

        $segment = $this->clear($this->CI_in->uri->segment(1));
        switch($segment)
        {
            case 'ru':
                //Insert here languages
                //case 'de':
            case 'en':
                break;
            default:
                $segment = 'ru';
        }
        return $segment;
    }


    /**
     * Проверяем пришедшие данные, в основном для AJAX
     * It examines the data mainly for AJAX
     *
     * @param $data - данные (data)
     * @param bool $is_int - проверять как число (check the number)
     * @param bool $is_string - проверять как строку (check as a string)
     * @param bool $unsigned - проверять ли на меньше чем 0 (check whether less than 0)
     * @param bool $zero - проверять ли на равенство 0 (check whether the equality 0)
     * @return bool
     */
    public function checkData($data, $is_int = false, $is_string = false, $unsigned = true, $zero = true)
    {
        if($is_string === true)
        {
            if(!is_string(trim($data)))
                return false;
        }
        elseif($is_int === true)
        {
            $data = intval($data);
            if(!is_numeric($data))
                return false;

            if($zero === true)
            {
                if($data == 0)
                    return false;
            }

            if($unsigned === true)
            {
                if($data < 0)
                    return false;
            }
        }

        return true;
    }

    /**
     * Функция проверки ajax запроса и проверки всех его параметров, если что то не так, то возвращаем error
     * Function check ajax request and checking all of its parameters, if there is something wrong, then we return error
     * @return array
     */
    public  function isAjax()
    {
        //если это аякс запрос
        if($this->CI_in->input->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest')
        {
            $data = $this->initApp('welcome_controller', 0, 'login', true, true);
            if($data['checkAuth']['title_error'] != '')
                return ['status'=>'error', 'resultTitle'=>$data['checkAuth']['title_error'], "resultText"=>$data['checkAuth']['title_error']];

            //количество всех аргументов функции
            $numargs = func_num_args();
            if($numargs <= 0)
                return ['status'=>'error', 'resultTitle'=>$data['task_views'][24], 'resultText'=>$data['task_views'][24]];

            //получаем все аргументы переданные в функцию
            $arg_list = func_get_args();
            for ($i = 0; $i < $numargs; $i++)
            {
                $postError = false;

                if(!is_array($arg_list[$i]))
                    $postError = true;
                else
                {
                    //если название в аргументе не содержиться этой надписи
                    if($arg_list[$i][0] != 'noPost')
                    {
                        //если существует такое название в post массиве
                        if(!isset($_POST[$arg_list[$i][0]]))
                            $postError = true;
                        else
                        {
                            //если это число
                            if($arg_list[$i][1] == 'int')
                            {
                                //если существует третий параметр, то не проверяем на "равно 0"
                                if(isset($arg_list[$i][2]))
                                {
                                    if(@$this->checkData($_POST[$arg_list[$i][0]], true, false, true, false) !== true)
                                        $postError = true;
                                }
                                //если нет третьего параметра, то проверяем и на "равно 0"
                                else
                                    if(@$this->checkData($_POST[$arg_list[$i][0]], true) !== true)
                                        $postError = true;
                            }
                            //если это строка
                            else
                                if(@$this->checkData($_POST[$arg_list[$i][0]], false, true) !== true)
                                    $postError = true;
                        }
                    }
                }




                if($postError === true)
                    return ['status' => 'error', 'resultTitle'=> $data['js'][0], 'resultText'=>$data['task_views'][6]];

                return ['status'=>"success", 'data'=>$data];
            }

        }
        else
            return ['status'=>'error', 'resultTitle'=>"NU NIXUYA SEBE TI CHEGO SDELAL ;)", "resultText"=>"NU NIXUYA SEBE TI CHEGO SDELAL ;)"];
    }


    /**
     * Логирование ошибок, которые не являются ошибками php
     * Logging of errors that are not errors php
     * @param $str - строка содержащая элементы для замены. Например :_class_: (string containing the elements for a replacement. For example :_class_:)
     * @param array $replace_with - то, чем будем заменять. Например :_class_: заменится константой __CLASS__ (what will replace. For example :_class_: replace the constant __CLASS__)
     * @param array $to_replace - все элементы, которые должны заменить (All items should be replaced)
     * @param $json_encode_data - это не валидные данные, которые пытались передать серверу. (this is not valid data that tried to pass the server.)
     */
    public function errorCodeLog($str, $replace_with = [], $to_replace = [':_class_:', ':_method_:', ':_line_:'], $json_encode_data = null)
    {
        if($to_replace == 'same')
            $to_replace = [':_class_:', ':_method_:', ':_line_:'];

        $strError = "\n --> " . strtr($str, array_combine($to_replace, $replace_with)) . "\n";
        if(!is_null($json_encode_data))
            $strError .= " --> Invalid data: ".json_encode($json_encode_data)."\n\r";

        log_message('error', $strError);
    }


    /**
     * (FOR AJAX)
     * Если возникает какая то ошибка, то передаем ее в json
     * If there is any mistake, transfer it in json
     * @param $data
     * @param string $title - это либо сама ошибка, либо массив с готовыми ошибками (that either a mistake, or the massif with ready mistakes)
     * @param bool $arrayOrNot - если true, то $title должен быть массивом (if true, $title has to be an array)
     * @param array $additionalParam - дополнительные параметры, которые нужно передать (additional parameters which need to be transferred)
     * @return bool
     * TODO сделать запись в файл, в котором будет содержаться инфа об ошибке, которая высвечивалась людям. Типо свой лог
     */
    public function returnResponse(&$data, $title = '', $arrayOrNot = false, $additionalParam = [])
    {
        if($arrayOrNot === false)
            $errorResponse = ['status' => 'error', 'resultTitle'=> $data['languages_desc'][0]['titleError'][$data['segment']], 'resultText'=>$title];
        else
            $errorResponse = $title;

        if(!empty($additionalParam))
            $errorResponse['additionalParam'] = $additionalParam;

        echo json_encode($errorResponse);
        return true;
    }

}
?>