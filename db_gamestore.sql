-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2024 at 01:02 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_gamestore`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', 'admin', '2024-11-27 23:32:22');

-- --------------------------------------------------------

--
-- Table structure for table `discount_events`
--

CREATE TABLE `discount_events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL,
  `discount_value` decimal(10,2) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_events`
--

INSERT INTO `discount_events` (`id`, `event_name`, `discount_type`, `discount_value`, `start_date`, `end_date`) VALUES
(3, 'asu', 'percentage', 50.00, '2024-11-24 21:04:00', '2024-12-29 21:04:00');

-- --------------------------------------------------------

--
-- Table structure for table `discount_event_games`
--

CREATE TABLE `discount_event_games` (
  `event_id` int(11) NOT NULL,
  `game_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount_event_games`
--

INSERT INTO `discount_event_games` (`event_id`, `game_id`) VALUES
(3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `description`, `price`, `stock`, `created_at`, `image`) VALUES
(4, 'Darksouls 3', 'murah', 499000.00, 13, '2024-11-28 00:17:36', 'ds.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `game_id`, `amount`, `payment_method`, `status`, `total_amount`, `transaction_date`) VALUES
(1, 1, 1, 998000, 'credit_card', 'completed', 998000.00, '2024-11-28 01:00:17'),
(2, 1, 4, 998000, 'credit_card', 'pending', 998000.00, '2024-11-28 01:00:17'),
(3, 5, 1, 499000, 'credit_card', 'completed', 499000.00, '2024-11-28 09:33:49'),
(4, 8, 1, 5491000, 'bank_transfer', 'completed', 5491000.00, '2024-11-28 09:44:44'),
(5, 8, 4, 5491000, 'bank_transfer', 'completed', 5491000.00, '2024-11-28 09:44:44'),
(6, 8, 4, 698000, 'credit_card', 'completed', 698000.00, '2024-11-28 09:45:35'),
(7, 8, 7, 698000, 'credit_card', 'pending', 698000.00, '2024-11-28 09:45:35'),
(8, 8, 4, 499000, 'credit_card', 'completed', 499000.00, '2024-11-28 09:46:22'),
(9, 9, 4, 499000, 'credit_card', 'completed', 499000.00, '2024-12-02 08:15:32'),
(10, 9, 4, 249500, 'bank_transfer', 'completed', 249500.00, '2024-12-02 08:25:16'),
(11, 1, 7, 149000, 'e_wallet', 'completed', 149000.00, '2024-12-02 10:40:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `membership_level` varchar(50) DEFAULT 'Regular',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `membership_level`, `created_at`) VALUES
(1, 'user', 'user@example.com', 'user', 'Regular', '2024-11-27 23:32:34'),
(3, 'Painah', 'exam@gmail.com', 'painah', 'Regular', '2024-11-27 23:56:33'),
(5, 'user2', 'use2r@example.com', 'user2', 'Regular', '2024-11-28 09:33:06'),
(8, 'user123', 'user12@example.com', 'user123', 'Admin', '2024-11-27 23:32:34'),
(9, 'manto', 'manto@elek', '123', 'Regular', '2024-12-02 08:12:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `discount_events`
--
ALTER TABLE `discount_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discount_event_games`
--
ALTER TABLE `discount_event_games`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `game_id` (`game_id`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `discount_events`
--
ALTER TABLE `discount_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `discount_event_games`
--
ALTER TABLE `discount_event_games`
  ADD CONSTRAINT `discount_event_games_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
