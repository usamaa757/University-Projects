-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2024 at 01:07 AM
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
-- Database: `college_admission`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
(1, 'Talha', 'admin@gmail.com', '$2y$10$YhzONIZ3RCvD0T39Q39Zi.fo5EDOfwPQiRcB5PycBlSP1Ex4v7PTC'),
(2, 'hana', 'talha@gmail.com', '$2y$10$TzYUZ/nmb6kQN7B1cXlbLOKOjQlhQ8sUO6FB4mv1Pqiv32IAv8I.q');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`city_id`, `city_name`) VALUES
(1, 'Abbottabad'),
(2, 'Badin'),
(3, 'Bahawalpur'),
(4, 'Chakwal'),
(5, 'Chamaan'),
(6, 'Karachi'),
(7, 'Lahore'),
(8, 'Faislabad'),
(9, 'Multan'),
(10, 'Rawalpindi'),
(11, 'Islamabad'),
(12, 'Sukkar');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `country_id` int(11) NOT NULL,
  `country_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`country_id`, `country_name`) VALUES
(1, 'Afghanistan'),
(2, 'Åland Islands'),
(3, 'Albania'),
(4, 'Algeria'),
(5, 'American Samoa'),
(6, 'Andorra'),
(7, 'Angola'),
(8, 'Anguilla'),
(9, 'Antarctica'),
(10, 'Antigua and Barbuda'),
(11, 'Argentina'),
(12, 'Armenia'),
(13, 'Aruba'),
(14, 'Australia'),
(15, 'Austria'),
(16, 'Azerbaijan'),
(17, 'Bahamas'),
(18, 'Bahrain'),
(19, 'Bangladesh'),
(20, 'Barbados'),
(21, 'Belarus'),
(22, 'Belgium'),
(23, 'Belize'),
(24, 'Benin'),
(25, 'Bermuda'),
(26, 'Bhutan'),
(27, 'Bolivia'),
(28, 'Bosnia and Herzegovina'),
(29, 'Botswana'),
(30, 'Bouvet Island'),
(31, 'Brazil'),
(32, 'British Indian Ocean Territory'),
(33, 'Brunei Darussalam'),
(34, 'Bulgaria'),
(35, 'Burkina Faso'),
(36, 'Burundi'),
(37, 'Cabo Verde'),
(38, 'Cambodia'),
(39, 'Cameroon'),
(40, 'Canada'),
(41, 'Cayman Islands'),
(42, 'Central African Republic'),
(43, 'Chad'),
(44, 'Chile'),
(45, 'China'),
(46, 'Christmas Island'),
(47, 'Cocos (Keeling) Islands'),
(48, 'Colombia'),
(49, 'Comoros'),
(50, 'Congo'),
(51, 'Congo, Democratic Republic of the'),
(52, 'Cook Islands'),
(53, 'Costa Rica'),
(54, 'Croatia'),
(55, 'Cuba'),
(56, 'Curaçao'),
(57, 'Cyprus'),
(58, 'Czech Republic'),
(59, 'Denmark'),
(60, 'Djibouti'),
(61, 'Dominica'),
(62, 'Dominican Republic'),
(63, 'Ecuador'),
(64, 'Egypt'),
(65, 'El Salvador'),
(66, 'Equatorial Guinea'),
(67, 'Eritrea'),
(68, 'Estonia'),
(69, 'Eswatini'),
(70, 'Ethiopia'),
(71, 'Falkland Islands (Malvinas)'),
(72, 'Faroe Islands'),
(73, 'Fiji'),
(74, 'Finland'),
(75, 'France'),
(76, 'French Guiana'),
(77, 'French Polynesia'),
(78, 'French Southern Territories'),
(79, 'Gabon'),
(80, 'Gambia'),
(81, 'Georgia'),
(82, 'Germany'),
(83, 'Ghana'),
(84, 'Gibraltar'),
(85, 'Greece'),
(86, 'Greenland'),
(87, 'Grenada'),
(88, 'Guadeloupe'),
(89, 'Guam'),
(90, 'Guatemala'),
(91, 'Guernsey'),
(92, 'Guinea'),
(93, 'Guinea-Bissau'),
(94, 'Guyana'),
(95, 'Haiti'),
(96, 'Heard Island and McDonald Islands'),
(97, 'Holy See'),
(98, 'Honduras'),
(99, 'Hong Kong'),
(100, 'Hungary'),
(101, 'Iceland'),
(102, 'India'),
(103, 'Indonesia'),
(104, 'Iran'),
(105, 'Iraq'),
(106, 'Ireland'),
(107, 'Isle of Man'),
(108, 'Israel'),
(109, 'Italy'),
(110, 'Jamaica'),
(111, 'Japan'),
(112, 'Jersey'),
(113, 'Jordan'),
(114, 'Kazakhstan'),
(115, 'Kenya'),
(116, 'Kiribati'),
(117, 'Korea (North)'),
(118, 'Korea (South)'),
(119, 'Kuwait'),
(120, 'Kyrgyzstan'),
(121, 'Lao People\'s Democratic Republic'),
(122, 'Latvia'),
(123, 'Lebanon'),
(124, 'Lesotho'),
(125, 'Liberia'),
(126, 'Libya'),
(127, 'Liechtenstein'),
(128, 'Lithuania'),
(129, 'Luxembourg'),
(130, 'Macao'),
(131, 'Madagascar'),
(132, 'Malawi'),
(133, 'Malaysia'),
(134, 'Maldives'),
(135, 'Mali'),
(136, 'Malta'),
(137, 'Marshall Islands'),
(138, 'Martinique'),
(139, 'Mauritania'),
(140, 'Mauritius'),
(141, 'Mayotte'),
(142, 'Mexico'),
(143, 'Micronesia'),
(144, 'Moldova'),
(145, 'Monaco'),
(146, 'Mongolia'),
(147, 'Montenegro'),
(148, 'Montserrat'),
(149, 'Morocco'),
(150, 'Mozambique'),
(151, 'Myanmar'),
(152, 'Namibia'),
(153, 'Nauru'),
(154, 'Nepal'),
(155, 'Netherlands'),
(156, 'New Caledonia'),
(157, 'New Zealand'),
(158, 'Nicaragua'),
(159, 'Niger'),
(160, 'Nigeria'),
(161, 'Niue'),
(162, 'Norfolk Island'),
(163, 'North Macedonia'),
(164, 'Northern Mariana Islands'),
(165, 'Norway'),
(166, 'Oman'),
(167, 'Pakistan'),
(168, 'Palau'),
(169, 'Panama'),
(170, 'Papua New Guinea'),
(171, 'Paraguay'),
(172, 'Peru'),
(173, 'Philippines'),
(174, 'Pitcairn'),
(175, 'Poland'),
(176, 'Portugal'),
(177, 'Puerto Rico'),
(178, 'Qatar'),
(179, 'Romania'),
(180, 'Russian Federation'),
(181, 'Rwanda'),
(182, 'Réunion'),
(183, 'Saint Barthélemy'),
(184, 'Saint Helena'),
(185, 'Saint Kitts and Nevis'),
(186, 'Saint Lucia'),
(187, 'Saint Martin (French)'),
(188, 'Saint Pierre and Miquelon'),
(189, 'Saint Vincent and the Grenadines'),
(190, 'Samoa'),
(191, 'San Marino'),
(192, 'Sao Tome and Principe'),
(193, 'Saudi Arabia'),
(194, 'Senegal'),
(195, 'Serbia'),
(196, 'Seychelles'),
(197, 'Sierra Leone'),
(198, 'Singapore'),
(199, 'Sint Maarten (Dutch)'),
(200, 'Slovakia'),
(201, 'Slovenia'),
(202, 'Solomon Islands'),
(203, 'Somalia'),
(204, 'South Africa'),
(205, 'South Georgia and the South Sandwich Islands'),
(206, 'South Sudan'),
(207, 'Spain'),
(208, 'Sri Lanka'),
(209, 'Sudan'),
(210, 'Suriname'),
(211, 'Svalbard and Jan Mayen'),
(212, 'Sweden'),
(213, 'Switzerland'),
(214, 'Syrian Arab Republic'),
(215, 'Taiwan'),
(216, 'Tajikistan'),
(217, 'Tanzania'),
(218, 'Thailand'),
(219, 'Timor-Leste'),
(220, 'Togo'),
(221, 'Tokelau'),
(222, 'Tonga'),
(223, 'Trinidad and Tobago'),
(224, 'Tunisia'),
(225, 'Turkey'),
(226, 'Turkmenistan'),
(227, 'Tuvalu'),
(228, 'Uganda'),
(229, 'Ukraine'),
(230, 'United Arab Emirates'),
(231, 'United Kingdom'),
(232, 'United States of America'),
(233, 'Uruguay'),
(234, 'Uzbekistan'),
(235, 'Vanuatu'),
(236, 'Venezuela'),
(237, 'Viet Nam'),
(238, 'Western Sahara'),
(239, 'Yemen'),
(240, 'Zambia'),
(241, 'Zimbabwe');

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `education_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `qualification` varchar(50) NOT NULL,
  `institute_name` varchar(100) NOT NULL,
  `passing_year` int(11) NOT NULL,
  `grade` varchar(10) NOT NULL,
  `obtained_marks` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`education_id`, `student_id`, `qualification`, `institute_name`, `passing_year`, `grade`, `obtained_marks`, `total_marks`) VALUES
