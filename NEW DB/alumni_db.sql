-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 09:04 PM
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
-- Database: `alumni_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `description`, `timestamp`) VALUES
(1, 23, 'ARCHIVE', 'Archived alumni with ID: 10', '2025-08-21 04:37:11'),
(2, 23, 'ARCHIVE', 'Archived alumni with ID: 19', '2025-08-21 04:37:30'),
(3, 23, 'ARCHIVE', 'Archived alumni with ID: 33', '2025-08-21 04:37:38'),
(4, 23, 'RESTORE', 'Restored alumni with ID: 9 from archives', '2025-08-21 04:45:49'),
(5, 23, 'RESTORE', 'Restored alumni with ID: 9 from archives', '2025-08-21 04:45:51'),
(6, 23, 'RESTORE', 'Restored alumni with ID: 8 from archives', '2025-08-21 06:27:01'),
(7, 23, 'ARCHIVE', 'Archived alumni with ID: 9', '2025-08-22 08:53:40'),
(8, 23, 'ARCHIVE', 'Archived alumni with ID: 23', '2025-08-22 08:54:46'),
(9, 23, 'RESTORE', 'Restored alumni with ID: 10 from archives', '2025-08-22 08:54:52'),
(10, 23, 'RESTORE', 'Restored alumni with ID: 7 from archives', '2025-08-22 08:54:53'),
(11, 23, 'RESTORE', 'Restored alumni with ID: 12 from archives', '2025-08-22 08:54:55'),
(12, 23, 'ARCHIVE', 'Archived alumni with ID: 43', '2025-08-24 16:38:10'),
(13, 23, 'RESTORE', 'Restored alumni with ID: 2 from archives', '2025-08-24 16:42:57'),
(14, 23, 'ARCHIVE', 'Archived alumni with ID: 43', '2025-08-24 16:43:07'),
(15, 23, 'ARCHIVE', 'Archived alumni with ID: 45', '2025-08-24 16:53:01'),
(16, 23, 'ARCHIVE', 'Archived alumni with ID: 48', '2025-08-24 17:06:23'),
(17, 23, 'ARCHIVE', 'Archived alumni with ID: 49', '2025-08-24 17:13:39'),
(18, 23, 'ARCHIVE', 'Archived alumni with ID: 44', '2025-08-24 17:13:54'),
(19, 23, 'ARCHIVE', 'Archived alumni with ID: 47', '2025-08-24 17:29:36'),
(20, 23, 'RESTORE', 'Restored alumni with ID: 18 from archives', '2025-08-24 17:34:26'),
(21, 23, 'RESTORE', 'Restored alumni with ID: 17 from archives', '2025-08-24 17:34:46'),
(22, 23, 'RESTORE', 'Restored alumni with ID: 13 from archives', '2025-08-24 17:34:46'),
(23, 23, 'RESTORE', 'Restored alumni with ID: 3 from archives', '2025-08-24 17:34:46'),
(24, 23, 'RESTORE', 'Restored alumni with ID: 16 from archives', '2025-08-24 17:34:46'),
(25, 23, 'RESTORE', 'Restored alumni with ID: 7 from archives', '2025-08-24 17:34:46'),
(26, 23, 'ARCHIVE', 'Archived alumni with ID: 51', '2025-08-24 17:45:08'),
(27, 23, 'RESTORE', 'Restored alumni with ID: 19 from archives', '2025-08-24 17:45:17'),
(28, 23, 'ARCHIVE', 'Archived alumni with ID: 50', '2025-08-24 19:29:42'),
(29, 23, 'RESTORE', 'Restored alumni with ID: 20 from archives', '2025-08-24 19:29:49'),
(30, 23, 'ARCHIVE', 'Archived alumni with ID: 50', '2025-08-24 19:30:11'),
(31, 23, 'RESTORE', 'Restored alumni with ID: 21 from archives', '2025-08-24 19:30:18'),
(32, 23, 'ARCHIVE', 'Archived alumni with ID: 50', '2025-08-26 14:49:41');

-- --------------------------------------------------------

--
-- Table structure for table `alumni_ids`
--

CREATE TABLE `alumni_ids` (
  `id` int(11) NOT NULL,
  `alumni_id` varchar(50) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `suffixname` varchar(20) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `batch` year(4) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `program_type` enum('College','Senior High') DEFAULT 'College',
  `strand_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `alumni_ids`
--

