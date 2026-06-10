-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2025 at 07:50 AM
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
-- Database: `event_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendees`
--

CREATE TABLE `attendees` (
  `attendee_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendees`
--

INSERT INTO `attendees` (`attendee_id`, `username`, `email`, `password`) VALUES
(1, 'Asif', 'asif@gmail.com', '$2y$10$fQHvlpT1laHfbjGH/lGptuxfPF20CvYweVZNbsOwlwpkDI521hTq2'),
(2, 'Amir', 'amir@gmail.com', '$2y$10$hjpIwMWM6OvMKjx8BIAPVOieRUMrXkWGk.Y3THuf/djvVZkzy5PR6'),
(3, 'Ali Raza', 'ali@gmail.com', '$2y$10$pBAsKAq.04G2eg8o.JC31uKMe4UmzIBk8KhQnFXj5efnIWG7w8kti');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `event_description` text DEFAULT NULL,
  `organizer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `location`, `event_date`, `event_description`, `organizer_id`) VALUES
(2, 'Birthday', 'Islamabad', '2025-04-13', 'Happy birthday', 1);

-- --------------------------------------------------------

--
-- Table structure for table `organizers`
--

CREATE TABLE `organizers` (
  `organizer_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizers`
--

INSERT INTO `organizers` (`organizer_id`, `username`, `email`, `password`) VALUES
(1, 'Asad', 'asad@gmail.com', '$2y$10$EQO9wC2Qdi0VtV0Lpls3su0qwlGbALUAzKeU6MIZiFj48.GWxZ.L6');

-- --------------------------------------------------------

--
-- Table structure for table `rsvps`
--

CREATE TABLE `rsvps` (
  `rsvp_id` int(11) NOT NULL,
  `attendee_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL,
  `rsvp_date` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('attend','maybe','decline') DEFAULT 'attend'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rsvps`
--

INSERT INTO `rsvps` (`rsvp_id`, `attendee_id`, `event_id`, `rsvp_date`, `status`) VALUES
(1, 1, 2, '2025-04-13 04:17:28', 'attend'),
(3, 3, 2, '2025-04-13 04:18:29', 'maybe'),
(4, 2, 2, '2025-04-13 04:19:16', 'attend');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendees`
--
ALTER TABLE `attendees`
  ADD PRIMARY KEY (`attendee_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organizer_id` (`organizer_id`);

--
-- Indexes for table `organizers`
--
ALTER TABLE `organizers`
  ADD PRIMARY KEY (`organizer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `rsvps`
--
ALTER TABLE `rsvps`
  ADD PRIMARY KEY (`rsvp_id`),
  ADD UNIQUE KEY `unique_attendee_event` (`attendee_id`,`event_id`),
  ADD KEY `event_id` (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendees`
--
ALTER TABLE `attendees`
  MODIFY `attendee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `organizers`
--
ALTER TABLE `organizers`
  MODIFY `organizer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rsvps`
--
ALTER TABLE `rsvps`
  MODIFY `rsvp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `organizers` (`organizer_id`);

--
-- Constraints for table `rsvps`
--
ALTER TABLE `rsvps`
  ADD CONSTRAINT `rsvps_ibfk_1` FOREIGN KEY (`attendee_id`) REFERENCES `attendees` (`attendee_id`),
  ADD CONSTRAINT `rsvps_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
