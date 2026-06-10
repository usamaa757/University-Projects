-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 29, 2025 at 04:08 PM
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
-- Database: `prms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Administrator', 'admin@gmail.com', '$2y$10$GH4pCLZOg0KE4VK7BwThBuqTmjt2azc39qSs4KVYGte9Ct5N/GGr.', '2025-04-25 06:36:02');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `status` enum('Pending','Scheduled','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `status` enum('approved','rejected','pending') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_history`
--

CREATE TABLE `family_history` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `condition_name` varchar(100) DEFAULT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `status` enum('Positive','Negative') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_history`
--

CREATE TABLE `medical_history` (
  `history_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `disease_name` varchar(100) DEFAULT NULL,
  `diagnosis_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `medicine_id` int(11) NOT NULL,
  `medicine_name` varchar(100) NOT NULL,
  `dosage` varchar(50) DEFAULT NULL,
  `frequency` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`medicine_id`, `medicine_name`, `dosage`, `frequency`) VALUES
(1, 'Paracetamol', '500mg', 'Every 6 hours'),
(2, 'Ibuprofen', '400mg', 'Twice daily'),
(3, 'Amoxicillin', '250mg', 'Three times a day'),
(4, 'Azithromycin', '500mg', 'Once daily'),
(5, 'Ciprofloxacin', '500mg', 'Twice daily'),
(6, 'Metronidazole', '400mg', 'Three times a day'),
(7, 'Aspirin', '75mg', 'Once daily'),
(8, 'Loratadine', '10mg', 'Once daily'),
(9, 'Cetirizine', '10mg', 'Once daily'),
(10, 'Omeprazole', '20mg', 'Once daily'),
(11, 'Pantoprazole', '40mg', 'Once daily'),
(12, 'Ranitidine', '150mg', 'Twice daily'),
(13, 'Dicyclomine', '20mg', 'Three times a day'),
(14, 'Domperidone', '10mg', 'Three times a day'),
(15, 'Ondansetron', '4mg', 'Twice daily'),
(16, 'Chlorpheniramine', '4mg', 'Every 4–6 hours'),
(17, 'Diphenhydramine', '25mg', 'Every 6 hours'),
(18, 'Clindamycin', '300mg', 'Three times a day'),
(19, 'Doxycycline', '100mg', 'Twice daily'),
(20, 'Erythromycin', '250mg', 'Four times a day'),
(21, 'Hydrochlorothiazide', '25mg', 'Once daily'),
(22, 'Losartan', '50mg', 'Once daily'),
(23, 'Amlodipine', '5mg', 'Once daily'),
(24, 'Metoprolol', '50mg', 'Twice daily'),
(25, 'Atenolol', '50mg', 'Once daily'),
(26, 'Furosemide', '40mg', 'Once daily'),
(27, 'Spironolactone', '25mg', 'Once daily'),
(28, 'Simvastatin', '20mg', 'Once daily'),
(29, 'Atorvastatin', '10mg', 'Once daily'),
(30, 'Rosuvastatin', '10mg', 'Once daily'),
(31, 'Levothyroxine', '50mcg', 'Once daily'),
(32, 'Insulin Glargine', '10 units', 'Once daily'),
(33, 'Insulin Lispro', '5 units', 'Before meals'),
(34, 'Glimepiride', '1mg', 'Once daily'),
(35, 'Metformin', '500mg', 'Twice daily'),
(36, 'Pioglitazone', '15mg', 'Once daily'),
(37, 'Prednisone', '10mg', 'Once daily'),
(38, 'Hydrocortisone', '100mg', 'Every 6 hours'),
(39, 'Salbutamol', '100mcg', 'As needed'),
(40, 'Budesonide', '200mcg', 'Twice daily'),
(41, 'Montelukast', '10mg', 'Once daily'),
(42, 'Cetirizine + Pseudoephedrine', '10/120mg', 'Twice daily'),
(43, 'Rifampicin', '600mg', 'Once daily'),
(44, 'Isoniazid', '300mg', 'Once daily'),
(45, 'Ethambutol', '800mg', 'Once daily'),
(46, 'Pyrazinamide', '1500mg', 'Once daily'),
(47, 'Vitamin C', '500mg', 'Once daily'),
(48, 'Vitamin D3', '1000 IU', 'Once daily'),
(49, 'Calcium Carbonate', '500mg', 'Once daily'),
(50, 'Iron Folic Acid', '150mg/0.5mg', 'Once daily'),
(51, 'Zinc Sulfate', '20mg', 'Once daily'),
(52, 'Magnesium Oxide', '250mg', 'Once daily'),
(53, 'Folic Acid', '5mg', 'Once daily'),
(54, 'B Complex', '1 tablet', 'Once daily'),
(55, 'Oral Rehydration Salt', '-', 'As needed'),
(56, 'Mebendazole', '100mg', 'Twice daily for 3 days'),
(57, 'Albendazole', '400mg', 'Single dose'),
(58, 'Loperamide', '2mg', 'After each loose stool'),
(59, 'Bisacodyl', '5mg', 'At bedtime'),
(60, 'Docusate Sodium', '100mg', 'Twice daily'),
(61, 'Povidone Iodine', '-', 'Twice daily'),
(62, 'Hydrocortisone Cream', '1%', 'Twice daily'),
(63, 'Clotrimazole', '1%', 'Twice daily'),
(64, 'Miconazole', '2%', 'Twice daily'),
(65, 'Betamethasone', '0.05%', 'Twice daily'),
(66, 'Gentamicin', '0.1%', 'Twice daily'),
(67, 'Neomycin', '0.5%', 'Twice daily'),
(68, 'Tetracycline Eye Ointment', '1%', 'Twice daily'),
(69, 'Ciprofloxacin Eye Drops', '0.3%', 'Four times a day'),
(70, 'Timolol Eye Drops', '0.5%', 'Twice daily'),
(71, 'Latanoprost Eye Drops', '0.005%', 'Once daily'),
(72, 'Artificial Tears', '-', 'As needed'),
(73, 'Acetaminophen', '500mg', 'Every 6 hours'),
(74, 'Codeine + Acetaminophen', '30/300mg', 'Every 6 hours'),
(75, 'Tramadol', '50mg', 'Every 6 hours'),
(76, 'Morphine', '10mg', 'Every 4 hours'),
(77, 'Naloxone', '0.4mg', 'As needed'),
(78, 'Diazepam', '5mg', 'Twice daily'),
(79, 'Lorazepam', '1mg', 'Once daily'),
(80, 'Clonazepam', '0.5mg', 'Once daily'),
(81, 'Fluoxetine', '20mg', 'Once daily'),
(82, 'Sertraline', '50mg', 'Once daily'),
(83, 'Citalopram', '20mg', 'Once daily'),
(84, 'Haloperidol', '5mg', 'Twice daily'),
(85, 'Risperidone', '2mg', 'Once daily'),
(86, 'Olanzapine', '5mg', 'Once daily'),
(87, 'Carbamazepine', '200mg', 'Twice daily'),
(88, 'Valproic Acid', '250mg', 'Twice daily'),
(89, 'Phenytoin', '100mg', 'Three times a day'),
(90, 'Levothyroxine', '100mcg', 'Once daily'),
(91, 'Warfarin', '5mg', 'Once daily'),
(92, 'Heparin', '5000 IU', 'Every 8 hours'),
(93, 'Enoxaparin', '40mg', 'Once daily'),
(94, 'Clopidogrel', '75mg', 'Once daily'),
(95, 'Digoxin', '0.25mg', 'Once daily'),
(96, 'Nitroglycerin', '0.4mg', 'As needed'),
(97, 'Isosorbide Mononitrate', '30mg', 'Once daily');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `age` varchar(10) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `contact` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `disease` varchar(255) DEFAULT NULL,
  `registration_date` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','checked') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_instructions`
