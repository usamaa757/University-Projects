-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2025 at 11:00 PM
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
-- Database: `eveluation_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `due_date` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `faculty_id`, `title`, `description`, `category`, `due_date`, `created_at`) VALUES
(2, 3, 'Assignment 1', 'descriptino', 'Computer Science', '2025-11-24 20:00:00', '2025-11-22 20:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `target_table` varchar(100) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `submission_version_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `overall_rating` tinyint(4) DEFAULT NULL,
  `score_structure` tinyint(4) DEFAULT NULL,
  `score_clarity` tinyint(4) DEFAULT NULL,
  `score_originality` tinyint(4) DEFAULT NULL,
  `score_relevance` tinyint(4) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `status` enum('Accepted','Rejected','Needs Improvement','Accepted and Published') NOT NULL,
  `commented_file_path` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `submission_version_id`, `evaluator_id`, `overall_rating`, `score_structure`, `score_clarity`, `score_originality`, `score_relevance`, `comments`, `status`, `commented_file_path`, `created_at`) VALUES
(1, 2, 3, 4, NULL, NULL, NULL, NULL, 'good', 'Accepted and Published', 'uploads/evaluations/1763806492_CS619 Final Report completed kvbs.doc', '2025-11-22 15:14:52'),
(2, 1, 3, 4, NULL, NULL, NULL, NULL, 'rf', 'Accepted', 'uploads/evaluations/1763806540_Kids Vaccination Booking System (KVBS)  dd.docx', '2025-11-22 15:15:40'),
(3, 3, 3, 3, NULL, NULL, NULL, NULL, 'good', 'Accepted', '', '2025-11-22 21:43:14');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `status` enum('Pending','Sent','Failed') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `abstract` text DEFAULT NULL,
  `keywords` varchar(500) DEFAULT NULL,
  `status` enum('Submitted','Needs Improvement','Rejected','Accepted','Accepted and Published') DEFAULT 'Submitted',
  `selected_supervisor_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `assignment_id`, `student_id`, `title`, `abstract`, `keywords`, `status`, `selected_supervisor_id`, `created_at`, `last_updated_at`) VALUES
(1, 2, 2, 'hello', ' this is what', 'good', 'Accepted', 3, '2025-11-22 14:42:43', '2025-11-22 21:43:14'),
(2, 2, 4, 'd', 'd', 'd', 'Accepted and Published', 3, '2025-11-22 15:14:04', '2025-11-22 21:43:06');

-- --------------------------------------------------------

--
-- Table structure for table `submission_versions`
--

CREATE TABLE `submission_versions` (
  `id` int(11) NOT NULL,
  `submission_id` int(11) NOT NULL,
  `version_no` int(11) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `original_filename` varchar(255) DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp(),
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submission_versions`
--

INSERT INTO `submission_versions` (`id`, `submission_id`, `version_no`, `file_path`, `original_filename`, `uploaded_at`, `file_size`, `mime_type`, `notes`) VALUES
(1, 1, 1, 'uploads/research/1763804563_02. Design Document KVBS completed.docx', '02. Design Document KVBS completed.docx', '2025-11-22 14:42:43', NULL, NULL, NULL),
(2, 2, 1, 'uploads/research/1763806444_01. SRS Kids Vaccination Booking System (KVBS) completed.docx', '01. SRS Kids Vaccination Booking System (KVBS) completed.docx', '2025-11-22 15:14:05', NULL, NULL, NULL),
(3, 1, 2, 'uploads/research/1763808151_02. Design Document KVBS completed.docx', '02. Design Document KVBS completed.docx', '2025-11-22 15:42:31', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('student','faculty','admin') NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `university_email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `student_id` varchar(100) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `full_name`, `university_email`, `password_hash`, `student_id`, `program`, `is_active`, `created_at`) VALUES
(1, 'admin', 'Admin', 'admin@vu.edu.pk', '$2y$10$UW8NGMC6XlSKE0gp2Tbu4ug3bVFpfVP3hEO/TG/GXociW01XMasJW', '', '', 1, '2025-11-22 13:11:08'),
(2, 'student', 'asif Kharal', 'bc200402785@vu.edu.pk', '$2y$10$rTmTi7wzRsLHiw3iqXdOx.QwE/nX.Calpk9hXsyLRtUKO94yFGaKa', 'bc200402785', 'Computer Science', 1, '2025-11-22 13:56:48'),
(3, 'faculty', 'Usama Ahmad', 'abc@vu.edu.pk', '$2y$10$EdpEVPF5yj1Tyz42LZS9o.djMhgW4PS1fB4F/6Vx1VYi3C3AXHNT2', '', '', 1, '2025-11-22 13:59:13'),
(4, 'student', 'asif Kharal', 'student1@vu.edu.pk', '$2y$10$tmTCKIAZd9fbPJez9NF6ZuJe/wHekFjc.m6yA2NY8Mmw7uEQobh5W', 'bc20040232', 'Information Technology', 1, '2025-11-22 15:09:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_version_id` (`submission_version_id`),
  ADD KEY `evaluator_id` (`evaluator_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `selected_supervisor_id` (`selected_supervisor_id`);

--
-- Indexes for table `submission_versions`
--
ALTER TABLE `submission_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_id` (`submission_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `university_email` (`university_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `submission_versions`
--
ALTER TABLE `submission_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`submission_version_id`) REFERENCES `submission_versions` (`id`),
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`),
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `submissions_ibfk_3` FOREIGN KEY (`selected_supervisor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `submission_versions`
--
ALTER TABLE `submission_versions`
  ADD CONSTRAINT `submission_versions_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
