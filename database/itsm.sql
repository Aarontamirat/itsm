-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 10:55 AM
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
-- Database: `itsm`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `phone`, `email`, `location`, `created_at`, `is_active`) VALUES
(1, 'Head Office', '+251114703407', 'ho@lucyinsuranceet.com', 'In front of Capital Hotel, 22, Addis Ababa.', '2025-06-05 09:59:13', 1),
(2, 'Kazanchis Branch', '+251116611701', 'kazanchis@lucyinsuranceet.com', 'Kazanchis', '2025-06-05 10:09:01', 1),
(3, 'Piassa Branch', '+251111267551', 'piassa@lucyinsuranceet.com', 'Piassa', '2025-06-05 10:09:34', 1),
(4, 'Stadium Branch', '+251115581724', 'stadium@lucyinsuranceet.com', 'Stadium', '2025-06-05 10:09:50', 1),
(5, 'Bole Medhanialem Branch', '+251116393847', 'bolem@lucyinsuranceet.com', 'Bole Medhanialem', '2025-06-05 10:10:16', 1),
(6, 'Lideta Branch', '+251115578800', 'lideta@lucyinsuranceet.com', 'Lideta', '2025-06-05 10:10:33', 1),
(7, 'Debre Birhan Branch', '+251116375657', 'debrebirhan@lucyinsuranceet.com', 'Debre Birhan', '2025-06-05 10:11:00', 1),
(8, 'Addey Abeba Branch', '+251116902333', 'adeyabeba@lucyinsuranceet.com', 'Addey Abeba', '2025-06-05 10:11:31', 1),
(9, 'Betel Branch', '+251113697052', 'betel@lucyinsuranceet.com', 'Betel', '2025-06-05 10:11:48', 1),
(10, 'Addisu Gebeya Branch', '+251111547206', 'adg@lucyinsuranceet.com', 'Addisu Gebeya', '2025-06-05 10:12:27', 1),
(11, 'BahirDar Branch', '+251583205173', 'bahirdar@lucyinsuranceet.com', 'BahirDar', '2025-06-05 10:12:51', 1),
(12, 'Diredawa Branch', '+251254114116', 'diredawa@lucyinsuranceet.com', 'Diredawa', '2025-06-05 10:13:14', 1),
(13, 'Adama Branch', '+251221111853', 'adama@lucyinsuranceet.com', 'Adama', '2025-06-05 10:13:28', 1),
(14, 'Lemikura Branch', '+251116390645', 'lemikura@lucyinsuranceet.com', 'Lemikura', '2025-06-05 10:13:45', 1),
(15, 'Arat Kilo Branch', '+251111265510', '4kilo@lucyinsuranceet.com', 'Arat Kilo', '2025-06-05 10:14:06', 1),
(16, 'Yoseph Branch', '+251114709055', 'yoseph@lucyinsuranceet.com', 'Yoseph', '2025-06-05 10:14:24', 1),
(17, 'Figa Branch', '+251116660888', 'figa@lucyinsuranceet.com', 'Figa', '2025-06-05 10:14:39', 1),
(18, 'Ayertena Branch', '+251113693865', 'ayertena@lucyinsuranceet.com', 'Ayertena', '2025-06-05 10:14:54', 1),
(19, 'Goro Branch', '+251116689411', 'goro@lucyinsuranceet.com', 'Goro', '2025-06-05 10:15:01', 1),
(20, 'Gulele Branch', '+251112737778', 'gulele@lucyinsuranceet.com', 'Gulele', '2025-06-05 10:15:17', 1),
(21, 'Lamberet Branch', '+251116733144', 'lamberet@lucyinsuranceet.com', 'Lamberet', '2025-06-05 10:15:38', 1),
(22, 'Yerer Branch', '+251116676150', 'yerer@lucyinsuranceet.com', 'Yerer', '2025-06-05 10:15:51', 1),
(23, 'Habte Giorgis Branch', '+251111706561', 'hg@lucyinsuranceet.com', 'Habte Giorgis', '2025-06-05 10:16:07', 1),
(24, 'CMC Branch', '+251118134221', 'cmc@lucyinsuranceet.com', 'CMC', '2025-06-05 10:16:17', 1),
(25, 'Lebu Branch', '+251114625703', 'lebu@lucyinsuranceet.com', 'Lebu', '2025-06-05 10:16:24', 1),
(26, 'Kality Branch', '+251114717515', 'kality@lucyinsuranceet.com', 'Kality', '2025-06-05 10:16:33', 1),
(27, 'Kera Branch', '+251114702228', 'kera@lucyinsuranceet.com', 'Kera', '2025-06-05 10:16:42', 1),
(28, 'Megenagna Branch', '+251116674393', 'megenagna@lucyinsuranceet.com', 'Megenagna', '2025-06-05 10:17:06', 1),
(29, 'Merkato Branch', '+251112755747', 'merkato@lucyinsuranceet.com', 'Merkato', '2025-06-05 10:17:17', 1),
(30, 'Bole Branch', '+251115573475', 'bole@lucyinsuranceet.com', 'Bole', '2025-06-05 10:18:08', 1),
(31, 'Wolaita Branch', '+251461808888', 'wolaita@lucyinsuranceet.com', 'Wolaita', '2025-06-05 10:18:27', 1),
(32, 'Bulbula Branch', '+251114704213', 'bulbula@lucyinsuranceet.com', 'Bulbula', '2025-06-05 10:18:44', 1),
(33, 'Beklobet Branch', '+251114703977', 'beklobet@lucyinsuranceet.com', 'Beklobet', '2025-06-05 10:18:58', 1),
(34, 'Hawassa Branch', '+251462123571', 'hawassa@lucyinsuranceet.com', 'Hawassa', '2025-06-05 10:19:09', 1),
(35, 'Mekelle Branch', '+251342416117', 'mekelle@lucyinsuranceet.com', 'Mekelle', '2025-06-05 10:19:24', 1),
(36, 'Jimma Branch', '+251472111156', 'jimma@lucyinsuranceet.com', 'Jimma', '2025-06-05 10:19:37', 1),
(37, 'Meskel Flower Branch', '+251114704213', 'meskelflower@lucyinsuranceet.com', 'Meskel Flower', '2025-06-05 10:19:54', 1),
(38, 'Alemgena Branch', '+251114704213', 'alemgena@lucyinsuranceet.com', 'Alemgena', '2025-06-05 10:20:05', 1),
(39, 'Sebategna Branch', '+251114704213', 'sebategna@lucyinsuranceet.com', 'Sebategna', '2025-06-05 10:20:21', 1),
(40, 'Bulgaria Branch', '+251114704213', 'bulgaria@lucyinsuranceet.com', 'Bulgaria', '2025-06-05 10:20:45', 1),
(41, 'Mizan Teferi Branch', '+251114704213', 'mizan@lucyinsuranceet.com', 'Mizan Teferi', '2025-06-05 10:22:33', 1),
(42, 'Main Branch', '+251114703407', 'ho@lucyinsuranceet.com', 'In front of Capital Hotel, 22, Addis Ababa.', '2025-06-05 10:23:45', 1),
(43, 'CEO', '+251114704213', 'CEO@lucyinsuranceet.com', '6th Floor', '2025-06-05 10:24:20', 1),
(44, 'Legal', '+251114704213', 'legal@lucyinsuranceet.com', '5th', '2025-06-05 10:24:45', 1),
(45, 'Finance', '+251114704213', 'finance@lucyinsuranceet.com', '5th', '2025-06-05 10:24:54', 1),
(46, 'Marketing', '+251114704213', 'marketing@lucyinsuranceet.com', '5th', '2025-06-05 10:25:06', 1),
(47, 'Risk', '+251114704213', 'risk@lucyinsuranceet.com', '5th', '2025-06-05 10:25:12', 1),
(48, 'Reinsurance', '+251114704213', 'reinsurance@lucyinsuranceet.com', '5th', '2025-06-05 10:25:21', 1),
(49, 'Claims & Recovery', '+251114704213', 'claims@lucyinsuranceet.com', '4th', '2025-06-05 10:25:28', 1),
(50, 'Audit', '+251114704213', 'audit@lucyinsuranceet.com', '4th', '2025-06-05 10:26:05', 1),
(51, 'Engineering', '251114704213', 'engineering@lucyinsuranceet.com', '3rd', '2025-06-05 10:26:16', 1),
(52, 'Operation', '251114704213', 'operation@lucyinsuranceet.com', '3rd', '2025-06-05 10:26:24', 1),
(53, 'HR & Logistics', '251114704213', 'hrlg@lucyinsuranceet.com', '3rd & 1st', '2025-06-05 10:26:49', 1),
(55, 'Ethics', '251114704213', 'ethics@lucyinsuranceet.com', '5th', '2025-06-05 10:28:10', 1);

-- --------------------------------------------------------

--
-- Table structure for table `files`
--

CREATE TABLE `files` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `filepath` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `files`
--

INSERT INTO `files` (`id`, `incident_id`, `filename`, `filepath`, `uploaded_at`) VALUES
(2, 0, NULL, '../uploads/default_avatar.png', '2025-05-27 14:18:23');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','assigned','not fixed','fixed','rejected','support','reopened','fixed_confirmed') DEFAULT 'pending',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `assigned_to` int(11) DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `branch_id` int(11) DEFAULT NULL,
  `assigned_date` datetime DEFAULT NULL,
  `fixed_date` datetime DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `saved_amount` decimal(10,2) DEFAULT NULL,
  `remark` text DEFAULT NULL,
  `rejected_by` int(11) DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `title`, `description`, `status`, `priority`, `assigned_to`, `submitted_by`, `created_at`, `branch_id`, `assigned_date`, `fixed_date`, `category_id`, `saved_amount`, `remark`, `rejected_by`, `rejection_reason`, `rejected_at`) VALUES
