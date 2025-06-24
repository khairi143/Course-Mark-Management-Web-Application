-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2025 at 01:46 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `course_marks_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `advisors`
--

CREATE TABLE `advisors` (
  `id` int(20) NOT NULL,
  `user_id` int(20) NOT NULL,
  `advisor_full_name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisors`
--

INSERT INTO `advisors` (`id`, `user_id`, `advisor_full_name`) VALUES
(3, 24, 'Dr Siti Zaiton'),
(5, 28, 'Dr Wan');

-- --------------------------------------------------------

--
-- Table structure for table `advisor_notes`
--

CREATE TABLE `advisor_notes` (
  `id` int(11) NOT NULL,
  `advisor_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `advisor_notes`
--

INSERT INTO `advisor_notes` (`id`, `advisor_id`, `student_id`, `title`, `content`, `date`, `created_at`) VALUES
(9, 3, 2, 'Reminder', 'Reminder to send student exchange information to this student via whatsapp', '2025-06-26', '2025-06-24 20:24:15');

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `assessment_name` varchar(255) NOT NULL,
  `max_marks` int(11) NOT NULL,
  `weight_percentage` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessments`
--

INSERT INTO `assessments` (`id`, `section_id`, `assessment_name`, `max_marks`, `weight_percentage`) VALUES
(5, 3, 'Quiz 1', 15, 5.00),
(6, 3, 'Quiz 2', 15, 5.00),
(7, 3, 'Final Exam', 100, 30.00),
(8, 5, 'Quiz 1', 15, 5.00),
(9, 5, 'Quiz 2', 15, 5.00),
(10, 5, 'Assignment 1', 100, 20.00),
(11, 5, 'Assignment 2', 100, 20.00),
(12, 5, 'Project', 100, 20.00),
(13, 5, 'Final Exam', 100, 30.00),
(14, 8, 'Quizzes', 50, 15.00),
(15, 8, 'Assignments', 80, 55.00),
(16, 8, 'Final Exam', 100, 30.00),
(18, 13, 'Quiz 2', 15, 5.00),
(19, 13, 'Assignment 1', 100, 20.00),
(20, 13, 'Assignment 2', 100, 20.00),
(21, 13, 'Project', 100, 20.00),
(22, 13, 'Final Exam', 100, 30.00),
(23, 13, 'Quiz 1', 15, 5.00);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `department` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `department`) VALUES
(2, 'CS102', 'Intro to CS', 'Computer Science'),
(4, 'CS103', 'Intro to OOP', 'Computer Science'),
(5, 'CS104', 'Data Structure & Algorithm', 'Computer Science'),
(6, 'CS105', 'Discrete Structure', 'Computer Science');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `section_id` int(20) NOT NULL,
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_percentage` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `section_id`, `enrollment_date`, `total_percentage`) VALUES
(6, 2, 3, '2025-06-23 20:08:17', 40.00),
(7, 4, 3, '2025-06-23 20:54:10', 27.33),
(8, 6, 3, '2025-06-24 12:15:03', 39.03),
(9, 2, 5, '2025-06-24 16:34:59', 100.00),
(10, 8, 3, '2025-06-24 19:08:13', NULL),
(11, 8, 5, '2025-06-24 19:08:21', 98.43),
(12, 6, 8, '2025-06-24 19:08:26', 100.00),
(13, 4, 8, '2025-06-24 19:08:29', 87.13),
(14, 2, 8, '2025-06-24 19:08:35', 89.70),
(15, 6, 12, '2025-06-24 19:08:56', NULL),
(16, 4, 12, '2025-06-24 19:09:00', NULL),
(17, 7, 12, '2025-06-24 19:09:03', NULL),
(18, 8, 13, '2025-06-24 20:14:36', 81.63),
(19, 7, 13, '2025-06-24 20:14:40', 77.60),
(20, 6, 13, '2025-06-24 20:14:43', 87.13),
(21, 4, 13, '2025-06-24 20:14:46', 78.80),
(22, 2, 13, '2025-06-24 20:14:50', 86.30);

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `user_id`, `department`) VALUES
(1, 3, 'Computer Science'),
(4, 14, 'Computer Science'),
(6, 26, 'Computer Science');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` text NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `reviewed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `user_id`, `action`, `timestamp`, `reviewed`) VALUES
(1, 1, 'login', '2025-06-25 03:05:05', 1),
(2, 3, 'login', '2025-06-25 03:07:40', 1),
(3, 10, 'login', '2025-06-25 03:12:20', 1),
(4, 3, 'login', '2025-06-25 03:26:47', 1),
(5, 10, 'login', '2025-06-25 03:29:29', 1),
(6, 24, 'login', '2025-06-25 03:30:16', 1),
(7, 1, 'login', '2025-06-25 03:30:37', 0),
(8, 28, 'login', '2025-06-25 03:44:08', 0),
(9, 24, 'login', '2025-06-25 03:45:39', 0),
(10, 1, 'login', '2025-06-25 04:10:32', 0),
(11, 1, 'login', '2025-06-25 04:10:46', 0),
(12, 3, 'login', '2025-06-25 04:13:58', 0),
(13, 10, 'login', '2025-06-25 04:20:38', 0),
(14, 24, 'login', '2025-06-25 04:22:51', 0),
(15, 3, 'login', '2025-06-25 04:27:19', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('mark_update','assessment_created','general','system') DEFAULT 'general',
  `related_assessment_id` int(11) DEFAULT NULL,
  `related_section_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `related_assessment_id`, `related_section_id`, `is_read`, `created_at`, `read_at`) VALUES
(7, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 6, 3, 1, '2025-06-23 21:28:30', '2025-06-24 13:52:04'),
(8, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 6, 3, 1, '2025-06-23 21:28:40', '2025-06-24 13:52:03'),
(9, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 5, 3, 0, '2025-06-24 11:16:45', NULL),
(10, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 6, 3, 0, '2025-06-24 11:16:45', NULL),
(11, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 7, 3, 0, '2025-06-24 11:16:45', NULL),
(12, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 7, 3, 0, '2025-06-24 11:17:20', NULL),
(13, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 5, 3, 0, '2025-06-24 12:15:21', NULL),
(14, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 6, 3, 0, '2025-06-24 12:15:21', NULL),
(15, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 7, 3, 0, '2025-06-24 12:15:21', NULL),
(16, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 8, 5, 1, '2025-06-24 19:11:10', '2025-06-24 19:12:27'),
(17, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 9, 5, 1, '2025-06-24 19:11:10', '2025-06-24 19:12:27'),
(18, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 1\' has been updated. Please check your results.', 'mark_update', 10, 5, 1, '2025-06-24 19:11:10', '2025-06-24 19:12:27'),
(19, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 2\' has been updated. Please check your results.', 'mark_update', 11, 5, 1, '2025-06-24 19:11:10', '2025-06-24 19:12:28'),
(20, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Project\' has been updated. Please check your results.', 'mark_update', 12, 5, 1, '2025-06-24 19:11:10', '2025-06-24 19:12:28'),
(21, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 13, 5, 1, '2025-06-24 19:11:10', '2025-06-24 19:12:29'),
(22, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 8, 5, 0, '2025-06-24 19:11:23', NULL),
(23, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 9, 5, 0, '2025-06-24 19:11:23', NULL),
(24, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 1\' has been updated. Please check your results.', 'mark_update', 10, 5, 0, '2025-06-24 19:11:23', NULL),
(25, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 2\' has been updated. Please check your results.', 'mark_update', 11, 5, 0, '2025-06-24 19:11:23', NULL),
(26, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Project\' has been updated. Please check your results.', 'mark_update', 12, 5, 0, '2025-06-24 19:11:23', NULL),
(27, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 13, 5, 0, '2025-06-24 19:11:23', NULL),
(28, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Quizzes\' has been updated. Please check your results.', 'mark_update', 14, 8, 0, '2025-06-24 19:28:51', NULL),
(29, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Assignments\' has been updated. Please check your results.', 'mark_update', 15, 8, 0, '2025-06-24 19:28:51', NULL),
(30, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 16, 8, 0, '2025-06-24 19:28:51', NULL),
(31, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Quizzes\' has been updated. Please check your results.', 'mark_update', 14, 8, 0, '2025-06-24 19:29:03', NULL),
(32, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Assignments\' has been updated. Please check your results.', 'mark_update', 15, 8, 0, '2025-06-24 19:29:03', NULL),
(33, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 16, 8, 0, '2025-06-24 19:29:03', NULL),
(34, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Quizzes\' has been updated. Please check your results.', 'mark_update', 14, 8, 0, '2025-06-24 19:29:13', NULL),
(35, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Assignments\' has been updated. Please check your results.', 'mark_update', 15, 8, 0, '2025-06-24 19:29:13', NULL),
(36, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 16, 8, 0, '2025-06-24 19:29:13', NULL),
(37, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 18, 13, 0, '2025-06-24 20:18:22', NULL),
(38, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 1\' has been updated. Please check your results.', 'mark_update', 19, 13, 0, '2025-06-24 20:18:22', NULL),
(39, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 2\' has been updated. Please check your results.', 'mark_update', 20, 13, 0, '2025-06-24 20:18:22', NULL),
(40, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Project\' has been updated. Please check your results.', 'mark_update', 21, 13, 0, '2025-06-24 20:18:22', NULL),
(41, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 22, 13, 0, '2025-06-24 20:18:22', NULL),
(42, 25, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 23, 13, 0, '2025-06-24 20:18:22', NULL),
(43, 23, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 18, 13, 0, '2025-06-24 20:18:46', NULL),
(44, 23, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 1\' has been updated. Please check your results.', 'mark_update', 19, 13, 0, '2025-06-24 20:18:46', NULL),
(45, 23, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 2\' has been updated. Please check your results.', 'mark_update', 20, 13, 0, '2025-06-24 20:18:46', NULL),
(46, 23, 'Assessment Mark Updated', 'Your mark for assessment \'Project\' has been updated. Please check your results.', 'mark_update', 21, 13, 0, '2025-06-24 20:18:46', NULL),
(47, 23, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 22, 13, 0, '2025-06-24 20:18:46', NULL),
(48, 23, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 23, 13, 0, '2025-06-24 20:18:46', NULL),
(49, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 18, 13, 0, '2025-06-24 20:19:00', NULL),
(50, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 1\' has been updated. Please check your results.', 'mark_update', 19, 13, 0, '2025-06-24 20:19:00', NULL),
(51, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 2\' has been updated. Please check your results.', 'mark_update', 20, 13, 0, '2025-06-24 20:19:00', NULL),
(52, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Project\' has been updated. Please check your results.', 'mark_update', 21, 13, 0, '2025-06-24 20:19:00', NULL),
(53, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 22, 13, 0, '2025-06-24 20:19:00', NULL),
(54, 19, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 23, 13, 0, '2025-06-24 20:19:00', NULL),
(55, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 18, 13, 0, '2025-06-24 20:19:12', NULL),
(56, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 1\' has been updated. Please check your results.', 'mark_update', 19, 13, 0, '2025-06-24 20:19:12', NULL),
(57, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 2\' has been updated. Please check your results.', 'mark_update', 20, 13, 0, '2025-06-24 20:19:12', NULL),
(58, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Project\' has been updated. Please check your results.', 'mark_update', 21, 13, 0, '2025-06-24 20:19:12', NULL),
(59, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 22, 13, 0, '2025-06-24 20:19:12', NULL),
(60, 10, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 23, 13, 0, '2025-06-24 20:19:12', NULL),
(61, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 2\' has been updated. Please check your results.', 'mark_update', 18, 13, 0, '2025-06-24 20:20:03', NULL),
(62, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 1\' has been updated. Please check your results.', 'mark_update', 19, 13, 0, '2025-06-24 20:20:03', NULL),
(63, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Assignment 2\' has been updated. Please check your results.', 'mark_update', 20, 13, 0, '2025-06-24 20:20:03', NULL),
(64, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Project\' has been updated. Please check your results.', 'mark_update', 21, 13, 0, '2025-06-24 20:20:03', NULL),
(65, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Final Exam\' has been updated. Please check your results.', 'mark_update', 22, 13, 0, '2025-06-24 20:20:03', NULL),
(66, 12, 'Assessment Mark Updated', 'Your mark for assessment \'Quiz 1\' has been updated. Please check your results.', 'mark_update', 23, 13, 0, '2025-06-24 20:20:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `remark_requests`
--

CREATE TABLE `remark_requests` (
  `id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `justification` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remark_requests`
--

INSERT INTO `remark_requests` (`id`, `assessment_id`, `student_id`, `justification`) VALUES
(1, 5, 2, 'Please remark this'),
(2, 13, 2, 'no logic mark'),
(3, 21, 2, 'dr please remark');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`) VALUES
(4, 'admin'),
(3, 'advisor'),
(1, 'lecturer'),
(2, 'student');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  `max_capacity` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `course_id`, `section_name`, `lecturer_id`, `max_capacity`) VALUES
(3, 2, '01', 1, 30),
(5, 4, '01', 1, 30),
(6, 5, '01', 4, 30),
(7, 5, '02', 6, 30),
(8, 5, '03', 1, 30),
(9, 2, '02', 4, 30),
(10, 2, '03', 6, 30),
(11, 4, '02', 6, 30),
(12, 4, '03', 1, 30),
(13, 6, '01', 1, 30),
(14, 6, '02', 6, 30);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `matric_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `advisor_id` int(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `user_id`, `name`, `matric_number`, `email`, `phone`, `address`, `advisor_id`) VALUES
(2, 10, 'PUA ZHI YING', 'A22EC0103', 'pzhiying0306@gmail.com', '0187888263', '19, Jalan Bestari 5/1, Taman Nusa Bestari', 3),
(4, 12, 'TAM JIA HAO', 'A22EC0106', 'pzhiyingyt@gmail.com', '0187888263', '19, Jalan Bestari 5/1 ', 3),
(6, 19, 'TAN YOU CHUN', 'A22EC0108', 'pzhiyin3@gmail.com', '0182344231', 'address', 5),
(7, 23, 'KUAN JI TONG', 'A22EC0066', 'pzhiy@gmail.com', '0123222321', 'address', 5),
(8, 25, 'OH KAI XUAN', 'A22EC0102', 'okx@gmail.com', '0187888263', 'address', 3);

-- --------------------------------------------------------

--
-- Table structure for table `student_assessment_marks`
--

CREATE TABLE `student_assessment_marks` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `assessment_id` int(11) NOT NULL,
  `marks_obtained` decimal(5,2) NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_assessment_marks`
