-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 15, 2025 at 06:10 PM
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
-- Database: `event_registration_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `actor_type` enum('admin','client') NOT NULL,
  `actor_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `actor_type`, `actor_id`, `action`, `description`, `created_at`) VALUES
(1, 'admin', 2, 'approve_client', 'Approved client: Mhark Pentinio from Mhark Event', '2025-05-15 05:01:27'),
(2, 'admin', 2, 'suspend_client', 'Suspended client: Mhark Pentinio from Mhark Event', '2025-05-15 05:11:22'),
(3, 'admin', 2, 'approve_client', 'Approved client: Mhark Pentinio from Mhark Event', '2025-05-15 05:11:43'),
(4, 'admin', 2, 'approve_client', 'Approved client: Mhark Pentinio from Mhark Event', '2025-05-15 05:12:11'),
(5, 'client', 1, 'create_event', 'Created new event: 123 (published)', '2025-05-15 06:05:38'),
(6, 'client', 1, 'create_event', 'Created new event: 123 (published)', '2025-05-15 06:07:31'),
(7, 'client', 1, 'create_event', 'Created new event: 123 (published)', '2025-05-15 06:09:25'),
(8, 'client', 1, 'create_event', 'Created new event: 12312 (published)', '2025-05-15 06:12:32'),
(9, 'client', 1, 'create_event', 'Created new event: 123 (draft)', '2025-05-15 06:14:34'),
(10, 'client', 1, 'create_event', 'Created new event: 123 (pending)', '2025-05-15 06:17:58'),
(11, 'client', 1, 'update_event', 'Updated event: 123', '2025-05-15 06:23:17'),
(12, 'client', 1, 'update_event', 'Updated event: 123', '2025-05-15 06:25:21'),
(13, 'client', 1, 'update_event', 'Updated event: sadasdad', '2025-05-15 06:25:31'),
(14, 'client', 1, 'update_event', 'Updated event:  vsd', '2025-05-15 06:25:41'),
(15, 'client', 1, 'update_event', 'Updated event:  vsd', '2025-05-15 06:29:02'),
(16, 'admin', 2, 'reject_event', 'Rejected event: 123 (ID: 3). Reason: ', '2025-05-15 07:11:57'),
(17, 'admin', 2, 'delete_category', 'Deleted category: Networking', '2025-05-15 07:17:17'),
(18, 'admin', 2, 'create_category', 'Created new category: 123', '2025-05-15 07:17:21'),
(19, 'client', 1, 'create_event', 'Created new event: 123 (pending)', '2025-05-15 09:23:24'),
(20, 'client', 1, 'update_event', 'Updated event:  vsd', '2025-05-15 12:08:11'),
(21, 'client', 1, 'update_event', 'Updated event: qqq3', '2025-05-15 13:01:08'),
(22, '', 0, '2', 'suspend_client', '2025-05-15 13:41:11'),
(23, '', 0, '2', 'approve_client', '2025-05-15 13:41:13'),
(24, '', 0, '2', 'suspend_client', '2025-05-15 15:21:59'),
(25, 'admin', 2, 'approve_event', 'Approved event: 123 (ID: 7)', '2025-05-15 15:22:34'),
(26, 'admin', 2, 'approve_event', 'Approved event: 12312 (ID: 4)', '2025-05-15 15:24:26'),
(27, 'admin', 2, 'approve_event', 'Approved event: 123 (ID: 3)', '2025-05-15 15:26:37'),
(28, 'admin', 2, 'approve_event', 'Approved event: qqq3 (ID: 2)', '2025-05-15 15:28:32'),
(29, 'admin', 2, 'approve_event', 'Approved event: qqq3 (ID: 2)', '2025-05-15 15:30:46'),
(30, 'admin', 2, 'approve_event', 'Approved event: 123 (ID: 3)', '2025-05-15 15:32:27'),
(31, 'admin', 2, 'approve_event', 'Approved event: qqq3 (ID: 2)', '2025-05-15 15:33:05'),
(32, 'admin', 2, 'approve_event', 'Approved event: 12312 (ID: 4)', '2025-05-15 15:38:58'),
(33, 'admin', 2, 'approve_event', 'Approved event: qqq3 (ID: 2)', '2025-05-15 15:40:04'),
(34, 'admin', 2, 'approve_event', 'Approved event: 123 (ID: 3)', '2025-05-15 15:41:08'),
(35, 'admin', 2, 'approve_event', 'Approved event: qqq3 (ID: 2)', '2025-05-15 15:43:42'),
(36, 'admin', 2, 'create_category', 'Created new category: 123', '2025-05-15 15:43:55'),
(37, '', 0, '2', 'approve_client', '2025-05-15 15:48:21'),
(38, 'client', 4, 'create_event', 'Created new event: Tech Innovators Meetup 2025 (pending)', '2025-05-15 15:50:52'),
(39, 'admin', 2, 'approve_event', 'Approved event: Tech Innovators Meetup 2025 (ID: 8)', '2025-05-15 15:54:48'),
(40, 'client', 4, 'update_event', 'Updated event: Tech Innovators Meetup 2025', '2025-05-15 16:01:40');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `role_id`, `created_at`) VALUES
(2, 'admin', '$2y$10$g3TNLzA08RH0ajoyu./WOuVWSOe6IF8mQwgBh2tOAPG4XQxNhY9Ia', 'admin@example.com', 1, '2025-05-15 04:18:15');

