<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Если заполнено - это значит то, что человек закончил установку и то, что он вообще редактировал этот файл
 * If filled - it means that the person has finished the installation and the fact that he did edit this file
 */
define('IS_FILL_KEY', 'a53d6030b');

/**
 * Настройка базы данных
 * Setting up the database
 */
define('NAME_DB', 'time_log_home');
define('USER_DB', 'root');
define('PASSWORD_DB', '');
define('HOST_DB', 'localhost');

/**
 * Первоначальные данные администратора
 * Initial data administrator
 */
define('ADMIN_EMAIL', 'admin@admin.ru'); //на email будут высылаться системные уведомления
define('ADMIN_PASS', 'admin');
define('ADMIN_LOGIN', 'admin');
define('ADMIN_NAME', 'Administrator');
define('YOUR_LANG', 'en'); //ru, en . now no other language

//вконце обязательно поставить "/"
//be sure to put at the end of "/"
define('SITE_URL', 'http://time.log/');
//FIXME если вы обращаетесь к сайту через localhost, то измените эту строчку, а также...
//define('SITE_URL', 'http://localhost/time.log/');

/**
 * Постраничная навигация.
 * Pagination.
 * @define MAX_COUNT_LINK_FOR_PAGINATION - Количество ссылок. На данный момент она равна 10 (Number of references to the pagination. At the moment it is 10)
 * @define MAX_COUNT_OBJECT_PER_PAGE - Количество задач, отображаемых в виде. На данный момент 10 (Number of references to the pagination. At the moment it is 10)
 */
//если слишком много ссылок навигации, то уменьшить (if there are too much links of navigation, to reduce)
define('MAX_COUNT_LINK_FOR_PAGINATION', 10);
//если мало отображаются заданий на одной странице, то увеличить это число (if are a little displayed tasks on one page, to increase this number)
define('MAX_COUNT_OBJECT_PER_PAGE', 5);

/**
 * Путь к главной папке с файлами отображения сайта
 * Way to the main folder with files of display of the site
 */
define('DEFAULT_VIEW', 'default');