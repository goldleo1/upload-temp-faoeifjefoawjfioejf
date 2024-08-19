create DATABASE knockOn;
use knockOn;

CREATE TABLE `posts` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `title` VARCHAR(100) NOT NULL,
    `description` TEXT NULL,
    `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updatedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `user_id` INT(11) DEFAULT NULL,
    PRIMARY KEY(`id`)
);

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `year` int(11) NOT NULL,
  `track` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `files` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `fileSizes` varchar(500) NOT NULL,
    `fileSizeSum` INT(11) NOT NULL,
    `fileCount` TINYINT(1) NOT NULL,
    `fileNames` varchar(500) NOT NULL,
    `uploadedAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `user_id` INT(11) NOT NULL,
    `post_id` INT(11) NOT NULL,
    PRIMARY KEY(`id`)
);

use mysql;
select user, host from user;
create user 'guest'@'localhost' identified by 'guest';
grant all privileges on knockOn.* to 'guest'@'localhost';
FLUSH PRIVILEGES;