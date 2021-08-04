CREATE TABLE `roles` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(255) NOT NULL,
 `description` text NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `users`(
    `id` INT(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    `role_id` INT(11) DEFAULT NULL,
    `username` VARCHAR(255) UNIQUE NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `tel` VARCHAR(25) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `profile_picture` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `users_ibfk_1` FOREIGN KEY(`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL ON UPDATE NO ACTION
);

INSERT INTO `roles`(`id`, `name`, `description`) 
VALUES  (1, 'Admin', 'Has authority of users and roles and permissions.' ), 
        (2, 'Author', 'Has full authority of own posts'), 
        (3, 'Editor', 'Has full authority over all posts');

CREATE TABLE `permissions` (
 `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
 `name` varchar(255) NOT NULL UNIQUE KEY,
 `description` text NOT NULL
);

CREATE TABLE `permission_role` (
 `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
 `role_id` int(11) NOT NULL,
 `permission_id` int(11) NOT NULL,
 KEY `role_id` (`role_id`),
 KEY `permission_id` (`permission_id`),
 CONSTRAINT `permission_role_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
 CONSTRAINT `permission_role_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`)
);




-- ======================================================

ALTER TABLE `users` ADD `post_role` ENUM('Author','Admin') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `products` ADD `published` TINYINT(1) NOT NULL AFTER `category`;
ALTER TABLE `products` ADD `client_id` INT(11) NOT NULL DEFAULT '1' AFTER `published`;
UPDATE `products` SET `published`=1 WHERE 1;
UPDATE `users` SET `post_role` = 'Admin' WHERE `users`.`id` = 1;