-- --------------------------------------------------------

--
-- Table structure for table `admin_roles`
--

CREATE TABLE `admin_roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_roles`
--

INSERT INTO `admin_roles` (`id`, `name`, `description`) VALUES
(1, 'Super Admin', 'Has full access to all system features');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Conference', 'Professional conferences, conventions, and large-scale business gatherings'),
(2, 'Workshop', 'Interactive learning sessions focused on skill development and hands-on practice'),
(3, 'Seminar', 'Educational presentations and lectures by industry experts'),
(4, 'Training', ''),
(6, 'Exhibition', ''),
(7, 'Webinar', ''),
(8, '123', '12312'),
(9, '123', '123213');

-- --------------------------------------------------------

--
-- Table structure for table `checkins`
--

CREATE TABLE `checkins` (
  `checkin_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `checkin_time` datetime DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL,
  `status` enum('present','late','absent') DEFAULT 'present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkins`
--

INSERT INTO `checkins` (`checkin_id`, `participant_id`, `event_id`, `checkin_time`, `notes`, `status`) VALUES
(1, 3, 2, '2025-05-15 21:05:18', NULL, ''),
(2, 4, 8, '2025-05-16 00:02:17', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `organization` varchar(100) DEFAULT NULL,
  `approved` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `name`, `email`, `password`, `organization`, `approved`, `created_at`) VALUES
(1, 'Mhark Pentinio', 'mhark@example.com', '$2y$10$KrOi.KYfOTN0/hp5AKhUUuOqLsJnbD3EpkjQjSHP2W6ekGY6077ki', 'Mhark Event', 1, '2025-05-15 03:51:42'),
(2, 'Mhark Pentinio', 'john.doe@example.com', '$2y$10$3Z7YGvSEZFHNpTnesyEe1esmmLQhjL1nIn9iyU1ehLcq152FBJbWq', 'Mhark Event', 1, '2025-05-15 03:53:26'),
(3, 'Mhark Pentinio', 'john.doe123@example.com', '$2y$10$xDijKUVkDz9BJAOlwdGeKeDdWyOKVilz4TiBhQpjXGiqUJT2.aAgW', 'Mhark Event', 0, '2025-05-15 03:53:38'),
(4, 'Mhark Pentinio', 'mharkpentinio@gmail.com', '$2y$10$UnGYu2CtB9DQ8U62bN/Lwu4XIrAP5GtEGdlM7yTRVU33z0Pqpvhi2', 'Ewan ko', 1, '2025-05-15 15:47:40');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `event_date` datetime NOT NULL,
  `registration_deadline` datetime NOT NULL,
  `location` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `slots` int(11) NOT NULL,
  `max_participants_per_registration` int(11) DEFAULT 1,
  `visibility` enum('public','private','invite-only') DEFAULT 'public',
  `status` enum('pending','published','draft','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `client_id`, `title`, `description`, `category_id`, `type_id`, `event_date`, `registration_deadline`, `location`, `price`, `slots`, `max_participants_per_registration`, `visibility`, `status`, `rejection_reason`, `banner`, `created_at`, `updated_at`) VALUES
(1, 1, '123', '123123', 3, 3, '2025-05-20 14:03:00', '2025-05-16 14:03:00', '{\"physical\":\"123\",\"virtual\":\"http:\\/\\/localhost\\/event-registration-system\\/views\\/client\\/create-event.php\"}', 100.00, 123, 1, 'private', 'published', NULL, 'uploads/events/event_banner_682584329e24d.jpg', '2025-05-15 06:05:38', '2025-05-15 06:05:38'),
(2, 1, 'qqq3', '123123', 3, 3, '2025-05-15 14:03:00', '2025-05-15 14:03:00', '{\"physical\":\"123\",\"virtual\":\"http:\\/\\/localhost\\/event-registration-system\\/views\\/client\\/create-event.php\"}', 100.00, 123, NULL, 'public', '', NULL, 'uploads/events/event_banner_682584a36374d.jpg', '2025-05-15 06:07:31', '2025-05-15 15:43:42'),
(3, 1, '123', '123123', 3, 3, '2025-05-20 14:03:00', '2025-05-16 14:03:00', '{\"physical\":\"123\",\"virtual\":\"http:\\/\\/localhost\\/event-registration-system\\/views\\/client\\/create-event.php\"}', 100.00, 123, 1, 'private', '', NULL, 'uploads/events/event_banner_68258515e843e.jpg', '2025-05-15 06:09:25', '2025-05-15 15:41:08'),
(4, 1, '12312', '3123123', NULL, 1, '2025-05-23 14:12:00', '2025-05-16 14:12:00', '123213', 123.00, 12, 1, 'public', '', 'eqweqwe', 'uploads/events/event_banner_682585d007773.jpg', '2025-05-15 06:12:32', '2025-05-15 15:38:58'),
(5, 1, '123', '123123', 3, 2, '2025-05-29 14:14:00', '2025-05-19 14:14:00', '{\"physical\":\"\",\"virtual\":\"http:\\/\\/localhost\\/event-registration-system\\/views\\/client\\/create-event.php\"}', 123.00, 3123, 1, 'private', 'draft', NULL, NULL, '2025-05-15 06:14:34', '2025-05-15 06:14:34'),
(6, 1, ' vsd', '123123', 6, 1, '2025-05-15 14:17:00', '2025-05-15 14:17:00', '123', 1233.00, 12312, NULL, 'private', 'rejected', NULL, 'uploads/events/event_banner_68258716341f3.jpg', '2025-05-15 06:17:58', '2025-05-15 12:08:11'),
(7, 1, '123', '123', 8, 1, '2025-05-22 17:23:00', '2025-05-17 17:23:00', '123', 12312.00, 123, 1, 'private', '', NULL, 'uploads/events/event_banner_6825b28ca8180.jpg', '2025-05-15 09:23:24', '2025-05-15 15:22:34'),
(8, 4, 'Tech Innovators Meetup 2025', ' Join industry leaders and startups in a full-day networking and tech innovation showcase. Discover the latest trends, pitch ideas, and build partnerships.', 1, 1, '2025-05-16 23:50:00', '2025-05-15 16:52:00', 'Lipa City, Batangas', 100.00, 100, NULL, 'public', 'published', NULL, 'uploads/events/event_banner_68260d5cead18.png', '2025-05-15 15:50:52', '2025-05-15 16:01:40');

-- --------------------------------------------------------

--
-- Table structure for table `event_types`
--

CREATE TABLE `event_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_types`
--

INSERT INTO `event_types` (`id`, `name`) VALUES
(1, 'Physical'),
(2, 'Virtual'),
(3, 'Hybrid');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `recipient_type` enum('admin','client','participant') NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `recipient_type`, `recipient_id`, `message`, `is_read`, `created_at`) VALUES
(1, 'admin', 1, 'New client registration: Mhark Pentinio from Mhark Event requires approval.', 0, '2025-05-15 03:53:26'),
(2, 'admin', 1, 'New client registration: Mhark Pentinio from Mhark Event requires approval.', 0, '2025-05-15 03:53:38'),
(3, 'participant', 2, 'Your payment for 12312 has been confirmed.', 0, '2025-05-15 11:56:28'),
(4, 'participant', 2, 'Your payment for 123 has been confirmed.', 0, '2025-05-15 12:27:27'),
(5, 'participant', 3, 'Your payment for qqq3 has been confirmed.', 0, '2025-05-15 13:00:07'),
(6, 'admin', 2, 'New client registration: Mhark Pentinio from Ewan ko requires approval.', 0, '2025-05-15 15:47:40'),
(7, 'participant', 4, 'Your payment for Tech Innovators Meetup 2025 has been confirmed.', 0, '2025-05-15 15:59:43');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  `phone` varchar(20) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `name`, `email`, `password`, `created_at`, `status`, `phone`, `last_login`) VALUES
(4, 'Mhark Pentinio', 'mharkpentinio@gmail.com', '$2y$10$Il6jHVRw3/NFTnmAaIbkU.ain6fbt.ZfE1P4gpL5YHIlnqhxjAfH6', '2025-05-15 15:53:01', 'active', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `registration_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `reference_number` varchar(100) NOT NULL,
  `proof_file` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `decline_notes` text DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `verified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `registration_id`, `participant_id`, `event_id`, `amount`, `payment_method`, `reference_number`, `proof_file`, `status`, `created_at`, `decline_notes`, `verified_at`, `verified_by`) VALUES
