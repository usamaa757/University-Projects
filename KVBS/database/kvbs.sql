-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2025 at 10:44 PM
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
-- Database: `kvbs`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `vaccine_name` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `preferred_date` date DEFAULT NULL,
  `preferred_time` time DEFAULT NULL,
  `status` enum('pending','confirmed','rejected','completed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `worker_id` int(11) DEFAULT NULL,
  `child_id` int(11) NOT NULL,
  `vaccinated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `children`
--

CREATE TABLE `children` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `child_name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `city` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','parent','worker') NOT NULL DEFAULT 'parent',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `city`, `address`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@gmail.com', '$2y$10$BnPmQyKjYfU5..xSdDOrluvHKo1MsU7FC4xIk55ugxcRJm.QaS9SO', '123', '', '1\r\n2', 'admin', '2025-10-11 16:52:17'),
(9, 'asif Kharal', 'asif@gmail.com', '$2y$10$zNvbVLEGbx4OBIF23.hH2OeiDS3gcCSv8WRuD56L0CGuhf3reXT8i', '3086391012', '', 'Lahore', 'worker', '2025-10-11 17:42:33'),
(11, 'Asad Ahmad', 'asad@gmail.com', '$2y$10$TGhuwcqP7TtZ5AtAM7yTtek1XrRp/JFHpY99EyM3d2kuiH5LVxEAi', '3086391012', '', '1', 'worker', '2025-10-13 17:04:39');

-- --------------------------------------------------------

--
-- Table structure for table `vaccines`
--

CREATE TABLE `vaccines` (
  `id` int(11) NOT NULL,
  `vaccine_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `age_group` varchar(100) DEFAULT NULL,
  `dose_count` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vaccines`
--

INSERT INTO `vaccines` (`id`, `vaccine_name`, `description`, `age_group`, `dose_count`, `created_at`) VALUES
(3, 'BCG', 'Protects against tuberculosis (TB)', 'At birth', 1, '2025-10-12 11:36:54'),
(4, 'Hepatitis B', 'Prevents hepatitis B infection', 'At birth, 6 weeks, 10 weeks, 14 weeks', 3, '2025-10-12 11:36:54'),
(5, 'Polio (OPV)', 'Protects against poliomyelitis', 'At birth, 6 weeks, 10 weeks, 14 weeks', 4, '2025-10-12 11:36:54'),
(6, 'DTP', 'Protects against diphtheria, tetanus, and pertussis', '6 weeks, 10 weeks, 14 weeks', 3, '2025-10-12 11:36:54'),
(7, 'Hib', 'Prevents Haemophilus influenzae type b infection', '6 weeks, 10 weeks, 14 weeks', 3, '2025-10-12 11:36:54'),
(8, 'Rotavirus', 'Protects against rotavirus diarrhea', '6 weeks, 10 weeks', 2, '2025-10-12 11:36:54'),
(9, 'Pneumococcal (PCV)', 'Prevents pneumococcal pneumonia and meningitis', '6 weeks, 10 weeks, 14 weeks', 3, '2025-10-12 11:36:54'),
(10, 'Measles', 'Prevents measles infection', '9 months', 1, '2025-10-12 11:36:54'),
(11, 'MMR', 'Protects against measles, mumps, and rubella', '15 months', 2, '2025-10-12 11:36:54'),
(12, 'Typhoid', 'Protects against typhoid fever', '2 years', 1, '2025-10-12 11:36:54'),
(13, 'Hepatitis A', 'Prevents hepatitis A infection', '12 months', 2, '2025-10-12 11:36:54'),
(14, 'Influenza', 'Protects against seasonal flu', '6 months and older (annual)', 1, '2025-10-12 11:36:54'),
(15, 'Chickenpox (Varicella)', 'Protects against varicella (chickenpox)', '15 months', 2, '2025-10-12 11:36:54'),
(16, 'Meningococcal', 'Prevents meningococcal meningitis', '9 months', 1, '2025-10-12 11:36:54'),
(17, 'Japanese Encephalitis', 'Prevents Japanese encephalitis virus infection', '12 months', 2, '2025-10-12 11:36:54'),
(18, 'HPV', 'Prevents human papillomavirus infection (cervical cancer)', '9–14 years', 2, '2025-10-12 11:36:54'),
(19, 'Tdap', 'Boosts protection against tetanus, diphtheria, and pertussis', '10–12 years', 1, '2025-10-12 11:36:54'),
(20, 'Typhoid Booster', 'Booster dose for typhoid vaccine', '5 years', 1, '2025-10-12 11:36:54'),
(21, 'Polio Booster (IPV)', 'Boosts immunity against poliomyelitis', '5 years', 1, '2025-10-12 11:36:54'),
(22, 'COVID-19 Pediatric', 'Protects children from COVID-19 infection', '5 years and above', 2, '2025-10-12 11:36:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`parent_id`),
  ADD KEY `bookings_ibfk_2` (`worker_id`),
  ADD KEY `bookings_ibfk_3` (`child_id`);

--
-- Indexes for table `children`
--
ALTER TABLE `children`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vaccines`
--
ALTER TABLE `vaccines`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `children`
--
ALTER TABLE `children`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vaccines`
--
ALTER TABLE `vaccines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`worker_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`child_id`) REFERENCES `children` (`id`);

--
-- Constraints for table `children`
--
ALTER TABLE `children`
  ADD CONSTRAINT `children_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
