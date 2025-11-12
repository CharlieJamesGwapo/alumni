-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2025 at 08:53 AM
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
  `user_name` varchar(255) DEFAULT NULL,
  `user_type` enum('admin','registrar','alumni') DEFAULT 'alumni',
  `action_type` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `user_name`, `user_type`, `action_type`, `details`, `target_id`, `target_type`, `ip_address`, `user_agent`, `timestamp`) VALUES
(1, 1, 'Admin', 'admin', 'CREATE_JOB', 'Job: MOISTDOMAIN at MOISTDOMAIN', NULL, 'job', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:07:05'),
(2, 1, 'Admin', 'admin', 'CREATE_COURSE', 'Created course: MOISTDOMAIN', 12, 'course', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:07:16'),
(3, 1, 'Admin', 'admin', 'DELETE_COURSE', 'Deleted course: MOISTDOMAIN', 12, 'course', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:07:21'),
(4, 1, 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:08:33'),
(5, 1, 'Admin', 'admin', 'DELETE_COURSE', 'Deleted course: BDSA', 11, 'course', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:11:42'),
(6, 1, 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:13:55'),
(7, 1, 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:31:29'),
(8, 1, 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:32:46'),
(9, 1, 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:32:47'),
(10, 1, 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:32:48'),
(11, 1, 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:33:28'),
(12, 1, 'Admin', 'admin', '23', 'Alumni validated', 0, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:35:16'),
(13, 1, 'Admin', 'admin', '23', 'Alumni validated', 0, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:39:16'),
(14, 1, 'Admin', 'admin', '23', 'Alumni validated', 0, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:47:16'),
(15, 1, 'Admin', 'admin', '23', 'Alumni validated', 0, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:49:20'),
(16, 23, 'test', 'registrar', 'Alumni validated', 'Alumni AINE CASAS (ID: BSBA-2604) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:51:07'),
(17, 23, 'test', 'registrar', 'Alumni validated', 'Alumni AINE CASAS (ID: BSBA-2604) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:55:13'),
(18, 23, 'test', 'registrar', 'Alumni validated', 'Alumni MARY ANNE AMPO (ID: SHS-2503) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:57:06'),
(19, 23, 'test', 'registrar', 'Alumni validated', 'Alumni MARY ANNE AMPO (ID: SHS-2503) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 02:06:00'),
(20, 23, 'test', 'registrar', 'Alumni validated', 'Alumni MICHEL CABALLERO (ID: CC-2501) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 03:07:42'),
(21, 23, 'test', 'registrar', 'Alumni validated', 'Alumni AINE CASAS (ID: BSBA-2604) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 05:18:32'),
(22, 23, 'test', 'registrar', 'Alumni validated', 'Alumni GIANFRANCO MADRONERO (ID: CC-2584) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 08:47:37'),
(23, 23, 'test', 'registrar', 'Alumni validated', 'Alumni Angela Santos (ID: CC-2504) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 14:45:34'),
(24, 23, 'test', 'registrar', 'Alumni validated', 'Alumni Anthony Torres (ID: CC-2507) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 14:45:41'),
(25, 23, 'test', 'registrar', 'Alumni validated', 'Alumni Arvin Mendoza (ID: CC-2520) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 14:45:53'),
(26, 23, 'test', 'registrar', 'Alumni validated', 'Alumni Charlie Cruz (ID: CC-2501) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 14:46:24'),
(27, 23, 'test', 'registrar', 'Alumni validated', 'Alumni JANRO MICABALO (ID: CC-2507) was validated by registrar test', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-19 18:07:59'),
(28, 23, 'test', 'registrar', 'Alumni validated', 'Alumni JANRO MICABALO (ID: CC-2507) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 18:21:37');

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
  `email` varchar(255) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `batch` year(4) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `program_type` enum('College','Senior High') DEFAULT 'College',
  `strand_id` int(11) DEFAULT NULL,
  `contact_no` varchar(11) NOT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `alumni_ids`
--

INSERT INTO `alumni_ids` (`id`, `alumni_id`, `lastname`, `firstname`, `middlename`, `suffixname`, `email`, `birthdate`, `gender`, `batch`, `course_id`, `major_id`, `date_created`, `program_type`, `strand_id`, `contact_no`, `password`) VALUES
(1, 'CC-2501', 'CABALLERO', 'MICHEL', 'JAMOROI', NULL, 'kennethladra3@gmail.com', '2001-05-19', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '09677314559', NULL),
(3, 'CC-2503', 'PADUA', 'CELESTE', 'SACEDON', NULL, 'paulinmgrayos@gmail.com', '2000-03-01', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '09856122841', NULL),
(5, 'CC-2505', 'BENDICIO', 'CARLO', 'LABUANON', NULL, NULL, '2002-04-13', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(6, 'CC-2506', 'MACAHILOS', 'KRISTIAN KING', 'MACARAYO', NULL, NULL, '2002-12-15', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(7, 'CC-2507', 'MICABALO', 'JANRO', 'JOVEN', NULL, 'capstonee2@gmail.com', '2002-08-16', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '09856122843', '$2y$10$Nn7V4cqWElfb63DfKo6peuX/Pe4jfcOBUcd3ElJNcbeBCKUipf8zi'),
(8, 'CC-2508', 'PREL', 'NINO LOVE', 'B.', NULL, NULL, '2001-01-05', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(9, 'CC-2509', 'LADRA', 'JOHN LEE', 'M.', NULL, NULL, '2000-01-03', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(10, 'CC-2510', 'VERGARA', 'MICHAEL VINCENT', 'G.', NULL, NULL, '2002-04-04', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(11, 'CC-2511', 'FERNANDEZ', 'JOHN DAVE', 'L.', NULL, NULL, '2002-06-22', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(12, 'CC-2512', 'RAYON', 'JAPHETH JIREH', NULL, NULL, NULL, '2002-10-18', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(13, 'CC-2513', 'ADAJAR', 'JUVELLE', 'C.', NULL, NULL, '2002-12-13', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(14, 'CC-2514', 'CANQUE', 'CHASTER', 'L.', NULL, NULL, '2001-01-01', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(15, 'CC-2515', 'BORRES', 'JOHN CONRAD', 'A.', NULL, NULL, '2000-08-19', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(16, 'CC-2516', 'PAJARON', 'CLAVEL', 'I.', NULL, NULL, '1999-10-14', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(17, 'CC-2517', 'TUBIANO', 'JAY ANNE', 'C.', NULL, NULL, '2003-06-07', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(18, 'CC-2518', 'VARIACION', 'MERLYN', 'T.', NULL, NULL, '2002-06-19', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(19, 'CC-2519', 'PAJARON', 'DAIMIE ROSE', 'D.', NULL, NULL, '2000-05-19', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(20, 'CC-2520', 'VALMORES', 'JOY', 'C.', NULL, NULL, '2001-06-15', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(21, 'CC-2521', 'ALABA', 'ALTHEA', 'M.', NULL, NULL, '2002-06-25', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(22, 'CC-2522', 'CAGALAWAN', 'RHENIL JANE', 'D.', NULL, NULL, '2002-08-21', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(23, 'CC-2523', 'GAYLAWAN', 'JOYSAN', 'U.', NULL, NULL, '2002-08-31', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(24, 'CC-2524', 'DALANIEL', 'JENNIE', 'D.', NULL, NULL, '2002-01-07', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(25, 'CC-2525', 'PAGHANAPAN', 'JERORIS JAY', 'R.', NULL, NULL, '2003-03-18', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(26, 'CC-2526', 'WARQUE', 'MELCHOR', 'C.', 'JR.', NULL, '2002-11-25', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(27, 'CC-2527', 'DEGAMO', 'KEVIN JOHN MICHAEL', NULL, NULL, NULL, '2003-02-27', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(28, 'CC-2528', 'APA', 'LEONARDO', 'S.', NULL, NULL, '2002-05-03', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(29, 'CC-2529', 'DAGHAY', 'AXEL KING', 'P.', NULL, NULL, '1998-10-17', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(30, 'CC-2530', 'SUDLAYAN', 'RAYNAND', 'G.', NULL, NULL, '2000-10-03', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(31, 'CC-2531', 'PADAYAO', 'TIMOTEO', 'M.', 'JR.', NULL, '2000-02-14', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(32, 'CC-2532', 'VIRTUDAZO', 'JIMUEL', 'GULA', NULL, NULL, '2003-02-25', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(33, 'CC-2533', 'CABACTULAN', 'DONNA MAE', 'C.', NULL, NULL, '2003-02-20', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(34, 'CC-2534', 'SALISE', 'RICA', 'S.', NULL, NULL, '2002-08-05', 'Female', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(35, 'CC-2535', 'BADILLA', 'ANNERSON', 'C.', NULL, NULL, '2002-08-30', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(36, 'CC-2536', 'CADRON', 'PHILIP', 'P.', NULL, NULL, '2002-10-21', 'Male', '2025', 6, NULL, '2025-08-26 06:03:44', 'College', NULL, '', NULL),
(37, 'CC-2579', 'GALDO', 'ROVIE', 'OPENA', NULL, NULL, '2002-10-28', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL, '', NULL),
(38, 'CC-2580', 'UCAB', 'GERALD', 'RAGAS', NULL, NULL, '2000-05-19', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL, '', NULL),
(39, 'CC-2581', 'MACAS', 'MARY ANNE MAE', 'YAMIT', NULL, NULL, '1994-04-05', 'Female', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL, '', NULL),
(40, 'CC-2582', 'ESPANOL', 'JOMARIE', 'BETARMOS', NULL, NULL, '2002-09-29', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL, '', NULL),
(41, 'CC-2583', 'ABOG', 'FRANCO', 'ALFAR', NULL, NULL, '2000-11-25', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL, '', NULL),
(42, 'CC-2584', 'MADRONERO', 'GIANFRANCO', 'SALIW', NULL, '', '1988-10-11', 'Male', '2025', 1, NULL, '2025-08-26 06:04:35', 'College', NULL, '09856122843', NULL),
(83, 'CC-2585', 'SUCANDAN', 'NOVY JANE', 'BALDOMAN', NULL, NULL, '2002-09-14', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL, '', NULL),
(84, 'CC-2586', 'CACULBA', 'ALBERT', 'HALUAG', NULL, NULL, '1999-04-26', 'Male', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL, '', NULL),
(85, 'CC-2587', 'RAYON', 'RHEJOY', 'MAMARAN', NULL, NULL, '2003-04-13', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL, '', NULL),
(86, 'CC-2588', 'ACERA', 'ISA BONNI EVE', 'ALONZO', NULL, NULL, '2002-11-30', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL, '', NULL),
(87, 'CC-2589', 'BALUSAN', 'METCHIE', 'M.', NULL, NULL, '2001-12-28', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL, '', NULL),
(88, 'CC-2590', 'MARAVELES', 'ROWAR', 'ASUR', NULL, NULL, '2003-02-07', 'Female', '2025', 2, 1, '2025-08-26 06:30:00', 'College', NULL, '', NULL),
(89, 'CC-2591', 'LOZANO', 'MOSES JIREH', 'AMATONG', NULL, 'moseslozano20@gmail.com', '2000-11-21', 'Male', '2022', 1, NULL, '2025-09-06 05:40:03', 'College', NULL, '09164813376', NULL),
(91, 'CC-2592', 'BAGARES', 'KIMLEYE', 'P', NULL, 'bkimleye@gmail.com', '2001-11-15', 'Male', '2023', 6, NULL, '2025-09-06 06:03:15', 'College', NULL, '09970680004', NULL),
(93, 'CC-2593', 'PADILLA', 'SHIEHONNY THEA', NULL, NULL, 'Padillashiehonny@gmail.com', '2000-11-17', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000001', NULL),
(94, 'CC-2594', 'DELA CRUZ', 'VINCONT', 'ALABAN', NULL, 'Pagoyss@gmail.com', '2002-04-04', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000002', NULL),
(95, 'CC-2595', 'LEGASPI', 'IRAH', 'NAVARRO', NULL, 'Irah095@gmail.com', '2001-11-23', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000003', NULL),
(96, 'CC-2596', 'PAGARA', 'JULITO', NULL, NULL, NULL, '1990-10-24', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000004', NULL),
(97, 'CC-2597', 'LLAGAS', 'CHARITY MAE', 'MICABALO', NULL, 'llagascharitymae@gmail.com', '2001-11-01', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000005', NULL),
(98, 'CC-2598', 'REYES', 'ALVIN', 'ESTARES', NULL, 'alvinreyesii210@gmail.com', '2001-10-27', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000006', NULL),
(99, 'CC-2599', 'OCATE', 'VENZ JERIC', 'ZARATE', NULL, 'ocatevenz@gmail.com', '2002-01-30', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000007', NULL),
(100, 'CC-2600', 'SABALLA', 'SEMMARY', 'GUERRA', NULL, 'semdaxguerra@gmail.com', '2000-07-26', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000008', NULL),
(101, 'CC-2601', 'OROJALES', 'BRAYAN REY', 'P', NULL, 'brayanorojales@gamil.com', '2001-04-11', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000009', NULL),
(102, 'CC-2602', 'F', 'PRINCESS ALLIAH', NULL, NULL, NULL, '2003-01-28', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000010', NULL),
(103, 'CC-2603', 'NAJOS', 'JHANNA JANE', 'CAMANIAN', NULL, 'jhannajanenajos@gmail.com', '2002-03-28', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000011', NULL),
(104, 'CC-2604', 'MORGA', 'MAICA', 'CATANONG', NULL, 'Maicamorga4@gmail.com', '2001-11-25', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000012', NULL),
(105, 'CC-2605', 'LILOC', 'VANESSA', NULL, NULL, 'lilocvanessa@gmail.com', '2001-10-02', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000013', NULL),
(106, 'CC-2606', 'NUENA', 'RUSSEL', NULL, NULL, 'Nuenarussel1@gmail.com', '2001-12-07', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000014', NULL),
(107, 'CC-2607', 'DALAUTA', 'PAULA JENNESSA', NULL, NULL, 'paulajennessa@gmail.com', '2002-10-17', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000015', NULL),
(108, 'CC-2608', 'SALAZAR', 'TRISHA', NULL, NULL, 'Salazartrisha088@gmail.com', '2003-06-20', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000016', NULL),
(109, 'CC-2609', 'CAGADAS', 'ARCHE NEAL', 'CABANTAC', NULL, 'archenealcagadas@gmail.com', '2000-06-14', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000017', NULL),
(110, 'CC-2610', 'ABOC', 'RETMOND', 'DE LA CRUZ', NULL, 'abocreymond@gmail.com', '2000-03-10', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000018', NULL),
(111, 'CC-2611', 'CABURAL', 'JHAN NICO', NULL, NULL, 'nicnicimafranca24@gmail.co', '2000-12-24', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000019', NULL),
(112, 'CC-2612', 'ZAPICO', 'ASHLYCHELLE', NULL, NULL, 'Zapicoashley91@gmail.com', '2003-04-06', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000020', NULL),
(113, 'CC-2613', 'BARROS', 'JOLINA', 'ESTRADA', NULL, 'Barrosjohna@gmail.com', '2001-10-23', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000021', NULL),
(114, 'CC-2614', 'CABISAN', 'HANZ MASARN', NULL, NULL, 'Cabisar11@gmail.com', '2001-10-15', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000022', NULL),
(115, 'CC-2615', 'LIRCY', 'XYDER CLYDE', 'PANTE', NULL, 'Xyderclyde1@gmail.com', '2002-11-06', 'Male', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000023', NULL),
(116, 'CC-2616', 'GALEON', 'DONALYN', NULL, NULL, 'acquiatandonalyn@gmail.com', '2003-02-06', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000024', NULL),
(117, 'CC-2617', 'GLEZYL', ' ', NULL, NULL, NULL, '0000-00-00', 'Female', '2025', 13, NULL, '2025-09-08 22:04:11', 'College', NULL, '09170000025', NULL),
(118, 'SHS-2501', 'LABIAL', 'PRINCESS JANE', 'B', NULL, 'princess.labial@dummy.com', '2023-07-16', 'Female', '2025', NULL, NULL, '2025-09-08 23:03:15', '', 2, '09171234567', NULL),
(119, 'SHS-2502', 'RODRIGUEZ', 'ROSELYN', 'T', NULL, 'roselyn.rodriguez@dummy.com', '2000-06-04', 'Female', '2025', NULL, NULL, '2025-09-08 23:03:15', '', 5, '09179876543', NULL),
(120, 'SHS-2503', 'AMPO', 'MARY ANNE', 'QUIMSON', NULL, 'capstonee2@gmail.com', '2004-02-25', 'Female', '2025', NULL, NULL, '2025-09-08 23:03:15', '', 1, '09856122843', NULL),
(121, 'MW-2501', 'LABIAL', 'ARAVILA', 'LAGNO', NULL, 'abinglabial24@gmail.com', '2003-11-24', 'Female', '2025', 5, NULL, '2025-09-09 01:47:40', 'College', NULL, '09168658690', NULL),
(122, 'MW-2502', 'MAISO', 'AIVE', 'D', NULL, 'KYAHGOMONAY200@GMAIL.COM', '1998-11-11', 'Female', '2025', 5, NULL, '2025-09-09 01:47:40', 'College', NULL, '09709843066', NULL),
(123, 'MW-2503', 'TACULOD', 'JESSELLE', 'JABAGAT', NULL, 'Jesselletaculod@gmail.com', '2003-10-11', 'Female', '2025', 5, NULL, '2025-09-09 01:47:40', 'College', NULL, '09922573860', NULL),
(124, 'MW-2504', 'MARBAN', 'PEARL ANGELYN', 'ABANIL', NULL, 'marbanpearl@gmail.com', '2003-09-06', 'Female', '2025', 5, NULL, '2025-09-09 01:47:40', 'College', NULL, '9630995906', NULL),
(125, 'MW-2505', 'GUZMAN', 'KATE', 'LILOC', NULL, 'kateguzman01@gmail.com', '2001-04-05', 'Female', '2025', 5, NULL, '2025-09-09 01:47:40', 'College', NULL, '9357752147', NULL),
(126, 'MW-2506', 'GUZMAN', 'CARLOS', 'LILOC', NULL, NULL, '2002-10-19', 'Male', '2025', 5, NULL, '2025-09-09 03:00:26', 'College', NULL, '09534873506', NULL),
(127, 'MW-2507', 'ECHANO', 'BARBE ANN', 'PAGRAN', NULL, 'echanobarbe227@gmail.com', '2004-02-07', 'Female', '2025', 5, NULL, '2025-09-09 03:00:26', 'College', NULL, '09193336544', NULL),
(128, 'MW-2508', 'VIRTUDAZO', 'APPLE MAE', 'CUSAY', NULL, 'virtudazoapplemae22@gmail.com', '2003-12-23', 'Female', '2025', 5, NULL, '2025-09-09 03:00:26', 'College', NULL, '09066657058', NULL),
(129, 'MW-2509', 'TAMPO', 'STELLA SHAHANIE', 'MADRIDANO', NULL, NULL, '1997-08-30', 'Female', '2025', 5, NULL, '2025-09-09 03:00:26', 'College', NULL, '09514979428', NULL),
(130, 'CC-25034', 'AMPO', 'MARY ANNE', 'QUIMSON', NULL, 'ampomary01@gmail.com', '2000-01-01', 'Female', '2025', 1, NULL, '2025-09-09 05:32:13', 'College', NULL, '09856122843', NULL);

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
  `academic_honor` varchar(50) DEFAULT NULL,
  `current_company` varchar(255) DEFAULT NULL,
  `current_position` varchar(255) DEFAULT NULL,
  `id_release_count` int(11) DEFAULT 0 COMMENT 'Number of times ID has been printed/released'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `alumnus_bio`
--

INSERT INTO `alumnus_bio` (`id`, `alumni_id`, `lastname`, `firstname`, `middlename`, `suffixname`, `birthdate`, `address`, `employment_status`, `employment_history`, `gender`, `batch`, `course_id`, `major_id`, `strand_id`, `connected_to`, `contact_no`, `company_address`, `company_email`, `email`, `password`, `img`, `date_created`, `avatar`, `status`, `last_login`, `academic_honor`, `current_company`, `current_position`, `id_release_count`) VALUES
(254, 'CC-2507', 'CHARLIE', 'ABEJO', 'ZAPICO', NULL, '2002-08-16', 'Waterfall', 'employed', '[{\"date_started\":\"2024-05-20\",\"duration\":\"1 year 4 months\"}]', 'Male', '2025', 6, NULL, NULL, 'EDUCATION', '09856122843', 'Waterfall', 'Waterfall@hotmail.com', 'capstonee2@gmail.com', '$2y$10$Nn7V4cqWElfb63DfKo6peuX/Pe4jfcOBUcd3ElJNcbeBCKUipf8zi', 'profile_254_1758560503.png', '2025-09-22 10:04:27', NULL, 1, NULL, NULL, NULL, NULL, 6);

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
(7, 68, 'JAMAICO', 'BARSOBIA', 'CABANDUCOS', 'Male', '0985612243', 'capstonee2@gmail.com', 2025, 6, 'Charlie', 'Waterfall ', 'Cagayan de oro Mis.or', 1, '2025-09-05 13:47:27', 23, NULL),
(20, 57, 'AXEL KING', 'P.', 'DAGHAY', 'Male', '09164907870', 'charlieeee@gmail.com', 2025, 6, 'Waterfall', 'Waterfall', 'Cagayan de oro Mis.or', 1, '2025-09-08 14:33:32', 23, NULL),
(21, 150, 'MARY ANNE', 'QUIMSON', 'AMPO', 'Female', '09856122843', 'capstonee2@gmail.com', 2025, NULL, '', '', '', 1, '2025-09-09 07:00:19', 23, 1),
(22, 66, 'CELESTE', 'SACEDON', 'PADUA', 'Female', '0985612241', 'paulinmgrayos@gmail.com', 2025, 6, 'Waterfallz', 'Waterfall', 'Cagayan de oro Mis.or', 1, '2025-09-09 11:00:53', 23, NULL),
(23, 54, 'Nicholas', 'John', 'Young', 'Male', '0985612243', 'gsADA2@gmail.com', 2016, 3, 'Waterfallz', 'Waterfall', 'Waterfall', 1, '2025-09-09 11:13:39', 23, NULL);

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
(2, 'MOIST', 'Santa Cruz Cogon Balingasag', 'IT Specialist', '&lt;p data-start=&quot;173&quot; data-end=&quot;587&quot;&gt;&lt;b data-start=&quot;173&quot; data-end=&quot;193&quot;&gt;Job Description:&lt;/b&gt;&lt;br data-start=&quot;193&quot; data-end=&quot;196&quot;&gt;We are seeking a skilled and dedicated IT Specialist to join our team at MOIST. The IT Specialist will be responsible for ensuring the smooth operation and maintenance of all company IT systems, providing support to employees, and troubleshooting technical issues. The ideal candidate will have expertise in network administration, hardware and software troubleshooting, and system security.&lt;/p&gt;&lt;p data-start=&quot;589&quot; data-end=&quot;614&quot;&gt;&lt;b data-start=&quot;589&quot; data-end=&quot;614&quot;&gt;Key Responsibilities:&lt;/b&gt;&lt;/p&gt;&lt;ul data-start=&quot;615&quot; data-end=&quot;1036&quot;&gt;&lt;li data-start=&quot;615&quot; data-end=&quot;685&quot;&gt;Install, configure, and maintain IT systems, software, and hardware.&lt;/li&gt;&lt;li data-start=&quot;686&quot; data-end=&quot;754&quot;&gt;Provide technical support and troubleshooting assistance to staff.&lt;/li&gt;&lt;li data-start=&quot;755&quot; data-end=&quot;825&quot;&gt;Monitor system performance and ensure all IT systems are up to date.&lt;/li&gt;&lt;li data-start=&quot;826&quot; data-end=&quot;879&quot;&gt;Manage network security and safeguard company data.&lt;/li&gt;&lt;li data-start=&quot;880&quot; data-end=&quot;964&quot;&gt;Assist with the setup and maintenance of internal communication tools and systems.&lt;/li&gt;&lt;li data-start=&quot;965&quot; data-end=&quot;1033&quot;&gt;Document and report on IT infrastructure, issues, and resolutions.&lt;/li&gt;&lt;/ul&gt;&lt;p data-start=&quot;1037&quot; data-end=&quot;1056&quot;&gt;&lt;b data-start=&quot;1037&quot; data-end=&quot;1056&quot;&gt;Qualifications:&lt;/b&gt;&lt;/p&gt;&lt;ul data-start=&quot;1057&quot; data-end=&quot;1398&quot;&gt;&lt;li data-start=&quot;1057&quot; data-end=&quot;1139&quot;&gt;Bachelor&rsquo;s degree in Information Technology, Computer Science, or related field.&lt;/li&gt;&lt;li data-start=&quot;1140&quot; data-end=&quot;1201&quot;&gt;Proven experience in IT support and network administration.&lt;/li&gt;&lt;li data-start=&quot;1202&quot; data-end=&quot;1282&quot;&gt;Strong knowledge of operating systems, hardware, and software troubleshooting.&lt;/li&gt;&lt;li data-start=&quot;1283&quot; data-end=&quot;1341&quot;&gt;Ability to work independently and in a team environment.&lt;/li&gt;&lt;li data-start=&quot;1342&quot; data-end=&quot;1398&quot;&gt;Strong problem-solving skills and attention to detail.&lt;/li&gt;&lt;/ul&gt;', 1, '2020-10-15 15:05:37'),
(3, 'MOISTDOMAIN', 'MOISTDOMAIN', 'MOISTDOMAIN', '&lt;span style=&quot;font-size: 14px;&quot;&gt;MOISTDOMAIN&lt;/span&gt;', 1, '2025-09-09 03:07:05'),
(4, 'Test Company', 'Remote', 'Test Developer Position', 'This is a test job posting to verify the system is working correctly.', 1, '2025-09-09 10:56:54');

-- --------------------------------------------------------

--
-- Table structure for table `conversation_threads`
--

CREATE TABLE `conversation_threads` (
  `id` int(11) NOT NULL,
  `thread_subject` varchar(500) NOT NULL,
  `started_by_id` int(11) NOT NULL,
  `started_by_type` enum('registrar','alumni') NOT NULL,
  `last_message_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `message_count` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(10, 'CTHM', 'CTHM'),
(13, 'BS HOSPITALITY MANAGEMENT', 'BS HOSPITALITY MANAGEMENT');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` int(11) NOT NULL,
  `template_name` varchar(255) NOT NULL,
  `template_subject` varchar(500) NOT NULL,
  `template_body` longtext NOT NULL,
  `category` varchar(100) DEFAULT 'general',
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `template_name`, `template_subject`, `template_body`, `category`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Event Invitation', 'You\'re Invited: MOIST Event', '<p>Dear {{firstname}} {{lastname}},</p>\n<p>We are pleased to invite you to <strong>{{event_name}}</strong>.</p>\n<p><strong>Event Details:</strong></p>\n<ul>\n<li><strong>Date:</strong> {{event_date}}</li>\n<li><strong>Time:</strong> {{event_time}}</li>\n<li><strong>Venue:</strong> {{event_venue}}</li>\n</ul>\n<p>We look forward to seeing you there!</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 'events', 1, 1, '2025-11-07 12:47:22', '2025-11-07 12:59:45'),
(2, 'General Announcement', 'Important Announcement from MOIST', '<p>Dear {{firstname}} {{lastname}},</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 'announcements', 1, 1, '2025-11-07 12:47:22', '2025-11-07 12:59:45'),
(3, 'Survey Request', 'MOIST Alumni Tracer Study - We Need Your Input', '<p>Dear {{firstname}} {{lastname}},</p>\n<p>As a valued alumnus of MOIST, we would appreciate your participation in our alumni survey.</p>\n<p>Your feedback helps us improve our programs and services.</p>\n<p><strong>Survey Link:</strong> {{survey_link}}</p>\n<p>Thank you for your time!</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 'surveys', 1, 1, '2025-11-07 12:47:22', '2025-11-07 12:59:45'),
(4, 'Job Opportunity', 'Job Opportunity: {{job_title}}', '<p>Dear {{firstname}} {{lastname}},</p>\n<p>We wanted to share an exciting job opportunity that might interest you:</p>\n<p><strong>Position:</strong> {{job_title}}<br>\n<strong>Company:</strong> {{company_name}}<br>\n<strong>Location:</strong> {{job_location}}</p>\n<p>{{job_description}}</p>\n<p>For more information, please contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 'opportunities', 1, 1, '2025-11-07 12:47:22', '2025-11-07 12:47:22'),
(7, 'Alumni Homecoming Invitation', 'MOIST Alumni Homecoming {{event_date}}', 'Dear {{name}},\n\nGreetings from MOIST Alumni Office!\n\nWe are thrilled to invite you to our Annual Alumni Homecoming celebration!\n\n🎉 Event Details:\nDate: {{event_date}}\nTime: {{event_time}}\nVenue: MOIST Campus Grounds\nBatch: {{batch}} and All Alumni Welcome!\n\n📋 Program Highlights:\n• Registration and Fellowship\n• Campus Tour and Reminiscing\n• Alumni Recognition Awards\n• Entertainment and Games\n• Dinner and Socials\n\nAs a valued member of the {{course}} program, your presence would make this celebration even more special.\n\nPlease confirm your attendance by replying to this email or contacting us at the Alumni Office.\n\nWe look forward to reconnecting with you!\n\nBest regards,\nMOIST Alumni Office\nMisamis Oriental Institute of Science and Technology', 'events', 1, 1, '2025-11-07 12:59:45', '2025-11-07 12:59:45'),
(9, 'Job Opportunity Announcement', 'Job Opportunity for MOIST Alumni', 'Dear {{name}},\n\nGreetings from MOIST Alumni Office!\n\nWe are pleased to share an exciting job opportunity that may be of interest to you and fellow alumni.\n\n💼 Position Details:\nJob Title: [Insert Position]\nCompany: [Insert Company Name]\nLocation: [Insert Location]\nEmployment Type: [Full-time/Part-time/Contract]\nQualifications: [Insert Key Requirements]\n\n📝 Job Description:\n[Insert brief description of responsibilities and requirements]\n\nPreferred Course: {{course}} or related fields\nExperience Level: [Entry/Mid/Senior Level]\n\nHow to Apply:\nInterested applicants may send their resume and application letter to: [Insert Email/Link]\nDeadline: [Insert Deadline]\n\nFor more information, please contact:\n[Insert Contact Person and Details]\n\nWe encourage qualified alumni to apply and share this opportunity with others who might be interested.\n\nBest of luck!\n\nMOIST Alumni Office\nCareer Services and Placement Office\nMisamis Oriental Institute of Science and Technology', 'opportunities', 1, 1, '2025-11-07 12:59:45', '2025-11-07 12:59:45'),
(10, 'Seminar/Workshop Invitation', 'Invitation: Professional Development Seminar', 'Dear {{name}},\n\nGreetings from MOIST!\n\nWe are excited to invite you to an upcoming professional development seminar designed specifically for our alumni.\n\n📚 Seminar Details:\nTitle: [Insert Seminar Title]\nDate: {{event_date}}\nTime: {{event_time}}\nVenue: [Insert Venue/Online Platform]\nSpeaker: [Insert Speaker Name and Credentials]\n\n🎯 Topics to be Covered:\n• [Topic 1]\n• [Topic 2]\n• [Topic 3]\n\nThis seminar is FREE for all MOIST alumni and will provide:\n✓ Certificate of Participation\n✓ Networking opportunities\n✓ Continuing Professional Development (CPD) units (if applicable)\n✓ Learning materials\n\nRegistration:\nPlease confirm your attendance by [Insert Deadline] through: [Insert Registration Link/Email]\nLimited slots available!\n\nFor inquiries, contact the Alumni Office at [Insert Contact Details].\n\nWe look forward to your participation!\n\nBest regards,\nMOIST Alumni Office\nProfessional Development Committee\nMisamis Oriental Institute of Science and Technology', 'events', 1, 1, '2025-11-07 12:59:45', '2025-11-07 12:59:45'),
(11, 'Donation/Fundraising Appeal', 'Support MOIST: Alumni Giving Campaign', 'Dear {{name}},\n\nWarm greetings from your Alma Mater, MOIST!\n\nAs a proud graduate of {{course}} (Batch {{batch}}), you are part of the MOIST legacy that continues to shape future generations.\n\n🎓 Alumni Giving Campaign\nWe are reaching out to invite you to support our institution through the MOIST Alumni Giving Campaign.\n\n💡 Your donation will help:\n• Provide scholarships for deserving students\n• Upgrade facilities and laboratories\n• Support faculty development programs\n• Enhance student services and activities\n\nEvery contribution, big or small, makes a difference in the lives of our students.\n\n💳 Ways to Donate:\nBank Transfer: [Insert Bank Details]\nGCash/PayMaya: [Insert Number]\nDirect Donation: Visit the Alumni Office\n\nFor donations of ₱1,000 and above, you will receive:\n✓ Official receipt for tax purposes\n✓ Recognition in our Donor Wall\n✓ Alumni newsletter subscription\n\nYour generosity today will create opportunities for tomorrow\'s leaders.\n\nFor more information, please contact the Alumni Office at [Insert Contact Details].\n\nThank you for your continued support!\n\nWith gratitude,\nMOIST Alumni Office\nDevelopment and Fundraising Committee\nMisamis Oriental Institute of Science and Technology', 'general', 1, 1, '2025-11-07 12:59:45', '2025-11-07 12:59:45'),
(12, 'Account Validation Reminder', 'Action Required: Validate Your MOIST Alumni Account', 'Dear {{name}},\n\nGreetings from MOIST Alumni Office!\n\nWe noticed that your alumni account requires validation to ensure you receive all updates and benefits.\n\n👤 Your Account Information:\nName: {{firstname}} {{lastname}}\nEmail: {{email}}\nCourse: {{course}}\nBatch: {{batch}}\n\n✅ Please validate your account to:\n• Receive important announcements and invitations\n• Access alumni portal and services\n• Participate in alumni events and activities\n• Connect with fellow alumni\n• Receive job opportunities and career updates\n\n🔗 How to Validate:\n1. Click the validation link: [Insert Link]\n2. Update your profile information\n3. Verify your email address\n\nThis will only take 2-3 minutes of your time.\n\nIf you have already validated your account, please disregard this message.\n\nFor assistance, contact the Alumni Office at [Insert Contact Details].\n\nThank you for staying connected with MOIST!\n\nBest regards,\nMOIST Alumni Office\nDatabase Management Team\nMisamis Oriental Institute of Science and Technology', 'general', 1, 1, '2025-11-07 12:59:45', '2025-11-07 12:59:45');

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
  `user_id` int(30) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `title`, `content`, `schedule`, `banner`, `user_id`, `date_created`, `approved`) VALUES
(15, 'ALUMNI HOME COMING', '&lt;p class=&quot;xdj266r x14z9mp xat24cr x1lziwak x1vvkbs x126k92a&quot; style=&quot;margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Calling all proud alumni of MISAMIS ORIENTAL INSTITUTE OF SCIENCE AND TECHNOLOGY (MOIST), INC.!&lt;/p&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;It&amp;#x2019;s time to come home! &lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Join us for the 5th Alumni Homecoming on May 31, 2025, at MOIST, Inc., Sta. Cruz, Cogon, Balingasag.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Let&rsquo;s Reunite, Relive, Reignite the memories that made us who we are.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;&lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistianalumni?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTianAlumni&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/5thhomecoming?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#5thHomecoming&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/reuniterelivereignite?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#ReuniteReliveReignite&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistfamily?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTFamily&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/onceamoistianalwaysamoistian?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#OnceAMOISTianAlwaysAMOISTian&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;&lt;/p&gt;', '2025-09-08 21:00:00', '1757333280_7_event.jpg', 1, '2025-09-08 20:08:45', 1),
(16, 'ALUMNI', '&lt;p class=&quot;xdj266r x14z9mp xat24cr x1lziwak x1vvkbs x126k92a&quot; style=&quot;margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Calling all proud alumni of MISAMIS ORIENTAL INSTITUTE OF SCIENCE AND TECHNOLOGY (MOIST), INC.!&lt;/p&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;It&amp;#x2019;s time to come home! &lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Join us for the 5th Alumni Homecoming on May 31, 2025, at MOIST, Inc., Sta. Cruz, Cogon, Balingasag.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Let&rsquo;s Reunite, Relive, Reignite the memories that made us who we are.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;&lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistianalumni?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTianAlumni&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/5thhomecoming?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#5thHomecoming&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/reuniterelivereignite?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#ReuniteReliveReignite&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistfamily?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTFamily&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/onceamoistianalwaysamoistian?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#OnceAMOISTianAlwaysAMOISTian&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;&lt;/p&gt;', '2025-09-17 20:09:00', '1757333340_2_event.jpg', 1, '2025-09-08 20:09:27', 1),
(17, 'ALUMNI NEW', '&lt;p class=&quot;xdj266r x14z9mp xat24cr x1lziwak x1vvkbs x126k92a&quot; style=&quot;margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Calling all proud alumni of MISAMIS ORIENTAL INSTITUTE OF SCIENCE AND TECHNOLOGY (MOIST), INC.!&lt;/p&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;It&amp;#x2019;s time to come home! &lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Join us for the 5th Alumni Homecoming on May 31, 2025, at MOIST, Inc., Sta. Cruz, Cogon, Balingasag.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Let&rsquo;s Reunite, Relive, Reignite the memories that made us who we are.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;&lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistianalumni?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTianAlumni&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/5thhomecoming?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#5thHomecoming&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/reuniterelivereignite?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#ReuniteReliveReignite&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistfamily?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTFamily&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/onceamoistianalwaysamoistian?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#OnceAMOISTianAlwaysAMOISTian&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;&lt;/p&gt;', '2025-09-12 20:09:00', '1757333400_3_event.jpg', 1, '2025-09-08 20:10:30', 1),
(18, 'Post by  ', 'KP', '2025-09-09 09:20:14', '', NULL, '2025-09-09 09:20:14', 0),
(19, 'Post by  ', 'HUHU', '2025-09-09 09:22:48', '', NULL, '2025-09-09 09:22:48', 0);

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
(8, 6, 27, '2025-08-21 03:36:19'),
(9, 9, 68, '2025-09-05 09:31:54'),
(10, 7, 68, '2025-09-05 09:32:54'),
(11, 7, 69, '2025-09-05 20:39:03'),
(12, 16, 254, '2025-09-27 01:26:42');

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
(3, 6, 27, 'Apila ko', '2025-08-18 07:13:42'),
(6, 7, 69, 'Apil ko', '2025-09-05 20:39:08'),
(7, 9, 69, 'HAHAHAA', '2025-09-06 13:27:16'),
(8, 17, 157, 'HOHO', '2025-09-09 08:33:20');

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
(4, 6, 27, '2025-08-18 04:57:00'),
(5, 7, 66, '2025-09-02 13:08:38'),
(6, 8, 66, '2025-09-02 13:17:25'),
(7, 9, 66, '2025-09-02 13:17:28'),
(8, 7, 68, '2025-09-03 22:37:01'),
(10, 10, 68, '2025-09-04 03:44:41'),
(11, 9, 68, '2025-09-05 09:31:56'),
(12, 7, 69, '2025-09-05 20:39:02'),
(13, 9, 69, '2025-09-06 13:27:09'),
(14, 17, 157, '2025-09-09 08:33:27'),
(15, 16, 157, '2025-09-09 08:33:29'),
(16, 15, 157, '2025-09-09 08:33:30'),
(17, 17, 158, '2025-09-09 09:09:15');

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
-- Table structure for table `homecoming_rsvp`
--

CREATE TABLE `homecoming_rsvp` (
  `id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `response` enum('attending','not_attending') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `homecoming_rsvp`
--

INSERT INTO `homecoming_rsvp` (`id`, `alumni_id`, `token`, `response`, `created_at`, `responded_at`, `updated_at`) VALUES
(2, 254, '3fec36a9394979a4249fcbb85fe8941648c0e8d7da0f33b9987810305de82790', 'attending', '2025-09-26 12:01:58', '2025-11-03 08:29:49', '2025-11-03 08:29:49');

-- --------------------------------------------------------

--
-- Table structure for table `id_release_log`
--

CREATE TABLE `id_release_log` (
  `id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `release_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `released_by` varchar(100) DEFAULT NULL,
  `release_method` enum('print_button','ctrl_p','manual') DEFAULT 'print_button',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `id_release_log`
--

INSERT INTO `id_release_log` (`id`, `alumni_id`, `release_date`, `released_by`, `release_method`, `ip_address`, `user_agent`) VALUES
(1, 254, '2025-09-26 12:00:45', 'test', 'print_button', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(2, 254, '2025-09-26 13:10:06', 'test', 'print_button', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36'),
(3, 254, '2025-10-24 14:21:46', 'test', 'ctrl_p', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(4, 254, '2025-10-24 14:29:28', 'test', 'print_button', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(5, 254, '2025-10-25 00:34:17', 'test', 'ctrl_p', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36'),
(6, 254, '2025-10-25 00:34:23', 'test', 'ctrl_p', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36');

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
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_type` enum('registrar','alumni') NOT NULL,
  `recipient_id` int(11) DEFAULT NULL,
  `recipient_type` enum('registrar','alumni','multiple') DEFAULT NULL,
  `subject` varchar(500) NOT NULL,
  `message_body` longtext NOT NULL,
  `template_id` int(11) DEFAULT NULL,
  `parent_message_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_starred` tinyint(1) DEFAULT 0,
  `is_archived` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `sender_type`, `recipient_id`, `recipient_type`, `subject`, `message_body`, `template_id`, `parent_message_id`, `is_read`, `is_starred`, `is_archived`, `is_deleted`, `sent_at`, `read_at`) VALUES
(1, 23, 'registrar', 254, 'alumni', 'EVENTS', 'EVENTS', 0, NULL, 0, 0, 0, 0, '2025-11-07 13:01:16', NULL),
(2, 23, 'registrar', 254, 'alumni', 'EVENTS', 'CHARLIE', 0, NULL, 0, 0, 0, 0, '2025-11-07 13:20:01', NULL),
(3, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 13:53:05', NULL),
(4, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 13:59:18', NULL),
(5, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 14:05:01', NULL),
(7, 23, 'registrar', 254, 'alumni', 'Invitation: Professional Development Seminar', 'Dear ABEJO CHARLIE,\n\nGreetings from MOIST!\n\nWe are excited to invite you to an upcoming professional development seminar designed specifically for our alumni.\n\n📚 Seminar Details:\nTitle: [Insert Seminar Title]\nDate: {{event_date}}\nTime: {{event_time}}\nVenue: [Insert Venue/Online Platform]\nSpeaker: [Insert Speaker Name and Credentials]\n\n🎯 Topics to be Covered:\n• [Topic 1]\n• [Topic 2]\n• [Topic 3]\n\nThis seminar is FREE for all MOIST alumni and will provide:\n✓ Certificate of Participation\n✓ Networking opportunities\n✓ Continuing Professional Development (CPD) units (if applicable)\n✓ Learning materials\n\nRegistration:\nPlease confirm your attendance by [Insert Deadline] through: [Insert Registration Link/Email]\nLimited slots available!\n\nFor inquiries, contact the Alumni Office at [Insert Contact Details].\n\nWe look forward to your participation!\n\nBest regards,\nMOIST Alumni Office\nProfessional Development Committee\nMisamis Oriental Institute of Science and Technology', 0, NULL, 0, 0, 0, 0, '2025-11-07 14:20:29', NULL),
(8, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 14:40:30', NULL),
(9, 23, 'registrar', 8, 'alumni', 'Re: Important Announcement from MOIST', 'WHY', NULL, NULL, 0, 0, 0, 0, '2025-11-07 14:41:19', NULL),
(10, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 14:42:33', NULL),
(11, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 14:43:28', NULL),
(12, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 14:45:51', NULL),
(13, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 14:47:10', NULL),
(14, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 14:52:12', NULL),
(16, 23, 'registrar', 254, 'alumni', 'Important Announcement from MOIST', '<p>Dear ABEJO CHARLIE,</p>\n<p>We would like to inform you about the following:</p>\n<p>{{announcement_content}}</p>\n<p>If you have any questions, please don\'t hesitate to contact us.</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-07 15:01:59', NULL),
(17, 23, 'registrar', 254, 'alumni', 'Action Required: Validate Your MOIST Alumni Account', 'Dear ABEJO CHARLIE,\n\nGreetings from MOIST Alumni Office!\n\nWe noticed that your alumni account requires validation to ensure you receive all updates and benefits.\n\n👤 Your Account Information:\nName: ABEJO CHARLIE\nEmail: capstonee2@gmail.com\nCourse: CRIMINOLOGY\nBatch: 2025\n\n✅ Please validate your account to:\n• Receive important announcements and invitations\n• Access alumni portal and services\n• Participate in alumni events and activities\n• Connect with fellow alumni\n• Receive job opportunities and career updates\n\n🔗 How to Validate:\n1. Click the validation link: [Insert Link]\n2. Update your profile information\n3. Verify your email address\n\nThis will only take 2-3 minutes of your time.\n\nIf you have already validated your account, please disregard this message.\n\nFor assistance, contact the Alumni Office at [Insert Contact Details].\n\nThank you for staying connected with MOIST!\n\nBest regards,\nMOIST Alumni Office\nDatabase Management Team\nMisamis Oriental Institute of Science and Technology', 0, NULL, 0, 0, 0, 0, '2025-11-07 15:08:58', NULL),
(18, 23, 'registrar', 17, 'alumni', 'Re: Action Required: Validate Your MOIST Alumni Account', 'TY', NULL, NULL, 0, 0, 0, 0, '2025-11-07 15:17:11', NULL),
(19, 23, 'registrar', 254, 'alumni', 'You\'re Invited: MOIST Event', '<p>Dear ABEJO CHARLIE,</p>\n<p>We are pleased to invite you to <strong>{{event_name}}</strong>.</p>\n<p><strong>Event Details:</strong></p>\n<ul>\n<li><strong>Date:</strong> {{event_date}}</li>\n<li><strong>Time:</strong> {{event_time}}</li>\n<li><strong>Venue:</strong> {{event_venue}}</li>\n</ul>\n<p>We look forward to seeing you there!</p>\n<p>Best regards,<br>MOIST Alumni Office</p>', 0, NULL, 0, 0, 0, 0, '2025-11-08 00:51:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `message_attachments`
--

CREATE TABLE `message_attachments` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_type` varchar(100) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message_recipients`
--

CREATE TABLE `message_recipients` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `recipient_type` enum('alumni') NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `rsvp_status` enum('pending','accept','decline','maybe') DEFAULT 'pending',
  `rsvp_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `message_recipients`
--

INSERT INTO `message_recipients` (`id`, `message_id`, `recipient_id`, `recipient_type`, `recipient_email`, `is_read`, `read_at`, `rsvp_status`, `rsvp_at`, `created_at`) VALUES
(1, 1, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 13:01:16'),
(2, 2, 254, 'alumni', 'capstonee2@gmail.com', 1, '2025-11-07 14:37:22', 'accept', '2025-11-07 22:37:22', '2025-11-07 13:20:01'),
(3, 3, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 13:53:05'),
(4, 4, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 13:59:18'),
(5, 5, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 14:05:01'),
(7, 7, 254, 'alumni', 'capstonee2@gmail.com', 1, '2025-11-07 14:26:54', 'accept', '2025-11-07 22:26:54', '2025-11-07 14:20:29'),
(8, 8, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 14:40:30'),
(9, 9, 8, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 14:41:19'),
(10, 10, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 14:42:33'),
(11, 11, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 14:43:28'),
(12, 12, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 14:45:51'),
(13, 13, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 14:47:10'),
(14, 14, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 14:52:12'),
(16, 16, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'accept', '2025-11-07 23:02:30', '2025-11-07 15:01:59'),
(17, 17, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'accept', '2025-11-07 23:09:11', '2025-11-07 15:08:58'),
(18, 18, 17, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'pending', NULL, '2025-11-07 15:17:11'),
(19, 19, 254, 'alumni', 'capstonee2@gmail.com', 0, NULL, 'accept', '2025-11-08 08:51:54', '2025-11-08 00:51:08');

-- --------------------------------------------------------

--
-- Table structure for table `message_replies`
--

CREATE TABLE `message_replies` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `reply_content` text NOT NULL,
  `replied_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `message_replies`
--

INSERT INTO `message_replies` (`id`, `message_id`, `recipient_id`, `reply_content`, `replied_at`, `is_read`, `read_at`) VALUES
(1, 7, 254, 'GG', '2025-11-07 22:27:20', 0, NULL),
(2, 7, 254, 'GG', '2025-11-07 22:27:26', 0, NULL),
(3, 8, 254, 'YES', '2025-11-07 22:40:49', 0, NULL),
(4, 16, 254, 'GOOD JOB', '2025-11-07 23:02:30', 0, NULL),
(5, 17, 254, 'G', '2025-11-07 23:09:11', 0, NULL),
(6, 19, 254, 'Naay bayad ani?', '2025-11-08 08:51:54', 0, NULL);

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
(1, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(2, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(3, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(4, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(5, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(6, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(7, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(8, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(9, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(10, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(11, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(12, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(13, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(14, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(15, 'tineemaeeyy@gmail.com', NULL, '982020', '2025-08-17 21:57:49', 1, '2025-08-17 13:52:49'),
(16, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(17, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(18, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(19, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(20, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(21, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(22, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(23, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(24, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(25, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(26, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(27, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(28, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(29, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(30, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(31, 'tineemaeeyy@gmail.com', NULL, '313477', '2025-08-19 01:03:35', 1, '2025-08-18 16:58:35'),
(32, 'tineemaeeyy@gmail.com', NULL, '974774', '2025-08-19 01:03:39', 1, '2025-08-18 16:58:39'),
(33, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(34, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(35, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(36, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(37, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(38, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(39, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(40, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(41, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(42, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(43, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(44, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(45, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(46, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(47, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(48, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(49, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(50, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(51, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(52, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(53, 'marialanievalmoria009@gmail.com', NULL, '841195', '2025-08-29 02:55:03', 1, '2025-08-28 18:50:03'),
(54, 'marialanievalmoria009@gmail.com', NULL, '143918', '2025-08-29 03:31:34', 1, '2025-08-28 19:26:34'),
(55, 'marialanievalmoria009@gmail.com', NULL, '908867', '2025-08-29 04:02:50', 1, '2025-08-28 19:57:50'),
(56, 'marialanievalmoria009@gmail.com', NULL, '223502', '2025-08-29 04:07:27', 1, '2025-08-28 20:02:27'),
(57, 'marialanievalmoria009@gmail.com', NULL, '556848', '2025-08-29 04:23:02', 1, '2025-08-28 20:18:02'),
(58, 'marialanievalmoria009@gmail.com', NULL, '116778', '2025-08-29 04:31:44', 1, '2025-08-28 20:26:44'),
(59, 'marialanievalmoria009@gmail.com', NULL, '153321', '2025-08-29 04:35:57', 1, '2025-08-28 20:30:57'),
(60, 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', 1, '2025-09-02 01:47:14'),
(62, '09856122843', NULL, '748532', '2025-09-02 03:39:13', 0, '2025-09-02 01:34:13'),
(63, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '492697', '2025-09-02 10:01:38', 1, '2025-09-02 01:56:38'),
(64, '09856122843', '09856122843', '891972', '2025-09-02 10:04:41', 0, '2025-09-02 01:59:41'),
(65, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '647804', '2025-09-02 10:05:40', 1, '2025-09-02 02:00:40'),
(66, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '832023', '2025-09-02 10:18:48', 1, '2025-09-02 02:13:48'),
(67, '09856122843', '09856122843', '237272', '2025-09-02 10:20:05', 1, '2025-09-02 02:15:05'),
(68, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '403426', '2025-09-02 10:21:03', 1, '2025-09-02 02:16:03'),
(69, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '859340', '2025-09-02 10:28:57', 1, '2025-09-02 02:23:57'),
(70, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '455531', '2025-09-02 10:34:11', 1, '2025-09-02 02:29:11'),
(71, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '494168', '2025-09-02 10:39:23', 1, '2025-09-02 02:34:23'),
(72, 'paulinmgrayos@gmail.com', 'paulinmgrayos@gmail.', '813598', '2025-09-02 10:48:04', 1, '2025-09-02 02:43:04'),
(73, 'paulinmgrayos@gmail.com', 'paulinmgrayos@gmail.', '470019', '2025-09-02 10:48:47', 1, '2025-09-02 02:43:47'),
(74, 'paulinmgrayos@gmail.com', 'paulinmgrayos@gmail.', '880596', '2025-09-02 10:49:32', 1, '2025-09-02 02:44:32'),
(75, 'paulinmgrayos@gmail.com', 'paulinmgrayos@gmail.', '542650', '2025-09-02 10:57:12', 1, '2025-09-02 02:52:12'),
(76, '09856122843', '09856122843', '270795', '2025-09-02 10:57:56', 1, '2025-09-02 02:52:56'),
(77, '09856122843', '09856122843', '831178', '2025-09-02 10:58:31', 1, '2025-09-02 02:53:31'),
(78, 'paulinmgrayos@gmail.com', NULL, '130964', '2025-09-02 12:50:19', 1, '2025-09-02 04:45:19'),
(79, 'paulinmgrayos@gmail.com', NULL, '199307', '2025-09-02 13:21:07', 1, '2025-09-02 05:16:07'),
(80, 'paulinmgrayos@gmail.com', NULL, '431467', '2025-09-02 13:33:06', 1, '2025-09-02 05:28:06'),
(81, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '239821', '2025-09-02 19:02:23', 1, '2025-09-02 10:57:23'),
(82, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '628745', '2025-09-02 19:03:26', 1, '2025-09-02 10:58:26'),
(83, '09856122843', '09856122843', '223221', '2025-09-02 19:04:14', 1, '2025-09-02 10:59:14'),
(84, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '787574', '2025-09-02 19:09:10', 1, '2025-09-02 11:04:10'),
(85, 'capstonee2@gmail.com', NULL, '789028', '2025-09-02 19:12:18', 1, '2025-09-02 11:07:18'),
(86, '09856122843', '09856122843', '678235', '2025-09-03 21:04:25', 1, '2025-09-03 12:59:25'),
(87, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '918653', '2025-09-03 21:05:03', 1, '2025-09-03 13:00:03'),
(88, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '780185', '2025-09-03 22:34:29', 1, '2025-09-03 14:29:29'),
(89, '09856122843', '09856122843', '201090', '2025-09-03 22:35:24', 0, '2025-09-03 14:30:24'),
(90, '09856122843', '09856122843', '543729', '2025-09-03 22:36:31', 1, '2025-09-03 14:31:31'),
(91, 'capstonee2@gmail.com', NULL, '806294', '2025-09-03 22:40:50', 1, '2025-09-03 14:35:50'),
(92, 'capstonee2@gmail.com', NULL, '460301', '2025-09-03 22:50:29', 1, '2025-09-03 14:45:29'),
(93, 'capstonee2@gmail.com', NULL, '158577', '2025-09-03 22:56:15', 1, '2025-09-03 14:51:15'),
(94, 'capstonee2@gmail.com', NULL, '495275', '2025-09-03 23:02:12', 1, '2025-09-03 14:57:12'),
(95, 'capstonee2@gmail.com', NULL, '159038', '2025-09-03 23:05:59', 1, '2025-09-03 15:00:59'),
(96, 'capstonee2@gmail.com', NULL, '608897', '2025-09-03 23:08:29', 1, '2025-09-03 15:03:29'),
(97, 'capstonee2@gmail.com', NULL, '715718', '2025-09-03 23:17:41', 1, '2025-09-03 15:12:41'),
(98, 'capstonee2@gmail.com', NULL, '861205', '2025-09-03 23:36:18', 1, '2025-09-03 15:31:18'),
(99, 'capstonee2@gmail.com', NULL, '253163', '2025-09-04 00:15:15', 1, '2025-09-03 16:10:15'),
(100, 'capstonee2@gmail.com', NULL, '586743', '2025-09-04 00:35:02', 1, '2025-09-03 16:30:02'),
(101, 'capstonee2@gmail.com', NULL, '484415', '2025-09-04 00:56:18', 1, '2025-09-03 16:51:18'),
(102, 'capstonee2@gmail.com', NULL, '967743', '2025-09-04 01:04:16', 1, '2025-09-03 16:59:16'),
(103, 'capstonee2@gmail.com', NULL, '824779', '2025-09-04 01:14:40', 1, '2025-09-03 17:09:40'),
(104, 'capstonee2@gmail.com', NULL, '292951', '2025-09-04 01:31:27', 1, '2025-09-03 17:26:27'),
(105, 'capstonee2@gmail.com', NULL, '626196', '2025-09-04 02:44:06', 1, '2025-09-03 18:39:06'),
(106, 'capstonee2@gmail.com', NULL, '947619', '2025-09-04 02:56:12', 1, '2025-09-03 18:51:12'),
(107, 'capstonee2@gmail.com', NULL, '266801', '2025-09-04 03:11:20', 1, '2025-09-03 19:06:20'),
(108, 'capstonee2@gmail.com', NULL, '305219', '2025-09-04 03:38:48', 1, '2025-09-03 19:33:48'),
(109, 'capstonee2@gmail.com', NULL, '899146', '2025-09-04 13:40:07', 1, '2025-09-04 05:35:07'),
(110, 'capstonee2@gmail.com', NULL, '860708', '2025-09-04 20:45:57', 1, '2025-09-04 12:40:57'),
(111, 'capstonee2@gmail.com', NULL, '518973', '2025-09-04 20:46:57', 1, '2025-09-04 12:41:57'),
(112, 'capstonee2@gmail.com', NULL, '591019', '2025-09-04 22:16:08', 1, '2025-09-04 14:11:08'),
(113, 'capstonee2@gmail.com', NULL, '484265', '2025-09-05 09:33:46', 1, '2025-09-05 01:28:46'),
(114, '09856122843', '09856122843', '389409', '2025-09-05 13:56:24', 0, '2025-09-05 05:51:24'),
(115, '09856122843', '09856122843', '770436', '2025-09-05 13:57:47', 0, '2025-09-05 05:52:47'),
(116, '09856122843', '09856122843', '140047', '2025-09-05 13:59:11', 0, '2025-09-05 05:54:11'),
(117, '09856122843', '09856122843', '990139', '2025-09-05 14:00:12', 0, '2025-09-05 05:55:12'),
(118, '09856122843', '09856122843', '511082', '2025-09-05 14:01:44', 0, '2025-09-05 05:56:44'),
(119, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '945754', '2025-09-05 18:49:20', 1, '2025-09-05 10:44:20'),
(120, '09856122843', '09856122843', '139800', '2025-09-05 18:52:49', 0, '2025-09-05 10:47:49'),
(121, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '969726', '2025-09-05 19:48:17', 1, '2025-09-05 11:43:17'),
(122, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '189658', '2025-09-05 20:12:51', 1, '2025-09-05 12:07:51'),
(123, 'capstonee2@gmail.com', NULL, '804396', '2025-09-05 20:17:49', 1, '2025-09-05 12:12:49'),
(124, 'capstonee2@gmail.com', NULL, '943906', '2025-09-05 20:43:05', 1, '2025-09-05 12:38:05'),
(125, '09856122843', '09856122843', '568314', '2025-09-06 13:16:24', 0, '2025-09-06 05:11:24'),
(126, '09856122843', '09856122843', '543435', '2025-09-06 13:17:39', 0, '2025-09-06 05:12:39'),
(127, '09856122843', '09856122843', '219831', '2025-09-06 13:19:13', 0, '2025-09-06 05:14:13'),
(128, 'capstonee2@gmail.com', NULL, '675102', '2025-09-06 13:29:56', 1, '2025-09-06 05:24:56'),
(129, 'capstonee2@gmail.com', NULL, '652367', '2025-09-06 13:31:27', 1, '2025-09-06 05:26:27'),
(130, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '460242', '2025-09-06 13:38:03', 1, '2025-09-06 05:33:03'),
(131, 'moseslozano20@gmail.com', 'moseslozano20@gmail.', '591725', '2025-09-06 13:48:17', 1, '2025-09-06 05:43:17'),
(132, 'moseslozano20@gmail.com', NULL, '519275', '2025-09-06 13:53:30', 1, '2025-09-06 05:48:30'),
(133, 'moseslozano20@gmail.com', NULL, '152112', '2025-09-06 13:54:31', 0, '2025-09-06 05:49:31'),
(134, 'capstonee2@gmail.com', NULL, '976563', '2025-09-06 13:55:34', 1, '2025-09-06 05:50:34'),
(135, 'bkimleye@gmail.com', 'bkimleye@gmail.com', '374367', '2025-09-06 14:12:40', 0, '2025-09-06 06:07:40'),
(136, 'bkimleye@gmail.com', 'bkimleye@gmail.com', '487833', '2025-09-06 14:13:54', 1, '2025-09-06 06:08:54'),
(137, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '630265', '2025-09-09 07:15:51', 1, '2025-09-08 23:10:51'),
(138, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '698179', '2025-09-09 07:17:25', 1, '2025-09-08 23:12:25'),
(139, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '814426', '2025-09-09 07:22:08', 1, '2025-09-08 23:17:08'),
(140, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '867732', '2025-09-09 07:27:22', 1, '2025-09-08 23:22:22'),
(141, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '551532', '2025-09-09 07:41:49', 1, '2025-09-08 23:36:49'),
(142, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '466585', '2025-09-09 07:50:27', 1, '2025-09-08 23:45:27'),
(143, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '472634', '2025-09-09 08:01:14', 1, '2025-09-08 23:56:14'),
(144, 'capstonee2@gmail.com', NULL, '107227', '2025-09-09 08:19:52', 1, '2025-09-09 00:14:52'),
(145, 'capstonee2@gmail.com', NULL, '326770', '2025-09-09 09:03:34', 1, '2025-09-09 00:58:34'),
(146, 'capstonee2@gmail.com', NULL, '715427', '2025-09-09 09:07:32', 1, '2025-09-09 01:02:32'),
(147, '09677314559', '09677314559', '441907', '2025-09-09 09:08:58', 0, '2025-09-09 01:03:58'),
(148, '09677314559', '09677314559', '273717', '2025-09-09 09:10:05', 1, '2025-09-09 01:05:05'),
(149, 'kennethladra3@gmail.com', NULL, '240065', '2025-09-09 09:13:35', 1, '2025-09-09 01:08:35'),
(150, 'capstonee2@gmail.com', NULL, '664399', '2025-09-09 10:27:07', 1, '2025-09-09 02:22:07'),
(151, 'capstonee2@gmail.com', NULL, '983683', '2025-09-09 10:57:06', 1, '2025-09-09 02:52:06'),
(152, 'capstonee2@gmail.com', NULL, '250772', '2025-09-09 11:02:36', 1, '2025-09-09 02:57:36'),
(153, '09856122843', '09856122843', '446047', '2025-09-09 13:45:35', 0, '2025-09-09 05:40:35'),
(154, 'capstonee2@gmail.com', NULL, '159823', '2025-09-09 14:43:00', 1, '2025-09-09 06:38:00'),
(155, '09856122843', '09856122843', '810064', '2025-09-09 14:47:34', 1, '2025-09-09 06:42:34'),
(156, 'ampomary01@gmail.com', NULL, '750189', '2025-09-09 14:55:09', 1, '2025-09-09 06:50:09'),
(157, 'capstonee2@gmail.com', NULL, '904545', '2025-09-09 15:18:18', 1, '2025-09-09 07:13:18'),
(158, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '868819', '2025-09-20 00:06:33', 1, '2025-09-19 16:01:33'),
(159, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '953367', '2025-09-20 00:10:23', 1, '2025-09-19 16:05:23'),
(160, 'capstonee2@gmail.com', NULL, '350913', '2025-09-20 00:14:14', 1, '2025-09-19 16:09:15'),
(161, 'capstonee2@gmail.com', NULL, '357881', '2025-09-21 12:04:15', 1, '2025-09-21 03:59:15'),
(162, 'capstonee2@gmail.com', NULL, '552806', '2025-09-21 12:17:24', 1, '2025-09-21 04:12:24'),
(163, 'capstonee2@gmail.com', 'capstonee2@gmail.com', '530456', '2025-09-23 00:07:58', 1, '2025-09-22 16:02:58'),
(164, 'capstonee2@gmail.com', NULL, '151311', '2025-09-23 00:27:20', 1, '2025-09-22 16:22:20'),
(165, 'capstonee2@gmail.com', NULL, '679010', '2025-09-23 00:35:59', 1, '2025-09-22 16:30:59'),
(166, 'capstonee2@gmail.com', NULL, '786612', '2025-09-26 20:54:21', 1, '2025-09-26 12:49:21'),
(167, 'capstonee2@gmail.com', NULL, '100655', '2025-09-26 21:21:30', 1, '2025-09-26 13:16:30'),
(168, 'capstonee2@gmail.com', NULL, '851728', '2025-09-27 15:53:35', 1, '2025-09-27 07:48:35'),
(169, 'capstonee2@gmail.com', NULL, '297954', '2025-09-29 19:41:18', 1, '2025-09-29 11:36:18'),
(170, 'capstonee2@gmail.com', NULL, '628371', '2025-09-29 21:43:55', 1, '2025-09-29 13:38:55'),
(171, 'capstonee2@gmail.com', NULL, '594188', '2025-09-29 21:55:36', 1, '2025-09-29 13:50:36'),
(172, 'capstonee2@gmail.com', NULL, '585629', '2025-09-30 02:27:01', 1, '2025-09-29 18:22:01'),
(173, 'capstonee2@gmail.com', NULL, '252942', '2025-10-23 18:55:20', 1, '2025-10-23 10:50:20'),
(174, 'capstonee2@gmail.com', NULL, '321074', '2025-10-24 23:02:49', 1, '2025-10-24 14:57:49'),
(175, 'capstonee2@gmail.com', NULL, '750175', '2025-10-24 23:06:36', 1, '2025-10-24 15:01:36'),
(176, 'capstonee2@gmail.com', NULL, '275136', '2025-10-24 23:08:09', 1, '2025-10-24 15:03:09'),
(177, 'capstonee2@gmail.com', NULL, '442967', '2025-10-25 08:34:57', 1, '2025-10-25 00:29:57'),
(178, 'capstonee2@gmail.com', NULL, '852232', '2025-10-25 09:01:35', 1, '2025-10-25 00:56:35'),
(179, 'capstonee2@gmail.com', NULL, '652102', '2025-11-08 08:37:41', 1, '2025-11-08 00:32:41');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `alumni_id` varchar(50) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `alumni_id`, `contact`, `otp`, `expires_at`, `used`, `created_at`) VALUES
(1, 'CC-2507', 'capstonee2@gmail.com', '544909', '2025-09-21 11:52:28', 0, '2025-09-21 03:47:28'),
(2, 'CC-2507', 'capstonee2@gmail.com', '501290', '2025-09-21 11:52:33', 1, '2025-09-21 03:47:33'),
(3, 'CC-2507', '09856122843', '304135', '2025-09-21 11:53:47', 0, '2025-09-21 03:48:47'),
(4, 'CC-2507', 'capstonee2@gmail.com', '195447', '2025-09-21 11:56:33', 1, '2025-09-21 03:51:33'),
(5, 'CC-2507', '09856122843', '474396', '2025-09-21 12:01:37', 0, '2025-09-21 03:56:37'),
(6, 'CC-2507', 'capstonee2@gmail.com', '926113', '2025-09-21 12:02:42', 1, '2025-09-21 03:57:42'),
(7, 'CC-2507', '09856122843', '384184', '2025-09-21 12:12:40', 1, '2025-09-21 04:07:40'),
(8, 'CC-2507', '09856122843', '154652', '2025-09-21 12:16:42', 1, '2025-09-21 04:11:42'),
(9, 'CC-2507', 'capstonee2@gmail.com', '178483', '2025-09-21 12:25:07', 1, '2025-09-21 04:20:07'),
(10, 'CC-2507', 'capstonee2@gmail.com', '505677', '2025-09-21 16:03:51', 1, '2025-09-21 07:58:51'),
(11, 'CC-2507', '09856122843', '159292', '2025-09-21 16:04:28', 1, '2025-09-21 07:59:28'),
(12, 'CC-2507', 'capstonee2@gmail.com', '905948', '2025-09-26 20:52:35', 1, '2025-09-26 12:47:35'),
(13, 'CC-2507', 'capstonee2@gmail.com', '826749', '2025-09-29 19:39:20', 1, '2025-09-29 11:34:20'),
(14, 'CC-2507', 'capstonee2@gmail.com', '101796', '2025-10-23 19:58:18', 1, '2025-10-23 11:53:18'),
(15, 'CC-2507', 'capstonee2@gmail.com', '177548', '2025-10-24 23:01:46', 1, '2025-10-24 14:56:46'),
(16, 'CC-2507', 'capstonee2@gmail.com', '592399', '2025-10-25 21:58:57', 0, '2025-10-25 13:53:57'),
(17, 'CC-2507', 'capstonee2@gmail.com', '579551', '2025-11-08 08:36:15', 1, '2025-11-08 00:31:15'),
(18, 'CC-2507', 'capstonee2@gmail.com', '181545', '2025-11-08 09:00:20', 1, '2025-11-08 00:55:20');

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `speaker_events`
--

CREATE TABLE `speaker_events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_date` varchar(100) NOT NULL,
  `event_time` varchar(100) NOT NULL,
  `event_venue` varchar(255) NOT NULL,
  `event_topic` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('planning','invitations_sent','confirmed','completed','cancelled') DEFAULT 'planning',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `speaker_invitations`
--

CREATE TABLE `speaker_invitations` (
  `id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `event_date` varchar(100) DEFAULT NULL,
  `event_time` varchar(100) DEFAULT NULL,
  `event_venue` varchar(255) DEFAULT NULL,
  `event_topic` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `invited_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `invited_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `speaker_invitations`
--

INSERT INTO `speaker_invitations` (`id`, `alumni_id`, `event_date`, `event_time`, `event_venue`, `event_topic`, `subject`, `content`, `invited_at`, `invited_by`) VALUES
(1, 254, 'November 2025', '5pm', 'moist', 'Success', NULL, NULL, '2025-10-24 13:22:37', NULL),
(2, 254, 'November 2025', '5:00 - 10:00pm', 'moist', 'Success', NULL, NULL, '2025-10-25 00:47:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `speaker_rsvp`
--

CREATE TABLE `speaker_rsvp` (
  `id` int(11) NOT NULL,
  `alumni_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `response` enum('pending','accept','decline') DEFAULT 'pending',
  `event_date` varchar(100) DEFAULT NULL,
  `event_time` varchar(100) DEFAULT NULL,
  `event_venue` varchar(255) DEFAULT NULL,
  `event_topic` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `speaker_rsvp`
--

INSERT INTO `speaker_rsvp` (`id`, `alumni_id`, `token`, `response`, `event_date`, `event_time`, `event_venue`, `event_topic`, `created_at`, `updated_at`) VALUES
(1, 254, 'a7cdb93838c455c0c92f89e038aebdd02032876f50a5f2514a305163d8b05c43', 'accept', 'November 2025', '5:00 - 10:00pm', 'moist', 'Success', '2025-10-24 13:22:33', '2025-10-25 00:47:28');

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
(34, 'Charlie CHARLIEJAMES', 'Ca@gmail.com', '7c0e9a8857289bd39512723e5c3010a4', 3, '', 11);

-- --------------------------------------------------------

--
-- Table structure for table `user_posts`
--

CREATE TABLE `user_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_timestamp` (`timestamp`),
  ADD KEY `idx_action_type` (`action_type`),
  ADD KEY `idx_user_type` (`user_type`);

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
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_id_release_count` (`id_release_count`);

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
-- Indexes for table `conversation_threads`
--
ALTER TABLE `conversation_threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_started_by` (`started_by_id`,`started_by_type`),
  ADD KEY `idx_last_message` (`last_message_at`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_name` (`template_name`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_active` (`is_active`);

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
-- Indexes for table `homecoming_rsvp`
--
ALTER TABLE `homecoming_rsvp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD UNIQUE KEY `unique_alumni` (`alumni_id`),
  ADD UNIQUE KEY `unique_token` (`token`),
  ADD KEY `idx_alumni_id` (`alumni_id`),
  ADD KEY `idx_response` (`response`);

--
-- Indexes for table `id_release_log`
--
ALTER TABLE `id_release_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alumni_release` (`alumni_id`,`release_date`);

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
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sender` (`sender_id`,`sender_type`),
  ADD KEY `idx_recipient` (`recipient_id`,`recipient_type`),
  ADD KEY `idx_sent_at` (`sent_at`),
  ADD KEY `idx_parent` (`parent_message_id`);

--
-- Indexes for table `message_attachments`
--
ALTER TABLE `message_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_message` (`message_id`);

--
-- Indexes for table `message_recipients`
--
ALTER TABLE `message_recipients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_message` (`message_id`),
  ADD KEY `idx_recipient` (`recipient_id`),
  ADD KEY `idx_rsvp_status` (`rsvp_status`);

--
-- Indexes for table `message_replies`
--
ALTER TABLE `message_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_message_id` (`message_id`),
  ADD KEY `idx_recipient_id` (`recipient_id`),
  ADD KEY `idx_replied_at` (`replied_at`);

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
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_post_id` (`post_id`),
  ADD KEY `idx_user_id` (`user_id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_like` (`post_id`,`user_id`),
  ADD KEY `idx_post_id` (`post_id`);

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
-- Indexes for table `speaker_events`
--
ALTER TABLE `speaker_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_by` (`created_by`);

--
-- Indexes for table `speaker_invitations`
--
ALTER TABLE `speaker_invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_alumni` (`alumni_id`),
  ADD KEY `idx_invited_at` (`invited_at`),
  ADD KEY `idx_invited_by` (`invited_by`),
  ADD KEY `idx_event_date` (`event_date`);

--
-- Indexes for table `speaker_rsvp`
--
ALTER TABLE `speaker_rsvp`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_token` (`token`),
  ADD UNIQUE KEY `unique_alumni` (`alumni_id`),
  ADD KEY `idx_response` (`response`),
  ADD KEY `idx_alumni` (`alumni_id`),
  ADD KEY `idx_created_at` (`created_at`);

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
-- Indexes for table `user_posts`
--
ALTER TABLE `user_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `alumni_ids`
--
ALTER TABLE `alumni_ids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `alumnus_bio`
--
ALTER TABLE `alumnus_bio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `alumnus_bio_archive`
--
ALTER TABLE `alumnus_bio_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `archive_alumni`
--
ALTER TABLE `archive_alumni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `careers`
--
ALTER TABLE `careers`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `conversation_threads`
--
ALTER TABLE `conversation_threads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

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
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `event_bookmarks`
--
ALTER TABLE `event_bookmarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `event_comments`
--
ALTER TABLE `event_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `event_commits`
--
ALTER TABLE `event_commits`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_likes`
--
ALTER TABLE `event_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
-- AUTO_INCREMENT for table `homecoming_rsvp`
--
ALTER TABLE `homecoming_rsvp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `id_release_log`
--
ALTER TABLE `id_release_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `message_attachments`
--
ALTER TABLE `message_attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message_recipients`
--
ALTER TABLE `message_recipients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `message_replies`
--
ALTER TABLE `message_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `otp_history`
--
ALTER TABLE `otp_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=180;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `privacy_agreements`
--
ALTER TABLE `privacy_agreements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `speaker_events`
--
ALTER TABLE `speaker_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `speaker_invitations`
--
ALTER TABLE `speaker_invitations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `speaker_rsvp`
--
ALTER TABLE `speaker_rsvp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `user_posts`
--
ALTER TABLE `user_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
-- Constraints for table `homecoming_rsvp`
--
ALTER TABLE `homecoming_rsvp`
  ADD CONSTRAINT `homecoming_rsvp_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `id_release_log`
--
ALTER TABLE `id_release_log`
  ADD CONSTRAINT `id_release_log_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `majors`
--
ALTER TABLE `majors`
  ADD CONSTRAINT `majors_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`parent_message_id`) REFERENCES `messages` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `message_attachments`
--
ALTER TABLE `message_attachments`
  ADD CONSTRAINT `message_attachments_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message_recipients`
--
ALTER TABLE `message_recipients`
  ADD CONSTRAINT `message_recipients_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message_replies`
--
ALTER TABLE `message_replies`
  ADD CONSTRAINT `message_replies_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_replies_ibfk_2` FOREIGN KEY (`recipient_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE;

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
