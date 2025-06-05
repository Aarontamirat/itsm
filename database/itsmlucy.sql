-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2025 at 04:02 PM
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
(1, 'Head Office', 'In front of Capital Hotel, 22, Addis Ababa.', '2025-06-05 12:59:13'),
(2, 'Kazanchis Branch', 'Kazanchis', '2025-06-05 13:09:01'),
(3, 'Piassa Branch', 'Piassa', '2025-06-05 13:09:34'),
(4, 'Stadium Branch', 'Stadium', '2025-06-05 13:09:50'),
(5, 'Bole Medhanialem Branch', 'Bole Medhanialem', '2025-06-05 13:10:16'),
(6, 'Lideta Branch', 'Lideta', '2025-06-05 13:10:33'),
(7, 'Debre Birhan Branch', 'Debre Birhan', '2025-06-05 13:11:00'),
(8, 'Addey Abeba Branch', 'Addey Abeba', '2025-06-05 13:11:31'),
(9, 'Betel Branch', 'Betel', '2025-06-05 13:11:48'),
(10, 'Addisu Gebeya Branch', 'Addisu Gebeya', '2025-06-05 13:12:27'),
(11, 'BahirDar Branch', 'BahirDar', '2025-06-05 13:12:51'),
(12, 'Diredawa Branch', 'Diredawa', '2025-06-05 13:13:14'),
(13, 'Adama Branch', 'Adama', '2025-06-05 13:13:28'),
(14, 'Lemikura Branch', 'Lemikura', '2025-06-05 13:13:45'),
(15, 'Arat Kilo Branch', 'Arat Kilo', '2025-06-05 13:14:06'),
(16, 'Yoseph Branch', 'Yoseph', '2025-06-05 13:14:24'),
(17, 'Figa Branch', 'Figa', '2025-06-05 13:14:39'),
(18, 'Ayertena Branch', 'Ayertena', '2025-06-05 13:14:54'),
(19, 'Goro Branch', 'Goro', '2025-06-05 13:15:01'),
(20, 'Gulele Branch', 'Gulele', '2025-06-05 13:15:17'),
(21, 'Lamberet Branch', 'Lamberet', '2025-06-05 13:15:38'),
(22, 'Yerer Branch', 'Yerer', '2025-06-05 13:15:51'),
(23, 'Habte Giorgis Branch', 'Habte Giorgis', '2025-06-05 13:16:07'),
(24, 'CMC Branch', 'CMC', '2025-06-05 13:16:17'),
(25, 'Lebu Branch', 'Lebu', '2025-06-05 13:16:24'),
(26, 'Kality Branch', 'Kality', '2025-06-05 13:16:33'),
(27, 'Kera Branch', 'Kera', '2025-06-05 13:16:42'),
(28, 'Megenagna Branch', 'Megenagna', '2025-06-05 13:17:06'),
(29, 'Merkato Branch', 'Merkato', '2025-06-05 13:17:17'),
(30, 'Bole Branch', 'Bole', '2025-06-05 13:18:08'),
(31, 'Wolaita Branch', 'Wolaita', '2025-06-05 13:18:27'),
(32, 'Bulbula Branch', 'Bulbula', '2025-06-05 13:18:44'),
(33, 'Beklobet Branch', 'Beklobet', '2025-06-05 13:18:58'),
(34, 'Hawassa Branch', 'Hawassa', '2025-06-05 13:19:09'),
(35, 'Mekelle Branch', 'Mekelle', '2025-06-05 13:19:24'),
(36, 'Jimma Branch', 'Jimma', '2025-06-05 13:19:37'),
(37, 'Meskel Flower Branch', 'Meskel Flower', '2025-06-05 13:19:54'),
(38, 'Alemgena Branch', 'Alemgena', '2025-06-05 13:20:05'),
(39, 'Sebategna Branch', 'Sebategna', '2025-06-05 13:20:21'),
(40, 'Bulgaria Branch', 'Bulgaria', '2025-06-05 13:20:45'),
(41, 'Mizan Teferi Branch', 'Mizan Teferi', '2025-06-05 13:22:33'),
(42, 'Main Branch', 'In front of Capital Hotel, 22, Addis Ababa.', '2025-06-05 13:23:45'),
(43, 'CEO', '6th Floor', '2025-06-05 13:24:20'),
(44, 'Legal', '5th', '2025-06-05 13:24:45'),
(45, 'Finance', '5th', '2025-06-05 13:24:54'),
(46, 'Marketing', '5th', '2025-06-05 13:25:06'),
(47, 'Risk', '5th', '2025-06-05 13:25:12'),
(48, 'Reinsurance', '5th', '2025-06-05 13:25:21'),
(49, 'Claims & Recovery', '4th', '2025-06-05 13:25:28'),
(50, 'Audit', '4th', '2025-06-05 13:26:05'),
(51, 'Engineering', '3rd', '2025-06-05 13:26:16'),
(52, 'Operation', '3rd', '2025-06-05 13:26:24'),
(53, 'HR & Logistics', '3rd & 1st', '2025-06-05 13:26:49'),
(55, 'Ethics', '5th', '2025-06-05 13:28:10');

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
(2, 5, NULL, '../uploads/default_avatar.png', '2025-05-27 14:18:23');

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

