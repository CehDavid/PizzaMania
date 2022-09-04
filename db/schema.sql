-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Gép: pelda_host:3306
-- Létrehozás ideje: 2022. Sze 04. 11:08
-- Kiszolgáló verziója: 8.0.30
-- PHP verzió: 8.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `test_db`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `addresses`
--

CREATE TABLE `addresses` (
  `id` int NOT NULL,
  `address` varchar(255) NOT NULL,
  `info` varchar(255) NOT NULL,
  `userId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `addresses`
--

INSERT INTO `addresses` (`id`, `address`, `info`, `userId`) VALUES
(7, 'Darabos utca 10', '8. em 33. aj', 6);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `orderDetails`
--

CREATE TABLE `orderDetails` (
  `id` int NOT NULL,
  `orderId` varchar(255) NOT NULL,
  `productId` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `orderDetails`
--

INSERT INTO `orderDetails` (`id`, `orderId`, `productId`) VALUES
(4, '62e7b9920a25b', 2),
(5, '62e7b9920a25b', 9),
(6, '62e7c08cf009f', 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `orders`
--

CREATE TABLE `orders` (
  `id` varchar(255) NOT NULL,
  `addressId` int NOT NULL,
  `comment` text NOT NULL,
  `payment` tinyint(1) NOT NULL,
  `date` date NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `orders`
--

INSERT INTO `orders` (`id`, `addressId`, `comment`, `payment`, `date`, `status`) VALUES
('62e7b9920a25b', 6, '', 1, '2022-08-01', 'Elküldve'),
('62e7c08cf009f', 6, '', 1, '2022-08-01', 'Elküldve');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `typeId` int NOT NULL,
  `discription` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `price` int NOT NULL,
  `isActive` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `products`
--

INSERT INTO `products` (`id`, `name`, `typeId`, `discription`, `price`, `isActive`) VALUES
(2, 'Sonkás Pizza', 1, 'pizzaszósz alap, sonka, dupla sajt', 1890, 1),
(3, 'Húsimádó pizza', 1, 'pizza szósz, sonka, bacon, kolbász, füstölt sajt', 2090, 1),
(4, 'Sajtkrémes pizza', 1, 'sajtkrémes alap, sonka, jalapeno,sült hagyma, sajt', 1990, 1),
(5, 'Vega pizza', 1, 'pizza szósz, kalifornia paprika, paradicsom, sajt, ruccola', 2090, 1),
(6, 'Mánia burger', 2, 'Dubla húspogácsa, sajt, bacon, tükörtojás, burgerszósz, sajláta, ropogós bucik', 1890, 1),
(7, 'Óriás burger', 2, 'Dubla húspogácsa,dupla sajt,dupla bacon, burgerszósz, sajláta, ropogós bucik', 1890, 1),
(8, 'Sajtburger', 2, 'húspogácsa, dupla sajt, burgerszósz, sajláta, ropogós bucik', 1690, 1),
(9, 'Gyros Tál', 4, 'Nagy adag gyros hús,  20 dkg hasáb burgonya, tartár márstás', 1560, 1),
(10, 'Coca-cola 0,25l', 5, 'Szénsavas kóla ízű üdítőital', 390, 1),
(11, 'Fanta 0,25l', 5, 'Szénsavas, narancs ízű üdítőital', 390, 1),
(12, 'Coca-cola Zéró 0,25l', 5, 'Szénsavas, cukormentes, kóla ízű üdítőital', 390, 1),
(13, 'Sprite Zéró 0,25l', 5, 'Szénsavas, cukormentes, citrom ízű üdítőital', 390, 1),
(14, 'Óriás gyros tál', 4, 'Dupla adag gyros hús, 35 dkg hasáb burgonya,friss saláta, paradicsom, uborka, tartár márstás', 2090, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `productTypes`
--

CREATE TABLE `productTypes` (
  `id` int NOT NULL,
  `type` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `productTypes`
--

INSERT INTO `productTypes` (`id`, `type`, `img`) VALUES
(1, 'Pizzák', 'public/img/pizza-icon.png'),
(2, 'Burgerek', 'public/img/hamburger-icon.png'),
(4, 'Tálak', 'public/img/salad-icon.png'),
(5, 'Üdítők', 'public/img/drink-icon.png');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `user` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `firstName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `lastName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `user`, `password`, `email`, `phone`, `firstName`, `lastName`) VALUES
(6, 'Test', '$2y$10$cIZc7qVuHs6kOCHOoLBYCeRfZAFvfz/rURKGpr/F0ZOERRM6hNgfe', 'test@gmail.com', '06701212312', 'Zsolt', 'Test');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `orderDetails`
--
ALTER TABLE `orderDetails`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `productTypes`
--
ALTER TABLE `productTypes`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT a táblához `orderDetails`
--
ALTER TABLE `orderDetails`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT a táblához `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT a táblához `productTypes`
--
ALTER TABLE `productTypes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
