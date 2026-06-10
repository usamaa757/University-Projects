-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2025 at 06:57 PM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `created_at`, `status`) VALUES
(1, 'Admin User', 'admin@gmail.com', '$2y$10$GH4pCLZOg0KE4VK7BwThBuqTmjt2azc39qSs4KVYGte9Ct5N/GGr.', '2025-06-14 05:12:13', 'accepted');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `status` enum('Pending','Accepted','Cancelled','Completed') DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `confirmed_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `care_plans`
--

CREATE TABLE `care_plans` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `care_plan_steps`
--

CREATE TABLE `care_plan_steps` (
  `id` int(11) NOT NULL,
  `care_plan_id` int(11) DEFAULT NULL,
  `step_number` int(11) DEFAULT NULL,
  `step_description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `availability` enum('Available','Unavailable') DEFAULT 'Available',
  `status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `family_history`
--

CREATE TABLE `family_history` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `relative_name` varchar(100) DEFAULT NULL,
  `relation` varchar(50) DEFAULT NULL,
  `status` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medicines`
--

CREATE TABLE `medicines` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `added_on` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `medicines`
--

INSERT INTO `medicines` (`id`, `name`, `type`, `description`, `added_on`) VALUES
(1, 'Paracetamol', 'Tablet', 'Pain reliever and fever reducer', '2025-06-15 05:50:36'),
(2, 'Ibuprofen', 'Tablet', 'Anti-inflammatory painkiller', '2025-06-15 05:50:36'),
(3, 'Amoxicillin', 'Capsule', 'Antibiotic for bacterial infections', '2025-06-15 05:50:36'),
(4, 'Cephalexin', 'Capsule', 'Antibiotic for respiratory infections', '2025-06-15 05:50:36'),
(5, 'Azithromycin', 'Tablet', 'Macrolide antibiotic', '2025-06-15 05:50:36'),
(6, 'Ciprofloxacin', 'Tablet', 'Broad-spectrum antibiotic', '2025-06-15 05:50:36'),
(7, 'Metformin', 'Tablet', 'For type 2 diabetes', '2025-06-15 05:50:36'),
(8, 'Glimepiride', 'Tablet', 'Blood sugar control', '2025-06-15 05:50:36'),
(9, 'Atorvastatin', 'Tablet', 'Cholesterol lowering', '2025-06-15 05:50:36'),
(10, 'Rosuvastatin', 'Tablet', 'Low-density lipoprotein control', '2025-06-15 05:50:36'),
(11, 'Omeprazole', 'Capsule', 'Gastric acid suppression', '2025-06-15 05:50:36'),
(12, 'Pantoprazole', 'Tablet', 'GERD and ulcer treatment', '2025-06-15 05:50:36'),
(13, 'Losartan', 'Tablet', 'Hypertension management', '2025-06-15 05:50:36'),
(14, 'Amlodipine', 'Tablet', 'Treatment of high blood pressure', '2025-06-15 05:50:36'),
(15, 'Enalapril', 'Tablet', 'ACE inhibitor', '2025-06-15 05:50:36'),
(16, 'Hydrochlorothiazide', 'Tablet', 'Diuretic for hypertension', '2025-06-15 05:50:36'),
(17, 'Levothyroxine', 'Tablet', 'Hypothyroidism treatment', '2025-06-15 05:50:36'),
(18, 'Salbutamol', 'Inhaler', 'Bronchodilator for asthma', '2025-06-15 05:50:36'),
(19, 'Fluticasone', 'Inhaler', 'Asthma/COPD maintenance', '2025-06-15 05:50:36'),
(20, 'Cetirizine', 'Tablet', 'Allergy relief', '2025-06-15 05:50:36'),
(21, 'Loratadine', 'Tablet', 'Non-drowsy antihistamine', '2025-06-15 05:50:36'),
(22, 'Montelukast', 'Tablet', 'Allergic rhinitis treatment', '2025-06-15 05:50:36'),
(23, 'Prednisone', 'Tablet', 'Corticosteroid anti-inflammatory', '2025-06-15 05:50:36'),
(24, 'Hydrocortisone', 'Cream', 'Topical corticosteroid', '2025-06-15 05:50:36'),
(25, 'Clotrimazole', 'Cream', 'Antifungal treatment', '2025-06-15 05:50:36'),
(26, 'Fluconazole', 'Tablet', 'Systemic antifungal', '2025-06-15 05:50:36'),
(27, 'Metronidazole', 'Tablet', 'Antiprotozoal/antibiotic', '2025-06-15 05:50:36'),
(28, 'Doxycycline', 'Capsule', 'Antibiotic for acne, rosacea', '2025-06-15 05:50:36'),
(29, 'Azathioprine', 'Tablet', 'Immunosuppressant', '2025-06-15 05:50:36'),
(30, 'Methotrexate', 'Tablet', 'Used for rheumatoid arthritis', '2025-06-15 05:50:36'),
(31, 'Insulin Lispro', 'Injection', 'Rapid‑acting insulin', '2025-06-15 05:50:36'),
(32, 'Insulin Glargine', 'Injection', 'Long‑acting insulin', '2025-06-15 05:50:36'),
(33, 'Warfarin', 'Tablet', 'Anticoagulant', '2025-06-15 05:50:36'),
(34, 'Enoxaparin', 'Injection', 'Low molecular weight heparin', '2025-06-15 05:50:36'),
(35, 'Dabigatran', 'Capsule', 'Direct thrombin inhibitor', '2025-06-15 05:50:36'),
(36, 'Apixaban', 'Tablet', 'Factor Xa inhibitor', '2025-06-15 05:50:36'),
(37, 'Clopidogrel', 'Tablet', 'Antiplatelet agent', '2025-06-15 05:50:36'),
(38, 'Aspirin', 'Tablet', 'Low-dose cardioprotective', '2025-06-15 05:50:36'),
(39, 'Digoxin', 'Tablet', 'Heart failure treatment', '2025-06-15 05:50:36'),
(40, 'Furosemide', 'Tablet', 'Loop diuretic', '2025-06-15 05:50:36'),
(41, 'Alprazolam', 'Tablet', 'Anxiety treatment', '2025-06-15 05:50:36'),
(42, 'Diazepam', 'Tablet', 'Benzodiazepine for anxiety', '2025-06-15 05:50:36'),
(43, 'Sertraline', 'Tablet', 'Antidepressant (SSRI)', '2025-06-15 05:50:36'),
(44, 'Fluoxetine', 'Tablet', 'Antidepressant (SSRI)', '2025-06-15 05:50:36'),
(45, 'Methadone', 'Tablet', 'Opioid dependence therapy', '2025-06-15 05:50:36'),
(46, 'Morphine', 'Injection', 'Severe pain relief', '2025-06-15 05:50:36'),
(47, 'Tramadol', 'Tablet', 'Moderate to severe pain', '2025-06-15 05:50:36'),
(48, 'Codeine', 'Tablet', 'Mild pain relief', '2025-06-15 05:50:36'),
(49, 'Gabapentin', 'Capsule', 'Neuropathic pain', '2025-06-15 05:50:36'),
(50, 'Pregabalin', 'Capsule', 'Neuropathic pain treatment', '2025-06-15 05:50:36'),
(51, 'Alprazolam ODT', 'Tablet', 'Orally disintegrating anxiety med', '2025-06-15 05:50:36'),
(52, 'Metoclopramide', 'Tablet', 'Antiemetic', '2025-06-15 05:50:36'),
(53, 'Ondansetron', 'Tablet', 'Nausea & vomiting relief', '2025-06-15 05:50:36'),
(54, 'Loperamide', 'Tablet', 'Diarrhea control', '2025-06-15 05:50:36'),
(55, 'Bisacodyl', 'Tablet', 'Laxative stimulant', '2025-06-15 05:50:36'),
(56, 'Simvastatin', 'Tablet', 'Cholesterol lowering', '2025-06-15 05:50:36'),
(57, 'Ezetimibe', 'Tablet', 'Cholesterol absorption inhibitor', '2025-06-15 05:50:36'),
(58, 'Fenofibrate', 'Tablet', 'Triglyceride reducer', '2025-06-15 05:50:36'),
(59, 'Niacin', 'Tablet', 'Vitamin B3 for cholesterol', '2025-06-15 05:50:36'),
(60, 'Coenzyme Q10', 'Capsule', 'Antioxidant supplement', '2025-06-15 05:50:36'),
(61, 'Vitamin D3', 'Tablet', 'Bone health support', '2025-06-15 05:50:36'),
(62, 'Calcium Carbonate', 'Tablet', 'Calcium supplement', '2025-06-15 05:50:36'),
(63, 'Ferrous Sulfate', 'Tablet', 'Iron supplement', '2025-06-15 05:50:36'),
(64, 'Zinc Sulfate', 'Tablet', 'Mineral supplement', '2025-06-15 05:50:36'),
(65, 'Omega-3 Fish Oil', 'Capsule', 'Heart health supplement', '2025-06-15 05:50:36'),
(66, 'Probiotic Capsule', 'Capsule', 'Gut health support', '2025-06-15 05:50:36'),
(67, 'Methocarbamol', 'Tablet', 'Muscle relaxant', '2025-06-15 05:50:36'),
(68, 'Cyclobenzaprine', 'Tablet', 'Muscle relaxer', '2025-06-15 05:50:36'),
(69, 'Tamsulosin', 'Capsule', 'Benign prostatic hyperplasia', '2025-06-15 05:50:36'),
(70, 'Finasteride', 'Tablet', 'Hair loss / BPH therapy', '2025-06-15 05:50:36'),
(71, 'Sildenafil', 'Tablet', 'Erectile dysfunction', '2025-06-15 05:50:36'),
(72, 'Tadalafil', 'Tablet', 'Erectile dysfunction', '2025-06-15 05:50:36'),
(73, 'Sertraline O.S.', 'Tablet', 'Extended-release antidepressant', '2025-06-15 05:50:36'),
(74, 'Memantine', 'Tablet', 'Alzheimer’s treatment', '2025-06-15 05:50:36'),
(75, 'Levodopa', 'Tablet', 'Parkinson’s disease', '2025-06-15 05:50:36'),
(76, 'Carbidopa/Levodopa', 'Tablet', 'Parkinson’s combo treatment', '2025-06-15 05:50:36'),
(77, 'Alendronate', 'Tablet', 'Osteoporosis treatment', '2025-06-15 05:50:36'),
(78, 'Albuterol', 'Inhaler', 'Asthma bronchodilator', '2025-06-15 05:50:36'),
(79, 'Tiotropium', 'Inhaler', 'COPD maintenance', '2025-06-15 05:50:36'),
(80, 'Montelukast O.S.', 'Tablet', 'Extended-release allergy relief', '2025-06-15 05:50:36'),
(81, 'Clarithromycin', 'Tablet', 'Antibiotic respiratory infections', '2025-06-15 05:50:36'),
(82, 'Levofloxacin', 'Tablet', 'Broad-spectrum antibiotic', '2025-06-15 05:50:36'),
(83, 'Moxifloxacin', 'Tablet', 'Respiratory tract infections', '2025-06-15 05:50:36'),
(84, 'Ranitidine', 'Tablet', 'H2 blocker for ulcers', '2025-06-15 05:50:36'),
(85, 'Phenylephrine', 'Tablet', 'Decongestant', '2025-06-15 05:50:36'),
(86, 'Pseudoephedrine', 'Tablet', 'Nasal decongestant', '2025-06-15 05:50:36'),
(87, 'Dextromethorphan', 'Tablet', 'Cough suppressant', '2025-06-15 05:50:36');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `disease` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_care_plans`
--

CREATE TABLE `patient_care_plans` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `care_plan_id` int(11) DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` datetime DEFAULT current_timestamp(),
  `status` enum('active','completed','cancelled') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_care_progress`
--

CREATE TABLE `patient_care_progress` (
  `id` int(11) NOT NULL,
  `patient_care_plan_id` int(11) DEFAULT NULL,
  `care_plan_step_id` int(11) DEFAULT NULL,
  `status` enum('pending','in_progress','done') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_instructions`
