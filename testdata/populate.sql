-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 14, 2025 at 12:34 PM
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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cleaningservices`
--

INSERT INTO `cleaningservices` (`service_id`, `cleaner_id`, `service_title`, `service_description`, `service_type`, `service_price`, `views`, `shortlisted`) VALUES
(1, 2, 'Sparkling Window Cleaning Service', 'Let natural light pour in through spotless, streak-free windows! Our professional window cleaning service ensures a crystal-clear view, removing dirt, smudges, and water stains from both interior and exterior surfaces. Perfect for homes, offices, and high-rise buildings.', 'Window Cleaning', 65, 4, 2),
(2, 2, 'Ultimate DEEP Clean Package', 'Tackle built-up grime with our thorough deep cleaning service. We sanitize kitchens, scrub bathrooms, wipe baseboards, dust vents, and reach the corners regular cleans miss. Ideal for spring cleaning or move-in/move-out scenarios.', 'Deep Cleaning', 155, 37, 12),
(10, 3, 'Post Reno Clean Up Crew', 'Renovations leave dust and debris everywhere. Weâ€™ll do the heavy liftingâ€”clearing dust, paint splatters, and construction residues to reveal your newly renovated space.', 'Post Renovation Cleaning', 180, 24, 11),
(11, 3, 'Mattress Fresh & Sanitized', 'Say goodbye to dust mites, sweat stains, and odors. Our deep steam and vacuum extraction cleaning removes allergens and bacteria for a healthier nightâ€™s sleep.', 'Mattress Cleaning', 70, 6, 2),
(12, 2, 'Standard Home Cleaning', 'General cleaning for apartments and houses including dusting, vacuuming, mopping, and bathroom sanitization.', 'General Cleaning', 70, 8, 5),
(13, 2, 'Carpet Refresh', 'A complete deep-cleaning service for carpets using steam extraction and eco-safe shampoo to remove dirt, allergens, and stains. Ideal for homes with kids or pets. Includes pre-treatment for high-traffic areas and deodorizing.', 'Carpet Cleaning', 90, 21, 13),
(14, 2, 'Pet-Friendly Cleaning', 'Designed for homes with pets, this service includes hair removal from furniture and floors, deodorizing, stain treatment for accidents, and the use of animal-safe cleaning products.', 'Pet Friendly', 100, 30, 28),
(15, 3, 'Move-In / Move-Out Cleaning', 'A meticulous cleaning service for empty homes during transitions. Includes deep cleaning of every room, inside cabinets and drawers, fixtures, vents, and appliances. Designed to help renters and homeowners meet lease requirements or start fresh in a new home.', 'Deep Cleaning, Move-In/Move-Out', 200, 37, 31),
(16, 3, 'Window & Glass Cleaning', 'Interior and exterior cleaning of windows, glass doors, and mirrors using streak-free solutions. Includes screen washing and sill wiping. Optional water-fed pole for high-rise windows (up to 3 stories).', 'Window/Glass Cleaning', 60, 44, 17),
(17, 3, 'Sofa & Upholstery Cleaning', 'Professional cleaning of fabric and leather sofas, chairs, and ottomans using industry-grade tools. Removes embedded dirt, body oils, food stains, and pet odors. Includes protective coating if requested.', 'Furniture Cleaning', 75, 9, 3),
(18, 5, 'Luxury Home Detailing', 'Professional dusting and micro-cleaning of fine art, sculptures, antique woodwork, and high-value collectibles using museum-approved methods. Our trained team uses pH-neutral products and anti-static cloths to preserve integrity and value.', 'Detailing', 500, 9, 6),
(19, 5, 'Chandelier & Lighting Fixture Cleaning', 'Dismantling and hand-cleaning of delicate chandeliers, pendant lights, and designer fixtures using crystal-safe solvents and microfiber polishing. Each prism is cleaned individually for maximum brilliance.', 'Lighting', 300, 17, 15),
(20, 5, 'Private Spa & Sauna Cleaning', 'Meticulous deep cleaning and sanitization of personal saunas, steam rooms, and jacuzzi tubs using non-corrosive, skin-safe agents. Includes descaling of mineral buildup, aromatherapy diffuser cleaning, and towel restocking (optional).', 'Spa/Sauna', 250, 10, 2),
(21, 5, 'Marble & Stone Polishing Service', 'Restorative polishing of luxury stone surfaces including marble, granite, and quartz. Removes micro-scratches, water stains, and etching, leaving a glossy, mirror-like finish. Includes sealing upon request.', 'Polishing', 300, 43, 15),
(24, 13, 'Tile and Grout Cleaning', 'Professional cleaning to restore the shine of tiles and grout', 'Residential', 160, 80, 17),
(25, 11, 'Air Duct Cleaning', 'Thorough cleaning of HVAC ducts to improve air quality', 'Commercial', 300, 70, 15),
(26, 12, 'Pressure Washing', 'High-pressure washing for driveways, patios, and exterior walls', 'Residential', 180, 95, 20),
(27, 9, 'Holiday Prep Clean', 'Get your home ready for guests with a comprehensive clean. Includes dusting, floor mopping, window polishing, and bathroom sanitization.', 'Seasonal Cleaning', 120, 37, 3),
(28, 9, 'Dust-Free Home Service', 'Say goodbye to dust bunnies with our specialized service. Includes HEPA vacuuming, surface dusting, and air vent cleaning.', 'Dusting', 70, 6, 2),
(29, 9, 'Upholstery Cleaning', 'Professional cleaning for sofas, chairs, and other upholstery', 'Residential', 140, 85, 18),
(30, 10, 'Floor Polishing', 'Expert floor polishing for a glossy, scratch-free finish', 'Commercial', 260, 100, 22),
(31, 11, 'Car Interior Detailing', 'Make your car’s interior feel brand new. Includes vacuuming, fabric shampooing, leather conditioning, and odor removal.', 'Automotive Cleaning', 150, 17, 9),
(32, 11, 'Tile & Grout Restoration', 'High-pressure steam cleaning and grout sealing to restore the sparkle of your tiled floors and walls.', 'Tile Cleaning', 200, 14, 10),
(33, 1, 'Housekeeping', 'Regular housekeeping services for a clean and organized home', 'Residential', 120, 150, 40),
(34, 2, 'Office Cleaning', 'Daily or weekly cleaning services for office spaces', 'Commercial', 180, 130, 35),
(35, 13, 'High-Rise Window Cleaning', 'Professional window cleaning for high-rise buildings using rope access or water-fed poles for up to 10 stories.', 'Commercial Cleaning', 300, 17, 5),
(36, 4, 'Sanitization Services', 'Disinfection and sanitization for homes and businesses', 'Commercial', 220, 90, 20),
(37, 5, 'Deep Cleaning', 'Comprehensive deep cleaning for homes and offices', 'Residential', 200, 120, 30),
(38, 6, 'Carpet Cleaning', 'Professional carpet and rug cleaning services', 'Commercial', 150, 95, 25),
(39, 7, 'Window Cleaning', 'Crystal clear window cleaning for high-rises and homes', 'Commercial', 100, 80, 20),
(40, 8, 'Move-In/Move-Out Cleaning', 'Thorough cleaning for moving in or out of properties', 'Residential', 250, 110, 35),
(41, 31, 'Boat Cleaning Service', 'Professional cleaning service for boats and yachts. Includes interior detailing, deck cleaning, and hull maintenance.', 'Marine Cleaning', 350, 15, 8),
(42, 32, 'Holiday Setup Service', 'Complete holiday decoration and setup service. Includes light installation, decoration placement, and safety checks.', 'Holiday Services', 200, 25, 12),
(43, 27, 'Ceiling Fan & Light Fixture Cleaning', 'Remove dust, cobwebs, and grime from hard-to-reach ceiling fans and light fixtures.', 'Specialty Cleaning', 70, 42, 14),
(44, 28, 'Gutter & Roof Cleaning', 'Clear your gutters and roofs of debris, leaves, and dirt to prevent water damage and blockages.', 'Exterior Cleaning', 300, 12, 6),
(45, 29, 'Air Duct & Vent Cleaning', 'Improve indoor air quality by removing dust, pollen, and mold from HVAC ducts and vents.', 'Air Duct Cleaning', 250, 30, 4),
(46, 30, 'Tile & Grout Restoration', 'Bring back the shine to your tiles with high-pressure cleaning and professional grout restoration.', 'Tile Cleaning', 200, 16, 11),
(47, 31, 'Allergy Relief Clean', 'Remove allergens, dust mites, and pollen from your home with hypoallergenic products and HEPA filtration.', 'Allergy Cleaning', 180, 38, 19),
(48, 32, 'Tech & Electronics Cleaning', 'Safe, anti-static cleaning of electronics, including TVs, monitors, gaming consoles, and speakers.', 'Specialty Cleaning', 90, 41, 11),
(53, 37, 'Pressure Washing Service', 'Blast away dirt, grime, and algae from driveways, sidewalks, and building exteriors.', 'Exterior Cleaning', 200, 40, 34),
(54, 38, 'Medical Office Cleaning', 'Comprehensive cleaning for medical and dental offices, including disinfecting high-touch surfaces and sanitizing patient rooms.', 'Commercial Cleaning', 400, 28, 15),
(55, 39, 'Restaurant Hood & Kitchen Cleaning', 'Deep cleaning for commercial kitchens, including grease removal, exhaust hood cleaning, and tile scrubbing.', 'Commercial Cleaning', 500, 18, 2),
(57, 2, 'Premium Carpet Deep Clean', 'Professional carpet cleaning using eco-friendly solutions. Includes stain removal, deodorizing, and protective treatment.', 'Carpet Cleaning', 120, 6, 4),
(58, 2, 'Kitchen Deep Sanitization', 'Complete kitchen cleaning including appliances, cabinets, and countertops. Uses food-safe cleaning products.', 'Kitchen Cleaning', 150, 24, 2),
(59, 3, 'Green Home Cleaning', 'Eco-friendly cleaning service using natural products. Perfect for families with children and pets.', 'Eco-Friendly', 85, 49, 43),
(60, 3, 'Bathroom Refresh', 'Deep cleaning of bathrooms including tile, grout, fixtures, and shower doors.', 'Bathroom Cleaning', 95, 24, 4),
(61, 5, 'Office Space Cleaning', 'Professional cleaning for office spaces including workstations, common areas, and restrooms.', 'Commercial Cleaning', 200, 22, 6),
(62, 5, 'Event Venue Cleanup', 'Post-event cleaning service for party venues, halls, and event spaces.', 'Event Cleanup', 250, 37, 34),
(63, 9, 'Luxury Home Maintenance', 'Regular maintenance cleaning for high-end homes. Includes attention to detail and premium products.', 'Luxury Cleaning', 300, 21, 17),
(64, 9, 'Art & Antique Care', 'Specialized cleaning for valuable art pieces and antiques using museum-grade products.', 'Specialty Cleaning', 400, 39, 16),
(65, 11, 'Garden Furniture Cleaning', 'Restoration and cleaning of outdoor furniture, including cushions and metal frames.', 'Outdoor Cleaning', 80, 31, 18),
(66, 11, 'Pet-Friendly Home Clean', 'Specialized cleaning for homes with pets, including odor removal and pet hair treatment.', 'Pet Friendly', 110, 39, 28),
(67, 13, 'Medical Office Sanitization', 'Professional cleaning for medical facilities using hospital-grade disinfectants.', 'Medical Cleaning', 350, 2, 2),
(68, 13, 'Dental Office Cleaning', 'Specialized cleaning for dental practices, including equipment and waiting areas.', 'Medical Cleaning', 300, 37, 8),
(69, 15, 'Restaurant Kitchen Deep Clean', 'Complete cleaning of commercial kitchens including equipment and exhaust systems.', 'Commercial Cleaning', 450, 33, 15),
(70, 15, 'Food Service Area Cleaning', 'Sanitization of food preparation and serving areas in restaurants.', 'Commercial Cleaning', 200, 3, 2),
(71, 17, 'Gym & Fitness Center Clean', 'Comprehensive cleaning of fitness facilities including equipment and locker rooms.', 'Commercial Cleaning', 280, 12, 2),
(72, 17, 'Sports Facility Maintenance', 'Regular cleaning and maintenance for sports complexes and training facilities.', 'Commercial Cleaning', 350, 49, 3),
(73, 19, 'Retail Store Cleaning', 'Professional cleaning for retail spaces including display areas and fitting rooms.', 'Commercial Cleaning', 180, 12, 2),
(74, 19, 'Shopping Mall Maintenance', 'Regular cleaning service for shopping mall common areas and restrooms.', 'Commercial Cleaning', 400, 11, 2),
(75, 27, 'Industrial Space Cleaning', 'Specialized cleaning for warehouses and industrial facilities.', 'Industrial Cleaning', 500, 15, 12),
(76, 27, 'Factory Floor Maintenance', 'Regular cleaning and maintenance of factory floors and work areas.', 'Industrial Cleaning', 450, 40, 36);

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
) ENGINE=MyISAM AUTO_INCREMENT=61 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
(7, 17, 7, 3, '2025-05-01 14:15:23', 'completed', 4, 'My sofa is spotless, but the drying time was a bit long.'),
(8, 12, 8, 2, '2025-05-02 11:05:12', 'completed', 5, 'Very thorough and efficient home cleaning!'),
(9, 15, 8, 3, '2025-05-02 12:35:45', 'rejected', 0, ''),
(10, 18, 9, 5, '2025-05-03 09:25:32', 'accepted', 5, 'My luxury furniture has never looked better. Worth every cent!'),
(11, 14, 10, 2, '2025-05-04 08:10:15', 'pending', 0, ''),
(12, 13, 10, 2, '2025-05-04 13:55:32', 'accepted', 5, 'Carpet looks and smells amazing!'),
(13, 16, 11, 3, '2025-05-05 10:20:44', 'completed', 4, 'Windows are spotless, but missed a few corners.'),
(14, 11, 12, 3, '2025-05-05 15:10:21', 'completed', 5, 'Mattress feels fresh and clean. Will definitely book again.'),
(15, 24, 13, 7, '2025-05-06 14:45:12', 'pending', 0, ''),
(16, 25, 14, 8, '2025-05-07 16:05:35', 'rejected', 0, ''),
(17, 26, 14, 8, '2025-05-07 16:20:15', 'completed', 5, 'Post-event cleanup was quick and thorough.'),
(18, 27, 15, 9, '2025-05-08 17:30:50', 'accepted', 5, 'No dust in sight! My allergies are much better now.'),
(19, 28, 15, 9, '2025-05-08 18:45:23', 'accepted', 4, 'Good job, but a bit pricey for dusting.'),
(20, 29, 16, 10, '2025-05-09 09:00:15', 'pending', 0, ''),
(21, 30, 16, 10, '2025-05-09 09:30:42', 'completed', 5, 'Roof and gutters are spotless! Great attention to detail.'),
(22, 31, 17, 11, '2025-05-10 12:15:55', 'completed', 4, 'Car interior looks and smells great, but missed a few spots.'),
(23, 32, 18, 11, '2025-05-10 14:00:05', 'accepted', 5, 'Tiles and grout look brand new!'),
(24, 33, 19, 12, '2025-05-11 10:35:12', 'pending', 0, ''),
(25, 34, 20, 12, '2025-05-11 11:15:50', 'accepted', 5, 'Airbnb guest was impressed with the spotless cleanliness.'),
(26, 35, 19, 13, '2025-05-11 15:10:25', 'completed', 5, 'High-rise windows are sparkling clean!'),
(27, 36, 20, 14, '2025-05-11 16:20:40', 'accepted', 5, 'Thorough disinfection. I feel much safer now.'),
(28, 37, 21, 27, '2025-05-12 10:45:00', 'accepted', 5, 'My patio looks brand new!'),
(29, 38, 22, 28, '2025-05-12 11:15:00', 'accepted', 4, 'Great job, but a bit pricey.'),
(30, 39, 23, 29, '2025-05-12 12:30:00', 'rejected', 0, ''),
(31, 40, 24, 30, '2025-05-12 13:00:00', 'pending', 0, ''),
(32, 41, 25, 31, '2025-05-13 14:00:00', 'accepted', 5, 'Perfect for my boat! Will book again.'),
(33, 42, 26, 32, '2025-05-13 15:00:00', 'accepted', 4, 'Great holiday setup, but some lights were tangled.'),
(34, 43, 27, 33, '2025-05-13 16:30:00', 'accepted', 5, 'Spotless and efficient!'),
(35, 44, 28, 34, '2025-05-13 17:00:00', 'rejected', 0, ''),
(36, 45, 29, 35, '2024-05-14 09:00:00', 'accepted', 5, 'Air feels fresher already.'),
(37, 46, 30, 36, '2024-05-14 10:00:00', 'accepted', 5, 'My tiles have never looked this good!'),
(38, 47, 31, 37, '2024-05-14 11:30:00', 'pending', 0, ''),
(39, 48, 32, 38, '2024-05-14 12:30:00', 'accepted', 5, 'No more dusty screens!'),
(40, 49, 33, 39, '2024-05-14 13:00:00', 'accepted', 5, 'My couch looks and smells great.'),
(41, 50, 34, 40, '2024-05-14 14:00:00', 'rejected', 0, ''),
(42, 51, 35, 41, '2024-05-15 15:00:00', 'accepted', 4, 'Clean basement, but a bit dusty after a few days.'),
(43, 52, 36, 42, '2024-05-15 16:00:00', 'accepted', 5, 'Perfect for my BBQ parties!'),
(44, 53, 37, 43, '2024-05-15 17:00:00', 'pending', 0, ''),
(45, 54, 38, 44, '2024-05-15 18:00:00', 'accepted', 5, 'Fast and professional!'),
(46, 55, 39, 45, '2024-05-16 09:00:00', 'accepted', 5, 'Excellent service, highly recommend.'),
(47, 56, 40, 46, '2024-05-16 10:00:00', 'rejected', 0, ''),
(48, 57, 41, 47, '2024-05-17 11:00:00', 'accepted', 5, 'Excellent deep cleaning service!'),
(49, 58, 42, 48, '2024-05-17 12:00:00', 'pending', 0, ''),
(50, 59, 43, 49, '2024-05-17 13:00:00', 'accepted', 4, 'Good service but a bit pricey'),
(51, 60, 44, 50, '2024-05-18 14:00:00', 'completed', 5, 'Perfect for my spring cleaning needs'),
(52, 61, 45, 51, '2024-05-18 15:00:00', 'accepted', 5, 'Very thorough and professional'),
(53, 62, 46, 52, '2024-05-19 16:00:00', 'rejected', 0, ''),
(54, 63, 47, 53, '2024-05-19 17:00:00', 'accepted', 5, 'Great attention to detail'),
(55, 64, 48, 54, '2024-05-20 18:00:00', 'pending', 0, ''),
(56, 65, 49, 55, '2024-05-20 19:00:00', 'accepted', 4, 'Good service but took longer than expected'),
(57, 66, 50, 56, '2024-05-21 20:00:00', 'completed', 5, 'Excellent work on my windows'),
(58, 67, 51, 57, '2024-05-21 21:00:00', 'accepted', 5, 'Very satisfied with the service'),
(59, 68, 52, 58, '2024-05-22 22:00:00', 'rejected', 0, ''),
(60, 69, 53, 59, '2024-05-22 23:00:00', 'accepted', 5, 'Perfect for my Airbnb property');

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
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, 9, 'Mia', 'Wong', 'Eco-friendly cleaning specialist', 'Female', NULL, 1),
(11, 10, 'Lucas', 'Ong', 'Condo and penthouse specialist', 'Male', NULL, 1),
(12, 11, 'Sophia', 'Lee', 'Fast and efficient cleaner', 'Female', NULL, 1),
(13, 12, 'Daniel', 'Chia', 'Cleaning with a smile!', 'Male', NULL, 1),
(14, 13, 'Emily', 'Tan', 'I love turning chaos into order', 'Female', NULL, 1),
(15, 14, 'David', 'Ng', 'High-rise cleaning pro', 'Male', NULL, 1),
(16, 15, 'Sarah', 'Lau', 'Precision cleaning for a spotless home', 'Female', NULL, 1),
(17, 16, 'Mike', 'Tan', 'Owner of a cozy 3-room flat', 'Male', NULL, 1),
(18, 17, 'Rachel', 'Goh', 'Enjoys a tidy, minimalist lifestyle', 'Female', NULL, 1),
(19, 18, 'Ethan', 'Wong', '5-room BTO owner', 'Male', NULL, 1),
(20, 19, 'Alicia', 'Lim', 'Landed property owner', 'Female', NULL, 1),
(21, 20, 'Henry', 'Lim', 'HDB expert', 'Male', NULL, 1),
(22, 21, 'Noah', 'Sim', 'Tech-savvy and always on the lookout for smart cleaning solutions.', 'Male', NULL, 1),
(23, 22, 'Olivia', 'Ng', 'I love a spotless home and fresh air.', 'Female', NULL, 1),
(24, 23, 'Liam', 'Chong', 'Balcony garden and plant enthusiast.', 'Male', NULL, 1),
(25, 24, 'Emma', 'Wong', 'Minimalist living advocate, less is more.', 'Female', NULL, 1),
(26, 25, 'James', 'Lim', 'Perfectionist when it comes to clean floors.', 'Male', NULL, 1),
(27, 26, 'Sophia', 'Tan', 'Keeping homes and hearts warm.', 'Female', NULL, 1),
(28, 27, 'Benjamin', 'Lee', 'Dog dad and BBQ lover.', 'Male', NULL, 1),
(29, 28, 'Charlotte', 'Chia', 'Mother of two, passionate about clean spaces.', 'Female', NULL, 1),
(30, 29, 'Elijah', 'Teo', 'Busy professional who loves coming home to a clean house.', 'Male', NULL, 1),
(31, 30, 'Amelia', 'Wong', 'Plant lover and proud cat mom.', 'Female', NULL, 1),
(32, 31, 'William', 'Koh', 'Ex-marine, disciplined and detail-oriented.', 'Male', NULL, 1),
(33, 32, 'Mia', 'Goh', 'I enjoy transforming messy spaces into peaceful havens.', 'Female', NULL, 1),
(34, 33, 'Henry', 'Choo', 'Semi-retired and loves a clean workshop.', 'Male', NULL, 1),
(35, 34, 'Ella', 'Tan', 'Fitness enthusiast who believes in a clutter-free life.', 'Female', NULL, 1),
(36, 35, 'Mason', 'Ong', 'Enjoys spending weekends on DIY projects.', 'Male', NULL, 1),
(37, 36, 'Isabella', 'Sim', 'Organizing fanatic and closet organizer.', 'Female', NULL, 1),
(38, 37, 'Lucas', 'Yap', 'Part-time musician, full-time neat freak.', 'Male', NULL, 1),
(39, 38, 'Ava', 'Ng', 'Travel lover who keeps her home spotless.', 'Female', NULL, 1),
(40, 39, 'Alexander', 'Low', 'Clean freak with a keen eye for detail.', 'Male', NULL, 1),
(41, 40, 'Zoe', 'Lim', 'Loves a bright, organized living space.', 'Female', NULL, 1);

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
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shortlist`
--

