-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 26, 2024 at 11:04 AM
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
-- Database: `kumidb`
--

-- --------------------------------------------------------

--
-- Table structure for table `Answers`
--

CREATE TABLE `Answers` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `order_position` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Answers`
--

INSERT INTO `Answers` (`answer_id`, `question_id`, `answer_text`, `is_correct`, `order_position`, `created_at`) VALUES
(34, 29, '1', 0, NULL, '2024-11-26 10:03:08'),
(35, 29, '2', 0, NULL, '2024-11-26 10:03:08'),
(36, 29, '1d', 1, NULL, '2024-11-26 10:03:08'),
(37, 30, '1', 0, NULL, '2024-11-26 10:03:11'),
(38, 30, '2', 0, NULL, '2024-11-26 10:03:11'),
(39, 30, '1d', 1, NULL, '2024-11-26 10:03:11'),
(40, 31, '1', 0, NULL, '2024-11-26 10:03:16'),
(41, 31, '2', 0, NULL, '2024-11-26 10:03:16'),
(42, 31, '1d', 1, NULL, '2024-11-26 10:03:16');

-- --------------------------------------------------------

--
-- Table structure for table `Events`
--

CREATE TABLE `Events` (
  `event_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Events`
--

INSERT INTO `Events` (`event_id`, `name`, `description`, `date`, `created_at`) VALUES
(1, 'Midterm Deadline', 'Submission for midterm quizzes.', '2024-11-30', '2024-11-21 18:36:26'),
(2, 'Quiz Reminder', 'Reminder for upcoming quizzes.', '2024-12-01', '2024-11-21 18:36:26'),
(3, 'Group Quiz Reminder', 'Reminder for group quiz submission.', '2024-12-09', '2024-11-21 18:36:26'),
(4, 'Quiz Feedback Available', 'Feedback for recent quizzes is now available.', '2024-12-12', '2024-11-21 18:36:26');

-- --------------------------------------------------------

--
-- Table structure for table `GroupMembers`
--

CREATE TABLE `GroupMembers` (
  `group_member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `GroupMembers`
--

