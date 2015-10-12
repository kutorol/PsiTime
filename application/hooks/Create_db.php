<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Create_db {


    private function _createDB($lang, $filePath)
    {
        $link = @mysql_connect(HOST_DB, USER_DB, PASSWORD_DB);
        if (!$link) {
            show_error($lang['error_1']);
            exit;
        }

        $sql = 'CREATE DATABASE IF NOT EXISTS '.NAME_DB.' CHARACTER SET utf8 COLLATE utf8_general_ci';
        if (mysql_query($sql, $link))
            file_put_contents($filePath, NAME_DB);
        else
        {
            show_error($lang['error_2'].NAME_DB."': <br>". mysql_error());
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
            show_error($lang['error_3']);
            exit;
        }
        else
            //подключаем файл со словами
            include APPPATH . 'language/db_hook/' . YOUR_LANG . '/install.php';

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


                    /**
                     * FIXME удалить time.log/ на другом сервере
                     */
                    if($redirect === true)
                    {
                        //FIXME не редиректит, если до этого не был отправлен заголовок или наоборот он не должен быть отправлен. Короче тут бред пиздец какой
                        //самый простой и тупой способ отправить заголовки, чтобы потом редирект сработал, иначе миграции не применятся и установка не завершится.
                        //Кто поумнее - исправьте на более правильный вариант
                        //echo 1;
                        //header('Location: /time.log/'.YOUR_LANG.'/welcome/install');
                        header('Location: /'.YOUR_LANG.'/welcome/install');
                        exit;
                    }

                }
                catch(Exception $e)
                {
                    show_error($lang['error_6'].$e->getMessage());
                    exit;
                }
            }
            else
            {
                show_error($lang['error_3']);
                exit;
            }
        }
        else
        {
            show_error($lang['error_4_1'].substr( md5(rand()), 0, 9).$lang['error_4_2']);
            exit;
        }
    }
}