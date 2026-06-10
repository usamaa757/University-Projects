-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2024 at 09:55 PM
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
-- Database: `sas_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('approved') NOT NULL DEFAULT 'approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_name`, `email`, `username`, `password`, `status`) VALUES
(1, 'Kinza', 'admin@gmail.com', 'Kinza', '$2y$10$kR0iV3EvygRxoHP6NemPJOISxyorDy1PQ56b5/9pajyqBKic0qVK6', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `assignment_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `due_date` date NOT NULL,
  `assignment_file` varchar(255) NOT NULL,
  `class_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`assignment_id`, `course_id`, `teacher_id`, `title`, `description`, `due_date`, `assignment_file`, `class_id`) VALUES
(1, 4, 2, 'sss', '', '2024-08-25', 'uploadsjutt letter.docx', 2),
(2, 4, 2, 'm', '', '2024-09-04', 'uploadsbulboff.jpg', 2),
(3, 4, 2, 'dfdfdfd', '', '2024-08-29', 'uploads/bulboff.jpg', 2);

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL,
  `status` enum('present','absent') NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`attendance_id`, `student_id`, `course_id`, `class_id`, `attendance_date`, `status`, `teacher_id`) VALUES
(1, 5, 4, 2, '2024-08-27', 'present', 2),
(2, 5, 5, 2, '2024-08-27', 'present', 2);

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_name`) VALUES
(1, 'class 6'),
(2, 'class 7'),
(3, 'class 8'),
(4, 'Class 9');

-- --------------------------------------------------------

--
-- Table structure for table `class_course`
--

CREATE TABLE `class_course` (
  `class_course_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `class_course`
--

INSERT INTO `class_course` (`class_course_id`, `class_id`, `course_id`) VALUES
(1, 2, 4),
(2, 2, 5),
(3, 2, 6),
(4, 2, 15),
(5, 1, 1),
(6, 1, 2),
(7, 1, 3),
(8, 1, 4),
(9, 1, 5);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(10) NOT NULL,
  `course_description` text NOT NULL,
  `class_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `course_description`, `class_id`) VALUES
(1, 'ENG001', 'Grade 6 English focuses on building reading comprehension and writing skills through the exploration of various literary genres. Students will engage in activities to analyze texts for themes and literary devices, while also practicing narrative and descriptive writing techniques.', 1),
(2, 'MATH001', 'Grade 6 Mathematics extends to algebraic expressions, geometry, and probability through real-world applications, fostering analytical skills. Problem-solving tasks deepen understanding and promote mathematical reasoning.', 1),
(3, 'COMP001', 'Grade 6 Computer Science introduces students to basic programming concepts using beginner-friendly languages like Scratch. Through hands-on projects, students learn to create interactive animations and games, fostering problem-solving skills and computational thinking.', 1),
(4, 'ENG002', 'Grade 7 English focuses on developing analytical and communication skills through the study of literature and writing workshops. Emphasis is placed on critical analysis of texts and advanced writing techniques such as persuasive writing and literary analysis essays.', 2),
(5, 'MATH002', 'Grade 7 Mathematics builds upon foundational skills, covering topics such as algebraic expressions, geometry, statistics, and probability. Through problem-solving tasks and real-world applications, students develop critical thinking and mathematical reasoning abilities.', 2),
(6, 'COMP002', 'In Grade 7 Computer Science, students learn advanced programming concepts in languages like Python and explore algorithms and data structures. Through coding challenges and real-world applications, students develop a deeper understanding of computational thinking and problem-solving strategies.', 2),
(7, 'ENG003', 'In Grade 8 English, students further refine their analytical and communication skills through the study of literature and writing workshops. Focus is placed on critical analysis of texts and advanced writing techniques such as persuasive writing and literary analysis essays.', 3),
(8, 'MATH003', 'Grade 8 Mathematics builds upon previous knowledge, covering advanced topics such as algebra, geometry, trigonometry, and statistics. Through problem-solving tasks and real-world applications, students deepen their understanding of mathematical concepts and develop critical thinking skills.', 3),
(9, 'COMP003', 'In Grade 8 Computer Science, students delve deeper into programming languages such as Python, exploring more advanced concepts like algorithms and data structures. Through coding challenges and real-world applications, students develop a deeper understanding of computational thinking and problem-solving strategies.', 3),
(11, 'SCI001', 'Grade 6 Science explores fundamental concepts in biology, chemistry, and physics through hands-on experiments and scientific inquiry, fostering curiosity and critical thinking.', 1),
(12, 'URD001', 'Grade 6 Urdu focuses on developing reading, writing, and comprehension skills in the Urdu language through poetry, prose, and grammar exercises.', 1),
(13, 'ISL001', 'Grade 6 Islamiat introduces students to the basic principles and teachings of Islam, emphasizing moral values, ethics, and religious practices.', 1),
(14, 'ETH001', 'Grade 6 Ethics introduces students to fundamental ethical principles and moral reasoning, exploring topics such as fairness, responsibility, and integrity.', 1),
(15, 'SCI002', 'Grade 7 Science explores advanced topics in biology, chemistry, and physics, emphasizing scientific inquiry, experimentation, and data analysis skills.', 2),
(16, 'URD002', 'Grade 7 Urdu focuses on further developing reading, writing, and comprehension skills in the Urdu language through poetry, prose, and literary analysis.', 2),
(17, 'ISL002', 'Grade 7 Islamiat delves deeper into the principles and teachings of Islam, exploring topics such as Islamic history, ethics, and spirituality.', 2),
(18, 'ETH002', 'Grade 7 Ethics explores ethical dilemmas and moral decision-making, examining issues such as social justice, human rights, and environmental responsibility.', 2),
(19, 'SCI003', 'Grade 8 Science provides an in-depth exploration of key scientific concepts in biology, chemistry, and physics, emphasizing scientific inquiry, experimentation, and critical analysis.', 3),
(20, 'URD003', 'Grade 8 Urdu focuses on advanced reading, writing, and comprehension skills in the Urdu language, including poetry appreciation, prose analysis, and creative writing.', 3),
(21, 'ISL003', 'Grade 8 Islamiat deepens students\' understanding of Islamic principles, exploring topics such as Islamic law, theology, and spirituality in greater depth.', 3),
(22, 'ETH003', 'Grade 8 Ethics examines ethical theories and their practical applications in contemporary society, discussing issues such as moral relativism, cultural diversity, and ethical leadership.', 3),
(23, 'C++ Essent', 'sdsds', 3);

-- --------------------------------------------------------

--
-- Table structure for table `course_selection`
--

CREATE TABLE `course_selection` (
  `selection_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `class_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_selection`
