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
                  `role_id` int(10) unsigned NOT NULL DEFAULT '4',
                  `name` varchar(255) NOT NULL,
                  `login` varchar(255) NOT NULL,
                  `password` varchar(255) NOT NULL,
                  `email` varchar(255) NOT NULL,
                  `hash` varchar(255) NOT NULL,
                  `img` varchar(255) NOT NULL DEFAULT 'img/noimg.png',
                  `count_projects` INT(5) UNSIGNED NOT NULL DEFAULT '0',
                  `status` enum('0','1') NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id_user`),
                  UNIQUE KEY `id_user` (`id_user`),
                  KEY `role_id` (`role_id`),
                  KEY `name` (`name`),
                  KEY `login` (`login`),
                  KEY `status` (`status`)
                ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Тут все юзеры с их ролью';";

        $this->db->query($sql);

        $q = $this->db->get('users')->result_array();
        if(empty($q))
        {
            $sql = "INSERT INTO `users` (`id_user`, `role_id`, `name`, `login`, `password`, `email`, `hash`, `status`, `count_projects`) VALUES
                    (1, 1, '".ADMIN_NAME."', '".ADMIN_LOGIN."', '".sha1(md5(ADMIN_PASS.'Y2LVejV1zNXBne'))."', '".ADMIN_EMAIL."', 'Y2LVejV1zNXBne', '1', 1);";
            $this->db->query($sql);
        }


        /**
         * Таблица ролей юзеров
         * Table of roles users
         */
        $sql = "CREATE TABLE IF NOT EXISTS `role` (
                  `id_role` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `title_ru` varchar(255) NOT NULL,
                  `title_en` varchar(255) NOT NULL,
                  `programm` varchar(255) NOT NULL,
                  `is_delete` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'если 0, то нельзя не удалять не редактировать',
                  PRIMARY KEY (`id_role`),
                  UNIQUE KEY `id_role` (`id_role`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
        $this->db->query($sql);

        $q = $this->db->get('role')->result_array();
        if(empty($q))
        {
            $sql = "INSERT INTO `role` (`id_role`, `title_ru`, `title_en`, `programm`, `is_delete`) VALUES
                (1, 'Администратор', 'Administrator', 'admin', 0),
                (2, 'PHP разработчик', 'PHP developer', 'backend', 1),
                (3, 'JS Разработчик', 'JS Developer', 'frontend', 1),
                (4, 'Гость', 'Guest', 'guest', 0);";
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
                                    (1, 'Первая задача', 1, 1);";
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
              UNIQUE KEY `id_complexity` (`id_complexity`),
              KEY `name_complexity_ru` (`name_complexity_ru`),
              KEY `name_complexity_en` (`name_complexity_en`)
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
         * Тут все задачи для разных проектов
         * Here all the tasks for different projects
         */
        $sql = "CREATE TABLE IF NOT EXISTS `task` (
                  `id_task` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `complexity_id` int(10) unsigned NOT NULL,
                  `user_id` int(10) unsigned NOT NULL,
                  `project_id` int(10) unsigned NOT NULL,
                  `title` varchar(255) NOT NULL,
                  `status` enum('0','1','2','3','4') NOT NULL COMMENT '0-добавили,1-делаем,2-готово,3-готово,но с просрочкой,4 - на паузе',
                  `time_start` varchar(255) NOT NULL,
                  `time_end` varchar(255) NOT NULL,
                  `text` text NOT NULL,
                  `links_to_img` text NOT NULL,
                  `pause` text NOT NULL,
                  `time_much` varchar(255) NOT NULL COMMENT 'сколько прошло времени  свыше нормы',
                  `link_to_srv` varchar(255) NOT NULL,
                  `day_start` int(10) unsigned NOT NULL,
                  `month_start` int(10) unsigned NOT NULL,
                  `year_start` int(10) unsigned NOT NULL,
                  `time_for_complete` int(10) unsigned NOT NULL COMMENT 'время заданное на выполнение',
                  `time_for_complete_value` enum('1', '2', '3', '4') NOT NULL COMMENT 'размер времени заданного на выполнение. 0-мин,1-час,2-день,3-месяц',
                  `time_lanch` varchar(255) NOT NULL COMMENT 'время когда пойдешь на обед',
                  `time_end_day` varchar(255) NOT NULL COMMENT 'время конца рабочего дня',
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
            $sql = "INSERT INTO `task` (`id_task`, `complexity_id`, `user_id`, `project_id`, `title`, `status`, `time_start`, `text`, `day_start`, `month_start`, `year_start`, `time_for_complete`, `time_for_complete_value`, `time_lanch`, `time_end_day`) VALUES
                    ('1', '2', '1', '1', 'Первая задача', '0', '".time()."', 'Комментарий к задаче', '".date('d')."', '".date('m')."', '".date('Y')."', '12', '2', '13', '".(time()+7200)."');";
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
        $this->dbforge->drop_table('role');
        $this->dbforge->drop_table('projects');
        $this->dbforge->drop_table('complexity');
        $this->dbforge->drop_table('task');
    }
}