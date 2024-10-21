-- Create the database
CREATE DATABASE IF NOT EXISTS todo_app;

-- Grant all permissions to the user 'todo_user' on the todo_app database
GRANT ALL PRIVILEGES ON todo_app.* TO 'todo_user'@'localhost' IDENTIFIED BY 'webpro1000';

-- Use the newly created database
USE todo_app;

-- Create the 'users' table
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create the 'tasks' table
CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_description` varchar(255) NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `task_type` varchar(50) NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Apply privileges immediately
FLUSH PRIVILEGES;
