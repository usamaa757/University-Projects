-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2024 at 06:52 PM
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
-- Database: `ambulance_booking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `admin_name` varchar(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `admin_name`, `email`, `password`) VALUES
(1, 'Bilal', 'admin@gmail.com', '$2y$10$kR0iV3EvygRxoHP6NemPJOISxyorDy1PQ56b5/9pajyqBKic0qVK6'),
(2, 'Bilal Ch', 'admin1@gmail.com', '$2y$10$kR0iV3EvygRxoHP6NemPJOISxyorDy1PQ56b5/9pajyqBKic0qVK6');

-- --------------------------------------------------------

--
-- Table structure for table `ambulances`
--

CREATE TABLE `ambulances` (
  `ambulance_id` int(11) NOT NULL,
  `plate_number` varchar(50) NOT NULL,
  `status` enum('Available','Unavailable','Busy') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ambulances`
--

INSERT INTO `ambulances` (`ambulance_id`, `plate_number`, `status`) VALUES
(1, 'ABC-123', 'Busy'),
(2, 'DEF-456', 'Available'),
(3, 'GHI-789', 'Available'),
(4, 'JKL-012', 'Available'),
(5, 'MNO-345', 'Available'),
(6, 'PQR-678', 'Available'),
(7, 'STU-901', 'Available'),
(8, 'VWX-234', 'Busy'),
(9, 'YZA-567', 'Available'),
(10, 'BCD-890', 'Available'),
(11, 'EFG-123', 'Available'),
(12, 'HIJ-456', 'Available'),
(13, 'KLM-789', 'Busy'),
(14, 'NOP-012', 'Available'),
(15, 'QRS-345', 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `ambulance_driver_assignment`
--

CREATE TABLE `ambulance_driver_assignment` (
  `id` int(11) NOT NULL,
  `ambulance_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `assignment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ambulance_driver_assignment`
--

INSERT INTO `ambulance_driver_assignment` (`id`, `ambulance_id`, `driver_id`, `assignment_date`) VALUES
(1, 1, 1, '2024-07-23 21:08:12'),
(2, 2, 2, '2024-07-23 21:08:35'),
(3, 3, 3, '2024-07-23 21:16:46'),
(4, 4, 6, '2024-07-23 21:16:52'),
(5, 5, 5, '2024-07-23 21:17:00'),
(6, 6, 6, '2024-07-23 21:17:06'),
(7, 7, 7, '2024-07-23 21:17:12');

-- --------------------------------------------------------

--
-- Table structure for table `ambulance_hospital_assignment`
--

CREATE TABLE `ambulance_hospital_assignment` (
  `assignment_id` int(11) NOT NULL,
  `ambulance_id` int(11) NOT NULL,
  `hosp_id` int(11) NOT NULL,
  `status` enum('Available','Busy','','') NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ambulance_hospital_assignment`
--

INSERT INTO `ambulance_hospital_assignment` (`assignment_id`, `ambulance_id`, `hosp_id`, `status`, `assigned_at`) VALUES
(1, 1, 1, 'Available', '2024-07-21 00:00:22'),
(2, 2, 1, 'Available', '2024-07-21 00:00:30'),
(3, 3, 2, 'Available', '2024-07-21 00:00:35'),
(5, 5, 3, 'Available', '2024-07-21 00:00:53'),
(6, 4, 4, 'Available', '2024-07-21 00:01:04'),
(7, 6, 4, 'Available', '2024-07-21 00:01:08'),
(8, 7, 5, 'Available', '2024-07-21 00:01:12'),
(9, 9, 1, 'Available', '2024-07-21 00:05:00'),
(10, 10, 2, 'Available', '2024-07-21 00:05:16'),
(11, 11, 1, 'Available', '2024-07-21 00:05:21'),
(12, 14, 2, 'Available', '2024-07-21 00:05:27'),
(13, 15, 3, 'Available', '2024-07-21 00:05:32'),
(14, 12, 5, 'Available', '2024-07-21 00:05:37'),
(15, 8, 1, 'Available', '2024-11-18 22:57:36'),
(16, 8, 5, 'Available', '2024-11-18 22:57:42'),
(17, 13, 3, 'Available', '2024-11-18 22:57:46'),
(18, 13, 5, 'Available', '2024-11-18 22:57:54'),
(19, 1, 2, 'Available', '2024-11-18 23:06:06'),
(20, 1, 5, 'Available', '2024-11-18 23:06:31');

-- --------------------------------------------------------

--
-- Table structure for table `ambulance_user_assignment`
--

CREATE TABLE `ambulance_user_assignment` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `ambulance_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` int(11) NOT NULL,
  `status` enum('Busy','Completed','Pending') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ambulance_user_assignment`
--

INSERT INTO `ambulance_user_assignment` (`id`, `booking_id`, `ambulance_id`, `assigned_at`, `assigned_by`, `status`) VALUES
(3, 1, 15, '2024-07-25 19:10:45', 1, 'Completed'),
(4, 2, 7, '2024-11-18 16:20:05', 1, 'Completed'),
(5, 3, 3, '2024-11-18 17:23:46', 1, 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pickup_point` varchar(255) NOT NULL,
  `hosp_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `pickup_point`, `hosp_id`, `patient_id`, `booking_date`, `booking_time`, `created_at`) VALUES
(1, 2, 'Near Post office green town', 3, 1, '2024-07-24', '00:29:00', '2024-07-23 19:29:03'),
(2, 1, 'Near Post office green town', 5, 3, '2024-11-18', '21:14:00', '2024-11-18 16:14:57'),
(3, 1, '123', 2, 5, '2024-11-18', '22:23:00', '2024-11-18 17:22:09');

-- --------------------------------------------------------

--
-- Table structure for table `detailed_driver_record`
--

CREATE TABLE `detailed_driver_record` (
  `assignment_id` int(11) NOT NULL,
  `ambulance_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `booking_id` int(11) DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detailed_driver_record`
--

INSERT INTO `detailed_driver_record` (`assignment_id`, `ambulance_id`, `driver_id`, `assigned_at`, `booking_id`, `status`) VALUES
(6, 7, 7, '2024-11-18 16:20:05', 2, 'active'),
(7, 3, 3, '2024-11-18 17:23:46', 3, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `diseases`
--

CREATE TABLE `diseases` (
  `disease_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diseases`
--

INSERT INTO `diseases` (`disease_id`, `name`) VALUES
(1, 'Cardiovascular Disease'),
(2, 'Diabetes'),
(3, 'Hypertension'),
(4, 'Asthma'),
(5, 'Chronic Obstructive Pulmonary Disease'),
(6, 'Cancer'),
(7, 'Arthritis'),
(8, 'Kidney Disease'),
(9, 'Liver Disease'),
(10, 'HIV/AIDS'),
(11, 'Influenza'),
(12, 'Pneumonia'),
(13, 'Tuberculosis'),
(14, 'Malaria'),
(15, 'Dengue'),
(16, 'Cholera'),
(17, 'Typhoid'),
(18, 'Hepatitis A'),
(19, 'Hepatitis B'),
(20, 'Hepatitis C'),
(21, 'Measles'),
(22, 'Mumps'),
(23, 'Rubella'),
(24, 'Chickenpox'),
(25, 'Shingles'),
(26, 'Lyme Disease'),
(27, 'Zika Virus'),
(28, 'Ebola Virus'),
(29, 'Meningitis'),
(30, 'Encephalitis'),
(31, 'Alzheimer’s Disease'),
(32, 'Parkinson’s Disease'),
(33, 'Epilepsy'),
(34, 'Multiple Sclerosis'),
(35, 'Autism Spectrum Disorder'),
(36, 'Depression'),
(37, 'Anxiety'),
(38, 'Bipolar Disorder'),
(39, 'Schizophrenia'),
(40, 'Obsessive-Compulsive Disorder');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `specialty` varchar(255) NOT NULL,
  `hosp_id` int(11) NOT NULL,
  `availability` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `name`, `specialty`, `hosp_id`, `availability`) VALUES
(6, 'Dr. John Smith', 'Cardiologist', 2, 'Mon-Fri 9am-4pm'),
(7, 'Dr. Emily Brown', 'Endocrinologist', 1, 'Mon-Fri 8am-4pm'),
(8, 'Dr. Michael White', 'Nephrologist', 2, 'Mon-Thu 10am-6pm'),
(9, 'Dr. Sarah Davis', 'Pulmonologist', 2, 'Tue-Sat 9am-5pm'),
(10, 'Dr. David Wilson', 'Oncologist', 3, 'Mon-Fri 9am-5pm'),
(11, 'Dr. Mary Taylor', 'Rheumatologist', 3, 'Mon-Fri 10am-6pm'),
(12, 'Dr. James Brown', 'Infectious Disease Specialist', 4, 'Mon-Fri 9am-5pm'),
(13, 'Dr. Linda Johnson', 'Neurologist', 4, 'Mon-Thu 9am-5pm'),
(14, 'Dr. Barbara Clark', 'Psychiatrist', 5, 'Mon-Fri 9am-5pm'),
(15, 'Dr. Jolly', 'Psychiatrist', 4, 'Mon-Fri 9am-4pm');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `driver_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `status` enum('Available','Unavailable') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`driver_id`, `name`, `license_number`, `phone_number`, `status`, `created_at`) VALUES
(1, 'Amir Khan', 'D12345678', '555-1235', 'Available', '2024-07-20 06:21:28'),
(2, 'Jane Smith', 'D23456789', '555-2345', 'Available', '2024-07-20 06:21:28'),
(3, 'Robert Brown', 'D34567890', '555-3456', 'Available', '2024-07-20 06:21:28'),
(4, 'Emily Johnson', 'D45678901', '555-4567', 'Available', '2024-07-20 06:21:28'),
(5, 'Michael Williams', 'D56789012', '555-5678', 'Unavailable', '2024-07-20 06:21:28'),
(6, 'Linda Jones', 'D67890123', '555-6789', 'Available', '2024-07-20 06:21:28'),
(7, 'David Garcia', 'D78901234', '555-7890', 'Available', '2024-07-20 06:21:28'),
(8, 'Sarah Martinez', 'D89012345', '555-8901', 'Unavailable', '2024-07-20 06:21:28'),
(9, 'James Rodriguez', 'D90123456', '555-9012', 'Available', '2024-07-20 06:21:28'),
(10, 'Patricia Davis', 'D01234567', '555-0123', 'Available', '2024-07-20 06:21:28');

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `hosp_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `specialties` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`hosp_id`, `name`, `phone`, `email`, `address`, `specialties`) VALUES
(1, 'General Hospital', '+1 (217) 555-0101', 'hospital1@example.com', '1234 Elm Street, Springfield, IL, 62704', 'Cardiology, Endocrinology, Nephrology, Pulmonology, Oncology, Rheumatology, Infectious Disease, Gastroenterology, Neurology, Psychiatry'),
(2, 'City Hospital', '+1 (217) 555-0103', 'hospital3@example.com', '9101 Maple Road, Springfield, IL, 62706', 'Infectious Disease, Pulmonology, Endocrinology, Nephrology, Hepatology, Rheumatology, Neurology, Psychiatry, Dermatology, Cardiology'),
(3, 'Community Hospital', '+1 (217) 555-0105', 'hospital5@example.com', '1457 Ring Road, Springfield, IL, 62708', 'Pediatrics, Infectious Disease, Neurology, Psychiatry, Oncology, Cardiology, Endocrinology, Rheumatology, Gastroenterology, Pulmonology'),
(4, 'Specialized Hospital', '+1 (217) 555-0104', 'hospital4@example.com', '1213 Birch Street, Springfield, IL, 62707', 'Neurology, Psychiatry, Rheumatology, Infectious Disease, Oncology, Endocrinology, Cardiology, Nephrology, Pulmonology, Gastroenterology'),
(5, 'Medical Center', '+1 (217) 555-0102', 'hospital2@example.com', '5678 Oak Avenue, Springfield, IL, 62705', 'Cardiology, Oncology, Infectious Disease, Neurology, Psychiatry, Pulmonology, Endocrinology, Rheumatology');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `disease_id` int(11) NOT NULL,
  `patient_status` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `patient_name` varchar(100) NOT NULL,
  `patient_age` int(11) NOT NULL,
  `patient_gender` enum('Male','Female','Other') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `disease_id`, `patient_status`, `created_at`, `patient_name`, `patient_age`, `patient_gender`) VALUES
(1, 7, 'Serious Condtion', '2024-07-23 19:29:03', 'Qadeer', 55, 'Male'),
(2, 37, 'dfd', '2024-07-25 18:53:36', 'Qadeer', 45, 'Male'),
(3, 36, 'Normal Condition', '2024-11-18 16:14:57', 'Qadeer', 23, 'Male'),
(4, 31, 'UNKOWN', '2024-11-18 17:21:36', 'Zakir', 34, 'Male'),
(5, 31, 'UNKOWN', '2024-11-18 17:22:09', 'Zakir', 34, 'Male');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'Asif Khan', 'asif@gmail.com', '$2y$10$26pBkq1HlNY5lEMwcZeqx.dnRF/VM/q8vaFb0ZtTjdmK/1JQlM9mC', '2024-07-18 18:24:15'),
(2, 'Amir Aziz', 'amir@gmail.com', '$2y$10$BJBBfAjptyZvIEFWlRgdYeMdqX16XNYTcjeQG..T9QCyo0rE.nIUS', '2024-07-23 18:14:54'),
(3, 'Asad', 'asad@gmail.com', '$2y$10$Ay7eC//eeUQMa4s1HIM9o.sMW0cvgCQ29R3ao49JkUw5XuJA47dPG', '2024-11-19 16:52:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `ambulances`
--
ALTER TABLE `ambulances`
  ADD PRIMARY KEY (`ambulance_id`);

--
-- Indexes for table `ambulance_driver_assignment`
--
ALTER TABLE `ambulance_driver_assignment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ambulance_id` (`ambulance_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `ambulance_hospital_assignment`
--
ALTER TABLE `ambulance_hospital_assignment`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `ambulance_id` (`ambulance_id`),
  ADD KEY `hosp_id` (`hosp_id`);

--
-- Indexes for table `ambulance_user_assignment`
--
ALTER TABLE `ambulance_user_assignment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `ambulance_id` (`ambulance_id`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `destination_id` (`hosp_id`),
  ADD KEY `bookings_ibfk_3` (`patient_id`);

--
-- Indexes for table `detailed_driver_record`
--
ALTER TABLE `detailed_driver_record`
  ADD PRIMARY KEY (`assignment_id`),
  ADD KEY `ambulance_id` (`ambulance_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `diseases`
--
ALTER TABLE `diseases`
  ADD PRIMARY KEY (`disease_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`driver_id`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`hosp_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `disease_id` (`disease_id`);

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
-- AUTO_INCREMENT for table `ambulances`
--
ALTER TABLE `ambulances`
  MODIFY `ambulance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `ambulance_driver_assignment`
--
ALTER TABLE `ambulance_driver_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ambulance_hospital_assignment`
--
ALTER TABLE `ambulance_hospital_assignment`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `ambulance_user_assignment`
--
ALTER TABLE `ambulance_user_assignment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `detailed_driver_record`
--
ALTER TABLE `detailed_driver_record`
  MODIFY `assignment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `diseases`
--
ALTER TABLE `diseases`
  MODIFY `disease_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `driver_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `hosp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ambulance_driver_assignment`
--
ALTER TABLE `ambulance_driver_assignment`
  ADD CONSTRAINT `ambulance_driver_assignment_ibfk_1` FOREIGN KEY (`ambulance_id`) REFERENCES `ambulances` (`ambulance_id`),
  ADD CONSTRAINT `ambulance_driver_assignment_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`);

--
-- Constraints for table `ambulance_hospital_assignment`
--
ALTER TABLE `ambulance_hospital_assignment`
  ADD CONSTRAINT `ambulance_hospital_assignment_ibfk_1` FOREIGN KEY (`ambulance_id`) REFERENCES `ambulances` (`ambulance_id`),
  ADD CONSTRAINT `ambulance_hospital_assignment_ibfk_2` FOREIGN KEY (`hosp_id`) REFERENCES `hospitals` (`hosp_id`);

--
-- Constraints for table `ambulance_user_assignment`
--
ALTER TABLE `ambulance_user_assignment`
  ADD CONSTRAINT `ambulance_user_assignment_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`),
  ADD CONSTRAINT `ambulance_user_assignment_ibfk_2` FOREIGN KEY (`ambulance_id`) REFERENCES `ambulances` (`ambulance_id`),
  ADD CONSTRAINT `ambulance_user_assignment_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`hosp_id`) REFERENCES `hospitals` (`hosp_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `detailed_driver_record`
--
ALTER TABLE `detailed_driver_record`
  ADD CONSTRAINT `detailed_driver_record_ibfk_1` FOREIGN KEY (`ambulance_id`) REFERENCES `ambulances` (`ambulance_id`),
  ADD CONSTRAINT `detailed_driver_record_ibfk_2` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`driver_id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`disease_id`) REFERENCES `diseases` (`disease_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