--

CREATE TABLE `patient_instructions` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `instruction_type` enum('pre','post','discharge') NOT NULL,
  `instruction_text` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient_tests`
--

CREATE TABLE `patient_tests` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `assigned_date` datetime DEFAULT current_timestamp(),
  `status` enum('Pending','Completed') DEFAULT 'Pending',
  `remarks` text DEFAULT NULL,
  `report_file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `receptionists`
--

CREATE TABLE `receptionists` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tests`
--

INSERT INTO `tests` (`id`, `name`) VALUES
(1, 'Complete Blood Count (CBC)'),
(2, 'Blood Glucose Test'),
(3, 'Lipid Profile'),
(4, 'Liver Function Test (LFT)'),
(5, 'Kidney Function Test (KFT)'),
(6, 'Thyroid Function Test (TFT)'),
(7, 'Urinalysis'),
(8, 'Electrocardiogram (ECG)'),
(9, 'X-Ray Chest'),
(10, 'MRI Brain'),
(11, 'CT Scan Abdomen'),
(12, 'Ultrasound Pelvis'),
(13, 'Vitamin D Test'),
(14, 'Vitamin B12 Test'),
(15, 'COVID-19 PCR Test'),
(16, 'Malaria Test'),
(17, 'Dengue NS1 Antigen Test'),
(18, 'HIV Test'),
(19, 'Hepatitis B Test'),
(20, 'Hepatitis C Test'),
(21, 'Tuberculosis Test (Mantoux)'),
(22, 'Stool Test'),
(23, 'Echocardiogram'),
(24, 'Blood Pressure Monitoring'),
(25, 'Allergy Panel'),
(26, 'Blood Culture'),
(27, 'Pap Smear'),
(28, 'Prostate-Specific Antigen (PSA) Test'),
(29, 'Bone Density Test'),
(30, 'Eye Examination');

-- --------------------------------------------------------

--
-- Table structure for table `treatments`
--

CREATE TABLE `treatments` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `medicine_id` int(11) NOT NULL,
  `treatment` text DEFAULT NULL,
  `treatment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL,
  `email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `care_plans`
--
ALTER TABLE `care_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `care_plan_steps`
--
ALTER TABLE `care_plan_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `care_plan_id` (`care_plan_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `family_history`
--
ALTER TABLE `family_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medicines`
--
ALTER TABLE `medicines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `patient_care_plans`
--
ALTER TABLE `patient_care_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_care_progress`
--
ALTER TABLE `patient_care_progress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_instructions`
--
ALTER TABLE `patient_instructions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `patient_tests`
--
ALTER TABLE `patient_tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `test_id` (`test_id`);

--
-- Indexes for table `receptionists`
--
ALTER TABLE `receptionists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treatments`
--
ALTER TABLE `treatments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `treatments_ibfk_3` (`medicine_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `care_plans`
--
ALTER TABLE `care_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `care_plan_steps`
--
ALTER TABLE `care_plan_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `family_history`
--
ALTER TABLE `family_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medicines`
--
ALTER TABLE `medicines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_care_plans`
--
ALTER TABLE `patient_care_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_care_progress`
--
ALTER TABLE `patient_care_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_instructions`
--
ALTER TABLE `patient_instructions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_tests`
--
ALTER TABLE `patient_tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `receptionists`
--
ALTER TABLE `receptionists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `treatments`
--
ALTER TABLE `treatments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `care_plan_steps`
--
ALTER TABLE `care_plan_steps`
  ADD CONSTRAINT `care_plan_steps_ibfk_1` FOREIGN KEY (`care_plan_id`) REFERENCES `care_plans` (`id`);

--
-- Constraints for table `family_history`
--
ALTER TABLE `family_history`
  ADD CONSTRAINT `family_history_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_instructions`
--
ALTER TABLE `patient_instructions`
  ADD CONSTRAINT `patient_instructions_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `patient_tests`
--
ALTER TABLE `patient_tests`
  ADD CONSTRAINT `patient_tests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `patient_tests_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`),
  ADD CONSTRAINT `patient_tests_ibfk_3` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`);

--
-- Constraints for table `treatments`
--
ALTER TABLE `treatments`
  ADD CONSTRAINT `treatments_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`),
  ADD CONSTRAINT `treatments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `treatments_ibfk_3` FOREIGN KEY (`medicine_id`) REFERENCES `medicines` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
