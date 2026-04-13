-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2026 at 09:58 AM
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
-- Database: `checkmate`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `system_name` varchar(255) DEFAULT NULL,
  `system_tagline` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`, `updated_at`, `system_name`, `system_tagline`) VALUES
(1, 'System', 'Administrator', 'admin@nbsc.edu.ph', '$2y$10$HHDPAd8sANHcZ.tCsz09MuUqEZ5WOQBVpn1L9BgbUA1IzKGKb6DDq', 'admin', '2026-04-06 03:23:46', '2026-04-06 06:56:03', 'Student Evaluation System', 'Empowering excellence in education through comprehensive student performance tracking, evaluation, and assessment reporting.');

-- --------------------------------------------------------

--
-- Table structure for table `admin_promotions`
--

CREATE TABLE `admin_promotions` (
  `id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL,
  `promoted_to` varchar(50) NOT NULL COMMENT 'program_head or admin',
  `promoted_by` int(11) NOT NULL COMMENT 'admin user_id',
  `promotion_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_promotions`
--

INSERT INTO `admin_promotions` (`id`, `instructor_id`, `promoted_to`, `promoted_by`, `promotion_date`) VALUES
(6, 8, 'program_head', 1, '2026-04-06 08:46:39');

-- --------------------------------------------------------

--
-- Table structure for table `instructors`
--

CREATE TABLE `instructors` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(100) DEFAULT 'Instructor',
  `phone` varchar(50) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `avatar_gradient_from` varchar(20) DEFAULT '#667eea',
  `avatar_gradient_to` varchar(20) DEFAULT '#764ba2',
  `status` enum('on duty','on leave','on travel') DEFAULT 'on duty',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `instructors`
--

INSERT INTO `instructors` (`id`, `first_name`, `middle_name`, `last_name`, `department`, `suffix`, `email`, `password`, `position`, `phone`, `birthday`, `avatar`, `avatar_gradient_from`, `avatar_gradient_to`, `status`, `created_at`, `updated_at`) VALUES
(7, 'clifford', 'r', 'rule', NULL, 'V', 'rule@gmail.com', '$2y$10$ZNNfzpaqacE25/8Y1omcZevDvseTR/tq/0SXm.mqRTeMY5x.0wzSO', 'Instructor', NULL, NULL, NULL, '#667eea', '#764ba2', 'on duty', '2026-04-06 07:00:12', '2026-04-06 07:00:12'),
(8, 'joshua', 'tesoro', 'quidit', NULL, 'V', 'opop@gmail.com', '$2y$10$09Bo1/tnZ6pGJk6C5TacDu858KUbeJT7iRiursh.d6qzbHbDU1V/G', 'Instructor', NULL, NULL, '8.jpg', '#667eea', '#764ba2', 'on duty', '2026-04-06 07:00:53', '2026-04-12 14:25:46');

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `id` int(11) NOT NULL,
  `major_name` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT 'fas fa-building',
  `gradient_from` varchar(20) DEFAULT '#d4a843',
  `gradient_to` varchar(20) DEFAULT '#e8c768',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`id`, `major_name`, `display_name`, `description`, `icon_class`, `gradient_from`, `gradient_to`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Operational Management', 'Operational Management', 'Focuses on business operations, processes, and management strategies to optimize organizational efficiency.', 'fas fa-cogs', '#d4a843', '#e8c768', 1, 1, '2026-04-06 03:23:46', '2026-04-06 03:23:46'),
