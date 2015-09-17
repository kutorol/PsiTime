<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common
{
    /**
     * @var - В переменной лежит инстанс
     * The variable is an instance
     */
    private $CI_in;

    /**
     * Получаем инстанс
     * We get the instance
     */
    public function __construct()
    {
        $this->CI_in =& get_instance();
    }


    public function getCookie($data = [])
    {
        //куки об ошибках или просто сообщения
        $data['error'] = $this->clear($this->CI_in->input->cookie('error', true));
        $data['text'] = $this->clear($this->CI_in->input->cookie('text', true));
        if($data['text'] != '')
            $data['error'] = $data['text'];


        /**
         * В этом значении находится часть класса (чтобы показывать нужным цветом ошибки или успешные операции) для bootstrap
         */
        $data['status_text'] = $this->clear($this->CI_in->input->cookie('status_text', true));
        switch($data['status_text'])
        {
            case 'danger':case 'success':case 'info':case 'alert':case 'primary':case 'default':case 'warning': break;
            default:
                $data['status_text'] = 'danger';
        }

        return $data;
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
        redirect(base_url().$url);
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

        if($check)
            $this->redirect_to($url, $title, $name_cookie, $status);

        return $title;
    }

    /**
     * Общая функция для проверки авторизации. Если это аякс, то редирект не делать
     * The general function for checking authorization. If it is Ajax, the redirect do
     * @param bool $ajax
     * @return bool
     */
    public function checkAuth($ajax = false)
    {
        //проверка зашел или нет юзер уже
        $check = $this->checkCookieSession($ajax);

        if($ajax)
            return $check['check'];

        if($check['check'])
        {
            $this->redirect_to(
                'task',
                ($check['login'] != '') ? 'Вы уже зашли под логином - '.$check['login'] : 'Вы уже зашли в TimeBig'
            );
        }
    }


    /**
     * Проверяем куки на авторизацию юзера
     * Check the cookies on the user authorization
     */
    public function checkCookieSession($ajax = false)
    {
        $error = ['title_error'=>''];

        $redirect = ($ajax) ? false : true;
        $login = $check_user = '';
        $login = $this->CI_in->input->cookie('login', true);
        $login = $this->clear($login);

        $check_user = $this->CI_in->input->cookie('chech_user', true);
        $check_user = $this->clear($check_user);

        //проверяем сессию
        $session_user = $this->clear($this->CI_in->session->userdata('session_user'));
        $session_user = explode('|', $session_user);
        if(count($session_user) == 3)
        {
            if($session_user[0] == 'avtoriz' && $session_user[1] == md5('02u4hash3894'))
                return ['login'=>$session_user[2], 'check'=>true]; //в $session_user[2] находится логин
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
                    return ['login'=>$login, 'check'=>true];
                }
                else
                    $error['title_error'] = $this->dropCookie($redirect, '', 'Очень странная ошибка... Попробуйте заного авторизироваться!');
            }
            else
                $error['title_error'] = $this->dropCookie($redirect, '', 'Какие то не правильные куки! Попрошу не издеваться!');
        }
        else
            $error['title_error'] = $this->dropCookie();

        $error['login'] = '';
        $error['check'] = false;

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
	* ������� ��������� ������� �� ���� � ����������
	*
	* (array)$array  - ��� ��, �������� � ������� ��� �������� ���������� ����
	*/
    public function checkActiveBlocks($langTitle, $title = 'diaries', $segment = 'ru', $ajax = 'yes')
    {
        switch($segment){case 'ru':break; case 'en':break; default: $segment = 'ru';}
        $CI =& get_instance();
        $array  = $CI->db->where('title',$title)->get('blocks')->row_array();
        if(empty($array))
        {
            if($ajax == 'yes')
                $this->my_redirect_on_vhod($langTitle,$segment, '');
            return false;
        }

        if($array['on_off'] == 0)
        {
            if($ajax == 'yes')
                $this->my_redirect_on_vhod($langTitle, $segment, '');
            return false;
        }
        return true;
    }

    /**
     * ��� �� file_get_contents, ������ � ���� �������� (�� �������)
     */
    public function curlWithPostField($url = '', $params, $lang = array(), $post = 'yes', $redirect = 'yes', $userAgent = 'no')
    {
        $CI =& get_instance();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if($post == 'yes')
        {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        }

        if($userAgent == 'yes')
        {
            curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12');
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);

        if ($result === FALSE)
        {
            if($redirect == 'yes')
                $this->my_redirect_on_vhod($lang['socSetVk_error_1'], $CI->uri->segment(1), '');
            //echo "cURL Error: " . curl_error($curl);
        }

        curl_close($curl);

        return $result;
    }


	/**
	* ������� ��������� �� �� �������� ��������
	*
	* @$id - ����������� �����
	*/
	public function checkId($id = 0, $check = '')
	{		
		if(!isset($id))
			return false; 
			
		if($check === null)
		{
			if(!is_numeric($id) || strlen($id) > 20 || $id == '')
				return false;
			else
				return true;
		}
		else
		{
			if(!is_numeric($id) || strlen($id) > 20 || $id == '' || $id <= 0)
				return false;
			else
				return true;
		}
	}
	
	/**
	* ����������� ���������� ��������� �����
	*
	* $n - �����, �� �������� ������� ���������
	* $titles - ������ ���� (������, ������, �����)
	*/
	public function number($n, $titles) 
	{
		if($n == 0)
			return $titles[2];
		else
			return ($titles[($n=($n=$n%100)>19? ($n%10):$n)==1?0 : (($n>1&&$n<=4)?1:2)]);
	}

    /**
     * ����������� ������ ���� ������ � ���������
     * @param $config
     * @param string $segment
     * @return mixed
     */
    public function getConfigPerPage($config, $segment = 'ru')
    {
        if($segment == 'en')
        {
            $config['first_link'] = 'First';
            $config['last_link'] = 'Last';
        }

        return $config;
    }
	

	

	
	/**
	* ������� ������ ���� �����. �� ��������� ����� ������� ��� �����, ���� ������ �� ��������
	*
	* @$papka - ����� � ������� �������� ��� ��� �������
	* @$file - ����, ������� ����� � ���� �����
	* (array)$data - $array - ���������� ������ ���� �� ����� �������, �� �� � ���������
	*/
	public function views($papka = 'login', $file = 'content', $data, $titleLib = 'all_display', $library = 'admin_lib')
	{
		$CI =& get_instance();
		
		$data['papka'] = $papka;
		$data['file_view'] = $file;
		$CI->$library->$titleLib($data);
	}
	
	/**
	* ������� ������������� ����� � �������
	*
	* (array)$data - ��� ������, ��������� ����� � ������ ���� ������ �������.
	* (array)$lang - ��� ����� � ���� ������.
	*/
	public function setHeadersTitle($data, $nameMeta = "titleMainDesc", $nameTitle = "titleMain")
	{
		$data['page_info']['title'] = $data['langNew'][$nameTitle];
		
		if(empty($data['name']) || $data['name'] == '')
		{$data['name'] = $data['page_info']['title'];}
		
		$data['page_info']['meta_d'] = $data['langNew'][$nameMeta];
		
		return $data;
	}
	
	/**
	* ������� ��������� ������� ������� � ���� ��, ������� ������ � ������ �� ������� ��������
	*
	* @$user - ��� ������ � ����� � ��� ����� �� �����
	* @$langBanAccount - ��� ����� ������ ���� ������� (�������)
	*/
	public function checkBanUser($user, $langBanAccount, $temp = 'none')
	{
		$CI =& get_instance();
		
		if($user['ban'] == 1)
		{
			$CI->session->unset_userdata('user');
			delete_cookie("name");

            if($temp == 'none')
            {
                set_cookie('error_class',$langBanAccount, 2);
                //�������������� �� �� �� ��������
                redirect($_SERVER['HTTP_REFERER']);
            }
			return false;
		}
		return true;
	}
	
	/**
	* ������� �������� ���� ���� ��� �������
	*/
	private function _nameAdmin($data, $title = 'empty')
	{
		$CI =& get_instance();
		
		$data['name'] = $CI->input->cookie('error',TRUE);
		if(empty($data['name']) || trim($data['name']) == '')
			$data['name'] = $data['langNew'][$title];
			
		$data['page_info']['title'] = $data['name'];
		
		return $data;
	}

    /**
     *  ������� �������� �� ����� �� ���� � ������ str. ���� $temp = 1, �� ���� ��������� ��������� ���� ���������
     * @param string $start
     * @param string $stop
     * @param string $str
     * @param int $temp
     * @return string
     */
    public function cut_str($start = '', $stop = '',$str = '', $temp = 0)
    {
        if($temp == 1)
            $spos = strripos($str,$start);
        else
            $spos = strpos($str,$start);

        $spos = $spos + strlen($start);
        $text = substr($str, $spos);
        $end_pos = strpos($text,$stop);
        $text = substr($text,0,$end_pos);
        return $text;
    }

    /**
     * ������� ������� � ������ ��� � ����������� �� � ������
     * @param string $text
     * @return mixed
     */
    public function changeTextToLink($text = '')
    {
        $text = preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" target='_blank'>$3</a>", $text);
        $text = preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" target='_blank'>$3</a>", $text);
        return $text;
    }
	
	/*
	* ������� �������� ��� ����� ��������� ��������
	*/
	public function init($data, $nameMeta = "titleMainDesc", $nameTitle = "titleMain", $temp = 'none', $nameLibLang = 'langadmin')
	{
		$CI =& get_instance();
		
		//��������� ������� ����� � ���� ��� ����������� ��� � ��
		$data['segment'] = $segment = $CI->checkactiveblocks->getInfoSegment($CI->uri->segment(1));
		//���� ��� ���� ������ ����� ���
		$data['langAll'] = $CI->langforall->getLangForVideo($segment);
		
		if($temp == 'none')
		{
            //��� �������� ������� ����
            $data['langNew'] = $CI->$nameLibLang->getLang($data['segment']);
			//��������� ����� � ����
			$data = $this->setHeadersTitle($data,$nameMeta,$nameTitle);
			if(!empty($data['user_info'])){$this->checkBanUser($data['user_info'], $data['langAll']['banAccount']);}
		}
		else
		{
			//��� �������� ������� ����
			$data['langNew'] = $CI->$nameLibLang->getLang();
			$data = $this->_nameAdmin($data, $nameTitle);
		}
			
		return $data;
	}
	
	/* ��������� ������� ������ ������� ������� �� ��������. 
	* ���� ��� ��������, �� ��������� ��� ���� (������ ����� ���� ��������)
	* 
	* @$str - ������ �� ������� ��� ���������� �����
	*/
	public function sms_translit($str = '') 
	{
			$translit = array(
					" "=>'-',"�"=>"A","�"=>"B","�"=>"V","�"=>"G",
					"�"=>"D","�"=>"E", "�"=>"E", "�"=>"J","�"=>"Z","�"=>"I",
					"�"=>"Y","�"=>"K","�"=>"L","�"=>"M","�"=>"N",
					"�"=>"O","�"=>"P","�"=>"R","�"=>"S","�"=>"T",
					"�"=>"U","�"=>"F","�"=>"H","�"=>"TS","�"=>"CH",
					"�"=>"SH","�"=>"SCH","�"=>"","�"=>"YI","�"=>"",
					"�"=>"E","�"=>"YU","�"=>"YA","�"=>"a","�"=>"b",
					"�"=>"v","�"=>"g","�"=>"d","�"=>"e", "�"=>"e","�"=>"j",
					"�"=>"z","�"=>"i","�"=>"y","�"=>"k","�"=>"l",
					"�"=>"m","�"=>"n","�"=>"o","�"=>"p","�"=>"r",
					"�"=>"s","�"=>"t","�"=>"u","�"=>"f","�"=>"h",
					"�"=>"ts","�"=>"ch","�"=>"sh","�"=>"sch","�"=>"y",
					"�"=>"yi","�"=>"","�"=>"e","�"=>"yu","�"=>"ya","."=> "",
           "/"=> "_",","=>"_", "-"=>"_","("=>"",")"=>"","["=>"",
           "]"=>"", "="=>"_","+"=>"_","*"=>"","?"=>"","\""=>"",
           "'"=>"","&"=>"", "%"=>"","#"=>"","@"=>"","!"=>"",
           ";"=>"","�"=>"","^"=>"",":"=>"","~"=>"","\\"=>""
			);
			return strtr($str,$translit);
	}

    /*
	* ������� ��������� ����������� �� ����������
	*
	* (array)$data - ��� ������ � ����� � ��� ����� �� �����
	* @$segment - ���� �����
	*/
    public function checkAvtorizationUser($data = array(), $segment = 'ru', $temp = 'js')
    {
        if(empty($data['user_info']))
        {
            if($temp != 'js')
                $this->my_redirect_on_vhod($data['langForDiaries']['loginIn'], $segment, 'avtoriz/register');
            return false;
        }
        return true;
    }
}
?>