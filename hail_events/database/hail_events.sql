-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 03 ديسمبر 2025 الساعة 23:59
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hail_events`
--

-- --------------------------------------------------------

--
-- بنية الجدول `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) DEFAULT NULL,
  `color` varchar(7) DEFAULT '#00A878',
  `icon` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `color`, `icon`, `created_at`) VALUES
(1, 'Music', 'music', '#FF6B6B', NULL, '2025-11-30 17:51:57'),
(2, 'Sports', 'sports', '#4ECDC4', NULL, '2025-11-30 17:51:57'),
(3, 'Technology', 'technology', '#45B7D1', NULL, '2025-11-30 17:51:57'),
(4, 'Business', 'business', '#FFA07A', NULL, '2025-11-30 17:51:57'),
(5, 'Arts & Culture', 'arts-culture', '#98D8C8', NULL, '2025-11-30 17:51:57'),
(6, 'Education', 'education', '#F7DC6F', NULL, '2025-11-30 17:51:57'),
(7, 'Health & Wellness', 'health-wellness', '#BB8FCE', NULL, '2025-11-30 17:51:57'),
(8, 'Food & Drink', 'food-drink', '#F8B88B', NULL, '2025-11-30 17:51:57'),
(9, 'Kids & Family', 'kids-family', '#85C1E2', NULL, '2025-11-30 17:51:57'),
(10, 'Networking', 'networking', '#A9DFBF', NULL, '2025-11-30 17:51:57'),
(12, 'z', 'z', '#3d8a74', NULL, '2025-11-30 19:43:59');

-- --------------------------------------------------------