(1, 'Computer', 'computer does not bootup', 'fixed_confirmed', 'high', 4, 56, '2025-06-17 04:57:55', 48, '2025-06-17 10:59:23', '2025-06-17 11:03:18', 1, 300.00, NULL, NULL, NULL, NULL),
(2, 'lock release and export file', 'core system password lock and export file from legacy to live system', 'fixed_confirmed', 'high', 4, 46, '2025-06-17 07:45:58', 42, '2025-06-17 13:46:31', '2025-06-17 13:50:36', 9, 0.00, NULL, NULL, NULL, NULL),
(3, 'core system lock user', 'core system lock user  to claim département', 'fixed_confirmed', 'low', 3, 57, '2025-06-17 07:51:27', 49, '2025-06-17 13:55:03', '2025-06-17 13:56:56', 9, 200.00, NULL, NULL, NULL, NULL),
(4, 'lock release', 'core system password lock', 'fixed_confirmed', 'high', 4, 52, '2025-06-17 08:10:16', 44, '2025-06-17 14:10:39', '2025-06-17 14:11:05', 9, 0.00, NULL, NULL, NULL, NULL),
(5, 'lock release', 'core system password locked', 'fixed_confirmed', 'high', 4, 57, '2025-06-17 08:54:59', 49, '2025-06-17 14:56:08', '2025-06-17 14:56:32', 9, 0.00, NULL, NULL, NULL, NULL),
(6, 'System problems', 'Tp Passenger Increase and Decrease', 'pending', 'low', 62, 59, '2025-06-18 04:10:28', 52, '2025-06-18 10:16:35', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(7, 'Write off problem', 'How to Write off canceled policy with new policy', 'pending', 'medium', 62, 59, '2025-06-18 04:11:15', 52, '2025-06-18 10:16:32', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(8, 'report print out problems', 'Most report included proposals canceled before the premium is collected, this should be corrected.', 'pending', 'medium', 62, 59, '2025-06-18 04:13:08', 52, '2025-06-18 10:16:29', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(9, 'approval stage error', 'on approval error message is showing Debit and Credit does not tally', 'pending', 'medium', 62, 59, '2025-06-18 04:14:05', 52, '2025-06-18 10:16:27', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(10, 'summary reports issue', 'before approval manager want to see summary of the policy, need to include in the system', 'pending', 'medium', 62, 59, '2025-06-18 04:15:13', 52, '2025-06-18 10:16:19', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(11, 'Total loss advise report', 'searching by claim number is not working', 'fixed_confirmed', 'medium', 62, 57, '2025-06-18 04:23:36', 49, '2025-06-18 10:28:09', '2025-06-18 11:24:27', 8, 0.00, NULL, NULL, NULL, NULL),
(12, 'want  two network cable.', 'want two network cable for CEO secretary', 'fixed_confirmed', 'medium', 2, 51, '2025-06-18 04:26:12', 43, '2025-06-18 11:37:57', '2025-06-18 14:51:36', 2, 500.00, NULL, NULL, NULL, NULL),
(13, 'Withholding tax', 'General Jornal customer may bring two invoice with different vendor and with different withholding currently on the system there is no option to pay to the customer with such scenarios', 'pending', 'medium', 62, 53, '2025-06-18 04:26:29', 45, '2025-06-18 10:28:05', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(14, 'Vat Cancellation', 'if customer returned cheque within the month cancellation including the vat should be possible. but if the customer returned the cheque next month vat cancellation should not be possible', 'pending', 'low', 62, 53, '2025-06-18 04:27:46', 45, '2025-06-18 10:28:02', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(16, 'Printer share issue.', 'add printer driver to computer, and share this printer from CEO secretary cashier.', 'fixed_confirmed', 'medium', 3, 51, '2025-06-18 04:33:45', 43, '2025-06-18 10:34:26', '2025-06-18 10:36:49', 5, 300.00, NULL, NULL, NULL, NULL),
(17, 'UnderWriting Excel File Generation for Risk Details Uploads', 'During UnderWriting Excel File Generation for Risk Details Uploads Generate excel file, Save in Local and Validate excel file Button not working.', 'fixed_confirmed', 'medium', 2, 29, '2025-06-18 09:31:08', 25, '2025-06-18 15:39:46', '2025-06-18 16:00:23', 8, 600.00, NULL, NULL, NULL, NULL),
(18, 'Property Managment system', 'ATPM V.1 Database Property Managment system compiler loading problem.', 'fixed_confirmed', 'high', 2, 49, '2025-06-18 09:58:56', 53, '2025-06-18 15:59:33', '2025-06-18 16:01:47', 8, 0.00, NULL, NULL, NULL, NULL),
(19, 'export file', 'export file from legacy to new core system', 'fixed_confirmed', 'medium', 4, 59, '2025-06-18 10:18:04', 52, '2025-06-18 16:19:04', '2025-06-18 16:19:56', 10, 0.00, NULL, NULL, NULL, NULL),
(20, 'user transfer', 'user transfer based on letter no lucy/hrlo/1241/17', 'fixed_confirmed', 'medium', 4, 49, '2025-06-18 10:26:49', 53, '2025-06-18 16:27:13', '2025-06-18 16:27:38', 11, 0.00, NULL, NULL, NULL, NULL),
(21, 'lock release', 'core system password locked', 'fixed_confirmed', 'high', 4, 9, '2025-06-18 10:54:43', 5, '2025-06-18 16:55:01', '2025-06-18 16:55:18', 9, 0.00, NULL, NULL, NULL, NULL),
(22, 'Service Charge', 'when creating insurance transfer service charge the charge amount showing wrong amount', 'pending', 'low', 62, 59, '2025-06-19 02:54:57', 52, '2025-06-19 09:01:31', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(23, 'excess print out', 'after changing the excess amount on the printout, it is not coming correctly', 'pending', 'medium', 62, 59, '2025-06-19 02:58:12', 52, '2025-06-19 09:01:27', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(24, 'yellow card cancellation', 'there is no option on the system to cancel the yellow card', 'pending', 'medium', 62, 59, '2025-06-19 02:59:31', 52, '2025-06-19 09:01:24', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(25, 'marine inland transit', 'Multiple ATP Cancellation by insured and by company is not avaliable', 'fixed_confirmed', 'low', 62, 59, '2025-06-19 03:00:28', 52, '2025-06-19 09:01:00', '2025-06-19 10:37:12', 8, 0.00, NULL, NULL, NULL, NULL),
(26, 'Printer share', 'Printer share issue for one computer.', 'fixed_confirmed', 'low', 3, 46, '2025-06-19 04:51:09', 42, '2025-06-19 10:51:50', '2025-06-19 10:52:16', 5, 100.00, NULL, NULL, NULL, NULL),
(27, 'printer issue', 'printer failed to print', 'fixed_confirmed', 'medium', 4, 49, '2025-06-19 05:32:06', 53, '2025-06-19 11:32:42', '2025-06-19 11:33:19', 6, 300.00, NULL, NULL, NULL, NULL),
(28, 'lock release', 'core system password locked', 'fixed_confirmed', 'high', 4, 25, '2025-06-19 07:45:59', 21, '2025-06-19 13:46:29', '2025-06-19 13:47:26', 9, 0.00, NULL, NULL, NULL, NULL),
(29, 'Internet Problem', 'Internet access has been turned off. we need a quick review.', 'fixed_confirmed', 'high', 5, 14, '2025-06-19 13:41:40', 10, '2025-06-19 16:51:58', '2025-06-21 11:27:26', 4, 1500.00, 'Tele Finally fixed it.', NULL, NULL, NULL),
(30, 'lock user from core system', 'lock user from core system for user betelehem', 'fixed_confirmed', 'low', 3, 22, '2025-06-21 05:42:41', 18, '2025-06-21 08:42:41', '2025-06-21 09:09:03', 9, 0.00, 'solved', NULL, NULL, NULL),
(31, 'lock user from core system', 'lock user from core system', 'fixed_confirmed', 'low', 3, 19, '2025-06-21 05:44:17', 15, '2025-06-21 08:44:17', '2025-06-21 09:08:30', 9, 0.00, 'solved', NULL, NULL, NULL),
(32, 'Add SSD drive', 'Add SSD drive for storage purpose', 'fixed_confirmed', 'medium', 3, 56, '2025-06-21 05:46:30', 48, '2025-06-21 09:06:36', '2025-06-21 09:08:10', 2, 100.00, 'solved', NULL, NULL, NULL),
(35, 'Format computer and add printer driver', 'Format computer and add printer driver finally share this printer for other three desktop computer', 'fixed_confirmed', 'high', 3, 56, '2025-06-21 05:50:59', 48, '2025-06-21 09:05:27', '2025-06-21 09:07:42', 8, 500.00, 'solved', NULL, NULL, NULL),
(38, 'payment proces', 'how can we handle Lucy insurance vehicles an insurance using the system and how to handle the payment process', 'pending', 'low', 62, 59, '2025-06-21 07:57:42', 52, '2025-06-21 10:58:17', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(39, 'Bond Extension', 'Bond extension: when extending any number of days, the system should automatically extend from the expiry date of the current policy, or allow effective date flexibility.', 'pending', 'high', 62, 59, '2025-06-21 07:59:20', 52, '2025-06-21 10:59:57', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(40, 'professional Indemnity', 'Professional Indemnity policy no. 10956: sum insured change is not calculating correctly.', 'pending', 'low', 62, 59, '2025-06-21 08:02:11', 52, '2025-06-21 11:02:29', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(41, 'Date Ended Accidentally', 'We have ended the date accidentally we request a fix.', 'fixed_confirmed', 'medium', 5, 44, '2025-06-21 08:20:40', 40, '2025-06-21 11:20:40', '2025-06-21 11:27:00', 8, 300.00, 'Went into the database and changed it using a console', NULL, NULL, NULL),
(42, 'upgrade computer storage', 'Storage needed un upgrade for performance', 'fixed_confirmed', 'low', 4, 49, '2025-06-21 08:32:06', 53, '2025-06-21 11:32:06', '2025-06-21 11:38:04', 1, 500.00, 'add a external SSD drive to increase the performance of computer', NULL, NULL, NULL),
(43, 'Lock release', 'Core system password locked', 'fixed_confirmed', 'high', 4, 28, '2025-06-21 08:45:18', 24, '2025-06-21 11:45:18', '2025-06-21 11:46:02', 9, 50.00, 'released it.', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `incident_counts`
-- (See below for the actual view)
--
CREATE TABLE `incident_counts` (
`branch_name` varchar(100)
,`name` varchar(100)
,`report_date` date
,`total_incidents` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `incident_fix_times`
-- (See below for the actual view)
--
CREATE TABLE `incident_fix_times` (
`incident_id` int(11)
,`title` varchar(255)
,`report_date` timestamp
,`fixed_date` datetime
,`days_to_fix` int(7)
,`branch_name` varchar(100)
,`name` varchar(100)
,`assigned_staff` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `incident_logs`
--

CREATE TABLE `incident_logs` (
  `id` int(11) NOT NULL,
  `incident_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incident_logs`
--

INSERT INTO `incident_logs` (`id`, `incident_id`, `user_id`, `action`, `created_at`) VALUES
(1, 1, 56, 'Incident reported by User ID: 56', '2025-06-17 04:57:55'),
(2, 1, 50, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-17 04:59:23'),
(3, 1, 4, 'Status changed to fixed', '2025-06-17 05:03:18'),
(4, NULL, 47, 'Reset password for user ID 1', '2025-06-17 07:37:39'),
(5, NULL, 50, 'Reset password for user ID 46', '2025-06-17 07:42:36'),
(6, 2, 46, 'Incident reported by User ID: 46', '2025-06-17 07:45:58'),
(7, NULL, 47, 'Reset password for user ID 3', '2025-06-17 07:45:59'),
(8, 2, 47, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-17 07:46:31'),
(9, 2, 4, 'Status changed to fixed', '2025-06-17 07:50:36'),
(10, 3, 57, 'Incident reported by User ID: 57', '2025-06-17 07:51:27'),
(11, 3, 61, 'Assigned to IT Staff (User ID: Mengistu Ferdie)', '2025-06-17 07:55:03'),
(12, 3, 3, 'Status changed to pending', '2025-06-17 07:56:18'),
(13, 3, 3, 'Status changed to fixed', '2025-06-17 07:56:56'),
(14, 4, 52, 'Incident reported by User ID: 52', '2025-06-17 08:10:16'),
(15, 4, 50, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-17 08:10:39'),
(16, 4, 4, 'Status changed to fixed', '2025-06-17 08:11:05'),
(17, 5, 57, 'Incident reported by User ID: 57', '2025-06-17 08:54:59'),
(18, 5, 50, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-17 08:56:08'),
(19, 5, 4, 'Status changed to fixed', '2025-06-17 08:56:32'),
(20, 6, 59, 'Incident reported by User ID: 59', '2025-06-18 04:10:28'),
(21, 7, 59, 'Incident reported by User ID: 59', '2025-06-18 04:11:15'),
(22, 8, 59, 'Incident reported by User ID: 59', '2025-06-18 04:13:08'),
(23, 9, 59, 'Incident reported by User ID: 59', '2025-06-18 04:14:05'),
(24, 10, 59, 'Incident reported by User ID: 59', '2025-06-18 04:15:13'),
(25, 10, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:16:19'),
(26, 9, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:16:27'),
(27, 8, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:16:29'),
(28, 7, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:16:32'),
(29, 6, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:16:35'),
(30, NULL, 1, 'Reset password for user ID 57', '2025-06-18 04:21:54'),
(31, 11, 57, 'Incident reported by User ID: 57', '2025-06-18 04:23:36'),
(32, 12, 51, 'Incident reported by User ID: 51', '2025-06-18 04:26:12'),
(33, 13, 53, 'Incident reported by User ID: 53', '2025-06-18 04:26:29'),
(34, 14, 53, 'Incident reported by User ID: 53', '2025-06-18 04:27:46'),
(35, 14, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:28:02'),
(36, 13, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:28:05'),
(37, 12, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:28:07'),
(38, 11, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-18 04:28:09'),
(39, 15, 51, 'Incident reported by User ID: 51', '2025-06-18 04:29:15'),
(40, 15, 61, 'Status updated to \'pending\'', '2025-06-18 04:30:22'),
(41, 15, 61, 'Status updated to \'rejected\'', '2025-06-18 04:30:54'),
(42, 16, 51, 'Incident reported by User ID: 51', '2025-06-18 04:33:45'),
(43, 16, 61, 'Assigned to IT Staff (User ID: Mengistu Ferdie)', '2025-06-18 04:34:26'),
(44, 16, 3, 'Status changed to fixed', '2025-06-18 04:36:24'),
(45, 16, 3, 'Status changed to fixed', '2025-06-18 04:36:49'),
(46, 11, 62, 'Status changed to fixed', '2025-06-18 05:24:27'),
(47, 12, 1, 'Assigned to IT Staff (Mengistu Ferdie)', '2025-06-18 05:37:52'),
(48, 12, 1, 'Assigned to IT Staff (Yehuwalashet Yitagesu)', '2025-06-18 05:37:57'),
(49, NULL, 47, 'Reset password for user ID 2', '2025-06-18 08:45:27'),
(50, 12, 2, 'Status changed to pending', '2025-06-18 08:51:09'),
(51, 12, 2, 'Status changed to fixed', '2025-06-18 08:51:36'),
(52, 17, 29, 'Incident reported by User ID: 29', '2025-06-18 09:31:08'),
(53, 17, 63, 'Assigned to IT Staff (User ID: Yehuwalashet Yitagesu)', '2025-06-18 09:39:46'),
(54, 18, 49, 'Incident reported by User ID: 49', '2025-06-18 09:58:56'),
(55, 18, 63, 'Assigned to IT Staff (User ID: Yehuwalashet Yitagesu)', '2025-06-18 09:59:33'),
(56, 17, 2, 'Status changed to fixed', '2025-06-18 10:00:23'),
(57, 18, 2, 'Status changed to fixed', '2025-06-18 10:01:47'),
(58, 19, 59, 'Incident reported by User ID: 59', '2025-06-18 10:18:04'),
(59, 19, 50, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-18 10:19:04'),
(60, 19, 4, 'Status changed to fixed', '2025-06-18 10:19:56'),
(61, NULL, 47, 'Reset password for user ID 49', '2025-06-18 10:22:31'),
(62, 20, 49, 'Incident reported by User ID: 49', '2025-06-18 10:26:49'),
(63, 20, 50, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-18 10:27:13'),
(64, 20, 4, 'Status changed to fixed', '2025-06-18 10:27:38'),
(65, 21, 9, 'Incident reported by User ID: 9', '2025-06-18 10:54:43'),
(66, 21, 50, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-18 10:55:01'),
(67, 21, 4, 'Status changed to fixed', '2025-06-18 10:55:19'),
(68, 22, 59, 'Incident reported by User ID: 59', '2025-06-19 02:54:57'),
(69, 23, 59, 'Incident reported by User ID: 59', '2025-06-19 02:58:12'),
(70, 24, 59, 'Incident reported by User ID: 59', '2025-06-19 02:59:31'),
(71, 25, 59, 'Incident reported by User ID: 59', '2025-06-19 03:00:28'),
(72, 25, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-19 03:01:00'),
(73, 24, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-19 03:01:24'),
(74, 23, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-19 03:01:27'),
(75, 22, 1, 'Assigned to IT Staff (User ID: Mikiyas Wondimu)', '2025-06-19 03:01:31'),
(76, 25, 62, 'Status changed to fixed', '2025-06-19 04:37:12'),
(77, 26, 46, 'Incident reported by User ID: 46', '2025-06-19 04:51:09'),
(78, 26, 61, 'Assigned to IT Staff (User ID: Mengistu Ferdie)', '2025-06-19 04:51:50'),
(79, 26, 3, 'Status changed to fixed', '2025-06-19 04:52:16'),
(80, 27, 49, 'Incident reported by User ID: 49', '2025-06-19 05:32:06'),
(81, 27, 50, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-19 05:32:42'),
(82, 27, 4, 'Status changed to fixed', '2025-06-19 05:33:19'),
(83, 24, 62, 'Status changed to pending', '2025-06-19 07:18:09'),
(84, 23, 62, 'Status changed to pending', '2025-06-19 07:18:25'),
(85, 22, 62, 'Status changed to pending', '2025-06-19 07:18:29'),
(86, 14, 62, 'Status changed to pending', '2025-06-19 07:18:32'),
(87, 13, 62, 'Status changed to pending', '2025-06-19 07:18:38'),
(88, 24, 62, 'Status changed to pending', '2025-06-19 07:18:41'),
(89, 10, 62, 'Status changed to pending', '2025-06-19 07:18:48'),
(90, 9, 62, 'Status changed to pending', '2025-06-19 07:19:06'),
(91, 8, 62, 'Status changed to pending', '2025-06-19 07:19:21'),
(92, 7, 62, 'Status changed to pending', '2025-06-19 07:19:25'),
(93, 6, 62, 'Status changed to pending', '2025-06-19 07:19:30'),
(94, 28, 25, 'Incident reported by User ID: 25', '2025-06-19 07:45:59'),
(95, 28, 50, 'Assigned to IT Staff (User ID: Eleni Zerihun)', '2025-06-19 07:46:29'),
(96, 28, 4, 'Status changed to fixed', '2025-06-19 07:47:26'),
(97, 29, 14, 'Incident reported by User ID: 14', '2025-06-19 13:41:40'),
(98, 29, 5, 'Status changed to support by Aaron Tamirat', '2025-06-19 13:51:09'),
(99, 29, 47, 'Assigned to IT Staff (User ID: Aaron Tamirat)', '2025-06-19 13:51:58'),
(100, 29, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-19 13:54:34'),
(101, 30, 22, 'Incident reported by User ID: 22', '2025-06-21 05:42:41'),
(102, 31, 19, 'Incident reported by User ID: 19', '2025-06-21 05:44:17'),
(103, 35, 61, 'Assigned to IT Staff (User ID: Mengistu Ferdie)', '2025-06-21 06:05:27'),
(104, 15, 61, 'Assigned to IT Staff (User ID: Mengistu Ferdie)', '2025-06-21 06:06:15'),
(105, 32, 61, 'Assigned to IT Staff (User ID: Mengistu Ferdie)', '2025-06-21 06:06:36'),
(106, 35, 3, 'Status changed to fixed by Mengistu Ferdie', '2025-06-21 06:07:42'),
(107, 32, 3, 'Status changed to fixed by Mengistu Ferdie', '2025-06-21 06:08:10'),
(108, 31, 3, 'Status changed to fixed by Mengistu Ferdie', '2025-06-21 06:08:30'),
(109, 30, 3, 'Status changed to fixed by Mengistu Ferdie', '2025-06-21 06:09:03'),
(110, 15, 3, 'Status changed to fixed by Mengistu Ferdie', '2025-06-21 06:09:36'),
(111, 37, 56, 'Incident reported by User ID: 56', '2025-06-21 06:15:06'),
(112, 29, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 07:12:31'),
(113, 29, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 07:14:14'),
(114, 29, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 07:17:43'),
(115, 29, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 07:17:52'),
(116, 38, 59, 'Incident reported by User ID: 59', '2025-06-21 07:57:42'),
(117, 38, 1, 'Assigned to IT Staff (Mikiyas Wondimu)', '2025-06-21 07:58:17'),
(118, 39, 59, 'Incident reported by User ID: 59', '2025-06-21 07:59:20'),
(119, 39, 1, 'Assigned to IT Staff (Mikiyas Wondimu)', '2025-06-21 07:59:49'),
(120, 39, 1, 'Assigned to IT Staff (Mikiyas Wondimu)', '2025-06-21 07:59:57'),
(121, 40, 59, 'Incident reported by User ID: 59', '2025-06-21 08:02:11'),
(122, 40, 1, 'Assigned to IT Staff (Mikiyas Wondimu)', '2025-06-21 08:02:29'),
(123, 29, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 08:18:12'),
(124, 29, 5, 'Status changed to not fixed by Aaron Tamirat', '2025-06-21 08:18:51'),
(125, 29, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 08:18:58'),
(126, 41, 44, 'Incident reported by User ID: 44', '2025-06-21 08:20:41'),
(127, 41, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 08:21:07'),
(128, 41, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 08:27:00'),
(129, 29, 5, 'Status changed to fixed by Aaron Tamirat', '2025-06-21 08:27:26'),
(130, 42, 49, 'Incident reported by User ID: 49', '2025-06-21 08:32:06'),
(131, 40, 62, 'Status changed to pending by Mikiyas Wondimu', '2025-06-21 08:34:41'),
(132, 39, 62, 'Status changed to pending by Mikiyas Wondimu', '2025-06-21 08:34:59'),
(133, 38, 62, 'Status changed to pending by Mikiyas Wondimu', '2025-06-21 08:35:04'),
(134, 42, 4, 'Status changed to fixed by Eleni Zerihun', '2025-06-21 08:38:04'),
(135, 43, 28, 'Incident reported by User ID: 28', '2025-06-21 08:45:18'),
(136, 43, 4, 'Status changed to fixed by Eleni Zerihun', '2025-06-21 08:46:02');

-- --------------------------------------------------------

--
-- Table structure for table `kb_articles`
--

CREATE TABLE `kb_articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kb_categories`
--

CREATE TABLE `kb_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kb_categories`
--

INSERT INTO `kb_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Computer', '2025-06-05 10:43:03'),
(2, 'Hardware', '2025-06-05 10:43:15'),
(3, 'Inline Phone', '2025-06-05 10:43:32'),
(4, 'Internet & Network', '2025-06-05 10:43:47'),
(5, 'Network Sharing Print', '2025-06-05 10:44:03'),
(6, 'Printer', '2025-06-05 10:44:11'),
(7, 'Shared Storage', '2025-06-05 10:44:21'),
(8, 'Applications', '2025-06-05 10:44:50'),
(9, 'Authentication & Login', '2025-06-05 10:45:04'),
(10, 'File Migration', '2025-06-17 07:47:21'),
(11, 'Employee Transfer', '2025-06-18 10:26:08');

-- --------------------------------------------------------

--
-- Table structure for table `kb_feedback`
--

CREATE TABLE `kb_feedback` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `feedback_type` enum('good','bad') NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `related_incident_id` int(11) DEFAULT NULL,
  `is_seen` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `related_incident_id`, `is_seen`, `created_at`) VALUES
(1, 1, 'New incident reported', 1, 1, '2025-06-17 04:57:55'),
(2, 47, 'New incident reported', 1, 1, '2025-06-17 04:57:55'),
(3, 50, 'New incident reported', 1, 1, '2025-06-17 04:57:55'),
(4, 4, 'You have been assigned to an incident', 1, 1, '2025-06-17 04:59:23'),
(5, 56, 'Your incident (ID: 1) has been marked as fixed.', 1, 1, '2025-06-17 05:03:18'),
(6, 1, 'New incident reported', 2, 1, '2025-06-17 07:45:58'),
(7, 47, 'New incident reported', 2, 1, '2025-06-17 07:45:58'),
(8, 50, 'New incident reported', 2, 1, '2025-06-17 07:45:58'),
(9, 4, 'You have been assigned to an incident', 2, 1, '2025-06-17 07:46:31'),
(10, 46, 'Your incident (ID: 2) has been marked as fixed.', 2, 0, '2025-06-17 07:50:36'),
(11, 1, 'New incident reported', 3, 1, '2025-06-17 07:51:27'),
(12, 47, 'New incident reported', 3, 1, '2025-06-17 07:51:27'),
(13, 50, 'New incident reported', 3, 1, '2025-06-17 07:51:27'),
(14, 3, 'You have been assigned to an incident', 3, 1, '2025-06-17 07:55:03'),
(15, 57, 'Your incident (ID: 3) has been marked as pending.', 3, 1, '2025-06-17 07:56:18'),
(16, 57, 'Your incident (ID: 3) has been marked as fixed.', 3, 1, '2025-06-17 07:56:56'),
(17, 1, 'New incident reported', 4, 1, '2025-06-17 08:10:16'),
(18, 47, 'New incident reported', 4, 1, '2025-06-17 08:10:16'),
(19, 50, 'New incident reported', 4, 1, '2025-06-17 08:10:16'),
(20, 61, 'New incident reported', 4, 1, '2025-06-17 08:10:16'),
(21, 4, 'You have been assigned to an incident', 4, 1, '2025-06-17 08:10:39'),
(22, 52, 'Your incident (ID: 4) has been marked as fixed.', 4, 0, '2025-06-17 08:11:05'),
(23, 1, 'New incident reported', 5, 1, '2025-06-17 08:54:59'),
(24, 47, 'New incident reported', 5, 1, '2025-06-17 08:54:59'),
(25, 50, 'New incident reported', 5, 1, '2025-06-17 08:54:59'),
(26, 61, 'New incident reported', 5, 1, '2025-06-17 08:54:59'),
(27, 4, 'You have been assigned to an incident', 5, 1, '2025-06-17 08:56:08'),
(28, 57, 'Your incident (ID: 5) has been marked as fixed.', 5, 1, '2025-06-17 08:56:32'),
(29, 1, 'New incident reported', 6, 1, '2025-06-18 04:10:28'),
(30, 47, 'New incident reported', 6, 1, '2025-06-18 04:10:28'),
(31, 50, 'New incident reported', 6, 1, '2025-06-18 04:10:28'),
(32, 61, 'New incident reported', 6, 1, '2025-06-18 04:10:28'),
(33, 1, 'New incident reported', 7, 1, '2025-06-18 04:11:15'),
(34, 47, 'New incident reported', 7, 1, '2025-06-18 04:11:15'),
(35, 50, 'New incident reported', 7, 1, '2025-06-18 04:11:15'),
(36, 61, 'New incident reported', 7, 1, '2025-06-18 04:11:15'),
(37, 1, 'New incident reported', 8, 1, '2025-06-18 04:13:08'),
(38, 47, 'New incident reported', 8, 1, '2025-06-18 04:13:08'),
(39, 50, 'New incident reported', 8, 1, '2025-06-18 04:13:08'),
(40, 61, 'New incident reported', 8, 1, '2025-06-18 04:13:08'),
(41, 1, 'New incident reported', 9, 1, '2025-06-18 04:14:05'),
(42, 47, 'New incident reported', 9, 1, '2025-06-18 04:14:05'),
(43, 50, 'New incident reported', 9, 1, '2025-06-18 04:14:05'),
(44, 61, 'New incident reported', 9, 1, '2025-06-18 04:14:05'),
(45, 1, 'New incident reported', 10, 1, '2025-06-18 04:15:13'),
(46, 47, 'New incident reported', 10, 1, '2025-06-18 04:15:13'),
(47, 50, 'New incident reported', 10, 1, '2025-06-18 04:15:13'),
(48, 61, 'New incident reported', 10, 1, '2025-06-18 04:15:13'),
(49, 62, 'You have been assigned to an incident', 10, 1, '2025-06-18 04:16:19'),
(50, 62, 'You have been assigned to an incident', 9, 1, '2025-06-18 04:16:27'),
(51, 62, 'You have been assigned to an incident', 8, 1, '2025-06-18 04:16:29'),
(52, 62, 'You have been assigned to an incident', 7, 1, '2025-06-18 04:16:32'),
(53, 62, 'You have been assigned to an incident', 6, 1, '2025-06-18 04:16:35'),
(54, 1, 'New incident reported', 11, 1, '2025-06-18 04:23:36'),
(55, 47, 'New incident reported', 11, 1, '2025-06-18 04:23:36'),
(56, 50, 'New incident reported', 11, 1, '2025-06-18 04:23:36'),
(57, 61, 'New incident reported', 11, 1, '2025-06-18 04:23:36'),
(58, 1, 'New incident reported', 12, 1, '2025-06-18 04:26:12'),
(59, 47, 'New incident reported', 12, 1, '2025-06-18 04:26:12'),
(60, 50, 'New incident reported', 12, 1, '2025-06-18 04:26:12'),
(61, 61, 'New incident reported', 12, 1, '2025-06-18 04:26:12'),
(62, 1, 'New incident reported', 13, 1, '2025-06-18 04:26:29'),
(63, 47, 'New incident reported', 13, 1, '2025-06-18 04:26:29'),
(64, 50, 'New incident reported', 13, 1, '2025-06-18 04:26:29'),
(65, 61, 'New incident reported', 13, 1, '2025-06-18 04:26:29'),
(66, 1, 'New incident reported', 14, 1, '2025-06-18 04:27:46'),
(67, 47, 'New incident reported', 14, 1, '2025-06-18 04:27:46'),
(68, 50, 'New incident reported', 14, 1, '2025-06-18 04:27:46'),
(69, 61, 'New incident reported', 14, 1, '2025-06-18 04:27:46'),
(70, 62, 'You have been assigned to an incident', 14, 1, '2025-06-18 04:28:02'),
(71, 62, 'You have been assigned to an incident', 13, 1, '2025-06-18 04:28:05'),
(72, 62, 'You have been assigned to an incident', 12, 1, '2025-06-18 04:28:07'),
(73, 62, 'You have been assigned to an incident', 11, 1, '2025-06-18 04:28:09'),
(74, 1, 'New incident reported', 15, 1, '2025-06-18 04:29:15'),
(75, 47, 'New incident reported', 15, 1, '2025-06-18 04:29:15'),
(76, 50, 'New incident reported', 15, 1, '2025-06-18 04:29:15'),
(77, 61, 'New incident reported', 15, 1, '2025-06-18 04:29:15'),
(78, 51, 'Your incident (ID: 15) has been marked as pending.', 15, 1, '2025-06-18 04:30:22'),
(79, 51, 'Your incident (ID: 15) has been marked as rejected.', 15, 1, '2025-06-18 04:30:54'),
(80, 1, 'New incident reported', 16, 1, '2025-06-18 04:33:45'),
(81, 47, 'New incident reported', 16, 1, '2025-06-18 04:33:45'),
(82, 50, 'New incident reported', 16, 1, '2025-06-18 04:33:45'),
(83, 61, 'New incident reported', 16, 1, '2025-06-18 04:33:45'),
(84, 3, 'You have been assigned to an incident', 16, 1, '2025-06-18 04:34:26'),
(85, 51, 'Your incident (ID: 16) has been marked as fixed.', 16, 0, '2025-06-18 04:36:24'),
(86, 51, 'Your incident (ID: 16) has been marked as fixed.', 16, 0, '2025-06-18 04:36:49'),
(87, 57, 'Your incident (ID: 11) has been marked as fixed.', 11, 0, '2025-06-18 05:24:27'),
(88, 3, 'You have been assigned to an incident', 12, 1, '2025-06-18 05:37:52'),
(89, 2, 'You have been assigned to an incident', 12, 1, '2025-06-18 05:37:57'),
(90, 51, 'Your incident (ID: 12) has been marked as pending.', 12, 0, '2025-06-18 08:51:09'),
(91, 51, 'Your incident (ID: 12) has been marked as fixed.', 12, 0, '2025-06-18 08:51:36'),
(92, 1, 'New incident reported', 17, 1, '2025-06-18 09:31:08'),
(93, 47, 'New incident reported', 17, 1, '2025-06-18 09:31:08'),
(94, 50, 'New incident reported', 17, 1, '2025-06-18 09:31:08'),
(95, 61, 'New incident reported', 17, 1, '2025-06-18 09:31:08'),
(96, 63, 'New incident reported', 17, 1, '2025-06-18 09:31:08'),
(97, 2, 'You have been assigned to an incident', 17, 0, '2025-06-18 09:39:46'),
(98, 1, 'New incident reported', 18, 1, '2025-06-18 09:58:56'),
(99, 47, 'New incident reported', 18, 1, '2025-06-18 09:58:56'),
(100, 50, 'New incident reported', 18, 1, '2025-06-18 09:58:56'),
(101, 61, 'New incident reported', 18, 1, '2025-06-18 09:58:56'),
(102, 63, 'New incident reported', 18, 1, '2025-06-18 09:58:56'),
(103, 2, 'You have been assigned to an incident', 18, 0, '2025-06-18 09:59:33'),
(104, 29, 'Your incident (ID: 17) has been marked as fixed.', 17, 0, '2025-06-18 10:00:23'),
(105, 49, 'Your incident (ID: 18) has been marked as fixed.', 18, 1, '2025-06-18 10:01:47'),
(106, 1, 'New incident reported', 19, 1, '2025-06-18 10:18:04'),
(107, 47, 'New incident reported', 19, 1, '2025-06-18 10:18:04'),
(108, 50, 'New incident reported', 19, 1, '2025-06-18 10:18:04'),
(109, 61, 'New incident reported', 19, 1, '2025-06-18 10:18:04'),
(110, 63, 'New incident reported', 19, 0, '2025-06-18 10:18:04'),
(111, 4, 'You have been assigned to an incident', 19, 1, '2025-06-18 10:19:04'),
(112, 59, 'Your incident (ID: 19) has been marked as fixed.', 19, 1, '2025-06-18 10:19:56'),
(113, 1, 'New incident reported', 20, 1, '2025-06-18 10:26:49'),
(114, 47, 'New incident reported', 20, 1, '2025-06-18 10:26:49'),
(115, 50, 'New incident reported', 20, 1, '2025-06-18 10:26:49'),
(116, 61, 'New incident reported', 20, 1, '2025-06-18 10:26:49'),
(117, 63, 'New incident reported', 20, 0, '2025-06-18 10:26:49'),
(118, 4, 'You have been assigned to an incident', 20, 1, '2025-06-18 10:27:13'),
(119, 49, 'Your incident (ID: 20) has been marked as fixed.', 20, 1, '2025-06-18 10:27:38'),
(120, 1, 'New incident reported', 21, 1, '2025-06-18 10:54:43'),
(121, 47, 'New incident reported', 21, 1, '2025-06-18 10:54:43'),
(122, 50, 'New incident reported', 21, 1, '2025-06-18 10:54:43'),
(123, 61, 'New incident reported', 21, 1, '2025-06-18 10:54:43'),
(124, 63, 'New incident reported', 21, 0, '2025-06-18 10:54:43'),
(125, 4, 'You have been assigned to an incident', 21, 1, '2025-06-18 10:55:01'),
(126, 9, 'Your incident (ID: 21) has been marked as fixed.', 21, 0, '2025-06-18 10:55:19'),
(127, 1, 'New incident reported', 22, 1, '2025-06-19 02:54:57'),
(128, 47, 'New incident reported', 22, 1, '2025-06-19 02:54:57'),
(129, 50, 'New incident reported', 22, 1, '2025-06-19 02:54:57'),
(130, 61, 'New incident reported', 22, 1, '2025-06-19 02:54:57'),
(131, 63, 'New incident reported', 22, 0, '2025-06-19 02:54:57'),
(132, 1, 'New incident reported', 23, 1, '2025-06-19 02:58:12'),
(133, 47, 'New incident reported', 23, 1, '2025-06-19 02:58:12'),
(134, 50, 'New incident reported', 23, 1, '2025-06-19 02:58:12'),
(135, 61, 'New incident reported', 23, 1, '2025-06-19 02:58:12'),
(136, 63, 'New incident reported', 23, 0, '2025-06-19 02:58:12'),
(137, 1, 'New incident reported', 24, 1, '2025-06-19 02:59:31'),
(138, 47, 'New incident reported', 24, 1, '2025-06-19 02:59:31'),
(139, 50, 'New incident reported', 24, 1, '2025-06-19 02:59:31'),
(140, 61, 'New incident reported', 24, 1, '2025-06-19 02:59:31'),
(141, 63, 'New incident reported', 24, 0, '2025-06-19 02:59:31'),
(142, 1, 'New incident reported', 25, 1, '2025-06-19 03:00:28'),
(143, 47, 'New incident reported', 25, 1, '2025-06-19 03:00:28'),
(144, 50, 'New incident reported', 25, 1, '2025-06-19 03:00:28'),
(145, 61, 'New incident reported', 25, 1, '2025-06-19 03:00:28'),
(146, 63, 'New incident reported', 25, 0, '2025-06-19 03:00:28'),
(147, 62, 'You have been assigned to an incident', 25, 1, '2025-06-19 03:01:00'),
(148, 62, 'You have been assigned to an incident', 24, 1, '2025-06-19 03:01:24'),
(149, 62, 'You have been assigned to an incident', 23, 1, '2025-06-19 03:01:27'),
(150, 62, 'You have been assigned to an incident', 22, 1, '2025-06-19 03:01:31'),
(151, 59, 'Your incident (ID: 25) has been marked as fixed.', 25, 0, '2025-06-19 04:37:12'),
(152, 1, 'New incident reported', 26, 1, '2025-06-19 04:51:09'),
(153, 47, 'New incident reported', 26, 1, '2025-06-19 04:51:09'),
(154, 50, 'New incident reported', 26, 1, '2025-06-19 04:51:09'),
(155, 61, 'New incident reported', 26, 1, '2025-06-19 04:51:09'),
(156, 63, 'New incident reported', 26, 0, '2025-06-19 04:51:09'),
(157, 3, 'You have been assigned to an incident', 26, 1, '2025-06-19 04:51:50'),
(158, 46, 'Your incident (ID: 26) has been marked as fixed.', 26, 0, '2025-06-19 04:52:16'),
(159, 1, 'New incident reported', 27, 1, '2025-06-19 05:32:06'),
(160, 47, 'New incident reported', 27, 1, '2025-06-19 05:32:06'),
(161, 50, 'New incident reported', 27, 1, '2025-06-19 05:32:06'),
(162, 61, 'New incident reported', 27, 1, '2025-06-19 05:32:06'),
(163, 63, 'New incident reported', 27, 0, '2025-06-19 05:32:06'),
(164, 4, 'You have been assigned to an incident', 27, 1, '2025-06-19 05:32:42'),
(165, 49, 'Your incident (ID: 27) has been marked as fixed.', 27, 1, '2025-06-19 05:33:19'),
(166, 59, 'Your incident (ID: 24) has been marked as pending.', 24, 0, '2025-06-19 07:18:09'),
(167, 59, 'Your incident (ID: 23) has been marked as pending.', 23, 0, '2025-06-19 07:18:25'),
(168, 59, 'Your incident (ID: 22) has been marked as pending.', 22, 0, '2025-06-19 07:18:29'),
(169, 53, 'Your incident (ID: 14) has been marked as pending.', 14, 0, '2025-06-19 07:18:32'),
(170, 53, 'Your incident (ID: 13) has been marked as pending.', 13, 0, '2025-06-19 07:18:38'),
(171, 59, 'Your incident (ID: 24) has been marked as pending.', 24, 0, '2025-06-19 07:18:41'),
(172, 59, 'Your incident (ID: 10) has been marked as pending.', 10, 0, '2025-06-19 07:18:48'),
(173, 59, 'Your incident (ID: 9) has been marked as pending.', 9, 0, '2025-06-19 07:19:06'),
(174, 59, 'Your incident (ID: 8) has been marked as pending.', 8, 0, '2025-06-19 07:19:21'),
(175, 59, 'Your incident (ID: 7) has been marked as pending.', 7, 0, '2025-06-19 07:19:25'),
(176, 59, 'Your incident (ID: 6) has been marked as pending.', 6, 0, '2025-06-19 07:19:30'),
(177, 1, 'New incident reported', 28, 1, '2025-06-19 07:45:59'),
(178, 47, 'New incident reported', 28, 1, '2025-06-19 07:45:59'),
(179, 50, 'New incident reported', 28, 1, '2025-06-19 07:45:59'),
(180, 61, 'New incident reported', 28, 1, '2025-06-19 07:45:59'),
(181, 63, 'New incident reported', 28, 0, '2025-06-19 07:45:59'),
(182, 4, 'You have been assigned to an incident', 28, 1, '2025-06-19 07:46:29'),
(183, 25, 'Your incident (ID: 28) has been marked as fixed.', 28, 0, '2025-06-19 07:47:26'),
(184, 1, 'New incident reported', 29, 1, '2025-06-19 13:41:40'),
(185, 47, 'New incident reported', 29, 1, '2025-06-19 13:41:40'),
(186, 50, 'New incident reported', 29, 1, '2025-06-19 13:41:40'),
(187, 61, 'New incident reported', 29, 1, '2025-06-19 13:41:40'),
(188, 63, 'New incident reported', 29, 0, '2025-06-19 13:41:40'),
(189, 5, 'You have been assigned to an incident', 29, 1, '2025-06-19 13:41:40'),
(190, 1, 'Aaron Tamirat asked for support for incident 29', 29, 1, '2025-06-19 13:51:09'),
(191, 47, 'Aaron Tamirat asked for support for incident 29', 29, 1, '2025-06-19 13:51:09'),
(192, 50, 'Aaron Tamirat asked for support for incident 29', 29, 1, '2025-06-19 13:51:09'),
(193, 61, 'Aaron Tamirat asked for support for incident 29', 29, 1, '2025-06-19 13:51:09'),
(194, 63, 'Aaron Tamirat asked for support for incident 29', 29, 0, '2025-06-19 13:51:09'),
(195, 5, 'You have been assigned to an incident', 29, 1, '2025-06-19 13:51:58'),
(196, 14, 'Your incident (ID: 29) has been marked as fixed.', 29, 0, '2025-06-19 13:54:34'),
(197, 1, 'New incident reported', 30, 1, '2025-06-21 05:42:41'),
(198, 47, 'New incident reported', 30, 1, '2025-06-21 05:42:41'),
(199, 50, 'New incident reported', 30, 1, '2025-06-21 05:42:41'),
(200, 61, 'New incident reported', 30, 1, '2025-06-21 05:42:41'),
(201, 63, 'New incident reported', 30, 0, '2025-06-21 05:42:41'),
(202, 3, 'You have been assigned to an incident', 30, 0, '2025-06-21 05:42:41'),
(203, 1, 'New incident reported', 31, 1, '2025-06-21 05:44:17'),
(204, 47, 'New incident reported', 31, 1, '2025-06-21 05:44:17'),
(205, 50, 'New incident reported', 31, 1, '2025-06-21 05:44:17'),
(206, 61, 'New incident reported', 31, 1, '2025-06-21 05:44:17'),
(207, 63, 'New incident reported', 31, 0, '2025-06-21 05:44:17'),
(208, 3, 'You have been assigned to an incident', 31, 0, '2025-06-21 05:44:17'),
(209, 1, 'New incident reported', 32, 1, '2025-06-21 05:46:30'),
(210, 47, 'New incident reported', 32, 1, '2025-06-21 05:46:30'),
(211, 50, 'New incident reported', 32, 1, '2025-06-21 05:46:30'),
(212, 61, 'New incident reported', 32, 1, '2025-06-21 05:46:30'),
(213, 63, 'New incident reported', 32, 0, '2025-06-21 05:46:30'),
(214, 1, 'New incident reported', 33, 1, '2025-06-21 05:47:19'),
(215, 47, 'New incident reported', 33, 1, '2025-06-21 05:47:19'),
(216, 50, 'New incident reported', 33, 1, '2025-06-21 05:47:19'),
(217, 61, 'New incident reported', 33, 1, '2025-06-21 05:47:19'),
(218, 63, 'New incident reported', 33, 0, '2025-06-21 05:47:19'),
(219, 1, 'New incident reported', 34, 1, '2025-06-21 05:48:08'),
(220, 47, 'New incident reported', 34, 1, '2025-06-21 05:48:08'),
(221, 50, 'New incident reported', 34, 1, '2025-06-21 05:48:08'),
(222, 61, 'New incident reported', 34, 1, '2025-06-21 05:48:08'),
(223, 63, 'New incident reported', 34, 0, '2025-06-21 05:48:08'),
(224, 1, 'New incident reported', 35, 1, '2025-06-21 05:50:59'),
(225, 47, 'New incident reported', 35, 1, '2025-06-21 05:50:59'),
(226, 50, 'New incident reported', 35, 1, '2025-06-21 05:50:59'),
(227, 61, 'New incident reported', 35, 1, '2025-06-21 05:50:59'),
(228, 63, 'New incident reported', 35, 0, '2025-06-21 05:50:59'),
(229, 1, 'New incident reported', 36, 1, '2025-06-21 06:04:30'),
(230, 47, 'New incident reported', 36, 1, '2025-06-21 06:04:30'),
(231, 50, 'New incident reported', 36, 1, '2025-06-21 06:04:30'),
(232, 61, 'New incident reported', 36, 1, '2025-06-21 06:04:30'),
(233, 63, 'New incident reported', 36, 0, '2025-06-21 06:04:30'),
(234, 3, 'You have been assigned to an incident', 35, 0, '2025-06-21 06:05:27'),
(235, 3, 'You have been assigned to an incident', 15, 0, '2025-06-21 06:06:15'),
(236, 3, 'You have been assigned to an incident', 32, 0, '2025-06-21 06:06:36'),
(237, 56, 'Your incident (ID: 35) has been marked as fixed.', 35, 1, '2025-06-21 06:07:42'),
(238, 56, 'Your incident (ID: 32) has been marked as fixed.', 32, 1, '2025-06-21 06:08:10'),
(239, 19, 'Your incident (ID: 31) has been marked as fixed.', 31, 1, '2025-06-21 06:08:30'),
(240, 22, 'Your incident (ID: 30) has been marked as fixed.', 30, 1, '2025-06-21 06:09:03'),
(241, 51, 'Your incident (ID: 15) has been marked as fixed.', 15, 0, '2025-06-21 06:09:36'),
(242, 1, 'New incident reported', 37, 1, '2025-06-21 06:15:06'),
(243, 47, 'New incident reported', 37, 1, '2025-06-21 06:15:06'),
(244, 50, 'New incident reported', 37, 1, '2025-06-21 06:15:06'),
(245, 61, 'New incident reported', 37, 1, '2025-06-21 06:15:06'),
(246, 63, 'New incident reported', 37, 0, '2025-06-21 06:15:06'),
(247, 3, 'You have been assigned to an incident', 37, 0, '2025-06-21 06:15:06'),
(248, 3, 'Incident #32 has been confirmed as fixed.', 32, 0, '2025-06-21 06:15:22'),
(249, 3, 'Incident #32 has been confirmed as fixed.', 32, 0, '2025-06-21 06:19:07'),
(250, 3, 'Incident #35 has been confirmed as fixed.', 35, 0, '2025-06-21 06:19:08'),
(251, 3, 'Incident #31 has been confirmed as fixed.', 31, 0, '2025-06-21 06:37:39'),
(252, 3, 'Incident #30 has been confirmed as fixed.', 30, 0, '2025-06-21 06:38:03'),
(253, 14, 'Your incident (ID: 29) has been marked as fixed.', 29, 0, '2025-06-21 07:12:31'),
(254, 14, 'Your incident (ID: 29) has been marked as fixed.', 29, 0, '2025-06-21 07:14:14'),
(255, 14, 'Your incident (ID: 29) has been marked as fixed.', 29, 0, '2025-06-21 07:17:43'),
(256, 14, 'Your incident (ID: 29) has been marked as fixed.', 29, 0, '2025-06-21 07:17:52'),
(257, 1, 'New incident reported', 38, 1, '2025-06-21 07:57:42'),
(258, 47, 'New incident reported', 38, 1, '2025-06-21 07:57:42'),
(259, 50, 'New incident reported', 38, 1, '2025-06-21 07:57:42'),
(260, 61, 'New incident reported', 38, 1, '2025-06-21 07:57:42'),
(261, 63, 'New incident reported', 38, 1, '2025-06-21 07:57:42'),
(262, 2, 'You have been assigned to an incident', 38, 1, '2025-06-21 07:57:42'),
(263, 62, 'You have been assigned to an incident', 38, 1, '2025-06-21 07:58:17'),
(264, 1, 'New incident reported', 39, 1, '2025-06-21 07:59:20'),
(265, 47, 'New incident reported', 39, 1, '2025-06-21 07:59:20'),
(266, 50, 'New incident reported', 39, 1, '2025-06-21 07:59:20'),
(267, 61, 'New incident reported', 39, 1, '2025-06-21 07:59:20'),
(268, 63, 'New incident reported', 39, 1, '2025-06-21 07:59:20'),
(269, 2, 'You have been assigned to an incident', 39, 1, '2025-06-21 07:59:20'),
(270, 62, 'You have been assigned to an incident', 39, 1, '2025-06-21 07:59:49'),
(271, 62, 'You have been assigned to an incident', 39, 1, '2025-06-21 07:59:57'),
(272, 1, 'New incident reported', 40, 1, '2025-06-21 08:02:11'),
(273, 47, 'New incident reported', 40, 1, '2025-06-21 08:02:11'),
(274, 50, 'New incident reported', 40, 1, '2025-06-21 08:02:11'),
(275, 61, 'New incident reported', 40, 1, '2025-06-21 08:02:11'),
(276, 63, 'New incident reported', 40, 1, '2025-06-21 08:02:11'),
(277, 2, 'You have been assigned to an incident', 40, 1, '2025-06-21 08:02:11'),
(278, 62, 'You have been assigned to an incident', 40, 1, '2025-06-21 08:02:29'),
(279, 14, 'Your incident (ID: 29) has been marked as fixed.', 29, 0, '2025-06-21 08:18:12'),
(280, 14, 'Your incident (ID: 29) has been marked as not fixed.', 29, 0, '2025-06-21 08:18:51'),
(281, 14, 'Your incident (ID: 29) has been marked as fixed.', 29, 0, '2025-06-21 08:18:58'),
(282, 1, 'New incident reported', 41, 0, '2025-06-21 08:20:41'),
(283, 47, 'New incident reported', 41, 1, '2025-06-21 08:20:41'),
(284, 50, 'New incident reported', 41, 1, '2025-06-21 08:20:41'),
(285, 61, 'New incident reported', 41, 0, '2025-06-21 08:20:41'),
(286, 63, 'New incident reported', 41, 0, '2025-06-21 08:20:41'),
(287, 5, 'You have been assigned to an incident', 41, 1, '2025-06-21 08:20:41'),
(288, 44, 'Your incident (ID: 41) has been marked as fixed.', 41, 1, '2025-06-21 08:21:07'),
(289, 44, 'Your incident (ID: 41) has been marked as fixed.', 41, 1, '2025-06-21 08:27:00'),
(290, 14, 'Your incident (ID: 29) has been marked as fixed.', 29, 0, '2025-06-21 08:27:26'),
(291, 4, 'Incident #27 has been confirmed as fixed.', 27, 1, '2025-06-21 08:28:06'),
(292, 1, 'New incident reported', 42, 0, '2025-06-21 08:32:06'),
(293, 47, 'New incident reported', 42, 1, '2025-06-21 08:32:06'),
(294, 50, 'New incident reported', 42, 1, '2025-06-21 08:32:06'),
(295, 61, 'New incident reported', 42, 0, '2025-06-21 08:32:06'),
(296, 63, 'New incident reported', 42, 0, '2025-06-21 08:32:06'),
(297, 4, 'You have been assigned to an incident', 42, 1, '2025-06-21 08:32:06'),
(298, 59, 'Your incident (ID: 40) has been marked as pending.', 40, 0, '2025-06-21 08:34:41'),
(299, 59, 'Your incident (ID: 39) has been marked as pending.', 39, 0, '2025-06-21 08:34:59'),
(300, 59, 'Your incident (ID: 38) has been marked as pending.', 38, 0, '2025-06-21 08:35:04'),
(301, 5, 'Incident #41 has been confirmed as fixed.', 41, 1, '2025-06-21 08:35:14'),
(302, 5, 'Incident #41 has been confirmed as fixed.', 41, 1, '2025-06-21 08:35:15'),
(303, 49, 'Your incident (ID: 42) has been marked as fixed.', 42, 1, '2025-06-21 08:38:04'),
(304, 4, 'Incident #42 has been confirmed as fixed.', 42, 1, '2025-06-21 08:38:29'),
(305, 1, 'New incident reported', 43, 0, '2025-06-21 08:45:18'),
(306, 47, 'New incident reported', 43, 1, '2025-06-21 08:45:18'),
(307, 50, 'New incident reported', 43, 0, '2025-06-21 08:45:18'),
(308, 61, 'New incident reported', 43, 0, '2025-06-21 08:45:18'),
(309, 63, 'New incident reported', 43, 0, '2025-06-21 08:45:18'),
(310, 4, 'You have been assigned to an incident', 43, 1, '2025-06-21 08:45:18'),
(311, 28, 'Your incident (ID: 43) has been marked as fixed.', 43, 1, '2025-06-21 08:46:02'),
(312, 4, 'Incident #43 has been confirmed as fixed.', 43, 1, '2025-06-21 08:46:45');

-- --------------------------------------------------------

--
-- Table structure for table `staff_branch_assignments`
--

CREATE TABLE `staff_branch_assignments` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_branch_assignments`
--

INSERT INTO `staff_branch_assignments` (`id`, `staff_id`, `branch_id`, `created_at`) VALUES
(1, 4, 5, '2025-06-19 12:51:13'),
(2, 4, 8, '2025-06-19 12:51:13'),
(3, 4, 12, '2025-06-19 12:51:13'),
(4, 4, 14, '2025-06-19 12:51:13'),
(5, 4, 17, '2025-06-19 12:51:13'),
(6, 4, 21, '2025-06-19 12:51:13'),
(7, 4, 22, '2025-06-19 12:51:13'),
(8, 4, 24, '2025-06-19 12:51:13'),
(9, 4, 30, '2025-06-19 12:51:13'),
(10, 4, 31, '2025-06-19 12:51:13'),
(11, 4, 34, '2025-06-19 12:51:13'),
(12, 4, 46, '2025-06-19 12:51:13'),
(13, 4, 53, '2025-06-19 12:51:13'),
(14, 5, 2, '2025-06-19 12:53:52'),
(15, 5, 4, '2025-06-19 12:53:52'),
(16, 5, 6, '2025-06-19 12:53:52'),
(17, 5, 10, '2025-06-19 12:53:52'),
(18, 5, 19, '2025-06-19 12:53:52'),
(19, 5, 23, '2025-06-19 12:53:52'),
(20, 5, 28, '2025-06-19 12:53:52'),
(21, 5, 40, '2025-06-19 12:53:52'),
(22, 5, 42, '2025-06-19 12:53:52'),
(23, 5, 44, '2025-06-19 12:53:52'),
(24, 5, 51, '2025-06-19 12:53:52'),
(38, 2, 13, '2025-06-19 13:06:28'),
(39, 2, 43, '2025-06-19 13:06:28'),
(40, 2, 45, '2025-06-19 13:06:28'),
(41, 2, 26, '2025-06-19 13:06:28'),
(42, 2, 27, '2025-06-19 13:06:28'),
(43, 2, 25, '2025-06-19 13:06:28'),
(44, 2, 35, '2025-06-19 13:06:28'),
(45, 2, 52, '2025-06-19 13:06:28'),
(46, 2, 16, '2025-06-19 13:06:28'),
(51, 3, 38, '2025-06-21 06:07:26'),
(52, 3, 15, '2025-06-21 06:07:26'),
(53, 3, 50, '2025-06-21 06:07:26'),
(54, 3, 18, '2025-06-21 06:07:26'),
(55, 3, 11, '2025-06-21 06:07:26'),
(56, 3, 33, '2025-06-21 06:07:26'),
(57, 3, 9, '2025-06-21 06:07:26'),
(58, 3, 49, '2025-06-21 06:07:26'),
(59, 3, 7, '2025-06-21 06:07:26'),
(60, 3, 20, '2025-06-21 06:07:26'),
(61, 3, 29, '2025-06-21 06:07:26'),
(62, 3, 3, '2025-06-21 06:07:26'),
(63, 3, 48, '2025-06-21 06:07:26'),
(64, 3, 39, '2025-06-21 06:07:26');

-- --------------------------------------------------------

--
-- Stand-in structure for view `staff_performance`
-- (See below for the actual view)
--
CREATE TABLE `staff_performance` (
`staff_id` int(11)
,`name` varchar(100)
,`fixed_count` bigint(21)
,`not_fixed_count` bigint(21)
,`avg_days_to_fix` decimal(10,4)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `force_password_change` tinyint(1) DEFAULT 1,
  `branch_id` int(11) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `force_password_change`, `branch_id`, `profile_picture`, `is_active`) VALUES
(1, 'Mikiyas Wondimu', 'mikiyas@lucy.com', '$2y$10$368jd5sxW/7J7WYPMrVLcOhOHdwoMJnoPFG3fmsaOVNS4AaVH.bQG', 'admin', '2025-06-05 09:25:38', 0, 1, NULL, 1),
(2, 'Yehuwalashet Yitagesu', 'yehuwalashet@lucy.com', '$2y$10$9ySHwQPOikPpuF3.3MI2p.py5LGAZU0Kon7g8MOZDJlvaBbwJNTWu', 'staff', '2025-06-05 10:03:11', 0, 1, NULL, 1),
(3, 'Mengistu Ferdie', 'mengistu@lucy.com', '$2y$10$45HUW49UnUoljEdigFx26uU7ive.Ph9P4MWQ4NmaGvhO/5RekZL4a', 'staff', '2025-06-05 10:05:50', 0, 1, NULL, 1),
(4, 'Eleni Zerihun', 'eleni@lucy.com', '$2y$10$q9CbtmLGELYHYwnUrJAoaOiZl238ar3thWdYcj.h3O/d7MQzPj4V.', 'staff', '2025-06-05 10:06:43', 0, 1, 'download (1).jfif', 1),
(5, 'Aaron Tamirat', 'aaron@lucy.com', '$2y$10$Qsr6uzz3Qw4gCOZQRsCcr.n5xuSTar75qnqpB7gIyKF6v0Oo2IPSu', 'staff', '2025-06-05 10:07:45', 0, 1, NULL, 1),
(6, 'User Kazanchis', 'kazanchis@lucy.com', '$2y$10$l5nB1vhu45zUDq3IqfiB/eYJ.vq6tmQ52T9riYkgCBu5y2Ze2f.qS', 'user', '2025-06-05 10:51:25', 0, 2, NULL, 1),
(7, 'User Piassa', 'piassa@lucy.com', '$2y$10$Og5DgpcnieRkE62Q6Da9H.fNnlMkqz8jAzfU00sOl38xzbzR9kiTC', 'user', '2025-06-05 10:52:09', 1, 3, NULL, 1),
(8, 'User Stadium', 'stadium@lucy.com', '$2y$10$QOhOrGX02GC3CkPWxKbpguPJVBQVkg780tUVAs7pVuVDKr8EKE3Yy', 'user', '2025-06-05 10:52:40', 1, 4, NULL, 1),
(9, 'User BoleMedhanialem', 'bolemedhanialem@lucy.com', '$2y$10$XO3Ps6kkqYOEldva93L6euV.I63H0pRwAFmB6w2jR.Alf9wsQgrRW', 'user', '2025-06-05 10:53:27', 0, 5, NULL, 1),
(10, 'User Lideta', 'lideta@lucy.com', '$2y$10$AlIz2xVrC15mbocWxtAhjetKbeM/o02f6QItHScMfn38Cna/6x9Q6', 'user', '2025-06-05 10:53:55', 1, 6, NULL, 1),
(11, 'User DebreBirhan', 'debrebirhan@lucy.com', '$2y$10$DhGDPtpzyQHov5MDqdWH7uMenPjgmYDAclEWNqoub7Zdg2sxNU5FC', 'user', '2025-06-05 10:54:26', 1, 7, NULL, 1),
(12, 'User AddeyAbeba', 'addeyabeba@lucy.com', '$2y$10$HjJ2hr9h3NBCWO5oFK.axOkCC7glboVtoIl1xcXPCkSQV8RE.tXrW', 'user', '2025-06-05 10:55:34', 1, 8, NULL, 1),
(13, 'User Betel', 'betel@lucy.com', '$2y$10$5Ud7fzXBlZYodatL6o/uDe9y8YuJZB3sWTB1cC2nP9zW8gu/U9n4W', 'user', '2025-06-05 10:56:02', 1, 9, NULL, 1),
(14, 'User AddisuGebeya', 'addisugebeya@lucy.com', '$2y$10$zB4Q7wKyYytQUR/mtkX/5uxI2/PkVi7U2jOd.qUpaxVoUb2upGyq6', 'user', '2025-06-05 10:56:28', 0, 10, NULL, 1),
(15, 'User Bahirdar', 'bahirdar@lucy.com', '$2y$10$uQq4M9kvqChSCnK.oJjCMue5einY62kED0pgDKfrYfIjsf7K8PPj6', 'user', '2025-06-05 10:56:55', 1, 11, NULL, 1),
(16, 'User Diredawa', 'diredawa@lucy.com', '$2y$10$xJ1mUEzoK2HWvOAL7vspOO61QzwFKhjjQJqFesUese3/1Joqyhdse', 'user', '2025-06-05 10:57:30', 1, 12, NULL, 1),
(17, 'User Adama', 'adama@lucy.com', '$2y$10$Iwx4MJlXGnv26qqGE/fkC.WXpADghvhob68Y/HxyDXTnTHD267p8a', 'user', '2025-06-05 10:57:51', 0, 13, NULL, 1),
(18, 'User Lemikura', 'lemikura@lucy.com', '$2y$10$J5U61s3YTeylHYwfeiE3yeo6pUDy0HjOU5cS5tWekYlx8nwePlTIW', 'user', '2025-06-05 10:58:16', 1, 14, NULL, 1),
(19, 'User AratKilo', 'aratkilo@lucy.com', '$2y$10$dnY1A3nLl14fIuxikYLe7eRxHNj8OwQywv3P/S.GwR1txcNjYwU1i', 'user', '2025-06-05 10:58:47', 0, 15, NULL, 1),
(20, 'User Yoseph', 'yoseph@lucy.com', '$2y$10$eSAmxiLyhzP/RYs7X3wS2.XLHP0S2nAFY8q5VVAUDqKbwmeMwQvpG', 'user', '2025-06-05 10:59:12', 1, 16, NULL, 1),
(21, 'User Figa', 'figa@lucy.com', '$2y$10$guDL.pG5d1maNnoiT.on7unDBgrDc5bNpKC3JSV1NC4Z7SroV3Sdq', 'user', '2025-06-05 11:00:23', 1, 17, NULL, 1),
(22, 'User Ayertena', 'ayertena@lucy.com', '$2y$10$U497TWmbCemY1iJFRk5hAuRbZOd52I2cGUsJzkd5VfBX0ULlN.KXO', 'user', '2025-06-05 11:00:46', 0, 18, NULL, 1),
(23, 'User Goro', 'goro@lucy.com', '$2y$10$VycO9XWqTKaYD0ThezbYkuzYwwrTa21y9.PrsDQyRYU7Sw9xKlQV6', 'user', '2025-06-07 02:48:42', 0, 19, NULL, 1),
(24, 'User Gulele', 'gulele@lucy.com', '$2y$10$Z9WCtcgkhLCODPlLu9P1jO33S2wNZVS9rEcHhGJo0qxa0uiZyYbGC', 'user', '2025-06-07 02:49:07', 1, 20, NULL, 1),
(25, 'User Lamberet', 'lamberet@lucy.com', '$2y$10$HLgr4S.UVcM6lsGmccEsW.3.Bdu/3f8mfe1olvt8kP.kslUQDvuBy', 'user', '2025-06-07 02:49:45', 0, 21, NULL, 1),
(26, 'User Yerer', 'yerer@lucy.com', '$2y$10$hkbb4CE.WB38GenWuuGEs.lLl7VteRYbEIkfEegkmUmr73BDjhJgm', 'user', '2025-06-07 02:50:21', 1, 22, NULL, 1),
(27, 'User Habtegiorgis', 'habtegiorgis@lucy.com', '$2y$10$vS74sxUqVYEAuycgk9d0Qe/cA4FDQCextk/S4AYtSmDqC85zSPLI.', 'user', '2025-06-07 02:50:53', 1, 23, NULL, 1),
(28, 'User CMC', 'cmc@lucy.com', '$2y$10$uvNWZXANWvBDo50aHqL7HOkNYSuNbTMta/sEH4uUnfRwv3IGKUlJ6', 'user', '2025-06-07 02:51:14', 0, 24, NULL, 1),
(29, 'User Lebu', 'lebu@lucy.com', '$2y$10$Gzbf8qQBntmKa6elMlohAuD.jda8YxK7.ZAVrJq5AUfmqKrbtep62', 'user', '2025-06-07 02:51:27', 0, 25, NULL, 1),
(30, 'User Kality', 'kality@lucy.com', '$2y$10$fFu9YIbcco/2xQb1r6SvMecma8XABYQlo0tt.OPVupK85rtUhGbfy', 'user', '2025-06-07 02:51:43', 1, 26, NULL, 1),
(31, 'User Kera', 'kera@lucy.com', '$2y$10$UMwttdKgVGSm2lCaCs/DzOuWqO4mcKqbtn3qDIVXr.nLFbzpnDSCK', 'user', '2025-06-07 02:51:57', 1, 27, NULL, 1),
(32, 'User Megenagna', 'megenagna@lucy.com', '$2y$10$KUCtIy0mARPPYv3UU0VgLezEHlQwfk.wGtssXDfZ/POgpZCwFoWn.', 'user', '2025-06-07 02:52:21', 1, 28, NULL, 1),
(33, 'User Merkato', 'merkato@lucy.com', '$2y$10$PwpFSLQa6hNSKI0j/0sZ2eARowPKUcQHMVlhbdqXooOATCENevQ/m', 'user', '2025-06-07 02:52:44', 1, 29, NULL, 1),
(34, 'User Bole', 'bole@lucy.com', '$2y$10$kiBvHA.T7SkLG2YT/pkKuuB/epi9eUicxQ6LzaJilw39qAeRpCviK', 'user', '2025-06-07 02:53:15', 1, 30, NULL, 1),
(35, 'User Wolaita', 'wolaita@lucy.com', '$2y$10$oQDWksbcXLjHzv2n1p2gZObJSjYp47mRO5mHOz.mgU8LqOY3Z.z.y', 'user', '2025-06-07 02:53:36', 1, 31, NULL, 1),
(36, 'User Bulbula', 'bulbula@lucy.com', '$2y$10$7mIoCHgRuog77HR7i6tTU.o5KIVpZRvJ3TreE5EFBkG0JMggIBsTW', 'user', '2025-06-07 02:53:52', 1, 32, NULL, 1),
(37, 'User Beklobet', 'beklobet@lucy.com', '$2y$10$q4iyu4igkvq3T5yfqGY.QeVKwa0tdMtw3MqV328yP1w5ErAMPbpL.', 'user', '2025-06-07 02:54:10', 1, 33, NULL, 1),
(38, 'User Hawassa', 'hawassa@lucy.com', '$2y$10$.rVyw1bsoU6i4j.RwwTdre6AyirLRZElDMhTXsCGv1jesYAvhuZT6', 'user', '2025-06-07 02:54:30', 1, 34, NULL, 1),
(39, 'User Mekelle', 'mekelle@lucy.com', '$2y$10$tvvPvhAl0v/v9D5NwuTXn.Ib3Bogp0RUFdCFgtmxVqpCJoyiCZntW', 'user', '2025-06-07 02:54:45', 1, 35, NULL, 1),
(40, 'User Jimma', 'jimma@lucy.com', '$2y$10$f2Ru8zI8rm8EG0pedx2S2.DIL5nibSex.YKFGBEO7Gij.uxwQ31qa', 'user', '2025-06-07 02:55:03', 1, 36, NULL, 1),
(41, 'User Meskelflower', 'meskelflower@lucy.com', '$2y$10$0HvZQorFD6bZtMDadN.5b.3IhDZntCcZj/JA0WJlV.0/o0MttK7m.', 'user', '2025-06-07 02:55:23', 1, 37, NULL, 1),
(42, 'User Alemgena', 'alemgena@lucy.com', '$2y$10$LIhp3f9wV7ycWYX3WTDQkOFLrJlQlBHtnx/U8KLwYjy3HKKZY4aP2', 'user', '2025-06-07 02:55:41', 1, 38, NULL, 1),
(43, 'User Sebategna', 'sebategna@lucy.com', '$2y$10$fh3C4lx.U4V2J1R464VXoOIHswpYrRUm5c2cydpEVSkdzMY/Yi6S2', 'user', '2025-06-07 02:56:00', 1, 39, NULL, 1),
(44, 'User Bulgaria', 'bulgaria@lucy.com', '$2y$10$9yIzC5p8XRCroFEUWJaOAek7IvApFOa7C02lXjIeS1g3./Y0LM9Xm', 'user', '2025-06-07 02:56:32', 0, 40, NULL, 1),
(45, 'User Mizan', 'mizan@lucy.com', '$2y$10$/y7vU7YHZY1V3y0mkqFwzO/PYNAGAbgTxqmBPIydksTM9XUW9gNFS', 'user', '2025-06-07 02:56:56', 1, 41, NULL, 1),
(46, 'User Main', 'main@lucy.com', '$2y$10$uzNg2WbzEXDvaLnuxH6FReLKzwhZA17caH/trhIt9w9Iy104QWkPi', 'user', '2025-06-07 02:58:15', 0, 42, NULL, 1),
(47, 'Aaron Tamirat', 'aaronadmin@lucy.com', '$2y$10$AWs/RjM6hekaQ/LmAvrk4Ozlw5sjtvmq7CeAQpih//7PnpBKt0WZq', 'admin', '2025-06-11 06:06:31', 0, 1, NULL, 1),
(48, 'User Engineering', 'engineering@lucy.com', '$2y$10$oMFedcpBxDlevUj2SIZ1S.1PxrIAF4Zm5bYKWvf3Nc3so5pVt1QrC', 'user', '2025-06-11 09:03:30', 1, 51, NULL, 1),
(49, 'User HR & Logistic', 'hrlg@lucy.com', '$2y$10$xx3jAVgrRsWXKrJiFX.IJ.vZsxGlcCTFkgJXFzufqxxPiwhpcj1SS', 'user', '2025-06-12 08:28:28', 0, 53, NULL, 1),
(50, 'Eleni Zerihun', 'eleniadmin@lucy.com', '$2y$10$g3zfdJ/HknLc33Qxxu.8H.L55XnbGPPv3lAHCmLrJDeXuFEjrxSJ2', 'admin', '2025-06-17 04:42:11', 0, 1, NULL, 1),
(51, 'User CEO', 'ceo@lucy.com', '$2y$10$eWRfyC3SuhN4G1ieNh.WGuGAQb/rVt9PMkoSorz8lgSdHKULJphi2', 'user', '2025-06-17 04:46:13', 0, 43, NULL, 1),
(52, 'user legal', 'legal@lucy.com', '$2y$10$VZCJOoFn1db5W3z5Ka/sSeE6ylF52xkzAFUSfThBcwNHJoKKQpEfC', 'user', '2025-06-17 04:47:31', 0, 44, NULL, 1),
(53, 'user Finance', 'Finance@lucy.com', '$2y$10$y8ua/ZhgNnn2ZO/c2CAlSOMg5UetvhDsRQlOREsLUZO7hV88N716G', 'user', '2025-06-17 04:48:07', 0, 45, NULL, 1),
(54, 'user Marketing', 'Marketin@lucy.com', '$2y$10$kTENK1jVLqLvkiv2Ag2oruBHN5.I0c2nunIThYZPImFWJ7OUEqR7.', 'user', '2025-06-17 04:48:37', 1, 46, NULL, 1),
(55, 'user Risk', 'Risk@lucy.com', '$2y$10$eBhkBuSp/q4xJ4n6fgxuVuN6OXrbl7GlzvUwrSTvWuUIksYKzoIN2', 'user', '2025-06-17 04:49:11', 1, 47, NULL, 1),
(56, 'use Reinsurance', 'Reinsurance@lucy.com', '$2y$10$dP8nszmfRILMpaEAfkuVmugBC5gLYiLRCvahgIGcnqXMKbqv1PEzi', 'user', '2025-06-17 04:49:51', 0, 48, NULL, 1),
(57, 'user claim & recovery', 'clre@lucy.com', '$2y$10$hUuFQVrVvE2my824lN6W9.tDirUNhQ0IrTrBNWT9ykw/.UZ0l05rm', 'user', '2025-06-17 04:53:06', 0, 49, NULL, 1),
(58, 'user Audit', 'Audit@lucy.com', '$2y$10$WGRQw1nPsRDcK5r1OVVriOHzTQtoHi8v6UWoqyxGriVFr9S9rmeDu', 'user', '2025-06-17 04:53:35', 1, 50, NULL, 1),
(59, 'User Operation', 'Operation@lucy.com', '$2y$10$QwixJk4Y5gcMl7Vi7vvWOeBcChgA2qtdHLrY82nVmxA6ePYCmI8PW', 'user', '2025-06-17 04:54:15', 0, 52, NULL, 1),
(60, 'User Ethics', 'Ethics@lucy.com', '$2y$10$/JpV.T4fbV.4MCGr.ldw4.fewTK0XCscgrxFob/0/LLGgwI1I4Ma2', 'user', '2025-06-17 04:54:44', 1, 55, NULL, 1),
(61, 'Mengistu Ferdie Admin', 'mengistuadmin@lucy.com', '$2y$10$/ZUd.FocGefsYmqne9IkCu8W9QW0THpGtYltwNzreB7jJU1D6CdM2', 'admin', '2025-06-17 07:53:35', 0, 1, NULL, 1),
(62, 'Mikiyas Wondimu', 'mikistaff@lucy.com', '$2y$10$EJvTjxSw2y6UAg5o7wx3/uCBEWujSPtnYxxOAJ1zy5eRCDatcGoNe', 'staff', '2025-06-18 04:12:58', 0, 1, NULL, 1),
(63, 'IT STAFF ADMIN', 'staffadmin@lucy.com', '$2y$10$5EJab8uhp5dqZ.LziteURuZyNGwBJv4JVxlAQAs5s.3klekbBElOS', 'admin', '2025-06-18 08:47:40', 0, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Structure for view `incident_counts`
--
DROP TABLE IF EXISTS `incident_counts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `incident_counts`  AS SELECT `b`.`name` AS `branch_name`, `c`.`name` AS `name`, cast(`i`.`created_at` as date) AS `report_date`, count(0) AS `total_incidents` FROM ((`incidents` `i` left join `branches` `b` on(`i`.`branch_id` = `b`.`id`)) left join `kb_categories` `c` on(`i`.`category_id` = `c`.`id`)) GROUP BY `b`.`name`, `c`.`name`, cast(`i`.`created_at` as date) ;

-- --------------------------------------------------------

--
-- Structure for view `incident_fix_times`
--
DROP TABLE IF EXISTS `incident_fix_times`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `incident_fix_times`  AS SELECT `i`.`id` AS `incident_id`, `i`.`title` AS `title`, `i`.`created_at` AS `report_date`, `i`.`fixed_date` AS `fixed_date`, to_days(`i`.`fixed_date`) - to_days(`i`.`created_at`) AS `days_to_fix`, `b`.`name` AS `branch_name`, `c`.`name` AS `name`, `u`.`name` AS `assigned_staff` FROM (((`incidents` `i` left join `branches` `b` on(`i`.`branch_id` = `b`.`id`)) left join `kb_categories` `c` on(`i`.`category_id` = `c`.`id`)) left join `users` `u` on(`i`.`assigned_to` = `u`.`id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `staff_performance`
--
DROP TABLE IF EXISTS `staff_performance`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `staff_performance`  AS SELECT `u`.`id` AS `staff_id`, `u`.`name` AS `name`, count(case when `i`.`status` = 'fixed' then 1 end) AS `fixed_count`, count(case when `i`.`status` <> 'fixed' then 1 end) AS `not_fixed_count`, avg(to_days(`i`.`fixed_date`) - to_days(`i`.`assigned_date`)) AS `avg_days_to_fix` FROM (`users` `u` left join `incidents` `i` on(`i`.`assigned_to` = `u`.`id`)) WHERE `u`.`role` = 'staff' GROUP BY `u`.`id`, `u`.`name` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_id` (`incident_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `submitted_by` (`submitted_by`),
  ADD KEY `fk_incidents_branch` (`branch_id`),
  ADD KEY `incidents_ibfk_3` (`category_id`),
  ADD KEY `fk_incidents_rejected_by` (`rejected_by`);

--
-- Indexes for table `incident_logs`
--
ALTER TABLE `incident_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `incident_id` (`incident_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kb_articles`
--
ALTER TABLE `kb_articles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `kb_categories`
--
ALTER TABLE `kb_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `kb_feedback`
--
ALTER TABLE `kb_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `notifications_ibfk_2` (`related_incident_id`);

--
-- Indexes for table `staff_branch_assignments`
--
ALTER TABLE `staff_branch_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_branch` (`branch_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `incident_logs`
--
ALTER TABLE `incident_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=137;

--
-- AUTO_INCREMENT for table `kb_articles`
--
ALTER TABLE `kb_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kb_categories`
--
ALTER TABLE `kb_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `kb_feedback`
--
ALTER TABLE `kb_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=313;

--
-- AUTO_INCREMENT for table `staff_branch_assignments`
--
ALTER TABLE `staff_branch_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `fk_incidents_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_incidents_rejected_by` FOREIGN KEY (`rejected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `incidents_ibfk_2` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `incidents_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `kb_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `incident_logs`
--
ALTER TABLE `incident_logs`
  ADD CONSTRAINT `incident_logs_ibfk_1` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`),
  ADD CONSTRAINT `incident_logs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `kb_articles`
--
ALTER TABLE `kb_articles`
  ADD CONSTRAINT `kb_articles_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `kb_categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kb_articles_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kb_feedback`
--
ALTER TABLE `kb_feedback`
  ADD CONSTRAINT `kb_feedback_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `kb_articles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kb_feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`related_incident_id`) REFERENCES `incidents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `staff_branch_assignments`
--
ALTER TABLE `staff_branch_assignments`
  ADD CONSTRAINT `staff_branch_assignments_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `staff_branch_assignments_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
