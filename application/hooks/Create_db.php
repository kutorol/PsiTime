<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Create_db {

    public function add()
    {

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
                            show_error("Данные подключения к Базе Данных неправильны!<br> Измените файл -> config.php <- в корне сайта");
                            exit;
                        }

                        $sql = 'CREATE DATABASE IF NOT EXISTS '.NAME_DB.' CHARACTER SET utf8 COLLATE utf8_general_ci';
                        if (mysql_query($sql, $link))
                        {
                            file_put_contents($filePath, '1');
                            header('Location: /time.log/'.YOUR_LANG.'/welcome/install');
                        }
                        else
                        {
                            show_error("Ошибка при создании базы данных '".NAME_DB."': <br>". mysql_error());
                            exit;
                        }
                    }
                }
                catch(Exception $e)
                {
                    show_error("Ошибка : <br>".$e->getMessage());
                    exit;
                }
            }
            else
            {
                show_error("Заполните константу YOUR_LANG значением 'ru' или 'en' в файле config.php в корне сайта!");
                exit;
            }
        }
        else
        {
            show_error("Заполните константу IS_FILL_KEY этим значением '".substr( md5(rand()), 0, 9)."' в файле config.php в корне сайта!");
            exit;
        }
    }
}