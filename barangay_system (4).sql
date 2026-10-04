-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2026 at 03:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `barangay_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `ID` int(11) NOT NULL,
  `STAFFID` int(11) NOT NULL,
  `ACTION_TYPE` varchar(255) NOT NULL,
  `TARGET_TABLE` varchar(255) NOT NULL,
  `TARGET_ID` int(11) NOT NULL,
  `TARGET_NAME` varchar(255) NOT NULL,
  `ACTION_DESCRIPTION` varchar(255) NOT NULL,
  `CREATE_AT` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`ID`, `STAFFID`, `ACTION_TYPE`, `TARGET_TABLE`, `TARGET_ID`, `TARGET_NAME`, `ACTION_DESCRIPTION`, `CREATE_AT`) VALUES
(4, 28, 'Delete', 'RESIDENT', 36, 'Joan Santos', 'Deleted resident: Joan Santos (ID: 36)', '2025-05-21 08:47:52'),
(5, 28, 'Add', 'RESIDENT', 37, 'Justine Santos', 'Added new resident: Justine Santos (ID: 37)', '2025-05-21 08:56:28'),
(6, 28, 'Update', 'RESIDENT', 37, 'JustineSS Santos', 'Updated resident: JustineSS Santos (ID: 37)', '2025-05-21 09:00:57'),
(7, 28, 'Update', 'RESIDENT', 37, 'Justine Santos', 'Updated resident: Justine Santos (ID: 37)', '2025-05-21 09:01:32'),
(8, 28, 'Add', 'BLOTTER2', 7, 'Justine Pagdanganan', 'Added new blotter record for complainant: Justine Pagdanganan (Blotter ID: 7)', '2025-05-21 09:04:40'),
(9, 28, 'Delete', 'BLOTTER2', 3, 'NB', 'Deleted blotter record: NB (ID: 3)', '2025-05-21 09:08:32'),
(10, 28, 'Update', 'BLOTTER2', 7, 'Justine Pagdanganan', 'Updated blotter record for complainant: Justine Pagdanganan (ID: 7)', '2025-05-21 09:12:23'),
(11, 28, 'Update', 'BLOTTER2', 7, 'Justine Pagdanganan', 'Updated blotter record for complainant: Justine Pagdanganan (ID: 7)', '2025-05-21 09:13:08'),
(12, 28, 'Delete', 'BUSINESS_PERMIT', 14, 'SDF', 'Deleted business permit: SDF (ID: 14)', '2025-05-21 09:20:14'),
(13, 28, 'Update', 'BUSINESS_PERMIT', 18, 'Joan Stores', 'Updated business permit: Joan Stores (ID: 18)', '2025-05-21 09:22:09'),
(14, 28, 'Add', 'business_permit', 19, 'Patatasan', 'Added new business permit: Patatasan (Permit #: 20250521-032209-10)', '2025-05-21 09:25:03'),
(15, 28, 'Print', 'BUSINESS_PERMIT', 19, 'Patatasan', 'Printed business permit: Patatasan (Permit No: 20250521-032209-10)', '2025-05-22 06:47:59'),
(16, 28, 'Print', 'BLOTTER2', 5, 'Malou', 'Printed blotter certificate for complainant: Malou', '2025-05-22 06:51:58'),
(17, 28, 'Print', 'BLOTTER2', 7, 'Justine Pagdanganan', 'Printed blotter certificate for complainant: Justine Pagdanganan', '2025-05-22 06:53:13'),
(18, 28, 'Print', 'BLOTTER2', 6, 'PATRICK', 'Printed blotter certificate for complainant: PATRICK', '2025-05-22 06:54:12'),
(19, 28, 'Print', 'RESIDENT', 37, 'Justine Santos', 'Printed Barangay Clearance for: Justine Santos', '2025-05-22 06:58:30'),
(20, 28, 'Print', 'RESIDENT', 37, 'Justine Santos', 'Printed Barangay Clearance for: Justine Santos', '2025-05-22 06:59:46'),
(21, 28, 'Print', 'RESIDENT', 37, 'Justine Santos', 'Printed Barangay Indigency for: Justine Santos', '2025-05-22 07:00:18'),
(22, 31, 'Delete', 'BUSINESS_PERMIT', 18, 'Joan Stores', 'Deleted business permit: Joan Stores (ID: 18)', '2025-05-22 08:39:11'),
(23, 31, 'Update', 'BUSINESS_PERMIT', 19, 'Patatasan', 'Updated business permit: Patatasan (ID: 19)', '2025-05-22 08:39:33'),
(24, 28, 'Add', 'BLOTTER2', 11, 'Atina', 'Added new blotter record for complainant: Atina (Blotter ID: 11)', '2025-06-07 08:56:26'),
(25, 28, 'Add', 'BLOTTER2', 12, 'Jeffrey', 'Added new blotter record for complainant: Jeffrey (Blotter ID: 12)', '2025-06-07 08:58:12'),
(26, 28, 'Print', 'RESIDENT', 48, 'Jeber Santos', 'Printed Barangay Clearance for: Jeber Santos', '2025-06-07 09:37:24'),
(27, 28, 'Print', 'BLOTTER2', 12, 'Jeffrey', 'Printed blotter certificate for complainant: Jeffrey', '2025-06-07 09:38:59'),
(28, 28, 'Print', 'RESIDENT', 47, 'Kristal Santos', 'Printed Barangay Indigency for: Kristal Santos', '2025-06-07 09:39:31'),
(29, 28, 'Print', 'BLOTTER2', 12, 'Jeffrey', 'Printed blotter certificate for complainant: Jeffrey', '2025-06-07 09:39:59'),
(30, 28, 'Print', 'RESIDENT', 47, 'Kristal Santos', 'Printed Barangay Indigency for: Kristal Santos', '2025-06-07 09:41:01'),
(31, 28, 'Print', 'RESIDENT', 42, 'Joan  Santos', 'Printed Barangay Clearance for: Joan  Santos', '2025-06-07 09:44:04'),
(32, 28, 'Print', 'BUSINESS_PERMIT', 20, 'Patatasan', 'Printed business permit: Patatasan (Permit No: 20250524-233112-87)', '2025-06-07 09:44:26'),
(33, 28, 'Print', 'RESIDENT', 48, 'Jeber Santos', 'Printed Barangay Clearance for: Jeber Santos', '2025-06-07 09:45:37');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_info`
--

