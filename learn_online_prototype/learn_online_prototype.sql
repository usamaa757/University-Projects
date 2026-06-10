-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2024 at 07:01 AM
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
-- Database: `learn_online_prototype`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(10) NOT NULL,
  `name` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `pic` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password`, `pic`) VALUES
(6, 'Administrator', 'admin@gmail.com', '$2y$10$LQoEYoOPkrYJoKm1m4EkHuQ/ygdVPhya7XbzjUcD6B.hlTHa5GkMa', '');

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `lesson_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_size` varchar(255) DEFAULT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_ext` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `title`, `file_name`, `file_size`, `file_type`, `file_ext`, `start_date`, `end_date`) VALUES
(5, 'Lecture 1', 'Hai_Kahan_Ka_Irada_-_Nusrat_Fateh_Ali_Khan_-_Top_Qawwali_Songs(360p).mp4', '52635259', 'video/mp4', 'mp4', '2024-08-10', '2024-08-10');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `option_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `option_text` varchar(255) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`option_id`, `question_id`, `option_text`, `is_correct`) VALUES
(17, 5, '1', 0),
(18, 5, '2', 0),
(19, 5, '3', 0),
(20, 5, '4', 1);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `quiz_id`, `question_text`) VALUES
(5, 3, '2+2');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `start_date`, `end_date`, `upload_date`) VALUES
(3, '2024-08-10', '2024-08-10', '2024-08-10 14:11:57');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `student_id` int(255) NOT NULL,
  `student_name` varchar(15) NOT NULL,
  `gender` varchar(5) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `student_email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected','') NOT NULL DEFAULT 'pending',
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`student_id`, `student_name`, `gender`, `phone`, `student_email`, `password`, `picture`, `status`, `date`) VALUES
(1, 'Afnan', 'male', '0302019385', 'afnan@gmail.com', '$2y$10$c6GpU.wVeRM5rz9JE/ya1.DwdDCXAD4luYoqteotFcTm6NOpgF.K2', '../assets/profile_pic/522430.jpg', 'approved', '2024-08-10 13:27:50'),
(2, 'Ali', 'male', '09764243999', 'ali@gmail.com', '$2y$10$UxXE1swDlJqgN.K7Pfd17ugLd5t6hp1jD3SEbtXEQTZRRVAQgE7hW', '../assets/profile_pic/pic.jpg', 'approved', '2024-08-10 07:42:20'),
(3, 'Zakir', 'male', '12345674564', 'zakir@gmail.com', '$2y$10$Sd8XtzM7fwfi.qdKfeX4iuVh5EDOlAuaqLrO4joW6RYDL7SBiBvm2', '../assets/profile_pic/pic.jpg', 'approved', '2024-08-11 05:00:02');

-- --------------------------------------------------------

--
-- Table structure for table `student_quiz`
--

CREATE TABLE `student_quiz` (
  `student_quiz_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `quiz_id` int(11) DEFAULT NULL,
  `submission_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `marks` int(11) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_quiz`
--

INSERT INTO `student_quiz` (`student_quiz_id`, `student_id`, `quiz_id`, `submission_time`, `marks`, `status`) VALUES
(3, 1, 3, '2024-08-10 14:40:40', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `student_quiz_answers`
--

CREATE TABLE `student_quiz_answers` (
  `student_answer_id` int(11) NOT NULL,
  `student_quiz_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `selected_option_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_quiz_answers`
--

INSERT INTO `student_quiz_answers` (`student_answer_id`, `student_quiz_id`, `question_id`, `selected_option_id`) VALUES
(3, 3, 5, 20);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `Email` (`email`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`);

--
-- Indexes for table `registration`
--
ALTER TABLE `registration`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `Student_Email` (`student_email`),
  ADD UNIQUE KEY `Student_Phone` (`phone`);

--
-- Indexes for table `student_quiz`
--
ALTER TABLE `student_quiz`
  ADD PRIMARY KEY (`student_quiz_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `student_quiz_answers`
--
ALTER TABLE `student_quiz_answers`
  ADD PRIMARY KEY (`student_answer_id`),
  ADD KEY `student_quiz_id` (`student_quiz_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_option_id` (`selected_option_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `student_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_quiz`
--
ALTER TABLE `student_quiz`
  MODIFY `student_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_quiz_answers`
--
ALTER TABLE `student_quiz_answers`
  MODIFY `student_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`);

--
-- Constraints for table `student_quiz`
--
ALTER TABLE `student_quiz`
  ADD CONSTRAINT `student_quiz_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `registration` (`student_id`),
  ADD CONSTRAINT `student_quiz_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`);

--
-- Constraints for table `student_quiz_answers`
--
ALTER TABLE `student_quiz_answers`
  ADD CONSTRAINT `student_quiz_answers_ibfk_1` FOREIGN KEY (`student_quiz_id`) REFERENCES `student_quiz` (`student_quiz_id`),
  ADD CONSTRAINT `student_quiz_answers_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`),
  ADD CONSTRAINT `student_quiz_answers_ibfk_3` FOREIGN KEY (`selected_option_id`) REFERENCES `options` (`option_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
