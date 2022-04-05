-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost:3306
-- Время создания: Фев 20 2022 г., 12:57
-- Версия сервера: 10.3.32-MariaDB-cll-lve
-- Версия PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal`
--

CREATE TABLE `hl_appeal` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(2) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `date_update` int(11) UNSIGNED NOT NULL,
  `date_leave` int(11) UNSIGNED NOT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `photo` int(1) NOT NULL,
  `location_lat` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location_lng` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_appeal` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_chat`
--

CREATE TABLE `hl_appeal_chat` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `who` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_date`
--

CREATE TABLE `hl_appeal_date` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `date` int(11) UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `approved` int(1) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_gallery`
--

CREATE TABLE `hl_appeal_gallery` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `image` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_unique_id` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_size` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_rating`
--

CREATE TABLE `hl_appeal_rating` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_request`
--

CREATE TABLE `hl_appeal_request` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `type` int(2) NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_request_type`
--

CREATE TABLE `hl_appeal_request_type` (
  `id` int(11) NOT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `hl_appeal_request_type`
--

INSERT INTO `hl_appeal_request_type` (`id`, `title`, `comment`) VALUES
(1, 'Запит на зміну виконавця', 'Запит на зміну виконавця - якщо звернення не відповідає компетенції виконавця або в разі необхідності залучення інших виконавців.'),
(2, 'Запит на зміну контрольної дати', 'Подати запит на зміну контрольної дати виконання робіт.');

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_status`
--

CREATE TABLE `hl_appeal_status` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `notify` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_user`
--

CREATE TABLE `hl_appeal_user` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_work`
--

CREATE TABLE `hl_appeal_work` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_appeal_work_file`
--

CREATE TABLE `hl_appeal_work_file` (
  `id` int(11) NOT NULL,
  `appeal_id` int(11) NOT NULL,
  `work_id` int(11) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `photo` int(1) NOT NULL,
  `doc` int(1) NOT NULL,
  `file` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `completed` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_bot_user`
--