(1, 4, 'Matric', 'Virtual University', 2012, 'A', 700, 1100),
(2, 4, 'Intermediate', 'Virtual University', 2014, 'A', 850, 1100),
(5, 2, 'Metric', 'virtual university', 2012, 'a', 700, 1100),
(6, 2, 'intermediate', 'virtual university', 2014, 'A', 750, 1100),
(37, 1, 'Matric', 'Virtual University', 2012, 'A', 700, 1000),
(38, 1, 'Intermediate', 'Virtual University', 2014, 'A', 800, 1100),
(39, 3, 'Matric', 'Virtual University', 2010, 'B', 700, 1100),
(40, 3, 'Intermediate', 'Virtual University', 2012, 'B', 987, 1100),
(41, 5, 'Matric', 'Virtual University', 2010, 'a', 800, 1100),
(42, 5, 'Intermediate', 'Virtual University', 2012, 'b', 800, 1000),
(43, 6, 'Matric', 'Virtual University', 2010, 'a', 900, 1100),
(44, 6, 'Intermediate', 'Virtual University', 2012, 'a', 700, 1100);

-- --------------------------------------------------------

--
-- Table structure for table `student_marksheets`
--

CREATE TABLE `student_marksheets` (
  `marksheet_id` int(11) NOT NULL,
  `education_id` int(11) NOT NULL,
  `marksheet_type` enum('Matric','Intermediate') NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `marksheet_img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_marksheets`
--

INSERT INTO `student_marksheets` (`marksheet_id`, `education_id`, `marksheet_type`, `student_id`, `marksheet_img`) VALUES
(13, 1, 'Matric', 4, 'uploads/4_66db4f187dbef_login.PNG'),
(14, 2, 'Intermediate', 4, 'uploads/matric_1725715421_4_10443767712.jpg'),
(15, 5, 'Matric', 2, 'uploads/66dc71eab336b_matric_1725715421_4_10443767712.jpg'),
(16, 6, 'Intermediate', 2, 'uploads/66dc71eab7e2c_4_bulboff.jpg'),
(33, 37, 'Matric', 1, 'uploads/download (1).jpg'),
(34, 38, 'Intermediate', 1, 'uploads/download (1).png'),
(35, 39, 'Matric', 3, 'uploads/4_23_66db4e8dce9db_login.PNG'),
(36, 40, 'Intermediate', 3, 'uploads/4_pic 01.jpeg'),
(37, 41, 'Matric', 5, 'uploads/4_23_pic 03.jpeg'),
(38, 42, 'Intermediate', 5, 'uploads/4_pic 01.jpeg'),
(39, 43, 'Matric', 6, 'uploads/4_10443767712.jpg'),
(40, 44, 'Intermediate', 6, 'uploads/bulboff (1).jpg');

-- --------------------------------------------------------

--
-- Table structure for table `stud_admission`
--

CREATE TABLE `stud_admission` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `cnic` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `dob` date NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `postal_address` text NOT NULL,
  `residential_address` text NOT NULL,
  `qualification` varchar(255) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `photograph` varchar(255) DEFAULT NULL,
  `remarks` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stud_admission`
--

INSERT INTO `stud_admission` (`id`, `student_id`, `cnic`, `full_name`, `gender`, `dob`, `nationality`, `country`, `city`, `postal_address`, `residential_address`, `qualification`, `degree`, `program`, `photograph`, `remarks`, `status`) VALUES
(27, 4, '12345-1234567-7', 'Zakir', 'Male', '2002-11-11', 'international', 'Pakistan', 'Multan', '12345', 'Lahore', '', 'BS', 'Software Engineering', 'uploads/66db72fc1fb5d_4_23_pic 02.jpeg', 'not aplicable', 'rejected'),
(29, 2, '12345-1234567-8', 'asif', 'Male', '2002-11-11', 'pakistani', 'Austria', 'Badin', '12345', 'lahore', '', 'BS', 'Computer Science', 'uploads/66dc71eaab3a4_4_23_pic 02.jpeg', 'approved', 'approved'),
(46, 1, '12345-2132322-7', 'Talha', 'Male', '2001-11-05', 'International', 'Pakistan', 'Chamaan', '24000', 'Lahore', 'Matric', 'BS', 'Computer Science', 'uploads/66dca0024c7b5_4_23_pic 02.jpeg', 'Degree img is not readable pls upload again till 10 sep, 204', 'pending'),
(47, 3, '12345-1254567-8', 'Ali Ahmad', 'Male', '2001-11-11', 'pakistani', 'Pakistan', 'Rawalpindi', '12345', 'Lahore', 'Matric', '', 'Software Engineering', 'uploads/66dca122b6ae8_4_23_pic 02.jpeg', '', 'pending'),
(48, 5, '12345-2442322-7', 'ahmad', 'Male', '2024-09-08', 'pakistani', 'Armenia', 'Badin', '12345', 'lahore', 'Intermediate', 'BS', 'Software Engineering', 'uploads/66dcd47903395_4_23_pic 02.jpeg', '', 'pending'),
(49, 6, '12345-1234567-8', 'asif', 'Male', '2002-11-11', 'pakistani', 'Anguilla', 'Multan', '12345', 'karachi', 'Intermediate', 'BS', 'Computer Science', 'uploads/66dcdbdc4cba7_4_23_pic 03.jpeg', '', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users_reg`
--

CREATE TABLE `users_reg` (
  `id` int(11) NOT NULL,
  `full_name` varchar(222) NOT NULL,
  `email` varchar(222) NOT NULL,
  `password` varchar(222) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_reg`
--

INSERT INTO `users_reg` (`id`, `full_name`, `email`, `password`, `date`) VALUES
(1, 'Talha Bari', 'talha@gmail.com', '$2y$10$TzYUZ/nmb6kQN7B1cXlbLOKOjQlhQ8sUO6FB4mv1Pqiv32IAv8I.q', '2024-09-05 23:00:03'),
(2, 'Asif', 'asif@gmail.com', '$2y$10$TzYUZ/nmb6kQN7B1cXlbLOKOjQlhQ8sUO6FB4mv1Pqiv32IAv8I.q', '2024-09-06 08:53:26'),
(3, 'Ali Ahmad', 'ali@gmail.com', '$2y$10$P4iygFv.mivKMN/ZggAtuunnKKN9wHWN8NADhOgfAr69x1mbH8CVG', '2024-09-06 13:04:38'),
(4, 'zakir', 'zakir@gmail.com', '$2y$10$e47YFa/U2rFHPMTX1drxXePTrosh5n/rTV6BK8SPjpUIRvIlGMrcK', '2024-09-06 17:50:48'),
(5, 'Afnan Ahmad', 'afnan@gmail.com', '$2y$10$nr3l3g.TXFGuWZ28VXPmguVgXaTXbHMpgyXVHDSNizsDQ0O1F3ISu', '2024-09-07 22:12:30'),
(6, 'kami', 'kami@gmail.com', '$2y$10$LsH66.Q8mGEEwO94vc/QoOn27c8lh49bK5D9ZBlXWDI7G1pmZTPEG', '2024-09-07 22:56:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`education_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `student_marksheets`
--
ALTER TABLE `student_marksheets`
  ADD PRIMARY KEY (`marksheet_id`);

--
-- Indexes for table `stud_admission`
--
ALTER TABLE `stud_admission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users_reg`
--
ALTER TABLE `users_reg`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `student_marksheets`
--
ALTER TABLE `student_marksheets`
  MODIFY `marksheet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `stud_admission`
--
ALTER TABLE `stud_admission`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `users_reg`
--
ALTER TABLE `users_reg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users_reg` (`id`);

--
-- Constraints for table `stud_admission`
--
ALTER TABLE `stud_admission`
  ADD CONSTRAINT `stud_admission_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users_reg` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
