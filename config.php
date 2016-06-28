<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Отображает красиво вывод массивов через d(array());
* Displays beautiful conclusion arrays by d(array());
*/
require_once "vendor/autoload.php";

/**
 * Если заполнено - это значит то, что человек закончил установку и то, что он вообще редактировал этот файл
 * If filled - it means that the person has finished the installation and the fact that he did edit this file
 */
define('IS_FILL_KEY', 'a5s3d6030b');

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
define('ADMIN_EMAIL', 'admin@admin.ru'); //TODO на email будут высылаться системные уведомления 
define('ADMIN_PASS', 'admin');
define('ADMIN_LOGIN', 'admin');
define('ADMIN_NAME', 'Administrator');
define('YOUR_LANG', 'ru'); //ru, en . now no other language
define('MAX_WORK_TIME_IN_DAY', '8'); //количество рабочих часов в день, не считая обеденного перерыва

//!!!!!!!! вконце обязательно поставить "/", а в начале http://
//!!!!!!!! be sure to put at the end of "/", do not forget http://
define('SITE_URL', 'http://PsiTime.org/');

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
 * Если false, то искользуются красивые чекбоксы, но порой при нажатии они неактивируются... , если true - стандартные
 * If false, then use the checkboxes beautiful, but sometimes when you are non-inducing ..., if true - standard
 * TODO - сделать стили для css и некоторые функции в js переработать
 */
define('DEFAULT_CHECKBOX', false);

/**
 * Путь к главной папке с файлами отображения сайта
 * Way to the main folder with files of display of the site
 */
define('DEFAULT_VIEW', 'default');