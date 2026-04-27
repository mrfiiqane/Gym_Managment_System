-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:4306:4306
-- Generation Time: Apr 27, 2026 at 10:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gym_managment_system`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_authorites_sp` (IN `_userId` VARCHAR(250) CHARSET utf8)   BEGIN

SELECT category.id category_id, category.name category_name, category.role category, system_links.id link_id, system_links.name link_name, system_actions.id action_id, system_actions.name action_name
FROM `user_authority`
LEFT JOIN system_actions on user_authority.action = system_actions.id
LEFT JOIN system_links on system_actions.link_id = system_links.id
LEFT JOIN category on system_links.category_id = category.id 
WHERE user_authority.user_id = _userId ORDER BY category.role, system_links.id, system_actions.id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `get_user_menu_sp` (IN `_userId` VARCHAR(250) CHARSET utf8)   BEGIN

SELECT category.id category_id, category.name category_name, category.role category, system_links.id link_id, system_links.name link_name, system_links.link
FROM `user_authority`
LEFT JOIN system_actions on user_authority.action = system_actions.id
LEFT JOIN system_links on system_actions.link_id = system_links.id
LEFT JOIN category on system_links.category_id = category.id 
WHERE user_authority.user_id = _userId GROUP BY system_links.id ORDER BY category.name, system_links.id, system_actions.id;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `login_sp` (IN `_identity` VARCHAR(250) CHARSET utf8)   BEGIN
    SELECT u.*, r.role_name 
    FROM users u 
    JOIN roles r ON u.role_id = r.id 
    WHERE u.username = _identity OR u.email = _identity 
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `read_all_categories_sp` (IN `_search` VARCHAR(250), IN `_limit` INT, IN `_offset` INT)   BEGIN
    DECLARE _total INT;
    SELECT COUNT(*) INTO _total FROM category WHERE name LIKE CONCAT('%', _search, '%');
    
    SELECT *, _total as TotalCount FROM category WHERE name LIKE CONCAT('%', _search, '%') ORDER BY id DESC LIMIT _limit OFFSET _offset;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `read_all_links_sp` (IN `_search` VARCHAR(250), IN `_limit` INT, IN `_offset` INT)   BEGIN
    DECLARE _total INT;
    SELECT COUNT(*) INTO _total FROM system_links WHERE name LIKE CONCAT('%', _search, '%');
    
    SELECT sl.*, c.name as category_name, _total as TotalCount FROM system_links sl 
    LEFT JOIN category c ON sl.category_id = c.id
    WHERE sl.name LIKE CONCAT('%', _search, '%') ORDER BY sl.id DESC LIMIT _limit OFFSET _offset;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `read_all_system_actions_sp` (IN `_search` VARCHAR(250), IN `_limit` INT, IN `_offset` INT)   BEGIN
    DECLARE _total INT;
    SELECT COUNT(*) INTO _total FROM system_actions WHERE name LIKE CONCAT('%', _search, '%');
    
    SELECT sa.*, sl.name as link_name, _total as TotalCount FROM system_actions sa 
    LEFT JOIN system_links sl ON sa.link_id = sl.id
    WHERE sa.name LIKE CONCAT('%', _search, '%') ORDER BY sa.id DESC LIMIT _limit OFFSET _offset;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `singup_sp` (IN `_id` VARCHAR(250) CHARSET utf8, IN `_full_name` VARCHAR(255) CHARSET utf8, IN `_username` VARCHAR(100) CHARSET utf8, IN `_email` VARCHAR(255) CHARSET utf8, IN `_phone` VARCHAR(20) CHARSET utf8, IN `_password` VARCHAR(255) CHARSET utf8, IN `_image` VARCHAR(255) CHARSET utf8, IN `_role_id` INT)   BEGIN
    INSERT INTO users (id, full_name, username, email, phone, password, image, role_id, status)
    VALUES (_id, _full_name, _username, _email, _phone, _password, _image, _role_id, 'Active');
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `role` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `icon`, `role`, `date`) VALUES
(1, 'dashboard', 'dashboard', 'Dashboard', '2026-04-15 17:35:43'),
(2, 'Academic Management', 'account_tree', 'Academic', '2026-04-19 07:21:51'),
(3, 'Report', 'report', 'Reports', '2026-04-19 07:22:33'),
(4, 'Finance', 'finance', 'Finance', '2026-04-19 07:23:15'),
(5, 'News', 'news', 'News', '2026-04-19 07:24:02'),
(6, 'System Management', 'manage_accounts', 'SystemManagement', '2026-04-19 07:25:10'),
(7, 'Support & Legals', 'support_agent', 'SupportLegal', '2026-04-19 10:15:04');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` varchar(250) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `otp_code`, `expires_at`, `used`, `created_at`) VALUES
(6, 'USR0002', '491746', '2026-04-15 13:37:42', 1, '2026-04-15 10:32:42'),
(8, 'USR0004', '371189', '2026-04-15 20:49:27', 1, '2026-04-15 17:44:27'),
(10, 'USR0003', '976098', '2026-04-19 09:55:58', 0, '2026-04-19 06:50:58');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `created_at`) VALUES
(1, 'Admin', 'System maamulka', '2026-03-25 16:31:10'),
(2, 'User', 'Isticmaale caadi ah', '2026-03-25 16:31:10');

-- --------------------------------------------------------

--
-- Table structure for table `system_actions`
--

CREATE TABLE `system_actions` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `action` varchar(250) NOT NULL,
  `link_id` int(11) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_actions`
