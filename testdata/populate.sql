-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 19, 2025 at 11:25 AM
-- Server version: 11.3.2-MariaDB
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `csit314`
--

-- --------------------------------------------------------

--
-- Table structure for table `cleaningservices`
--

DROP TABLE IF EXISTS `cleaningservices`;
CREATE TABLE IF NOT EXISTS `cleaningservices` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `cleaner_id` int(11) NOT NULL,
  `service_title` varchar(256) NOT NULL,
  `service_description` text NOT NULL,
  `service_category` int(100) NOT NULL,
  `service_price` double DEFAULT NULL,
  `views` int(10) UNSIGNED DEFAULT 0,
  `shortlisted` int(10) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`service_id`),
  KEY `cleaner_id` (`cleaner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cleaningservices`
--

INSERT INTO `cleaningservices` (`service_id`, `cleaner_id`, `service_title`, `service_description`, `service_category`, `service_price`, `views`, `shortlisted`) VALUES
(1, 2, 'Sparkling Window Cleaning Service', 'Let natural light pour in through spotless, streak-free windows! Our professional window cleaning service ensures a crystal-clear view, removing dirt, smudges, and water stains from both interior and exterior surfaces. Perfect for homes, offices, and high-rise buildings.', 2, 65, 76, 0),
(2, 2, 'Ultimate DEEP Clean Package', 'Tackle built-up grime with our thorough deep cleaning service. We sanitize kitchens, scrub bathrooms, wipe baseboards, dust vents, and reach the corners regular cleans miss. Ideal for spring cleaning or move-in/move-out scenarios.', 1, 155, 26, 0),
(3, 3, 'Eco-Friendly Kitchen Deep Clean', 'Thorough cleaning of kitchen surfaces, appliances, and cabinets using environmentally safe products. Ideal for keeping your kitchen spotless and toxin-free.', 6, 120, 30, 0),
(4, 3, 'Bathroom Sanitization and Mold Removal', 'Comprehensive bathroom cleaning including sanitizing toilets, sinks, showers, and removal of mold and mildew with professional-grade cleaners.', 1, 130, 18, 0),
(8, 5, 'Specialized Leather Sofa Cleaning', 'Gentle yet thorough cleaning of leather sofas and chairs to maintain softness and remove stains without damage.', 7, 140, 20, 0),
(9, 5, 'Allergy-Reducing Bedroom Cleaning', 'Target dust mites, allergens, and pet dander in bedrooms with steam cleaning, vacuuming, and hypoallergenic products.', 4, 110, 12, 0),
(10, 3, 'Post Reno Clean Up Crew', 'Renovations leave dust and debris everywhere. Weâ€™ll do the heavy liftingâ€”clearing dust, paint splatters, and construction residues to reveal your newly renovated space.', 3, 180, 9, 0),
(11, 3, 'Mattress Fresh & Sanitized', 'Say goodbye to dust mites, sweat stains, and odors. Our deep steam and vacuum extraction cleaning removes allergens and bacteria for a healthier nightâ€™s sleep.', 4, 70, 3, 0),
(12, 2, 'Standard Home Cleaning', 'General cleaning for apartments and houses including dusting, vacuuming, mopping, and bathroom sanitization.', 1, 70, 1, 0),
(13, 2, 'Carpet Refresh', 'A complete deep-cleaning service for carpets using steam extraction and eco-safe shampoo to remove dirt, allergens, and stains. Ideal for homes with kids or pets. Includes pre-treatment for high-traffic areas and deodorizing.', 5, 90, 0, 0),
(14, 2, 'Pet-Friendly Cleaning', 'Designed for homes with pets, this service includes hair removal from furniture and floors, deodorizing, stain treatment for accidents, and the use of animal-safe cleaning products.', 9, 100, 1, 0),
(15, 3, 'Move-In / Move-Out Cleaning', 'A meticulous cleaning service for empty homes during transitions. Includes deep cleaning of every room, inside cabinets and drawers, fixtures, vents, and appliances. Designed to help renters and homeowners meet lease requirements or start fresh in a new home.', 3, 200, 0, 0),
(16, 3, 'Window & Glass Cleaning', 'Interior and exterior cleaning of windows, glass doors, and mirrors using streak-free solutions. Includes screen washing and sill wiping. Optional water-fed pole for high-rise windows (up to 3 stories).', 2, 60, 0, 0),
(17, 3, 'Sofa & Upholstery Cleaning', 'Professional cleaning of fabric and leather sofas, chairs, and ottomans using industry-grade tools. Removes embedded dirt, body oils, food stains, and pet odors. Includes protective coating if requested.', 7, 75, 0, 0),
(18, 5, 'Luxury Home Detailing', 'Professional dusting and micro-cleaning of fine art, sculptures, antique woodwork, and high-value collectibles using museum-approved methods. Our trained team uses pH-neutral products and anti-static cloths to preserve integrity and value.', 10, 500, 0, 0),
(19, 5, 'Chandelier & Lighting Fixture Cleaning', 'Dismantling and hand-cleaning of delicate chandeliers, pendant lights, and designer fixtures using crystal-safe solvents and microfiber polishing. Each prism is cleaned individually for maximum brilliance.', 8, 300, 0, 0),
(20, 5, 'Marble & Stone Polishing Service', 'Restorative polishing of luxury stone surfaces including marble, granite, and quartz. Removes micro-scratches, water stains, and etching, leaving a glossy, mirror-like finish. Includes sealing upon request.', 10, 300, 0, 0),
(29, 10, 'Garage and Basement Cleaning', 'Thorough cleaning and decluttering services for garages, basements, and storage spaces.', 1, 130, 4, 0),
(30, 10, 'Eco Carpet Shampooing', 'Deep shampoo and deodorize carpets with eco-friendly products safe for children and pets.', 5, 95, 18, 0),
(31, 11, 'Holiday Cleaning Special', 'Complete home cleaning package ideal before or after holidays including dusting, mopping, and kitchen/bathroom cleaning.', 1, 160, 22, 1),
(32, 11, 'Post-Party Cleanup', 'Efficient cleanup service after parties, including trash removal, floor cleaning, and kitchen sanitation.', 1, 110, 13, 0),
(33, 12, 'Wood Floor Polishing', 'Restore and polish hardwood floors to their original shine while protecting from wear.', 10, 180, 9, 0),
(34, 12, 'Leather Sofa Conditioning', 'Deep cleaning and conditioning of leather furniture to maintain softness and prevent cracking.', 7, 130, 11, 0),
(35, 13, 'Eco-Friendly Tile and Stone Cleaning', 'Gentle but effective cleaning and sealing of stone and tile surfaces using green products.', 10, 150, 6, 0),
(36, 13, 'Luxury Rug Cleaning', 'Careful cleaning and deodorizing of Persian and oriental rugs to preserve colors and fibers.', 5, 200, 7, 0),
(37, 14, 'Pet Hair Removal Service', 'Specialized removal of pet hair from upholstery, carpets, and floors.', 9, 100, 16, 0),
(38, 14, 'Allergen Reduction Cleaning', 'Targeted cleaning to reduce allergens throughout your home for healthier living.', 1, 90, 14, 0),
(39, 15, 'Customizable Cleaning Plans', 'Tailor your cleaning service to your home’s unique needs with our flexible plans.', 1, 100, 3, 0),
(40, 15, 'Window Screen Repair and Cleaning', 'Cleaning and minor repairs for window screens to improve appearance and function.', 2, 70, 2, 0),
(41, 16, 'High-Rise Window Cleaning', 'Safe and efficient cleaning of windows up to 5 stories high using water-fed poles and harness systems.', 2, 200, 8, 0),
(42, 16, 'Solar Panel Cleaning', 'Optimize solar panel efficiency with specialized cleaning free of abrasives and residues.', 2, 180, 5, 0),
(55, 23, 'Window Track and Frame Cleaning', 'Detail cleaning of window tracks and frames for smooth operation and appearance.', 2, 65, 9, 0),
(56, 23, 'Furniture Dusting and Polishing', 'Dust and polish wooden and metal furniture to a high shine.', 7, 80, 10, 0),
(57, 24, 'Luxury Chandelier Cleaning', 'Hand-cleaning of delicate chandeliers using specialized tools and cleaners.', 8, 320, 7, 0),
(58, 24, 'Mirrored Surface Cleaning', 'Streak-free cleaning of mirrors and reflective surfaces.', 2, 90, 12, 0),
(59, 25, 'Eco-Friendly Upholstery Cleaning', 'Deep cleaning using non-toxic, eco-friendly products safe for kids and pets.', 7, 130, 8, 0),
(60, 25, 'Floor Waxing and Buffing', 'Apply wax and buff floors to restore shine and protect surfaces.', 10, 150, 10, 0);

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
CREATE TABLE IF NOT EXISTS `matches` (
  `match_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_id` int(11) NOT NULL,
  `homeowner_id` int(11) NOT NULL,
  `cleaner_id` int(11) NOT NULL,
  `match_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','accepted','rejected','completed') DEFAULT 'pending',
  `rating` int(11) DEFAULT NULL,
  `review` text DEFAULT NULL,
  PRIMARY KEY (`match_id`),
  KEY `service_id` (`service_id`),
  KEY `homeowner_id` (`homeowner_id`),
  KEY `cleaner_id` (`cleaner_id`)
) ENGINE=MyISAM AUTO_INCREMENT=89 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`match_id`, `service_id`, `homeowner_id`, `cleaner_id`, `match_date`, `status`, `rating`, `review`) VALUES
(3, 1, 4, 2, '2025-05-17 16:49:42', 'accepted', 5, 'Amazing service!'),
(4, 9, 4, 5, '2025-05-17 16:51:03', 'accepted', 4, 'Great, but took a bit too long'),
(5, 19, 4, 5, '2025-05-19 16:58:30', 'accepted', 5, 'My room is looking brighter than ever!'),
(6, 2, 8, 2, '2025-05-18 18:16:47', 'accepted', 4, 'Very thorough!'),
(7, 14, 17, 2, '2025-05-18 18:17:25', 'accepted', 4, 'Got rid of most of my cat\'s fur!'),
(8, 2, 18, 2, '2025-05-18 18:19:38', 'rejected', NULL, NULL),
(9, 13, 18, 2, '2025-05-18 18:20:08', 'accepted', 5, 'Friendly fella'),
(10, 59, 8, 25, '2025-05-19 18:41:58', 'accepted', 3, 'Scratched my glass...'),
(11, 60, 8, 25, '2025-05-17 18:42:01', 'accepted', 5, 'Shiny floors!'),
(12, 35, 20, 13, '2025-05-19 18:43:35', 'accepted', 3, 'Decent'),
(13, 36, 20, 13, '2025-05-19 18:43:37', 'accepted', 1, 'Bleached my expensive rug :('),
(14, 41, 21, 16, '2025-05-19 18:45:50', 'accepted', 5, 'My windows look brand new!'),
(15, 42, 21, 16, '2025-05-18 18:45:53', 'accepted', 5, 'Tip top quality'),
(16, 38, 17, 14, '2025-05-19 18:50:40', 'accepted', 4, 'Good price'),
(17, 4, 17, 3, '2025-05-19 18:52:24', 'accepted', 5, 'Great service!'),
(18, 9, 17, 5, '2025-05-19 18:53:19', 'accepted', 4, 'Quality service'),
(19, 59, 21, 16, '2025-04-30 00:57:15', 'accepted', 5, 'Satisfied with the service.'),
(20, 1, 17, 14, '2025-05-12 23:19:33', 'accepted', 4, 'Excellent work!'),
(21, 4, 8, 3, '2025-04-22 13:09:29', 'accepted', 4, 'Left a few spots.'),
(22, 2, 21, 13, '2025-04-15 15:12:36', 'pending', NULL, 'NULL'),
(23, 14, 18, 3, '2025-04-26 00:42:20', 'accepted', 2, 'Very satisfied.'),
(24, 2, 4, 5, '2025-04-30 16:24:03', 'accepted', 2, 'Very satisfied.'),
(25, 14, 18, 16, '2025-04-18 11:43:00', 'rejected', NULL, 'NULL'),
(26, 19, 17, 3, '2025-05-14 02:19:20', 'accepted', 2, 'Not the best, but okay.'),
(27, 42, 20, 2, '2025-04-16 04:20:18', 'rejected', NULL, 'NULL'),
(28, 38, 21, 25, '2025-04-24 07:17:55', 'accepted', 1, 'Average experience.'),
(29, 41, 21, 5, '2025-04-17 10:08:28', 'accepted', 5, 'Solid work.'),
(30, 13, 8, 2, '2025-04-06 16:10:46', 'accepted', 5, 'Friendly and professional.'),
(31, 13, 8, 13, '2025-04-28 03:50:33', 'accepted', 5, 'Highly recommended!'),
(32, 38, 17, 13, '2025-05-18 10:39:21', 'rejected', NULL, 'NULL'),
(33, 13, 20, 2, '2025-05-14 21:04:00', 'accepted', 2, 'Amazing experience.'),
(34, 9, 8, 2, '2025-05-15 17:56:21', 'accepted', 1, 'Perfect service!'),
(35, 35, 8, 13, '2025-05-18 00:02:25', 'accepted', 3, 'Expected a bit more.'),
(36, 19, 8, 16, '2025-04-23 14:11:13', 'accepted', 5, 'Quick and efficient.'),
(37, 60, 21, 13, '2025-05-04 04:14:11', 'accepted', 5, 'Friendly and professional.'),
(38, 36, 21, 3, '2025-05-04 01:01:37', 'rejected', NULL, 'NULL'),
(39, 60, 8, 3, '2025-04-24 02:03:58', 'accepted', 4, 'Excellent work!'),
(40, 36, 4, 25, '2025-04-21 08:20:00', 'accepted', 5, 'Will hire again.'),
(41, 9, 4, 5, '2025-04-13 03:10:13', 'accepted', 4, 'Felt a bit rushed.'),
(42, 19, 18, 3, '2025-05-12 21:16:17', 'accepted', 1, 'Got the job done.'),
(43, 42, 18, 25, '2025-05-12 20:38:32', 'accepted', 5, 'Amazing experience.'),
(44, 13, 20, 3, '2025-05-17 02:39:45', 'accepted', 4, 'Fantastic job!'),
(45, 60, 20, 13, '2025-04-27 07:05:31', 'rejected', NULL, 'NULL'),
(46, 38, 4, 25, '2025-04-21 18:56:38', 'pending', NULL, 'NULL'),
(47, 9, 21, 14, '2025-04-07 03:11:01', 'accepted', 4, 'Top-notch service.'),
(48, 2, 20, 16, '2025-05-11 15:21:26', 'pending', NULL, 'NULL'),
(49, 14, 4, 3, '2025-04-25 01:26:22', 'pending', NULL, 'NULL'),
(50, 19, 18, 16, '2025-04-05 21:04:23', 'accepted', 1, 'Not the best, but okay.'),
(51, 41, 17, 25, '2025-04-19 16:28:21', 'rejected', NULL, 'NULL'),
(52, 2, 18, 25, '2025-04-11 00:48:08', 'accepted', 4, 'Excellent work!'),
(53, 59, 21, 2, '2025-04-03 06:34:51', 'accepted', 5, 'Got the job done.'),
(54, 59, 17, 13, '2025-04-19 02:44:36', 'accepted', 5, 'Top-notch service.'),
(55, 38, 21, 25, '2025-05-11 13:57:49', 'pending', NULL, 'NULL'),
(56, 59, 17, 16, '2025-04-28 15:29:45', 'accepted', 4, 'Met my expectations.'),
(57, 9, 8, 16, '2025-04-23 03:05:01', 'rejected', NULL, 'NULL'),
(58, 14, 8, 25, '2025-04-05 13:48:01', 'accepted', 5, 'Top-notch service.'),
(59, 42, 4, 16, '2025-05-05 17:21:02', 'accepted', 3, 'Friendly and professional.'),
(60, 35, 20, 25, '2025-04-15 09:16:06', 'accepted', 4, 'Friendly and professional.'),
(61, 36, 17, 14, '2025-05-01 04:02:38', 'accepted', 4, 'Average experience.'),
(62, 4, 20, 2, '2025-04-14 13:02:01', 'accepted', 4, 'Impressive attention to detail.'),
(63, 14, 18, 14, '2025-04-24 02:39:48', 'accepted', 5, 'Impressive attention to detail.'),
(64, 9, 8, 25, '2025-05-02 01:47:54', 'accepted', 3, 'Outstanding service.'),
(65, 2, 20, 16, '2025-05-17 09:26:44', 'accepted', 5, 'Thorough cleaning.'),
(66, 59, 8, 25, '2025-04-24 19:40:18', 'rejected', NULL, 'NULL'),
(67, 42, 17, 14, '2025-04-19 21:04:32', 'accepted', 3, 'Great job!'),
(68, 14, 4, 25, '2025-05-18 13:50:12', 'accepted', 5, 'Spotless work!'),
(69, 59, 20, 2, '2025-04-11 23:41:38', 'rejected', NULL, 'NULL'),
(70, 2, 4, 25, '2025-04-08 01:17:17', 'accepted', 4, 'Excellent work!'),
(71, 14, 20, 2, '2025-05-18 19:37:01', 'accepted', 2, 'Met my expectations.'),
(72, 41, 17, 16, '2025-04-23 12:24:08', 'accepted', 5, 'Will hire again.'),
(73, 2, 4, 5, '2025-05-01 06:12:51', 'accepted', 4, 'Quality could be better.'),
(74, 42, 20, 3, '2025-04-25 02:04:01', 'accepted', 5, 'Satisfied with the service.'),
(75, 36, 20, 13, '2025-05-01 20:49:19', 'accepted', 5, 'Met my expectations.'),
(76, 4, 17, 25, '2025-05-18 04:52:15', 'accepted', 4, 'Reliable and trustworthy.'),
(77, 1, 20, 25, '2025-04-27 05:53:17', 'accepted', 5, 'Will hire again.'),
(78, 60, 21, 13, '2025-04-12 11:30:21', 'accepted', 4, 'Great work!'),
(79, 60, 4, 16, '2025-04-28 11:22:50', 'rejected', NULL, 'NULL'),
(80, 35, 21, 14, '2025-04-27 12:18:49', 'rejected', NULL, 'NULL'),
(81, 9, 20, 25, '2025-05-01 18:33:05', 'accepted', 2, 'Quick and efficient.'),
(82, 42, 8, 2, '2025-04-20 08:07:32', 'accepted', 1, 'Met my expectations.'),
(83, 59, 21, 16, '2025-04-09 00:07:49', 'accepted', 5, 'Got the job done.'),
(84, 60, 18, 5, '2025-05-04 04:41:59', 'accepted', 5, 'Met my expectations.'),
(85, 36, 20, 5, '2025-04-25 01:18:58', 'accepted', 4, 'Felt a bit rushed.'),
(86, 60, 18, 25, '2025-05-13 09:52:15', 'accepted', 5, 'Quality could be better.'),
(87, 1, 18, 14, '2025-04-10 17:07:28', 'accepted', 4, 'Left a few spots.'),
(88, 59, 4, 13, '2025-04-27 04:27:30', 'accepted', 4, 'Expected a bit more.');

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
CREATE TABLE IF NOT EXISTS `profile` (
  `profile_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(256) NOT NULL,
  `last_name` varchar(256) NOT NULL,
  `about` varchar(256) NOT NULL,
  `gender` varchar(16) NOT NULL,
  `profile_image` longblob DEFAULT NULL,
  `status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`profile_id`),
  KEY `user_id` (`user_id`),
  KEY `status_id` (`status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`profile_id`, `user_id`, `first_name`, `last_name`, `about`, `gender`, `profile_image`, `status_id`) VALUES
(1, 1, 'John', 'Doe', 'I am the only user admin here.', 'Male', NULL, 1),
(2, 2, 'Jack', 'Tan', 'Cleaning services at affordable prices!', 'M', NULL, 1),
(3, 3, 'Jane ', 'Lim', 'Speedy cleaning solutions!', 'Female', NULL, 1),
(4, 4, 'Hairy', 'Harry', '5 Room BTO', 'Male', NULL, 1),
(5, 5, 'Adam', 'Lim', 'Luxury Cleaning Solutions', 'Male', NULL, 1),
(6, 6, 'Selena', 'Gomez', 'Landed', 'Female', NULL, 1),
(7, 7, 'Platform', 'Manager', 'i manage this platform', 'Female', NULL, 1),
(9, 8, 'Justin', 'Bieber', '5 Room semi-detached', 'Male', NULL, 1),
(10, 9, 'Alice', 'Wong', '4 Room apartment owner', 'Female', NULL, 1),
(11, 10, 'Bob', 'Cheng', 'Experienced cleaner', 'Male', NULL, 1),
(12, 11, 'Clara', 'Lee', 'Professional cleaning expert', 'Female', NULL, 1),
(13, 12, 'David', 'Ng', 'Specializes in office cleaning', 'Male', NULL, 1),
(14, 13, 'Emma', 'Tan', 'Passionate about cleanliness', 'Female', NULL, 1),
(15, 14, 'Frank', 'Lim', 'Dedicated to spotless homes', 'Male', NULL, 1),
(16, 15, 'Grace', 'Lau', 'Reliable cleaning service', 'Female', NULL, 1),
(17, 16, 'Harry', 'Khoo', 'Detail-oriented cleaner', 'Male', NULL, 1),
(18, 17, 'Isabel', 'Ong', 'Homeowner of a 5-room flat', 'Female', NULL, 1),
(19, 18, 'Jason', 'Goh', 'Cares about a clean living space', 'Male', NULL, 1),
(20, 19, 'Kelly', 'Yeo', 'Owner of a condominium', 'Female', NULL, 1),
(21, 20, 'Leon', 'Wong', 'Happy homeowner', 'Male', NULL, 1),
(22, 21, 'Maya', 'Tan', 'Enjoys gardening and clean homes', 'Female', NULL, 1),
(23, 22, 'Nathan', 'Lee', 'Homeowner and pet lover', 'Male', NULL, 1),
(24, 23, 'Olivia', 'Chua', 'Clean and tidy specialist', 'Female', NULL, 1),
(25, 24, 'Paul', 'Lim', 'Committed cleaner', 'Male', NULL, 1),
(26, 25, 'Quinn', 'Tan', 'Loves organized spaces', 'Female', NULL, 1),
(27, 26, 'Ryan', 'Ng', 'Focused on quality service', 'Male', NULL, 1),
(28, 27, 'Sabrina', 'Lau', 'Enjoys helping homeowners', 'Female', NULL, 1),
(29, 28, 'Timothy', 'Goh', 'Experienced in deep cleaning', 'Male', NULL, 1),
(30, 29, 'Usha', 'Khoo', 'Cleaning is my passion', 'Female', NULL, 1),
(31, 30, 'Victor', 'Lim', 'Ensuring spotless homes', 'Male', NULL, 1),
(32, 31, 'Wendy', 'Ong', 'Homeowner of a HDB flat', 'Female', NULL, 1),
(33, 32, 'Xavier', 'Tan', 'Reliable cleaning professional', 'Male', NULL, 1),
(34, 33, 'Yvonne', 'Lee', 'Keeping homes sparkling', 'Female', NULL, 1),
(35, 34, 'Zachary', 'Chua', 'Efficient cleaner', 'Male', NULL, 1),
(36, 35, 'Amy', 'Lim', 'Professional and punctual', 'Female', NULL, 1),
(37, 36, 'Brian', 'Tan', 'Focused on client satisfaction', 'Male', NULL, 1),
(38, 37, 'Chloe', 'Ng', 'Loves helping families', 'Female', NULL, 1),
(39, 38, 'Derek', 'Lau', 'Passionate about cleaning', 'Male', NULL, 1),
(40, 39, 'Elaine', 'Goh', 'Dedicated to detail', 'Female', NULL, 1),
(41, 40, 'Felix', 'Khoo', 'Experienced cleaner', 'Male', NULL, 1),
(42, 41, 'Gina', 'Lim', 'Homeowner and community lover', 'Female', NULL, 1),
(43, 42, 'Henry', 'Ong', 'Clean home enthusiast', 'Male', NULL, 1),
(44, 43, 'Irene', 'Tan', 'Focused on quality service', 'Female', NULL, 1),
(45, 44, 'Jack', 'Lee', 'Detail-oriented cleaner', 'Male', NULL, 1),
(46, 45, 'Karen', 'Chua', 'Happy homeowner', 'Female', NULL, 1),
(47, 46, 'Liam', 'Lim', 'Professional cleaner', 'Male', NULL, 1),
(48, 47, 'Mia', 'Tan', 'Enjoys tidy spaces', 'Female', NULL, 1),
(49, 48, 'Noah', 'Ng', 'Committed cleaner', 'Male', NULL, 1),
(50, 49, 'Olga', 'Lau', 'Clean home advocate', 'Female', NULL, 1),
(51, 50, 'Peter', 'Goh', 'Experienced in all cleaning', 'Male', NULL, 1),
(52, 51, 'Queenie', 'Khoo', 'Reliable and friendly', 'Female', NULL, 1),
(53, 52, 'Robert', 'Lim', 'Homeowner and pet lover', 'Male', NULL, 1),
(54, 53, 'Susan', 'Ong', 'Efficient cleaning expert', 'Female', NULL, 1),
(55, 54, 'Thomas', 'Tan', 'Focused on client needs', 'Male', NULL, 1),
(56, 55, 'Ulysses', 'Lee', 'Passionate cleaner', 'Male', NULL, 1),
(57, 56, 'Victoria', 'Chua', 'Caring homeowner', 'Female', NULL, 1),
(58, 57, 'William', 'Lim', 'Experienced cleaner', 'Male', NULL, 1),
(59, 58, 'Xena', 'Tan', 'Dedicated to cleanliness', 'Female', NULL, 1),
(60, 59, 'Yusuf', 'Ng', 'Reliable service provider', 'Male', NULL, 1),
(61, 60, 'Zara', 'Lau', 'Homeowner and cleaner', 'Female', NULL, 1),
(62, 61, 'Adam', 'Goh', 'Enjoys helping others', 'Male', NULL, 1),
(63, 62, 'Bella', 'Khoo', 'Loves organized homes', 'Female', NULL, 1),
(64, 63, 'Calvin', 'Lim', 'Dedicated cleaner', 'Male', NULL, 1),
(65, 64, 'Diana', 'Ong', 'Passionate about cleaning', 'Female', NULL, 1),
(66, 65, 'Ethan', 'Tan', 'Professional cleaner', 'Male', NULL, 1),
(67, 66, 'Fiona', 'Lee', 'Homeowner and cleaner', 'Female', NULL, 1),
(68, 67, 'George', 'Chua', 'Reliable and efficient', 'Male', NULL, 1),
(69, 68, 'Hannah', 'Lim', 'Cares about cleanliness', 'Female', NULL, 1),
(70, 69, 'Ian', 'Tan', 'Experienced cleaner', 'Male', NULL, 1),
(71, 70, 'Jenny', 'Ng', 'Homeowner and organizer', 'Female', NULL, 1),
(72, 71, 'Kevin', 'Lau', 'Reliable cleaning professional', 'Male', NULL, 1),
(73, 72, 'Laura', 'Goh', 'Passionate about tidy homes', 'Female', NULL, 1),
(74, 73, 'Mark', 'Khoo', 'Experienced cleaner', 'Male', NULL, 1),
(75, 74, 'Nina', 'Lim', 'Homeowner and cleaner', 'Female', NULL, 1),
(76, 75, 'Owen', 'Ong', 'Efficient and friendly', 'Male', NULL, 1),
(77, 76, 'Paula', 'Tan', 'Loves clean homes', 'Female', NULL, 1),
(78, 77, 'Quentin', 'Lee', 'Detail-oriented cleaner', 'Male', NULL, 1),
(79, 78, 'Rachel', 'Chua', 'Homeowner and cleaner', 'Female', NULL, 1),
(80, 79, 'Steven', 'Lim', 'Reliable cleaner', 'Male', NULL, 1),
(81, 80, 'Tina', 'Tan', 'Passionate about cleaning', 'Female', NULL, 1),
(82, 81, 'Umar', 'Ng', 'Dedicated cleaning professional', 'Male', NULL, 1),
(83, 82, 'Vera', 'Lau', 'Organized and reliable', 'Female', NULL, 1),
(84, 83, 'Wesley', 'Goh', 'Experienced cleaner', 'Male', NULL, 1),
(85, 84, 'Ximena', 'Khoo', 'Homeowner and cleaner', 'Female', NULL, 1),
(86, 85, 'Yann', 'Lim', 'Efficient cleaner', 'Male', NULL, 1),
(87, 86, 'Zoe', 'Ong', 'Passionate about tidiness', 'Female', NULL, 1),
(88, 87, 'Aaron', 'Tan', 'Reliable and friendly', 'Male', NULL, 1),
(89, 88, 'Brenda', 'Lee', 'Homeowner and cleaner', 'Female', NULL, 1),
(90, 89, 'Carl', 'Chua', 'Experienced cleaning professional', 'Male', NULL, 1),
(91, 90, 'Denise', 'Lim', 'Passionate about cleaning', 'Female', NULL, 1),
(92, 91, 'Eli', 'Tan', 'Dedicated cleaner', 'Male', NULL, 1),
(93, 92, 'Faith', 'Ng', 'Organized and reliable', 'Female', NULL, 1),
(94, 93, 'Gavin', 'Lau', 'Efficient cleaner', 'Male', NULL, 1),
(95, 94, 'Holly', 'Goh', 'Homeowner and cleaner', 'Female', NULL, 1),
(96, 95, 'Ian', 'Khoo', 'Experienced cleaner', 'Male', NULL, 1),
(97, 96, 'Jane', 'Lim', 'Passionate about tidy homes', 'Female', NULL, 1),
(98, 97, 'Kyle', 'Tan', 'Reliable cleaning service', 'Male', NULL, 1),
(99, 98, 'Lily', 'Lee', 'Homeowner and cleaner', 'Female', NULL, 1),
(100, 99, 'Mike', 'Chua', 'Experienced cleaner', 'Male', NULL, 1),
(101, 100, 'Nora', 'Lim', 'Dedicated to cleanliness', 'Female', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(256) DEFAULT NULL,
  `role_description` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_name`, `role_description`) VALUES
(1, 'User Admin', 'super admin'),
(2, 'Cleaner', 'cleaner can create listing and view all listing'),
(3, 'Home Owner', 'home owner can view listing and review listing!'),
(4, 'Platform Management', 'manage cleaning services and get reports');

-- --------------------------------------------------------

--
-- Table structure for table `service_categories`
--

DROP TABLE IF EXISTS `service_categories`;
CREATE TABLE IF NOT EXISTS `service_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) NOT NULL,
  `status_id` int(11) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `service_categories`
--

INSERT INTO `service_categories` (`category_id`, `category`, `status_id`) VALUES
(1, 'General Cleaning', 1),
(2, 'Window Cleaning', 1),
(3, 'Renovation Cleaning', 1),
(4, 'Bedroom Cleaning', 1),
(5, 'Living Room Cleaning', 1),
(6, 'Kitchen Cleaning', 1),
(7, 'Furniture', 1),
(8, 'Lighting', 1),
(9, 'Pet Cleaning', 1),
(10, 'Detailing', 1);

-- --------------------------------------------------------

--
-- Table structure for table `shortlist`
--

DROP TABLE IF EXISTS `shortlist`;
CREATE TABLE IF NOT EXISTS `shortlist` (
  `shortlist_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `shortlist_date` date DEFAULT curdate(),
  PRIMARY KEY (`shortlist_id`),
  KEY `buyer_id` (`user_id`),
  KEY `listing_id` (`service_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shortlist`
--

INSERT INTO `shortlist` (`shortlist_id`, `user_id`, `service_id`, `shortlist_date`) VALUES
(4, 4, 1, '2025-05-19'),
(5, 4, 9, '2025-05-19'),
(6, 4, 19, '2025-05-19'),
(7, 8, 2, '2025-05-19'),
(8, 17, 14, '2025-05-19'),
(9, 18, 2, '2025-05-19'),
(10, 18, 13, '2025-05-19'),
(11, 8, 59, '2025-05-19'),
(12, 8, 60, '2025-05-19'),
(13, 20, 35, '2025-05-19'),
(14, 20, 36, '2025-05-19'),
(15, 21, 41, '2025-05-19'),
(16, 21, 42, '2025-05-19'),
(17, 18, 14, '2025-05-19'),
(18, 18, 31, '2025-05-19'),
(19, 18, 42, '2025-05-19'),
(20, 18, 57, '2025-05-19'),
(21, 9, 19, '2025-05-19'),
(22, 9, 15, '2025-05-19'),
(23, 9, 14, '2025-05-19'),
(24, 9, 58, '2025-05-19'),
(25, 9, 41, '2025-05-19'),
(26, 21, 29, '2025-05-19'),
(27, 21, 17, '2025-05-19'),
(28, 21, 55, '2025-05-19'),
(29, 21, 1, '2025-05-19'),
(30, 33, 19, '2025-05-19'),
(31, 33, 56, '2025-05-19'),
(32, 33, 58, '2025-05-19'),
(33, 33, 20, '2025-05-19'),
(34, 33, 1, '2025-05-19'),
(35, 33, 4, '2025-05-19'),
(36, 17, 38, '2025-05-19'),
(37, 17, 4, '2025-05-19'),
(38, 17, 9, '2025-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
CREATE TABLE IF NOT EXISTS `status` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `status_name` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_id`, `status_name`) VALUES
(1, 'Active'),
(2, 'Suspended');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `email` varchar(256) NOT NULL,
  `phone_num` varchar(256) NOT NULL,
  `status_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone_num` (`phone_num`),
  KEY `role_id` (`role_id`),
  KEY `status_id` (`status_id`)
) ENGINE=MyISAM AUTO_INCREMENT=111 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role_id`, `email`, `phone_num`, `status_id`) VALUES
(1, 'superadmin', 'testing', 1, 'superadmin@mail.com', '99120912', 1),
(8, 'homeowner3', 'testing', 3, 'homeowner3@mail.com', '99123214', 1),
(7, 'pm1', 'testing', 4, 'pm1@mail.com', '89981221', 1),
(6, 'homeowner2', 'testing', 3, 'homeowner2@mail.com', '88118822', 1),
(5, 'cleaner3', 'testing', 2, 'cleaner3@mail.com', '80801234', 1),
(4, 'homeowner1', 'testing', 3, 'homeowner1@mail.com', '92291000', 1),
(2, 'cleaner1', 'testing', 2, 'cleaner1@mail.com', '91239123', 1),
(3, 'cleaner2', 'testing', 2, 'cleaner2@mail.com', '92229222', 1),
(9, 'homeowner4', 'testing', 3, 'homeowner4@mail.com', '99990000', 1),
(10, 'cleaner4', 'testing', 2, 'cleaner4@example.com', '91234567', 1),
(11, 'cleaner5', 'testing', 2, 'cleaner5@example.com', '91234568', 1),
(12, 'cleaner6', 'testing', 2, 'cleaner6@example.com', '92345679', 1),
(13, 'cleaner7', 'testing', 2, 'cleaner7@example.com', '93456780', 1),
(14, 'cleaner8', 'testing', 2, 'cleaner8@example.com', '94567881', 1),
(15, 'cleaner9', 'testing', 2, 'cleaner9@example.com', '95678982', 1),
(16, 'cleaner10', 'testing', 2, 'cleaner10@example.com', '96789083', 1),
(17, 'homeowner5', 'testing', 3, 'homeowner5@example.com', '87890124', 1),
(18, 'homeowner6', 'testing', 3, 'homeowner6@example.com', '88801235', 1),
(19, 'homeowner7', 'testing', 3, 'homeowner7@example.com', '89812346', 1),
(20, 'homeowner8', 'testing', 3, 'homeowner8@example.com', '81234567', 1),
(21, 'homeowner9', 'testing', 3, 'homeowner9@example.com', '82345678', 1),
(22, 'homeowner10', 'testing', 3, 'homeowner10@example.com', '83456789', 1),
(23, 'cleaner11', 'testing', 2, 'cleaner11@example.com', '97890125', 1),
(24, 'cleaner12', 'testing', 2, 'cleaner12@example.com', '98801236', 1),
(25, 'cleaner13', 'testing', 2, 'cleaner13@example.com', '99812347', 1),
(26, 'cleaner14', 'testing', 2, 'cleaner14@example.com', '98765438', 1),
(27, 'cleaner15', 'testing', 2, 'cleaner15@example.com', '97654329', 1),
(28, 'cleaner16', 'testing', 2, 'cleaner16@example.com', '96543210', 1),
(29, 'cleaner17', 'testing', 2, 'cleaner17@example.com', '95432101', 1),
(30, 'cleaner18', 'testing', 2, 'cleaner18@example.com', '94321012', 1),
(31, 'cleaner19', 'testing', 2, 'cleaner19@example.com', '93210123', 1),
(32, 'cleaner20', 'testing', 2, 'cleaner20@example.com', '92101234', 1),
(33, 'homeowner11', 'testing', 3, 'homeowner11@example.com', '85678902', 1),
(34, 'homeowner12', 'testing', 3, 'homeowner12@example.com', '86789013', 1),
(35, 'cleaner21', 'testing', 2, 'cleaner21@example.com', '91123456', 1),
(36, 'cleaner22', 'testing', 2, 'cleaner22@example.com', '92234567', 1),
(37, 'cleaner23', 'testing', 2, 'cleaner23@example.com', '93345678', 1),
(38, 'cleaner24', 'testing', 2, 'cleaner24@example.com', '94456789', 1),
(39, 'cleaner25', 'testing', 2, 'cleaner25@example.com', '95567890', 1),
(40, 'cleaner26', 'testing', 2, 'cleaner26@example.com', '96678901', 1),
(41, 'cleaner27', 'testing', 2, 'cleaner27@example.com', '97789012', 1),
(42, 'cleaner28', 'testing', 2, 'cleaner28@example.com', '98890123', 1),
(43, 'cleaner29', 'testing', 2, 'cleaner29@example.com', '99901234', 1),
(44, 'cleaner30', 'testing', 2, 'cleaner30@example.com', '98812345', 1),
(45, 'homeowner13', 'testing', 3, 'homeowner13@example.com', '85678909', 1),
(46, 'homeowner14', 'testing', 3, 'homeowner14@example.com', '86789014', 1),
(47, 'homeowner15', 'testing', 3, 'homeowner15@example.com', '87890125', 1),
(48, 'homeowner16', 'testing', 3, 'homeowner16@example.com', '88801236', 1),
(49, 'homeowner17', 'testing', 3, 'homeowner17@example.com', '89812357', 1),
(50, 'homeowner18', 'testing', 3, 'homeowner18@example.com', '81234568', 1),
(51, 'homeowner19', 'testing', 3, 'homeowner19@example.com', '82345679', 1),
(52, 'homeowner20', 'testing', 3, 'homeowner20@example.com', '83456780', 1),
(53, 'cleaner31', 'testing', 2, 'cleaner31@example.com', '91123457', 1),
(54, 'cleaner32', 'testing', 2, 'cleaner32@example.com', '92234568', 1),
(55, 'cleaner33', 'testing', 2, 'cleaner33@example.com', '93345679', 1),
(56, 'cleaner34', 'testing', 2, 'cleaner34@example.com', '94456780', 1),
(57, 'cleaner35', 'testing', 2, 'cleaner35@example.com', '95567881', 1),
(58, 'cleaner36', 'testing', 2, 'cleaner36@example.com', '96678902', 1),
(59, 'cleaner37', 'testing', 2, 'cleaner37@example.com', '97789013', 1),
(60, 'cleaner38', 'testing', 2, 'cleaner38@example.com', '98890124', 1),
(61, 'homeowner21', 'testing', 3, 'homeowner21@example.com', '84567892', 1),
(62, 'homeowner22', 'testing', 3, 'homeowner22@example.com', '85678903', 1),
(63, 'homeowner23', 'testing', 3, 'homeowner23@example.com', '86789015', 1),
(64, 'homeowner24', 'testing', 3, 'homeowner24@example.com', '87890126', 1),
(65, 'homeowner25', 'testing', 3, 'homeowner25@example.com', '88801237', 1),
(66, 'homeowner26', 'testing', 3, 'homeowner26@example.com', '89812358', 1),
(67, 'homeowner27', 'testing', 3, 'homeowner27@example.com', '81234569', 1),
(68, 'homeowner28', 'testing', 3, 'homeowner28@example.com', '82345680', 1),
(69, 'homeowner29', 'testing', 3, 'homeowner29@example.com', '83456781', 1),
(70, 'homeowner30', 'testing', 3, 'homeowner30@example.com', '84567893', 1),
(71, 'cleaner39', 'testing', 2, 'cleaner39@example.com', '91123458', 1),
(72, 'cleaner40', 'testing', 2, 'cleaner40@example.com', '92234569', 1),
(73, 'cleaner41', 'testing', 2, 'cleaner41@example.com', '93345680', 1),
(74, 'cleaner42', 'testing', 2, 'cleaner42@example.com', '94456781', 1),
(75, 'cleaner43', 'testing', 2, 'cleaner43@example.com', '95567882', 1),
(76, 'cleaner44', 'testing', 2, 'cleaner44@example.com', '96678903', 1),
(77, 'cleaner45', 'testing', 2, 'cleaner45@example.com', '97789014', 1),
(78, 'cleaner46', 'testing', 2, 'cleaner46@example.com', '98890125', 1),
(79, 'homeowner31', 'testing', 3, 'homeowner31@example.com', '85678904', 1),
(80, 'homeowner32', 'testing', 3, 'homeowner32@example.com', '86789016', 1),
(81, 'homeowner33', 'testing', 3, 'homeowner33@example.com', '87890127', 1),
(82, 'homeowner34', 'testing', 3, 'homeowner34@example.com', '88801238', 1),
(83, 'homeowner35', 'testing', 3, 'homeowner35@example.com', '89812359', 1),
(84, 'homeowner36', 'testing', 3, 'homeowner36@example.com', '81234570', 1),
(85, 'homeowner37', 'testing', 3, 'homeowner37@example.com', '82345681', 1),
(86, 'homeowner38', 'testing', 3, 'homeowner38@example.com', '83456782', 1),
(87, 'homeowner39', 'testing', 3, 'homeowner39@example.com', '84567894', 1),
(88, 'homeowner40', 'testing', 3, 'homeowner40@example.com', '85678905', 1),
(89, 'cleaner47', 'testing', 2, 'cleaner47@example.com', '91123459', 1),
(90, 'cleaner48', 'testing', 2, 'cleaner48@example.com', '92234570', 1),
(91, 'cleaner49', 'testing', 2, 'cleaner49@example.com', '93345681', 1),
(92, 'cleaner50', 'testing', 2, 'cleaner50@example.com', '94456782', 1),
(93, 'cleaner51', 'testing', 2, 'cleaner51@example.com', '95567883', 1),
(94, 'cleaner52', 'testing', 2, 'cleaner52@example.com', '96678904', 1),
(95, 'cleaner53', 'testing', 2, 'cleaner53@example.com', '97789015', 1),
(96, 'cleaner54', 'testing', 2, 'cleaner54@example.com', '98890126', 1),
(97, 'homeowner41', 'testing', 3, 'homeowner41@example.com', '85678906', 1),
(98, 'homeowner42', 'testing', 3, 'homeowner42@example.com', '86789017', 1),
(99, 'homeowner43', 'testing', 3, 'homeowner43@example.com', '87890128', 1),
(100, 'homeowner44', 'testing', 3, 'homeowner44@example.com', '88801239', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
