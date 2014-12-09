CREATE DATABASE task_manager;
 
USE task_manager;
 
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` text NOT NULL,
  `api_key` varchar(32) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
);
 
CREATE TABLE IF NOT EXISTS `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task` text NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_id` int(11),
  PRIMARY KEY (`id`),
  KEY `project_id`(`project_id`)
);
 
CREATE TABLE IF NOT EXISTS `user_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `task_id` (`task_id`)
);

CREATE TABLE IF NOT EXISTS `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_name` text NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `references` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` text,
  `description` text,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `QAs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text,
  `content` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `project_id` int(11),
  `user_id` int(11),
  `status` int DEFAULT 0,
  `answer` text,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`)
);

ALTER TABLE `tasks` ADD FOREIGN KEY (`project_id`) REFERENCES `task_manager`.`projects`(`id`)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `QAs` ADD FOREIGN KEY (`user_id`) REFERENCES `task_manager`.`users`(`id`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `QAs` ADD FOREIGN KEY (`project_id`) REFERENCES `task_manager`.`projects`(`id`)
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE  `user_tasks` ADD FOREIGN KEY (  `user_id` ) REFERENCES  `task_manager`.`users` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;
 
ALTER TABLE  `user_tasks` ADD FOREIGN KEY (  `task_id` ) REFERENCES  `task_manager`.`tasks` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;