--
-- Dumping data for table `kb_categories`
--

INSERT INTO `kb_categories` (`id`, `name`, `created_at`) VALUES
(1, 'Computer', '2025-06-05 13:43:03'),
(2, 'Hardware', '2025-06-05 13:43:15'),
(3, 'Inline Phone', '2025-06-05 13:43:32'),
(4, 'Internet & Network', '2025-06-05 13:43:47'),
(5, 'Network Sharing Print', '2025-06-05 13:44:03'),
(6, 'Printer', '2025-06-05 13:44:11'),
(7, 'Shared Storage', '2025-06-05 13:44:21'),
(8, 'Applications & Software', '2025-06-05 13:44:50'),
(9, 'Authentication & Login', '2025-06-05 13:45:04');

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
(1, 'Mikiyas Wondimu', 'mikiyas@lucy.com', '$2y$10$2a2YskZP2SeC36dGAK4W7.w6kZzQqEczgzlr5PIXTE4HTCth1js9u', 'admin', '2025-06-05 12:25:38', 0, 1, NULL),
(2, 'Yehuwalashet Yitagesu', 'yehuwalashet@lucy.com', '$2y$10$qjPKjHDgnMjmkNzSvy4VXe8mwUBdwpsTkv5zVoHQsw3N.dZKuOZ1W', 'staff', '2025-06-05 13:03:11', 1, 1, NULL),
(3, 'Mengistu Ferdie', 'mengistu@lucy.com', '$2y$10$Z31DxTFIh.SLDTx/7gW2N.z6WwtOjD6XBc8Iua2dpljaW7c4A4Bha', 'staff', '2025-06-05 13:05:50', 1, 1, NULL),
(4, 'Eleni Zerihun', 'eleni@lucy.com', '$2y$10$0IaqC9.4l9nkBJg3WwQOSeuWNl/FMTcJhilPoXLLJkdf2w10UQ4ce', 'staff', '2025-06-05 13:06:43', 1, 1, NULL),
(5, 'Aaron Tamirat', 'aaron@lucy.com', '$2y$10$pilIo1wiKKudyCQ5kbtg0.3RNrcqbL1Vzhf3wMj7/dU1s17YyTCV2', 'staff', '2025-06-05 13:07:45', 1, 1, NULL),
(6, 'User Kazanchis', 'kazanchis@lucy.com', '$2y$10$V2sQeUc/HwP4FHtwj5N1sOKGQuIp4cLuG985SSVNuP6SjuK.OxFw2', 'user', '2025-06-05 13:51:25', 1, 2, NULL),
(7, 'User Piassa', 'piassa@lucy.com', '$2y$10$Og5DgpcnieRkE62Q6Da9H.fNnlMkqz8jAzfU00sOl38xzbzR9kiTC', 'user', '2025-06-05 13:52:09', 1, 3, NULL),
(8, 'User Stadium', 'stadium@lucy.com', '$2y$10$QOhOrGX02GC3CkPWxKbpguPJVBQVkg780tUVAs7pVuVDKr8EKE3Yy', 'user', '2025-06-05 13:52:40', 1, 4, NULL),
(9, 'User BoleMedhanialem', 'bolemedhanialem@lucy.com', '$2y$10$lSWYxpdPDbmovUrW8EVkv.o72mB30Ax8/WzJT68a0wADfGAr2HX3q', 'user', '2025-06-05 13:53:27', 1, 5, NULL),
(10, 'User Lideta', 'lideta@lucy.com', '$2y$10$AlIz2xVrC15mbocWxtAhjetKbeM/o02f6QItHScMfn38Cna/6x9Q6', 'user', '2025-06-05 13:53:55', 1, 6, NULL),
(11, 'User DebreBirhan', 'debrebirhan@lucy.com', '$2y$10$DhGDPtpzyQHov5MDqdWH7uMenPjgmYDAclEWNqoub7Zdg2sxNU5FC', 'user', '2025-06-05 13:54:26', 1, 7, NULL),
(12, 'User AddeyAbeba', 'addeyabeba@lucy.com', '$2y$10$HjJ2hr9h3NBCWO5oFK.axOkCC7glboVtoIl1xcXPCkSQV8RE.tXrW', 'user', '2025-06-05 13:55:34', 1, 8, NULL),
(13, 'User Betel', 'betel@lucy.com', '$2y$10$5Ud7fzXBlZYodatL6o/uDe9y8YuJZB3sWTB1cC2nP9zW8gu/U9n4W', 'user', '2025-06-05 13:56:02', 1, 9, NULL),
(14, 'User AddisuGebeya', 'addisugebeya@lucy.com', '$2y$10$0JV5.78CPGGEd5tdlFuWI.Fc0pqXEqJt0DanEtlsOsIXaNQC/X7uG', 'user', '2025-06-05 13:56:28', 1, 10, NULL),
(15, 'User Bahirdar', 'bahirdar@lucy.com', '$2y$10$uQq4M9kvqChSCnK.oJjCMue5einY62kED0pgDKfrYfIjsf7K8PPj6', 'user', '2025-06-05 13:56:55', 1, 11, NULL),
(16, 'User Diredawa', 'diredawa@lucy.com', '$2y$10$xJ1mUEzoK2HWvOAL7vspOO61QzwFKhjjQJqFesUese3/1Joqyhdse', 'user', '2025-06-05 13:57:30', 1, 12, NULL),
(17, 'User Adama', 'adama@lucy.com', '$2y$10$K93QZNlAkfRHFV4BbBUEiO8F559YTZg0ctbwMdBHyd1hJ9I/lkkfe', 'user', '2025-06-05 13:57:51', 1, 13, NULL),
(18, 'User Lemikura', 'lemikura@lucy.com', '$2y$10$J5U61s3YTeylHYwfeiE3yeo6pUDy0HjOU5cS5tWekYlx8nwePlTIW', 'user', '2025-06-05 13:58:16', 1, 14, NULL),
(19, 'User AratKilo', 'aratkilo@lucy.com', '$2y$10$uN4vmqFb2xj8Llj3Nz0iVepgRPC/IyEuLpo5hibY2AevYvwwWMhlC', 'user', '2025-06-05 13:58:47', 1, 15, NULL),
(20, 'User Yoseph', 'yoseph@lucy.com', '$2y$10$eSAmxiLyhzP/RYs7X3wS2.XLHP0S2nAFY8q5VVAUDqKbwmeMwQvpG', 'user', '2025-06-05 13:59:12', 1, 16, NULL),
(21, 'User Figa', 'figa@lucy.com', '$2y$10$guDL.pG5d1maNnoiT.on7unDBgrDc5bNpKC3JSV1NC4Z7SroV3Sdq', 'user', '2025-06-05 14:00:23', 1, 17, NULL),
(22, 'User Ayertena', 'ayertena@lucy.com', '$2y$10$zakHeZm.G5xwYxC7vFEXOux491boBkUFD7o9w0x6NsAS2UG4TYD0O', 'user', '2025-06-05 14:00:46', 1, 18, NULL);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
