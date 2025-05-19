-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 19, 2025 at 07:18 AM
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
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cleaningservices`
--

INSERT INTO `cleaningservices` (`service_id`, `cleaner_id`, `service_title`, `service_description`, `service_category`, `service_price`, `views`, `shortlisted`) VALUES
(1, 2, 'Sparkling Window Cleaning Service', 'Let natural light pour in through spotless, streak-free windows! Our professional window cleaning service ensures a crystal-clear view, removing dirt, smudges, and water stains from both interior and exterior surfaces. Perfect for homes, offices, and high-rise buildings.', 2, 65, 75, 0),
(2, 2, 'Ultimate DEEP Clean Package', 'Tackle built-up grime with our thorough deep cleaning service. We sanitize kitchens, scrub bathrooms, wipe baseboards, dust vents, and reach the corners regular cleans miss. Ideal for spring cleaning or move-in/move-out scenarios.', 1, 155, 26, 0),
(3, 3, 'Eco-Friendly Kitchen Deep Clean', 'Thorough cleaning of kitchen surfaces, appliances, and cabinets using environmentally safe products. Ideal for keeping your kitchen spotless and toxin-free.', 6, 120, 30, 0),
(4, 3, 'Bathroom Sanitization and Mold Removal', 'Comprehensive bathroom cleaning including sanitizing toilets, sinks, showers, and removal of mold and mildew with professional-grade cleaners.', 1, 130, 18, 0),
(5, 4, 'Comprehensive Garage Cleaning', 'Clear out dust, dirt, and debris from your garage, including floor scrubbing and organizing services.', 10, 110, 10, 0),
(6, 4, 'Eco-Safe Kitchen Appliance Cleaning', 'Deep cleaning of ovens, refrigerators, microwaves, and dishwashers with eco-friendly products to remove grease and grime.', 6, 95, 14, 0),
(7, 4, 'Office Workspace Sanitizing', 'Disinfect and sanitize desks, chairs, keyboards, and common areas to promote a healthy work environment.', 1, 85, 25, 0),
(8, 5, 'Specialized Leather Sofa Cleaning', 'Gentle yet thorough cleaning of leather sofas and chairs to maintain softness and remove stains without damage.', 7, 140, 20, 0),
(9, 5, 'Allergy-Reducing Bedroom Cleaning', 'Target dust mites, allergens, and pet dander in bedrooms with steam cleaning, vacuuming, and hypoallergenic products.', 4, 110, 12, 0),
(10, 3, 'Post Reno Clean Up Crew', 'Renovations leave dust and debris everywhere. Weâ€™ll do the heavy liftingâ€”clearing dust, paint splatters, and construction residues to reveal your newly renovated space.', 3, 180, 9, 0),
(11, 3, 'Mattress Fresh & Sanitized', 'Say goodbye to dust mites, sweat stains, and odors. Our deep steam and vacuum extraction cleaning removes allergens and bacteria for a healthier nightâ€™s sleep.', 4, 70, 2, 0),
(12, 2, 'Standard Home Cleaning', 'General cleaning for apartments and houses including dusting, vacuuming, mopping, and bathroom sanitization.', 1, 70, 1, 0),
(13, 2, 'Carpet Refresh', 'A complete deep-cleaning service for carpets using steam extraction and eco-safe shampoo to remove dirt, allergens, and stains. Ideal for homes with kids or pets. Includes pre-treatment for high-traffic areas and deodorizing.', 5, 90, 0, 0),
(14, 2, 'Pet-Friendly Cleaning', 'Designed for homes with pets, this service includes hair removal from furniture and floors, deodorizing, stain treatment for accidents, and the use of animal-safe cleaning products.', 9, 100, 1, 0),
(15, 3, 'Move-In / Move-Out Cleaning', 'A meticulous cleaning service for empty homes during transitions. Includes deep cleaning of every room, inside cabinets and drawers, fixtures, vents, and appliances. Designed to help renters and homeowners meet lease requirements or start fresh in a new home.', 3, 200, 0, 0),
(16, 3, 'Window & Glass Cleaning', 'Interior and exterior cleaning of windows, glass doors, and mirrors using streak-free solutions. Includes screen washing and sill wiping. Optional water-fed pole for high-rise windows (up to 3 stories).', 2, 60, 0, 0),
(17, 3, 'Sofa & Upholstery Cleaning', 'Professional cleaning of fabric and leather sofas, chairs, and ottomans using industry-grade tools. Removes embedded dirt, body oils, food stains, and pet odors. Includes protective coating if requested.', 7, 75, 0, 0),
(18, 5, 'Luxury Home Detailing', 'Professional dusting and micro-cleaning of fine art, sculptures, antique woodwork, and high-value collectibles using museum-approved methods. Our trained team uses pH-neutral products and anti-static cloths to preserve integrity and value.', 10, 500, 0, 0),
(19, 5, 'Chandelier & Lighting Fixture Cleaning', 'Dismantling and hand-cleaning of delicate chandeliers, pendant lights, and designer fixtures using crystal-safe solvents and microfiber polishing. Each prism is cleaned individually for maximum brilliance.', 8, 300, 0, 0),
(20, 5, 'Marble & Stone Polishing Service', 'Restorative polishing of luxury stone surfaces including marble, granite, and quartz. Removes micro-scratches, water stains, and etching, leaving a glossy, mirror-like finish. Includes sealing upon request.', 10, 300, 0, 0),
(21, 6, 'Eco-Friendly Kitchen Cleaning', 'Using biodegradable and non-toxic products, we deep clean your kitchen surfaces, appliances, and floors while keeping your environment safe and green.', 6, 85, 12, 0),
(22, 6, 'Fridge and Oven Deep Clean', 'Detailed cleaning and sanitizing of fridge interiors, ovens, and microwaves to remove food residues, grease, and odors.', 6, 120, 7, 0),
(23, 7, 'Bathroom Sanitization Service', 'Disinfect and sanitize toilets, sinks, showers, and floors with hospital-grade cleaners to maintain a hygienic bathroom.', 4, 80, 20, 1),
(24, 7, 'Tile and Grout Cleaning', 'Remove dirt, mold, and stains from tiles and grout lines in bathrooms and kitchens using steam cleaning.', 6, 95, 8, 0),
(25, 8, 'Pet Odor Removal', 'Eliminate stubborn pet odors and stains from carpets, upholstery, and floors using enzymatic cleaners.', 9, 110, 15, 1),
(26, 8, 'Air Duct and Vent Cleaning', 'Improve air quality by removing dust and allergens from your home’s vents and air ducts.', 1, 140, 5, 0),
(27, 9, 'Curtain and Blind Cleaning', 'Professional cleaning of curtains, blinds, and drapes to remove dust and allergens without damage.', 7, 75, 10, 0),
(28, 9, 'Mattress Deep Steam Cleaning', 'Eliminate dust mites, allergens, and odors from mattresses with deep steam extraction.', 4, 90, 25, 1),
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
(43, 17, 'Ceiling Fan and Light Fixture Cleaning', 'Removal of dust and cobwebs from ceiling fans and light fixtures to improve air quality and lighting.', 8, 85, 7, 0),
(44, 17, 'Wall and Baseboard Cleaning', 'Detailed cleaning of walls and baseboards to remove scuff marks and dust buildup.', 1, 75, 11, 0),
(45, 18, 'Luxury Upholstery Cleaning', 'Deep cleaning of high-end upholstery fabrics and materials with gentle, non-damaging methods.', 7, 220, 9, 0),
(46, 18, 'Antique Furniture Care', 'Careful cleaning and preservation of antique wooden furniture.', 7, 250, 4, 0),
(47, 19, 'Exterior Pressure Washing', 'Remove dirt, mold, and mildew from exterior walls, patios, and driveways with professional pressure washing.', 1, 160, 10, 0),
(48, 19, 'Deck and Fence Cleaning', 'Deep cleaning and restoration of wooden decks and fences.', 1, 140, 12, 0),
(49, 20, 'Home Office Cleaning', 'Sanitize and organize your home office space for better productivity.', 1, 120, 8, 0),
(50, 20, 'Computer and Electronics Dusting', 'Safe dust removal from computers, printers, and other electronics.', 1, 80, 5, 0),
(51, 21, 'Pet-Safe Floor Cleaning', 'Mop and clean floors with products safe for pets and children.', 9, 75, 9, 0),
(52, 21, 'Green Cleaning for Sensitive Skin', 'Use hypoallergenic and fragrance-free products for sensitive individuals.', 1, 90, 6, 0),
(53, 22, 'Basement Mold Remediation Cleaning', 'Cleaning and treatment services targeting mold and mildew in basements.', 1, 190, 4, 0),
(54, 22, 'Appliance Exterior Cleaning', 'Cleaning exteriors of kitchen appliances including refrigerators, ovens, and dishwashers.', 6, 70, 11, 0),
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
) ENGINE=MyISAM AUTO_INCREMENT=51 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`match_id`, `service_id`, `homeowner_id`, `cleaner_id`, `match_date`, `status`, `rating`, `review`) VALUES
(1, 10, 4, 3, '2025-04-26 16:05:05', 'pending', 0, ''),
(2, 2, 4, 2, '2025-04-26 16:20:37', 'rejected', 0, ''),
(3, 1, 4, 2, '2025-04-26 16:22:38', 'accepted', 5, 'My windows were sparkling and shiny indeed after they were done!'),
(4, 21, 4, 5, '2025-04-26 19:25:13', 'accepted', 4, 'Great service, but price is steep'),
(5, 20, 6, 5, '2025-04-26 19:27:13', 'accepted', 5, 'My spa feels brand new!'),
(6, 21, 6, 5, '2025-04-26 20:05:07', 'accepted', 5, 'super shiny!!'),
(7, 3, 2, 1, '2025-05-01 10:15:00', 'accepted', 4, 'Good cleaning, but a bit late.'),
(8, 5, 6, 4, '2025-05-02 09:00:00', 'pending', 0, ''),
(9, 11, 4, 3, '2025-05-19 14:16:13', 'pending', NULL, NULL),
(10, 7, 1, 2, '2025-04-27 12:30:22', 'accepted', 5, 'Excellent service, very thorough!'),
(11, 9, 2, 3, '2025-04-28 15:45:12', 'rejected', 0, ''),
(12, 4, 4, 1, '2025-04-29 08:10:05', 'accepted', 3, 'Satisfactory, but room for improvement.'),
(13, 6, 6, 2, '2025-04-30 16:50:30', 'accepted', 5, 'Very professional and neat work.'),
(14, 8, 1, 3, '2025-05-01 13:25:45', 'pending', 0, ''),
(15, 10, 2, 4, '2025-05-02 17:00:00', 'accepted', 4, 'Good job on the cleaning.'),
(16, 12, 4, 2, '2025-05-03 11:45:35', 'accepted', 5, 'Superb detail cleaning.'),
(17, 14, 6, 1, '2025-05-04 09:30:50', 'pending', 0, ''),
(18, 13, 1, 5, '2025-05-05 14:15:20', 'accepted', 5, 'Very happy with the service!'),
(19, 15, 2, 3, '2025-05-06 10:05:40', 'rejected', 0, ''),
(20, 16, 4, 4, '2025-05-07 08:45:15', 'accepted', 4, 'Good, but took longer than expected.'),
(21, 17, 6, 2, '2025-05-08 12:00:00', 'pending', 0, ''),
(22, 18, 1, 1, '2025-05-09 15:30:10', 'accepted', 5, 'Perfect cleaning service!'),
(23, 19, 2, 5, '2025-05-10 09:45:55', 'accepted', 5, 'Very polite and efficient.'),
(24, 20, 4, 3, '2025-05-11 11:20:05', 'pending', 0, ''),
(25, 21, 6, 4, '2025-05-12 16:40:35', 'accepted', 4, 'Nice and clean, will book again.'),
(26, 3, 1, 2, '2025-05-13 10:15:00', 'accepted', 5, 'Spotless! Highly recommend.'),
(27, 5, 2, 3, '2025-05-14 14:05:25', 'rejected', 0, ''),
(28, 7, 4, 1, '2025-05-15 09:30:50', 'accepted', 3, 'Okay service, could be better.'),
(29, 9, 6, 5, '2025-05-16 13:45:10', 'pending', 0, ''),
(30, 4, 1, 2, '2025-05-17 11:00:00', 'accepted', 5, 'Excellent and fast cleaning!'),
(31, 6, 2, 3, '2025-05-18 10:30:30', 'accepted', 5, 'Very satisfied.'),
(32, 8, 4, 4, '2025-05-18 12:15:45', 'pending', 0, ''),
(33, 10, 6, 1, '2025-04-28 16:25:55', 'accepted', 5, 'Highly recommend this cleaner!'),
(34, 12, 1, 2, '2025-04-29 14:10:00', 'rejected', 0, ''),
(35, 14, 2, 3, '2025-04-30 13:00:20', 'accepted', 4, 'Good cleaning, on time.'),
(36, 16, 4, 5, '2025-05-01 08:45:30', 'accepted', 5, 'Perfect!'),
(37, 18, 6, 1, '2025-05-02 15:30:45', 'pending', 0, ''),
(38, 20, 1, 4, '2025-05-03 09:50:15', 'accepted', 5, 'Spotless job!'),
(39, 22, 2, 2, '2025-05-04 10:20:30', 'rejected', 0, ''),
(40, 24, 4, 3, '2025-05-05 11:35:50', 'accepted', 4, 'Good, will book again.'),
(41, 26, 6, 5, '2025-05-06 12:45:00', 'accepted', 5, 'Fantastic cleaning.'),
(42, 28, 1, 2, '2025-05-07 14:00:10', 'pending', 0, ''),
(43, 30, 2, 3, '2025-05-08 15:15:30', 'accepted', 5, 'Highly recommended!'),
(44, 32, 4, 4, '2025-05-09 09:40:20', 'accepted', 4, 'Good work, thanks.'),
(45, 34, 6, 1, '2025-05-10 16:10:55', 'pending', 0, ''),
(46, 36, 1, 5, '2025-05-11 13:25:35', 'accepted', 5, 'Excellent service.'),
(47, 38, 2, 2, '2025-05-12 10:05:45', 'rejected', 0, ''),
(48, 40, 4, 3, '2025-05-13 11:55:00', 'accepted', 4, 'Very good job.'),
(49, 42, 6, 4, '2025-05-14 08:40:15', 'accepted', 5, 'Perfect cleaning experience!'),
(50, 44, 1, 1, '2025-05-15 14:35:30', 'pending', 0, '');

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
(2, 2, 'Jack', 'Tan', 'Cleaning services at affordable prices!', 'Male', NULL, 1),
(3, 3, 'Jane ', 'Lim', 'Speedy cleaning solutions!', 'Female', NULL, 1),
(4, 4, 'Hairy', 'Harry', '4 Room BTO', 'Male', NULL, 1),
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
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shortlist`
--