--

INSERT INTO `student_assessment_marks` (`id`, `student_id`, `assessment_id`, `marks_obtained`, `submission_date`) VALUES
(3, 2, 7, 100.00, '2025-06-23 20:01:58'),
(4, 2, 5, 15.00, '2025-06-23 20:04:47'),
(5, 2, 6, 15.00, '2025-06-23 20:04:47'),
(10, 4, 5, 5.00, '2025-06-24 11:16:45'),
(11, 4, 6, 5.00, '2025-06-24 11:16:45'),
(12, 4, 7, 80.00, '2025-06-24 11:16:45'),
(13, 6, 5, 14.00, '2025-06-24 12:15:21'),
(14, 6, 6, 14.00, '2025-06-24 12:15:21'),
(15, 6, 7, 99.00, '2025-06-24 12:15:21'),
(16, 2, 8, 15.00, '2025-06-24 19:11:10'),
(17, 2, 9, 15.00, '2025-06-24 19:11:10'),
(18, 2, 10, 100.00, '2025-06-24 19:11:10'),
(19, 2, 11, 100.00, '2025-06-24 19:11:10'),
(20, 2, 12, 100.00, '2025-06-24 19:11:10'),
(21, 2, 13, 100.00, '2025-06-24 19:11:10'),
(22, 8, 8, 14.00, '2025-06-24 19:11:23'),
(23, 8, 9, 14.00, '2025-06-24 19:11:23'),
(24, 8, 10, 99.00, '2025-06-24 19:11:23'),
(25, 8, 11, 99.00, '2025-06-24 19:11:23'),
(26, 8, 12, 99.00, '2025-06-24 19:11:23'),
(27, 8, 13, 99.00, '2025-06-24 19:11:23'),
(28, 6, 14, 50.00, '2025-06-24 19:28:51'),
(29, 6, 15, 80.00, '2025-06-24 19:28:51'),
(30, 6, 16, 100.00, '2025-06-24 19:28:51'),
(31, 4, 14, 40.00, '2025-06-24 19:29:03'),
(32, 4, 15, 70.00, '2025-06-24 19:29:03'),
(33, 4, 16, 90.00, '2025-06-24 19:29:03'),
(34, 2, 14, 42.00, '2025-06-24 19:29:13'),
(35, 2, 15, 72.00, '2025-06-24 19:29:13'),
(36, 2, 16, 92.00, '2025-06-24 19:29:13'),
(37, 8, 18, 13.00, '2025-06-24 20:18:22'),
(38, 8, 19, 86.00, '2025-06-24 20:18:22'),
(39, 8, 20, 64.00, '2025-06-24 20:18:22'),
(40, 8, 21, 96.00, '2025-06-24 20:18:22'),
(41, 8, 22, 77.00, '2025-06-24 20:18:22'),
(42, 8, 23, 15.00, '2025-06-24 20:18:22'),
(43, 7, 18, 15.00, '2025-06-24 20:18:46'),
(44, 7, 19, 76.00, '2025-06-24 20:18:46'),
(45, 7, 20, 86.00, '2025-06-24 20:18:46'),
(46, 7, 21, 77.00, '2025-06-24 20:18:46'),
(47, 7, 22, 66.00, '2025-06-24 20:18:46'),
(48, 7, 23, 15.00, '2025-06-24 20:18:46'),
(49, 6, 18, 15.00, '2025-06-24 20:19:00'),
(50, 6, 19, 97.00, '2025-06-24 20:19:00'),
(51, 6, 20, 77.00, '2025-06-24 20:19:00'),
(52, 6, 21, 98.00, '2025-06-24 20:19:00'),
(53, 6, 22, 78.00, '2025-06-24 20:19:00'),
(54, 6, 23, 13.00, '2025-06-24 20:19:00'),
(55, 2, 18, 15.00, '2025-06-24 20:19:12'),
(56, 2, 19, 98.00, '2025-06-24 20:19:12'),
(57, 2, 20, 87.00, '2025-06-24 20:19:12'),
(58, 2, 21, 66.00, '2025-06-24 20:19:12'),
(59, 2, 22, 87.00, '2025-06-24 20:19:12'),
(60, 2, 23, 15.00, '2025-06-24 20:19:12'),
(61, 4, 18, 15.00, '2025-06-24 20:20:03'),
(62, 4, 19, 87.00, '2025-06-24 20:20:03'),
(63, 4, 20, 67.00, '2025-06-24 20:20:03'),
(64, 4, 21, 76.00, '2025-06-24 20:20:03'),
(65, 4, 22, 76.00, '2025-06-24 20:20:03'),
(66, 4, 23, 15.00, '2025-06-24 20:20:03');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `password`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin', '$2y$10$PiBtpwj3bqTBZsvmfzlxaeYmJUp11e6OKaBq2t5LBiy324Xs9UnTa', 4, '2025-06-14 16:04:16', '2025-06-24 19:33:01'),
(3, 'mrnorizam', 'Mr Norizam', '$2y$10$Z6Opx5SbmdrHGdEK3XHLSOKs5ctc3avDRivjF/MisgM5UP.c/JS6e', 1, '2025-06-17 08:29:17', '2025-06-24 19:42:07'),
(10, 'A22EC0103', 'PUA ZHI YING', '$2y$10$/HX0Ic.ESl8JbABx9h7YNecYL8hfbJa0U5Vnn2FJGSZV.nmZAcST.', 2, '2025-06-17 17:07:46', '2025-06-24 19:42:11'),
(12, 'A22EC0106', 'TAM JIA HAO', '$2y$10$SgDhcHDzAdYtVB2bb.2eJu6sZAyh4.Kt68rU0FRJjPxvXbbMttGBS', 2, '2025-06-18 15:48:38', '2025-06-24 11:48:46'),
(14, 'wannsir', 'Prof Wan', '$2y$10$yVKIFl8AEcBmlIasF4eeT.aBJqBpHICJbkuhmKH2i2DpaeIWnSOnm', 1, '2025-06-23 16:32:36', '2025-06-24 18:41:29'),
(19, 'A22EC0108', 'TAN YOU CHUN', '$2y$10$UUSPNRgqHYn7DalOYsx4jeowAQdXIbbIsIP3X.8kv2rRIcSVNG6vi', 2, '2025-06-24 12:14:45', '2025-06-24 20:13:19'),
(23, 'A22EC0066', 'KUAN JI TONG', '$2y$10$cTqG3A8FLoA7HZpzL.V93OTGV9ATUrepJW3Zld5dxPttWdNwatl3W', 2, '2025-06-24 15:30:30', '2025-06-24 15:30:30'),
(24, 'drsitizaiton', 'Dr Siti Zaiton', '$2y$10$ClEUzvZDr96OSPr5ru5iBevVIWIjhR9IWHGmnGGx/YofqD0mFI42a', 3, '2025-06-24 18:14:55', '2025-06-24 18:14:55'),
(25, 'A22EC0102', 'OH KAI XUAN', '$2y$10$h3x5bMjprjh73i25aii9Cu4VFW/Vt4FpeLJgr2poOl4aSu009CkMG', 2, '2025-06-24 18:22:02', '2025-06-24 18:22:02'),
(26, 'drsim', 'Sim Hiew Moi', '$2y$10$zfV/r8gCc6wPIf5LnjkaoO6/9xHgSaH1Y2ZORyg11e7kkXEa7/qgm', 1, '2025-06-24 18:49:25', '2025-06-24 20:13:44'),
(28, 'wannsiradvisor', 'Dr Wan', '$2y$10$ygXlRvVsIhtxBIzGyg0tf.PifQEO/sBrN4mnF2K78IuECUeLljqkC', 3, '2025-06-24 19:43:45', '2025-06-24 19:43:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advisors`
--
ALTER TABLE `advisors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk1` (`user_id`);

--
-- Indexes for table `advisor_notes`
--
ALTER TABLE `advisor_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `advisor_notes_ibfk_1` (`advisor_id`),
  ADD KEY `advisor_notes_ibfk_2` (`student_id`);

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assessments_ibfk_1` (`section_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `enrollments_ibfk_2` (`section_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `related_assessment_id` (`related_assessment_id`),
  ADD KEY `related_section_id` (`related_section_id`);

--
-- Indexes for table `remark_requests`
--
ALTER TABLE `remark_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assessment_id` (`assessment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`role_name`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `sections_ibfk_2` (`lecturer_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matric_number` (`matric_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `students_ibfk_2` (`advisor_id`);

--
-- Indexes for table `student_assessment_marks`
--
ALTER TABLE `student_assessment_marks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`assessment_id`),
  ADD KEY `assessment_id` (`assessment_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advisors`
--
ALTER TABLE `advisors`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `advisor_notes`
--
ALTER TABLE `advisor_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `remark_requests`
--
ALTER TABLE `remark_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `student_assessment_marks`
--
ALTER TABLE `student_assessment_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advisors`
--
ALTER TABLE `advisors`
  ADD CONSTRAINT `fk1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `advisor_notes`
--
ALTER TABLE `advisor_notes`
  ADD CONSTRAINT `advisor_notes_ibfk_1` FOREIGN KEY (`advisor_id`) REFERENCES `advisors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `advisor_notes_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `assessments`
--
ALTER TABLE `assessments`
  ADD CONSTRAINT `assessments_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `lecturers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`related_assessment_id`) REFERENCES `assessments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`related_section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `remark_requests`
--
ALTER TABLE `remark_requests`
  ADD CONSTRAINT `remark_requests_ibfk_1` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `remark_requests_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sections_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`advisor_id`) REFERENCES `advisors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_assessment_marks`
--
ALTER TABLE `student_assessment_marks`
  ADD CONSTRAINT `student_assessment_marks_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_assessment_marks_ibfk_2` FOREIGN KEY (`assessment_id`) REFERENCES `assessments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
