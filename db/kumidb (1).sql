-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2024 at 12:34 AM
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
-- Database: `kumidb`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `answer_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `model_answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`answer_id`, `question_id`, `answer_text`, `is_correct`, `model_answer`) VALUES
(1, 1, '4', 1, NULL),
(2, 1, '5', 0, NULL),
(3, 1, '6', 0, NULL),
(4, 2, '15', 1, NULL),
(5, 2, '10', 0, NULL),
(6, 2, '20', 0, NULL),
(7, 5, 'True', 0, NULL),
(8, 5, 'False', 0, NULL),
(9, 8, 'forest', 0, NULL),
(10, 8, 'Newtons House', 0, NULL),
(11, 9, 'A frog', 0, NULL),
(12, 9, 'A dog', 0, NULL),
(13, 9, 'because God said so', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `name`, `description`, `date`, `created_at`) VALUES
(1, 'Midterm Deadline', 'Submission for midterm quizzes.', '2024-11-30', '2024-11-21 18:36:26'),
(2, 'Quiz Reminder', 'Reminder for upcoming quizzes.', '2024-12-01', '2024-11-21 18:36:26'),
(3, 'Group Quiz Reminder', 'Reminder for group quiz submission.', '2024-12-09', '2024-11-21 18:36:26'),
(4, 'Quiz Feedback Available', 'Feedback for recent quizzes is now available.', '2024-12-12', '2024-11-21 18:36:26');

-- --------------------------------------------------------

--
-- Table structure for table `groupmembers`
--

CREATE TABLE `groupmembers` (
  `group_member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groupmembers`
--

INSERT INTO `groupmembers` (`group_member_id`, `group_id`, `user_id`) VALUES
(1, 1, 2),
(2, 1, 5),
(3, 2, 5),
(4, 2, 9);

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE `groups` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`group_id`, `group_name`, `created_at`) VALUES
(1, 'Math Enthusiasts', '2024-11-21 18:02:15'),
(2, 'Science Wizards', '2024-11-21 18:02:15');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `question_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `type` enum('multiple_choice','true_false','short_answer') NOT NULL,
  `points` decimal(5,2) DEFAULT 1.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`question_id`, `quiz_id`, `question_text`, `type`, `points`, `created_at`) VALUES
(1, 1, 'What is 2 + 2?', 'multiple_choice', 1.00, '2024-11-21 18:00:49'),
(2, 1, 'What is 5 * 3?', 'multiple_choice', 1.00, '2024-11-21 18:00:49'),
(3, 2, 'Name a state of matter.', 'short_answer', 2.00, '2024-11-21 18:00:49'),
(4, 3, 'Who was the first president of the US?', 'short_answer', 2.00, '2024-11-21 18:00:49'),
(5, 5, 'DFS or BFS', 'multiple_choice', 1.00, '2024-11-22 22:58:53'),
(6, 5, 'Who created DFS', 'short_answer', 1.00, '2024-11-22 22:58:53'),
(7, 7, 'Who is Albert', 'short_answer', 1.00, '2024-11-22 23:12:45'),
(8, 7, 'Where was Albert', 'multiple_choice', 1.00, '2024-11-22 23:12:45'),
(9, 7, 'Why is Albert', 'multiple_choice', 1.00, '2024-11-22 23:12:45');

-- --------------------------------------------------------

--
-- Table structure for table `quizresults`
--

CREATE TABLE `quizresults` (
  `result_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `score` decimal(5,2) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizresults`
--

INSERT INTO `quizresults` (`result_id`, `quiz_id`, `user_id`, `group_id`, `score`, `submitted_at`) VALUES
(1, 1, 2, NULL, 95.00, '2024-11-21 18:02:25'),
(2, 1, 5, NULL, 88.00, '2024-11-21 18:02:25'),
(3, 2, NULL, 1, 85.00, '2024-11-21 18:02:25'),
(4, 2, NULL, 2, 90.00, '2024-11-21 18:02:25');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `quiz_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `mode` enum('individual','group','live','asynchronous') NOT NULL,
  `deadline` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `quiz_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`quiz_id`, `title`, `description`, `created_by`, `mode`, `deadline`, `created_at`, `quiz_code`) VALUES
(1, 'Math Basics', 'A basic math quiz.', 8, 'individual', '2024-12-01 23:59:59', '2024-11-21 17:57:01', NULL),
(2, 'Science Quiz', 'Test your science knowledge.', 8, 'group', '2024-12-10 23:59:59', '2024-11-21 17:57:01', NULL),
(3, 'History Quiz', 'Explore historical events.', 11, 'asynchronous', '2024-12-15 23:59:59', '2024-11-21 17:57:01', NULL),
(5, 'Quiz- DFS', 'This is a quiz to test students knowledge on DFS', 13, 'asynchronous', '2024-11-30 22:58:00', '2024-11-22 22:58:53', NULL),
(6, 'Quiz- DFS', 'This is a quiz to test students knowledge on DFS', 13, 'asynchronous', '2024-11-30 22:58:00', '2024-11-22 23:01:30', NULL),
(7, 'HISTORY 101 - Quiz 2 ', 'Basic Historic quiz to test basic historical concepts and data realities', 13, 'asynchronous', '2024-11-29 23:11:00', '2024-11-22 23:12:45', NULL),
(10, 'Untitled Quiz', '', 13, 'asynchronous', '0000-00-00 00:00:00', '2024-11-22 23:22:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `response_id` int(11) NOT NULL,
  `result_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer_id` int(11) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `text_response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`response_id`, `result_id`, `question_id`, `selected_answer_id`, `is_correct`, `text_response`) VALUES