CREATE TABLE `barangay_info` (
  `ID` int(11) NOT NULL,
  `PROVINCE_NAME` varchar(255) NOT NULL,
  `TOWN_NAME` varchar(255) NOT NULL,
  `BARANGAY_NAME` varchar(255) NOT NULL,
  `CONTACT_NUMBER` varchar(255) NOT NULL,
  `DASHBOARD_TEXT` varchar(255) NOT NULL,
  `MUNICIPAL_LOGO` varchar(255) NOT NULL,
  `BARANGAY_LOGO` varchar(255) NOT NULL,
  `DASHBOARD_IMAGE` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_info`
--

INSERT INTO `barangay_info` (`ID`, `PROVINCE_NAME`, `TOWN_NAME`, `BARANGAY_NAME`, `CONTACT_NUMBER`, `DASHBOARD_TEXT`, `MUNICIPAL_LOGO`, `BARANGAY_LOGO`, `DASHBOARD_IMAGE`) VALUES
(11, 'Bulacan', 'Calumpit', 'San marcos', '09475747475', 'Best Brangayas', 'Manila_Seal_1950.svg.png', 'Bagahabag.png', 'bluebg.jpg'),
(15, 'Bulacan', 'Calumpit', 'San marcos', '09475747475', 'Best Brangayas', 'municipal_6815c81079633_', 'barangay_6815c8107963a_', 'dashboard_6815c8107963c_'),
(16, 'Bulacan', 'Calumpit', 'San marcos', '09475747475', 'Best Brangayas', 'municipal_6815c8940fb1b_', 'barangay_6815c8940fb23_', 'dashboard_6815c8940fb25_'),
(17, 'Bulacan', 'Calumpit', 'San marcos', '09475747475', 'Best Brangayas', 'municipal_6815c8a1cd6ba_calumpit.png', 'barangay_6815c8a1cd6c7_', 'dashboard_6815c8a1cd6ca_'),
(18, 'Bulacan', 'Calumpit', 'San marcos', '09475747475', 'Best Brangayas', 'municipal_68160813c127c_san_marcos.png', 'barangay_68160813c128c_', 'dashboard_68160813c1290_'),
(19, 'Bulacan', 'Calumpit', 'San marcos', '09475747475', 'Best Brangayas', 'municipal_681ff4c13f1a5_Logo_png.png', 'barangay_681ff4c13f1af_', 'dashboard_681ff4c13f1b2_');

-- --------------------------------------------------------

--
-- Table structure for table `barangay_position`
--

CREATE TABLE `barangay_position` (
  `ID` int(11) NOT NULL,
  `POSITION` varchar(255) NOT NULL,
  `ORDER_NUM` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangay_position`
--

INSERT INTO `barangay_position` (`ID`, `POSITION`, `ORDER_NUM`) VALUES
(5, 'Barangay Captain (Punong Barangay)', '1'),
(6, 'Barangay Kagawad (Councilor)', '2'),
(7, 'Sangguniang Kabataan', '3'),
(8, 'Barangay Secretary', '4'),
(9, 'Barangay Treasurer', '5'),
(10, 'Barangay Tanod (Peace and Order personnel)', '6'),
(11, 'Barangay Health Worker (BHW)', '7'),
(12, 'Day Care Worker', '8'),
(13, 'Barangay Nutrition Scholar (BNS)', '9'),
(14, 'Barangay Volunteer Worker', '10');

-- --------------------------------------------------------

--
-- Table structure for table `blotter`
--
-- Error reading structure for table barangay_system.blotter: #1932 - Table 'barangay_system.blotter' doesn't exist in engine
-- Error reading data for table barangay_system.blotter: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'FROM `barangay_system`.`blotter`' at line 1

-- --------------------------------------------------------

--
-- Table structure for table `blotter2`
--

CREATE TABLE `blotter2` (
  `ID` int(11) NOT NULL,
  `INCIDENT_TYPE` varchar(100) DEFAULT NULL,
  `COMPLAINANT_NAME` varchar(150) DEFAULT NULL,
  `RESPONDENT` varchar(150) DEFAULT NULL,
  `VICTIM` varchar(150) DEFAULT NULL,
  `INCIDENT_DATE` varchar(255) DEFAULT NULL,
  `INCIDENT_TIME` varchar(255) DEFAULT NULL,
  `INCIDENT_LOCATION` varchar(255) DEFAULT NULL,
  `STATUS` varchar(50) DEFAULT NULL,
  `INCIDENT_DESCRIPTION` varchar(255) DEFAULT NULL,
  `DATE_SCHEDULE` varchar(255) DEFAULT NULL,
  `TIME_SCHEDULE` varchar(255) DEFAULT NULL,
  `DATE` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blotter2`
--

INSERT INTO `blotter2` (`ID`, `INCIDENT_TYPE`, `COMPLAINANT_NAME`, `RESPONDENT`, `VICTIM`, `INCIDENT_DATE`, `INCIDENT_TIME`, `INCIDENT_LOCATION`, `STATUS`, `INCIDENT_DESCRIPTION`, `DATE_SCHEDULE`, `TIME_SCHEDULE`, `DATE`) VALUES
(4, 'Amicable', 'Malou', 'Secretary', 'Mack', '2025-05-14', '03:21', 'BNBN', 'Active', 'SDF', '', '', '2025-06-14'),
(5, 'Incident', 'Malou', 'Secretary', 'Mack', '2025-05-13', '09:42', 'BNBN', 'Settled', 'Nagnakaw', NULL, NULL, '2025-04-16'),
(6, 'Dispute', 'PATRICK', 'Secretary', 'Mack', '2025-05-29', '08:12', 'Rueda Bulacan', 'Scheduled', 'Nangutangs', '2025-05-28', '08:11', '2025-05-20'),
(7, 'Amicable', 'Justine Pagdanganan', 'Secretary', 'Justine', '2025-05-14', '09:08', 'Rueda Bulacan', 'Scheduled', 'Ngnakaw', '2025-05-24', '09:07', '2025-05-21'),
(8, 'Amicable', 'Malou', 'Secretary', 'Mack', '2025-06-18', '09:05', 'Rueda Bulacan', 'Settled', 'Lasing', '', '', '2025-06-04'),
(9, 'Amicable', 'Rey', 'Secretary', 'Mack', '2025-06-25', '09:07', 'San juan', 'Active', 'Nanapak', '', '', '2025-04-16'),
(10, 'Amicable', 'Malou', 'Secretary', 'Mack', '2025-06-25', '05:28', 'Rueda Bulacan', 'Scheduled', 'Nanapak', '2025-06-07', '05:27', '2025-06-13'),
(11, 'Incident', 'Atina', 'Secretary', 'Mack', '2025-06-18', '08:59', 'Rueda Bulacan', 'Scheduled', 'Nanakit', '2025-06-14', '08:58', '2025-06-07'),
(12, 'Amicable', 'Jeffrey', 'Secretary', 'Mack', '2025-06-11', '08:59', 'Rueda Bulacan', 'Scheduled', 'nanakit', '2025-06-20', '08:02', '2025-04-19');

-- --------------------------------------------------------

--
-- Table structure for table `business_permit`
--

CREATE TABLE `business_permit` (
  `ID` int(11) NOT NULL,
  `PERMIT_NUM` varchar(255) NOT NULL,
  `BUSINESS_OWNER` varchar(255) NOT NULL,
  `BUSINESS_NAME` varchar(255) NOT NULL,
  `BUSINESS_TYPE` varchar(255) NOT NULL,
  `BUSINESS_ADDRESS` varchar(255) NOT NULL,
  `STATUS` varchar(255) NOT NULL,
  `DATE` date NOT NULL,
  `VALID_UNTIL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_permit`
--

INSERT INTO `business_permit` (`ID`, `PERMIT_NUM`, `BUSINESS_OWNER`, `BUSINESS_NAME`, `BUSINESS_TYPE`, `BUSINESS_ADDRESS`, `STATUS`, `DATE`, `VALID_UNTIL`) VALUES
(19, '20250521-032209-10', 'Justine Santos', 'Patatasan', 'SariSariss', 'San marcos calumpit', 'Approved', '2025-05-15', '2025-05-08'),
(20, '20250524-233112-87', 'Justine Santos', 'Patatasan', 'sdf', 'sdf', 'Pending', '2025-05-14', '2025-05-13');

-- --------------------------------------------------------

--
-- Table structure for table `chairmanship`
--

CREATE TABLE `chairmanship` (
  `ID` int(11) NOT NULL,
  `TITLE` varchar(255) NOT NULL,
  `ORDER_NUM` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chairmanship`
--

INSERT INTO `chairmanship` (`ID`, `TITLE`, `ORDER_NUM`) VALUES
(21, 'Chairman on Peace and Order', '1'),
(22, 'Chairman on Health and Sanitation', '2'),
(23, 'Chairman on Education', '3'),
(24, 'Chairman on Environment', '4'),
(25, 'Chairman on Infrastructure', '5'),
(26, 'Chairman on Livelihood', '6'),
(27, 'Chairman on Youth and Sports', '7'),
(28, 'Chairman on Solid Waste Management', '8'),
(29, 'Chairman on Disaster Risk Reduction and Management (DRRM)', '9'),
(30, 'Chairman on Agriculture and Fisheries', '10');

-- --------------------------------------------------------

--
-- Table structure for table `official`
--

CREATE TABLE `official` (
  `ID` int(11) NOT NULL,
  `FULL_NAME` varchar(255) NOT NULL,
  `CHAIRMANSHIP` varchar(255) NOT NULL,
  `POSITION` varchar(255) NOT NULL,
  `TERM_START` varchar(255) NOT NULL,
  `TERM_END` varchar(255) NOT NULL,
  `PROFILE` varchar(255) NOT NULL,
  `STATUS` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `official`
--

INSERT INTO `official` (`ID`, `FULL_NAME`, `CHAIRMANSHIP`, `POSITION`, `TERM_START`, `TERM_END`, `PROFILE`, `STATUS`) VALUES
(15, 'Rodrigo Duterte', 'Chairman on Peace and Order', 'Barangay captain (punong barangay)', '2025-05-17', '2025-05-15', '68365b37aaf02.png', 'Active'),
(19, 'Bong Go', 'Chairman on Environment', 'Barangay kagawad (councilor)', '2025-05-08', '2025-06-06', '6836510525be5.jpg', 'Active'),
(21, 'Bato Dela Rosa', 'Chairman on Solid Waste Management', 'Barangay kagawad (councilor)', '2025-05-28', '2025-05-31', '68365be910ed1.jpeg', 'Active'),
(22, 'Robin Padilla', 'Chairman on Education', 'Barangay kagawad (councilor)', '2025-05-23', '2025-05-31', '68365c9092183.jpg', 'Active'),
(23, 'Many Pac', 'Chairman on Peace and Order', 'Barangay kagawad (councilor)', '2025-05-14', '2025-06-26', '68365d2073361.png', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `otp`
--

CREATE TABLE `otp` (
  `OTPID` int(10) NOT NULL,
  `STAFFID` int(50) NOT NULL,
  `OTP` varchar(255) NOT NULL,
  `TIME` timestamp NOT NULL DEFAULT current_timestamp(),
  `EMAIL` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp`
--

INSERT INTO `otp` (`OTPID`, `STAFFID`, `OTP`, `TIME`, `EMAIL`) VALUES
(15, 0, '364739', '2025-03-08 14:31:39', ''),
(16, 0, '884382', '2025-03-08 14:31:49', '');

-- --------------------------------------------------------

--
-- Table structure for table `precinct`
--

CREATE TABLE `precinct` (
  `ID` int(11) NOT NULL,
  `RESIDENT_NAME` varchar(255) NOT NULL,
  `VOTER_ID` varchar(255) NOT NULL,
  `PRECINCT_NUMBER` varchar(255) NOT NULL,
  `STATUS` varchar(255) NOT NULL,
  `DATE_REGISTERED` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `precinct`
--

INSERT INTO `precinct` (`ID`, `RESIDENT_NAME`, `VOTER_ID`, `PRECINCT_NUMBER`, `STATUS`, `DATE_REGISTERED`) VALUES
(2, 'nene santos', '12223', '0993', 'Active', '2025-05-21'),
(3, 'Joan Cruz', 'asdasd', 'asd', 'Inactive', '2025-06-06');

-- --------------------------------------------------------

--
-- Table structure for table `purok`
--

CREATE TABLE `purok` (
  `ID` int(11) NOT NULL,
  `PUROK` varchar(255) NOT NULL,
  `DETAILS` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purok`
--

INSERT INTO `purok` (`ID`, `PUROK`, `DETAILS`) VALUES
(5, 'Purok 1', 'Residential Compound A'),
(6, 'Purok 2', 'Market Side / Commercial Zone'),
(7, 'Purok 3	', 'Riverside Community'),
(8, 'Purok 4	', 'School &amp; Civic Area	'),
(9, 'Purok 5	', 'Urban Poor Housing Block	'),
(10, 'Purok 6	', 'Middle-Class Residential Row	'),
(11, 'Purok 7	', 'Near Health Center &amp; Daycare	'),
(12, 'Purok 8	', 'Bridge Access / Roadside Area	'),
(13, 'Purok 9', 'Public Housing Apartments'),
(14, 'Purok 10', 'Coastal Edge / Fish Port Zone');

-- --------------------------------------------------------

--
-- Table structure for table `resident`
--

CREATE TABLE `resident` (
  `RESIDENT_ID` int(11) NOT NULL,
  `OFFICIAL_ID` int(11) NOT NULL,
  `FIRST_NAME` varchar(255) NOT NULL,
  `MIDDLE_NAME` varchar(255) NOT NULL,
  `LAST_NAME` varchar(255) NOT NULL,
  `ALIAS` varchar(255) NOT NULL,
  `PLACE_OF_BIRTH` varchar(255) NOT NULL,
  `BIRTH_DATE` varchar(255) NOT NULL,
  `AGE` int(255) NOT NULL,
  `CIVIL_STATUS` varchar(255) NOT NULL,
  `GENDER` varchar(255) NOT NULL,
  `PUROK` varchar(255) NOT NULL,
  `VOTER_STATUS` varchar(255) NOT NULL,
  `PHONE` varchar(255) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `PROFILE` varchar(255) NOT NULL,
  `DATE` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resident`
--

INSERT INTO `resident` (`RESIDENT_ID`, `OFFICIAL_ID`, `FIRST_NAME`, `MIDDLE_NAME`, `LAST_NAME`, `ALIAS`, `PLACE_OF_BIRTH`, `BIRTH_DATE`, `AGE`, `CIVIL_STATUS`, `GENDER`, `PUROK`, `VOTER_STATUS`, `PHONE`, `EMAIL`, `PROFILE`, `DATE`) VALUES
(37, 28, 'Justine', 'Cruz', 'Santos', 'Justine', 'Malolos', '2025-05-14', 33, 'Single', 'Female', 'Plaridel', 'Yes', '908345343434', 'just@gmail.com', '1711813887723.jpg', '2025-04-04'),
(38, 9, 'Elsa', 'Cruz', 'Manaloto', 'Elsa', 'Manila', '2025-05-13', 33, 'Single', 'Male', 'Purok 6', 'Yes', '09487754555', 'el@gmail.com', 'placeholder.jpg', '2025-05-30'),
(39, 9, 'Jasfer', 'Santos', 'Cruz', 'Jes', 'Manila', '2025-05-09', 23, 'Single', 'Male', 'Purok 7', 'Yes', '094578745454', 'jes@gmail.com', 'uber.jpg', '2025-02-12'),
(40, 9, 'Prince', 'Santos', 'Pagda', 'Prince', 'Makati', '2025-05-16', 34, 'Single', 'Male', 'Purok 6', 'Yes', '09378487877', 'prince@gmail.com', 'placeholder.jpg', '2025-05-30'),
(42, 9, 'Joan ', 'Cruz', 'Santos', 'Joan', 'Malolos', '2025-05-09', 23, 'Single', 'Female', 'Purok 6', 'Yes', '09687678678', 'joan@gmail.com', '71a8yqiJVXL._AC_UY1000_.jpg', '2025-05-30'),
(43, 9, 'Demy', 'Santos', 'Macapagal', 'Dem', 'Manil', '2025-05-21', 33, 'Single', 'Male', 'Purok 6', 'Yes', '9083453435', 'dem@gmail.com', 'placeholder.jpg', '2025-02-15'),
(44, 9, 'Jm', 'Santos', 'Pagda', 'Jm', 'Malolos', '2025-05-15', 23, 'Single', 'Male', 'Purok 6', 'Yes', '9083453445', 'jm@gmail.com', 'placeholder.png', '2025-05-30'),
(45, 9, 'Janel', 'Cruz', 'Santps', 'Janel', 'Manila', '2025-06-13', 23, 'Single', 'Male', 'Purok 6', 'Yes', '094578745454', 'janel@gmail.com', 'placeholder.png', '2025-06-02'),
(46, 9, 'Nora', 'Cruz', 'Santos', 'Nora', 'Manila', '2025-06-12', 45, 'Single', 'Male', 'Purok 6', 'Yes', '90834534444', 'nora@gmail.com', 'placeholder.jpg', '2025-02-20'),
(47, 9, 'Kristal', 'Cruz', 'Santos', 'Kris', 'Manila', '2025-06-20', 45, 'Single', 'Male', 'Purok 6', 'Yes', '908345340977', 'pat@gmail.com', 'placeholder.png', '2025-05-09'),
(48, 9, 'Jeber', 'Dingcol', 'Santos', 'Jeber', 'manila', '2025-06-20', 45, 'Single', 'Male', 'Purok 6', 'Yes', '90834534988', 'jeb@gmail.com', 'uber.jpg', '2025-06-05');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `STAFFID` int(10) NOT NULL,
  `FIRST_NAME` varchar(255) NOT NULL,
  `LAST_NAME` varchar(255) NOT NULL,
  `BIRTH_DAY` varchar(255) NOT NULL,
  `GENDER` varchar(255) NOT NULL,
  `EMAIL` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `USERTYPE` varchar(255) NOT NULL,
  `STATUS` varchar(255) NOT NULL,
  `OFFICIAL_POSITION` varchar(255) NOT NULL,
  `OTP` varchar(255) NOT NULL,
  `PROFILE` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`STAFFID`, `FIRST_NAME`, `LAST_NAME`, `BIRTH_DAY`, `GENDER`, `EMAIL`, `PASSWORD`, `USERTYPE`, `STATUS`, `OFFICIAL_POSITION`, `OTP`, `PROFILE`) VALUES
(9, 'Micka', 'Janna Jimenea Limasas', '2025-03-04', 'Female', 'anonymousehappy410@gmail.com', 'a43c27c2babefd68df8a694900f30a1c', 'Admin', 'Active', '', '', 'mica.jpg'),
(28, 'Pedro', 'Hill', '2025-05-08', 'Male', 'cuetopatrick91@gmail.com', '36fdba5968850579c0a89444f4ca4772', 'Staff', 'Active', '', '', '../images/placeholder.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `barangay_info`
--
ALTER TABLE `barangay_info`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `barangay_position`
--
ALTER TABLE `barangay_position`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `blotter2`
--
ALTER TABLE `blotter2`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `business_permit`
--
ALTER TABLE `business_permit`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `chairmanship`
--
ALTER TABLE `chairmanship`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `official`
--
ALTER TABLE `official`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `otp`
--
ALTER TABLE `otp`
  ADD PRIMARY KEY (`OTPID`);

--
-- Indexes for table `precinct`
--
ALTER TABLE `precinct`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `purok`
--
ALTER TABLE `purok`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `resident`
--
ALTER TABLE `resident`
  ADD PRIMARY KEY (`RESIDENT_ID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`STAFFID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `barangay_info`
--
ALTER TABLE `barangay_info`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `barangay_position`
--
ALTER TABLE `barangay_position`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `blotter2`
--
ALTER TABLE `blotter2`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `business_permit`
--
ALTER TABLE `business_permit`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `chairmanship`
--
ALTER TABLE `chairmanship`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `official`
--
ALTER TABLE `official`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `otp`
--
ALTER TABLE `otp`
  MODIFY `OTPID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `precinct`
--
ALTER TABLE `precinct`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `purok`
--
ALTER TABLE `purok`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `resident`
--
ALTER TABLE `resident`
  MODIFY `RESIDENT_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `STAFFID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