INSERT INTO `shortlist` (`shortlist_id`, `user_id`, `service_id`, `shortlist_date`) VALUES
(18, 6, 20, '2025-04-26'),
(24, 6, 21, '2025-04-26'),
(25, 6, 2, '2025-04-28'),
(26, 7, 15, '2025-04-29'),
(27, 8, 18, '2025-04-29'),
(28, 9, 13, '2025-04-30'),
(29, 10, 22, '2025-04-30'),
(30, 11, 17, '2025-05-01'),
(31, 12, 25, '2025-05-01'),
(32, 13, 23, '2025-05-02'),
(33, 14, 24, '2025-05-02'),
(34, 15, 29, '2025-05-03'),
(35, 16, 26, '2025-05-03'),
(36, 17, 30, '2025-05-04'),
(37, 18, 28, '2025-05-04'),
(38, 19, 31, '2025-05-05'),
(39, 20, 27, '2025-05-05'),
(40, 9, 33, '2025-05-06'),
(41, 10, 34, '2025-05-06'),
(42, 11, 35, '2025-05-07'),
(43, 12, 36, '2025-05-07'),
(44, 13, 32, '2025-05-08'),
(45, 14, 19, '2025-05-08'),
(46, 15, 16, '2025-05-09'),
(47, 16, 14, '2025-05-09'),
(48, 17, 11, '2025-05-10'),
(49, 18, 12, '2025-05-10'),
(50, 19, 10, '2025-05-11'),
(51, 20, 9, '2025-05-11'),
(52, 7, 37, '2025-05-12'),
(53, 8, 38, '2025-05-12'),
(54, 9, 39, '2025-05-12'),
(55, 10, 40, '2025-05-12'),
(56, 11, 41, '2025-05-13'),
(57, 12, 42, '2025-05-13'),
(58, 13, 43, '2025-05-13'),
(59, 14, 44, '2025-05-13'),
(60, 15, 45, '2025-05-14'),
(61, 16, 46, '2025-05-14'),
(62, 17, 47, '2025-05-14'),
(63, 18, 48, '2025-05-14'),
(64, 19, 49, '2025-05-15'),
(65, 20, 50, '2025-05-15'),
(66, 21, 51, '2025-05-15'),
(67, 22, 52, '2025-05-15'),
(68, 23, 53, '2025-05-16'),
(69, 24, 54, '2025-05-16'),
(70, 25, 55, '2025-05-16'),
(71, 26, 56, '2025-05-16'),
(72, 27, 37, '2025-05-17'),
(73, 28, 38, '2025-05-17'),
(74, 29, 39, '2025-05-17'),
(75, 30, 40, '2025-05-17'),
(76, 31, 41, '2025-05-18'),
(77, 32, 42, '2025-05-18'),
(78, 33, 43, '2025-05-18'),
(79, 34, 44, '2025-05-18'),
(80, 35, 45, '2025-05-19'),
(81, 36, 46, '2025-05-19'),
(82, 37, 47, '2025-05-19'),
(83, 38, 48, '2025-05-19'),
(84, 39, 49, '2025-05-20'),
(85, 40, 50, '2025-05-20'),
(86, 41, 57, '2025-05-21'),
(87, 42, 58, '2025-05-21'),
(88, 43, 59, '2025-05-21'),
(89, 44, 60, '2025-05-21'),
(90, 45, 61, '2025-05-22'),
(91, 46, 62, '2025-05-22'),
(92, 47, 63, '2025-05-22'),
(93, 48, 64, '2025-05-22'),
(94, 49, 65, '2025-05-23'),
(95, 50, 66, '2025-05-23'),
(96, 51, 67, '2025-05-23'),
(97, 52, 68, '2025-05-23'),
(98, 53, 69, '2025-05-24'),
(99, 54, 70, '2025-05-24'),
(100, 55, 71, '2025-05-24'),
(101, 56, 72, '2025-05-24');

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
(9, 'cleaner4', 'testing', 2, 'cleaner4@mail.com', '+6591234000', 1),
(10, 'homeowner4', 'testing', 3, 'homeowner4@mail.com', '+6597777000', 1),
(11, 'cleaner5', 'testing', 2, 'cleaner5@mail.com', '+6595555000', 1),
(12, 'homeowner5', 'testing', 3, 'homeowner5@mail.com', '+6594444000', 1),
(40, 'homeowner16', 'testing', 3, 'homeowner16@mail.com', '+6599990029', 1),
(39, 'cleaner18', 'testing', 2, 'cleaner18@mail.com', '+6599990028', 1),
(38, 'cleaner17', 'testing', 2, 'cleaner17@mail.com', '+6599990027', 1),
(37, 'cleaner16', 'testing', 2, 'cleaner16@mail.com', '+6599990026', 1),
(36, 'pm5', 'testing', 4, 'pm5@mail.com', '+6599990025', 1),
(35, 'pm4', 'testing', 4, 'pm4@mail.com', '+6599990024', 1),
(34, 'pm3', 'testing', 4, 'pm3@mail.com', '+6599990023', 1),
(33, 'pm2', 'testing', 4, 'pm2@mail.com', '+6599990022', 1),
(32, 'cleaner15', 'testing', 2, 'cleaner15@mail.com', '+6599990021', 1),
(31, 'cleaner14', 'testing', 2, 'cleaner14@mail.com', '+6599990020', 1),
(30, 'cleaner13', 'testing', 2, 'cleaner13@mail.com', '+6599990019', 1),
(29, 'cleaner12', 'testing', 2, 'cleaner12@mail.com', '+6599990018', 1),
(28, 'cleaner11', 'testing', 2, 'cleaner11@mail.com', '+6599990017', 1),
(27, 'cleaner10', 'testing', 2, 'cleaner10@mail.com', '+6599990016', 1),
(26, 'homeowner15', 'testing', 3, 'homeowner15@mail.com', '+6599990015', 1),
(25, 'homeowner14', 'testing', 3, 'homeowner14@mail.com', '+6599990014', 1),
(24, 'homeowner13', 'testing', 3, 'homeowner13@mail.com', '+6599990013', 1),
(23, 'homeowner12', 'testing', 3, 'homeowner12@mail.com', '+6599990012', 1),
(22, 'homeowner11', 'testing', 3, 'homeowner11@mail.com', '+6599990011', 1),
(21, 'homeowner10', 'testing', 3, 'homeowner10@mail.com', '+6599990010', 1),
(20, 'homeowner9', 'testing', 3, 'homeowner9@mail.com', '+6591111222', 1),
(19, 'cleaner9', 'testing', 2, 'cleaner9@mail.com', '+6599999000', 1),
(18, 'homeowner8', 'testing', 3, 'homeowner8@mail.com', '+6590000000', 1),
(17, 'cleaner8', 'testing', 2, 'cleaner8@mail.com', '+6591111000', 1),
(16, 'homeowner7', 'testing', 3, 'homeowner7@mail.com', '+6592222000', 1),
(15, 'cleaner7', 'testing', 2, 'cleaner7@mail.com', '+6593333000', 1),
(14, 'homeowner6', 'testing', 3, 'homeowner6@mail.com', '+6596666000', 1),
(13, 'cleaner6', 'testing', 2, 'cleaner6@mail.com', '+6598888000', 1),
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
