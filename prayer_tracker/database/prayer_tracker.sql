-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 20, 2025 at 07:08 PM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prayer_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `status` varchar(11) NOT NULL DEFAULT 'Active',
  `role` varchar(11) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `password_hash`, `email`, `status`, `role`) VALUES
(1, 'Administrator', '$2y$10$tSwJcUEOud5MIjwhLwkWseU3.VxC7q/QK7ySem4LQE2RCoKqNyTvy', 'admin@gmail.com', 'Active', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `guidance`
--

CREATE TABLE `guidance` (
  `guidance_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `type` enum('text','video') DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prayer`
--

CREATE TABLE `prayer` (
  `prayer_id` int(11) NOT NULL,
  `prayer_name` enum('Fajr','Dhuhr','Asr','Maghrib','Isha') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prayer_records`
--

CREATE TABLE `prayer_records` (
  `record_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `prayer_id` int(11) DEFAULT NULL,
  `status` enum('completed','qaza') DEFAULT 'qaza',
  `create_date` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `guidance`
--
ALTER TABLE `guidance`
  ADD PRIMARY KEY (`guidance_id`);

--
-- Indexes for table `prayer`
--
ALTER TABLE `prayer`
  ADD PRIMARY KEY (`prayer_id`);

--
-- Indexes for table `prayer_records`
--
ALTER TABLE `prayer_records`
  ADD PRIMARY KEY (`record_id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`date`,`prayer_id`),
  ADD KEY `namaz_id` (`prayer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `guidance`
--
ALTER TABLE `guidance`
  MODIFY `guidance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prayer`
--
ALTER TABLE `prayer`
  MODIFY `prayer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prayer_records`
--
ALTER TABLE `prayer_records`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `prayer_records`
--
ALTER TABLE `prayer_records`
  ADD CONSTRAINT `prayer_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `prayer_records_ibfk_2` FOREIGN KEY (`prayer_id`) REFERENCES `prayer` (`prayer_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
