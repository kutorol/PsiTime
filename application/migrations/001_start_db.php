<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Start_db extends CI_Migration{

    /**
     * Создаем первоначальную миграцию с данными
     * Create the initial migration of data
     */
    public function up()
    {

        /**
         * Таблица для юзеров
         * Table for users
         */
        $sql = "CREATE TABLE IF NOT EXISTS `users` (
                  `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT,
                  `name` varchar(255) NOT NULL,
                  `login` varchar(255) NOT NULL,
                  `password` varchar(255) NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `hash` varchar(255) NOT NULL,
                  `img` varchar(255) NOT NULL DEFAULT 'noimg.png' COMMENT 'все аватарки находятся в папке img',
                  `showOrNot3DChars` int(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 - показывать 3D графики, 2 - не показывать',
                  `showOrNotExportChars` int(1) unsigned NOT NULL DEFAULT '1' COMMENT '1 - показывать экспорт графиков, 2 - не показывать',
                  `count_projects` INT(5) UNSIGNED NOT NULL DEFAULT '0',
                  `status` enum('0','1') NOT NULL DEFAULT '0',
                  `hoursInDayToWork` VARCHAR(2) NOT NULL COMMENT 'сколько часов в день юзер работает',
                  PRIMARY KEY (`id_user`),
                  UNIQUE KEY `id_user` (`id_user`),
                  KEY `name` (`name`),
                  KEY `login` (`login`),
                  KEY `status` (`status`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Тут все юзеры с их ролью';";

        $this->db->query($sql);

        $q = $this->db->get('users')->result_array();
        if(empty($q))
        {
            $randHash = substr(sha1(md5(rand(5000, 5996034))), 0, 15);
            $sql = "INSERT INTO `users` (`id_user`, `name`, `login`, `password`, `email`, `hash`, `status`, `count_projects`, `hoursInDayToWork`) VALUES
                    (1, '".ADMIN_NAME."', '".ADMIN_LOGIN."', '".sha1(md5(ADMIN_PASS.$randHash))."', '".ADMIN_EMAIL."', '".$randHash."', '1', 1, ".intval(MAX_WORK_TIME_IN_DAY).");";
            $this->db->query($sql);
        }


        /**
         * Таблица проектов, которые включают в себя отдельные задания
         * Table of projects that include specific tasks
         */
        $sql = "CREATE TABLE IF NOT EXISTS `projects` (
                  `id_project` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `title` varchar(255) NOT NULL,
                  `responsible` int(11) NOT NULL COMMENT 'ид того, кто главный за этот проект',
                  `team_ids` TEXT NULL DEFAULT NULL,
                  PRIMARY KEY (`id_project`),
                  UNIQUE KEY `id_project` (`id_project`),
                  KEY `responsible` (`responsible`),
                  FULLTEXT KEY `title` (`title`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        $this->db->query($sql);

        $q = $this->db->get('projects')->result_array();
        if(empty($q))
        {
            $sql = "INSERT INTO `projects` (`id_project`, `title`, `responsible`, `team_ids`) VALUES
                                    (1, 'Первай проект', 1, 1);";
            $this->db->query($sql);
        }


        /**
         * Таблица с цветом и уровнем сложности задачи
         * Table with color and the level of complexity of the task
         */
        $sql = "CREATE TABLE IF NOT EXISTS `complexity` (
              `id_complexity` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name_complexity_ru` varchar(255) NOT NULL,
              `name_complexity_en` varchar(255) NOT NULL,
              `color` varchar(255) NOT NULL,
              PRIMARY KEY (`id_complexity`),
              UNIQUE KEY `id_complexity` (`id_complexity`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Тут все названия сложностей задач и их цвета';";
        $this->db->query($sql);

        $q = $this->db->get('complexity')->result_array();
        if(empty($q))
        {
            $sql = "INSERT INTO `complexity` (`id_complexity`, `name_complexity_ru`, `name_complexity_en`, `color`) VALUES
                    (1, 'Легкая', 'Easy', 'success'),
                    (2, 'Средняя', 'Medium', 'warning'),
                    (3, 'Высокая', 'High', 'danger');";
            $this->db->query($sql);
        }


        /**
         * Таблица приоритета задачи с ее иконкой ;)
         * Table task priority to its icon ;)
         */
        $sql = "CREATE TABLE IF NOT EXISTS `priority` (
              `id_priority` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `title_ru` varchar(255) NOT NULL,
              `title_en` varchar(255) NOT NULL,
              `icon` varchar(255) NOT NULL,
              `color` varchar(255) NOT NULL,
              PRIMARY KEY (`id_priority`),
              UNIQUE KEY `id_priority` (`id_priority`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Тут приоритет задачи';";
        $this->db->query($sql);

        $q = $this->db->get('priority')->result_array();
        if(empty($q))
        {
            $sql = "INSERT INTO `priority` (`id_priority`, `title_ru`, `title_en`, `icon`, `color`) VALUES
                    (1, 'Обычный', 'Normal', 'fa fa-mars', ''),
                    (2, 'Важный', 'Significant', 'fa fa-venus', 'default'),
                    (3, 'Серьезный', 'Serious', 'fa fa-venus-mars', 'primary'),
                    (4, 'Критический', 'Crucial', 'fa fa-mars-double', 'warning'),
                    (5, 'Срочно выполнить', 'Urgent perform', 'fa fa-transgender-alt', 'danger');";
            $this->db->query($sql);
        }


        /**
         * Тут все задачи для разных проектов
         * Here all the tasks for different projects
         */
        $sql = "CREATE TABLE IF NOT EXISTS `task` (
                  `id_task` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `complexity_id` int(10) unsigned NOT NULL,
                  `priority_id` INT unsigned DEFAULT '1',
                  `user_id` int(10) unsigned NOT NULL COMMENT 'id юзера, кто создал задачу',
                  `project_id` int(10) unsigned NOT NULL,
                  `title` varchar(255) NOT NULL,
                  `status` varchar(1) NOT NULL DEFAULT '0' COMMENT '0-добавили,1-делаем,2-готово,3-на паузе',
                  `performer_id` INT NOT NULL COMMENT 'id исполнителя',
                  `time_add` VARCHAR(50) NOT NULL COMMENT 'время добавления задачи',
                  `time_start` varchar(255) NOT NULL,
                  `time_end` varchar(255) NOT NULL,
                  `text` text NOT NULL,
                  `pause` text NOT NULL,
                  `pause_for_complite` TEXT NOT NULL COMMENT 'это пауза, когда задача выполнена, а потом ее вновь выполняют',
                  `day_start` int(10) unsigned NOT NULL,
                  `month_start` int(10) unsigned NOT NULL,
                  `year_start` int(10) unsigned NOT NULL,
                  `time_for_complete` int(10) unsigned NOT NULL COMMENT 'время заданное на выполнение',
                  `time_for_complete_value` TINYINT(1) NOT NULL COMMENT 'размер времени заданного на выполнение. 0-мин,1-час,2-день,3-неделя,4-месяц',
                  PRIMARY KEY (`id_task`),
                  UNIQUE KEY `id_task` (`id_task`),
                  KEY `complexity_id` (`complexity_id`),
                  KEY `user_id` (`user_id`),
                  KEY `project_id` (`project_id`),
                  KEY `time_for_complete` (`time_for_complete`),
                  KEY `day_start` (`day_start`),
                  KEY `month_start` (`month_start`),
                  KEY `year_start` (`year_start`),
                  KEY `time_start` (`time_start`),
                  KEY `time_end` (`time_end`),
                  KEY `status` (`status`),
                  FULLTEXT KEY `text` (`text`),
                  FULLTEXT KEY `title` (`title`)
                ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
        $this->db->query($sql);

        $q = $this->db->get('task')->result_array();
        if(empty($q))
        {
            $sql = "INSERT INTO `task` (`id_task`, `performer_id`, `complexity_id`, `user_id`, `project_id`, `title`, `status`, `time_add`, `text`, `day_start`, `month_start`, `year_start`, `time_for_complete`, `time_for_complete_value`) VALUES
                    ('1', '1', '2', '1', '1', 'Первая задача', '0', '".time()."', 'Комментарий к задаче', '".date('d')."', '".date('m')."', '".date('Y')."', '12', '1');";
            $this->db->query($sql);
        }

    }

    /**
     * Удаляем все таблицы, созданные выше
     * We remove all tables created above
     */
    public function down()
    {
        $this->dbforge->drop_table('users');
        $this->dbforge->drop_table('projects');
        $this->dbforge->drop_table('complexity');
        $this->dbforge->drop_table('priority');
        $this->dbforge->drop_table('task');
    }
}