INSERT INTO `GroupMembers` (`group_member_id`, `group_id`, `user_id`) VALUES
(1, 1, 2),
(2, 1, 5),
(3, 2, 5),
(4, 2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `Groups`
--

CREATE TABLE `Groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Groups`
--

INSERT INTO `Groups` (`group_id`, `group_name`, `created_at`) VALUES
(1, 'Math Enthusiasts', '2024-11-21 18:02:15'),
(2, 'Science Wizards', '2024-11-21 18:02:15');

-- --------------------------------------------------------

--
-- Table structure for table `Questions`
--

CREATE TABLE `Questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `type` enum('true_false','multiple_choice','multiple_answer','short_answer') NOT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `model_answer` text DEFAULT NULL,
  `order_position` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Questions`
--

INSERT INTO `Questions` (`question_id`, `quiz_id`, `question_text`, `type`, `points`, `model_answer`, `order_position`, `created_at`) VALUES
(29, 25, '1d', 'multiple_choice', 12, NULL, NULL, '2024-11-26 10:03:08'),
(30, 26, '1d', 'multiple_choice', 12, NULL, NULL, '2024-11-26 10:03:11'),
(31, 27, '1d', 'multiple_choice', 12, NULL, NULL, '2024-11-26 10:03:16');

-- --------------------------------------------------------

--
-- Table structure for table `QuizResults`
--

CREATE TABLE `QuizResults` (
  `result_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `score` decimal(5,2) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `QuizResults`
--

INSERT INTO `QuizResults` (`result_id`, `quiz_id`, `user_id`, `group_id`, `score`, `submitted_at`) VALUES
(1, 1, 2, NULL, 95.00, '2024-11-21 18:02:25'),
(2, 1, 5, NULL, 88.00, '2024-11-21 18:02:25'),
(3, 2, NULL, 1, 85.00, '2024-11-21 18:02:25'),
(4, 2, NULL, 2, 90.00, '2024-11-21 18:02:25'),
(5, 1, 6, NULL, 0.00, '2024-11-22 15:26:56'),
(6, 2, 6, NULL, 0.00, '2024-11-22 15:31:34'),
(8, 1, 13, NULL, 50.00, '2024-11-22 22:10:39'),
(9, 2, 13, NULL, 0.00, '2024-11-22 22:11:16'),
(10, 3, 13, NULL, 0.00, '2024-11-22 22:11:37'),
(11, 4, 2, NULL, 85.50, '2024-11-22 22:18:43'),
(12, 5, NULL, 1, 92.00, '2024-11-22 22:18:43'),
(13, 6, 5, NULL, 78.25, '2024-11-22 22:18:43'),
(14, 7, 7, NULL, 88.00, '2024-11-22 22:18:43'),
(15, 4, 2, NULL, 85.50, '2024-11-22 22:19:08'),
(16, 5, NULL, 1, 92.00, '2024-11-22 22:19:08'),
(17, 6, 5, NULL, 78.25, '2024-11-22 22:19:08'),
(18, 7, 7, NULL, 88.00, '2024-11-22 22:19:08'),
(20, 4, 13, NULL, 33.33, '2024-11-22 22:31:09'),
(21, 7, 15, NULL, 66.67, '2024-11-23 08:58:47'),
(22, 4, 17, NULL, 33.33, '2024-11-24 00:38:09');

-- --------------------------------------------------------

--
-- Table structure for table `Quizzes`
--

CREATE TABLE `Quizzes` (
  `quiz_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `mode` enum('individual','group','live','asynchronous') NOT NULL,
  `deadline` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `shuffle_questions` BOOLEAN DEFAULT FALSE,
  `shuffle_answers` BOOLEAN DEFAULT FALSE,
  `max_attempts` int(11) DEFAULT NULL,
  `time_limit` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Quizzes`
--

INSERT INTO `Quizzes` (`quiz_id`, `title`, `description`, `created_by`, `mode`, `deadline`, `created_at`, `shuffle_questions`, `shuffle_answers`, `max_attempts`, `time_limit`) VALUES
(1, 'Math Basics', 'A basic math quiz.', 8, 'individual', '2024-12-01 23:59:59', '2024-11-21 17:57:01', FALSE, FALSE, NULL, NULL),
(2, 'Science Quiz', 'Test your science knowledge.', 8, 'group', '2024-12-10 23:59:59', '2024-11-21 17:57:01', FALSE, FALSE, NULL, NULL),
(3, 'History Quiz', 'Explore historical events.', 11, 'asynchronous', '2024-12-15 23:59:59', '2024-11-21 17:57:01', FALSE, FALSE, NULL, NULL),
(4, 'Advanced Mathematics', 'Complex math problems for senior students', 8, 'individual', '2024-12-20 23:59:59', '2024-11-22 21:39:13', FALSE, FALSE, NULL, NULL),
(5, 'Physics Fundamentals', 'Group quiz on basic physics concepts', 11, 'group', '2024-12-25 23:59:59', '2024-11-22 21:39:13', FALSE, FALSE, NULL, NULL),
(6, 'Live Programming Test', 'Real-time coding assessment', 12, 'live', '2024-12-15 14:00:00', '2024-11-22 21:39:13', FALSE, FALSE, NULL, NULL),
(7, 'Literature Review', 'Analysis of classic literature', 8, 'asynchronous', '2024-12-30 23:59:59', '2024-11-22 21:39:13', FALSE, FALSE, NULL, NULL),
(8, 'qkj`', 'if', 19, 'individual', NULL, '2024-11-25 14:51:35', FALSE, FALSE, NULL, NULL),
(9, 'Test 3', 'Another quiz', 19, 'individual', '2024-11-30 16:30:00', '2024-11-25 16:17:57', FALSE, FALSE, NULL, NULL),
(13, 'Quiz 1 - 202', 'Another Quiz', 21, 'individual', '2024-11-28 02:47:00', '2024-11-25 23:44:55', FALSE, FALSE, NULL, NULL),
(14, 'Quiz Test 2', 'Another quiz', 21, 'individual', '2024-11-21 04:45:00', '2024-11-25 23:46:00', FALSE, FALSE, NULL, NULL),
(17, 'Test New Quiz Options', 'How is it looking', 22, 'individual', '2024-11-28 08:00:00', '2024-11-26 09:01:45', FALSE, FALSE, NULL, NULL),
(18, 'Test New Quiz Options', 'How is it looking', 22, 'individual', '2024-11-28 08:00:00', '2024-11-26 09:01:47', FALSE, FALSE, NULL, NULL),
(19, 'Hi', 'Test 2', 22, 'individual', '2024-11-08 12:02:00', '2024-11-26 09:04:33', FALSE, FALSE, NULL, NULL),
(20, 'Hiw', '1', 22, 'individual', '2024-11-26 09:17:00', '2024-11-26 09:12:34', FALSE, FALSE, NULL, NULL),
(21, 'Hiw', '1', 22, 'individual', '2024-11-26 09:17:00', '2024-11-26 09:45:37', FALSE, FALSE, NULL, NULL),
(22, 'Hiw', '1', 22, 'individual', '2024-11-26 09:17:00', '2024-11-26 09:45:41', FALSE, FALSE, NULL, NULL),
(23, 'FRANCES', 'HG', 22, 'individual', '2024-11-26 09:52:00', '2024-11-26 09:47:20', FALSE, FALSE, NULL, NULL),
(24, 'Hello', '13', 22, 'individual', '2024-11-26 09:05:00', '2024-11-26 10:00:21', FALSE, FALSE, NULL, NULL),
(25, '12', '13', 22, 'individual', '2024-11-26 10:07:00', '2024-11-26 10:03:08', FALSE, FALSE, NULL, NULL),
(26, '12', '13', 22, 'individual', '2024-11-26 10:07:00', '2024-11-26 10:03:11', FALSE, FALSE, NULL, NULL),
(27, '12', '13', 22, 'individual', '2024-11-26 10:07:00', '2024-11-26 10:03:16', FALSE, FALSE, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Responses`
--

CREATE TABLE `Responses` (
  `response_id` int(11) NOT NULL,
  `result_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer_id` int(11) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `text_response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(2, 'nana', 'amoako', 'nana@amoako.com', '$2y$10$Y4UkGkmv8XsIkGkQdCuKdOeHEOx8pyYPhf92GE4p1ZURE5n38McMW', 'student', '2024-11-21 16:14:02', '2024-11-21 16:14:02'),
(5, 'Alice', 'Brown', 'alice.brown@example.com', 'password123', 'student', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(6, 'nana', 'amo', 'nan@amo.co', '$2y$10$bmA.cH9IWlMG6m4Llvveqe2wP333gywJOSb2rgYw0OrIciOGa.AcG', 'student', '2024-11-21 16:24:45', '2024-11-21 16:24:45'),
(7, 'John', 'Doe', 'john.doe@example.com', 'password123', 'student', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(8, 'Charlie', 'Davis', 'charlie.davis@example.com', 'password123', 'teacher', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(9, 'Jane', 'Smith', 'jane.smith@example.com', 'password123', 'student', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(11, 'Bob', 'Johnson', 'bob.johnson@example.com', 'password123', 'teacher', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(12, 'teacher', 'nana', 'teacher@nana.com', '$2y$10$0ECLN5pCJ9.kaGmhgT76N.Vbnx.h9sZNeTMnxdhVVs6Y.NZmErzKG', 'teacher', '2024-11-21 18:58:17', '2024-11-21 18:58:17'),
(13, 'nana', 'test4', 'nanatest4@gmail.com', '$2y$10$SGEOeLsyhUTKqG4GDAghwOXiliPtqYSiEOJf8hivY9aXT/tDR1Zzq', 'student', '2024-11-22 22:10:12', '2024-11-22 22:10:12'),
(14, 'nana', 'teacher1', 'nana@teacher1.com', '$2y$10$wsZbxYv2eHg8Ml0MpbXXyOo61S86rss25irWEHKhVJtBZosOFqgui', 'teacher', '2024-11-22 23:41:09', '2024-11-22 23:41:09'),
(15, 'nana', 'test5', 'nanatest5@gmail.com', '$2y$10$0FRo6j4Z9C4rm9jlsa6FM.tps29VC..9u5L6Y3LT.AmOeEPIP/T8.', 'student', '2024-11-23 08:42:31', '2024-11-23 08:42:31'),
(16, 'Teacher', 'Nana', 'nana@teacher2.com', '$2y$10$csIHSt8jvH9lEBMmxkt3T.4EYKceQ6fhDdPH8s.PmC6iSEQCSq6Zy', 'teacher', '2024-11-24 00:34:13', '2024-11-24 00:34:13'),
(17, 'student1', 'test', 'student1@kumi.com', '$2y$10$vcxnXI2uy9LmYvFXuJoxWuHMYLLbf68EVtdFuzXL69T3WVM9JxAOa', 'student', '2024-11-24 00:37:52', '2024-11-24 00:37:52'),
(18, 'marge teach', 'nana', 'margeteach@nana.com', '$2y$10$bEWOiK9icdfwxlZHHHP8GuD0QkRi8Ecu4vmZbvZ4MbKPbZcMPS5t2', 'teacher', '2024-11-25 10:41:07', '2024-11-25 10:41:07'),
(19, 'nana', 'frances', 'nana.frances@email.com', '$2y$10$uysFFy7P35oSaD.8zHTIiODfqpxYGMWAOf6HMOX9ehzntTUME0Ki.', 'teacher', '2024-11-25 13:57:27', '2024-11-25 13:57:27'),
(20, 'Don', 'Elijah', 'donelijah@email.com', '$2y$10$3v7PQ7GQyg47xaP1n8bPVOxIN9cGvCkmNRZYGfZPiIbrTc7ffqLvC', 'teacher', '2024-11-25 20:52:14', '2024-11-25 20:52:14'),
(21, 'caleb', 'frances', 'calfran@nana.com', '$2y$10$CjLW75wHJVsXnJywMgXCAuyrobXLINkAbsWhFUxpWoP.TCA1qJp16', 'teacher', '2024-11-25 23:05:31', '2024-11-25 23:05:31'),
(22, 'newtest', 'page', 'newpage@test.com', '$2y$10$wzWUvoMubdNLsjLhgNE7gOOb/48MmETSGA340TUkjL39Uk8oAx/bi', 'teacher', '2024-11-26 09:00:13', '2024-11-26 09:00:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Answers`
--
ALTER TABLE `Answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `Events`
--
ALTER TABLE `Events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `GroupMembers`
--
ALTER TABLE `GroupMembers`
  ADD PRIMARY KEY (`group_member_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `Groups`
--
ALTER TABLE `Groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `Questions`
--
ALTER TABLE `Questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `QuizResults`
--
ALTER TABLE `QuizResults`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `Quizzes`
--
ALTER TABLE `Quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `Responses`
--
ALTER TABLE `Responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `result_id` (`result_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_answer_id` (`selected_answer_id`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Answers`
--
ALTER TABLE `Answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `Events`
--
ALTER TABLE `Events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `GroupMembers`
--
ALTER TABLE `GroupMembers`
  MODIFY `group_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Groups`
--
ALTER TABLE `Groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Questions`
--
ALTER TABLE `Questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `QuizResults`
--
ALTER TABLE `QuizResults`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `Quizzes`
--
ALTER TABLE `Quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `Responses`
--
ALTER TABLE `Responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Answers`
--
ALTER TABLE `Answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `Questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `GroupMembers`
--
ALTER TABLE `GroupMembers`
  ADD CONSTRAINT `groupmembers_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `Groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groupmembers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `Questions`
--
ALTER TABLE `Questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `Quizzes` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `QuizResults`
--
ALTER TABLE `QuizResults`
  ADD CONSTRAINT `quizresults_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `Quizzes` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizresults_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `Users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quizresults_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `Groups` (`group_id`) ON DELETE SET NULL;

--
-- Constraints for table `Quizzes`
--
ALTER TABLE `Quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `Users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `Responses`
--
ALTER TABLE `Responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`result_id`) REFERENCES `QuizResults` (`result_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `Questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `responses_ibfk_3` FOREIGN KEY (`selected_answer_id`) REFERENCES `Answers` (`answer_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
