-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 09, 2025 at 02:29 PM
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
(55, 'Ethics', '251114704213', 'ethics@lucyinsuranceet.com', '5th', '2025-06-05 10:28:10', 1),
(56, 'ICT', '+251111547208', 'ict@lucy.com', 'Addis Ababa, 22, Infront of Capital Hotel.', '2025-07-01 08:47:40', 1);

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
  `related_project_id` int(11) DEFAULT NULL,
  `is_seen` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','assigned','need_support','needs redo','fixed','confirmed fixed') DEFAULT 'pending',
  `main_status` enum('under_process','completed','needs_attention') DEFAULT 'under_process',
  `remark` text DEFAULT NULL,
  `deadline_date` date DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `completion_date` timestamp NULL DEFAULT NULL,
  `estimated_cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_comments`
--

CREATE TABLE `project_comments` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `job_position` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `force_password_change` tinyint(1) DEFAULT 1,
  `branch_id` int(11) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `job_position`, `created_at`, `force_password_change`, `branch_id`, `profile_picture`, `is_active`) VALUES
(1, 'Mikiyas Wondimu', 'mikiyas@lucy.com', '$2y$10$368jd5sxW/7J7WYPMrVLcOhOHdwoMJnoPFG3fmsaOVNS4AaVH.bQG', 'admin', NULL, '2025-06-05 09:25:38', 0, 1, NULL, 1),
(2, 'Yehuwalashet Yitagesu', 'yehuwalashet@lucy.com', '$2y$10$9ySHwQPOikPpuF3.3MI2p.py5LGAZU0Kon7g8MOZDJlvaBbwJNTWu', 'staff', NULL, '2025-06-05 10:03:11', 0, 1, NULL, 1),
(3, 'Mengistu Ferdie', 'mengistu@lucy.com', '$2y$10$45HUW49UnUoljEdigFx26uU7ive.Ph9P4MWQ4NmaGvhO/5RekZL4a', 'staff', 'Network & Infrastructure Officer I', '2025-06-05 10:05:50', 0, 1, 'young-woman-2417575_1280.jpg', 1),
(4, 'Eleni Zerihun', 'eleni@lucy.com', '$2y$10$q9CbtmLGELYHYwnUrJAoaOiZl238ar3thWdYcj.h3O/d7MQzPj4V.', 'staff', 'Database & System Administrator I', '2025-06-05 10:06:43', 0, 1, 'download (1).jfif', 1),
(5, 'Aaron Tamirat', 'aaron@lucy.com', '$2y$10$Qsr6uzz3Qw4gCOZQRsCcr.n5xuSTar75qnqpB7gIyKF6v0Oo2IPSu', 'staff', 'Database & System Administrator II', '2025-06-05 10:07:45', 0, 1, NULL, 1),
(6, 'User Kazanchis', 'kazanchis@lucy.com', '$2y$10$l5nB1vhu45zUDq3IqfiB/eYJ.vq6tmQ52T9riYkgCBu5y2Ze2f.qS', 'user', NULL, '2025-06-05 10:51:25', 0, 2, NULL, 1),
(7, 'User Piassa', 'piassa@lucy.com', '$2y$10$Og5DgpcnieRkE62Q6Da9H.fNnlMkqz8jAzfU00sOl38xzbzR9kiTC', 'user', NULL, '2025-06-05 10:52:09', 1, 3, NULL, 1),
(8, 'User Stadium', 'stadium@lucy.com', '$2y$10$QOhOrGX02GC3CkPWxKbpguPJVBQVkg780tUVAs7pVuVDKr8EKE3Yy', 'user', NULL, '2025-06-05 10:52:40', 1, 4, NULL, 1),
(9, 'User BoleMedhanialem', 'bolemedhanialem@lucy.com', '$2y$10$XO3Ps6kkqYOEldva93L6euV.I63H0pRwAFmB6w2jR.Alf9wsQgrRW', 'user', NULL, '2025-06-05 10:53:27', 0, 5, NULL, 1),
(10, 'User Lideta', 'lideta@lucy.com', '$2y$10$AlIz2xVrC15mbocWxtAhjetKbeM/o02f6QItHScMfn38Cna/6x9Q6', 'user', NULL, '2025-06-05 10:53:55', 1, 6, NULL, 1),
(11, 'User DebreBirhan', 'debrebirhan@lucy.com', '$2y$10$DhGDPtpzyQHov5MDqdWH7uMenPjgmYDAclEWNqoub7Zdg2sxNU5FC', 'user', NULL, '2025-06-05 10:54:26', 1, 7, NULL, 1),
(12, 'User AddeyAbeba', 'addeyabeba@lucy.com', '$2y$10$HjJ2hr9h3NBCWO5oFK.axOkCC7glboVtoIl1xcXPCkSQV8RE.tXrW', 'user', NULL, '2025-06-05 10:55:34', 1, 8, NULL, 1),
(13, 'User Betel', 'betel@lucy.com', '$2y$10$DL/V8H9xd2eOrdb6CleJ3uya43ctltr7rSQHM7lkCr6B9uYqSTtCm', 'user', NULL, '2025-06-05 10:56:02', 0, 9, NULL, 1),
(14, 'User AddisuGebeya', 'addisugebeya@lucy.com', '$2y$10$zB4Q7wKyYytQUR/mtkX/5uxI2/PkVi7U2jOd.qUpaxVoUb2upGyq6', 'user', NULL, '2025-06-05 10:56:28', 0, 10, NULL, 1),
(15, 'User Bahirdar', 'bahirdar@lucy.com', '$2y$10$uQq4M9kvqChSCnK.oJjCMue5einY62kED0pgDKfrYfIjsf7K8PPj6', 'user', NULL, '2025-06-05 10:56:55', 1, 11, NULL, 1),
(16, 'User Diredawa', 'diredawa@lucy.com', '$2y$10$xJ1mUEzoK2HWvOAL7vspOO61QzwFKhjjQJqFesUese3/1Joqyhdse', 'user', NULL, '2025-06-05 10:57:30', 1, 12, NULL, 1),
(17, 'User Adama', 'adama@lucy.com', '$2y$10$Iwx4MJlXGnv26qqGE/fkC.WXpADghvhob68Y/HxyDXTnTHD267p8a', 'user', NULL, '2025-06-05 10:57:51', 0, 13, NULL, 1),
(18, 'User Lemikura', 'lemikura@lucy.com', '$2y$10$J5U61s3YTeylHYwfeiE3yeo6pUDy0HjOU5cS5tWekYlx8nwePlTIW', 'user', NULL, '2025-06-05 10:58:16', 1, 14, NULL, 1),
(19, 'User AratKilo', 'aratkilo@lucy.com', '$2y$10$dnY1A3nLl14fIuxikYLe7eRxHNj8OwQywv3P/S.GwR1txcNjYwU1i', 'user', NULL, '2025-06-05 10:58:47', 0, 15, NULL, 1),
(20, 'User Yoseph', 'yoseph@lucy.com', '$2y$10$eSAmxiLyhzP/RYs7X3wS2.XLHP0S2nAFY8q5VVAUDqKbwmeMwQvpG', 'user', NULL, '2025-06-05 10:59:12', 1, 16, NULL, 1),
(21, 'User Figa', 'figa@lucy.com', '$2y$10$guDL.pG5d1maNnoiT.on7unDBgrDc5bNpKC3JSV1NC4Z7SroV3Sdq', 'user', NULL, '2025-06-05 11:00:23', 1, 17, NULL, 1),
(22, 'User Ayertena', 'ayertena@lucy.com', '$2y$10$U497TWmbCemY1iJFRk5hAuRbZOd52I2cGUsJzkd5VfBX0ULlN.KXO', 'user', NULL, '2025-06-05 11:00:46', 0, 18, NULL, 1),
(23, 'User Goro', 'goro@lucy.com', '$2y$10$VycO9XWqTKaYD0ThezbYkuzYwwrTa21y9.PrsDQyRYU7Sw9xKlQV6', 'user', NULL, '2025-06-07 02:48:42', 0, 19, NULL, 1),
(24, 'User Gulele', 'gulele@lucy.com', '$2y$10$Z9WCtcgkhLCODPlLu9P1jO33S2wNZVS9rEcHhGJo0qxa0uiZyYbGC', 'user', NULL, '2025-06-07 02:49:07', 1, 20, NULL, 1),
(25, 'User Lamberet', 'lamberet@lucy.com', '$2y$10$HLgr4S.UVcM6lsGmccEsW.3.Bdu/3f8mfe1olvt8kP.kslUQDvuBy', 'user', NULL, '2025-06-07 02:49:45', 0, 21, NULL, 1),
(26, 'User Yerer', 'yerer@lucy.com', '$2y$10$hkbb4CE.WB38GenWuuGEs.lLl7VteRYbEIkfEegkmUmr73BDjhJgm', 'user', NULL, '2025-06-07 02:50:21', 1, 22, NULL, 1),
(27, 'User Habtegiorgis', 'habtegiorgis@lucy.com', '$2y$10$vS74sxUqVYEAuycgk9d0Qe/cA4FDQCextk/S4AYtSmDqC85zSPLI.', 'user', NULL, '2025-06-07 02:50:53', 1, 23, NULL, 1),
(28, 'User CMC', 'cmc@lucy.com', '$2y$10$uvNWZXANWvBDo50aHqL7HOkNYSuNbTMta/sEH4uUnfRwv3IGKUlJ6', 'user', NULL, '2025-06-07 02:51:14', 0, 24, NULL, 1),
(29, 'User Lebu', 'lebu@lucy.com', '$2y$10$Gzbf8qQBntmKa6elMlohAuD.jda8YxK7.ZAVrJq5AUfmqKrbtep62', 'user', NULL, '2025-06-07 02:51:27', 0, 25, NULL, 1),
(30, 'User Kality', 'kality@lucy.com', '$2y$10$fFu9YIbcco/2xQb1r6SvMecma8XABYQlo0tt.OPVupK85rtUhGbfy', 'user', NULL, '2025-06-07 02:51:43', 1, 26, NULL, 1),
(31, 'User Kera', 'kera@lucy.com', '$2y$10$UMwttdKgVGSm2lCaCs/DzOuWqO4mcKqbtn3qDIVXr.nLFbzpnDSCK', 'user', NULL, '2025-06-07 02:51:57', 1, 27, NULL, 1),
(32, 'User Megenagna', 'megenagna@lucy.com', '$2y$10$3.9Zc18ipKlKfx.9ZudL..qb2.PrAEb5avOqVYNKWruJkIUkqfZ/u', 'user', NULL, '2025-06-07 02:52:21', 0, 28, NULL, 1),
(33, 'User Merkato', 'merkato@lucy.com', '$2y$10$PwpFSLQa6hNSKI0j/0sZ2eARowPKUcQHMVlhbdqXooOATCENevQ/m', 'user', NULL, '2025-06-07 02:52:44', 1, 29, NULL, 1),
(34, 'User Bole', 'bole@lucy.com', '$2y$10$kiBvHA.T7SkLG2YT/pkKuuB/epi9eUicxQ6LzaJilw39qAeRpCviK', 'user', NULL, '2025-06-07 02:53:15', 1, 30, NULL, 1),
(35, 'User Wolaita', 'wolaita@lucy.com', '$2y$10$oQDWksbcXLjHzv2n1p2gZObJSjYp47mRO5mHOz.mgU8LqOY3Z.z.y', 'user', NULL, '2025-06-07 02:53:36', 1, 31, NULL, 1),
(36, 'User Bulbula', 'bulbula@lucy.com', '$2y$10$7mIoCHgRuog77HR7i6tTU.o5KIVpZRvJ3TreE5EFBkG0JMggIBsTW', 'user', NULL, '2025-06-07 02:53:52', 1, 32, NULL, 1),
(37, 'User Beklobet', 'beklobet@lucy.com', '$2y$10$q4iyu4igkvq3T5yfqGY.QeVKwa0tdMtw3MqV328yP1w5ErAMPbpL.', 'user', NULL, '2025-06-07 02:54:10', 1, 33, NULL, 1),
(38, 'User Hawassa', 'hawassa@lucy.com', '$2y$10$.rVyw1bsoU6i4j.RwwTdre6AyirLRZElDMhTXsCGv1jesYAvhuZT6', 'user', NULL, '2025-06-07 02:54:30', 1, 34, NULL, 1),
(39, 'User Mekelle', 'mekelle@lucy.com', '$2y$10$tvvPvhAl0v/v9D5NwuTXn.Ib3Bogp0RUFdCFgtmxVqpCJoyiCZntW', 'user', NULL, '2025-06-07 02:54:45', 1, 35, NULL, 1),
(40, 'User Jimma', 'jimma@lucy.com', '$2y$10$f2Ru8zI8rm8EG0pedx2S2.DIL5nibSex.YKFGBEO7Gij.uxwQ31qa', 'user', NULL, '2025-06-07 02:55:03', 1, 36, NULL, 1),
(41, 'User Meskelflower', 'meskelflower@lucy.com', '$2y$10$0HvZQorFD6bZtMDadN.5b.3IhDZntCcZj/JA0WJlV.0/o0MttK7m.', 'user', NULL, '2025-06-07 02:55:23', 1, 37, NULL, 1),
(42, 'User Alemgena', 'alemgena@lucy.com', '$2y$10$LIhp3f9wV7ycWYX3WTDQkOFLrJlQlBHtnx/U8KLwYjy3HKKZY4aP2', 'user', NULL, '2025-06-07 02:55:41', 1, 38, NULL, 1),
(43, 'User Sebategna', 'sebategna@lucy.com', '$2y$10$fh3C4lx.U4V2J1R464VXoOIHswpYrRUm5c2cydpEVSkdzMY/Yi6S2', 'user', NULL, '2025-06-07 02:56:00', 1, 39, NULL, 1),
(44, 'User Bulgaria', 'bulgaria@lucy.com', '$2y$10$9yIzC5p8XRCroFEUWJaOAek7IvApFOa7C02lXjIeS1g3./Y0LM9Xm', 'user', NULL, '2025-06-07 02:56:32', 0, 40, NULL, 1),
(45, 'User Mizan', 'mizan@lucy.com', '$2y$10$/y7vU7YHZY1V3y0mkqFwzO/PYNAGAbgTxqmBPIydksTM9XUW9gNFS', 'user', NULL, '2025-06-07 02:56:56', 1, 41, NULL, 1),
(46, 'User Main', 'main@lucy.com', '$2y$10$uzNg2WbzEXDvaLnuxH6FReLKzwhZA17caH/trhIt9w9Iy104QWkPi', 'user', NULL, '2025-06-07 02:58:15', 0, 42, NULL, 1),
(47, 'Aaron Tamirat', 'aaronadmin@lucy.com', '$2y$10$AWs/RjM6hekaQ/LmAvrk4Ozlw5sjtvmq7CeAQpih//7PnpBKt0WZq', 'admin', NULL, '2025-06-11 06:06:31', 0, 1, NULL, 1),
(48, 'User Engineering', 'engineering@lucy.com', '$2y$10$oMFedcpBxDlevUj2SIZ1S.1PxrIAF4Zm5bYKWvf3Nc3so5pVt1QrC', 'user', NULL, '2025-06-11 09:03:30', 1, 51, NULL, 1),
(49, 'User HR & Logistic', 'hrlg@lucy.com', '$2y$10$xx3jAVgrRsWXKrJiFX.IJ.vZsxGlcCTFkgJXFzufqxxPiwhpcj1SS', 'user', NULL, '2025-06-12 08:28:28', 0, 53, NULL, 1),
(50, 'Eleni Zerihun', 'eleniadmin@lucy.com', '$2y$10$g3zfdJ/HknLc33Qxxu.8H.L55XnbGPPv3lAHCmLrJDeXuFEjrxSJ2', 'admin', NULL, '2025-06-17 04:42:11', 0, 1, NULL, 1),
(51, 'User CEO', 'ceo@lucy.com', '$2y$10$eWRfyC3SuhN4G1ieNh.WGuGAQb/rVt9PMkoSorz8lgSdHKULJphi2', 'user', NULL, '2025-06-17 04:46:13', 0, 43, NULL, 1),
(52, 'user legal', 'legal@lucy.com', '$2y$10$VZCJOoFn1db5W3z5Ka/sSeE6ylF52xkzAFUSfThBcwNHJoKKQpEfC', 'user', NULL, '2025-06-17 04:47:31', 0, 44, NULL, 1),
(53, 'user Finance', 'Finance@lucy.com', '$2y$10$y8ua/ZhgNnn2ZO/c2CAlSOMg5UetvhDsRQlOREsLUZO7hV88N716G', 'user', NULL, '2025-06-17 04:48:07', 0, 45, NULL, 1),
(54, 'user Marketing', 'Marketin@lucy.com', '$2y$10$kTENK1jVLqLvkiv2Ag2oruBHN5.I0c2nunIThYZPImFWJ7OUEqR7.', 'user', NULL, '2025-06-17 04:48:37', 1, 46, NULL, 1),
(55, 'user Risk', 'Risk@lucy.com', '$2y$10$eBhkBuSp/q4xJ4n6fgxuVuN6OXrbl7GlzvUwrSTvWuUIksYKzoIN2', 'user', NULL, '2025-06-17 04:49:11', 1, 47, NULL, 1),
(56, 'use Reinsurance', 'Reinsurance@lucy.com', '$2y$10$dP8nszmfRILMpaEAfkuVmugBC5gLYiLRCvahgIGcnqXMKbqv1PEzi', 'user', NULL, '2025-06-17 04:49:51', 0, 48, NULL, 1),
(57, 'user claim & recovery', 'clre@lucy.com', '$2y$10$hUuFQVrVvE2my824lN6W9.tDirUNhQ0IrTrBNWT9ykw/.UZ0l05rm', 'user', NULL, '2025-06-17 04:53:06', 0, 49, NULL, 1),
(58, 'user Audit', 'Audit@lucy.com', '$2y$10$WGRQw1nPsRDcK5r1OVVriOHzTQtoHi8v6UWoqyxGriVFr9S9rmeDu', 'user', NULL, '2025-06-17 04:53:35', 1, 50, NULL, 1),
(59, 'User Operation', 'Operation@lucy.com', '$2y$10$QwixJk4Y5gcMl7Vi7vvWOeBcChgA2qtdHLrY82nVmxA6ePYCmI8PW', 'user', NULL, '2025-06-17 04:54:15', 0, 52, NULL, 1),
(60, 'User Ethics', 'Ethics@lucy.com', '$2y$10$/JpV.T4fbV.4MCGr.ldw4.fewTK0XCscgrxFob/0/LLGgwI1I4Ma2', 'user', NULL, '2025-06-17 04:54:44', 1, 55, NULL, 1),
(61, 'Mengistu Ferdie Admin', 'mengistuadmin@lucy.com', '$2y$10$/ZUd.FocGefsYmqne9IkCu8W9QW0THpGtYltwNzreB7jJU1D6CdM2', 'admin', NULL, '2025-06-17 07:53:35', 0, 1, NULL, 1),
(62, 'Mikiyas Wondimu', 'mikistaff@lucy.com', '$2y$10$EJvTjxSw2y6UAg5o7wx3/uCBEWujSPtnYxxOAJ1zy5eRCDatcGoNe', 'staff', NULL, '2025-06-18 04:12:58', 0, 1, NULL, 1),
(63, 'IT STAFF ADMIN', 'staffadmin@lucy.com', '$2y$10$5EJab8uhp5dqZ.LziteURuZyNGwBJv4JVxlAQAs5s.3klekbBElOS', 'admin', NULL, '2025-06-18 08:47:40', 0, 1, NULL, 1),
(64, 'User ICT', 'ict@lucy.com', '$2y$10$6iiOuUOWo4BvkDtLoIsA0eYJetT/oVzO28T6tW7wt3hPZPrnwzN7u', 'user', NULL, '2025-07-01 08:48:19', 0, 56, NULL, 1);

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
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `project_comments`
--
ALTER TABLE `project_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `user_id` (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `incident_logs`
--
ALTER TABLE `incident_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kb_articles`
--
ALTER TABLE `kb_articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kb_categories`
--
ALTER TABLE `kb_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kb_feedback`
--
ALTER TABLE `kb_feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_comments`
--
ALTER TABLE `project_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff_branch_assignments`
--
ALTER TABLE `staff_branch_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

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
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_comments`
--
ALTER TABLE `project_comments`
  ADD CONSTRAINT `project_comments_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
