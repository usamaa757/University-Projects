-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 11:37 AM
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
-- Database: `art_gallery_prototype`
--

-- --------------------------------------------------------

--
-- Table structure for table `art_items`
--

CREATE TABLE `art_items` (
  `art_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `art_items`
--

INSERT INTO `art_items` (`art_id`, `seller_id`, `name`, `description`, `price`, `image`, `status`) VALUES
(3, 2, 'Car', 'A Running Car at Beach', 6733.00, '22Capture.PNG', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','seller') NOT NULL DEFAULT 'seller'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`) VALUES
(1, 'Administrator', 'admin@gmail.com', '$2y$10$GH4pCLZOg0KE4VK7BwThBuqTmjt2azc39qSs4KVYGte9Ct5N/GGr.', 'admin'),
(2, 'Asad', 'asad@gmail.com', '$2y$10$IRDuJYjkn5W6eai8tccmYuU2Ra/BInnGBq/n2x6uQ94Q4If.I3M.y', 'seller');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `art_items`
--
ALTER TABLE `art_items`
  ADD PRIMARY KEY (`art_id`),
  ADD KEY `seller_id` (`seller_id`);

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
-- AUTO_INCREMENT for table `art_items`
--
ALTER TABLE `art_items`
  MODIFY `art_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `art_items`
--
ALTER TABLE `art_items`
  ADD CONSTRAINT `art_items_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
