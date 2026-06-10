-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2024 at 07:58 PM
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
-- Database: `employee`
--

-- --------------------------------------------------------

--
-- Table structure for table `emp_table`
--

CREATE TABLE `emp_table` (
  `Emp_ID` varchar(50) NOT NULL,
  `Emp_Name` varchar(50) DEFAULT NULL,
  `Emp_Address` varchar(50) DEFAULT NULL,
  `Emp_Designation` varchar(50) DEFAULT NULL,
  `Emp_Salary` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_table`
--

INSERT INTO `emp_table` (`Emp_ID`, `Emp_Name`, `Emp_Address`, `Emp_Designation`, `Emp_Salary`) VALUES
('id1', 'Kamran', 'Lahore', 'Manager', 67000),
('id2', 'Bilal', 'Shaikho Pura', 'Student', 23000);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emp_table`
--
ALTER TABLE `emp_table`
  ADD PRIMARY KEY (`Emp_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
