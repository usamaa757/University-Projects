-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 10, 2025 at 10:58 AM
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
-- Database: `cosmetic_store_prototype`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `second_name` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `postcode` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `first_name`, `second_name`, `address`, `password`, `email`, `phone_number`, `town`, `region`, `postcode`, `country`) VALUES
(3, 'Administrator', '1234512345678', '1', '$2y$10$Ht94klT2Qj6Wqb4alafMb.vsw6ydTB22cV4qkMRAPcNa3bukdkSym', 'admin@gmail.com', '3086391012', 'Lahore', 'NY', '', 'United States');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `second_name` varchar(50) DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(100) DEFAULT NULL,
  `town` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `postcode` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `second_name`, `address`, `password`, `email`, `phone_number`, `town`, `region`, `postcode`, `country`) VALUES
(1, 'Administrator', NULL, NULL, '$2y$10$6koACAErUMroPsfV.wU/Uetyz.ZZ/qiPnbiQJ53sMBQBDv3dQs5mm', 'admin@gmail.com', NULL, NULL, NULL, NULL, NULL),
(2, 'Usama Ahmad', '1234512345678', '1', '$2y$10$6koACAErUMroPsfV.wU/Uetyz.ZZ/qiPnbiQJ53sMBQBDv3dQs5mm', 'usama@gmail.com', '', 'Lahore', 'NY', '', 'United States'),
(24, 'Afnan Ahmad', '1234531212367', 'adf', '$2y$10$7I.MY2A2PB5k8xyEpoin7uJ27hU5pdIv0zYHEsd2m0oXgMJYMr7ay', 'afnan@gmail.com', '12321312321', 'dfd', 'df', '', 'United States'),
(25, 'Usama Ahmad', '1234531234567', '1', '$2y$10$SWa4NB1Am4VbzgvsSrzap.w2CwIRk/X00CgqUXeTINq6MPMGLLx82', 'louci786@gmail.com', '34343', 'Lahore', 'NY', '', 'United States');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

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
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
