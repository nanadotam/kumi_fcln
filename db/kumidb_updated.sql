-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 22, 2024 at 12:53 AM
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
  `is_correct` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Answers`
--

INSERT INTO `Answers` (`answer_id`, `question_id`, `answer_text`, `is_correct`) VALUES
(1, 1, '4', 1),
(2, 1, '5', 0),
(3, 1, '6', 0),
(4, 2, '15', 1),
(5, 2, '10', 0),
(6, 2, '20', 0);

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
  `points` decimal(5,2) DEFAULT 1.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Questions`
--

INSERT INTO `Questions` (`question_id`, `quiz_id`, `question_text`, `type`, `points`, `created_at`) VALUES
(1, 1, 'What is 2 + 2?', 'multiple_choice', 1.00, '2024-11-21 18:00:49'),
(2, 1, 'What is 5 * 3?', 'multiple_choice', 1.00, '2024-11-21 18:00:49'),
(3, 2, 'Name a state of matter.', 'short_answer', 2.00, '2024-11-21 18:00:49'),
(4, 3, 'Who was the first president of the US?', 'short_answer', 2.00, '2024-11-21 18:00:49');

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
(4, 2, NULL, 2, 90.00, '2024-11-21 18:02:25');

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
(3, 'History Quiz', 'Explore historical events.', 11, 'asynchronous', '2024-12-15 23:59:59', '2024-11-21 17:57:01');

-- --------------------------------------------------------

--
-- Table structure for table `Responses`
--

CREATE TABLE `Responses` (
  `response_id` int(11) NOT NULL,
  `result_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_answer_id` int(11) DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Responses`
--

INSERT INTO `Responses` (`response_id`, `result_id`, `question_id`, `selected_answer_id`, `is_correct`) VALUES
(1, 1, 1, 1, 1),
(2, 1, 2, 4, 1),
(3, 2, 1, 2, 0),
(4, 2, 2, 5, 0),
(5, 3, 3, NULL, 1),
(6, 4, 3, NULL, 1);

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
(12, 'teacher', 'nana', 'teacher@nana.com', '$2y$10$0ECLN5pCJ9.kaGmhgT76N.Vbnx.h9sZNeTMnxdhVVs6Y.NZmErzKG', 'teacher', '2024-11-21 18:58:17', '2024-11-21 18:58:17');

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
  MODIFY `answer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `QuizResults`
--
ALTER TABLE `QuizResults`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Quizzes`
--
ALTER TABLE `Quizzes`
  MODIFY `quiz_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Responses`
--
ALTER TABLE `Responses`
  MODIFY `response_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
