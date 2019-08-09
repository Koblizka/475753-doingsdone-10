CREATE DATABASE IF NOT EXISTS `doingsdone`
DEFAULT CHARACTER SET `utf8`
DEFAULT COLLATE `utf8_general_ci`;

USE `doingsdone`;

CREATE TABLE IF NOT EXISTS `project` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT REFERENCES user ( id ),
  `name` VARCHAR ( 128 ) NOT NULL
);

CREATE TABLE IF NOT EXISTS `task` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT REFERENCES user ( id ),
  `project_id` INT REFERENCES project ( id ),
  `date_creation` DATE NOT NULL,
  `complete_status` TINYINT(1) DEFAULT 0,
  `name` VARCHAR ( 255 ) NOT NULL,
  `user_file` BLOB DEFAULT NULL,
  `deadline` DATE
);

CREATE TABLE IF NOT EXISTS `user` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `date_registration` DATE NOT NULL,
  `email` VARCHAR ( 128 ) NOT NULL UNIQUE,
  `name` VARCHAR ( 128 ) NOT NULL,
  `password` VARCHAR ( 64 )
);

CREATE INDEX u_email ON user( email );
CREATE INDEX u_name ON user( name );

CREATE INDEX d_complete ON task( deadline );
CREATE INDEX t_name ON task( name );

CREATE INDEX p_name ON project( name );
