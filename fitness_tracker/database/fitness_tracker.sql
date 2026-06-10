-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 12:06 PM
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
-- Database: `fitness_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `daily_meals`
--

CREATE TABLE `daily_meals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meal_type` enum('Breakfast','Lunch','Dinner','Snack') NOT NULL,
  `description` text NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `log_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_meals`
--

INSERT INTO `daily_meals` (`id`, `user_id`, `meal_type`, `description`, `calories`, `log_date`, `created_at`) VALUES
(1, 2, 'Breakfast', '3 eggs, 1kg butter', 3, '2025-12-12', '2025-12-12 04:39:33');

-- --------------------------------------------------------

--
-- Table structure for table `daily_water`
--

CREATE TABLE `daily_water` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount_ml` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_water`
--

INSERT INTO `daily_water` (`id`, `user_id`, `amount_ml`, `log_date`, `created_at`) VALUES
(1, 1, 200, '2025-12-11', '2025-12-11 06:35:18'),
(2, 2, 30, '2025-12-12', '2025-12-12 04:39:40');

-- --------------------------------------------------------

--
-- Table structure for table `daily_workouts`
--

CREATE TABLE `daily_workouts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `workout_id` int(11) NOT NULL,
  `reps_or_duration` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `log_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_workouts`
--

INSERT INTO `daily_workouts` (`id`, `user_id`, `workout_id`, `reps_or_duration`, `notes`, `log_date`, `created_at`) VALUES
(1, 1, 2, '3*15 Pushups', NULL, '2025-12-11', '2025-12-11 06:35:04');

-- --------------------------------------------------------

--
-- Table structure for table `diet_feedback`
--

CREATE TABLE `diet_feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `diet_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diet_feedback`
--

INSERT INTO `diet_feedback` (`id`, `user_id`, `diet_id`, `feedback_text`, `created_at`) VALUES
(2, 2, 3, 'good', '2025-12-12 03:58:16');

-- --------------------------------------------------------

--
-- Table structure for table `diet_plans`
--

CREATE TABLE `diet_plans` (
  `id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `calories` int(11) DEFAULT NULL,
  `meal_time` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diet_plans`
--

INSERT INTO `diet_plans` (`id`, `trainer_id`, `title`, `description`, `calories`, `meal_time`, `created_at`) VALUES
(3, 3, 'Plan-1', 'need to follow to your plan regularly', 3, 'breakfast', '2025-12-12 03:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `routine_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `trainer_response` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `routine_id`, `feedback_text`, `trainer_response`, `created_at`) VALUES
(4, 2, 6, 'good one', 'thanks', '2025-12-11 12:15:42');

-- --------------------------------------------------------

--
-- Table structure for table `fitness_tips`
--

CREATE TABLE `fitness_tips` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fitness_tips`
--

INSERT INTO `fitness_tips` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'Tip1', 'good health for good', '2025-12-11 06:16:53');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_suggestions`
--

CREATE TABLE `trainer_suggestions` (
  `id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `suggestion` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_suggestions`
--

INSERT INTO `trainer_suggestions` (`id`, `trainer_id`, `user_id`, `suggestion`, `created_at`) VALUES
(1, 3, 2, 'good', '2025-12-12 09:35:06');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `role` enum('admin','trainer','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `address`, `role`, `created_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$SxgAgJ690AMb2h0b72fvgerz1HBZJ45SA0aTypTQ/6Gn6zD/V6KK2', 'Lahore', 'admin', '2025-12-11 05:05:44'),
(2, 'Asad Khan', 'asad@gmail.com', '$2y$10$4IkoNn.kvJNDlVNqZvkVMu9hEB5zdnZPUQRlstblbm7cNwc0lGdEW', 'Lahore', 'user', '2025-12-11 05:06:02'),
(3, 'Asif Ali', 'asif@gmail.com', '$2y$10$g.NINJEha9qE9i4F661Uku1g4LCRwnCQLgFYfZxAoBqbF6QoyFD/C', 'Karachi', 'trainer', '2025-12-11 05:06:08');

-- --------------------------------------------------------

--
-- Table structure for table `workout_routines`
--

CREATE TABLE `workout_routines` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `difficulty` varchar(20) NOT NULL DEFAULT 'Beginner',
  `duration` varchar(50) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workout_routines`
--

INSERT INTO `workout_routines` (`id`, `title`, `description`, `trainer_id`, `created_at`, `difficulty`, `duration`) VALUES
(6, 'Routine1', 'Exercise(Daily 3 set of pushups', 3, '2025-12-11 07:12:25', 'Beginner', '45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `daily_meals`
--
ALTER TABLE `daily_meals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `daily_water`
--
ALTER TABLE `daily_water`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `daily_workouts`
--
ALTER TABLE `daily_workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `workout_id` (`workout_id`);

--
-- Indexes for table `diet_feedback`
--
ALTER TABLE `diet_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `diet_id` (`diet_id`);

--
-- Indexes for table `diet_plans`
--
ALTER TABLE `diet_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_workout_2` (`trainer_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `routine_id` (`routine_id`);

--
-- Indexes for table `fitness_tips`
--
ALTER TABLE `fitness_tips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainer_suggestions`
--
ALTER TABLE `trainer_suggestions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `workout_routines`
--
ALTER TABLE `workout_routines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_workout_1` (`trainer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `daily_meals`
--
ALTER TABLE `daily_meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `daily_water`
--
ALTER TABLE `daily_water`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `daily_workouts`
--
ALTER TABLE `daily_workouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `diet_feedback`
--
ALTER TABLE `diet_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `diet_plans`
--
ALTER TABLE `diet_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `fitness_tips`
--
ALTER TABLE `fitness_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trainer_suggestions`
--
ALTER TABLE `trainer_suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `workout_routines`
--
ALTER TABLE `workout_routines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_meals`
--
ALTER TABLE `daily_meals`
  ADD CONSTRAINT `daily_meals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `daily_water`
--
ALTER TABLE `daily_water`
  ADD CONSTRAINT `daily_water_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `daily_workouts`
--
ALTER TABLE `daily_workouts`
  ADD CONSTRAINT `daily_workouts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `daily_workouts_ibfk_2` FOREIGN KEY (`workout_id`) REFERENCES `workout_routines` (`id`);

--
-- Constraints for table `diet_feedback`
--
ALTER TABLE `diet_feedback`
  ADD CONSTRAINT `diet_feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `diet_feedback_ibfk_2` FOREIGN KEY (`diet_id`) REFERENCES `diet_plans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `diet_plans`
--
ALTER TABLE `diet_plans`
  ADD CONSTRAINT `fk_workout_2` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`routine_id`) REFERENCES `workout_routines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_routines`
--
ALTER TABLE `workout_routines`
  ADD CONSTRAINT `fk_workout_1` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
