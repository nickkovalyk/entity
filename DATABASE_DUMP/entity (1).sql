-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Янв 14 2019 г., 11:19
-- Версия сервера: 10.1.34-MariaDB
-- Версия PHP: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `entity`
--

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `attachment` text COLLATE utf8_unicode_ci,
  `homepage` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_id` int(11) NOT NULL DEFAULT '1',
  `parent_id` int(11) DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `browser` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `username`, `email`, `content`, `attachment`, `homepage`, `post_id`, `parent_id`, `ip`, `created_at`, `browser`) VALUES
(1, 'John', 'John@mail.com', '<strong>this is amazing comment</strong>', '/post/5c3c3f2b624c6.jpg', 'htttp://some.com/', 1, NULL, '127.0.0.1', '2019-01-14 06:50:03', NULL),
(2, 'Nick', 'nick@gmail.com', 'just another one', '/post/5c3c41ab5f4ac.txt', '', 1, NULL, '127.0.0.1', '2019-01-14 07:00:43', NULL),
(3, 'Nick', 'nick@gmail.com', 'Some text', '/post/5c3c41fb6b850.gif', '', 1, 1, '127.0.0.1', '2019-01-14 07:02:03', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `title`, `content`, `created_at`) VALUES
(1, 'Some post title', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras eu rhoncus lectus. Praesent in hendrerit purus. Integer id malesuada diam. Integer venenatis egestas enim, ac convallis nulla consectetur quis. Nam porttitor aliquam convallis. Curabitur pharetra nisi nec ex fermentum, a mattis velit fringilla. In vel faucibus purus. Vivamus hendrerit, lorem eget convallis lobortis, lorem sem suscipit libero, at tincidunt massa mauris in lacus.\r\n\r\nPhasellus rhoncus tincidunt mauris, sodales faucibus metus maximus ut. Donec id consequat felis, at commodo sapien. Fusce laoreet, felis a pellentesque fermentum, libero velit aliquam quam, sit amet malesuada tellus orci et odio. Praesent tincidunt quis felis quis venenatis. Aenean pulvinar iaculis massa quis sagittis. Etiam vitae suscipit lacus. Quisque nunc ligula, porta in dolor ut, hendrerit pulvinar eros. Aenean imperdiet libero metus.\r\n\r\nAenean vestibulum odio ac quam aliquet blandit. Vestibulum molestie sapien nec ligula tempus faucibus. Phasellus et libero quam. Etiam vitae quam fermentum, venenatis ex id, porttitor ipsum. Morbi eros nisi, lobortis vel ante quis, molestie fringilla metus. Nam ultrices feugiat erat, id scelerisque ligula tempor vel. Fusce ut massa congue sem mattis egestas molestie ultrices nibh. Praesent fermentum mauris in magna molestie imperdiet. Nam convallis velit et aliquet aliquet. Vestibulum sed maximus lacus. Ut pretium nulla ut suscipit pharetra. Etiam auctor ipsum id nibh euismod pretium. Phasellus in cursus metus. Nam ligula diam, feugiat vulputate nisi dignissim, dictum vehicula ante. Praesent eleifend eros eros, et viverra nisi consectetur at. Nulla quis mauris at velit euismod convallis sit amet sit amet nisl.\r\n\r\n', '2019-01-11 09:36:28');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
