-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 06, 2025 at 07:22 PM
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
-- Database: `career_hub_prototype`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Administrator', 'admin@gmail.com', '$2y$10$ZGxjvgoFEkhmk0KkV0MrTe4QfPsInG65LTCXMosJgVqzOTZgkWkmK', 'admin', '2025-03-06 11:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `job_requirements` text NOT NULL,
  `salary_range` varchar(100) DEFAULT NULL,
  `job_type` enum('Full-time','Part-time','Contract') NOT NULL,
  `application_deadline` date NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `employer_id`, `job_title`, `company_name`, `location`, `job_requirements`, `salary_range`, `job_type`, `application_deadline`, `status`, `created_at`) VALUES
(2, 2, 'Developer', 'IT Tech', 'Karachi', 'Web Developer with PHP', '70000 to 100000', 'Full-time', '2025-03-14', 'approved', '2025-03-06 11:38:23'),
(3, 2, 'teac', 'IT Tech', 'Karachi', 'fd', '5000 to 10000', 'Full-time', '2025-03-07', 'approved', '2025-03-06 17:44:28');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `jobseeker_id` int(11) NOT NULL,
  `resume` varchar(255) NOT NULL,
  `status` enum('Pending','Reviewed','Accepted','Rejected') DEFAULT 'Pending',
  `applied_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `job_id`, `jobseeker_id`, `resume`, `status`, `applied_at`) VALUES
(1, 2, 7, '', 'Pending', '2025-03-06 17:03:28'),
(2, 3, 7, '', 'Pending', '2025-03-06 17:45:08');

-- --------------------------------------------------------

--
-- Table structure for table `job_seekers`
--

CREATE TABLE `job_seekers` (
  `id` int(11) NOT NULL,
  `job_seeker_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `location` varchar(255) NOT NULL,
  `resume` varchar(255) DEFAULT NULL,
  `education` text NOT NULL,
  `experience` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_seekers`
--

INSERT INTO `job_seekers` (`id`, `job_seeker_id`, `name`, `email`, `contact`, `location`, `resume`, `education`, `experience`, `created_at`) VALUES
(1, 7, 'Afnan', 'afnan@gmail.com', '12312321321', 'Islamabad', 'uploads/1741282591_Django documentation.docx', 'df', 'df', '2025-03-06 17:36:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','employer','jobseeker') NOT NULL,
  `status` enum('suspend','active') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'Administrator', 'admin@gmail.com', '$2y$10$tSwJcUEOud5MIjwhLwkWseU3.VxC7q/QK7ySem4LQE2RCoKqNyTvy', 'admin', 'active', '2025-03-06 12:57:02'),
(2, 'Zakir', 'zakir@gmail.com', '$2y$10$Rkt6j4kmQ.0frRFnBKufe.D9iuk8Tg5qIOf2ChFzCq3pO2caSd.jC', 'employer', 'active', '2025-03-06 11:08:05'),
(3, 'Usama', 'usama@gmail.com', '$2y$10$OrJhkRzjeilgGi14XwFNZ.TyKVTQHps4YTBtLodh.S0IuxH90RAmu', 'jobseeker', 'active', '2025-03-06 12:49:23'),
(4, 'Umar', 'umar@gmail.com', '$2y$10$tSwJcUEOud5MIjwhLwkWseU3.VxC7q/QK7ySem4LQE2RCoKqNyTvy', 'employer', 'active', '2025-03-06 12:50:54'),
(6, 'asad', 'asad@gmail.com', '$2y$10$8229AzRXdZdgZ/6NIxXDHeD33FJvSR9x.1hYUeY2IK.ENGGwFyqJa', 'jobseeker', 'active', '2025-03-06 13:00:43'),
(7, 'Afnan', 'afnan@gmail.com', '$2y$10$d5mFhIVcIt2s1VTnjE.HuO1eGgLMxzrhs7jU.3y1Gza0fu8bwcDse', 'jobseeker', 'active', '2025-03-06 11:03:33'),
(8, 'Ameen', 'ameen@gmail.com', '$2y$10$IQyjCKZpF7/znfUOAKn2JOEwEXZlIvK.E7DDwuTYvMQAQd2s31hSm', 'employer', 'active', '2025-03-06 13:02:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `jobseeker_id` (`jobseeker_id`);

--
-- Indexes for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `job_seeker_id` (`job_seeker_id`);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_seekers`
--
ALTER TABLE `job_seekers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`jobseeker_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `job_seekers`
--
ALTER TABLE `job_seekers`
  ADD CONSTRAINT `job_seeker_id` FOREIGN KEY (`job_seeker_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
