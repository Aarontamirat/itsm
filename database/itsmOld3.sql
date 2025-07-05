-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 04, 2025 at 02:43 PM
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
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`, `location`, `created_at`) VALUES
(2, 'Main Branch', 'Addis Ababa, 22, In front of Capital Hotel.', '2025-05-22 08:55:07'),
(3, 'Head Office', 'Addis Ababa, 22, In front of Capital Hotel.', '2025-05-22 09:06:53'),
(4, '4 Kilo Branch', 'Addis Ababa, 4 Kilo , In front of Ethiopian Press Agency.', '2025-05-22 09:08:11'),
(5, 'Kazanchis Branch', 'Addis Ababa, Kazanchis, Around old Dashen Bank.', '2025-05-22 10:37:27');

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
(2, 5, NULL, '../uploads/default_avatar.png', '2025-05-27 14:18:23'),
(3, 8, NULL, '../uploads/1748929395_4934501_2537405.jpg', '2025-06-03 05:43:15'),
(4, 10, NULL, '../uploads/1748960036_output-onlinepngtools.png', '2025-06-03 14:13:56'),
(5, 11, NULL, '../uploads/1749026761_error2.jpg', '2025-06-04 08:46:01');

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','assigned','not fixed','fixed','rejected') DEFAULT 'pending',
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `assigned_to` int(11) DEFAULT NULL,
  `submitted_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `branch_id` int(11) DEFAULT NULL,
  `assigned_date` datetime DEFAULT NULL,
  `fixed_date` datetime DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `saved_amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`id`, `title`, `description`, `status`, `priority`, `assigned_to`, `submitted_by`, `created_at`, `branch_id`, `assigned_date`, `fixed_date`, `category_id`, `saved_amount`) VALUES
(1, 'Internet Issue', 'Internet is not working.', 'assigned', 'medium', 8, 9, '2025-05-26 08:04:58', 5, '2025-05-26 14:39:57', NULL, NULL, NULL),
(2, 'Printer error', 'Printer shows the following error code: 0xE0012', 'assigned', 'low', 8, 9, '2025-05-26 08:05:26', 5, '2025-05-27 16:31:43', NULL, NULL, NULL),
(3, 'Internet access issue', 'Can not connect to the internet due to some issue that I have no idea about.', 'fixed', 'low', 8, 9, '2025-05-26 13:48:02', 5, '2025-05-27 17:09:14', NULL, 4, NULL),
(4, 'Computer Error', 'Computer is not starting up, it doesn\'t power up at all.', 'pending', 'medium', 8, 9, '2025-05-27 11:03:40', 5, '2025-05-27 16:26:58', NULL, 4, NULL),
(5, 'Printer Dead', 'JKDfbasid dfh vijadv ijsn vjnv jnv ijdfvn', 'fixed', 'medium', 3, 2, '2025-05-27 14:18:23', 4, '2025-05-28 08:47:09', '2025-06-02 17:16:49', 1, 3200.00),
(6, 'Lucy Live System Login Issue', 'I tried logging in into lucy live system but I couldn\'t login as the login page froze.', 'assigned', 'low', 3, 9, '2025-05-30 11:39:00', 5, '2025-06-03 13:07:06', NULL, 4, NULL),
(8, 'Scanner Issue', 'Our scanner has stopped functioning and it has impacted our work performance, we need an immediate solution to keep working.', 'assigned', 'high', 3, 9, '2025-06-03 05:43:15', 5, '2025-06-03 08:44:53', NULL, 5, NULL),
(9, 'Scanner Issue2', 'Our scanner2 has stopped functioning and it has impacted our work performance, we need an immediate solution to keep working.', 'assigned', 'medium', 3, 2, '2025-06-03 05:51:48', 4, '2025-06-03 08:53:14', NULL, 5, NULL),
(10, 'VPN Router Issue', 'The VPN router is not starting up, there used to be lights on it but now it doesn\'t as a consequence we don\'t have an internet access as a branch.', 'fixed', 'high', 3, 9, '2025-06-03 14:13:56', 5, '2025-06-04 11:15:21', '2025-06-04 13:58:23', 2, 12000.00),
(11, 'Display Port Issue', 'The display port cable has stopped functioning in our branch we require a replacement.', 'assigned', 'medium', 10, 2, '2025-06-04 08:46:01', 4, '2025-06-04 12:24:16', '2025-06-04 11:54:57', 7, 0.00);

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
(1, 2, 1, 'Assigned to IT Staff (User ID: 3)', '2025-05-26 08:07:29'),
(2, 1, 1, 'Assigned to IT Staff (User ID: 8)', '2025-05-26 08:09:28'),
(3, NULL, 1, 'Reset password for user ID 8', '2025-05-26 08:36:56'),
(4, 2, 1, 'Assigned to IT Staff (User ID: 8)', '2025-05-26 11:39:53'),
(5, 1, 1, 'Assigned to IT Staff (User ID: 8)', '2025-05-26 11:39:57'),
(6, 3, 9, 'Incident reported by User ID: 9', '2025-05-26 13:48:02'),
(7, 3, 1, 'Assigned to IT Staff (User ID: 8)', '2025-05-26 14:08:24'),
(8, 3, 1, 'Assigned to IT Staff (Eleni)', '2025-05-26 14:30:11'),
(9, 3, 8, 'Status changed to Fixed', '2025-05-26 14:47:26'),
(10, 3, 1, 'Status updated to \'assigned\'', '2025-05-27 06:10:09'),
(11, 3, 1, 'Assigned to IT Staff (Mengistu)', '2025-05-27 06:12:24'),
(12, 3, 3, 'Status changed to Fixed', '2025-05-27 06:16:37'),
(13, 3, 1, 'Status updated to \'not fixed\'', '2025-05-27 06:30:56'),
(14, 3, 1, 'Status updated to \'pending\'', '2025-05-27 06:37:32'),
(15, 3, 1, 'Status updated to \'not fixed\'', '2025-05-27 06:39:50'),
(16, 3, 1, 'Status updated to \'pending\'', '2025-05-27 06:43:02'),
(17, 3, 3, 'Status changed to Fixed', '2025-05-27 06:44:36'),
(18, 4, 9, 'Incident reported by User ID: 9', '2025-05-27 11:03:40'),
(19, 4, 1, 'Assigned to IT Staff (User ID: 3)', '2025-05-27 13:26:20'),
(20, 4, 1, 'Assigned to IT Staff (Eleni)', '2025-05-27 13:26:58'),
(21, 2, 1, 'Assigned to IT Staff (Eleni)', '2025-05-27 13:31:44'),
(22, NULL, 1, 'Reset password for user ID 8', '2025-05-27 14:07:30'),
(23, 3, 1, 'Assigned to IT Staff (Eleni)', '2025-05-27 14:09:14'),
(24, 3, 8, 'Status changed to Fixed', '2025-05-27 14:12:30'),
(25, NULL, 6, 'Reset password for user ID 1', '2025-05-27 14:13:47'),
(26, 5, 2, 'Incident reported by User ID: 2', '2025-05-27 14:18:23'),
(27, 5, 1, 'Assigned to IT Staff (User ID: 3)', '2025-05-28 05:47:09'),
(28, 4, 8, 'Status changed to Fixed', '2025-05-28 10:37:57'),
(29, 5, 3, 'Status changed to Fixed', '2025-05-28 10:38:42'),
(30, 5, 3, 'Status changed to pending', '2025-05-28 10:40:41'),
(31, 5, 3, 'Status changed to not fixed', '2025-05-28 10:40:56'),
(32, 5, 3, 'Status changed to fixed', '2025-05-28 10:41:03'),
(33, 4, 8, 'Status changed to fixed', '2025-05-28 10:41:59'),
(34, 4, 8, 'Status changed to pending', '2025-05-28 10:42:25'),
(35, 4, 8, 'Status changed to fixed', '2025-05-28 10:47:01'),
(36, 4, 8, 'Status changed to pending', '2025-05-28 10:47:13'),
(37, 4, 8, 'Status changed to fixed', '2025-05-28 10:48:21'),
(38, 4, 8, 'Status changed to fixed', '2025-05-28 10:48:28'),
(39, 4, 8, 'Status changed to pending', '2025-05-28 10:48:39'),
(40, 4, 8, 'Status changed to fixed', '2025-05-28 10:55:00'),
(41, 4, 8, 'Status changed to pending', '2025-05-28 10:55:07'),
(42, 4, 8, 'Status changed to not fixed', '2025-05-28 10:55:23'),
(43, 4, 8, 'Status changed to pending', '2025-05-28 10:56:18'),
(44, 4, 8, 'Status changed to fixed', '2025-05-28 10:56:25'),
(45, 4, 8, 'Status changed to pending', '2025-05-28 10:56:34'),
(46, 4, 8, 'Status changed to fixed', '2025-05-28 11:40:53'),
(47, 4, 8, 'Status changed to pending', '2025-05-28 11:41:26'),
(48, 4, 8, 'Status changed to fixed', '2025-05-28 11:41:36'),
(49, 4, 8, 'Status changed to fixed', '2025-05-28 11:41:44'),
(50, 4, 8, 'Status changed to pending', '2025-05-28 11:41:50'),
(51, 4, 8, 'Status changed to fixed', '2025-05-28 11:42:01'),
(52, 4, 8, 'Status changed to not fixed', '2025-05-28 11:42:08'),
(53, 4, 8, 'Status changed to fixed', '2025-05-28 11:42:39'),
(54, NULL, 1, 'Reset password for user ID 8', '2025-05-30 10:54:24'),
(55, NULL, 1, 'Reset password for user ID 3', '2025-05-30 10:54:31'),
(56, NULL, 1, 'Reset password for user ID 6', '2025-05-30 10:54:40'),
(57, NULL, 1, 'Reset password for user ID 2', '2025-05-30 10:54:48'),
(58, NULL, 1, 'Reset password for user ID 9', '2025-05-30 10:54:52'),
(59, NULL, 1, 'Reset password for user ID 1', '2025-05-30 10:54:57'),
(60, NULL, 1, 'Reset password for user ID 1', '2025-05-30 11:09:29'),
(61, 6, 9, 'Incident reported by User ID: 9', '2025-05-30 11:39:00'),
(62, 4, 8, 'Status changed to pending', '2025-06-02 13:48:53'),
(63, 4, 8, 'Status changed to pending', '2025-06-02 13:51:49'),
(64, 4, 8, 'Status changed to fixed', '2025-06-02 13:53:16'),
(65, 4, 8, 'Status changed to pending', '2025-06-02 13:57:07'),
(66, 4, 8, 'Status changed to fixed', '2025-06-02 13:57:17'),
(67, 4, 8, 'Status changed to not fixed', '2025-06-02 13:57:26'),
(68, NULL, 6, 'Reset password for user ID 3', '2025-06-02 14:04:10'),
(69, NULL, 1, 'Reset password for user ID 3', '2025-06-02 14:05:05'),
(70, NULL, 1, 'Reset password for user ID 1', '2025-06-02 14:09:01'),
(71, NULL, 1, 'Reset password for user ID 3', '2025-06-02 14:09:43'),
(72, NULL, 1, 'Reset password for user ID 9', '2025-06-02 14:09:50'),
(73, 5, 3, 'Status changed to not fixed', '2025-06-02 14:12:52'),
(74, 5, 3, 'Status changed to fixed', '2025-06-02 14:14:01'),
(75, 5, 3, 'Status changed to fixed', '2025-06-02 14:16:49'),
(76, 8, 9, 'Incident reported by User ID: 9', '2025-06-03 05:43:15'),
(77, 8, 1, 'Assigned to IT Staff (User ID: 3)', '2025-06-03 05:44:53'),
(78, 9, 2, 'Incident reported by User ID: 2', '2025-06-03 05:51:48'),
(79, 9, 6, 'Assigned to IT Staff (User ID: 8)', '2025-06-03 05:52:13'),
(80, 9, 1, 'Assigned to IT Staff (Mengistu)', '2025-06-03 05:53:14'),
(81, 6, 1, 'Status updated to \'assigned\'', '2025-06-03 09:13:56'),
(82, 6, 1, 'Status updated to \'pending\'', '2025-06-03 09:14:00'),
(83, 6, 1, 'Assigned to IT Staff (User ID: Mengistu)', '2025-06-03 10:07:06'),
(84, 4, 8, 'Status changed to fixed', '2025-06-03 12:30:39'),
(85, 4, 8, 'Status changed to pending', '2025-06-03 12:30:51'),
(86, 4, 8, 'Category changed to Access & Permissions', '2025-06-03 13:21:46'),
(87, 4, 8, 'Category changed to Computer', '2025-06-03 13:22:23'),
(88, 4, 8, 'Category changed to Access & Permissions', '2025-06-03 13:37:30'),
(89, 4, 8, 'Category changed to Computer', '2025-06-03 13:37:52'),
(90, 10, 9, 'Incident reported by User ID: 9', '2025-06-03 14:13:56'),
(91, 10, 1, 'Assigned to IT Staff (User ID: Yehulashet)', '2025-06-04 08:08:49'),
(92, NULL, 1, 'Reset password for user ID 10', '2025-06-04 08:09:48'),
(93, 10, 10, 'Status changed to fixed', '2025-06-04 08:12:06'),
(94, 10, 1, 'Assigned to IT Staff (Mengistu)', '2025-06-04 08:15:21'),
(95, 10, 3, 'Status changed to pending', '2025-06-04 08:25:52'),
(96, 10, 3, 'Status changed to fixed', '2025-06-04 08:26:17'),
(97, 10, 3, 'Status changed to pending', '2025-06-04 08:28:34'),
(98, 11, 2, 'Incident reported by User ID: 2', '2025-06-04 08:46:01'),
(99, 11, 1, 'Assigned to IT Staff (User ID: Yehulashet)', '2025-06-04 08:46:22'),
(100, 11, 10, 'Status changed to fixed', '2025-06-04 08:54:57'),
(101, 11, 1, 'Assigned to IT Staff (Mengistu)', '2025-06-04 09:13:13'),
(102, 11, 1, 'Assigned to IT Staff (Mengistu)', '2025-06-04 09:24:00'),
(103, 11, 1, 'Assigned to IT Staff (Yehulashet)', '2025-06-04 09:24:16'),
(104, 10, 3, 'Status changed to fixed', '2025-06-04 10:58:23');

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

--
-- Dumping data for table `kb_articles`
--

INSERT INTO `kb_articles` (`id`, `title`, `content`, `created_by`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'How to reset your password?', 'Go to the login page and click on \"Forgot Password\".', NULL, 1, '2025-05-29 07:36:11', '2025-05-29 07:36:11'),
(2, 'Internet Connection', 'If you have an internet connection issue follow the following steps to check then reach us only if it doesn\'t work.\r\n1. Check the cable is correctly plugged in the right port, both on the computer and the router/switch.\r\n2. check the network status on the network icon of the computer in the taskbar, usually found at the right bottom of the screen.\r\n3. unplug the network cable and plug it in again.\r\n4. restart your computer.\r\n5. reach us.', NULL, 2, '2025-06-03 11:02:16', '2025-06-03 11:02:16');

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
(1, 'Access & Permissions', '2025-05-29 07:35:17'),
(2, 'Network Issues', '2025-05-29 07:35:42'),
(3, 'Printer', '2025-06-03 05:34:38'),
(4, 'Computer', '2025-06-03 05:34:55'),
(5, 'Scanner', '2025-06-03 05:35:04'),
(6, 'Login & Authentication', '2025-06-03 11:48:08'),
(7, 'Hardware', '2025-06-04 08:45:35');

-- --------------------------------------------------------

--
-- Table structure for table `kb_feedback`
--

CREATE TABLE `kb_feedback` (
  `id` int(11) NOT NULL,
  `article_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `feedback_type` enum('helpful','not_helpful') NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kb_feedback`
--

INSERT INTO `kb_feedback` (`id`, `article_id`, `user_id`, `feedback_type`, `comment`, `created_at`) VALUES
(1, 1, 1, 'helpful', NULL, '2025-05-29 07:39:23'),
(2, 2, 1, 'helpful', NULL, '2025-06-03 11:02:56'),
(3, 2, 9, 'helpful', NULL, '2025-06-04 06:39:04'),
(4, 1, 9, 'helpful', NULL, '2025-06-04 06:52:58');

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
(1, 1, 'New incident reported', 3, 1, '2025-05-26 13:48:02'),
(2, 6, 'New incident reported', 3, 1, '2025-05-26 13:48:02'),
(3, 8, 'You have been assigned to an incident', 3, 1, '2025-05-26 14:08:24'),
(4, 9, 'Your incident (ID: 3) has been marked as Fixed.', NULL, 1, '2025-05-26 14:47:26'),
(5, 3, 'You have been assigned to an incident', 3, 1, '2025-05-27 06:12:24'),
(6, 9, 'Your incident (ID: 3) has been marked as Fixed.', NULL, 1, '2025-05-27 06:16:37'),
(7, 9, 'Your incident (ID: 3) has been marked as pending.', NULL, 1, '2025-05-27 06:37:32'),
(8, 9, 'Your incident (ID: 3) has been marked as not fixed.', NULL, 1, '2025-05-27 06:39:50'),
(9, 9, 'Your incident (ID: 3) has been marked as pending.', 3, 1, '2025-05-27 06:43:02'),
(10, 9, 'Your incident (ID: 3) has been marked as Fixed.', 3, 1, '2025-05-27 06:44:36'),
(11, 1, 'New incident reported', 4, 1, '2025-05-27 11:03:40'),
(12, 6, 'New incident reported', 4, 1, '2025-05-27 11:03:40'),
(13, 3, 'You have been assigned to an incident', 4, 1, '2025-05-27 13:26:20'),
(14, 8, 'You have been assigned to an incident', 4, 1, '2025-05-27 13:26:58'),
(15, 8, 'You have been assigned to an incident', 2, 1, '2025-05-27 13:31:44'),
(16, 8, 'You have been assigned to an incident', 3, 1, '2025-05-27 14:09:14'),
(17, 9, 'Your incident (ID: 3) has been marked as Fixed.', 3, 1, '2025-05-27 14:12:30'),
(18, 1, 'New incident reported', 5, 1, '2025-05-27 14:18:23'),
(19, 6, 'New incident reported', 5, 1, '2025-05-27 14:18:23'),
(20, 3, 'You have been assigned to an incident', 5, 1, '2025-05-28 05:47:09'),
(21, 9, 'Your incident (ID: 4) has been marked as Fixed.', 4, 1, '2025-05-28 10:37:57'),
(22, 2, 'Your incident (ID: 5) has been marked as Fixed.', 5, 1, '2025-05-28 10:38:42'),
(23, 2, 'Your incident (ID: 5) has been marked as pending.', 5, 1, '2025-05-28 10:40:41'),
(24, 2, 'Your incident (ID: 5) has been marked as not fixed.', 5, 1, '2025-05-28 10:40:56'),
(25, 2, 'Your incident (ID: 5) has been marked as fixed.', 5, 1, '2025-05-28 10:41:03'),
(26, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 10:41:59'),
(27, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-05-28 10:42:25'),
(28, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 10:47:01'),
(29, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-05-28 10:47:13'),
(30, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 10:48:21'),
(31, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 10:48:28'),
(32, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-05-28 10:48:39'),
(33, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 10:55:00'),
(34, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-05-28 10:55:07'),
(35, 9, 'Your incident (ID: 4) has been marked as not fixed.', 4, 1, '2025-05-28 10:55:23'),
(36, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-05-28 10:56:18'),
(37, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 10:56:25'),
(38, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-05-28 10:56:34'),
(39, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 11:40:53'),
(40, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-05-28 11:41:26'),
(41, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 11:41:36'),
(42, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 11:41:44'),
(43, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-05-28 11:41:50'),
(44, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 11:42:01'),
(45, 9, 'Your incident (ID: 4) has been marked as not fixed.', 4, 1, '2025-05-28 11:42:08'),
(46, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-05-28 11:42:39'),
(47, 1, 'New incident reported', 6, 1, '2025-05-30 11:39:00'),
(48, 6, 'New incident reported', 6, 1, '2025-05-30 11:39:00'),
(49, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-06-02 13:48:53'),
(50, 9, 'Your incident (ID: 4) has been marked as pending.', 4, 1, '2025-06-02 13:51:49'),
(51, 9, 'Your incident (ID: 4) has been marked as fixed.', 4, 1, '2025-06-02 13:53:16'),
(52, 1, 'New incident reported', 8, 1, '2025-06-03 05:43:15'),
(53, 6, 'New incident reported', 8, 1, '2025-06-03 05:43:15'),
(54, 3, 'You have been assigned to an incident', 8, 1, '2025-06-03 05:44:53'),
(55, 1, 'New incident reported', 9, 1, '2025-06-03 05:51:48'),
(56, 6, 'New incident reported', 9, 1, '2025-06-03 05:51:48'),
(57, 8, 'You have been assigned to an incident', 9, 1, '2025-06-03 05:52:13'),
(58, 3, 'You have been assigned to an incident', 9, 1, '2025-06-03 05:53:14'),
(59, 9, 'Your incident (ID: 6) has been marked as assigned.', 6, 1, '2025-06-03 09:13:56'),
(60, 9, 'Your incident (ID: 6) has been marked as pending.', 6, 1, '2025-06-03 09:14:00'),
(61, 3, 'You have been assigned to an incident', 6, 1, '2025-06-03 10:07:06'),
(62, 1, 'New incident reported', 10, 1, '2025-06-03 14:13:56'),
(63, 6, 'New incident reported', 10, 0, '2025-06-03 14:13:56'),
(64, 10, 'You have been assigned to an incident', 10, 1, '2025-06-04 08:08:49'),
(65, 3, 'You have been assigned to an incident', 10, 1, '2025-06-04 08:15:21'),
(66, 9, 'Your incident (ID: 10) has been marked as fixed.', 10, 1, '2025-06-04 08:26:17'),
(67, 9, 'Your incident (ID: 10) has been marked as pending.', 10, 1, '2025-06-04 08:28:34'),
(68, 1, 'New incident reported', 11, 1, '2025-06-04 08:46:01'),
(69, 6, 'New incident reported', 11, 1, '2025-06-04 08:46:01'),
(70, 10, 'You have been assigned to an incident', 11, 1, '2025-06-04 08:46:22'),
(71, 2, 'Your incident (ID: 11) has been marked as fixed.', 11, 1, '2025-06-04 08:54:57'),
(72, 3, 'You have been assigned to an incident', 11, 1, '2025-06-04 09:13:13'),
(73, 3, 'You have been assigned to an incident', 11, 1, '2025-06-04 09:24:00'),
(74, 10, 'You have been assigned to an incident', 11, 0, '2025-06-04 09:24:16'),
(75, 9, 'Your incident (ID: 10) has been marked as fixed.', 10, 0, '2025-06-04 10:58:23');

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
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `force_password_change`, `branch_id`, `profile_picture`) VALUES
(1, 'Aamon', 'aamon@example.com', '$2y$10$G4l9Z9712AdCNf8Wj.dOQOh7Sv.iKKu05xLBlbIJVcLhjTCESuaKu', 'admin', '2025-05-11 15:25:35', 0, 3, 'ab09a454aae866a7086e12669fd74e75.jpg'),
(2, 'Simon', 'simon@example.com', '$2y$10$HCl3ox5so7tHqYp8nmjL2uDBh/0SSzCuH.A46KBonrujkfN4jmS/u', 'user', '2025-05-11 15:30:52', 0, 4, NULL),
(3, 'Mengistu', 'mengie@example.com', '$2y$10$UKy87nkuJFLX19xi4H/UrO9R8TSZyr83Zyewlm2j1Y.n2UvgN0WtG', 'staff', '2025-05-11 15:31:25', 0, 3, 'photo_2025-05-24_11-31-46.jpg'),
(6, 'Mikiyas', 'miki@example.com', '$2y$10$jxn86HXCDfMq9yY3S7utxOvGNT7FjdkTptPjhzPQ0FFKdWvMUYqSq', 'admin', '2025-05-22 09:57:51', 0, 3, 'male2.jpg'),
(8, 'Eleni', 'eleni@example.com', '$2y$10$y3qgtvdTyo4jk1ZElQkUXOKOTZL5eCVndbwJsOq2nPWWfsJqe.jGy', 'staff', '2025-05-22 10:40:33', 0, 3, 'female2.jpg'),
(9, 'Shewit', 'shewit@example.com', '$2y$10$0kZthQODoqE.QXzzto/4Tu8Oqt9tAJkQ3kdl2tY63Vhi3BzpswWX6', 'user', '2025-05-24 07:51:49', 0, 5, 'female3.jpg'),
(10, 'Yehulashet', 'yehualashet@example.com', '$2y$10$kh2DnBWiUCqDSFOWgUdUeOZVsfzcHUGLzyiWi4nNKY.F97ef5z5qW', 'staff', '2025-05-30 10:54:06', 0, 3, NULL);

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
  ADD KEY `incidents_ibfk_3` (`category_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `incident_logs`
--
ALTER TABLE `incident_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `kb_articles`
--
ALTER TABLE `kb_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `kb_categories`
--
ALTER TABLE `kb_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kb_feedback`
--
ALTER TABLE `kb_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `files`
--
ALTER TABLE `files`
  ADD CONSTRAINT `files_ibfk_1` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`id`);

--
-- Constraints for table `incidents`
--
ALTER TABLE `incidents`
  ADD CONSTRAINT `fk_incidents_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL,
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
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
