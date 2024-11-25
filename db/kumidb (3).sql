-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 25, 2024 at 03:40 PM
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
  `model_answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Answers`
--

INSERT INTO `Answers` (`answer_id`, `question_id`, `answer_text`, `is_correct`, `model_answer`) VALUES
(1, 1, '4', 1, NULL),
(2, 1, '5', 0, NULL),
(3, 1, '6', 0, NULL),
(4, 2, '15', 1, NULL),
(5, 2, '10', 0, NULL),
(6, 2, '20', 0, NULL),
(7, 5, 'x = -4 and x = 1.5', 1, 'The correct solution using quadratic formula'),
(8, 5, 'x = 4 and x = -1.5', 0, NULL),
(9, 5, 'x = 2 and x = -3', 0, NULL),
(10, 5, 'x = 3 and x = -2', 0, NULL),
(11, 8, 'Newton (N)', 1, 'The SI unit of force is Newton'),
(12, 8, 'Kilogram (kg)', 0, NULL),
(13, 8, 'Joule (J)', 0, NULL),
(14, 8, 'Pascal (Pa)', 0, NULL),
(15, 11, 'TypeError', 1, 'Cannot concatenate string and integer'),
(16, 11, '22', 0, NULL),
(17, 11, '4', 0, NULL),
(18, 11, 'undefined', 0, NULL),
(19, 14, 'Jane Austen', 1, NULL),
(20, 14, 'Emily Bronte', 0, NULL),
(21, 14, 'Charlotte Bronte', 0, NULL),
(22, 14, 'Virginia Woolf', 0, NULL),
(23, 5, 'Placeholder for missing answer 17', 0, NULL),
(24, 8, 'Placeholder for missing answer 21', 0, NULL),
(25, 11, 'Placeholder for missing answer 25', 0, NULL);

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
  `type` enum('multiple_choice','true_false','short_answer') NOT NULL,
  `points` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `question_type` enum('true_false','multiple_choice','multiple_answer','short_answer') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Questions`
--

INSERT INTO `Questions` (`question_id`, `quiz_id`, `question_text`, `type`, `points`, `created_at`, `question_type`) VALUES
(1, 1, 'What is 2 + 2?', 'multiple_choice', 1, '2024-11-21 18:00:49', 'true_false'),
(2, 1, 'What is 5 * 3?', 'multiple_choice', 1, '2024-11-21 18:00:49', 'true_false'),
(3, 2, 'Name a state of matter.', 'short_answer', 2, '2024-11-21 18:00:49', 'true_false'),
(4, 3, 'Who was the first president of the US?', 'short_answer', 2, '2024-11-21 18:00:49', 'true_false'),
(5, 4, 'Solve for x: 2x² + 5x - 12 = 0', 'multiple_choice', 3, '2024-11-22 21:42:40', 'true_false'),
(6, 4, 'Is the square root of a negative number a real number?', 'true_false', 1, '2024-11-22 21:42:40', 'true_false'),
(7, 4, 'Explain the concept of logarithms', 'short_answer', 3, '2024-11-22 21:42:40', 'true_false'),
(8, 5, 'What is the SI unit of force?', 'multiple_choice', 1, '2024-11-22 21:42:40', 'true_false'),
(9, 5, 'Calculate the acceleration of an object with mass 10kg under 20N force', 'short_answer', 3, '2024-11-22 21:42:40', 'true_false'),
(10, 5, 'True or False: Weight and mass are the same thing', 'true_false', 1, '2024-11-22 21:42:40', 'true_false'),
(11, 6, 'What is the output of: print(2 + \"2\")?', 'multiple_choice', 2, '2024-11-22 21:42:40', 'true_false'),
(12, 6, 'Write a function to check if a string is palindrome', 'short_answer', 3, '2024-11-22 21:42:40', 'true_false'),
(13, 6, 'Explain the difference between == and === in JavaScript', 'short_answer', 2, '2024-11-22 21:42:40', 'true_false'),
(14, 7, 'Who wrote \"Pride and Prejudice\"?', 'multiple_choice', 1, '2024-11-22 21:42:40', 'true_false'),
(15, 7, 'Analyze the main theme of \"1984\" by George Orwell', 'short_answer', 4, '2024-11-22 21:42:40', 'true_false'),
(16, 7, 'Is \"The Great Gatsby\" set in the 1920s?', 'true_false', 1, '2024-11-22 21:42:40', 'true_false');

-- --------------------------------------------------------

--
-- Table structure for table `question_options`
--

CREATE TABLE `question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Quizzes`
--

INSERT INTO `Quizzes` (`quiz_id`, `title`, `description`, `created_by`, `mode`, `deadline`, `created_at`) VALUES
(1, 'Math Basics', 'A basic math quiz.', 8, 'individual', '2024-12-01 23:59:59', '2024-11-21 17:57:01'),
(2, 'Science Quiz', 'Test your science knowledge.', 8, 'group', '2024-12-10 23:59:59', '2024-11-21 17:57:01'),
(3, 'History Quiz', 'Explore historical events.', 11, 'asynchronous', '2024-12-15 23:59:59', '2024-11-21 17:57:01'),
(4, 'Advanced Mathematics', 'Complex math problems for senior students', 8, 'individual', '2024-12-20 23:59:59', '2024-11-22 21:39:13'),
(5, 'Physics Fundamentals', 'Group quiz on basic physics concepts', 11, 'group', '2024-12-25 23:59:59', '2024-11-22 21:39:13'),
(6, 'Live Programming Test', 'Real-time coding assessment', 12, 'live', '2024-12-15 14:00:00', '2024-11-22 21:39:13'),
(7, 'Literature Review', 'Analysis of classic literature', 8, 'asynchronous', '2024-12-30 23:59:59', '2024-11-22 21:39:13');

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

--
-- Dumping data for table `Responses`
--

INSERT INTO `Responses` (`response_id`, `result_id`, `question_id`, `selected_answer_id`, `is_correct`, `text_response`) VALUES
(1, 1, 1, 1, 1, NULL),
(2, 1, 2, 4, 1, NULL),
(3, 2, 1, 2, 0, NULL),
(4, 2, 2, 5, 0, NULL),
(5, 3, 3, NULL, 1, NULL),
(6, 4, 3, NULL, 1, NULL),
(7, 5, 1, 1, 0, NULL),
(8, 5, 2, 4, 0, NULL),
(9, 8, 1, 1, 1, NULL),
(10, 8, 2, 5, 0, NULL),
(11, 9, 3, NULL, 0, 'Gas'),
(12, 10, 4, NULL, 0, 'George Washington'),
(34, 9, 5, 17, 1, NULL),
(35, 9, 6, NULL, 1, 'No, it is an imaginary number'),
(36, 9, 7, NULL, NULL, 'Logarithms are the inverse of exponential functions...'),
(37, 10, 8, 21, 1, NULL),
(38, 10, 9, NULL, NULL, 'a = F/m = 20N/10kg = 2 m/s²'),
(39, 11, 11, 25, 1, NULL),
(40, 11, 12, NULL, NULL, 'function isPalindrome(str) { return str === str.split(\"\").reverse().join(\"\"); }'),
(44, 20, 5, 7, 1, NULL),
(45, 20, 6, NULL, 0, NULL),
(46, 20, 7, NULL, 0, 'ii'),
(47, 21, 14, 19, 1, NULL),
(48, 21, 15, NULL, 0, 'kd'),
(49, 21, 16, NULL, 0, NULL),
(50, 22, 5, 7, 1, NULL),
(51, 22, 6, NULL, 0, NULL),
(52, 22, 7, NULL, 0, 'qc');

-- --------------------------------------------------------

--
-- Table structure for table `short_answer_details`
--

CREATE TABLE `short_answer_details` (
  `question_id` int(11) NOT NULL,
  `model_answer` text NOT NULL
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
(19, 'nana', 'frances', 'nana.frances@email.com', '$2y$10$uysFFy7P35oSaD.8zHTIiODfqpxYGMWAOf6HMOX9ehzntTUME0Ki.', 'teacher', '2024-11-25 13:57:27', '2024-11-25 13:57:27');

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
-- Indexes for table `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

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
-- Indexes for table `short_answer_details`
--
ALTER TABLE `short_answer_details`
  ADD PRIMARY KEY (`question_id`);

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
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

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
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `QuizResults`
--
ALTER TABLE `QuizResults`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `Quizzes`
--
ALTER TABLE `Quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Responses`
--
ALTER TABLE `Responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

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
-- Constraints for table `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `question_options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `Questions` (`question_id`) ON DELETE CASCADE;

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

--
-- Constraints for table `short_answer_details`
--
ALTER TABLE `short_answer_details`
  ADD CONSTRAINT `short_answer_details_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `Questions` (`question_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
