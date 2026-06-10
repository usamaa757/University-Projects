-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2024 at 09:06 PM
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
-- Database: `learn_online`
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
(5, 'Usama', 'usama@gmail.com', '$2y$10$Dv6oYoywQIG4s3joLOsn2./vwPtTE/rLJ/uITQAoZeAUjGZRD.Dhu', '../assets/profile_pic/522430 - Copy.jpg'),
(6, 'Administrator', 'admin@gmail.com', '$2y$10$LQoEYoOPkrYJoKm1m4EkHuQ/ygdVPhya7XbzjUcD6B.hlTHa5GkMa', ''),
(7, 'Zakir', 'admin1@gmail.com', '$2y$10$PUNXFF.x3/z5iFYA9xqbI.MtE2XR//RtvFXyHMCfd2quH7UQOLJ76', '../assets/profile_pic/522430 - Copy.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `course_id` int(255) NOT NULL,
  `assignment_question` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `create_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `course_id`, `assignment_question`, `start_date`, `end_date`, `create_date`) VALUES
(1, 1, ' 1st Assignment of computer Basic course this is very easy assignment so, students have to submit with in due date other zero marks will be given', '2024-06-09', '2024-06-10', '2024-06-09 17:21:23'),
(3, 1, ' assignment 2 updated', '2024-06-17', '2024-06-17', '2024-06-17 18:34:38');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(255) NOT NULL,
  `course_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`) VALUES
(1, 'Computer Basic'),
(2, 'Web Development'),
(3, 'Ethical Hacking'),
(4, 'Cyber Security');

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
  `course_id` int(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`lesson_id`, `title`, `file_name`, `file_size`, `file_type`, `file_ext`, `course_id`, `start_date`, `end_date`) VALUES
(2, 'Lesson 1', 'Pyar Tera Bachya Warga cute Love Story WATCH NOW.mp4', '36286830', 'video/mp4', 'mp4', 1, '2024-06-09', '2024-06-16'),
(3, 'Lesson 2', 'MP4 720p Aise na Mujhe Tum Dekho Love Song Korean Mix.mp4', '44249049', 'video/mp4', 'mp4', 1, '2024-06-16', '2024-06-17');

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
(13, 4, '1', 0),
(14, 4, '2', 0),
(15, 4, '3', 0),
(16, 4, '4', 1);

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
(4, 2, 'What is 2+2');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `course_id` int(255) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `course_id`, `course_name`, `start_date`, `end_date`, `upload_date`) VALUES
(2, 1, 'Computer Basic', '2024-06-09', '2024-06-15', '2024-06-15 03:41:42');

-- --------------------------------------------------------

--
-- Table structure for table `registration`
--

CREATE TABLE `registration` (
  `student_id` int(255) NOT NULL,
  `student_name` varchar(15) NOT NULL,
  `gender` varchar(5) NOT NULL,
  `course_id` int(255) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `student_email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration`
--

INSERT INTO `registration` (`student_id`, `student_name`, `gender`, `course_id`, `phone`, `student_email`, `password`, `picture`, `date`) VALUES
(1, 'Afnan', 'male', 1, '0302019385', 'afnan@gmail.com', '$2y$10$Dv6oYoywQIG4s3joLOsn2./vwPtTE/rLJ/uITQAoZeAUjGZRD.Dhu', '../assets/profile_pic/522430.jpg', '2024-06-15 03:37:59');

-- --------------------------------------------------------

--
-- Table structure for table `student_assignment`
--

CREATE TABLE `student_assignment` (
  `id` int(255) NOT NULL,
  `assignment_id` int(255) NOT NULL,
  `student_id` int(255) NOT NULL,
  `course_id` int(255) NOT NULL,
  `assignment_answer` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `marks` int(255) NOT NULL,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assignment`
--

INSERT INTO `student_assignment` (`id`, `assignment_id`, `student_id`, `course_id`, `assignment_answer`, `submission_date`, `marks`, `status`) VALUES
(5, 1, 1, 1, 'assignment submitted', '2024-06-10 20:01:29', 15, 'Submitted'),
(6, 3, 1, 1, 'submitted within time', '2024-06-17 18:35:25', 0, 'Submitted');

-- --------------------------------------------------------

--
-- Table structure for table `student_course`
--

CREATE TABLE `student_course` (
  `student_course_id` int(11) NOT NULL,
  `course_id` int(255) NOT NULL,
  `student_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_course`
--

INSERT INTO `student_course` (`student_course_id`, `course_id`, `student_id`) VALUES
(1, 1, 1);

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
(1, 1, 2, '2024-06-15 03:45:35', 1, '');

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
(1, 1, 4, 16);

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
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`lesson_id`),
  ADD KEY `course_id FK` (`course_id`);

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
-- Indexes for table `student_assignment`
--
ALTER TABLE `student_assignment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`course_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `assignment_id FK` (`assignment_id`);

--
-- Indexes for table `student_course`
--
ALTER TABLE `student_course`
  ADD PRIMARY KEY (`student_course_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id_ibfk_1` (`course_id`);

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
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `lesson_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `registration`
--
ALTER TABLE `registration`
  MODIFY `student_id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_assignment`
--
ALTER TABLE `student_assignment`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_course`
--
ALTER TABLE `student_course`
  MODIFY `student_course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_quiz`
--
ALTER TABLE `student_quiz`
  MODIFY `student_quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_quiz_answers`
--
ALTER TABLE `student_quiz_answers`
  MODIFY `student_answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `course_id FK` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);

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
-- Constraints for table `student_assignment`
--
ALTER TABLE `student_assignment`
  ADD CONSTRAINT `assignment_id FK` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`assignment_id`);

--
-- Constraints for table `student_course`
--
ALTER TABLE `student_course`
  ADD CONSTRAINT `course_id_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `student_course_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `registration` (`student_id`);

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
