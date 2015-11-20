<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Create_db {


    /**
     * Создаем базу данных
     * Creating a database
     * @param $lang
     * @param $filePath
     * @return bool
     */
    private function _createDB($lang, $filePath)
    {
        $link = @mysql_connect(HOST_DB, USER_DB, PASSWORD_DB);
        if (!$link) {
            log_message('error', $lang['error_1']);
            show_error($lang['error_1'], 500, $lang['error_8']);
            exit;
        }

        $sql = 'CREATE DATABASE IF NOT EXISTS '.NAME_DB.' CHARACTER SET utf8 COLLATE utf8_general_ci';
        if (mysql_query($sql, $link))
            file_put_contents($filePath, NAME_DB);
        else
        {
            log_message('error', $lang['error_2'].NAME_DB."': <br>". mysql_error());
            show_error($lang['error_2'].NAME_DB."': <br>". mysql_error(), 500, $lang['error_8']);
            exit;
        }

        return true;
    }
    /**
     * Функция проверяет наличие установки приложения и создает базу данных, или ничего не делает
     * The function checks whether the application is installed and creates a database, or does nothing
     */
    public function add()
    {
        $fail = false;
        //проверяем язык
        switch(YOUR_LANG)
        {
            case "ru":
            case "en":
                break;
            default:
                $fail = true;
        }

        $lang = [];
        if($fail === true)
        {
            include APPPATH . 'language/db_hook/ru/install.php';
            log_message('error', $lang['error_3']);
            show_error($lang['error_3'], 500, $lang['error_8']);
            exit;
        }
        else
            //подключаем файл со словами
            include APPPATH . 'language/db_hook/' . YOUR_LANG . '/install.php';

        //если число ссылок, которые должны выводиться в навигации не являются числом - ошибка
        if(!is_numeric(MAX_COUNT_LINK_FOR_PAGINATION) || intval(MAX_COUNT_LINK_FOR_PAGINATION) <= 0)
        {
            log_message('error', $lang['error_7']);
            show_error($lang['error_7'], 500, $lang['error_8']);
            exit;
        }

        //если число выводимых задач и прочих вещей не является числом - ошибка
        if(!is_numeric(MAX_COUNT_OBJECT_PER_PAGE) || intval(MAX_COUNT_OBJECT_PER_PAGE) <= 2)
        {
            log_message('error', $lang['error_9']);
            show_error($lang['error_9'], 500, $lang['error_8']);
            exit;
        }

        //если число выводимых задач и прочих вещей не является числом - ошибка
        if(!is_numeric(MAX_WORK_TIME_IN_DAY) || intval(MAX_WORK_TIME_IN_DAY) < 1 || intval(MAX_WORK_TIME_IN_DAY) > 14)
        {
            log_message('error', $lang['error_13']);
            show_error($lang['error_13'], 500, $lang['error_8']);
            exit;
        }

        //проверка email
        if (!preg_match("/^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z\-\.]+$/iu", ADMIN_EMAIL))
        {
            log_message('error', $lang['error_10']);
            show_error($lang['error_10'], 500, $lang['error_8']);
            exit;
        }

        //проверка имени администратора
        if(!preg_match("/^[а-яА-ЯёЁa-zA-Z ]{2,20}$/iu", ADMIN_NAME))
        {
            log_message('error', $lang['error_11']);
            show_error($lang['error_11'], 500, $lang['error_8']);
            exit;
        }

        //проверка логина администратора
        if(!preg_match("/^[a-zA-Z0-9\-_]{5,20}$/iu", ADMIN_LOGIN))
        {
            log_message('error', $lang['error_12']);
            show_error($lang['error_12'], 500, $lang['error_8']);
            exit;
        }

        //проверка логина администратора
        if(!preg_match("/^[a-zA-Z0-9_]{5,20}$/iu", NAME_DB))
        {
            log_message('error', $lang['error_14']);
            show_error($lang['error_14'], 500, $lang['error_8']);
            exit;
        }

        //эта проверка нужна только для того, чтобы знать, заполнил ли человек config.php в корне сайта
        //this check is necessary only in order that the nobility, whether the person of config.php in a site root filled
        if(IS_FILL_KEY != '')
        {
            if(YOUR_LANG != '')
            {
                try
                {
                    $redirect = false;
                    $filePath = APPPATH . 'migrations/install.log';
                    if(!file_exists($filePath))
                        $redirect = $this->_createDB($lang, $filePath);
                    else
                    {
                        $file = file_get_contents($filePath);
                        if($file != NAME_DB)
                            $redirect = $this->_createDB($lang, $filePath);
                    }

                    if($redirect === true)
                    {
                        //header('Location: /time.log/'.YOUR_LANG.'/welcome/install');
                        header('Location: /'.YOUR_LANG.'/welcome/install');
                        exit;
                    }
                }
                catch(Exception $e)
                {
                    log_message('error', $lang['error_6'].$e->getMessage());
                    show_error($lang['error_6'].$e->getMessage(), 500, $lang['error_8']);
                    exit;
                }
            }
            else
            {
                log_message('error', $lang['error_3']);
                show_error($lang['error_3'], 500, $lang['error_8']);
                exit;
            }
        }
        else
        {
            log_message('error', $lang['error_4_1'].substr( md5(rand()), 0, 9).$lang['error_4_2']);
            show_error($lang['error_4_1'].substr( md5(rand()), 0, 9).$lang['error_4_2'], 500, $lang['error_8']);
            exit;
        }
    }
}