--
-- بنية الجدول `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `short_description` varchar(300) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `venue_id` int(11) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT 0.00,
  `capacity` int(11) DEFAULT NULL,
  `registration_type` enum('free','registration','paid') DEFAULT 'free',
  `status` enum('draft','pending','published','cancelled') DEFAULT 'draft',
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`images`)),
  `views` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `events`
--

INSERT INTO `events` (`id`, `organizer_id`, `title`, `slug`, `short_description`, `description`, `category_id`, `start_datetime`, `end_datetime`, `venue_id`, `price`, `capacity`, `registration_type`, `status`, `images`, `views`, `created_at`, `updated_at`) VALUES
(1, 1, 'Hail Music Festival 2024', 'hail-music-festival-2024', 'Experience the best local and international music artists', 'Join us for an amazing music festival featuring top artists from around the world. Enjoy live performances, food, and entertainment for the whole family.', 1, '2024-12-15 18:00:00', '2024-12-15 23:00:00', 1, 75.00, 1000, 'paid', 'published', NULL, 246, '2025-11-30 17:51:57', '2025-11-30 18:05:55'),
(2, 1, 'Jazz Night at Convention Center', 'jazz-night-convention', 'Smooth jazz performances by renowned musicians', 'Experience an evening of smooth jazz with performances by internationally acclaimed musicians. Enjoy cocktails and dinner.', 1, '2024-12-20 20:00:00', '2024-12-20 23:30:00', 1, 50.00, 300, 'paid', 'published', NULL, 160, '2025-11-30 17:51:57', '2025-12-04 01:45:24'),
(3, 3, 'Hail Marathon 2024', 'hail-marathon-2024', 'Join thousands of runners in the annual Hail Marathon', 'The Hail Marathon is back! Run 42km through the beautiful streets of Hail. All fitness levels welcome. Registration includes race kit and refreshments.', 2, '2024-12-25 06:00:00', '2024-12-25 12:00:00', 2, 30.00, 5000, 'paid', 'published', NULL, 523, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(4, 3, 'Football Tournament', 'football-tournament-2024', 'Amateur football tournament with exciting prizes', 'Teams of 11 players compete in this exciting football tournament. Winners receive cash prizes and trophies.', 2, '2024-12-28 08:00:00', '2024-12-28 18:00:00', 2, 0.00, 500, 'free', 'published', NULL, 312, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(5, 1, 'Tech Conference 2024', 'tech-conference-2024', 'Latest trends in technology and innovation', 'Join industry leaders and innovators for discussions on AI, blockchain, cloud computing, and more. Includes networking sessions and workshops.', 3, '2025-01-10 09:00:00', '2025-01-10 17:00:00', 1, 100.00, 800, 'paid', 'published', NULL, 678, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(6, 3, 'Entrepreneurship Workshop', 'entrepreneurship-workshop', 'Learn how to start and grow your business', 'A comprehensive workshop covering business planning, funding, marketing, and scaling. Perfect for aspiring entrepreneurs.', 4, '2025-01-15 10:00:00', '2025-01-15 16:00:00', 1, 40.00, 200, 'paid', 'published', NULL, 235, '2025-11-30 17:51:57', '2025-11-30 19:54:15'),
(7, 1, 'Traditional Art Exhibition', 'traditional-art-exhibition', 'Showcase of local and regional traditional art', 'Discover beautiful traditional artworks from local and regional artists. Includes live demonstrations and artist meet-and-greets.', 5, '2025-01-20 10:00:00', '2025-01-20 20:00:00', 3, 0.00, 1000, 'free', 'published', NULL, 457, '2025-11-30 17:51:57', '2025-11-30 17:59:51'),
(8, 3, 'Digital Marketing Masterclass', 'digital-marketing-masterclass', 'Master modern digital marketing strategies', 'Learn SEO, social media marketing, content strategy, and analytics from industry experts. Hands-on training with real examples.', 6, '2025-01-25 14:00:00', '2025-01-25 18:00:00', 1, 60.00, 150, 'paid', 'published', NULL, 189, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(9, 1, 'Yoga & Meditation Retreat', 'yoga-meditation-retreat', 'Relax and rejuvenate with yoga and meditation', 'A full day retreat focused on wellness, yoga, meditation, and healthy living. Includes meals and relaxation sessions.', 7, '2025-02-01 07:00:00', '2025-02-01 18:00:00', 4, 80.00, 100, 'paid', 'published', NULL, 267, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(10, 3, 'International Food Festival', 'international-food-festival', 'Taste cuisines from around the world', 'Experience authentic international cuisines from various countries. Live cooking demonstrations and cultural performances.', 8, '2025-02-05 17:00:00', '2025-02-05 23:00:00', 1, 45.00, 500, 'paid', 'published', NULL, 346, '2025-11-30 17:51:57', '2025-11-30 19:34:55');

-- --------------------------------------------------------

--
-- Stand-in structure for view `event_statistics`
-- (See below for the actual view)
--
CREATE TABLE `event_statistics` (
`id` int(11)
,`title` varchar(255)
,`total_registrations` bigint(21)
,`total_revenue` decimal(30,2)
,`average_rating` decimal(14,4)
,`total_reviews` bigint(21)
);

-- --------------------------------------------------------

--
-- بنية الجدول `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `type`, `payload`, `is_read`, `created_at`) VALUES
(1, 2, 'event_reminder', '{\"event_id\": 1, \"event_title\": \"Hail Music Festival 2024\", \"message\": \"Your event is coming up soon!\"}', 0, '2025-11-30 17:51:57'),
(2, 4, 'registration_confirmed', '{\"event_id\": 3, \"event_title\": \"Hail Marathon 2024\", \"message\": \"Your registration is confirmed\"}', 1, '2025-11-30 17:51:57'),
(3, 2, 'new_review', '{\"event_id\": 1, \"reviewer\": \"Fatima Al-Dosari\", \"rating\": 5, \"message\": \"New review on your event\"}', 0, '2025-11-30 17:51:57');

-- --------------------------------------------------------

--
-- بنية الجدول `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ticket_type` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `amount_paid` decimal(8,2) DEFAULT NULL,
  `status` enum('confirmed','pending','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `registrations`
--

INSERT INTO `registrations` (`id`, `event_id`, `user_id`, `ticket_type`, `quantity`, `amount_paid`, `status`, `created_at`) VALUES
(1, 1, 2, 'General', 2, 150.00, 'confirmed', '2025-11-30 17:51:57'),
(2, 1, 4, 'VIP', 1, 100.00, 'confirmed', '2025-11-30 17:51:57'),
(3, 2, 2, 'General', 1, 50.00, 'confirmed', '2025-11-30 17:51:57'),
(4, 3, 4, 'General', 1, 30.00, 'confirmed', '2025-11-30 17:51:57'),
(5, 5, 2, 'Standard', 1, 100.00, 'confirmed', '2025-11-30 17:51:57'),
(6, 6, 4, 'General', 1, 40.00, 'confirmed', '2025-11-30 17:51:57'),
(7, 8, 2, 'Premium', 1, 80.00, 'confirmed', '2025-11-30 17:51:57'),
(8, 9, 4, 'General', 2, 90.00, 'confirmed', '2025-11-30 17:51:57'),
(9, 7, 3, 'VIP', 1, 0.00, 'confirmed', '2025-11-30 18:00:03');

-- --------------------------------------------------------

--
-- بنية الجدول `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `reviews`
--

INSERT INTO `reviews` (`id`, `event_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 2, 5, 'Amazing event! Great music and organization. Highly recommended!', '2025-11-30 17:51:57'),
(2, 1, 4, 4, 'Good event overall. Could improve the parking situation.', '2025-11-30 17:51:57'),
(3, 2, 2, 5, 'Best jazz night ever! The musicians were fantastic.', '2025-11-30 17:51:57'),
(4, 3, 4, 5, 'Great marathon experience. Well organized and fun!', '2025-11-30 17:51:57'),
(5, 5, 2, 4, 'Informative tech conference. Learned a lot from the speakers.', '2025-11-30 17:51:57'),
(6, 8, 2, 5, 'Relaxing and rejuvenating yoga retreat. Highly recommended!', '2025-11-30 17:51:57'),
(7, 9, 4, 4, 'Good food festival. Would have liked more variety.', '2025-11-30 17:51:57'),
(8, 2, 5, 5, 'good', '2025-12-04 01:45:24');

