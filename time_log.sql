-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Сен 22 2015 г., 13:24
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
  `name_complexity` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Тут все названия сложностей задач и их цвета';

-- --------------------------------------------------------

--
-- Структура таблицы `projects`
--

CREATE TABLE IF NOT EXISTS `projects` (
  `id_project` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `responsible` varchar(255) NOT NULL COMMENT 'ид того, кто главный за этот проект'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id_role` int(10) unsigned NOT NULL,
  `title_ru` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `role`
--

INSERT INTO `role` (`id_role`, `title_ru`, `title_en`) VALUES
(1, 'Администратор', 'admin'),
(2, 'PHP разработчик', 'backend'),
(5, 'JS Разработчик', 'frontend');

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
  `time_lanch` varchar(255) NOT NULL COMMENT 'время когда пойдешь на обед',
  `time_end_day` varchar(255) NOT NULL COMMENT 'время конца рабочего дня'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `status` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Тут все юзеры с их ролью';

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id_user`, `role_id`, `name`, `login`, `password`, `email`, `hash`, `status`) VALUES
(1, 2, 'Николай', 'klayn24', '1d0da79dfc2834abc58a3c4b1d97ebcbbd1f41ca', 'pipi310993@mail.ru', 'Y2LVejV1zNXBne', '1');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `complexity`
--
ALTER TABLE `complexity`
  ADD PRIMARY KEY (`id_complexity`);

--
-- Индексы таблицы `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id_project`);

--
-- Индексы таблицы `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Индексы таблицы `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id_task`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `complexity_id` (`complexity_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `time_start` (`time_start`),
  ADD KEY `time_end` (`time_end`),
  ADD KEY `time_much` (`time_much`),
  ADD KEY `day_start` (`day_start`),
  ADD KEY `month_start` (`month_start`),
  ADD KEY `year_start` (`year_start`),
  ADD KEY `time_for_complete` (`time_for_complete`),
  ADD FULLTEXT KEY `title` (`title`);
ALTER TABLE `task`
  ADD FULLTEXT KEY `text` (`text`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `complexity`
--
ALTER TABLE `complexity`
  MODIFY `id_complexity` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `projects`
--
ALTER TABLE `projects`
  MODIFY `id_project` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT для таблицы `task`
--
ALTER TABLE `task`
  MODIFY `id_task` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
