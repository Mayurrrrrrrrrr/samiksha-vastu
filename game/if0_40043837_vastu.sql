-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql206.infinityfree.com
-- Generation Time: Apr 22, 2026 at 12:51 PM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_40043837_vastu`
--

-- --------------------------------------------------------

--
-- Table structure for table `choices`
--

CREATE TABLE `choices` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `choice_text` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `choices`
--

INSERT INTO `choices` (`id`, `question_id`, `choice_text`) VALUES
(1, 1, 'South East (SE)'),
(2, 1, 'North East'),
(3, 1, 'North West'),
(4, 1, 'South West'),
(5, 2, 'North East (NE)'),
(6, 2, 'South East'),
(7, 2, 'West'),
(8, 2, 'South'),
(9, 3, 'North West (NW)'),
(10, 3, 'South West'),
(11, 3, 'East'),
(12, 3, 'North'),
(13, 4, 'South West (SW)'),
(14, 4, 'North West'),
(15, 4, 'North East'),
(16, 4, 'South East'),
(17, 5, 'East or North'),
(18, 5, 'South or West'),
(19, 5, 'South or East'),
(20, 5, 'West or North'),
(21, 6, 'South, SW, East, or North'),
(22, 6, 'North, NE, or West'),
(23, 6, 'SE, NW, or South'),
(24, 6, 'West, SW, or NE'),
(25, 7, 'North of North West'),
(26, 7, 'South of South East'),
(27, 7, 'East of North East'),
(28, 7, 'West of South West'),
(29, 8, 'East of North East'),
(30, 8, 'West of North West'),
(31, 8, 'South of South West'),
(32, 8, 'North of North East'),
(33, 9, 'South East'),
(34, 9, 'North West'),
(35, 9, 'North East'),
(36, 9, 'South West'),
(37, 10, 'East, North East, or West'),
(38, 10, 'South, SW, or SE'),
(39, 10, 'North, NW, or South'),
(40, 10, 'SE, SW, or NW'),
(41, 11, 'East or North East'),
(42, 11, 'West or South West'),
(43, 11, 'South or South East'),
(44, 11, 'North or North West'),
(45, 12, 'North or North East'),
(46, 12, 'South or South East'),
(47, 12, 'West or North West'),
(48, 12, 'East or South East'),
(49, 13, 'West'),
(50, 13, 'East'),
(51, 13, 'North'),
(52, 13, 'South'),
(105, 27, 'South'),
(106, 27, 'North'),
(107, 27, 'East'),
(108, 27, 'West'),
(109, 28, 'North or West'),
(110, 28, 'South or East'),
(111, 28, 'East or NE'),
(112, 28, 'SW or NW'),
(113, 29, 'WNW or SSW'),
(114, 29, 'NE or ESE'),
(115, 29, 'North or South'),
(116, 29, 'East or West'),
(117, 30, 'East South East'),
(118, 30, 'West North West'),
(119, 30, 'North North East'),
(120, 30, 'South South West'),
(121, 31, 'East'),
(122, 31, 'West'),
(123, 31, 'North'),
(124, 31, 'South'),
(125, 32, 'West'),
(126, 32, 'East'),
(127, 32, 'North'),
(128, 32, 'South'),
(129, 33, 'North'),
(130, 33, 'South'),
(131, 33, 'East'),
(132, 33, 'West'),
(133, 34, 'South'),
(134, 34, 'North'),
(135, 34, 'East'),
(136, 34, 'West'),
(137, 35, 'South East'),
(138, 35, 'South West'),
(139, 35, 'North East'),
(140, 35, 'North West'),
(141, 36, 'South West'),
(142, 36, 'North West'),
(143, 36, 'South East'),
(144, 36, 'North East'),
(145, 37, 'North West'),
(146, 37, 'North East'),
(147, 37, 'South West'),
(148, 37, 'South East'),
(149, 38, 'North East'),
(150, 38, 'South East'),
(151, 38, 'North West'),
(152, 38, 'South West');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `correct_option_id` int(11) DEFAULT NULL,
  `question_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question_text`, `correct_option_id`, `question_image`) VALUES
(1, 'Which is the ideal direction for Fire?', 1, NULL),
(2, 'Which direction represents the Water element?', 5, NULL),
(3, 'In which direction should Air be situated?', 9, NULL),
(4, 'Which direction is associated with the Earth element?', 13, NULL),
(5, 'For best study results, one should sit in the West facing:', 17, NULL),
(6, 'Which are the ideal directions for a Bedroom?', 21, NULL),
(7, 'Where is the best place for T.V. and entertainment?', 25, NULL),
(8, 'Which zone is best for Toys and fun Games?', 29, NULL),
(9, 'What is the ideal Vastu direction for a Kitchen?', 33, NULL),
(10, 'Where should the Pooja room be located?', 37, NULL),
(11, 'Which directions are most suitable for Plants?', 41, NULL),
(12, 'Where should Fountains and water bodies be placed?', 45, NULL),
(13, 'What is the ideal Vastu direction for the Dining Area?', 49, NULL),
(27, 'In which direction should a Gym be located?', 105, NULL),
(28, 'Which directions are best for a Money Locker?', 109, NULL),
(29, 'Which directions are recommended for a Toilet?', 113, NULL),
(30, 'Which place is best for Discussion and Churning?', 117, NULL),
(31, 'Which direction is ruled by the Sun?', 121, NULL),
(32, 'Which direction is associated with Saturn?', 125, NULL),
(33, 'Which direction is governed by Mercury?', 129, NULL),
(34, 'Which direction is ruled by Mars?', 133, NULL),
(35, 'Which direction is associated with Venus?', 137, NULL),
(36, 'Which direction is governed by Rahu?', 141, NULL),
(37, 'Which direction is associated with the Moon?', 145, NULL),
(38, 'Which direction is ruled by Jupiter?', 149, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `final_score` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `persona` varchar(50) DEFAULT 'Seeker',
  `badge` varchar(10) DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `phone_number`, `otp`, `is_verified`, `final_score`, `created_at`, `persona`, `badge`, `completed_at`) VALUES
(1, 'Gaurang', '9004437501', '7802', 1, 300, '2026-02-14 09:58:42', 'Master Architect', 'ðŸ†', NULL),
(2, 'Samriddhi ', '7000208511', '7324', 1, 1300, '2026-02-14 11:27:55', 'Master Architect', 'ðŸ†', NULL),
(3, 'Mrunal Utekar', '9821724169', '9395', 1, 1500, '2026-02-14 12:10:51', 'Master Architect', 'ðŸ†', NULL),
(4, 'Hetal', '9029368702', '4129', 1, 1200, '2026-02-14 12:18:26', 'Master Architect', 'ðŸ†', NULL),
(5, 'Sonalli', '8655388381', '6982', 1, 1200, '2026-02-14 12:19:45', 'Master Architect', 'ðŸ†', NULL),
(6, 'Ovee naik', '9637391593', '7401', 1, 1400, '2026-02-14 12:28:13', 'Master Architect', 'ðŸ†', NULL),
(7, 'Pratubha ', '9763990848', '4717', 1, 1500, '2026-02-14 12:34:42', 'Master Architect', 'ðŸ†', NULL),
(8, 'Ashok Rahi', '9867423486', '3794', 1, 1400, '2026-02-14 12:44:27', 'Master Architect', 'ðŸ†', NULL),
(9, 'Rupali Sachin Gate ', '9967371125', '6250', 1, 1400, '2026-02-14 12:52:37', 'Master Architect', 'ðŸ†', NULL),
(10, 'Pandurang Sonawane', '9571999165', '7221', 1, 1500, '2026-02-14 12:52:42', 'Master Architect', 'ðŸ†', NULL),
(11, 'Shrikant chavan', '9869703869', '4275', 1, 1500, '2026-02-14 13:04:57', 'Master Architect', 'ðŸ†', NULL),
(12, 'Ankit Gupta', '8947920130', '4940', 1, 1500, '2026-02-14 13:13:07', 'Master Architect', 'ðŸ†', NULL),
(13, 'Daksh kaustubh jedhe', '9768659522', '2845', 1, 1500, '2026-02-14 13:20:15', 'Master Architect', 'ðŸ†', NULL),
(14, 'Naresh ', '8758292037', '4852', 1, 1500, '2026-02-14 13:21:47', 'Master Architect', 'ðŸ†', NULL),
(15, 'Harsha haresh sawant ', '7786900060', '9671', 1, 1100, '2026-02-14 13:32:34', 'Master Architect', 'ðŸ†', NULL),
(16, 'Priti Guri ', '9323029325', '2785', 1, 1400, '2026-02-14 13:39:22', 'Master Architect', 'ðŸ†', NULL),
(17, 'Ajit', '7738666918', '8683', 1, 1400, '2026-02-14 13:44:07', 'Master Architect', 'ðŸ†', NULL),
(18, 'Shanti ', '9082583729', '7881', 1, 1500, '2026-02-14 14:57:47', 'Master Architect', 'ðŸ†', NULL),
(20, 'Sweetu ', '8291401785', '7570', 1, 1300, '2026-02-14 15:19:36', 'Master Architect', 'ðŸ†', NULL),
(21, 'Sapna Singh ', '8779624693', '3670', 1, 1500, '2026-02-14 15:33:46', 'Master Architect', 'ðŸ†', NULL),
(22, 'Mayur', '9644771118', '1591', 1, 0, '2026-02-15 11:29:18', 'Seeker', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `choices`
--
ALTER TABLE `choices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `question_text` (`question_text`) USING HASH;

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `phone_number` (`phone_number`),
  ADD UNIQUE KEY `phone_number_2` (`phone_number`),
  ADD UNIQUE KEY `phone_number_3` (`phone_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `choices`
--
ALTER TABLE `choices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=253;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `choices`
--
ALTER TABLE `choices`
  ADD CONSTRAINT `choices_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
