-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2018 at 04:51 PM
-- Server version: 10.1.33-MariaDB
-- PHP Version: 7.1.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fondomerende`
--

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE `actions` (
  `id` int(11) NOT NULL,
  `user_id` int(2) NOT NULL,
  `command_id` int(2) NOT NULL,
  `snack_id` int(2) NOT NULL,
  `snack_quantity` int(2) NOT NULL,
  `funds_amount` decimal(3,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commands`
--

CREATE TABLE `commands` (
  `id` int(2) NOT NULL,
  `en` varchar(15) NOT NULL,
  `it` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `commands`
--

INSERT INTO `commands` (`id`, `en`, `it`) VALUES
(1, 'eat', 'mangia'),
(2, 'buy', 'compra'),
(3, 'supply', 'rifornisci');

-- --------------------------------------------------------

--
-- Table structure for table `commands_alias`
--

CREATE TABLE `commands_alias` (
  `id` int(11) NOT NULL,
  `command_id` int(2) NOT NULL,
  `en_name` varchar(30) NOT NULL,
  `it_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `crates`
--

CREATE TABLE `crates` (
  `outflow_id` int(11) NOT NULL,
  `snack_id` int(2) NOT NULL,
  `snack_quantity` int(3) NOT NULL,
  `price_per_snack` decimal(5,2) NOT NULL,
  `expiration` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `eaten`
--

CREATE TABLE `eaten` (
  `snack_id` int(2) NOT NULL,
  `user_id` int(2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eaten`
--

INSERT INTO `eaten` (`snack_id`, `user_id`, `quantity`, `updated_at`) VALUES
(3, 1, 0, '2018-10-17 12:55:11');

-- --------------------------------------------------------

--
-- Table structure for table `fund_funds`
--

CREATE TABLE `fund_funds` (
  `total` decimal(5,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fund_funds`
--

INSERT INTO `fund_funds` (`total`, `updated_at`) VALUES
('10.00', '2018-10-17 14:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `inflows`
--

CREATE TABLE `inflows` (
  `id` int(11) NOT NULL,
  `user_id` int(2) NOT NULL,
  `amount` decimal(4,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `outflows`
--

CREATE TABLE `outflows` (
  `id` int(11) NOT NULL,
  `amount` decimal(5,2) NOT NULL,
  `snack_id` int(2) NOT NULL,
  `quantity` int(3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `snacks`
--

CREATE TABLE `snacks` (
  `id` int(2) NOT NULL,
  `name` varchar(60) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `snack_per_box` int(2) NOT NULL,
  `is_liquid` bit(1) NOT NULL DEFAULT b'0',
  `expiration_in_days` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `snacks`
--

INSERT INTO `snacks` (`id`, `name`, `price`, `snack_per_box`, `is_liquid`, `expiration_in_days`) VALUES
(1, 'Taralli Coop', '1.99', 12, b'0', 0),
(2, 'Baiocchi', '2.49', 6, b'0', 0),
(3, 'Kinder Bueno', '3.45', 6, b'0', 60);

-- --------------------------------------------------------

--
-- Table structure for table `snacks_stock`
--

CREATE TABLE `snacks_stock` (
  `snack_id` int(2) NOT NULL,
  `quantity` int(3) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `snacks_stock`
--

INSERT INTO `snacks_stock` (`snack_id`, `quantity`, `updated_at`) VALUES
(3, 0, '2018-10-17 14:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(2) NOT NULL,
  `user` varchar(15) NOT NULL,
  `password` varchar(60) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user`, `password`, `name`) VALUES
(1, 'matteobin', '', 'Matteo Bini'),
(2, 'francesco', '', 'Francesco');

-- --------------------------------------------------------

--
-- Table structure for table `users_alias`
--

CREATE TABLE `users_alias` (
  `id` int(11) NOT NULL,
  `user_id` int(2) NOT NULL,
  `name` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users_funds`
--

CREATE TABLE `users_funds` (
  `user_id` int(2) NOT NULL,
  `amount` decimal(4,2) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_funds`
--

INSERT INTO `users_funds` (`user_id`, `amount`, `updated_at`) VALUES
(1, '5.29', '2018-10-15 15:06:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `command_id` (`command_id`),
  ADD KEY `snack_id` (`snack_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `commands`
--
ALTER TABLE `commands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commands_alias`
--
ALTER TABLE `commands_alias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `command-id` (`command_id`);

--
-- Indexes for table `crates`
--
ALTER TABLE `crates`
  ADD PRIMARY KEY (`outflow_id`),
  ADD KEY `snack_id` (`snack_id`);

--
-- Indexes for table `eaten`
--
ALTER TABLE `eaten`
  ADD UNIQUE KEY `snack-id` (`snack_id`),
  ADD UNIQUE KEY `user-id` (`user_id`);

--
-- Indexes for table `fund_funds`
--
ALTER TABLE `fund_funds`
  ADD UNIQUE KEY `total` (`total`);

--
-- Indexes for table `inflows`
--
ALTER TABLE `inflows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user-id` (`user_id`);

--
-- Indexes for table `outflows`
--
ALTER TABLE `outflows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `snack-id` (`snack_id`);

--
-- Indexes for table `snacks`
--
ALTER TABLE `snacks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `snacks_stock`
--
ALTER TABLE `snacks_stock`
  ADD UNIQUE KEY `snack-id` (`snack_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_alias`
--
ALTER TABLE `users_alias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user-id` (`user_id`);

--
-- Indexes for table `users_funds`
--
ALTER TABLE `users_funds`
  ADD UNIQUE KEY `user-id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actions`
--
ALTER TABLE `actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `outflows`
--
ALTER TABLE `outflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `actions`
--
ALTER TABLE `actions`
  ADD CONSTRAINT `actions_ibfk_1` FOREIGN KEY (`command_id`) REFERENCES `commands` (`id`),
  ADD CONSTRAINT `actions_ibfk_2` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`),
  ADD CONSTRAINT `actions_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `commands_alias`
--
ALTER TABLE `commands_alias`
  ADD CONSTRAINT `commands_alias_ibfk_1` FOREIGN KEY (`command_id`) REFERENCES `commands` (`id`);

--
-- Constraints for table `crates`
--
ALTER TABLE `crates`
  ADD CONSTRAINT `crates_ibfk_1` FOREIGN KEY (`outflow_id`) REFERENCES `outflows` (`id`),
  ADD CONSTRAINT `crates_ibfk_2` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`);

--
-- Constraints for table `eaten`
--
ALTER TABLE `eaten`
  ADD CONSTRAINT `eaten_ibfk_1` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`),
  ADD CONSTRAINT `eaten_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `inflows`
--
ALTER TABLE `inflows`
  ADD CONSTRAINT `inflows_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `outflows`
--
ALTER TABLE `outflows`
  ADD CONSTRAINT `outflows_ibfk_1` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`);

--
-- Constraints for table `snacks_stock`
--
ALTER TABLE `snacks_stock`
  ADD CONSTRAINT `snacks_stock_ibfk_1` FOREIGN KEY (`snack_id`) REFERENCES `snacks` (`id`);

--
-- Constraints for table `users_alias`
--
ALTER TABLE `users_alias`
  ADD CONSTRAINT `users_alias_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `users_funds`
--
ALTER TABLE `users_funds`
  ADD CONSTRAINT `users_funds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
