-- Panic Button Application Database Schema
-- Version 1.0
--
-- This script creates the necessary tables for the application to run.
-- It's designed for MySQL/MariaDB.

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('siswa','guru') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `students`
--
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `class` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `teachers`
--
CREATE TABLE `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `reports`
--
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_user_id` int(11) NOT NULL,
  `report_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('Belum Diproses','Dalam Penanganan','Selesai') NOT NULL DEFAULT 'Belum Diproses',
  PRIMARY KEY (`id`),
  KEY `student_user_id` (`student_user_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`student_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Note on User Creation:
-- After setting up the database, please run the user generation script from your terminal
-- to create the initial admin and student accounts:
--
-- php util/generate_users.php
--
-- This script will create a default 'admin' and 'siswa' user with a default password.
-- It is highly recommended to change these passwords after your first login.
--