-- --------------------------------------------------------

--
-- بنية الجدول `saved_events`
--

CREATE TABLE `saved_events` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `saved_events`
--

INSERT INTO `saved_events` (`id`, `user_id`, `event_id`, `created_at`) VALUES
(1, 2, 1, '2025-11-30 17:51:57'),
(2, 2, 2, '2025-11-30 17:51:57'),
(3, 2, 5, '2025-11-30 17:51:57'),
(4, 4, 3, '2025-11-30 17:51:57'),
(5, 4, 6, '2025-11-30 17:51:57'),
(6, 4, 9, '2025-11-30 17:51:57'),
(7, 2, 10, '2025-11-30 19:35:02'),
(8, 5, 2, '2025-12-04 01:45:30');

-- --------------------------------------------------------

--
-- Stand-in structure for view `upcoming_events`
-- (See below for the actual view)
--
CREATE TABLE `upcoming_events` (
`id` int(11)
,`organizer_id` int(11)
,`title` varchar(255)
,`slug` varchar(255)
,`short_description` varchar(300)
,`description` longtext
,`category_id` int(11)
,`start_datetime` datetime
,`end_datetime` datetime
,`venue_id` int(11)
,`price` decimal(8,2)
,`capacity` int(11)
,`registration_type` enum('free','registration','paid')
,`status` enum('draft','pending','published','cancelled')
,`images` longtext
,`views` int(11)
,`created_at` datetime
,`updated_at` datetime
,`category_name` varchar(100)
,`venue_name` varchar(255)
,`organizer_name` varchar(150)
);

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('user','organizer','admin') DEFAULT 'user',
  `phone` varchar(30) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `phone`, `avatar`, `preferences`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@hailevents.com', '$2y$10$zxIhg9B6VoLIQqFrYUqI2eis.fFDbXKIKqIYEmQNkSzmmGYvvUeui', 'admin', '+966 50 000 0000', NULL, NULL, '2025-11-30 17:51:57', '2025-12-04 01:39:19'),