(1, 14, 3, 4, 123.00, 'gcash', '123213', '6825a51dd2974_d2a90139-44fe-4a8c-a6a6-1cedc1aaf82d.jpg', 'pending', '2025-05-15 08:26:05', NULL, NULL, NULL),
(2, 15, 2, 2, 100.00, 'bank', '213123123', '6825ccc25c075_Group 5.png', 'confirmed', '2025-05-15 11:15:14', NULL, '2025-05-15 20:27:27', 1),
(3, 16, 2, 4, 123.00, 'gcash', '123123123123', '6825cef271ba3_Blank diagram (6).png', 'confirmed', '2025-05-15 11:24:34', NULL, '2025-05-15 19:56:28', 1),
(4, 17, 3, 2, 100.00, 'gcash', '123123123123', '6825e03f8e8ec_pexels-photo-3856027.webp', 'pending', '2025-05-15 12:38:23', NULL, NULL, NULL),
(5, 19, 3, 4, 123.00, 'gcash', '123123', '6825e1154f50c_pexels-bertellifotografia-3856027.jpg', 'pending', '2025-05-15 12:41:57', NULL, NULL, NULL),
(6, 20, 3, 2, 100.00, 'gcash', '123123123', '6825e164eece3_pexels-bertellifotografia-3856027.jpg', 'pending', '2025-05-15 12:43:16', NULL, NULL, NULL),
(7, 21, 3, 2, 100.00, 'gcash', '123123', '6825e19fa5ce0_pexels-photo-3856027.webp', 'pending', '2025-05-15 12:44:15', NULL, NULL, NULL),
(8, 22, 3, 4, 123.00, 'gcash', '123123', '6825e1db71953_pexels-adrien-olichon-1257089-2387532.jpg', 'pending', '2025-05-15 12:45:15', NULL, NULL, NULL),
(9, 23, 3, 2, 100.00, 'gcash', '1231', '6825e2636498b_pexels-bertellifotografia-3856027.jpg', 'pending', '2025-05-15 12:47:31', NULL, NULL, NULL),
(10, 24, 3, 4, 123.00, 'gcash', '123123123123', '6825e27d14224_pexels-bertellifotografia-3856027.jpg', 'pending', '2025-05-15 12:47:57', NULL, NULL, NULL),
(11, 25, 3, 2, 100.00, 'gcash', '123123123123123', '6825e2dbe39a1_pexels-bertellifotografia-3856027.jpg', 'pending', '2025-05-15 12:49:31', NULL, NULL, NULL),
(12, 26, 3, 4, 123.00, 'gcash', '123123123123', '6825e33049fd2_pexels-bertellifotografia-3856027.jpg', 'pending', '2025-05-15 12:50:56', NULL, NULL, NULL),
(13, 27, 3, 2, 100.00, 'gcash', '12321312312', '6825e4a1b1002_pexels-photo-3856027.webp', 'confirmed', '2025-05-15 12:57:05', NULL, '2025-05-15 21:00:07', 1),
(14, 28, 4, 8, 100.00, 'gcash', '123123123123', '68260f01783c4_pexels-photo-3856027.webp', 'confirmed', '2025-05-15 15:57:53', NULL, '2025-05-15 23:59:43', 4);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('confirmed','cancelled') DEFAULT 'confirmed',
  `contact_number` varchar(20) DEFAULT NULL,
  `emergency_contact` varchar(255) DEFAULT NULL,
  `emergency_number` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `registration_code` varchar(100) DEFAULT NULL,
  `qr_code` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `participant_id`, `event_id`, `registered_at`, `status`, `contact_number`, `emergency_contact`, `emergency_number`, `notes`, `registration_code`, `qr_code`) VALUES
(27, 3, 2, '2025-05-15 12:57:05', 'confirmed', '09561480871', '09561480871', '09561480871', '123123', 'REG-78594939', 'REG-00000027'),
(28, 4, 8, '2025-05-15 15:57:53', 'confirmed', '09561480871', '09561480871', '09561480871', '', 'REG-97152169', 'REG-00000028');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_actor` (`actor_type`,`actor_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE `admin_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `checkins`
--
ALTER TABLE `checkins`
  ADD PRIMARY KEY (`checkin_id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `event_types`
--
ALTER TABLE `event_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notification_recipient` (`recipient_type`,`recipient_id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_id` (`registration_id`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_code` (`registration_code`),
  ADD KEY `participant_id` (`participant_id`),
  ADD KEY `event_id` (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE `admin_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `checkins`
--
ALTER TABLE `checkins`
  MODIFY `checkin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `event_types`
--
ALTER TABLE `event_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `admin_roles` (`id`);

--
-- Constraints for table `checkins`
--
ALTER TABLE `checkins`
  ADD CONSTRAINT `checkins_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `checkins_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `events_ibfk_3` FOREIGN KEY (`type_id`) REFERENCES `event_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`),
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`);

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