--

CREATE TABLE `patient_instructions` (
  `instruction_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `treatment_id` int(11) DEFAULT NULL,
  `pre_procedure` text DEFAULT NULL,
  `post_procedure` text DEFAULT NULL,
  `post_discharge` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `doctor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `test_id` int(11) NOT NULL,
  `test_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`test_id`, `test_name`) VALUES
(1, 'Blood Test'),
(2, 'X-Ray'),
(3, 'MRI'),
(4, 'CT Scan'),
(5, 'Urine Test'),
(6, 'ECG (Electrocardiogram)'),
(7, 'EEG (Electroencephalogram)'),
(8, 'Ultrasound'),
(9, 'Liver Function Test'),
(10, 'Kidney Function Test'),
(11, 'Thyroid Test'),
(12, 'Blood Sugar Test'),
(13, 'HbA1c Test'),
(14, 'Lipid Profile'),
(15, 'Complete Blood Count (CBC)'),
(16, 'Vitamin D Test'),
(17, 'Vitamin B12 Test'),
(18, 'Stool Test'),
(19, 'Sputum Test'),
(20, 'Chest X-Ray'),
(21, 'Echocardiogram'),
(22, 'Allergy Test'),
(23, 'COVID-19 Test'),
(24, 'Dengue Test'),
(25, 'Malaria Test'),
(26, 'HIV Test'),
(27, 'Pregnancy Test'),
(28, 'Pap Smear'),
(29, 'Prostate-Specific Antigen (PSA) Test'),
(30, 'Bone Density Test');

-- --------------------------------------------------------

--
-- Table structure for table `test_reports`
--

CREATE TABLE `test_reports` (
  `report_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatment`
--

CREATE TABLE `treatment` (
  `treatment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `suggested_treatment` text NOT NULL,
  `treatment_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_medicines`
--

CREATE TABLE `treatment_medicines` (
  `id` int(11) NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `medicine_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_tests`
--

CREATE TABLE `treatment_tests` (
  `id` int(11) NOT NULL,
  `treatment_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','doctor','staff') DEFAULT 'doctor',
  `full_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `family_history`
--
ALTER TABLE `family_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_history`
--
ALTER TABLE `medical_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`medicine_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indexes for table `patient_instructions`
--
ALTER TABLE `patient_instructions`
  ADD PRIMARY KEY (`instruction_id`),
  ADD KEY `pateint_instruction_ibfk_1` (`doctor_id`),
  ADD KEY `pateint_instruction_ibfk_2` (`patient_id`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `test_reports`
--
ALTER TABLE `test_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `treatment_id` (`treatment_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `treatment`
--
ALTER TABLE `treatment`
  ADD PRIMARY KEY (`treatment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `treatment_ibfk_2` (`doctor_id`);

--
-- Indexes for table `treatment_medicines`
--
ALTER TABLE `treatment_medicines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `treatment_id` (`treatment_id`),
  ADD KEY `medicine_id` (`medicine_id`);

--
-- Indexes for table `treatment_tests`
--
ALTER TABLE `treatment_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `treatment_id` (`treatment_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `doctor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_history`
--
ALTER TABLE `family_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_history`
--
ALTER TABLE `medical_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `medicine_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_instructions`
--
ALTER TABLE `patient_instructions`
  MODIFY `instruction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `test_reports`
--
ALTER TABLE `test_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatment`
--
ALTER TABLE `treatment`
  MODIFY `treatment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatment_medicines`
--
ALTER TABLE `treatment_medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `treatment_tests`
--
ALTER TABLE `treatment_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `medical_history`
--
ALTER TABLE `medical_history`
  ADD CONSTRAINT `medical_history_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_instructions`
--
ALTER TABLE `patient_instructions`
  ADD CONSTRAINT `pateint_instruction_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `pateint_instruction_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `test_reports`
--
ALTER TABLE `test_reports`
  ADD CONSTRAINT `test_reports_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `test_reports_ibfk_2` FOREIGN KEY (`treatment_id`) REFERENCES `treatment` (`treatment_id`),
  ADD CONSTRAINT `test_reports_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`);

--
-- Constraints for table `treatment`
--
ALTER TABLE `treatment`
  ADD CONSTRAINT `treatment_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `treatment_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`);

--
-- Constraints for table `treatment_medicines`
--
ALTER TABLE `treatment_medicines`
  ADD CONSTRAINT `treatment_medicines_ibfk_1` FOREIGN KEY (`treatment_id`) REFERENCES `treatment` (`treatment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `treatment_medicines_ibfk_2` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`medicine_id`) ON DELETE CASCADE;

--
-- Constraints for table `treatment_tests`
--
ALTER TABLE `treatment_tests`
  ADD CONSTRAINT `treatment_tests_ibfk_1` FOREIGN KEY (`treatment_id`) REFERENCES `treatment` (`treatment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `treatment_tests_ibfk_2` FOREIGN KEY (`test_id`) REFERENCES `tests` (`test_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
