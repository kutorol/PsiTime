-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Окт 07 2015 г., 14:15
-- Версия сервера: 5.6.25
-- Версия PHP: 5.6.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `time_log`
--

-- --------------------------------------------------------

--
-- Структура таблицы `complexity`
--

CREATE TABLE IF NOT EXISTS `complexity` (
  `id_complexity` int(11) unsigned NOT NULL,
  `name_complexity_ru` varchar(255) NOT NULL,
  `name_complexity_en` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Тут все названия сложностей задач и их цвета';

--
-- Дамп данных таблицы `complexity`
--

INSERT INTO `complexity` (`id_complexity`, `name_complexity_ru`, `name_complexity_en`, `color`) VALUES
(1, 'Легкая', 'Easy', 'success'),
(2, 'Средняя', 'Medium', 'warning'),
(3, 'Высокая', 'High', 'danger');

-- --------------------------------------------------------

--
-- Структура таблицы `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `version` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `migrations`
--

INSERT INTO `migrations` (`version`) VALUES
(1);

-- --------------------------------------------------------

--
-- Структура таблицы `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id_project` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `responsible` int(11) NOT NULL COMMENT 'ид того, кто главный за этот проект',
  `team_ids` text
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `projects`
--

INSERT INTO `projects` (`id_project`, `title`, `responsible`, `team_ids`) VALUES
(52, 'fasdg', 2, '2'),
(51, 'hdsf', 2, '2');

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id_role` int(10) unsigned NOT NULL,
  `title_ru` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `programm` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`id_role`, `title_ru`, `title_en`, `programm`) VALUES
(1, 'Администратор', 'Administrator', 'admin'),
(2, 'PHP разработчик', 'PHP developer', 'backend'),
(3, 'JS Разработчик', 'JS Developer', 'frontend');

-- --------------------------------------------------------

--
-- Структура таблицы `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `id_task` int(10) unsigned NOT NULL,
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
  `time_for_complete_value` enum('1','2','3','4') NOT NULL COMMENT 'размер времени заданного на выполнение. 0-мин,1-час,2-день,3-месяц',
  `time_lanch` varchar(255) NOT NULL COMMENT 'время когда пойдешь на обед',
  `time_end_day` varchar(255) NOT NULL COMMENT 'время конца рабочего дня'
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL DEFAULT 'img/noimg.png',
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `count_projects` int(5) unsigned NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Тут все юзеры с их ролью';

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_user`, `role_id`, `name`, `login`, `password`, `email`, `hash`, `img`, `status`, `count_projects`) VALUES
(1, 1, 'kol', 'admin', '49029afc5c33123b47a8246b92e757ea396268ef', 'admin@mail.ru', 'wBOEK3', 'img/noimg.png', '1', 2),
(2, 1, 'коля', 'admin2', 'b9c890a209c11152d3a33057cf350663ecf4a171', 'admin2@mail.ru', 'hDEZ7XrxvDKUz', 'img/noimg.png', '1', 2),
(3, 1, 'коля', 'admin3', 'b9c890a209c11152d3a33057cf350663ecf4a171', 'admin3@mail.ru', 'hDEZ7XrxvDKUz', 'img/noimg.png', '0', 0),
(4, 0, 'koksd', 'admin4', 'd050e5442f812d02f101671b2542c4b89f7a945a', 'pipi310993@mail.ru', 'eGOn3SUAHNOU3', 'img/noimg.png', '0', 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `complexity`
--
ALTER TABLE `complexity`
  ADD PRIMARY KEY (`id_complexity`),
  ADD UNIQUE KEY `id_complexity` (`id_complexity`),
  ADD KEY `name_complexity_ru` (`name_complexity_ru`),
  ADD KEY `name_complexity_en` (`name_complexity_en`);

--
-- Индексы таблицы `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id_project`),
  ADD UNIQUE KEY `id_project` (`id_project`),
  ADD KEY `responsible` (`responsible`),
  ADD FULLTEXT KEY `title` (`title`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`),
  ADD UNIQUE KEY `id_role` (`id_role`);

--
-- Индексы таблицы `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id_task`),
  ADD UNIQUE KEY `id_task` (`id_task`),
  ADD KEY `complexity_id` (`complexity_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `time_for_complete` (`time_for_complete`),
  ADD KEY `day_start` (`day_start`),
  ADD KEY `month_start` (`month_start`),
  ADD KEY `year_start` (`year_start`),
  ADD KEY `time_start` (`time_start`),
  ADD KEY `time_end` (`time_end`),
  ADD KEY `status` (`status`),
  ADD FULLTEXT KEY `text` (`text`);
ALTER TABLE `task`
  ADD FULLTEXT KEY `title` (`title`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `id_user` (`id_user`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `name` (`name`),
  ADD KEY `login` (`login`),
  ADD KEY `status` (`status`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `complexity`
--
ALTER TABLE `complexity`
  MODIFY `id_complexity` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `projects`
--
ALTER TABLE `projects`
  MODIFY `id_project` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=53;
--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `task`
--
ALTER TABLE `task`
  MODIFY `id_task` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
