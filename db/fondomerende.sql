-- phpMyAdmin SQL Dump
-- version 4.8.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2018 at 02:45 PM
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
  `snack_id` int(2) DEFAULT NULL,
  `snack_quantity` int(2) DEFAULT NULL,
  `funds_amount` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `commands`
--

CREATE TABLE `commands` (
  `id` int(2) NOT NULL,
  `name` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `commands`
--

INSERT INTO `commands` (`id`, `name`) VALUES
(4, 'add snack'),
(6, 'add user'),
(2, 'buy'),
(3, 'deposit'),
(1, 'eat'),
(5, 'edit snack'),
(7, 'edit user');

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
  `id` int(11) NOT NULL,
  `snack_id` int(2) NOT NULL,
  `user_id` int(2) NOT NULL,
  `quantity` int(11) DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `eaten`
--

INSERT INTO `eaten` (`id`, `snack_id`, `user_id`, `quantity`, `updated_at`) VALUES
(1, 3, 1, 0, '2018-10-22 07:49:06');

-- --------------------------------------------------------

--
-- Table structure for table `edits`
--

CREATE TABLE `edits` (
  `id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `column_name` varchar(30) NOT NULL,
  `old_s_value` varchar(60) DEFAULT NULL,
  `new_s_value` varchar(60) DEFAULT NULL,
  `old_d_value` decimal(4,2) DEFAULT NULL,
  `new_d_value` decimal(4,2) DEFAULT NULL,
  `old_i_value` int(4) DEFAULT NULL,
  `new_i_value` int(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `fund_funds`
--

CREATE TABLE `fund_funds` (
  `total` decimal(5,2) NOT NULL DEFAULT '0.00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `fund_funds`
--

INSERT INTO `fund_funds` (`total`, `updated_at`) VALUES
('10.00', '2018-10-22 07:48:13');

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
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `snacks_per_box` int(2) NOT NULL,
  `expiration_in_days` int(4) NOT NULL,
  `is_liquid` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `snacks`
--

INSERT INTO `snacks` (`id`, `name`, `price`, `snacks_per_box`, `expiration_in_days`, `is_liquid`) VALUES
(1, 'Taralli Coop', '1.99', 12, 0, 0),
(2, 'Baiocchi', '2.49', 6, 0, 0),
(3, 'Kinder Bueno', '3.45', 6, 60, 0);

-- --------------------------------------------------------

--
-- Table structure for table `snacks_stock`
--

CREATE TABLE `snacks_stock` (
  `snack_id` int(2) NOT NULL,
  `quantity` int(3) DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `snacks_stock`
--

INSERT INTO `snacks_stock` (`snack_id`, `quantity`, `updated_at`) VALUES
(3, 0, '2018-10-22 07:48:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `friendly_name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `friendly_name`) VALUES
(1, 'matteobin', '', 'Matteo Bini'),
(2, 'francesco', '', 'Francesco');

-- --------------------------------------------------------

--
-- Table structure for table `users_funds`
--

CREATE TABLE `users_funds` (
  `user_id` int(2) NOT NULL,
  `amount` decimal(4,2) NOT NULL DEFAULT '0.00',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users_funds`
--

INSERT INTO `users_funds` (`user_id`, `amount`, `updated_at`) VALUES
(1, '5.29', '2018-10-22 12:11:58');

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `eaten_ibfk_1` (`snack_id`),
  ADD KEY `eaten_ibfk_2` (`user_id`);

--
-- Indexes for table `edits`
--
ALTER TABLE `edits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_id` (`action_id`);

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
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `outflows`
--
ALTER TABLE `outflows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `snack_id` (`snack_id`);

--
-- Indexes for table `snacks`
--
ALTER TABLE `snacks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `snacks_stock`
--
ALTER TABLE `snacks_stock`
  ADD UNIQUE KEY `snack_id` (`snack_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users_funds`
--
ALTER TABLE `users_funds`
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `actions`
--
ALTER TABLE `actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eaten`
--
ALTER TABLE `eaten`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `edits`
--
ALTER TABLE `edits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inflows`
--
ALTER TABLE `inflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `outflows`
--
ALTER TABLE `outflows`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `snacks`
--
ALTER TABLE `snacks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `edits`
--
ALTER TABLE `edits`
  ADD CONSTRAINT `edits_ibfk_1` FOREIGN KEY (`action_id`) REFERENCES `actions` (`id`);

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
-- Constraints for table `users_funds`
--
ALTER TABLE `users_funds`
  ADD CONSTRAINT `users_funds_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
