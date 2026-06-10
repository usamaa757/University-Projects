-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 03, 2024 at 10:18 AM
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
-- Database: `chairperson_selection`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `password`, `name`, `email`) VALUES
('admin123', '$2y$10$kR0iV3EvygRxoHP6NemPJOISxyorDy1PQ56b5/9pajyqBKic0qVK6', 'Admin Name', 'admin@example.com');

-- --------------------------------------------------------

--
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `candidate_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `party` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `gender` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`candidate_id`, `name`, `party`, `department`, `description`, `created_at`, `status`, `gender`) VALUES
(1, 'Usama', 'PPP', 'IT', 'dfasdfds', '2024-07-11 17:13:53', 'Approved', 'Male'),
(2, 'Afnan', 'abc', 'Chemistry', 'pls vote me', '2024-07-13 22:04:59', 'Approved', 'Male'),
(3, 'Zakir', 'abc', 'Maths', 'pls', '2024-07-13 22:15:10', 'Rejected', 'Male');

-- --------------------------------------------------------

--
-- Table structure for table `voter_registration`
--

CREATE TABLE `voter_registration` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `department` varchar(100) NOT NULL,
  `registration_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `voter_id` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `plain_password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voter_registration`
--

INSERT INTO `voter_registration` (`id`, `name`, `gender`, `student_id`, `department`, `registration_status`, `voter_id`, `password`, `plain_password`) VALUES
(1, 'Usama', 'Male', '101', 'IT', 'approved', 'voter0011', '$2y$10$cKkTlWikrnU1eQfgTUDRS.8IXI9MpVOuWX4PYAe0gm6TJS8oIs2n2', '16c2b422'),
(2, 'Waleed Ullah', 'Male', '234', 'Physics', 'approved', 'voter4104', '$2y$10$YxG4MsokBrP.V4JRmzKtkeypLKANLX8aA/1P7RzHa..KvPJpMVQKS', '8638d1a4'),
(3, 'Umar', 'Male', '106', 'Maths', 'approved', 'voter8822', '$2y$10$3n0FV2t9Bg/tbTPzAEpG6emZlV2Rt/2A7YTGns3vbGC/aQcTgCuNG', '77a9882c'),
(4, 'Sami', 'Male', '5457', 'Physics', 'approved', 'voter8183', '$2y$10$E1unaSJJo4.Ym4ik3kzZ6ekHCaZulFNTk5c3Jvl5bXigCwLt8Fhz6', '00d8e561'),
(5, 'Afnan', 'Male', '119', 'Physics', 'approved', 'voter4873', '$2y$10$Cgw1uGlm0fMOJvPa39BFAeMBQtMnbFvR/CFdniX44QMvsg5ZlaIGq', '5aab7a97'),
(6, 'Amir', 'Male', '769', 'Chemistry', 'approved', 'voter6482', '$2y$10$BClBkCwtQ2iD7UoX7XRk4.qBdiuV/imn4ezZ2geT4dzDBTfslh/.m', '373a7729');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL,
  `voter_id` varchar(50) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `vote_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`vote_id`, `voter_id`, `candidate_id`, `vote_time`) VALUES
(3, 'voter0011', 1, '2024-07-12 19:43:08'),
(4, 'voter4104', 3, '2024-07-13 22:26:18'),
(6, 'voter4873', 3, '2024-07-14 04:03:35'),
(9, 'voter6482', 1, '2024-07-18 14:30:39');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`candidate_id`);

--
-- Indexes for table `voter_registration`
--
ALTER TABLE `voter_registration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`vote_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `voter_registration`
--
ALTER TABLE `voter_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `vote_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
