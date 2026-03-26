-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2026 at 04:03 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `document_tracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(10) UNSIGNED NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `department_name`, `department_code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Administrative Division', 'ADM', 1, '2026-03-23 02:47:17', '2026-03-23 02:47:17'),
(2, 'Chief of Hospital Office', 'CHO', 1, '2026-03-05 07:21:08', '2026-03-05 07:21:08'),
(3, 'Residential/Inpatient Treatment Division', 'RES', 1, '2026-02-20 01:36:34', NULL),
(4, 'Outpatient and Aftercare Division', 'OUT', 1, '2026-03-23 02:53:45', '2026-03-23 02:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `doc_id` int(10) UNSIGNED NOT NULL,
  `document_number` varchar(255) NOT NULL,
  `type_id` int(10) UNSIGNED NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `originating_section_id` int(10) UNSIGNED NOT NULL,
  `created_by` int(11) NOT NULL,
  `current_section_id` int(10) UNSIGNED NOT NULL,
  `current_holder_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('CREATED','PENDING','UNDER REVIEW','END OF CYCLE','REOPENED') NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `document_actions`
--

CREATE TABLE `document_actions` (
  `action_id` int(10) UNSIGNED NOT NULL,
  `doc_id` int(10) UNSIGNED NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `position_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `action_type` enum('CREATED','FORWARDED','RECEIVED','END OF CYCLE','REOPEN') NOT NULL,
  `remarks` text NOT NULL,
  `action_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `document_attachments`
--

CREATE TABLE `document_attachments` (
  `attachment_id` int(10) UNSIGNED NOT NULL,
  `doc_id` int(10) UNSIGNED NOT NULL,
  `file_original_name` varchar(255) NOT NULL,
  `file_stored_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL,
  `version_number` int(11) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `document_counters`
--

CREATE TABLE `document_counters` (
  `counter_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `year` int(11) NOT NULL,
  `last_number` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `type_id` int(10) UNSIGNED NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_code` varchar(255) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`type_id`, `type_name`, `type_code`, `is_active`, `created_at`) VALUES
(1, 'Policy Document', 'POL', 1, '2026-02-20 01:36:34'),
(2, 'Procedure Manual', 'PRO', 1, '2026-02-20 01:36:34'),
(3, 'Form', 'FRM', 1, '2026-02-20 01:36:34'),
(4, 'Report', 'RPT', 1, '2026-02-20 01:36:34'),
(5, 'Guideline', 'GDL', 1, '2026-02-20 01:36:34'),
(6, 'Memo', 'MEM', 1, '2026-02-20 01:36:34'),
(7, 'Instruction', 'INS', 1, '2026-02-20 01:36:34'),
(8, 'Announcement', 'ANN', 1, '2026-02-20 01:36:34'),
(9, 'Annual Budget', 'BUD', 1, '2026-02-20 01:36:34'),
(10, 'Supplementary Budget', 'SBUD', 1, '2026-02-20 01:36:34'),
(11, 'Health Advisory', 'ADV', 1, '2026-02-20 01:36:34'),
(12, 'Circular', 'CIR', 1, '2026-02-20 01:36:34'),
(13, 'Inspection Report', 'INSP', 1, '2026-02-20 01:36:34'),
(14, 'Audit Report', 'AUD', 1, '2026-02-20 01:36:34'),
(15, 'Training Manual', 'TRN', 1, '2026-02-20 01:36:34'),
(16, 'Evaluation Report', 'EVAL', 1, '2026-02-20 01:36:34'),
(17, 'Program Plan', 'PLAN', 1, '2026-02-20 01:36:34'),
(18, 'Funding Request', 'FUND', 1, '2026-02-20 01:36:34'),
(19, 'Vaccination Report', 'VAC', 1, '2026-02-20 01:36:34'),
(20, 'Emergency Memo', 'EMEM', 1, '2026-02-20 01:36:34');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(10) UNSIGNED NOT NULL,
  `position_title` varchar(255) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `section_id` int(10) UNSIGNED NOT NULL,
  `department_id` int(10) UNSIGNED NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `section_code` varchar(255) NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`section_id`, `department_id`, `section_name`, `section_code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Human Resources', 'HR', 1, '2026-03-23 02:51:26', '2026-03-23 10:51:26'),
(2, 1, 'Finance & Accounting', 'FIN', 1, '2026-03-23 02:50:14', '2026-03-23 10:50:14'),
(3, 2, 'Hospital Administration', 'HA', 1, '2026-03-05 07:21:08', '2026-03-05 15:21:08'),
(4, 2, 'Policy & Planning', 'PP', 0, '2026-03-09 03:00:58', '2026-03-09 11:00:58'),
(5, 3, 'Ward A', 'WA', 1, '2026-02-20 01:36:34', '2026-03-05 10:21:50'),
(6, 3, 'Ward B', 'WB', 1, '2026-02-20 01:36:34', '2026-03-05 10:21:50'),
(7, 4, 'Outpatient Services', 'OS', 1, '2026-03-23 02:53:45', '2026-03-23 10:53:45'),
(8, 4, 'Aftercare & Follow-up', 'AF', 1, '2026-03-23 02:53:45', '2026-03-23 10:53:45');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  'middle_name' varchar(255) NOT NULL,
  'last_name' varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `section_id` int(10) UNSIGNED NOT NULL,
  `position_id` int(10) UNSIGNED NOT NULL,
  `role` enum('ADMIN','CHIEF','DEPARTMENT-HEAD','SECTION-HEAD','EMPLOYEE') NOT NULL,
  `is_active` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

--
-- Indexes for dumped tables
--

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `documents_type_id_foreign` (`type_id`),
  ADD KEY `documents_originating_section_id_foreign` (`originating_section_id`),
  ADD KEY `documents_current_section_id_foreign` (`current_section_id`),
  ADD KEY `documents_current_holder_id_foreign` (`current_holder_id`);

--
-- Indexes for table `document_actions`
--
ALTER TABLE `document_actions`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `document_actions_doc_id_foreign` (`doc_id`),
  ADD KEY `document_actions_section_id_foreign` (`section_id`),
  ADD KEY `document_actions_user_id_foreign` (`user_id`),
  ADD KEY `fk_document_actions_position` (`position_id`);

--
-- Indexes for table `document_attachments`
--
ALTER TABLE `document_attachments`
  ADD PRIMARY KEY (`attachment_id`);

--
-- Indexes for table `document_counters`
--
ALTER TABLE `document_counters`
  ADD PRIMARY KEY (`counter_id`),
  ADD UNIQUE KEY `document_counters_department_id_unique` (`department_id`),
  ADD UNIQUE KEY `document_counters_section_id_unique` (`section_id`),
  ADD UNIQUE KEY `document_counters_year_unique` (`year`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`type_id`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`),

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`section_id`),
  ADD UNIQUE KEY `sections_section_code_unique` (`section_code`),
  ADD KEY `sections_department_id_foreign` (`department_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `users_section_id_foreign` (`section_id`),
  ADD KEY `users_position_id_foreign` (`position_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `doc_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `document_actions`
--
ALTER TABLE `document_actions`
  MODIFY `action_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=183;

--
-- AUTO_INCREMENT for table `document_attachments`
--
ALTER TABLE `document_attachments`
  MODIFY `attachment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `document_counters`
--
ALTER TABLE `document_counters`
  MODIFY `counter_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `type_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `section_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_current_holder_id_foreign` FOREIGN KEY (`current_holder_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documents_current_section_id_foreign` FOREIGN KEY (`current_section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `documents_originating_section_id_foreign` FOREIGN KEY (`originating_section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `documents_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `document_types` (`type_id`);

--
-- Constraints for table `document_actions`
--
ALTER TABLE `document_actions`
  ADD CONSTRAINT `document_actions_doc_id_foreign` FOREIGN KEY (`doc_id`) REFERENCES `documents` (`doc_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_actions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`),
  ADD CONSTRAINT `document_actions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `fk_document_actions_position` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE SET NULL;

--
-- Constraints for table `document_counters`
--
ALTER TABLE `document_counters`
  ADD CONSTRAINT `document_counters_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`);

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`),
  ADD CONSTRAINT `users_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`section_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