--

INSERT INTO `course_selection` (`selection_id`, `student_id`, `course_id`, `class_id`) VALUES
(36, 1, 1, 1),
(37, 1, 2, 1),
(38, 1, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `fee_vouchers`
--

CREATE TABLE `fee_vouchers` (
  `voucher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `fee_amount` decimal(10,2) NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fee_vouchers`
--

INSERT INTO `fee_vouchers` (`voucher_id`, `class_id`, `fee_amount`, `month`, `year`, `issue_date`, `due_date`) VALUES
(1, 2, 3444.00, '', '', '2024-08-24', '2024-09-23'),
(2, 1, 2300.00, 'September', '2024', '2024-08-25', '2024-09-24');

-- --------------------------------------------------------

--
-- Table structure for table `lectures`
--

CREATE TABLE `lectures` (
  `lecture_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `video_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lectures`
--

INSERT INTO `lectures` (`lecture_id`, `class_id`, `course_id`, `teacher_id`, `title`, `video_url`) VALUES
(1, 2, 1, 2, 'dsd', 'https://www.youtube.com/watch?v=DAxKQIG4T2Y');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `option_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` varchar(255) NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`option_id`, `question_id`, `answer_text`, `is_correct`, `created_at`) VALUES
(1, 1, 'answer1', 0, '2024-08-25 19:36:41'),
(2, 1, 'answer2', 0, '2024-08-25 19:36:41'),
(3, 1, 'answer3', 0, '2024-08-25 19:36:41'),
(4, 1, 'answer4', 0, '2024-08-25 19:36:41'),
(5, 2, 'answer1', 0, '2024-08-25 19:37:19'),
(6, 2, 'answer2', 0, '2024-08-25 19:37:19'),
(7, 2, 'answer3', 0, '2024-08-25 19:37:19'),
(8, 2, 'answer4', 0, '2024-08-25 19:37:19'),
(9, 3, 'd', 0, '2024-08-25 19:39:04'),
(10, 3, 'df ', 0, '2024-08-25 19:39:04'),
(11, 3, 'dfd', 0, '2024-08-25 19:39:04'),
(12, 3, 'd f', 1, '2024-08-25 19:39:04'),
(13, 5, '1', 1, '2024-08-25 20:20:18'),
(14, 5, '2', 0, '2024-08-25 20:20:18'),
(15, 5, '3', 0, '2024-08-25 20:20:18'),
(16, 5, '4', 0, '2024-08-25 20:20:18'),
(17, 6, '1', 0, '2024-08-25 20:20:28'),
(18, 6, '3343', 1, '2024-08-25 20:20:28'),
(19, 6, '434', 0, '2024-08-25 20:20:28'),
(20, 6, '23432', 0, '2024-08-25 20:20:28'),
(21, 7, 'dsfs', 0, '2024-08-25 20:20:34'),
(22, 7, 'df', 0, '2024-08-25 20:20:34'),
(23, 7, 'asdf', 1, '2024-08-25 20:20:34'),
(24, 7, '', 0, '2024-08-25 20:20:34'),
(25, 8, 'f', 0, '2024-08-25 20:21:02'),
(26, 8, 'fd', 0, '2024-08-25 20:21:02'),
(27, 8, 'dfd', 0, '2024-08-25 20:21:02'),
(28, 8, 'dfsdf', 1, '2024-08-25 20:21:02'),
(29, 9, 'sds', 1, '2024-08-25 20:34:43'),
(30, 9, 'sds', 0, '2024-08-25 20:34:43'),
(31, 9, 'sds', 0, '2024-08-25 20:34:43'),
(32, 9, 'sds', 0, '2024-08-25 20:34:43'),
(33, 10, 'dfd', 0, '2024-08-25 20:35:15'),
(34, 10, 'dfd', 1, '2024-08-25 20:35:15'),
(35, 10, 'fdf', 0, '2024-08-25 20:35:15'),
(36, 10, 'dfd', 0, '2024-08-25 20:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `parent_id` int(11) NOT NULL,
  `parent_name` varchar(100) NOT NULL,
  `phone` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','rejected','approved') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`parent_id`, `parent_name`, `phone`, `email`, `password`, `student_id`, `created_at`, `status`) VALUES
(4, 'Tahir', 302019384, 'tahir@gmail.com', '$2y$10$kpOJr.6yCWpepSYC5aIbpOwpxh2yqRwxo/cvXU4RnvSU/36ivPQ4y', 1, '2024-08-24 05:48:57', 'approved'),
(6, 'asif', 1010101010, 'asif@gmail.com', '$2y$10$frXbOWbOLnTOSH2pe9SCeuj1P0.F7ehsN1lSF9rMUyBT023odC7ZG', 5, '2024-08-25 07:08:23', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `parent_feedback`
--

CREATE TABLE `parent_feedback` (
  `id` int(11) NOT NULL,
  `remarks` text DEFAULT NULL,
  `additional_feedback` text DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `student_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parent_feedback`
--

INSERT INTO `parent_feedback` (`id`, `remarks`, `additional_feedback`, `submission_date`, `student_id`, `parent_id`, `teacher_id`) VALUES
(1, 'somewhat_satisfied', 'ssss', '2024-08-25 09:07:57', 5, NULL, 2),
(2, 'somewhat_satisfied', 'ssss', '2024-08-25 09:09:01', 5, 6, 2),
(3, 'somewhat_satisfied', 'ssss', '2024-08-25 09:11:06', 5, 6, 2),
(4, 'somewhat_satisfied', 'ssss', '2024-08-25 09:11:57', 5, 6, 2),
(5, 'somewhat_satisfied', 'ssss', '2024-08-25 09:12:14', 5, 6, 2),
(6, 'satisfied', 'cxc', '2024-08-25 09:15:13', 5, 6, 2);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `quiz_id`, `question_text`, `created_at`) VALUES
(1, 1, 'Queston no 2 update', '2024-08-25 19:36:41'),
(2, 1, 'Queston no 3 update', '2024-08-25 19:37:19'),
(3, 1, 'Queston no 4 update', '2024-08-25 19:39:04'),
(4, 1, 'Queston no 1 update', '2024-08-25 20:19:55'),
(5, 1, 'Queston no 1 update', '2024-08-25 20:20:18'),
(6, 1, 'Queston no 2 update', '2024-08-25 20:20:28'),
(7, 1, 'Queston no 3 update', '2024-08-25 20:20:34'),
(8, 1, 'Queston no 4 update', '2024-08-25 20:21:02'),
(9, 2, 'Queston no 1 update', '2024-08-25 20:34:43'),
(10, 2, 'Queston no 2 update', '2024-08-25 20:35:15');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `title`, `start_date`, `end_date`, `created_at`) VALUES
(1, 'first', '2024-08-26 00:30:00', '2024-08-26 00:30:00', '2024-08-25 19:31:01'),
(2, '2nd', '2024-08-26 00:00:00', '2024-08-26 00:00:00', '2024-08-25 20:19:33');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `result_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `marks` int(11) NOT NULL,
  `total_marks` int(255) DEFAULT NULL,
  `pass_year` date NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`result_id`, `student_id`, `course_id`, `class_id`, `marks`, `total_marks`, `pass_year`, `created_at`) VALUES
(2, 1, 1, 2, 78, 100, '2024-08-28', '2024-08-27 18:38:26'),
(3, 1, 2, 2, 90, 100, '2024-08-28', '2024-08-27 18:38:27'),
(4, 1, 3, 2, 81, 100, '2024-08-28', '2024-08-27 18:38:27');

-- --------------------------------------------------------

--
-- Table structure for table `salary_slips`
--

CREATE TABLE `salary_slips` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `month` varchar(255) NOT NULL,
  `year` int(11) NOT NULL,
  `salary` decimal(10,2) NOT NULL,
  `deductions` decimal(10,2) NOT NULL,
  `net_salary` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_slips`
--

INSERT INTO `salary_slips` (`id`, `teacher_id`, `month`, `year`, `salary`, `deductions`, `net_salary`, `created_at`) VALUES
(1, 1, 'april', 2024, 50000.00, 2000.00, 45000.00, '2024-04-29 21:19:00'),
(2, 1, 'april', 2024, 50000.00, 5000.00, 45000.00, '2024-04-29 21:41:25'),
(3, 1, 'april', 2024, 50000.00, 5000.00, 45000.00, '2024-04-29 21:52:51'),
(4, 1, 'april', 2024, 50000.00, 5000.00, 45000.00, '2024-04-29 21:53:45'),
(5, 1, 'april', 2024, 50000.00, 5000.00, 45000.00, '2024-04-29 21:55:18'),
(0, 7, '33', 3333, 333333.00, 33.00, 3333.00, '2024-08-23 19:11:31'),
(0, 1000, '33', 22, 333333.00, 33.00, 2323.00, '2024-08-24 19:14:49'),
(0, 8, '2024-08', 2024, 26000.00, 500.00, 25500.00, '2024-08-24 19:20:32'),
(0, 9, '2024-08', 2024, 333333.00, 222.00, 333111.00, '2024-08-24 19:21:34');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `status` enum('pending','reject','approved') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `student_name`, `dob`, `gender`, `email`, `password`, `class_id`, `status`) VALUES
(1, 'Afnan', '2003-06-24', 'Male', 'afnan@gmail.com', '$2y$10$lnVWY1ftw1whYVwSinWlxO3R7ozHAmWGJ5PcMozoAEkzNw9eINPkG', 1, 'approved'),
(5, 'Zakir', '2024-08-27', 'Male', 'zakir@gmail.com', '$2y$10$f92zhvlqOTF0XWlGrsdPAe1nkEwfi/VaU0sVkQapGfYkBw7JD.r8m', 2, 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('submitted') NOT NULL DEFAULT 'submitted'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`id`, `student_id`, `quiz_id`, `question_id`, `option_id`, `created_at`, `status`) VALUES
(7, 2, 1, 1, 1, '2024-08-25 20:07:54', 'submitted'),
(8, 2, 1, 2, 5, '2024-08-25 20:07:54', 'submitted'),
(9, 2, 1, 3, 9, '2024-08-25 20:07:54', 'submitted'),
(10, 2, 2, 9, 29, '2024-08-25 20:36:06', 'submitted'),
(11, 2, 2, 10, 34, '2024-08-25 20:36:06', 'submitted'),
(12, 2, 2, 9, 29, '2024-08-25 20:39:08', 'submitted'),
(13, 2, 2, 10, 34, '2024-08-25 20:39:08', 'submitted'),
(14, 1, 2, 9, 29, '2024-08-26 16:40:38', 'submitted'),
(15, 1, 2, 10, 33, '2024-08-26 16:40:38', 'submitted');

-- --------------------------------------------------------

--
-- Table structure for table `student_assignments`
--

CREATE TABLE `student_assignments` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `assignment_file` varchar(255) DEFAULT NULL,
  `status` enum('submitted') DEFAULT 'submitted',
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `marks` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assignments`
--

INSERT INTO `student_assignments` (`id`, `assignment_id`, `student_id`, `course_id`, `assignment_file`, `status`, `upload_date`, `marks`) VALUES
(4, 1, 1, 4, 'uploads/bulboff.jpg', 'submitted', '2024-08-26 18:04:10', 0),
(5, 3, 1, 4, 'uploads/bulb.html', 'submitted', '2024-08-26 18:35:37', 0),
(6, 3, 1, 4, 'uploads/bulboff.jpg', 'submitted', '2024-08-26 18:37:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `qualification` varchar(100) NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `experience` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`teacher_id`, `teacher_name`, `qualification`, `gender`, `experience`, `email`, `password`, `status`) VALUES
(2, 'Ali', 'fsc', 'male', '3', 'ali@gmail.com', '$2y$10$zUB5oYZvw7i9Id9yOyH8POI/BeWPpPumtaN3GwAYd3/lDOcH3M0/K', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_attendance`
--

CREATE TABLE `teacher_attendance` (
  `attendance_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_attendance`
--

INSERT INTO `teacher_attendance` (`attendance_id`, `teacher_id`, `date`, `status`) VALUES
(1, 8, '2024-08-24', 'present'),
(2, 9, '2024-08-24', 'present'),
(3, 10, '2024-08-24', 'present'),
(4, 11, '2024-08-24', 'present'),
(5, 8, '2024-08-24', 'absent'),
(6, 9, '2024-08-24', 'absent'),
(7, 10, '2024-08-24', 'present'),
(8, 11, '2024-08-24', 'present');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_class_course`
--

CREATE TABLE `teacher_class_course` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_class_course`
--

INSERT INTO `teacher_class_course` (`id`, `teacher_id`, `class_id`, `course_id`, `created_at`) VALUES
(1, 2, 2, 4, '2024-08-25 08:31:20'),
(2, 2, 2, 5, '2024-08-25 08:31:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `class_id` (`class_id`),
  ADD KEY `teacher_id` (`teacher_id`),
  ADD KEY `attendance_ibfk_1` (`student_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`);

--
-- Indexes for table `class_course`
--
ALTER TABLE `class_course`
  ADD PRIMARY KEY (`class_course_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `course_selection`
--
ALTER TABLE `course_selection`
  ADD PRIMARY KEY (`selection_id`);

--
-- Indexes for table `fee_vouchers`
--
ALTER TABLE `fee_vouchers`
  ADD PRIMARY KEY (`voucher_id`);

--
-- Indexes for table `lectures`
--
ALTER TABLE `lectures`
  ADD PRIMARY KEY (`lecture_id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`option_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`parent_id`);

--
-- Indexes for table `parent_feedback`
--
ALTER TABLE `parent_feedback`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `option_id` (`option_id`);

--
-- Indexes for table `student_assignments`
--
ALTER TABLE `student_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teacher_attendance`
--
ALTER TABLE `teacher_attendance`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `teacher_class_course`
--
ALTER TABLE `teacher_class_course`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `class_course`
--
ALTER TABLE `class_course`
  MODIFY `class_course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `course_selection`
--
ALTER TABLE `course_selection`
  MODIFY `selection_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `fee_vouchers`
--
ALTER TABLE `fee_vouchers`
  MODIFY `voucher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lectures`
--
ALTER TABLE `lectures`
  MODIFY `lecture_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `option_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `parent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `parent_feedback`
--
ALTER TABLE `parent_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_assignments`
--
ALTER TABLE `student_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_attendance`
--
ALTER TABLE `teacher_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `teacher_class_course`
--
ALTER TABLE `teacher_class_course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`),
  ADD CONSTRAINT `results_ibfk_3` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`),
  ADD CONSTRAINT `student_answers_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`),
  ADD CONSTRAINT `student_answers_ibfk_4` FOREIGN KEY (`option_id`) REFERENCES `options` (`option_id`);

--
-- Constraints for table `student_assignments`
--
ALTER TABLE `student_assignments`
  ADD CONSTRAINT `student_assignments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `student_assignments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
