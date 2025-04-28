-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Apr 28, 2025 at 11:06 AM
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
  `service_type` varchar(100) NOT NULL,
  `service_price` double DEFAULT NULL,
  `views` int(10) UNSIGNED DEFAULT 0,
  `shortlisted` int(10) UNSIGNED DEFAULT 0,
  PRIMARY KEY (`service_id`),
  KEY `cleaner_id` (`cleaner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cleaningservices`
--

INSERT INTO `cleaningservices` (`service_id`, `cleaner_id`, `service_title`, `service_description`, `service_type`, `service_price`, `views`, `shortlisted`) VALUES
(1, 2, 'Sparkling Window Cleaning Service', 'Let natural light pour in through spotless, streak-free windows! Our professional window cleaning service ensures a crystal-clear view, removing dirt, smudges, and water stains from both interior and exterior surfaces. Perfect for homes, offices, and high-rise buildings.', 'Window Cleaning', 65, 64, 0),
(2, 2, 'Ultimate DEEP Clean Package', 'Tackle built-up grime with our thorough deep cleaning service. We sanitize kitchens, scrub bathrooms, wipe baseboards, dust vents, and reach the corners regular cleans miss. Ideal for spring cleaning or move-in/move-out scenarios.', 'Deep Cleaning', 155, 26, 0),
(10, 3, 'Post Reno Clean Up Crew', 'Renovations leave dust and debris everywhere. Weâ€™ll do the heavy liftingâ€”clearing dust, paint splatters, and construction residues to reveal your newly renovated space.', 'Post Renovation Cleaning', 180, 9, 0),
(11, 3, 'Mattress Fresh & Sanitized', 'Say goodbye to dust mites, sweat stains, and odors. Our deep steam and vacuum extraction cleaning removes allergens and bacteria for a healthier nightâ€™s sleep.', 'Mattress Cleaning', 70, 2, 0),
(12, 2, 'Standard Home Cleaning', 'General cleaning for apartments and houses including dusting, vacuuming, mopping, and bathroom sanitization.', 'General Cleaning', 70, 0, 0),
(13, 2, 'Carpet Refresh', 'A complete deep-cleaning service for carpets using steam extraction and eco-safe shampoo to remove dirt, allergens, and stains. Ideal for homes with kids or pets. Includes pre-treatment for high-traffic areas and deodorizing.', 'Carpet Cleaning', 90, 0, 0),
(14, 2, 'Pet-Friendly Cleaning', 'Designed for homes with pets, this service includes hair removal from furniture and floors, deodorizing, stain treatment for accidents, and the use of animal-safe cleaning products.', 'Pet Friendly', 100, 0, 0),
(15, 3, 'Move-In / Move-Out Cleaning', 'A meticulous cleaning service for empty homes during transitions. Includes deep cleaning of every room, inside cabinets and drawers, fixtures, vents, and appliances. Designed to help renters and homeowners meet lease requirements or start fresh in a new home.', 'Deep Cleaning, Move-In/Move-Out', 200, 0, 0),
(16, 3, 'Window & Glass Cleaning', 'Interior and exterior cleaning of windows, glass doors, and mirrors using streak-free solutions. Includes screen washing and sill wiping. Optional water-fed pole for high-rise windows (up to 3 stories).', 'Window/Glass Cleaning', 60, 0, 0),
(17, 3, 'Sofa & Upholstery Cleaning', 'Professional cleaning of fabric and leather sofas, chairs, and ottomans using industry-grade tools. Removes embedded dirt, body oils, food stains, and pet odors. Includes protective coating if requested.', 'Furniture Cleaning', 75, 0, 0),
(18, 5, 'Luxury Home Detailing', 'Professional dusting and micro-cleaning of fine art, sculptures, antique woodwork, and high-value collectibles using museum-approved methods. Our trained team uses pH-neutral products and anti-static cloths to preserve integrity and value.', 'Detailing', 500, 0, 0),
(19, 5, 'Chandelier & Lighting Fixture Cleaning', 'Dismantling and hand-cleaning of delicate chandeliers, pendant lights, and designer fixtures using crystal-safe solvents and microfiber polishing. Each prism is cleaned individually for maximum brilliance.', 'Lighting', 300, 0, 0),
(20, 5, 'Private Spa & Sauna Cleaning', 'Meticulous deep cleaning and sanitization of personal saunas, steam rooms, and jacuzzi tubs using non-corrosive, skin-safe agents. Includes descaling of mineral buildup, aromatherapy diffuser cleaning, and towel restocking (optional).', 'Spa/Sauna', 250, 1, 0),
(21, 5, 'Marble & Stone Polishing Service', 'Restorative polishing of luxury stone surfaces including marble, granite, and quartz. Removes micro-scratches, water stains, and etching, leaving a glossy, mirror-like finish. Includes sealing upon request.', 'Polishing', 300, 0, 0);

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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`match_id`, `service_id`, `homeowner_id`, `cleaner_id`, `match_date`, `status`, `rating`, `review`) VALUES
(1, 10, 4, 3, '2025-04-26 16:05:05', 'pending', 0, ''),
(2, 2, 4, 2, '2025-04-26 16:20:37', 'rejected', 0, ''),
(3, 1, 4, 2, '2025-04-26 16:22:38', 'accepted', 5, 'My windows were sparkling and shiny indeed after they were done!'),
(4, 21, 4, 5, '2025-04-26 19:25:13', 'accepted', 4, 'Great service, but price is steep'),
(5, 20, 6, 5, '2025-04-26 19:27:13', 'accepted', 5, 'My spa feels brand new!'),
(6, 21, 6, 5, '2025-04-26 20:05:07', 'accepted', 5, 'super shiny!!');

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`profile_id`, `user_id`, `first_name`, `last_name`, `about`, `gender`, `profile_image`, `status_id`) VALUES
(1, 1, 'John', 'Doe', 'I am the only user admin here.', 'Male', NULL, 1),
(2, 2, 'Jack', 'Tan', 'Cleaning services at affordable prices!', 'Male', NULL, 1),
(3, 3, 'Jane ', 'Lim', 'Speedy cleaning solutions!', 'Female', NULL, 1),
(4, 4, 'Hairy', 'Harry', '4 Room BTO', 'Male', NULL, 1),
(5, 5, 'Adam', 'Lim', 'Luxury Cleaning Solutions', 'Male', NULL, 1),
(6, 6, 'Selena', 'Gomez', 'Landed', 'Female', NULL, 1),
(7, 7, 'Platform', 'Manager', 'i manage this platform', 'Female', NULL, 1),
(9, 8, 'Justin', 'Bieber', '5 Room semi-detached', 'Male', NULL, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shortlist`
--

INSERT INTO `shortlist` (`shortlist_id`, `user_id`, `service_id`, `shortlist_date`) VALUES
(18, 6, 20, '2025-04-26'),
(24, 6, 21, '2025-04-26'),
(25, 6, 2, '2025-04-28');

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
) ENGINE=MyISAM AUTO_INCREMENT=109 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role_id`, `email`, `phone_num`, `status_id`) VALUES
(1, 'superadmin', 'testing', 1, 'superadmin@mail.com', '+6590901212', 1),
(8, 'homeowner3', 'testing', 3, 'homeowner3@mail.com', '+6599220088', 1),
(7, 'pm1', 'testing', 4, 'pm1@mail.com', '+6589981221', 1),
(6, 'homeowner2', 'testing', 3, 'homeowner2@mail.com', '+6588118822', 1),
(5, 'cleaner3', 'testing', 2, 'cleaner3@mail.com', '+6580801234', 1),
(4, 'homeowner1', 'testing', 3, 'homeowner1@mail.com', '+6592291000', 1),
(2, 'cleaner1', 'testing', 2, 'cleaner1@mail.com', '+6591239123', 1),
(3, 'cleaner2', 'testing', 2, 'cleaner2@mail.com', '+6592229222', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
