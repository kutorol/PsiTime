<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Create_db {

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
                    $filePath = APPPATH . 'migrations/install.log';
                    if(!file_exists($filePath))
                    {
                        $link = @mysql_connect(HOST_DB, USER_DB, PASSWORD_DB);
                        if (!$link) {
                            show_error($lang['error_1']);
                            exit;
                        }

                        $sql = 'CREATE DATABASE IF NOT EXISTS '.NAME_DB.' CHARACTER SET utf8 COLLATE utf8_general_ci';
                        if (mysql_query($sql, $link))
                        {
                            file_put_contents($filePath, '1');
                            /**
                             * FIXME удалить time.log/ на другом сервере
                             */
                            header('Location: /time.log/'.YOUR_LANG.'/welcome/install');
                        }
                        else
                        {
                            show_error($lang['error_2'].NAME_DB."': <br>". mysql_error());
                            exit;
                        }
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