(2, 'Ahmed Al-Harbi', 'ahmed@example.com', '$2y$10$zxIhg9B6VoLIQqFrYUqI2eis.fFDbXKIKqIYEmQNkSzmmGYvvUeui', 'organizer', '+966 50 123 4567', NULL, NULL, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(3, 'Fatima Al-Dosari', 'fatima@example.com', '$2y$10$zxIhg9B6VoLIQqFrYUqI2eis.fFDbXKIKqIYEmQNkSzmmGYvvUeui', 'user', '+966 50 234 5678', NULL, NULL, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(4, 'Mohammed Al-Rashid', 'mohammed@example.com', '$2y$10$zxIhg9B6VoLIQqFrYUqI2eis.fFDbXKIKqIYEmQNkSzmmGYvvUeui', 'organizer', '+966 50 345 6789', NULL, NULL, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(5, 'Noor Al-Otaibi', 'noor@example.com', '$2y$10$zxIhg9B6VoLIQqFrYUqI2eis.fFDbXKIKqIYEmQNkSzmmGYvvUeui', 'user', '+966 50 456 7890', NULL, NULL, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(6, 'Samir Al-Shammari', 'samir@example.com', '$2y$10$zxIhg9B6VoLIQqFrYUqI2eis.fFDbXKIKqIYEmQNkSzmmGYvvUeui', 'organizer', '+966 50 567 8901', NULL, NULL, '2025-11-30 17:51:57', '2025-11-30 17:51:57'),
(7, 'Ali Al-Harbi', 'admin@example.com', '$2y$10$zxIhg9B6VoLIQqFrYUqI2eis.fFDbXKIKqIYEmQNkSzmmGYvvUeui', 'admin', '+966 50 223 4567', NULL, NULL, '2025-11-30 18:03:37', '2025-11-30 18:03:37');

-- --------------------------------------------------------

--
-- بنية الجدول `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- إرجاع أو استيراد بيانات الجدول `venues`
--

INSERT INTO `venues` (`id`, `name`, `address`, `lat`, `lng`, `contact_info`, `created_at`) VALUES
(1, 'Hail Convention Center', 'King Fahd Road, Hail, Saudi Arabia', 27.5166000, 41.7208000, '+966 16 534 0000', '2025-11-30 17:51:57'),
(2, 'Al-Qassim Sports Hall', 'Prince Saud Al-Abdulaziz Road, Hail', 27.5200000, 41.7250000, '+966 16 540 0000', '2025-11-30 17:51:57'),
(3, 'Hail Exhibition Center', 'Al-Malik Road, Hail', 27.5180000, 41.7200000, '+966 16 535 0000', '2025-11-30 17:51:57'),
(4, 'Prince Abdul Majeed Hall', 'Downtown Hail', 27.5160000, 41.7220000, '+966 16 530 0000', '2025-11-30 17:51:57'),
(5, 'Hail Cultural Center', 'Cultural District, Hail', 27.5140000, 41.7180000, '+966 16 536 0000', '2025-11-30 17:51:57'),
(6, 'ali Mohammed', 'Hail', 27.2489000, 41.2475000, '+966846245', '2025-11-30 19:43:35');

-- --------------------------------------------------------

--
-- Structure for view `event_statistics`
--
DROP TABLE IF EXISTS `event_statistics`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `event_statistics`  AS SELECT `e`.`id` AS `id`, `e`.`title` AS `title`, count(`r`.`id`) AS `total_registrations`, sum(`r`.`amount_paid`) AS `total_revenue`, avg(`rv`.`rating`) AS `average_rating`, count(distinct `rv`.`id`) AS `total_reviews` FROM ((`events` `e` left join `registrations` `r` on(`e`.`id` = `r`.`event_id` and `r`.`status` = 'confirmed')) left join `reviews` `rv` on(`e`.`id` = `rv`.`event_id`)) WHERE `e`.`status` = 'published' GROUP BY `e`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `upcoming_events`
--
DROP TABLE IF EXISTS `upcoming_events`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `upcoming_events`  AS SELECT `e`.`id` AS `id`, `e`.`organizer_id` AS `organizer_id`, `e`.`title` AS `title`, `e`.`slug` AS `slug`, `e`.`short_description` AS `short_description`, `e`.`description` AS `description`, `e`.`category_id` AS `category_id`, `e`.`start_datetime` AS `start_datetime`, `e`.`end_datetime` AS `end_datetime`, `e`.`venue_id` AS `venue_id`, `e`.`price` AS `price`, `e`.`capacity` AS `capacity`, `e`.`registration_type` AS `registration_type`, `e`.`status` AS `status`, `e`.`images` AS `images`, `e`.`views` AS `views`, `e`.`created_at` AS `created_at`, `e`.`updated_at` AS `updated_at`, `c`.`name` AS `category_name`, `v`.`name` AS `venue_name`, `u`.`name` AS `organizer_name` FROM (((`events` `e` left join `categories` `c` on(`e`.`category_id` = `c`.`id`)) left join `venues` `v` on(`e`.`venue_id` = `v`.`id`)) left join `users` `u` on(`e`.`organizer_id` = `u`.`id`)) WHERE `e`.`status` = 'published' AND `e`.`start_datetime` > current_timestamp() ORDER BY `e`.`start_datetime` ASC ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `venue_id` (`venue_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_start_datetime` (`start_datetime`),
  ADD KEY `idx_organizer_id` (`organizer_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_events_published` (`status`,`start_datetime`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_is_read` (`is_read`),
  ADD KEY `idx_notifications_user_read` (`user_id`,`is_read`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_registration` (`event_id`,`user_id`),
  ADD KEY `idx_event_id` (`event_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_registrations_event_user` (`event_id`,`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_review` (`event_id`,`user_id`),
  ADD KEY `idx_event_id` (`event_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `saved_events`
--
ALTER TABLE `saved_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_save` (`user_id`,`event_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `saved_events`
--
ALTER TABLE `saved_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_ibfk_3` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE SET NULL;

--
-- قيود الجداول `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- قيود الجداول `saved_events`
--
ALTER TABLE `saved_events`
  ADD CONSTRAINT `saved_events_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `saved_events_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