INSERT INTO `alumni_ids` (`id`, `alumni_id`, `lastname`, `firstname`, `middlename`, `suffixname`, `birthdate`, `gender`, `batch`, `course_id`, `major_id`, `date_created`, `program_type`, `strand_id`) VALUES
(1, 'CC-2501', 'CABALLERO', 'MICHEL', 'JAMOROI', NULL, '2001-05-19', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(2, 'CC-2502', 'CABANDUCOS', 'JAMAICO', 'BARSOBIA', NULL, '2002-12-13', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(3, 'CC-2503', 'PADUA', 'CELESTE', 'SACEDON', NULL, '2000-03-01', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(4, 'CC-2504', 'DESUSA', 'ROYCE', 'TAN', NULL, '1997-12-11', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(5, 'CC-2505', 'BENDICIO', 'CARLO', 'LABUANON', NULL, '2002-04-13', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(6, 'CC-2506', 'MACAHILOS', 'KRISTIAN KING', 'MACARAYO', NULL, '2002-12-15', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(7, 'CC-2507', 'MICABALO', 'JANRO', 'JOVEN', NULL, '2002-08-16', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(8, 'CC-2508', 'PREL', 'NINO LOVE', 'B.', NULL, '2001-01-05', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(9, 'CC-2509', 'LADRA', 'JOHN LEE', 'M.', NULL, '2000-01-03', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(10, 'CC-2510', 'VERGARA', 'MICHAEL VINCENT', 'G.', NULL, '2002-04-04', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(11, 'CC-2511', 'FERNANDEZ', 'JOHN DAVE', 'L.', NULL, '2002-06-22', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(12, 'CC-2512', 'RAYON', 'JAPHETH JIREH', NULL, NULL, '2002-10-18', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(13, 'CC-2513', 'ADAJAR', 'JUVELLE', 'C.', NULL, '2002-12-13', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(14, 'CC-2514', 'CANQUE', 'CHASTER', 'L.', NULL, '2001-01-01', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(15, 'CC-2515', 'BORRES', 'JOHN CONRAD', 'A.', NULL, '2000-08-19', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(16, 'CC-2516', 'PAJARON', 'CLAVEL', 'I.', NULL, '1999-10-14', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(17, 'CC-2517', 'TUBIANO', 'JAY ANNE', 'C.', NULL, '2003-06-07', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(18, 'CC-2518', 'VARIACION', 'MERLYN', 'T.', NULL, '2002-06-19', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(19, 'CC-2519', 'PAJARON', 'DAIMIE ROSE', 'D.', NULL, '2000-05-19', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(20, 'CC-2520', 'VALMORES', 'JOY', 'C.', NULL, '2001-06-15', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(21, 'CC-2521', 'ALABA', 'ALTHEA', 'M.', NULL, '2002-06-25', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(22, 'CC-2522', 'CAGALAWAN', 'RHENIL JANE', 'D.', NULL, '2002-08-21', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(23, 'CC-2523', 'GAYLAWAN', 'JOYSAN', 'U.', NULL, '2002-08-31', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(24, 'CC-2524', 'DALANIEL', 'JENNIE', 'D.', NULL, '2002-01-07', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(25, 'CC-2525', 'PAGHANAPAN', 'JERORIS JAY', 'R.', NULL, '2003-03-18', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(26, 'CC-2526', 'WARQUE', 'MELCHOR', 'C.', 'JR.', '2002-11-25', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(27, 'CC-2527', 'DEGAMO', 'KEVIN JOHN MICHAEL', NULL, NULL, '2003-02-27', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(28, 'CC-2528', 'APA', 'LEONARDO', 'S.', NULL, '2002-05-03', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(29, 'CC-2529', 'DAGHAY', 'AXEL KING', 'P.', NULL, '1998-10-17', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(30, 'CC-2530', 'SUDLAYAN', 'RAYNAND', 'G.', NULL, '2000-10-03', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(31, 'CC-2531', 'PADAYAO', 'TIMOTEO', 'M.', 'JR.', '2000-02-14', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(32, 'CC-2532', 'VIRTUDAZO', 'JIMUEL', 'GULA', NULL, '2003-02-25', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(33, 'CC-2533', 'CABACTULAN', 'DONNA MAE', 'C.', NULL, '2003-02-20', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(34, 'CC-2534', 'SALISE', 'RICA', 'S.', NULL, '2002-08-05', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(35, 'CC-2535', 'BADILLA', 'ANNERSON', 'C.', NULL, '2002-08-30', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(36, 'CC-2536', 'CADRON', 'PHILIP', 'P.', NULL, '2002-10-21', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL),
(37, 'CC-2579', 'GALDO', 'ROVIE', 'OPENA', NULL, '2002-10-28', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL),
(38, 'CC-2580', 'UCAB', 'GERALD', 'RAGAS', NULL, '2000-05-19', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL),
(39, 'CC-2581', 'MACAS', 'MARY ANNE MAE', 'YAMIT', NULL, '1994-04-05', 'Female', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL),
(40, 'CC-2582', 'ESPANOL', 'JOMARIE', 'BETARMOS', NULL, '2002-09-29', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL),
(41, 'CC-2583', 'ABOG', 'FRANCO', 'ALFAR', NULL, '2000-11-25', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL),
(42, 'CC-2584', 'MADRONERO', 'GIANFRANCO', 'SALIW', NULL, '1988-10-11', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL),
(83, 'CC-2585', 'SUCANDAN', 'NOVY JANE', 'BALDOMAN', NULL, '2002-09-14', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL),
(84, 'CC-2586', 'CACULBA', 'ALBERT', 'HALUAG', NULL, '1999-04-26', 'Male', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL),
(85, 'CC-2587', 'RAYON', 'RHEJOY', 'MAMARAN', NULL, '2003-04-13', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL),
(86, 'CC-2588', 'ACERA', 'ISA BONNI EVE', 'ALONZO', NULL, '2002-11-30', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL),
(87, 'CC-2589', 'BALUSAN', 'METCHIE', 'M.', NULL, '2001-12-28', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL),
(88, 'CC-2590', 'MARAVELES', 'ROWAR', 'ASUR', NULL, '2003-02-07', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `alumnus_bio`
--

CREATE TABLE `alumnus_bio` (
  `id` int(11) NOT NULL,
  `alumni_id` varchar(50) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `suffixname` varchar(20) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `address` text NOT NULL,
  `employment_status` varchar(64) NOT NULL DEFAULT '',
  `employment_history` longtext DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `batch` varchar(10) NOT NULL,
  `course_id` int(11) NOT NULL,
  `major_id` int(11) DEFAULT NULL,
  `strand_id` int(11) DEFAULT NULL,
  `connected_to` varchar(150) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `company_email` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `academic_honor` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `alumnus_bio`
--

INSERT INTO `alumnus_bio` (`id`, `alumni_id`, `lastname`, `firstname`, `middlename`, `suffixname`, `birthdate`, `address`, `employment_status`, `employment_history`, `gender`, `batch`, `course_id`, `major_id`, `strand_id`, `connected_to`, `contact_no`, `company_address`, `company_email`, `email`, `password`, `img`, `date_created`, `avatar`, `status`, `last_login`, `academic_honor`) VALUES
(51, '0', 'Dela Cruz', 'Maria', 'Santos', '', '2025-08-24', 'Sugbongcogon', '', NULL, 'Female', '2070', 0, NULL, 1, '', '', '', '', 'admin@gmail.com', '', NULL, '2025-08-24 09:45:17', 'default_avatar.jpg', 1, NULL, NULL),
(52, 'CC-2030', 'Taylor', 'Sophia', 'Grace', NULL, '1996-03-01', 'Cagayan de oro Mis.or', 'employed', '[{\"employment_status\":\"employed\",\"connected_to\":\"Waterfallz\",\"date_started\":\"2024-05-26\",\"company_address\":\"Waterfall\",\"company_email\":\"Waterfall@gmail.com\",\"contact_no\":\"0985612243\",\"duration\":\"1 year 3 months\"}]', 'Female', '2017', 6, NULL, NULL, '', '', '', '', 'cad2@gmail.com', '$2y$10$wnPCs4ZjJpRqR1oYJ.BCNuYnUgkpi3Z2aryJfGEW65BDdnrglgoLa', '1756174448_68ad1870208af.png', '2025-08-25 20:14:08', NULL, 1, NULL, NULL),
(53, 'CC-2034', 'Jackson', 'Isabella', 'Rose', NULL, '1998-08-07', 'Cagayan de oro Mis.or', 'employed', '[{\"date_started\":\"2024-05-26\",\"duration\":\"1 year 3 months\"}]', 'Female', '2018', 1, NULL, NULL, 'Waterfallz', '0985612243', 'Waterfall', 'Waterfall@gmail.com', 'CHALRIE@GMAIL.COM', '$2y$10$Keq3bFHfP0Zjc34G36/je.Vn.VOnn1AdG0kHfuGgxahUvZ7AlbTdi', '1756175141_68ad1b256d0da.png', '2025-08-25 20:25:41', NULL, 1, NULL, NULL),
(54, 'CC-2049', 'Young', 'Nicholas', 'John', NULL, '1995-07-28', 'Waterfall', 'employed', '[{\"date_started\":\"2023-05-26\",\"duration\":\"2 years 3 months\"}]', 'Male', '2016', 3, NULL, NULL, 'Waterfallz', '0985612243', 'Waterfall', 'Waterfall@gmail.com', 'gsADA2@gmail.com', '$2y$10$uEMol3OE65zZ3jbwKvQLdu0l50toa0nqCHURXZTKqHDwu.25Dew2G', '1756176639_68ad20ffdbb9b.png', '2025-08-25 20:50:39', NULL, 1, NULL, NULL),
(56, 'CC-2504', 'DESUSA', 'ROYCE', 'TAN', NULL, '1997-12-11', 'Waterfall', 'employed', '[{\"date_started\":\"2025-08-05\",\"duration\":\"Less than a month\"}]', 'Male', '2025', 6, NULL, NULL, 'Waterfall', '0985612243', 'Waterfall', 'Waterfall@gmail.com', 'marialanievalmoria009@gmail.com', '$2y$10$Sb.WKquneKJLRPwGIbRXhO4HLSv0mgfNWxkYa2fo10vqANDjApae6', '1756400005_68b089857d2b4.png', '2025-08-28 10:53:25', NULL, 1, NULL, NULL),
(57, 'CC-2529', 'DAGHAY', 'AXEL KING', 'P.', NULL, '1998-10-17', 'Cagayan de oro Mis.or', 'employed', '[{\"date_started\":\"2023-01-20\",\"duration\":\"2 years 7 months\"}]', 'Male', '2025', 6, NULL, NULL, 'Waterfall', '09164907870', 'Waterfall', 'Waterfall@gmail.com', 'charlieeee@gmail.com', '$2y$10$TW5Aae7ir6/3s347pH8qXebNMMPu/6n6IQJ0hKjQMBJ7xMoLicdda', '1756400828_68b08cbcbb5cf.png', '2025-08-28 11:07:08', NULL, 1, NULL, NULL),
(58, 'CC-2530', 'SUDLAYAN', 'RAYNAND', 'G.', NULL, '2000-10-03', 'Balingasag Misamis Oriental', 'employed', '[{\"date_started\":\"2025-05-26\",\"duration\":\"3 months\"}]', 'Male', '2025', 6, NULL, NULL, 'Waterfall', '09164907870', 'Waterfall', 'Waterfall@gmail.com', 'abejo@gmail.com', '$2y$10$gWgmpEPki5ydBUfDASMXqet.kbvUIVPCXBdCTzQCtR25YIq5WcUra', '1756400887_68b08cf75e651.png', '2025-08-28 11:08:07', NULL, 1, NULL, NULL),
(59, 'CC-2520', 'VALMORES', 'JOY', 'C.', NULL, '2001-06-15', 'Waterfall', 'not employed', '[{\"date_started\":\"2024-10-01\",\"duration\":\"10 months\"}]', 'Female', '2025', 6, NULL, NULL, 'Waterfall', '0985612222', 'Waterfall', 'Waterfall@gmail.com', 'jiejiee@gmail.com', '$2y$10$lzrGpbBQBfr10rk6DXdkYOkgd3QFrouXJvh1.xjTSP2RBOlBoZ.pG', '1756400950_68b08d36025c1.png', '2025-08-28 11:09:10', NULL, 1, NULL, NULL),
(60, 'CC-2590', 'MARAVELES', 'ROWAR', 'ASUR', NULL, '2003-02-07', 'Balingasag Misamis Oriental', 'employed', '[{\"date_started\":\"2004-05-20\",\"duration\":\"21 years 3 months\"}]', 'Female', '2025', 2, 1, NULL, 'Waterfall', '09164907870', 'Waterfall', 'Waterfall@gmail.com', 'aszdfsa@gmail.com', '$2y$10$jrg.ssIh2BY/SUIWRON8JenOnCShtJQnM0U2YnW2zbIhrpcszuyCK', '1756401016_68b08d783171d.png', '2025-08-28 11:10:16', NULL, 1, NULL, NULL),
(61, 'CC-2589', 'BALUSAN', 'METCHIE', 'M.', NULL, '2001-12-28', 'Balingasag Misamis Oriental', 'student', '[{\"date_started\":\"2020-05-26\",\"duration\":\"5 years 3 months\"}]', 'Female', '2025', 2, 1, NULL, 'Watefall', '09758802531', 'Waterfall', 'Waterfall@gmail.com', 'chaesef@yahoo.com', '$2y$10$hxb4FJnqPwjYMP5ECiDCzOq3MoFFmjTffr0F2Gk56cfCXgoQ.z8RG', NULL, '2025-08-28 11:11:02', NULL, 1, NULL, NULL),
(62, 'CC-2587', 'RAYON', 'RHEJOY', 'MAMARAN', NULL, '2003-04-13', 'Waterfall', 'employed', '[{\"date_started\":\"2025-10-10\",\"duration\":\"10 months\"}]', 'Female', '2025', 2, 1, NULL, 'Waterfall', '0985612243', 'Waterfall', 'Waterfall@gmail.com', 'marjsad@yahoo.com', '$2y$10$TU8t5zAJUW31D4CAs2kCfu5xSr3tJ8.OuP7HGmOdX8GNS2zoU6s..', '1756401105_68b08dd1696c5.png', '2025-08-28 11:11:45', NULL, 1, NULL, NULL),
(63, 'CC-2584', 'MADRONERO', 'GIANFRANCO', 'SALIW', NULL, '1988-10-11', 'Waterfall', 'employed', '[{\"date_started\":\"2025-02-05\",\"duration\":\"6 months\"}]', 'Male', '2025', 1, NULL, NULL, 'Watefall', '09164907870', 'Waterfall', 'Waterfall@gmail.com', 'kennethladra3@gmail.com', '$2y$10$7bV858CFGMqYV3Sj8FpDd.VTpN/MimzC6AlhQQfi4/uFP2qKQIYMO', '1756404080_68b0997075b0b.png', '2025-08-28 12:01:20', NULL, 1, NULL, NULL),
(64, 'CC-2585', 'SUCANDAN', 'NOVY JANE', 'BALDOMAN', NULL, '2002-09-14', 'Balingasag Misamis Oriental', 'employed', '[{\"date_started\":\"2002-05-26\",\"duration\":\"23 years 3 months\"}]', 'Female', '2025', 2, 1, NULL, 'Watefall', '0985612222', 'Waterfall', 'Waterfall@gmail.com', 'ladra@gmail.com', '$2y$10$Tj2qyPIPlFUrmXTH0zGVuOd2pDQLVdm47G8btr4ByhWNZU146Q1Q6', '1756404137_68b099a9a0ea4.png', '2025-08-28 12:02:17', NULL, 1, NULL, NULL),
(65, 'CC-2580', 'UCAB', 'GERALD', 'RAGAS', NULL, '2000-05-19', 'Cagayan de oro Mis.or', 'not employed', '[{\"date_started\":\"2004-10-02\",\"duration\":\"20 years 10 months\"}]', 'Male', '2025', 1, NULL, NULL, 'Waterfall', '09164907870', 'Waterfall', 'Waterfall@gmail.com', 'kp@gmail.com', '$2y$10$Hl9hBgDqFYQ4N02NG0RFBewwC6ovDaGCQxrY2HZswmlmkRfE27MWG', '1756404175_68b099cff3213.png', '2025-08-28 12:02:55', NULL, 1, NULL, NULL),
(66, 'CC-2581', 'MACAS', 'MARY ANNE MAE', 'YAMIT', NULL, '1994-04-05', 'Balingasag Misamis Oriental', 'student', '[{\"date_started\":\"2004-10-02\",\"duration\":\"20 years 10 months\"}]', 'Female', '2025', 1, NULL, NULL, 'Waterfall', '09164907870', 'Waterfall', 'Waterfall@gmail.com', 'keerty@gmail.com', '$2y$10$jKVOGH/vhuFecvcGq/wjp.ukh4FvdtlHZFt4bg.dmi967GAHxAyxO', '1756404221_68b099fdaba81.png', '2025-08-28 12:03:41', NULL, 1, NULL, NULL),
(67, 'CC-2582', 'ESPANOL', 'JOMARIE', 'BETARMOS', NULL, '2002-09-29', 'Cagayan de oro Mis.or', 'employed', '[{\"date_started\":\"2026-10-14\",\"duration\":\"10 months\"}]', 'Male', '2025', 1, NULL, NULL, 'Waterfall', '09164907870', 'Waterfall', 'Waterfall@gmail.com', 'jiejieagbongcabrera@gmail.com', '$2y$10$mtmNhRnr.LRQwBwWMXywhepZyvGeOxyPkw0CQGZka5oZQfZUpRZQ6', '1756404267_68b09a2becf7c.png', '2025-08-28 12:04:27', NULL, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `alumnus_bio_archive`
--

CREATE TABLE `alumnus_bio_archive` (
  `id` int(11) NOT NULL,
  `alumni_id` varchar(50) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `suffixname` varchar(20) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `address` text NOT NULL,
  `employment_status` varchar(64) NOT NULL DEFAULT '',
  `employment_history` longtext DEFAULT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `batch` varchar(10) NOT NULL,
  `course_id` int(11) NOT NULL,
  `major_id` int(11) DEFAULT NULL,
  `strand_id` int(11) DEFAULT NULL,
  `connected_to` varchar(150) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `company_email` varchar(150) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `img` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `academic_honor` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `archive_alumni`
--

CREATE TABLE `archive_alumni` (
  `id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `middlename` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) NOT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `batch` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `connected_to` varchar(255) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `archived_date` datetime DEFAULT current_timestamp(),
  `archived_by` int(11) DEFAULT NULL,
  `strand_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `archive_alumni`
--

INSERT INTO `archive_alumni` (`id`, `alumni_id`, `firstname`, `middlename`, `lastname`, `gender`, `contact_no`, `email`, `batch`, `course_id`, `connected_to`, `company_address`, `address`, `status`, `archived_date`, `archived_by`, `strand_id`) VALUES
(1, 50, 'Lily', '', 'Allen', 'Female', '09856122843', 'ccharli22@gmail.com', 2018, 1, 'IT CEBU PARK', 'Sugbongcogon', 'Sugbongcogon', 1, '2025-08-26 14:49:41', 23, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `careers`
--

CREATE TABLE `careers` (
  `id` int(30) NOT NULL,
  `company` varchar(250) NOT NULL,
  `location` text NOT NULL,
  `job_title` text NOT NULL,
  `description` text NOT NULL,
  `user_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `careers`
--

INSERT INTO `careers` (`id`, `company`, `location`, `job_title`, `description`, `user_id`, `date_created`) VALUES
(1, 'IT Company', 'Home-Based', 'Web Developer', '&lt;p style=&quot;-webkit-tap-highlight-color: rgba(0, 0, 0, 0); margin-top: 1.5em; margin-bottom: 1.5em; line-height: 1.5; animation: 1000ms linear 0s 1 normal none running fadeInLorem;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Sagittis eu volutpat odio facilisis mauris sit amet massa vitae. In tellus integer feugiat scelerisque varius morbi enim. Orci eu lobortis elementum nibh tellus molestie nunc. Vulputate ut pharetra sit amet aliquam id diam maecenas ultricies. Lacus sed viverra tellus in hac habitasse platea dictumst vestibulum. Eleifend donec pretium vulputate sapien nec. Enim praesent elementum facilisis leo vel fringilla est ullamcorper. Quam adipiscing vitae proin sagittis nisl rhoncus. Sed viverra ipsum nunc aliquet bibendum. Enim ut sem viverra aliquet eget sit amet tellus. Integer feugiat scelerisque varius morbi enim nunc faucibus.&lt;/p&gt;&lt;p style=&quot;-webkit-tap-highlight-color: rgba(0, 0, 0, 0); margin-top: 1.5em; margin-bottom: 1.5em; line-height: 1.5; animation: 1000ms linear 0s 1 normal none running fadeInLorem;&quot;&gt;Viverra justo nec ultrices dui. Leo vel orci porta non pulvinar neque laoreet. Id semper risus in hendrerit gravida rutrum quisque non tellus. Sit amet consectetur adipiscing elit ut. Id neque aliquam vestibulum morbi blandit cursus risus. Tristique senectus et netus et malesuada. Amet aliquam id diam maecenas ultricies mi eget mauris. Morbi tristique senectus et netus et malesuada. Diam phasellus vestibulum lorem sed risus. Tempor orci dapibus ultrices in. Mi sit amet mauris commodo quis imperdiet. Quisque sagittis purus sit amet volutpat. Vehicula ipsum a arcu cursus. Ornare quam viverra orci sagittis eu volutpat odio facilisis. Id volutpat lacus laoreet non curabitur. Cursus euismod quis viverra nibh cras pulvinar mattis nunc. Id aliquet lectus proin nibh nisl condimentum id venenatis. Eget nulla facilisi etiam dignissim diam quis enim lobortis. Lacus suspendisse faucibus interdum posuere lorem ipsum dolor sit amet.&lt;/p&gt;', 3, '2020-10-15 14:14:27'),
(2, 'MOIST', 'Santa Cruz Cogon Balingasag', 'IT Specialist', '&lt;p data-start=&quot;173&quot; data-end=&quot;587&quot;&gt;&lt;b data-start=&quot;173&quot; data-end=&quot;193&quot;&gt;Job Description:&lt;/b&gt;&lt;br data-start=&quot;193&quot; data-end=&quot;196&quot;&gt;We are seeking a skilled and dedicated IT Specialist to join our team at MOIST. The IT Specialist will be responsible for ensuring the smooth operation and maintenance of all company IT systems, providing support to employees, and troubleshooting technical issues. The ideal candidate will have expertise in network administration, hardware and software troubleshooting, and system security.&lt;/p&gt;&lt;p data-start=&quot;589&quot; data-end=&quot;614&quot;&gt;&lt;b data-start=&quot;589&quot; data-end=&quot;614&quot;&gt;Key Responsibilities:&lt;/b&gt;&lt;/p&gt;&lt;ul data-start=&quot;615&quot; data-end=&quot;1036&quot;&gt;&lt;li data-start=&quot;615&quot; data-end=&quot;685&quot;&gt;Install, configure, and maintain IT systems, software, and hardware.&lt;/li&gt;&lt;li data-start=&quot;686&quot; data-end=&quot;754&quot;&gt;Provide technical support and troubleshooting assistance to staff.&lt;/li&gt;&lt;li data-start=&quot;755&quot; data-end=&quot;825&quot;&gt;Monitor system performance and ensure all IT systems are up to date.&lt;/li&gt;&lt;li data-start=&quot;826&quot; data-end=&quot;879&quot;&gt;Manage network security and safeguard company data.&lt;/li&gt;&lt;li data-start=&quot;880&quot; data-end=&quot;964&quot;&gt;Assist with the setup and maintenance of internal communication tools and systems.&lt;/li&gt;&lt;li data-start=&quot;965&quot; data-end=&quot;1033&quot;&gt;Document and report on IT infrastructure, issues, and resolutions.&lt;/li&gt;&lt;/ul&gt;&lt;p data-start=&quot;1037&quot; data-end=&quot;1056&quot;&gt;&lt;b data-start=&quot;1037&quot; data-end=&quot;1056&quot;&gt;Qualifications:&lt;/b&gt;&lt;/p&gt;&lt;ul data-start=&quot;1057&quot; data-end=&quot;1398&quot;&gt;&lt;li data-start=&quot;1057&quot; data-end=&quot;1139&quot;&gt;Bachelor&rsquo;s degree in Information Technology, Computer Science, or related field.&lt;/li&gt;&lt;li data-start=&quot;1140&quot; data-end=&quot;1201&quot;&gt;Proven experience in IT support and network administration.&lt;/li&gt;&lt;li data-start=&quot;1202&quot; data-end=&quot;1282&quot;&gt;Strong knowledge of operating systems, hardware, and software troubleshooting.&lt;/li&gt;&lt;li data-start=&quot;1283&quot; data-end=&quot;1341&quot;&gt;Ability to work independently and in a team environment.&lt;/li&gt;&lt;li data-start=&quot;1342&quot; data-end=&quot;1398&quot;&gt;Strong problem-solving skills and attention to detail.&lt;/li&gt;&lt;/ul&gt;', 1, '2020-10-15 15:05:37');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(30) NOT NULL,
  `course` text NOT NULL,
  `about` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course`, `about`) VALUES
(1, 'BS Information Technology', 'Bachelor of Science in Information Technology'),
(2, 'BSED', 'Bachelor of Secondary Education'),
(3, 'BEED', 'Bachelor of Elementary Education'),
(4, 'BSBA', 'Bachelor of Science in Business Administration'),
(5, 'MIDWIFERY', 'Midwifery Program'),
(6, 'CRIMINOLOGY', 'Criminology Program'),
(7, 'DSHT', 'Diploma in Hospitality and Tourism'),
(17, 'DIPLOMA', 'DIPLOMA VS DISKARTE');

-- --------------------------------------------------------

--
-- Table structure for table `employment_history`
--

CREATE TABLE `employment_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `industry_type` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date_start` date NOT NULL,
  `date_end` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employment_status`
--

CREATE TABLE `employment_status` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `current_status` enum('Employed','Unemployed','Self-Employed','Student','Retired') NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `industry_type` varchar(255) DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(30) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `schedule` datetime NOT NULL,
  `banner` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `content`, `schedule`, `banner`, `date_created`) VALUES
(6, 'Alumni Homecoming 2025', 'Join us for the &lt;b data-start=&quot;46&quot; data-end=&quot;79&quot;&gt;MOIST-ALUMNI Homecoming Event&lt;/b&gt; on &lt;b data-start=&quot;83&quot; data-end=&quot;113&quot;&gt;March 8, 2025, at 11:00 AM&lt;/b&gt;! Reconnect with old friends, network with fellow alumni, and celebrate the journey that brought us together. The event will feature inspiring guest speakers, exciting activities, and opportunities to relive cherished memories. Don&rsquo;t miss this chance to be part of a meaningful gathering that strengthens our alumni community. We look forward to welcoming you back!', '2025-03-08 11:00:00', '1740794400_3_img.jpg', '2025-03-01 10:00:27');

-- --------------------------------------------------------

--
-- Table structure for table `event_bookmarks`
--

CREATE TABLE `event_bookmarks` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookmarks`
--

INSERT INTO `event_bookmarks` (`id`, `event_id`, `user_id`, `created_at`) VALUES
(3, 6, 25, '2025-08-16 01:46:30'),
(8, 6, 27, '2025-08-21 03:36:19');

-- --------------------------------------------------------

--
-- Table structure for table `event_comments`
--

CREATE TABLE `event_comments` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_comments`
--

INSERT INTO `event_comments` (`id`, `event_id`, `user_id`, `comment`, `created_at`) VALUES
(1, 6, 25, 'Chada lagi na', '2025-08-16 01:19:15'),
(2, 6, 28, 'Ako sad', '2025-08-17 21:54:14'),
(3, 6, 27, 'Apila ko', '2025-08-18 07:13:42');

-- --------------------------------------------------------

--
-- Table structure for table `event_commits`
--

CREATE TABLE `event_commits` (
  `id` int(30) NOT NULL,
  `event_id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_commits`
--

INSERT INTO `event_commits` (`id`, `event_id`, `user_id`) VALUES
(1, 1, 3),
(2, 2, 5),
(3, 1, 5),
(4, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `event_likes`
--

CREATE TABLE `event_likes` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_likes`
--

INSERT INTO `event_likes` (`id`, `event_id`, `user_id`, `created_at`) VALUES
(1, 6, 25, '2025-08-16 01:28:19'),
(3, 6, 28, '2025-08-17 21:54:10'),
(4, 6, 27, '2025-08-18 04:57:00');

-- --------------------------------------------------------

--
-- Table structure for table `forum_comments`
--

CREATE TABLE `forum_comments` (
  `id` int(30) NOT NULL,
  `topic_id` int(30) NOT NULL,
  `comment` text NOT NULL,
  `user_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_comments`
--

INSERT INTO `forum_comments` (`id`, `topic_id`, `comment`, `user_id`, `date_created`) VALUES
(1, 3, 'Sample updated Comment', 3, '2020-10-15 15:46:03'),
(5, 0, '', 1, '2020-10-16 09:49:34'),
(6, 1, 'Apply ko', 1, '2025-02-26 14:23:46'),
(7, 1, '&lt;h1&gt;&lt;u&gt;ako nalang apply&lt;/u&gt;&lt;/h1&gt;', 4, '2025-02-26 14:31:39'),
(8, 1, '&lt;span style=&quot;font-size:18px;&quot;&gt;Sge ugma&lt;/span&gt;', 1, '2025-02-26 14:32:45'),
(9, 1, '&lt;h2&gt;charliezapicoabejo123@gmail.com e contak ko&lt;/h2&gt;', 5, '2025-02-26 15:50:18'),
(10, 1, 'sge hulat ko', 1, '2025-02-26 15:50:37'),
(11, 1, 'pwedi mag apply ko tig ihaw sa babooy manok lang', 6, '2025-02-26 16:00:09'),
(12, 1, 'sge ikaw&amp;nbsp; akong ihawon una', 1, '2025-02-26 16:01:23'),
(13, 1, '&lt;h1&gt;please klnfg ba&lt;/h1&gt;', 1, '2025-02-26 16:02:12'),
(14, 1, 'ML NASAD TA', 1, '2025-02-27 21:35:50');

-- --------------------------------------------------------

--
-- Table structure for table `forum_topics`
--

CREATE TABLE `forum_topics` (
  `id` int(30) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_topics`
--

INSERT INTO `forum_topics` (`id`, `title`, `description`, `user_id`, `date_created`) VALUES
(4, 'Job Post Manager', '&lt;p&gt;CharliePogi is looking for a dedicated &lt;b data-start=&quot;289&quot; data-end=&quot;309&quot;&gt;Job Post Manager&lt;/b&gt; to oversee the creation, management, and optimization of job postings across various platforms. In this role, you will be responsible for drafting clear and engaging job descriptions, ensuring job posts align with the company&amp;#x2019;s recruitment strategy, and monitoring the performance of each post.&lt;/p&gt;&lt;p data-start=&quot;725&quot; data-end=&quot;763&quot; data-is-last-node=&quot;&quot; data-is-only-node=&quot;&quot;&gt;&lt;/p&gt;', 1, '2020-10-16 08:31:45');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(30) NOT NULL,
  `about` text NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `about`, `created`) VALUES
(10, 'Alumni homecoming is a special occasion that brings together former students, faculty, and staff to celebrate shared memories and achievements.', '2025-02-27 22:39:27');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `action` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `major` varchar(100) NOT NULL,
  `about` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`id`, `course_id`, `major`, `about`) VALUES
(1, 2, 'English', 'Bachelor of Secondary Education Major in English'),
(2, 2, 'Mathematics', 'Bachelor of Secondary Education Major in Mathematics'),
(3, 2, 'Social Studies', 'Bachelor of Secondary Education Major in Social Studies'),
(4, 2, 'Values Education', 'Bachelor of Secondary Education Major in Values Education');

-- --------------------------------------------------------

--
-- Table structure for table `otp_history`
--

CREATE TABLE `otp_history` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` datetime DEFAULT NULL,
  `status` enum('success','failed','expired') DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `email`, `phone`, `otp`, `expires_at`, `used`, `created_at`) VALUES
(1, 'capstonee2@gmail.com', NULL, '994955', '2025-08-16 20:28:11', 1, '2025-08-16 12:23:11'),
(2, 'capstonee2@gmail.com', NULL, '330681', '2025-08-17 07:45:58', 1, '2025-08-16 23:40:58'),
(3, 'capstonee2@gmail.com', NULL, '867548', '2025-08-17 08:17:22', 1, '2025-08-17 00:12:22'),
(4, 'capstonee2@gmail.com', NULL, '359501', '2025-08-17 08:18:42', 1, '2025-08-17 00:13:42'),
(5, 'capstonee2@gmail.com', NULL, '863005', '2025-08-17 08:19:11', 1, '2025-08-17 00:14:11'),
(6, 'capstonee2@gmail.com', NULL, '935507', '2025-08-17 08:23:53', 1, '2025-08-17 00:18:53'),
(7, 'capstonee2@gmail.com', NULL, '359793', '2025-08-17 08:49:49', 1, '2025-08-17 00:44:49'),
(8, 'capstonee2@gmail.com', NULL, '265132', '2025-08-17 13:24:14', 1, '2025-08-17 05:19:14'),
(9, 'capstonee2@gmail.com', NULL, '173544', '2025-08-17 13:30:18', 1, '2025-08-17 05:25:18'),
(10, 'capstonee2@gmail.com', NULL, '364530', '2025-08-17 17:10:41', 1, '2025-08-17 09:05:41'),
(11, 'capstonee2@gmail.com', NULL, '392146', '2025-08-17 17:25:14', 1, '2025-08-17 09:20:14'),
(12, 'capstonee2@gmail.com', NULL, '863616', '2025-08-17 17:27:25', 1, '2025-08-17 09:22:25'),
(13, 'capstonee2@gmail.com', NULL, '630197', '2025-08-17 17:31:13', 1, '2025-08-17 09:26:13'),
(14, 'capstonee2@gmail.com', NULL, '875083', '2025-08-17 21:53:15', 1, '2025-08-17 13:48:15'),
(15, 'tineemaeeyy@gmail.com', NULL, '982020', '2025-08-17 21:57:49', 1, '2025-08-17 13:52:49'),
(16, 'capstonee2@gmail.com', NULL, '516907', '2025-08-18 05:01:25', 1, '2025-08-17 20:56:25'),
(17, 'capstonee2@gmail.com', NULL, '360744', '2025-08-18 05:13:47', 1, '2025-08-17 21:08:47'),
(18, 'capstonee2@gmail.com', NULL, '739985', '2025-08-18 05:18:20', 1, '2025-08-17 21:13:20'),
(19, 'capstonee2@gmail.com', NULL, '700455', '2025-08-18 06:05:03', 1, '2025-08-17 22:00:03'),
(20, 'capstonee2@gmail.com', NULL, '203329', '2025-08-18 06:05:07', 1, '2025-08-17 22:00:07'),
(21, 'capstonee2@gmail.com', NULL, '200063', '2025-08-18 07:17:51', 1, '2025-08-17 23:12:51'),
(22, 'capstonee2@gmail.com', NULL, '472949', '2025-08-18 07:17:55', 1, '2025-08-17 23:12:55'),
(23, 'capstonee2@gmail.com', NULL, '811862', '2025-08-19 00:14:28', 1, '2025-08-18 16:09:28'),
(24, 'capstonee2@gmail.com', NULL, '536874', '2025-08-19 00:14:32', 1, '2025-08-18 16:09:32'),
(25, 'capstonee2@gmail.com', NULL, '381220', '2025-08-19 00:18:19', 1, '2025-08-18 16:13:19'),
(26, 'capstonee2@gmail.com', NULL, '806208', '2025-08-19 00:18:23', 1, '2025-08-18 16:13:23'),
(27, 'capstonee2@gmail.com', NULL, '124425', '2025-08-19 00:20:38', 1, '2025-08-18 16:15:38'),
(28, 'capstonee2@gmail.com', NULL, '286248', '2025-08-19 00:20:42', 1, '2025-08-18 16:15:42'),
(29, 'capstonee2@gmail.com', NULL, '853606', '2025-08-19 00:27:52', 1, '2025-08-18 16:22:52'),
(30, 'capstonee2@gmail.com', NULL, '925668', '2025-08-19 00:27:56', 1, '2025-08-18 16:22:56'),
(31, 'tineemaeeyy@gmail.com', NULL, '313477', '2025-08-19 01:03:35', 1, '2025-08-18 16:58:35'),
(32, 'tineemaeeyy@gmail.com', NULL, '974774', '2025-08-19 01:03:39', 1, '2025-08-18 16:58:39'),
(33, 'capstonee2@gmail.com', NULL, '731781', '2025-08-19 09:10:18', 1, '2025-08-19 01:05:18'),
(34, 'capstonee2@gmail.com', NULL, '402061', '2025-08-19 09:10:26', 1, '2025-08-19 01:05:26'),
(35, 'capstonee2@gmail.com', NULL, '854095', '2025-08-19 09:18:19', 1, '2025-08-19 01:13:19'),
(36, 'capstonee2@gmail.com', NULL, '672446', '2025-08-19 09:18:23', 1, '2025-08-19 01:13:23'),
(37, 'capstonee2@gmail.com', NULL, '737117', '2025-08-19 09:22:57', 1, '2025-08-19 01:17:57'),
(38, 'capstonee2@gmail.com', NULL, '609251', '2025-08-19 10:26:46', 1, '2025-08-19 02:21:46'),
(39, 'capstonee2@gmail.com', NULL, '617448', '2025-08-19 10:33:05', 1, '2025-08-19 02:28:05'),
(40, 'capstonee2@gmail.com', NULL, '591155', '2025-08-19 10:37:17', 1, '2025-08-19 02:32:17'),
(41, 'capstonee2@gmail.com', NULL, '684958', '2025-08-19 11:13:08', 1, '2025-08-19 03:08:08'),
(42, 'capstonee2@gmail.com', NULL, '853060', '2025-08-21 01:00:00', 1, '2025-08-20 16:55:00'),
(43, 'capstonee2@gmail.com', NULL, '309272', '2025-08-21 01:05:12', 1, '2025-08-20 17:00:12'),
(44, 'capstonee2@gmail.com', NULL, '119320', '2025-08-21 01:42:56', 1, '2025-08-20 17:37:56'),
(45, 'capstonee2@gmail.com', NULL, '582535', '2025-08-21 01:52:49', 1, '2025-08-20 17:47:49'),
(46, 'capstonee2@gmail.com', NULL, '152677', '2025-08-21 02:04:33', 1, '2025-08-20 17:59:33'),
(47, 'capstonee2@gmail.com', NULL, '423281', '2025-08-21 02:58:18', 1, '2025-08-20 18:53:18'),
(48, 'capstonee2@gmail.com', NULL, '229846', '2025-08-21 03:06:24', 1, '2025-08-20 19:01:24'),
(49, 'capstonee2@gmail.com', NULL, '218897', '2025-08-21 03:44:16', 1, '2025-08-20 19:39:16'),
(50, 'capstonee2@gmail.com', NULL, '160994', '2025-08-21 03:54:51', 1, '2025-08-20 19:49:51'),
(51, 'capstonee2@gmail.com', NULL, '794553', '2025-08-21 05:13:47', 1, '2025-08-20 21:08:47'),
(52, 'capstonee2@gmail.com', NULL, '272412', '2025-08-21 06:48:11', 1, '2025-08-20 22:43:11'),
(53, 'marialanievalmoria009@gmail.com', NULL, '841195', '2025-08-29 02:55:03', 1, '2025-08-28 18:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `privacy_agreements`
--

CREATE TABLE `privacy_agreements` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `agree_date` datetime NOT NULL,
  `ip_address` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `token` varchar(128) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`id`, `alumni_id`, `token`, `expiry`, `created_at`) VALUES
(1, 56, 'cca36b42290bbe247d454deed32771bb4bd09869223eb9a71268a845125d80c4', '2025-09-28 02:50:27', '2025-08-28 18:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `strands`
--

INSERT INTO `strands` (`id`, `name`, `description`) VALUES
(1, 'STEM', 'Science, Technology, Engineering, and Mathematics'),
(2, 'HUMSS', 'Humanities and Social Sciences'),
(3, 'ABM', 'Accountancy, Business, and Management'),
(4, 'GAS', 'General Academic Strand'),
(5, 'TVL', 'Technical-Vocational-Livelihood');

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `name`, `email`, `contact`, `cover_img`, `about_content`) VALUES
(1, 'MOIST ALUMNI TRACKING SYSTEM', 'capstonee2@gmail.com', '09856122843', '1740794460_1740717360_1602738120_pngtree-purple-hd-business-banner-image_5493.jpg', '&lt;p style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;/p&gt;&lt;article class=&quot;w-full text-token-text-primary focus-visible:outline-2 focus-visible:outline-offset-[-4px]&quot; dir=&quot;auto&quot; data-testid=&quot;conversation-turn-3&quot; data-scroll-anchor=&quot;true&quot;&gt;&lt;p class=&quot;m-auto text-base py-[18px] px-6&quot;&gt;&lt;/p&gt;&lt;p class=&quot;mx-auto flex flex-1 text-base gap-4 md:gap-5 lg:gap-6 md:max-w-3xl&quot;&gt;&lt;/p&gt;&lt;p class=&quot;group/conversation-turn relative flex w-full min-w-0 flex-col agent-turn @xs/thread:px-0 @sm/thread:px-1.5 @md/thread:px-4&quot;&gt;&lt;/p&gt;&lt;p class=&quot;flex-col gap-1 md:gap-3&quot;&gt;&lt;/p&gt;&lt;p class=&quot;flex max-w-full flex-col flex-grow&quot;&gt;&lt;/p&gt;&lt;p data-message-author-role=&quot;assistant&quot; data-message-id=&quot;25acc943-3923-44bf-ace9-29e39872e634&quot; dir=&quot;auto&quot; class=&quot;min-h-8 text-message relative flex w-full flex-col items-end gap-2 whitespace-normal break-words text-start [.text-message+&amp;amp;]:mt-5&quot; data-message-model-slug=&quot;gpt-4o-mini&quot;&gt;&lt;/p&gt;&lt;p class=&quot;flex w-full flex-col gap-1 empty:hidden first:pt-[3px]&quot;&gt;&lt;/p&gt;&lt;p class=&quot;markdown prose w-full break-words dark:prose-invert light&quot;&gt;&lt;/p&gt;&lt;p data-start=&quot;676&quot; data-end=&quot;1165&quot; data-is-last-node=&quot;&quot; data-is-only-node=&quot;&quot;&gt;I am developing the &lt;b data-start=&quot;696&quot; data-end=&quot;790&quot;&gt;MOIST (Misamis Oriental Institute of Science and Technology School Alumni Tracking System)&lt;/b&gt;. This system is designed to help track and manage alumni data, making it easier for the school to stay connected with former students, share updates, and facilitate alumni activities. By implementing MOIST, the school can efficiently maintain a robust alumni network while also offering a platform for alumni to engage with one another and contribute to the school&rsquo;s growth.&lt;/p&gt;&lt;/article&gt;&lt;p style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;&lt;/p&gt;');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 4 COMMENT '1=Admin,2=Alumni officer, 3= alumnus, 4=registrar',
  `auto_generated_pass` text NOT NULL,
  `alumnus_id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`, `auto_generated_pass`, `alumnus_id`) VALUES
(1, 'Admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', 1, '', 0),
(23, 'test', 'test', '098f6bcd4621d373cade4e832627b4f6', 4, '', 0),
(34, 'Charlie CHARLIEJAMES', 'Ca@gmail.com', '7c0e9a8857289bd39512723e5c3010a4', 3, '', 11),
(36, 'Charlie CHARLIEJAMES', 'capstonee2@gmail.com', '7c0e9a8857289bd39512723e5c3010a4', 3, '', 13);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `alumni_ids`
--
ALTER TABLE `alumni_ids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alumni_id` (`alumni_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `fk_alumni_strand` (`strand_id`);

--
-- Indexes for table `alumnus_bio`
--
ALTER TABLE `alumnus_bio`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `alumnus_bio_archive`
--
ALTER TABLE `alumnus_bio_archive`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `archive_alumni`
--
ALTER TABLE `archive_alumni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `archived_by` (`archived_by`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `careers`
--
ALTER TABLE `careers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employment_history`
--
ALTER TABLE `employment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `employment_status`
--
ALTER TABLE `employment_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `events` ADD FULLTEXT KEY `ft_title_content` (`title`,`content`);
ALTER TABLE `events` ADD FULLTEXT KEY `title` (`title`,`content`);

--
-- Indexes for table `event_bookmarks`
--
ALTER TABLE `event_bookmarks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bookmark` (`event_id`,`user_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_comments`
--
ALTER TABLE `event_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `event_commits`
--
ALTER TABLE `event_commits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_likes`
--
ALTER TABLE `event_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`event_id`,`user_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_comments`
--
ALTER TABLE `forum_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forum_topics`
--
ALTER TABLE `forum_topics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_idx` (`user_id`),
  ADD KEY `email_idx` (`email`(191));

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `otp_history`
--
ALTER TABLE `otp_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`(191));

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`(191),`otp`),
  ADD KEY `expires_at` (`expires_at`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `privacy_agreements`
--
ALTER TABLE `privacy_agreements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `alumni_id` (`alumni_id`);

--
-- Indexes for table `strands`
--
ALTER TABLE `strands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `alumni_ids`
--
ALTER TABLE `alumni_ids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `alumnus_bio`
--
ALTER TABLE `alumnus_bio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `alumnus_bio_archive`
--
ALTER TABLE `alumnus_bio_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `archive_alumni`
--
ALTER TABLE `archive_alumni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `careers`
--
ALTER TABLE `careers`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `employment_history`
--
ALTER TABLE `employment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employment_status`
--
ALTER TABLE `employment_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event_bookmarks`
--
ALTER TABLE `event_bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `event_comments`
--
ALTER TABLE `event_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `event_commits`
--
ALTER TABLE `event_commits`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_likes`
--
ALTER TABLE `event_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `forum_comments`
--
ALTER TABLE `forum_comments`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `otp_history`
--
ALTER TABLE `otp_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privacy_agreements`
--
ALTER TABLE `privacy_agreements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni_ids`
--
ALTER TABLE `alumni_ids`
  ADD CONSTRAINT `alumni_ids_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alumni_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alumni_strand` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_strand` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`);

--
-- Constraints for table `archive_alumni`
--
ALTER TABLE `archive_alumni`
  ADD CONSTRAINT `archive_alumni_ibfk_1` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `archive_alumni_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `employment_history`
--
ALTER TABLE `employment_history`
  ADD CONSTRAINT `employment_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`);

--
-- Constraints for table `employment_status`
--
ALTER TABLE `employment_status`
  ADD CONSTRAINT `employment_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`);

--
-- Constraints for table `majors`
--
ALTER TABLE `majors`
  ADD CONSTRAINT `majors_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `privacy_agreements`
--
ALTER TABLE `privacy_agreements`
  ADD CONSTRAINT `privacy_agreements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
