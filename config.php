<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Если заполнено - это значит то, что человек закончил установку  FIXME удалить ее надо
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
define('ADMIN_EMAIL', 'admin@admin.ru'); //на email будут высылаться системные уведомления FIXME проверить регуляркой
define('ADMIN_PASS', 'admin');
define('ADMIN_LOGIN', 'admin');//FIXME проверить на латиницу и цифры
define('ADMIN_NAME', 'Administrator'); //FIXME проверить на числа и латиницу
define('YOUR_LANG', 'en'); //ru, en . now no other language

/**
 * Постраничная навигация.
 * Pagination.
 *
 * @define COUNT_LINK_FOR_PAGINATION - Количество ссылок. На данный момент она равна 10 (Number of references to the pagination. At the moment it is 10)
 * @define COUNT_OBJECT_PER_PAGE - Количество задач, отображаемых в виде. На данный момент 10 (Number of references to the pagination. At the moment it is 10)
 */
define('COUNT_LINK_FOR_PAGINATION', 10);
define('COUNT_OBJECT_PER_PAGE', 5);