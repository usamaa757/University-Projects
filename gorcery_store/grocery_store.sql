-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 06, 2025 at 08:47 PM
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
-- Database: `grocery_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `subscription_status` enum('subscribed','unsubscribed') DEFAULT 'subscribed',
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `name`, `email`, `phone`, `subscription_status`, `date_added`) VALUES
(1, 'Zakir', 'webaccess757@gmail.com', '03089876523', 'subscribed', '2024-11-17 06:35:30');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`employee_id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Afnan', 'afnan@gmail.com', '$2y$10$axBmk.xiUn9eKVx3QAKCT.GbYpEs8sq83iSn2d3EcYVt3N8/tdZ.W', '2024-11-16 19:33:15');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `expense_type` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `expense_date` date DEFAULT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `expense_type`, `amount`, `expense_date`, `description`) VALUES
(1, 'abc', 12000.00, '2024-11-17', 'df'),
(2, 'ab', 13000.00, '2024-11-17', 'dfd');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `category`, `price`, `quantity`, `created_at`) VALUES
(2, 'Apple', 'Fruits', 2.99, 100, '2024-11-16 20:13:21'),
(3, 'Banana', 'Fruits', 1.49, 150, '2024-11-16 20:13:21'),
(4, 'Carrot', 'Vegetables', 0.99, 200, '2024-11-16 20:13:21'),
(5, 'Tomato', 'Vegetables', 1.29, 180, '2024-11-16 20:13:21'),
(6, 'Milk', 'Dairy', 1.99, 120, '2024-11-16 20:13:21'),
(7, 'Cheese', 'Dairy', 4.99, 50, '2024-11-16 20:13:21'),
(8, 'Yogurt', 'Dairy', 2.49, 80, '2024-11-16 20:13:21'),
(9, 'Bread', 'Bakery', 1.79, 220, '2024-11-16 20:13:21'),
(10, 'Butter', 'Dairy', 3.49, 60, '2024-11-16 20:13:21'),
(11, 'Eggs', 'Dairy', 2.99, 150, '2024-11-16 20:13:21'),
(12, 'Chicken', 'Meat', 5.99, 100, '2024-11-16 20:13:21'),
(13, 'Beef', 'Meat', 7.99, 80, '2024-11-16 20:13:21'),
(14, 'Fish', 'Meat', 8.49, 50, '2024-11-16 20:13:21'),
(15, 'Rice', 'Grains', 3.99, 200, '2024-11-16 20:13:21'),
(16, 'Pasta', 'Grains', 2.49, 150, '2024-11-16 20:13:21'),
(17, 'Flour', 'Baking', 1.89, 300, '2024-11-16 20:13:21'),
(18, 'Sugar', 'Baking', 1.29, 250, '2024-11-16 20:13:21'),
(19, 'Salt', 'Spices', 0.99, 500, '2024-11-16 20:13:21'),
(20, 'Pepper', 'Spices', 2.99, 200, '2024-11-16 20:13:21'),
(21, 'Cereal', 'Breakfast', 3.49, 180, '2024-11-16 20:13:21'),
(22, 'Oatmeal', 'Breakfast', 2.99, 150, '2024-11-16 20:13:21'),
(23, 'Juice', 'Beverages', 3.99, 130, '2024-11-16 20:13:21'),
(24, 'Soda', 'Beverages', 1.99, 220, '2024-11-16 20:13:21'),
(25, 'Water', 'Beverages', 0.89, 300, '2024-11-16 20:13:21'),
(26, 'Wine', 'Beverages', 9.99, 70, '2024-11-16 20:13:21'),
(27, 'Potato', 'Vegetable', 120.00, 300, '2024-11-16 20:19:17');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`sale_id`, `product_id`, `quantity`, `sale_date`, `total_amount`) VALUES
(1, 27, 34, '2024-11-17', 4080.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