(1, 1, 1, 1, 1, NULL),
(2, 1, 2, 4, 1, NULL),
(3, 2, 1, 2, 0, NULL),
(4, 2, 2, 5, 0, NULL),
(5, 3, 3, NULL, 1, NULL),
(6, 4, 3, NULL, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(2, 'nana', 'amoako', 'nana@amoako.com', '$2y$10$Y4UkGkmv8XsIkGkQdCuKdOeHEOx8pyYPhf92GE4p1ZURE5n38McMW', 'student', '2024-11-21 16:14:02', '2024-11-21 16:14:02'),
(5, 'Alice', 'Brown', 'alice.brown@example.com', 'password123', 'student', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(6, 'nana', 'amo', 'nan@amo.co', '$2y$10$bmA.cH9IWlMG6m4Llvveqe2wP333gywJOSb2rgYw0OrIciOGa.AcG', 'student', '2024-11-21 16:24:45', '2024-11-21 16:24:45'),
(7, 'John', 'Doe', 'john.doe@example.com', 'password123', 'student', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(8, 'Charlie', 'Davis', 'charlie.davis@example.com', 'password123', 'teacher', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(9, 'Jane', 'Smith', 'jane.smith@example.com', 'password123', 'student', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(11, 'Bob', 'Johnson', 'bob.johnson@example.com', 'password123', 'teacher', '2024-11-21 17:52:27', '2024-11-21 17:52:27'),
(12, 'teacher', 'nana', 'teacher@nana.com', '$2y$10$0ECLN5pCJ9.kaGmhgT76N.Vbnx.h9sZNeTMnxdhVVs6Y.NZmErzKG', 'teacher', '2024-11-21 18:58:17', '2024-11-21 18:58:17'),
(13, 'Caleb', 'Arthur', 'arthurcaleb12@gmail.com', '$2y$10$rdMsLGN0mTWPrKP.seJe0espj2cPjyd2D8PW7jdrisGMLuODWfMbm', 'teacher', '2024-11-22 21:13:10', '2024-11-22 21:13:10'),
(15, 'Caleb', 'Arthur', 'calebokwesiearthur@gmail.com', '$2y$10$nYIYxUc4MuGMyx1m4XFMAuJn36rkhXFoTHEKjKHbU/KvktHIizk6i', 'student', '2024-11-22 21:18:06', '2024-11-22 21:18:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`answer_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `groupmembers`
--
ALTER TABLE `groupmembers`
  ADD PRIMARY KEY (`group_member_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`question_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizresults`
--
ALTER TABLE `quizresults`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`quiz_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`response_id`),
  ADD KEY `result_id` (`result_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `selected_answer_id` (`selected_answer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `groupmembers`
--
ALTER TABLE `groupmembers`
  MODIFY `group_member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `quizresults`
--
ALTER TABLE `quizresults`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE;

--
-- Constraints for table `groupmembers`
--
ALTER TABLE `groupmembers`
  ADD CONSTRAINT `groupmembers_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `groupmembers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE;

--
-- Constraints for table `quizresults`
--
ALTER TABLE `quizresults`
  ADD CONSTRAINT `quizresults_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`quiz_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quizresults_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `quizresults_ibfk_3` FOREIGN KEY (`group_id`) REFERENCES `groups` (`group_id`) ON DELETE SET NULL;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`result_id`) REFERENCES `quizresults` (`result_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`question_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `responses_ibfk_3` FOREIGN KEY (`selected_answer_id`) REFERENCES `answers` (`answer_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
