-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2024 at 01:54 PM
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
-- Database: `online_book_exchange`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `email`, `password`) VALUES
(1, 'Maria', 'admin@gmail.com', '$2y$10$FiV.OGWwh1A338gBRDJBPe0aHjtKNm.LNrSw7pCFOt9XjeqPdciPK'),
(2, 'Administrator', 'admin1@gmail.com', '$2y$10$VkYXT204.a1ZzIiTACVvVu1kGFANQDHhW145c5uK/N3P6FGLmbkL.');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `book_id` int(11) NOT NULL,
  `book_title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `condition_state` varchar(50) NOT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('available','exchanged') DEFAULT 'available',
  `create_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_requests`
--

CREATE TABLE `exchange_requests` (
  `request_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `user_book_id` int(11) NOT NULL,
  `requested_by` int(11) NOT NULL,
  `requested_to` int(11) NOT NULL,
  `request_date` date NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `exchange_status` enum('pending','pickup','delivered') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_reviews`
--

CREATE TABLE `exchange_reviews` (
  `review_id` int(11) NOT NULL,
  `requested_to_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `requested_by_id` int(11) NOT NULL,
  `request_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text NOT NULL,
  `review_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issue_reports`
--

CREATE TABLE `issue_reports` (
  `report_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `issue_type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `uploaded_file` varchar(255) DEFAULT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `status` enum('sent','read') DEFAULT 'sent'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`book_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `user_book_id` (`user_book_id`),
  ADD KEY `requested_by` (`requested_by`),
  ADD KEY `exchange_requests_ibfk_4` (`requested_to`);

--
-- Indexes for table `exchange_reviews`
--
ALTER TABLE `exchange_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `requested_to_id` (`requested_to_id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `requested_by_id` (`requested_by_id`);

--
-- Indexes for table `issue_reports`
--
ALTER TABLE `issue_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_reviews`
--
ALTER TABLE `exchange_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issue_reports`
--
ALTER TABLE `issue_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `exchange_requests`
--
ALTER TABLE `exchange_requests`
  ADD CONSTRAINT `exchange_requests_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `exchange_requests_ibfk_2` FOREIGN KEY (`user_book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `exchange_requests_ibfk_3` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `exchange_requests_ibfk_4` FOREIGN KEY (`requested_to`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `exchange_reviews`
--
ALTER TABLE `exchange_reviews`
  ADD CONSTRAINT `exchange_reviews_ibfk_1` FOREIGN KEY (`requested_to_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `exchange_reviews_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`book_id`),
  ADD CONSTRAINT `exchange_reviews_ibfk_3` FOREIGN KEY (`requested_by_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `issue_reports`
--
ALTER TABLE `issue_reports`
  ADD CONSTRAINT `issue_reports_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
