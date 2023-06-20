-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 20 2023 г., 22:22
-- Версия сервера: 5.7.25
-- Версия PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `sberp`
--

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `_create` int(10) NOT NULL,
  `_sort` int(10) NOT NULL,
  `_modify` int(11) NOT NULL,
  `productPrice` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `productId` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `productName` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `productQuantity` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `orderNumber` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `yandexOrderNumber` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `operation` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `callbackCreationDate` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `_create`, `_sort`, `_modify`, `productPrice`, `productId`, `productName`, `productQuantity`, `orderNumber`, `yandexOrderNumber`, `operation`, `status`, `callbackCreationDate`) VALUES
(1, 1687288839, 1687288839, 1687288869, '1200', '10', 'Товар 1', '1', '99c164c5ae5415c650707fa08452489b', '1234567890-098776-234-522', 'deposited', '0', 'Mon Jan 31 21:46:52 MSK 2022');

-- --------------------------------------------------------

--
-- Структура таблицы `orders_log`
--

CREATE TABLE `orders_log` (
  `id` int(11) NOT NULL,
  `orderId` int(11) NOT NULL,
  `_create` int(10) NOT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `_sort` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `_modify` int(11) NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `_REQUEST` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders_log`
--

INSERT INTO `orders_log` (`id`, `orderId`, `_create`, `name`, `_sort`, `_modify`, `data`, `_REQUEST`) VALUES
(1, 1, 1687288840, 'Новый зака № 1 в 20-06-2023 22:20', '1687288840', 1687288840, '{\"data\":{\"productPrice\":1200,\"productId\":\"10\",\"productName\":\"Товар 1\",\"productQuantity\":1,\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\"}}', '{\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\",\"fio\":\"Иван Петров\",\"product\":\"Товар 1\",\"quantity\":\"1\"}'),
(2, 1, 1687288841, 'Попытка произвести оплату в 20-06-2023 22:20', '1687288841', 1687288841, '', '{\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\",\"fio\":\"Иван Петров\",\"product\":\"Товар 1\",\"quantity\":\"1\"}'),
(3, 1, 1687288842, 'setOrderData. Изменение данных заказа', '1687288842', 1687288842, '{\"yandexOrderNumber\":\"70906e55-7114-41d6-8332-4609dc6590f4\"}', '{\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\",\"fio\":\"Иван Петров\",\"product\":\"Товар 1\",\"quantity\":\"1\"}'),
(4, 1, 1687288844, 'setOrderData. Данные успешно изменены', '1687288844', 1687288844, '[]', '{\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\",\"fio\":\"Иван Петров\",\"product\":\"Товар 1\",\"quantity\":\"1\"}'),
(5, 1, 1687288845, 'Транзакция была выполнена', '1687288845', 1687288845, '{\"action\":\"success\",\"result\":{\"orderId\":\"70906e55-7114-41d6-8332-4609dc6590f4\",\"formUrl\":\"https://3dsec.sberbank.ru/payment/merchants/test/payment_ru.html?mdOrder=70906e55-7114-41d6-8332-4609dc6590f4\"}}', '{\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\",\"fio\":\"Иван Петров\",\"product\":\"Товар 1\",\"quantity\":\"1\"}'),
(6, 1, 1687288868, 'setOrderData. Изменение данных заказа', '1687288868', 1687288868, '{\"yandexOrderNumber\":\"1234567890-098776-234-522\",\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\",\"operation\":\"deposited\",\"status\":\"0\",\"callbackCreationDate\":\"Mon Jan 31 21:46:52 MSK 2022\"}', '{\"mdOrder\":\"1234567890-098776-234-522\",\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\",\"operation\":\"deposited\",\"callbackCreationDate\":\"Mon Jan 31 21:46:52 MSK 2022\",\"status\":\"0\"}'),
(7, 1, 1687288870, 'setOrderData. Данные успешно изменены', '1687288870', 1687288870, '[]', '{\"mdOrder\":\"1234567890-098776-234-522\",\"orderNumber\":\"99c164c5ae5415c650707fa08452489b\",\"operation\":\"deposited\",\"callbackCreationDate\":\"Mon Jan 31 21:46:52 MSK 2022\",\"status\":\"0\"}');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `orders_log`
--
ALTER TABLE `orders_log`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `orders_log`
--
ALTER TABLE `orders_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