CREATE TABLE `hl_bot_user` (
  `id` int(11) NOT NULL,
  `from_id` bigint(20) UNSIGNED NOT NULL,
  `chat_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_bot` int(1) NOT NULL,
  `from_first_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `date_update` int(11) UNSIGNED NOT NULL,
  `active` int(1) NOT NULL,
  `emailskip` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_message`
--

CREATE TABLE `hl_message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `package` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_message_needsend`
--

CREATE TABLE `hl_message_needsend` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_message_package`
--

CREATE TABLE `hl_message_package` (
  `id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `users` int(11) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `date_done` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_sessions`
--

CREATE TABLE `hl_sessions` (
  `id` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_status`
--

CREATE TABLE `hl_status` (
  `id` int(11) NOT NULL,
  `title` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `hl_status`
--

INSERT INTO `hl_status` (`id`, `title`) VALUES
(1, 'В обробці'),
(2, 'Відхилено'),
(3, 'Прийнято'),
(4, 'В роботі'),
(5, 'Виконано'),
(6, 'Уточнення даних');

-- --------------------------------------------------------

--
-- Структура таблицы `hl_telegram_command`
--

CREATE TABLE `hl_telegram_command` (
  `id` int(11) NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `command` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_telegram_message`
--

CREATE TABLE `hl_telegram_message` (
  `id` int(11) NOT NULL,
  `chat_id` bigint(20) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_user`
--

CREATE TABLE `hl_user` (
  `id` int(11) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `email` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pass` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_id` int(11) NOT NULL,
  `code_login` int(6) NOT NULL,
  `code_recovery` int(6) NOT NULL,
  `date_recovery` int(11) UNSIGNED NOT NULL,
  `email_send` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `hl_user`
--

INSERT INTO `hl_user` (`id`, `date_add`, `email`, `phone`, `first_name`, `last_name`, `title`, `description`, `comment`, `pass`, `group_id`, `code_login`, `code_recovery`, `date_recovery`, `email_send`) VALUES
(1, 1624125769, 'test@test.com', '', 'Admin', 'Admin', '', '', '', '$2y$10$WWSBtt/yvpEsrm3h8Unmw.w4CdYNwtOQek7Fuvs.8MVmSXm/D1xt6', 1, 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `hl_user_group`
--

CREATE TABLE `hl_user_group` (
  `id` int(11) NOT NULL,
  `title` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `hl_user_group`
--

INSERT INTO `hl_user_group` (`id`, `title`, `slug`) VALUES
(1, 'Відповідальна особа', 'manager'),
(2, 'Особа уповнажена приймати рішення', 'head'),
(3, 'Виконавець', 'implementer');

-- --------------------------------------------------------

--
-- Структура таблицы `hl_user_log`
--

CREATE TABLE `hl_user_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `log` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_user_login`
--

CREATE TABLE `hl_user_login` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` int(6) NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `status` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_viber_command`
--

CREATE TABLE `hl_viber_command` (
  `id` int(11) NOT NULL,
  `sender_id` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `command` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `hl_viber_message`
--

CREATE TABLE `hl_viber_message` (
  `id` int(11) NOT NULL,
  `sender_id` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_add` int(11) UNSIGNED NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Индексы таблицы `hl_appeal`
--
ALTER TABLE `hl_appeal`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_chat`
--
ALTER TABLE `hl_appeal_chat`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_date`
--
ALTER TABLE `hl_appeal_date`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_gallery`
--
ALTER TABLE `hl_appeal_gallery`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_rating`
--
ALTER TABLE `hl_appeal_rating`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_request`
--
ALTER TABLE `hl_appeal_request`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_request_type`
--
ALTER TABLE `hl_appeal_request_type`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_status`
--
ALTER TABLE `hl_appeal_status`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_user`
--
ALTER TABLE `hl_appeal_user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_work`
--
ALTER TABLE `hl_appeal_work`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_appeal_work_file`
--
ALTER TABLE `hl_appeal_work_file`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_bot_user`
--
ALTER TABLE `hl_bot_user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_message`
--
ALTER TABLE `hl_message`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_message_needsend`
--
ALTER TABLE `hl_message_needsend`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_message_package`
--
ALTER TABLE `hl_message_package`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_sessions`
--
ALTER TABLE `hl_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ci_sessions_timestamp` (`timestamp`);

--
-- Индексы таблицы `hl_status`
--
ALTER TABLE `hl_status`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_telegram_command`
--
ALTER TABLE `hl_telegram_command`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_telegram_message`
--
ALTER TABLE `hl_telegram_message`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_user`
--
ALTER TABLE `hl_user`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_user_group`
--
ALTER TABLE `hl_user_group`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_user_log`
--
ALTER TABLE `hl_user_log`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_user_login`
--
ALTER TABLE `hl_user_login`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_viber_command`
--
ALTER TABLE `hl_viber_command`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `hl_viber_message`
--
ALTER TABLE `hl_viber_message`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для таблицы `hl_appeal`
--
ALTER TABLE `hl_appeal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_chat`
--
ALTER TABLE `hl_appeal_chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_date`
--
ALTER TABLE `hl_appeal_date`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_gallery`
--
ALTER TABLE `hl_appeal_gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_rating`
--
ALTER TABLE `hl_appeal_rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_request`
--
ALTER TABLE `hl_appeal_request`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_request_type`
--
ALTER TABLE `hl_appeal_request_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_status`
--
ALTER TABLE `hl_appeal_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_user`
--
ALTER TABLE `hl_appeal_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_work`
--
ALTER TABLE `hl_appeal_work`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_appeal_work_file`
--
ALTER TABLE `hl_appeal_work_file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_bot_user`
--
ALTER TABLE `hl_bot_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_message`
--
ALTER TABLE `hl_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_message_needsend`
--
ALTER TABLE `hl_message_needsend`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_message_package`
--
ALTER TABLE `hl_message_package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_status`
--
ALTER TABLE `hl_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_telegram_command`
--
ALTER TABLE `hl_telegram_command`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_telegram_message`
--
ALTER TABLE `hl_telegram_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_user`
--
ALTER TABLE `hl_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_user_group`
--
ALTER TABLE `hl_user_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_user_log`
--
ALTER TABLE `hl_user_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_user_login`
--
ALTER TABLE `hl_user_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_viber_command`
--
ALTER TABLE `hl_viber_command`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `hl_viber_message`
--
ALTER TABLE `hl_viber_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
