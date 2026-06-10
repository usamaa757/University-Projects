-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 20, 2025 at 12:43 PM
-- Server version: 11.7.2-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `msgm_clothing_brand`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `email`, `phone`, `password`, `registration_date`) VALUES
(1, 'Sajjal Noor', 'sajjalnoor381@gmail.com', '123', '$2y$10$B0WnmslDxoyIyIIZKzufOevUDJAHX4zRm4A3Sky1dnsqGx78B7.WG', '2024-11-21 18:55:06'),
(2, 'Administrator', 'admin@gmail.com', '0302019385', '$2y$10$y3GN7SOWShm23o8.bLAcjeeH9LnxDUvTbA7QBsLiiQue8Cw7RsCKK', '2024-12-22 05:54:12');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_item_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cloth_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'Men\'s Apparel'),
(2, 'Women\'s Apparel'),
(4, 'Footwear'),
(5, 'Seasonal Collections'),
(6, 'T-Shirt'),
(7, 'Jeans'),
(8, 'Dress'),
(9, 'Shirt'),
(10, 'Jacket'),
(11, 'Sweater');

-- --------------------------------------------------------

--
-- Table structure for table `cloths`
--

CREATE TABLE `cloths` (
  `cloth_id` int(11) NOT NULL,
  `category_id` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `size` varchar(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cloths`
--

INSERT INTO `cloths` (`cloth_id`, `category_id`, `description`, `price`, `quantity`, `size`, `image_url`) VALUES
(23, '1', 'very nice', '340.00', 1, 'M', 'uploads/erd plantuml.png'),
(24, '4', 'Nice boots', '400.00', 20, 'S', 'uploads/Capture.PNG');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `text` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `title`, `text`, `created_at`) VALUES
(6, 4, 'Nice Brand', 'I like it very much', '2025-07-20 06:19:04');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `cloth_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `total_price` varchar(255) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('Unpaid','Paid') NOT NULL DEFAULT 'Unpaid',
  `order_status` enum('Pending','Shipped','Completed','Canceled') NOT NULL DEFAULT 'Pending',
  `ship_address` varchar(255) NOT NULL,
  `dispatched_at` datetime DEFAULT NULL,
  `out_for_delivery_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `estimated_delivery_time` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `cloth_id`, `quantity`, `payment_method`, `total_price`, `order_date`, `payment_status`, `order_status`, `ship_address`, `dispatched_at`, `out_for_delivery_at`, `delivered_at`, `estimated_delivery_time`) VALUES
(13, 4, 23, 3, '', '1020', '2025-07-20 06:31:55', 'Paid', 'Completed', 'Karachi', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `email`, `password`, `location`, `phone`, `created_at`) VALUES
(4, 'Sajjal Ahmad', 'Sajjalnoor381@gmail.com', '$2y$10$LozUozKDngZ5Ri9K6PXa1.e6a8BJtAh3j4zwLFSST4rOFAjp0Q3Zm', 'Lahore', '3030203234', '2025-07-20 06:17:34'),
(5, 'Asad Ahmad', 'asad@gmail.com', '$2y$10$LozUozKDngZ5Ri9K6PXa1.e6a8BJtAh3j4zwLFSST4rOFAjp0Q3Zm', 'Lahore', 'ad', '2025-07-20 06:55:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_item_id`),
  ADD KEY `plant_id` (`cloth_id`),
  ADD KEY `user_id_ibfk_1` (`user_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `cloths`
--
ALTER TABLE `cloths`
  ADD PRIMARY KEY (`cloth_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`user_name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `cloths`
--
ALTER TABLE `cloths`
  MODIFY `cloth_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`cloth_id`) REFERENCES `cloths` (`cloth_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_id_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