(2, 'Financial Management', 'Financial Management', 'Specializes in financial analysis, accounting, investment decisions, and corporate finance strategies.', 'fas fa-dollar-sign', '#3b82f6', '#60a5fa', 2, 1, '2026-04-06 03:23:46', '2026-04-06 03:23:46'),
(3, 'Marketing Management', 'Marketing Management', 'Covers marketing principles, consumer behavior, market research, and strategic marketing planning.', 'fas fa-chart-line', '#ec4899', '#f472b6', 3, 1, '2026-04-06 03:23:46', '2026-04-06 03:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `major_subjects`
--

CREATE TABLE `major_subjects` (
  `id` int(11) NOT NULL,
  `major_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `year_level` varchar(20) DEFAULT '1st Year',
  `semester` varchar(20) DEFAULT '1st Semester',
  `is_required` tinyint(1) DEFAULT 1,
  `is_prerequisite` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mentees`
--

CREATE TABLE `mentees` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mentor_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by_id` int(11) DEFAULT NULL,
  `assigned_by_name` varchar(255) DEFAULT NULL,
  `assignment_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mentees`
--

INSERT INTO `mentees` (`id`, `student_id`, `first_name`, `last_name`, `email`, `mentor_id`, `created_at`, `assigned_by_id`, `assigned_by_name`, `assignment_notes`) VALUES
(2, 1, 'John', 'Doe', 'john.doe@student.edu', 7, '2026-04-07 06:43:46', NULL, NULL, NULL),
(3, 3, 'Mike', 'Johnson', 'mike.j@student.edu', 8, '2026-04-07 06:44:35', NULL, NULL, NULL),
(4, 4, 'Sarah', 'Williams', 'sarah.w@student.edu', 8, '2026-04-07 06:44:35', NULL, NULL, NULL),
(5, 2, 'Jane', 'Wilson', 'jane.wilson@student.edu', 7, '2026-04-07 07:03:15', NULL, NULL, NULL),
(6, 7, 'nana', 'na', '2000@nbsc.du.ph', 8, '2026-04-08 04:59:57', NULL, NULL, NULL),
(7, 6, 'opop', 'wqeqweq', 'sdgb@gmail.com', 8, '2026-04-12 23:56:31', NULL, NULL, NULL),
(8, 5, 'Tom', 'Brown', 'tom.b@student.edu', 8, '2026-04-12 23:57:11', NULL, NULL, NULL),
(10, 1, 'John', 'Doe', 'john.doe@student.edu', 7, '2026-01-15 02:30:00', 1, 'Admin', 'Best overall performer'),
(11, 2, 'Jane', 'Wilson', 'jane.wilson@student.edu', 7, '2026-02-01 06:20:00', 1, 'Admin', 'Strong analytical skills'),
(12, 4, 'Mike', 'Johnson', 'mike.j@student.edu', 7, '2026-02-10 01:15:00', 1, 'Admin', 'Excellent communication'),
(13, 5, 'Sarah', 'Williams', 'sarah.w@student.edu', 7, '2026-03-05 03:45:00', 1, 'Admin', 'Top performer in Marketing'),
(14, 6, 'Tom', 'Brown', 'tom.b@student.edu', 7, '2026-03-12 08:30:00', 1, 'Admin', 'Shows great potential');

-- --------------------------------------------------------

--
-- Table structure for table `pending_instructors`
--

CREATE TABLE `pending_instructors` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pending_instructors`
--

INSERT INTO `pending_instructors` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `email`, `password`, `instructor_id`, `status`, `created_at`) VALUES
(3, 'Carol', 'M.', 'Williams', NULL, 'carol.williams@pending.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NULL, 'approved', '2026-04-06 03:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `program_heads`
--

CREATE TABLE `program_heads` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `position` varchar(100) DEFAULT 'Program Head',
  `office_location` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `program_heads`
--

INSERT INTO `program_heads` (`id`, `first_name`, `last_name`, `email`, `password`, `position`, `office_location`, `created_at`, `updated_at`) VALUES
(9, 'joshua', 'quidit', 'opop@gmail.com', '$2y$10$M4xgl87qNCwTSgckHxuZA.hX01JaG8BmmVl7PA4rkva6Nr84zZjqS', 'Program Head', NULL, '2026-04-06 08:46:39', '2026-04-06 08:46:39');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(200) NOT NULL,
  `report_description` varchar(500) DEFAULT NULL,
  `report_type` enum('pdf','excel') DEFAULT 'pdf',
  `icon_class` varchar(100) DEFAULT 'fas fa-file-pdf',
  `download_count` int(11) DEFAULT 0,
  `generated_by` varchar(50) DEFAULT 'instructor',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `report_name`, `report_description`, `report_type`, `icon_class`, `download_count`, `generated_by`, `created_at`) VALUES
(1, 'Evaluation Summary Report', 'Spring 2026 Semester', 'pdf', 'fas fa-file-pdf', 8, 'instructor', '2026-04-06 03:23:46'),
(2, 'Course Performance Report', 'All Courses - Academic Year 2025-2026', 'pdf', 'fas fa-file-pdf', 5, 'instructor', '2026-04-06 03:23:46'),
(3, 'Student Grades Export', 'Current Semester', 'excel', 'fas fa-file-excel', 6, 'instructor', '2026-04-06 03:23:46'),
(4, 'Feedback Analysis', 'All Courses - Comprehensive', 'pdf', 'fas fa-file-pdf', 4, 'instructor', '2026-04-06 03:23:46'),
(5, 'Department Performance Report', 'All Departments - Spring 2026', 'pdf', 'fas fa-file-pdf', 10, 'program_head', '2026-04-06 03:23:46'),
(6, 'Instructor Ranking Report', 'Top Performers - Academic Year 2025-2026', 'pdf', 'fas fa-file-pdf', 7, 'program_head', '2026-04-06 03:23:46'),
(7, 'Course Completion Report', 'Evaluation Completion Rates', 'excel', 'fas fa-file-excel', 3, 'program_head', '2026-04-06 03:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `suffix` varchar(20) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `major_id` int(11) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT NULL,
  `avatar_initials` varchar(5) DEFAULT NULL,
  `avatar_gradient_from` varchar(20) DEFAULT '#3b82f6',
  `avatar_gradient_to` varchar(20) DEFAULT '#60a5fa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `student_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `email`, `major_id`, `year_level`, `avatar_initials`, `avatar_gradient_from`, `avatar_gradient_to`, `created_at`, `updated_at`, `student_id`) VALUES
(1, 'John', 'M.', 'Doe', NULL, 'john.doe@student.edu', 1, '3rd Year', 'JD', '#3b82f6', '#60a5fa', '2026-04-06 03:23:46', '2026-04-07 05:22:12', '2222'),
(2, 'Jane', 'A.', 'Wilson', NULL, 'jane.wilson@student.edu', 1, '2nd Year', 'JW', '#10b981', '#34d399', '2026-04-06 03:23:46', '2026-04-07 06:00:51', '09090990'),
(3, 'Mike', 'R.', 'Johnson', 'Jr.', 'mike.j@student.edu', 3, '3rd Year', 'MJ', '#8b5cf6', '#a78bfa', '2026-04-06 03:23:46', '2026-04-07 05:27:59', '34232'),
(4, 'Sarah', 'L.', 'Williams', 'Sr.', 'sarah.w@student.edu', 1, '4th Year', 'SW', '#f43f5e', '#fb7185', '2026-04-06 03:23:46', '2026-04-07 05:19:13', '202012'),
(5, 'Tom', 'B.', 'Brown', 'Sr.', 'tom.b@student.edu', 1, '2nd Year', 'TB', '#f59e0b', '#fbbf24', '2026-04-06 03:23:46', '2026-04-12 23:47:54', '202224'),
(6, 'opop', 'twewq', 'wqeqweq', 'IV', 'sdgb@gmail.com', 2, '1st Year', 'OW', '#3b82f6', '#60a5fa', '2026-04-07 05:49:21', '2026-04-07 05:49:21', '2321424'),
(7, 'nana', 'moly', 'na', NULL, '2000@nbsc.du.ph', 2, '1st Year', 'NN', '#3b82f6', '#60a5fa', '2026-04-08 04:59:24', '2026-04-08 04:59:24', '2029909');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `units` decimal(3,1) DEFAULT 3.0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `default_year_level` varchar(20) DEFAULT '1st Year',
  `semester` varchar(20) DEFAULT '1st Semester',
  `prerequisite` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_code`, `subject_name`, `units`, `created_at`, `updated_at`, `default_year_level`, `semester`, `prerequisite`) VALUES
(9, 'ENG101', 'wqdqwd', 3.0, '2026-04-13 03:47:58', '2026-04-13 03:47:58', '3rd Year', '1st Semester', NULL),
(10, 'eqwd', 'asdsa', 3.0, '2026-04-13 06:55:57', '2026-04-13 06:55:57', '2nd Year', '1st Semester', NULL),
(11, 'sadas', 'ds', 3.0, '2026-04-13 07:18:19', '2026-04-13 07:18:19', '2nd Year', '1st Semester', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `instructor_id` int(11) NOT NULL,
  `priority` enum('low','medium','high') DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `instructor_id`, `priority`, `due_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'attendance', 'all student', 8, 'high', '2026-04-08', 'active', '2026-04-08 05:53:28', '2026-04-08 05:53:28'),
(2, 'attendance', 'sdfgn', 8, 'high', '2026-04-08', 'active', '2026-04-08 06:16:53', '2026-04-08 06:16:53'),
(3, 'asdfgh', 'sdaf', 8, 'medium', '2026-04-08', 'active', '2026-04-08 06:52:03', '2026-04-08 06:52:03'),
(4, 'fdsf', 'dsfsdf', 8, 'medium', NULL, 'active', '2026-04-09 05:39:49', '2026-04-09 05:39:49'),
(5, 'wwww', 'wwww', 8, 'medium', NULL, 'active', '2026-04-09 06:26:44', '2026-04-09 06:26:44'),
(6, 'vvv', 'vvv', 8, 'medium', NULL, 'active', '2026-04-09 06:27:02', '2026-04-09 06:27:02'),
(7, 'vvv', 'vvv', 8, 'medium', NULL, 'active', '2026-04-09 06:40:40', '2026-04-09 06:40:40');

-- --------------------------------------------------------

--
-- Table structure for table `task_assignments`
--

CREATE TABLE `task_assignments` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `mentee_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `completion_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_assignments`
--

INSERT INTO `task_assignments` (`id`, `task_id`, `mentee_id`, `assigned_at`, `status`, `completion_date`, `notes`) VALUES
(3, 3, 3, '2026-04-08 06:52:03', 'pending', NULL, NULL),
(5, 4, 3, '2026-04-09 05:39:49', 'pending', NULL, NULL),
(6, 4, 6, '2026-04-09 05:39:49', 'pending', NULL, NULL),
(7, 4, 4, '2026-04-09 05:39:49', 'pending', NULL, NULL),
(10, 7, 3, '2026-04-09 06:40:40', 'pending', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `admin_promotions`
--
ALTER TABLE `admin_promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_instructor_id` (`instructor_id`);

--
-- Indexes for table `instructors`
--
ALTER TABLE `instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_major_name` (`major_name`),
  ADD KEY `idx_major_name` (`major_name`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `major_subjects`
--
ALTER TABLE `major_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_major_subject` (`major_id`,`subject_id`);

--
-- Indexes for table `mentees`
--
ALTER TABLE `mentees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_mentor_id` (`mentor_id`),
  ADD KEY `idx_student_id` (`student_id`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `pending_instructors`
--
ALTER TABLE `pending_instructors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_instructor_id` (`instructor_id`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `program_heads`
--
ALTER TABLE `program_heads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_report_type` (`report_type`),
  ADD KEY `idx_generated_by` (`generated_by`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_major_id` (`major_id`),
  ADD KEY `idx_year_level` (`year_level`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_subject_code` (`subject_code`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_instructor_id` (`instructor_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_due_date` (`due_date`);

--
-- Indexes for table `task_assignments`
--
ALTER TABLE `task_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_task_mentee` (`task_id`,`mentee_id`),
  ADD KEY `idx_task_id` (`task_id`),
  ADD KEY `idx_mentee_id` (`mentee_id`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `admin_promotions`
--
ALTER TABLE `admin_promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `instructors`
--
ALTER TABLE `instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `major_subjects`
--
ALTER TABLE `major_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `mentees`
--
ALTER TABLE `mentees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `pending_instructors`
--
ALTER TABLE `pending_instructors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `program_heads`
--
ALTER TABLE `program_heads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `task_assignments`
--
ALTER TABLE `task_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_promotions`
--
ALTER TABLE `admin_promotions`
  ADD CONSTRAINT `admin_promotions_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mentees`
--
ALTER TABLE `mentees`
  ADD CONSTRAINT `mentees_ibfk_1` FOREIGN KEY (`mentor_id`) REFERENCES `instructors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `mentees_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `pending_instructors`
--
ALTER TABLE `pending_instructors`
  ADD CONSTRAINT `pending_instructors_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`major_id`) REFERENCES `majors` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `task_assignments`
--
ALTER TABLE `task_assignments`
  ADD CONSTRAINT `task_assignments_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `task_assignments_ibfk_2` FOREIGN KEY (`mentee_id`) REFERENCES `mentees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