--

INSERT INTO `system_actions` (`id`, `name`, `action`, `link_id`, `Date`) VALUES
(1, 'View Category', 'view_category', 1, '2026-04-19 10:23:36'),
(2, 'View System Links', 'view_system_links', 2, '2026-04-19 10:23:36'),
(3, 'View System Actions', 'view_system_actions', 3, '2026-04-19 10:23:36'),
(4, 'View User Authority', 'view_user_authority', 4, '2026-04-19 10:23:36'),
(5, 'View Fees Management', 'view_fees', 5, '2026-04-19 11:00:03'),
(6, 'View Recent Notices', 'view_notices', 6, '2026-04-19 11:02:41'),
(7, 'View System Info', 'view_info', 7, '2026-04-19 11:02:41');

-- --------------------------------------------------------

--
-- Stand-in structure for view `system_authority`
-- (See below for the actual view)
--
CREATE TABLE `system_authority` (
`id` int(11)
,`category` varchar(250)
,`icon` varchar(50)
,`role` varchar(250)
,`link_id` int(11)
,`name` varchar(250)
,`action_id` int(11)
,`action_name` varchar(250)
);

-- --------------------------------------------------------

--
-- Table structure for table `system_links`
--

CREATE TABLE `system_links` (
  `id` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  `link` varchar(250) NOT NULL,
  `category_id` int(11) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_links`
--

INSERT INTO `system_links` (`id`, `name`, `link`, `category_id`, `Date`) VALUES
(1, 'Category', 'category.php', 6, '2026-04-19 10:23:36'),
(2, 'System Links', 'system_link.php', 6, '2026-04-19 10:23:36'),
(3, 'System Actions', 'system_action.php', 6, '2026-04-19 10:23:36'),
(4, 'User Authority', 'user_authority.php', 6, '2026-04-19 10:23:36'),
(5, 'Fees Management', 'fees.php', 4, '2026-04-19 11:00:03'),
(6, 'Recent Notices', 'notices.php', 5, '2026-04-19 11:02:41'),
(7, 'System Info', 'info.php', 7, '2026-04-19 11:02:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(250) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `status` enum('Active','Pending','Block') DEFAULT 'Active',
  `image` varchar(255) DEFAULT 'default.png',
  `google_id` varchar(255) DEFAULT NULL,
  `auth_provider` enum('local','google') NOT NULL DEFAULT 'local',
  `role_id` int(11) DEFAULT 2,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `phone`, `password`, `status`, `image`, `google_id`, `auth_provider`, `role_id`, `created_at`) VALUES
('1', 'caasho adan', 'caasho', 'caashoadam@gmail.com', '+1090000000', '$2y$10$keLOpIRJykUjewHTm6Bb9eOYhMhF6Y/AWhAG7ygGILiHqXMcHmhti', 'Active', '1_b2ab.jpg', NULL, 'local', 2, '2026-04-12 13:57:30'),
('USR0001', 'ahmed jaamac', 'ahmed', 'ahmed@gmail.com', '+25290000000', '$2y$10$2lUQPki/bEf31cb2FPWZ5egUViXpk4cC4662YBo69h6xdbIAY8bWe', 'Active', 'USR0001_004a.png', NULL, 'local', 2, '2026-04-12 14:10:07'),
('USR0002', 'Sinkaaro Podcast', 'g_sinkaaropodcast40', 'sinkaaropodcast@gmail.com', NULL, '$2y$10$zIza7sYtVFCj9sEhZKIw9e8pmKT3Asza0TMi.CmIYoeLhIudOn2uS', 'Active', 'default.png', '114966606567960025774', 'google', 2, '2026-04-15 10:14:22'),
('USR0003', 'maxamed maxamuud ciise', 'maxamed064', 'maxamedfx064@gmail.com', '+252906816341', '$2y$10$TSUIcST9yTjLB3c8OWCYSOyFccfajGUWjUujsQIgbnhMefyWEEOd2', 'Active', 'USR0003_a33a.png', '101296219066999567681', 'google', 1, '2026-04-15 14:58:45'),
('USR0004', 'story story', 'g_storystorystorystory06490', 'storystorystorystory064@gmail.com', NULL, '$2y$10$.hHPp7H0Bc2lXBHsvoFRFeoOlWiVWEJKaY57/6RnaRADh7C64HKPe', 'Active', 'default.png', '102831353491257504714', 'google', 2, '2026-04-15 17:43:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_authority`
--

CREATE TABLE `user_authority` (
  `id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `action` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_authority`
--

INSERT INTO `user_authority` (`id`, `user_id`, `action`) VALUES
(5, 'USR0004', 'null'),
(12, 'USR0002', 'null'),
(28, 'USR0003', 'null'),
(29, 'USR0003', '5'),
(30, 'USR0003', 'null'),
(31, 'USR0003', '3'),
(32, 'USR0003', '1'),
(33, 'USR0003', '4'),
(34, 'USR0003', '2'),
(35, 'USR0003', 'null');

-- --------------------------------------------------------

--
-- Structure for view `system_authority`
--
DROP TABLE IF EXISTS `system_authority`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `system_authority`  AS SELECT `category`.`id` AS `id`, `category`.`name` AS `category`, `category`.`icon` AS `icon`, `category`.`role` AS `role`, `system_links`.`id` AS `link_id`, `system_links`.`name` AS `name`, `system_actions`.`id` AS `action_id`, `system_actions`.`name` AS `action_name` FROM ((`category` left join `system_links` on(`category`.`id` = `system_links`.`category_id`)) left join `system_actions` on(`system_links`.`id` = `system_actions`.`link_id`)) ORDER BY `category`.`role` ASC, `system_links`.`id` ASC, `system_actions`.`id` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `system_actions`
--
ALTER TABLE `system_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `action_link` (`link_id`);

--
-- Indexes for table `system_links`
--
ALTER TABLE `system_links`
  ADD PRIMARY KEY (`id`),
  ADD KEY `system_link_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `user_authority`
--
ALTER TABLE `user_authority`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_actions`
--
ALTER TABLE `system_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `system_links`
--
ALTER TABLE `system_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_authority`
--
ALTER TABLE `user_authority`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `pr_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `system_links`
--
ALTER TABLE `system_links`
  ADD CONSTRAINT `system_link_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
