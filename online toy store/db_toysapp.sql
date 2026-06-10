-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 29, 2024 at 11:09 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_toysapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `password`, `email`, `contact`) VALUES
(1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 'admin@admin.com', 'none');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `order_id` int(11) NOT NULL,
  `toyname` text NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`order_id`, `toyname`, `quantity`, `price`) VALUES
(5, 'Car', 4, 1500),
(5, 'Truck', 4, 500),
(6, 'Doll', 1, 1000),
(7, 'Doll', 1, 1000),
(8, 'Doll', 1, 1000),
(9, 'Doll', 1, 1000),
(10, 'Doll', 1, 1000),
(11, 'Doll', 1, 1000),
(12, 'Doll', 1, 1000),
(13, 'Doll', 1, 1000),
(14, 'Doll', 1, 1000),
(15, 'Doll', 1, 1000),
(16, 'Teddy Bear', 1, 1800),
(16, 'Rubix', 2, 200),
(17, 'Doll', 1, 1000),
(18, 'Doll', 2, 1000),
(18, 'Car', 1, 1500);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `catid` int(11) NOT NULL,
  `catname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`catid`, `catname`) VALUES
(1, 'Dolls'),
(2, 'Cars'),
(3, 'Stuffed Toys'),
(4, 'Puzzles');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shipinfo` text NOT NULL,
  `paymode` text NOT NULL,
  `orderdate` date NOT NULL,
  `totalamt` int(11) NOT NULL,
  `orderstatus` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `shipinfo`, `paymode`, `orderdate`, `totalamt`, `orderstatus`) VALUES
(18, 7, 'City Plaza, Iqbal Town, Karachi, Sindh.', 'Online', '2024-05-01', 3500, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payid` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `paystatus` varchar(100) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payid`, `order_id`, `paystatus`, `amount`) VALUES
(5, 5, 'paid', 0);

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `reqid` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `toyname` varchar(100) NOT NULL,
  `toypicture` varchar(100) NOT NULL,
  `toydesc` text NOT NULL,
  `feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`reqid`, `user_id`, `toyname`, `toypicture`, `toydesc`, `feedback`) VALUES
(1, 7, 'Barbie', 'images/barbie.jpg', 'Barbie Doll', 'Your suggestion has been noted');

-- --------------------------------------------------------

--
-- Table structure for table `toys`
--

CREATE TABLE `toys` (
  `id` int(11) NOT NULL,
  `toyid` varchar(50) NOT NULL,
  `toyname` varchar(100) NOT NULL,
  `toyprice` varchar(50) NOT NULL,
  `toypicture` varchar(100) NOT NULL,
  `catid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `costprice` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `toys`
--

INSERT INTO `toys` (`id`, `toyid`, `toyname`, `toyprice`, `toypicture`, `catid`, `quantity`, `costprice`) VALUES
(4, 'TOY3', 'Truck', '500', 'images/truck.jpg', 2, 15, 400),
(5, 'TOY4', 'Doll', '1000', 'images/doll.jpg', 1, 18, 850),
(7, 'TOY5', 'Teddy Bear', '1800', 'images/teddybear.jpg', 3, 20, 1600),
(8, 'TOY2', 'Car', '1500', 'images/car.jpg', 2, 19, 1300),
(9, 'TOY1', 'Rubix', '200', 'images/cubix.jpg', 4, 10, 170);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `contact`) VALUES
(4, 'Aida', '5f4dcc3b5aa765d61d8327deb882cf99', 'aidamir@customer.com', '1337111114'),
(7, 'Customer', '5f4dcc3b5aa765d61d8327deb882cf99', 'customer@customer.com', '123456798');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`catid`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payid`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`reqid`);

--
-- Indexes for table `toys`
--
ALTER TABLE `toys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `catid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `reqid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `toys`
--
ALTER TABLE `toys`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