INSERT INTO `shortlist` (`shortlist_id`, `user_id`, `service_id`, `shortlist_date`) VALUES
(1, 1, 3, '2025-05-10'),
(2, 1, 5, '2025-05-12'),
(3, 2, 7, '2025-05-14'),
(4, 2, 8, '2025-05-11'),
(5, 4, 4, '2025-05-09'),
(6, 4, 9, '2025-05-08'),
(7, 6, 6, '2025-05-07'),
(8, 6, 3, '2025-05-06'),
(9, 1, 10, '2025-05-05'),
(10, 2, 2, '2025-05-04'),
(11, 4, 7, '2025-05-03'),
(12, 6, 5, '2025-05-02'),
(13, 1, 1, '2025-05-01'),
(14, 2, 9, '2025-04-30'),
(15, 4, 8, '2025-04-29'),
(16, 6, 4, '2025-04-28'),
(17, 1, 6, '2025-04-27'),
(18, 6, 20, '2025-04-26'),
(19, 2, 3, '2025-05-15'),
(20, 4, 2, '2025-05-16'),
(21, 6, 7, '2025-05-14'),
(22, 1, 5, '2025-05-13'),
(23, 2, 4, '2025-05-12'),
(24, 6, 21, '2025-04-26'),
(25, 6, 2, '2025-04-28'),
(26, 4, 1, '2025-05-11'),
(27, 6, 9, '2025-05-10'),
(28, 1, 8, '2025-05-09'),
(29, 2, 6, '2025-05-08'),
(30, 4, 3, '2025-05-07'),
(31, 6, 2, '2025-05-06'),
(32, 1, 7, '2025-05-05'),
(33, 2, 14, '2025-05-19'),
(34, 2, 1, '2025-05-19'),
(36, 4, 11, '2025-05-19'),
(37, 4, 20, '2025-05-19'),
(38, 4, 10, '2025-05-19'),
(39, 4, 1, '2025-05-19'),
(40, 2, 5, '2025-05-04'),
(41, 4, 6, '2025-05-03'),
(42, 6, 4, '2025-05-02'),
(43, 1, 3, '2025-05-01'),
(44, 2, 1, '2025-04-30'),
(45, 4, 8, '2025-04-29'),
(46, 6, 9, '2025-04-28'),
(47, 1, 2, '2025-04-27'),
(48, 2, 7, '2025-04-26'),
(49, 4, 5, '2025-04-25'),
(50, 6, 3, '2025-04-24');

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
(1, 'superadmin', 'testing', 1, 'superadmin@mail.com', '+6590901212', 1),
(8, 'homeowner3', 'testing', 3, 'homeowner3@mail.com', '+6599220088', 1),
(7, 'pm1', 'testing', 4, 'pm1@mail.com', '+6589981221', 1),
(6, 'homeowner2', 'testing', 3, 'homeowner2@mail.com', '+6588118822', 1),
(5, 'cleaner3', 'testing', 2, 'cleaner3@mail.com', '+6580801234', 1),
(4, 'homeowner1', 'testing', 3, 'homeowner1@mail.com', '+6592291000', 1),
(2, 'cleaner1', 'testing', 2, 'cleaner1@mail.com', '+6591239123', 1),
(3, 'cleaner2', 'testing', 2, 'cleaner2@mail.com', '+6592229222', 1),
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
