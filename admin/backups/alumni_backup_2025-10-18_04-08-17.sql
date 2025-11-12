-- Alumni Database Backup
-- Generated on: 2025-10-18 04:08:17
-- Database: alumni_db

SET FOREIGN_KEY_CHECKS=0;

-- Table structure for `activity_log`
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_type` enum('admin','registrar','alumni') DEFAULT 'alumni',
  `action_type` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_timestamp` (`timestamp`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_user_type` (`user_type`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `activity_log`
INSERT INTO `activity_log` VALUES ('1', '1', 'Admin', 'admin', 'CREATE_JOB', 'Job: MOISTDOMAIN at MOISTDOMAIN', NULL, 'job', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:07:05');
INSERT INTO `activity_log` VALUES ('2', '1', 'Admin', 'admin', 'CREATE_COURSE', 'Created course: MOISTDOMAIN', '12', 'course', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:07:16');
INSERT INTO `activity_log` VALUES ('3', '1', 'Admin', 'admin', 'DELETE_COURSE', 'Deleted course: MOISTDOMAIN', '12', 'course', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:07:21');
INSERT INTO `activity_log` VALUES ('4', '1', 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:08:33');
INSERT INTO `activity_log` VALUES ('5', '1', 'Admin', 'admin', 'DELETE_COURSE', 'Deleted course: BDSA', '11', 'course', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:11:42');
INSERT INTO `activity_log` VALUES ('6', '1', 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 21:13:55');
INSERT INTO `activity_log` VALUES ('7', '1', 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:31:29');
INSERT INTO `activity_log` VALUES ('8', '1', 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:32:46');
INSERT INTO `activity_log` VALUES ('9', '1', 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:32:47');
INSERT INTO `activity_log` VALUES ('10', '1', 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:32:48');
INSERT INTO `activity_log` VALUES ('11', '1', 'Admin', 'admin', 'LOGIN', 'User logged into the system', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-08 23:33:28');
INSERT INTO `activity_log` VALUES ('12', '1', 'Admin', 'admin', '23', 'Alumni validated', '0', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:35:16');
INSERT INTO `activity_log` VALUES ('13', '1', 'Admin', 'admin', '23', 'Alumni validated', '0', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:39:16');
INSERT INTO `activity_log` VALUES ('14', '1', 'Admin', 'admin', '23', 'Alumni validated', '0', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:47:16');
INSERT INTO `activity_log` VALUES ('15', '1', 'Admin', 'admin', '23', 'Alumni validated', '0', NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:49:20');
INSERT INTO `activity_log` VALUES ('16', '23', 'test', 'registrar', 'Alumni validated', 'Alumni AINE CASAS (ID: BSBA-2604) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:51:07');
INSERT INTO `activity_log` VALUES ('17', '23', 'test', 'registrar', 'Alumni validated', 'Alumni AINE CASAS (ID: BSBA-2604) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:55:13');
INSERT INTO `activity_log` VALUES ('18', '23', 'test', 'registrar', 'Alumni validated', 'Alumni MARY ANNE AMPO (ID: SHS-2503) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 01:57:06');
INSERT INTO `activity_log` VALUES ('19', '23', 'test', 'registrar', 'Alumni validated', 'Alumni MARY ANNE AMPO (ID: SHS-2503) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 02:06:00');
INSERT INTO `activity_log` VALUES ('20', '23', 'test', 'registrar', 'Alumni validated', 'Alumni MICHEL CABALLERO (ID: CC-2501) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 03:07:42');
INSERT INTO `activity_log` VALUES ('21', '23', 'test', 'registrar', 'Alumni validated', 'Alumni AINE CASAS (ID: BSBA-2604) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 05:18:32');
INSERT INTO `activity_log` VALUES ('22', '23', 'test', 'registrar', 'Alumni validated', 'Alumni GIANFRANCO MADRONERO (ID: CC-2584) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-09 08:47:37');
INSERT INTO `activity_log` VALUES ('23', '23', 'test', 'registrar', 'Alumni validated', 'Alumni Angela Santos (ID: CC-2504) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 14:45:34');
INSERT INTO `activity_log` VALUES ('24', '23', 'test', 'registrar', 'Alumni validated', 'Alumni Anthony Torres (ID: CC-2507) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 14:45:41');
INSERT INTO `activity_log` VALUES ('25', '23', 'test', 'registrar', 'Alumni validated', 'Alumni Arvin Mendoza (ID: CC-2520) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 14:45:53');
INSERT INTO `activity_log` VALUES ('26', '23', 'test', 'registrar', 'Alumni validated', 'Alumni Charlie Cruz (ID: CC-2501) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-12 14:46:24');
INSERT INTO `activity_log` VALUES ('27', '23', 'test', 'registrar', 'Alumni validated', 'Alumni JANRO MICABALO (ID: CC-2507) was validated by registrar test', NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36', '2025-09-19 18:07:59');
INSERT INTO `activity_log` VALUES ('28', '23', 'test', 'registrar', 'Alumni validated', 'Alumni JANRO MICABALO (ID: CC-2507) was validated by registrar test', NULL, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 18:21:37');

-- Table structure for `alumni_ids`
DROP TABLE IF EXISTS `alumni_ids`;
CREATE TABLE `alumni_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumni_id` (`alumni_id`),
  KEY `course_id` (`course_id`),
  KEY `fk_alumni_strand` (`strand_id`),
  CONSTRAINT `alumni_ids_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_alumni_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_alumni_strand` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_strand` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=131 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data for table `alumni_ids`
INSERT INTO `alumni_ids` VALUES ('1', 'CC-2501', 'CABALLERO', 'MICHEL', 'JAMOROI', NULL, 'kennethladra3@gmail.com', '2001-05-19', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '09677314559', NULL);
INSERT INTO `alumni_ids` VALUES ('3', 'CC-2503', 'PADUA', 'CELESTE', 'SACEDON', NULL, 'paulinmgrayos@gmail.com', '2000-03-01', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '09856122841', NULL);
INSERT INTO `alumni_ids` VALUES ('5', 'CC-2505', 'BENDICIO', 'CARLO', 'LABUANON', NULL, NULL, '2002-04-13', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('6', 'CC-2506', 'MACAHILOS', 'KRISTIAN KING', 'MACARAYO', NULL, NULL, '2002-12-15', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('7', 'CC-2507', 'MICABALO', 'JANRO', 'JOVEN', NULL, 'capstonee2@gmail.com', '2002-08-16', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '09856122843', '$2y$10$32h2JrnBpU02GrOgoVBreunwv1g2CJlpby/ooWkqiCixvyBWCJJ2i');
INSERT INTO `alumni_ids` VALUES ('8', 'CC-2508', 'PREL', 'NINO LOVE', 'B.', NULL, NULL, '2001-01-05', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('9', 'CC-2509', 'LADRA', 'JOHN LEE', 'M.', NULL, NULL, '2000-01-03', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('10', 'CC-2510', 'VERGARA', 'MICHAEL VINCENT', 'G.', NULL, NULL, '2002-04-04', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('11', 'CC-2511', 'FERNANDEZ', 'JOHN DAVE', 'L.', NULL, NULL, '2002-06-22', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('12', 'CC-2512', 'RAYON', 'JAPHETH JIREH', NULL, NULL, NULL, '2002-10-18', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('13', 'CC-2513', 'ADAJAR', 'JUVELLE', 'C.', NULL, NULL, '2002-12-13', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('14', 'CC-2514', 'CANQUE', 'CHASTER', 'L.', NULL, NULL, '2001-01-01', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('15', 'CC-2515', 'BORRES', 'JOHN CONRAD', 'A.', NULL, NULL, '2000-08-19', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('16', 'CC-2516', 'PAJARON', 'CLAVEL', 'I.', NULL, NULL, '1999-10-14', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('17', 'CC-2517', 'TUBIANO', 'JAY ANNE', 'C.', NULL, NULL, '2003-06-07', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('18', 'CC-2518', 'VARIACION', 'MERLYN', 'T.', NULL, NULL, '2002-06-19', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('19', 'CC-2519', 'PAJARON', 'DAIMIE ROSE', 'D.', NULL, NULL, '2000-05-19', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('20', 'CC-2520', 'VALMORES', 'JOY', 'C.', NULL, NULL, '2001-06-15', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('21', 'CC-2521', 'ALABA', 'ALTHEA', 'M.', NULL, NULL, '2002-06-25', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('22', 'CC-2522', 'CAGALAWAN', 'RHENIL JANE', 'D.', NULL, NULL, '2002-08-21', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('23', 'CC-2523', 'GAYLAWAN', 'JOYSAN', 'U.', NULL, NULL, '2002-08-31', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('24', 'CC-2524', 'DALANIEL', 'JENNIE', 'D.', NULL, NULL, '2002-01-07', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('25', 'CC-2525', 'PAGHANAPAN', 'JERORIS JAY', 'R.', NULL, NULL, '2003-03-18', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('26', 'CC-2526', 'WARQUE', 'MELCHOR', 'C.', 'JR.', NULL, '2002-11-25', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('27', 'CC-2527', 'DEGAMO', 'KEVIN JOHN MICHAEL', NULL, NULL, NULL, '2003-02-27', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('28', 'CC-2528', 'APA', 'LEONARDO', 'S.', NULL, NULL, '2002-05-03', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('29', 'CC-2529', 'DAGHAY', 'AXEL KING', 'P.', NULL, NULL, '1998-10-17', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('30', 'CC-2530', 'SUDLAYAN', 'RAYNAND', 'G.', NULL, NULL, '2000-10-03', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('31', 'CC-2531', 'PADAYAO', 'TIMOTEO', 'M.', 'JR.', NULL, '2000-02-14', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('32', 'CC-2532', 'VIRTUDAZO', 'JIMUEL', 'GULA', NULL, NULL, '2003-02-25', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('33', 'CC-2533', 'CABACTULAN', 'DONNA MAE', 'C.', NULL, NULL, '2003-02-20', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('34', 'CC-2534', 'SALISE', 'RICA', 'S.', NULL, NULL, '2002-08-05', 'Female', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('35', 'CC-2535', 'BADILLA', 'ANNERSON', 'C.', NULL, NULL, '2002-08-30', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('36', 'CC-2536', 'CADRON', 'PHILIP', 'P.', NULL, NULL, '2002-10-21', 'Male', '2025', '6', NULL, '2025-08-26 14:03:44', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('37', 'CC-2579', 'GALDO', 'ROVIE', 'OPENA', NULL, NULL, '2002-10-28', 'Male', '2025', '1', NULL, '2025-08-26 14:04:35', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('38', 'CC-2580', 'UCAB', 'GERALD', 'RAGAS', NULL, NULL, '2000-05-19', 'Male', '2025', '1', NULL, '2025-08-26 14:04:35', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('39', 'CC-2581', 'MACAS', 'MARY ANNE MAE', 'YAMIT', NULL, NULL, '1994-04-05', 'Female', '2025', '1', NULL, '2025-08-26 14:04:35', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('40', 'CC-2582', 'ESPANOL', 'JOMARIE', 'BETARMOS', NULL, NULL, '2002-09-29', 'Male', '2025', '1', NULL, '2025-08-26 14:04:35', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('41', 'CC-2583', 'ABOG', 'FRANCO', 'ALFAR', NULL, NULL, '2000-11-25', 'Male', '2025', '1', NULL, '2025-08-26 14:04:35', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('42', 'CC-2584', 'MADRONERO', 'GIANFRANCO', 'SALIW', NULL, '', '1988-10-11', 'Male', '2025', '1', NULL, '2025-08-26 14:04:35', 'College', NULL, '09856122843', NULL);
INSERT INTO `alumni_ids` VALUES ('83', 'CC-2585', 'SUCANDAN', 'NOVY JANE', 'BALDOMAN', NULL, NULL, '2002-09-14', 'Female', '2025', '2', '1', '2025-08-26 14:30:00', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('84', 'CC-2586', 'CACULBA', 'ALBERT', 'HALUAG', NULL, NULL, '1999-04-26', 'Male', '2025', '2', '1', '2025-08-26 14:30:00', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('85', 'CC-2587', 'RAYON', 'RHEJOY', 'MAMARAN', NULL, NULL, '2003-04-13', 'Female', '2025', '2', '1', '2025-08-26 14:30:00', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('86', 'CC-2588', 'ACERA', 'ISA BONNI EVE', 'ALONZO', NULL, NULL, '2002-11-30', 'Female', '2025', '2', '1', '2025-08-26 14:30:00', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('87', 'CC-2589', 'BALUSAN', 'METCHIE', 'M.', NULL, NULL, '2001-12-28', 'Female', '2025', '2', '1', '2025-08-26 14:30:00', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('88', 'CC-2590', 'MARAVELES', 'ROWAR', 'ASUR', NULL, NULL, '2003-02-07', 'Female', '2025', '2', '1', '2025-08-26 14:30:00', 'College', NULL, '', NULL);
INSERT INTO `alumni_ids` VALUES ('89', 'CC-2591', 'LOZANO', 'MOSES JIREH', 'AMATONG', NULL, 'moseslozano20@gmail.com', '2000-11-21', 'Male', '2022', '1', NULL, '2025-09-06 13:40:03', 'College', NULL, '09164813376', NULL);
INSERT INTO `alumni_ids` VALUES ('91', 'CC-2592', 'BAGARES', 'KIMLEYE', 'P', NULL, 'bkimleye@gmail.com', '2001-11-15', 'Male', '2023', '6', NULL, '2025-09-06 14:03:15', 'College', NULL, '09970680004', NULL);
INSERT INTO `alumni_ids` VALUES ('93', 'CC-2593', 'PADILLA', 'SHIEHONNY THEA', NULL, NULL, 'Padillashiehonny@gmail.com', '2000-11-17', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000001', NULL);
INSERT INTO `alumni_ids` VALUES ('94', 'CC-2594', 'DELA CRUZ', 'VINCONT', 'ALABAN', NULL, 'Pagoyss@gmail.com', '2002-04-04', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000002', NULL);
INSERT INTO `alumni_ids` VALUES ('95', 'CC-2595', 'LEGASPI', 'IRAH', 'NAVARRO', NULL, 'Irah095@gmail.com', '2001-11-23', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000003', NULL);
INSERT INTO `alumni_ids` VALUES ('96', 'CC-2596', 'PAGARA', 'JULITO', NULL, NULL, NULL, '1990-10-24', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000004', NULL);
INSERT INTO `alumni_ids` VALUES ('97', 'CC-2597', 'LLAGAS', 'CHARITY MAE', 'MICABALO', NULL, 'llagascharitymae@gmail.com', '2001-11-01', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000005', NULL);
INSERT INTO `alumni_ids` VALUES ('98', 'CC-2598', 'REYES', 'ALVIN', 'ESTARES', NULL, 'alvinreyesii210@gmail.com', '2001-10-27', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000006', NULL);
INSERT INTO `alumni_ids` VALUES ('99', 'CC-2599', 'OCATE', 'VENZ JERIC', 'ZARATE', NULL, 'ocatevenz@gmail.com', '2002-01-30', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000007', NULL);
INSERT INTO `alumni_ids` VALUES ('100', 'CC-2600', 'SABALLA', 'SEMMARY', 'GUERRA', NULL, 'semdaxguerra@gmail.com', '2000-07-26', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000008', NULL);
INSERT INTO `alumni_ids` VALUES ('101', 'CC-2601', 'OROJALES', 'BRAYAN REY', 'P', NULL, 'brayanorojales@gamil.com', '2001-04-11', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000009', NULL);
INSERT INTO `alumni_ids` VALUES ('102', 'CC-2602', 'F', 'PRINCESS ALLIAH', NULL, NULL, NULL, '2003-01-28', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000010', NULL);
INSERT INTO `alumni_ids` VALUES ('103', 'CC-2603', 'NAJOS', 'JHANNA JANE', 'CAMANIAN', NULL, 'jhannajanenajos@gmail.com', '2002-03-28', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000011', NULL);
INSERT INTO `alumni_ids` VALUES ('104', 'CC-2604', 'MORGA', 'MAICA', 'CATANONG', NULL, 'Maicamorga4@gmail.com', '2001-11-25', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000012', NULL);
INSERT INTO `alumni_ids` VALUES ('105', 'CC-2605', 'LILOC', 'VANESSA', NULL, NULL, 'lilocvanessa@gmail.com', '2001-10-02', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000013', NULL);
INSERT INTO `alumni_ids` VALUES ('106', 'CC-2606', 'NUENA', 'RUSSEL', NULL, NULL, 'Nuenarussel1@gmail.com', '2001-12-07', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000014', NULL);
INSERT INTO `alumni_ids` VALUES ('107', 'CC-2607', 'DALAUTA', 'PAULA JENNESSA', NULL, NULL, 'paulajennessa@gmail.com', '2002-10-17', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000015', NULL);
INSERT INTO `alumni_ids` VALUES ('108', 'CC-2608', 'SALAZAR', 'TRISHA', NULL, NULL, 'Salazartrisha088@gmail.com', '2003-06-20', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000016', NULL);
INSERT INTO `alumni_ids` VALUES ('109', 'CC-2609', 'CAGADAS', 'ARCHE NEAL', 'CABANTAC', NULL, 'archenealcagadas@gmail.com', '2000-06-14', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000017', NULL);
INSERT INTO `alumni_ids` VALUES ('110', 'CC-2610', 'ABOC', 'RETMOND', 'DE LA CRUZ', NULL, 'abocreymond@gmail.com', '2000-03-10', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000018', NULL);
INSERT INTO `alumni_ids` VALUES ('111', 'CC-2611', 'CABURAL', 'JHAN NICO', NULL, NULL, 'nicnicimafranca24@gmail.co', '2000-12-24', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000019', NULL);
INSERT INTO `alumni_ids` VALUES ('112', 'CC-2612', 'ZAPICO', 'ASHLYCHELLE', NULL, NULL, 'Zapicoashley91@gmail.com', '2003-04-06', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000020', NULL);
INSERT INTO `alumni_ids` VALUES ('113', 'CC-2613', 'BARROS', 'JOLINA', 'ESTRADA', NULL, 'Barrosjohna@gmail.com', '2001-10-23', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000021', NULL);
INSERT INTO `alumni_ids` VALUES ('114', 'CC-2614', 'CABISAN', 'HANZ MASARN', NULL, NULL, 'Cabisar11@gmail.com', '2001-10-15', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000022', NULL);
INSERT INTO `alumni_ids` VALUES ('115', 'CC-2615', 'LIRCY', 'XYDER CLYDE', 'PANTE', NULL, 'Xyderclyde1@gmail.com', '2002-11-06', 'Male', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000023', NULL);
INSERT INTO `alumni_ids` VALUES ('116', 'CC-2616', 'GALEON', 'DONALYN', NULL, NULL, 'acquiatandonalyn@gmail.com', '2003-02-06', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000024', NULL);
INSERT INTO `alumni_ids` VALUES ('117', 'CC-2617', 'GLEZYL', ' ', NULL, NULL, NULL, '0000-00-00', 'Female', '2025', '13', NULL, '2025-09-09 06:04:11', 'College', NULL, '09170000025', NULL);
INSERT INTO `alumni_ids` VALUES ('118', 'SHS-2501', 'LABIAL', 'PRINCESS JANE', 'B', NULL, 'princess.labial@dummy.com', '2023-07-16', 'Female', '2025', NULL, NULL, '2025-09-09 07:03:15', '', '2', '09171234567', NULL);
INSERT INTO `alumni_ids` VALUES ('119', 'SHS-2502', 'RODRIGUEZ', 'ROSELYN', 'T', NULL, 'roselyn.rodriguez@dummy.com', '2000-06-04', 'Female', '2025', NULL, NULL, '2025-09-09 07:03:15', '', '5', '09179876543', NULL);
INSERT INTO `alumni_ids` VALUES ('120', 'SHS-2503', 'AMPO', 'MARY ANNE', 'QUIMSON', NULL, 'capstonee2@gmail.com', '2004-02-25', 'Female', '2025', NULL, NULL, '2025-09-09 07:03:15', '', '1', '09856122843', NULL);
INSERT INTO `alumni_ids` VALUES ('121', 'MW-2501', 'LABIAL', 'ARAVILA', 'LAGNO', NULL, 'abinglabial24@gmail.com', '2003-11-24', 'Female', '2025', '5', NULL, '2025-09-09 09:47:40', 'College', NULL, '09168658690', NULL);
INSERT INTO `alumni_ids` VALUES ('122', 'MW-2502', 'MAISO', 'AIVE', 'D', NULL, 'KYAHGOMONAY200@GMAIL.COM', '1998-11-11', 'Female', '2025', '5', NULL, '2025-09-09 09:47:40', 'College', NULL, '09709843066', NULL);
INSERT INTO `alumni_ids` VALUES ('123', 'MW-2503', 'TACULOD', 'JESSELLE', 'JABAGAT', NULL, 'Jesselletaculod@gmail.com', '2003-10-11', 'Female', '2025', '5', NULL, '2025-09-09 09:47:40', 'College', NULL, '09922573860', NULL);
INSERT INTO `alumni_ids` VALUES ('124', 'MW-2504', 'MARBAN', 'PEARL ANGELYN', 'ABANIL', NULL, 'marbanpearl@gmail.com', '2003-09-06', 'Female', '2025', '5', NULL, '2025-09-09 09:47:40', 'College', NULL, '9630995906', NULL);
INSERT INTO `alumni_ids` VALUES ('125', 'MW-2505', 'GUZMAN', 'KATE', 'LILOC', NULL, 'kateguzman01@gmail.com', '2001-04-05', 'Female', '2025', '5', NULL, '2025-09-09 09:47:40', 'College', NULL, '9357752147', NULL);
INSERT INTO `alumni_ids` VALUES ('126', 'MW-2506', 'GUZMAN', 'CARLOS', 'LILOC', NULL, NULL, '2002-10-19', 'Male', '2025', '5', NULL, '2025-09-09 11:00:26', 'College', NULL, '09534873506', NULL);
INSERT INTO `alumni_ids` VALUES ('127', 'MW-2507', 'ECHANO', 'BARBE ANN', 'PAGRAN', NULL, 'echanobarbe227@gmail.com', '2004-02-07', 'Female', '2025', '5', NULL, '2025-09-09 11:00:26', 'College', NULL, '09193336544', NULL);
INSERT INTO `alumni_ids` VALUES ('128', 'MW-2508', 'VIRTUDAZO', 'APPLE MAE', 'CUSAY', NULL, 'virtudazoapplemae22@gmail.com', '2003-12-23', 'Female', '2025', '5', NULL, '2025-09-09 11:00:26', 'College', NULL, '09066657058', NULL);
INSERT INTO `alumni_ids` VALUES ('129', 'MW-2509', 'TAMPO', 'STELLA SHAHANIE', 'MADRIDANO', NULL, NULL, '1997-08-30', 'Female', '2025', '5', NULL, '2025-09-09 11:00:26', 'College', NULL, '09514979428', NULL);
INSERT INTO `alumni_ids` VALUES ('130', 'CC-25034', 'AMPO', 'MARY ANNE', 'QUIMSON', NULL, 'ampomary01@gmail.com', '2000-01-01', 'Female', '2025', '1', NULL, '2025-09-09 13:32:13', 'College', NULL, '09856122843', NULL);

-- Table structure for `alumnus_bio`
DROP TABLE IF EXISTS `alumnus_bio`;
CREATE TABLE `alumnus_bio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `id_release_count` int(11) DEFAULT 0 COMMENT 'Number of times ID has been printed/released',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_id_release_count` (`id_release_count`)
) ENGINE=InnoDB AUTO_INCREMENT=255 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data for table `alumnus_bio`
INSERT INTO `alumnus_bio` VALUES ('254', 'CC-2507', 'MICABALO', 'JANRO', 'JOVEN', NULL, '2002-08-16', 'Waterfall', 'employed', '[{\"date_started\":\"2024-05-20\",\"duration\":\"1 year 4 months\"}]', 'Male', '2025', '6', NULL, NULL, 'EDUCATION', '09856122843', 'Waterfall', 'Waterfall@hotmail.com', 'capstonee2@gmail.com', '$2y$10$32h2JrnBpU02GrOgoVBreunwv1g2CJlpby/ooWkqiCixvyBWCJJ2i', 'profile_254_1760750263.jpg', '2025-09-22 18:04:27', NULL, '1', NULL, NULL, NULL, NULL, '2');

-- Table structure for `alumnus_bio_archive`
DROP TABLE IF EXISTS `alumnus_bio_archive`;
CREATE TABLE `alumnus_bio_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Table structure for `archive_alumni`
DROP TABLE IF EXISTS `archive_alumni`;
CREATE TABLE `archive_alumni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `strand_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `archived_by` (`archived_by`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `archive_alumni_ibfk_1` FOREIGN KEY (`archived_by`) REFERENCES `users` (`id`),
  CONSTRAINT `archive_alumni_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data for table `archive_alumni`
INSERT INTO `archive_alumni` VALUES ('7', '68', 'JAMAICO', 'BARSOBIA', 'CABANDUCOS', 'Male', '0985612243', 'capstonee2@gmail.com', '2025', '6', 'Charlie', 'Waterfall ', 'Cagayan de oro Mis.or', '1', '2025-09-05 13:47:27', '23', NULL);
INSERT INTO `archive_alumni` VALUES ('20', '57', 'AXEL KING', 'P.', 'DAGHAY', 'Male', '09164907870', 'charlieeee@gmail.com', '2025', '6', 'Waterfall', 'Waterfall', 'Cagayan de oro Mis.or', '1', '2025-09-08 14:33:32', '23', NULL);
INSERT INTO `archive_alumni` VALUES ('21', '150', 'MARY ANNE', 'QUIMSON', 'AMPO', 'Female', '09856122843', 'capstonee2@gmail.com', '2025', NULL, '', '', '', '1', '2025-09-09 07:00:19', '23', '1');
INSERT INTO `archive_alumni` VALUES ('22', '66', 'CELESTE', 'SACEDON', 'PADUA', 'Female', '0985612241', 'paulinmgrayos@gmail.com', '2025', '6', 'Waterfallz', 'Waterfall', 'Cagayan de oro Mis.or', '1', '2025-09-09 11:00:53', '23', NULL);
INSERT INTO `archive_alumni` VALUES ('23', '54', 'Nicholas', 'John', 'Young', 'Male', '0985612243', 'gsADA2@gmail.com', '2016', '3', 'Waterfallz', 'Waterfall', 'Waterfall', '1', '2025-09-09 11:13:39', '23', NULL);

-- Table structure for `careers`
DROP TABLE IF EXISTS `careers`;
CREATE TABLE `careers` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `company` varchar(250) NOT NULL,
  `location` text NOT NULL,
  `job_title` text NOT NULL,
  `description` text NOT NULL,
  `user_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `careers`
INSERT INTO `careers` VALUES ('1', 'IT Company', 'Home-Based', 'Web Developer', '&lt;p style=&quot;-webkit-tap-highlight-color: rgba(0, 0, 0, 0); margin-top: 1.5em; margin-bottom: 1.5em; line-height: 1.5; animation: 1000ms linear 0s 1 normal none running fadeInLorem;&quot;&gt;Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Sagittis eu volutpat odio facilisis mauris sit amet massa vitae. In tellus integer feugiat scelerisque varius morbi enim. Orci eu lobortis elementum nibh tellus molestie nunc. Vulputate ut pharetra sit amet aliquam id diam maecenas ultricies. Lacus sed viverra tellus in hac habitasse platea dictumst vestibulum. Eleifend donec pretium vulputate sapien nec. Enim praesent elementum facilisis leo vel fringilla est ullamcorper. Quam adipiscing vitae proin sagittis nisl rhoncus. Sed viverra ipsum nunc aliquet bibendum. Enim ut sem viverra aliquet eget sit amet tellus. Integer feugiat scelerisque varius morbi enim nunc faucibus.&lt;/p&gt;&lt;p style=&quot;-webkit-tap-highlight-color: rgba(0, 0, 0, 0); margin-top: 1.5em; margin-bottom: 1.5em; line-height: 1.5; animation: 1000ms linear 0s 1 normal none running fadeInLorem;&quot;&gt;Viverra justo nec ultrices dui. Leo vel orci porta non pulvinar neque laoreet. Id semper risus in hendrerit gravida rutrum quisque non tellus. Sit amet consectetur adipiscing elit ut. Id neque aliquam vestibulum morbi blandit cursus risus. Tristique senectus et netus et malesuada. Amet aliquam id diam maecenas ultricies mi eget mauris. Morbi tristique senectus et netus et malesuada. Diam phasellus vestibulum lorem sed risus. Tempor orci dapibus ultrices in. Mi sit amet mauris commodo quis imperdiet. Quisque sagittis purus sit amet volutpat. Vehicula ipsum a arcu cursus. Ornare quam viverra orci sagittis eu volutpat odio facilisis. Id volutpat lacus laoreet non curabitur. Cursus euismod quis viverra nibh cras pulvinar mattis nunc. Id aliquet lectus proin nibh nisl condimentum id venenatis. Eget nulla facilisi etiam dignissim diam quis enim lobortis. Lacus suspendisse faucibus interdum posuere lorem ipsum dolor sit amet.&lt;/p&gt;', '3', '2020-10-15 14:14:27');
INSERT INTO `careers` VALUES ('2', 'MOIST', 'Santa Cruz Cogon Balingasag', 'IT Specialist', '&lt;p data-start=&quot;173&quot; data-end=&quot;587&quot;&gt;&lt;b data-start=&quot;173&quot; data-end=&quot;193&quot;&gt;Job Description:&lt;/b&gt;&lt;br data-start=&quot;193&quot; data-end=&quot;196&quot;&gt;We are seeking a skilled and dedicated IT Specialist to join our team at MOIST. The IT Specialist will be responsible for ensuring the smooth operation and maintenance of all company IT systems, providing support to employees, and troubleshooting technical issues. The ideal candidate will have expertise in network administration, hardware and software troubleshooting, and system security.&lt;/p&gt;&lt;p data-start=&quot;589&quot; data-end=&quot;614&quot;&gt;&lt;b data-start=&quot;589&quot; data-end=&quot;614&quot;&gt;Key Responsibilities:&lt;/b&gt;&lt;/p&gt;&lt;ul data-start=&quot;615&quot; data-end=&quot;1036&quot;&gt;&lt;li data-start=&quot;615&quot; data-end=&quot;685&quot;&gt;Install, configure, and maintain IT systems, software, and hardware.&lt;/li&gt;&lt;li data-start=&quot;686&quot; data-end=&quot;754&quot;&gt;Provide technical support and troubleshooting assistance to staff.&lt;/li&gt;&lt;li data-start=&quot;755&quot; data-end=&quot;825&quot;&gt;Monitor system performance and ensure all IT systems are up to date.&lt;/li&gt;&lt;li data-start=&quot;826&quot; data-end=&quot;879&quot;&gt;Manage network security and safeguard company data.&lt;/li&gt;&lt;li data-start=&quot;880&quot; data-end=&quot;964&quot;&gt;Assist with the setup and maintenance of internal communication tools and systems.&lt;/li&gt;&lt;li data-start=&quot;965&quot; data-end=&quot;1033&quot;&gt;Document and report on IT infrastructure, issues, and resolutions.&lt;/li&gt;&lt;/ul&gt;&lt;p data-start=&quot;1037&quot; data-end=&quot;1056&quot;&gt;&lt;b data-start=&quot;1037&quot; data-end=&quot;1056&quot;&gt;Qualifications:&lt;/b&gt;&lt;/p&gt;&lt;ul data-start=&quot;1057&quot; data-end=&quot;1398&quot;&gt;&lt;li data-start=&quot;1057&quot; data-end=&quot;1139&quot;&gt;Bachelor&rsquo;s degree in Information Technology, Computer Science, or related field.&lt;/li&gt;&lt;li data-start=&quot;1140&quot; data-end=&quot;1201&quot;&gt;Proven experience in IT support and network administration.&lt;/li&gt;&lt;li data-start=&quot;1202&quot; data-end=&quot;1282&quot;&gt;Strong knowledge of operating systems, hardware, and software troubleshooting.&lt;/li&gt;&lt;li data-start=&quot;1283&quot; data-end=&quot;1341&quot;&gt;Ability to work independently and in a team environment.&lt;/li&gt;&lt;li data-start=&quot;1342&quot; data-end=&quot;1398&quot;&gt;Strong problem-solving skills and attention to detail.&lt;/li&gt;&lt;/ul&gt;', '1', '2020-10-15 15:05:37');
INSERT INTO `careers` VALUES ('3', 'MOISTDOMAIN', 'MOISTDOMAIN', 'MOISTDOMAIN', '&lt;span style=&quot;font-size: 14px;&quot;&gt;MOISTDOMAIN&lt;/span&gt;', '1', '2025-09-09 03:07:05');
INSERT INTO `careers` VALUES ('4', 'Test Company', 'Remote', 'Test Developer Position', 'This is a test job posting to verify the system is working correctly.', '1', '2025-09-09 10:56:54');

-- Table structure for `courses`
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `course` text NOT NULL,
  `about` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `courses`
INSERT INTO `courses` VALUES ('1', 'BS Information Technology', 'Bachelor of Science in Information Technology');
INSERT INTO `courses` VALUES ('2', 'BSED', 'Bachelor of Secondary Education');
INSERT INTO `courses` VALUES ('3', 'BEED', 'Bachelor of Elementary Education');
INSERT INTO `courses` VALUES ('4', 'BSBA', 'Bachelor of Science in Business Administration');
INSERT INTO `courses` VALUES ('5', 'MIDWIFERY', 'Midwifery Program');
INSERT INTO `courses` VALUES ('6', 'CRIMINOLOGY', 'Criminology Program');
INSERT INTO `courses` VALUES ('10', 'CTHM', 'CTHM');
INSERT INTO `courses` VALUES ('13', 'BS HOSPITALITY MANAGEMENT', 'BS HOSPITALITY MANAGEMENT');

-- Table structure for `employment_history`
DROP TABLE IF EXISTS `employment_history`;
CREATE TABLE `employment_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `company_name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `industry_type` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `date_start` date NOT NULL,
  `date_end` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `employment_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `employment_status`
DROP TABLE IF EXISTS `employment_status`;
CREATE TABLE `employment_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `current_status` enum('Employed','Unemployed','Self-Employed','Student','Retired') NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `industry_type` varchar(255) DEFAULT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `employment_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `event_bookmarks`
DROP TABLE IF EXISTS `event_bookmarks`;
CREATE TABLE `event_bookmarks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_bookmark` (`event_id`,`user_id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `event_bookmarks`
INSERT INTO `event_bookmarks` VALUES ('3', '6', '25', '2025-08-16 01:46:30');
INSERT INTO `event_bookmarks` VALUES ('8', '6', '27', '2025-08-21 03:36:19');
INSERT INTO `event_bookmarks` VALUES ('9', '9', '68', '2025-09-05 09:31:54');
INSERT INTO `event_bookmarks` VALUES ('10', '7', '68', '2025-09-05 09:32:54');
INSERT INTO `event_bookmarks` VALUES ('11', '7', '69', '2025-09-05 20:39:03');
INSERT INTO `event_bookmarks` VALUES ('12', '16', '254', '2025-09-27 01:26:42');

-- Table structure for `event_comments`
DROP TABLE IF EXISTS `event_comments`;
CREATE TABLE `event_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `event_comments`
INSERT INTO `event_comments` VALUES ('1', '6', '25', 'Chada lagi na', '2025-08-16 01:19:15');
INSERT INTO `event_comments` VALUES ('2', '6', '28', 'Ako sad', '2025-08-17 21:54:14');
INSERT INTO `event_comments` VALUES ('3', '6', '27', 'Apila ko', '2025-08-18 07:13:42');
INSERT INTO `event_comments` VALUES ('6', '7', '69', 'Apil ko', '2025-09-05 20:39:08');
INSERT INTO `event_comments` VALUES ('7', '9', '69', 'HAHAHAA', '2025-09-06 13:27:16');
INSERT INTO `event_comments` VALUES ('8', '17', '157', 'HOHO', '2025-09-09 08:33:20');

-- Table structure for `event_commits`
DROP TABLE IF EXISTS `event_commits`;
CREATE TABLE `event_commits` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `event_id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `event_commits`
INSERT INTO `event_commits` VALUES ('1', '1', '3');
INSERT INTO `event_commits` VALUES ('2', '2', '5');
INSERT INTO `event_commits` VALUES ('3', '1', '5');
INSERT INTO `event_commits` VALUES ('4', '1', '1');

-- Table structure for `event_likes`
DROP TABLE IF EXISTS `event_likes`;
CREATE TABLE `event_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`event_id`,`user_id`),
  KEY `event_id` (`event_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `event_likes`
INSERT INTO `event_likes` VALUES ('1', '6', '25', '2025-08-16 01:28:19');
INSERT INTO `event_likes` VALUES ('3', '6', '28', '2025-08-17 21:54:10');
INSERT INTO `event_likes` VALUES ('4', '6', '27', '2025-08-18 04:57:00');
INSERT INTO `event_likes` VALUES ('5', '7', '66', '2025-09-02 13:08:38');
INSERT INTO `event_likes` VALUES ('6', '8', '66', '2025-09-02 13:17:25');
INSERT INTO `event_likes` VALUES ('7', '9', '66', '2025-09-02 13:17:28');
INSERT INTO `event_likes` VALUES ('8', '7', '68', '2025-09-03 22:37:01');
INSERT INTO `event_likes` VALUES ('10', '10', '68', '2025-09-04 03:44:41');
INSERT INTO `event_likes` VALUES ('11', '9', '68', '2025-09-05 09:31:56');
INSERT INTO `event_likes` VALUES ('12', '7', '69', '2025-09-05 20:39:02');
INSERT INTO `event_likes` VALUES ('13', '9', '69', '2025-09-06 13:27:09');
INSERT INTO `event_likes` VALUES ('14', '17', '157', '2025-09-09 08:33:27');
INSERT INTO `event_likes` VALUES ('15', '16', '157', '2025-09-09 08:33:29');
INSERT INTO `event_likes` VALUES ('16', '15', '157', '2025-09-09 08:33:30');
INSERT INTO `event_likes` VALUES ('17', '17', '158', '2025-09-09 09:09:15');

-- Table structure for `events`
DROP TABLE IF EXISTS `events`;
CREATE TABLE `events` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `content` text NOT NULL,
  `schedule` datetime NOT NULL,
  `banner` text NOT NULL,
  `user_id` int(30) DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `approved` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ft_title_content` (`title`,`content`),
  FULLTEXT KEY `title` (`title`,`content`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `events`
INSERT INTO `events` VALUES ('15', 'ALUMNI HOME COMING', '&lt;p class=&quot;xdj266r x14z9mp xat24cr x1lziwak x1vvkbs x126k92a&quot; style=&quot;margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Calling all proud alumni of MISAMIS ORIENTAL INSTITUTE OF SCIENCE AND TECHNOLOGY (MOIST), INC.!&lt;/p&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;It&amp;#x2019;s time to come home! &lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Join us for the 5th Alumni Homecoming on May 31, 2025, at MOIST, Inc., Sta. Cruz, Cogon, Balingasag.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Let&rsquo;s Reunite, Relive, Reignite the memories that made us who we are.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;&lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistianalumni?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTianAlumni&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/5thhomecoming?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#5thHomecoming&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/reuniterelivereignite?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#ReuniteReliveReignite&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistfamily?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTFamily&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/onceamoistianalwaysamoistian?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#OnceAMOISTianAlwaysAMOISTian&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;&lt;/p&gt;', '2025-09-08 21:00:00', '1757333280_7_event.jpg', '1', '2025-09-08 20:08:45', '1');
INSERT INTO `events` VALUES ('16', 'ALUMNI', '&lt;p class=&quot;xdj266r x14z9mp xat24cr x1lziwak x1vvkbs x126k92a&quot; style=&quot;margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Calling all proud alumni of MISAMIS ORIENTAL INSTITUTE OF SCIENCE AND TECHNOLOGY (MOIST), INC.!&lt;/p&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;It&amp;#x2019;s time to come home! &lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Join us for the 5th Alumni Homecoming on May 31, 2025, at MOIST, Inc., Sta. Cruz, Cogon, Balingasag.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Let&rsquo;s Reunite, Relive, Reignite the memories that made us who we are.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;&lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistianalumni?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTianAlumni&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/5thhomecoming?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#5thHomecoming&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/reuniterelivereignite?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#ReuniteReliveReignite&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistfamily?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTFamily&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/onceamoistianalwaysamoistian?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#OnceAMOISTianAlwaysAMOISTian&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;&lt;/p&gt;', '2025-09-17 20:09:00', '1757333340_2_event.jpg', '1', '2025-09-08 20:09:27', '1');
INSERT INTO `events` VALUES ('17', 'ALUMNI NEW', '&lt;p class=&quot;xdj266r x14z9mp xat24cr x1lziwak x1vvkbs x126k92a&quot; style=&quot;margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Calling all proud alumni of MISAMIS ORIENTAL INSTITUTE OF SCIENCE AND TECHNOLOGY (MOIST), INC.!&lt;/p&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;It&amp;#x2019;s time to come home! &lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Join us for the 5th Alumni Homecoming on May 31, 2025, at MOIST, Inc., Sta. Cruz, Cogon, Balingasag.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;Let&rsquo;s Reunite, Relive, Reignite the memories that made us who we are.&lt;/p&gt;&lt;/p&gt;&lt;p class=&quot;x14z9mp xat24cr x1lziwak x1vvkbs xtlvy1s x126k92a&quot; style=&quot;margin-top: 0.5em; margin-bottom: 0px; overflow-wrap: break-word; white-space-collapse: preserve; font-family: &amp;quot;Segoe UI Historic&amp;quot;, &amp;quot;Segoe UI&amp;quot;, Helvetica, Arial, sans-serif; color: rgb(8, 8, 9); font-size: 15px;&quot;&gt;&lt;p dir=&quot;auto&quot; style=&quot;font-family: inherit;&quot;&gt;&lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistianalumni?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTianAlumni&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/5thhomecoming?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#5thHomecoming&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/reuniterelivereignite?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#ReuniteReliveReignite&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/moistfamily?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#MOISTFamily&lt;/a&gt;&lt;/span&gt; &lt;span class=&quot;html-span xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x1hl2dhg x16tdsg8 x1vvkbs&quot; style=&quot;margin: 0px; text-align: inherit; padding: 0px; overflow-wrap: break-word; font-family: inherit;&quot;&gt;&lt;a attributionsrc=&quot;/privacy_sandbox/comet/register/source/?xt=AZUa8zynXGjooZ9UiHAxFf7yKVUzfz1Bh65k7uXuN-vp8bi6j5c8tl-1K_dAuu9YEadjgtYkgyyn3p_u319l9qwfqVpN6YuYPIdpRNK-jDKHercRTPT3DX2MSiRD2vczzXDqd0N6oflCqLBGQkO35e-YHnfttO33PK9YVYs0S3qwfomZenPEg3uY_uPcRcAOEPnm5uwfsswbMaQJcsJk98qBVIhvZB64cGFXOSEgwrgrNLwTbnyKHF9gQg6K476_OZDA5Erjkz7adMH5Uxw9L65ArYiS9KGxIEAT8W6lPlYKRDrCONj9Wsf-vtn1-gCqUgaJpMwPyuxPrknJ9o_N8XPLAJVVd92llxJl8W8XWWIlSwsWH1rEPWFy-UswV6JHQoIl-qh6CdAhcdI_esVylViOunqqmEvFhCjFUO8nhmIzkl0yhrkeGsHzTc4QP99I0Ir1JNGGIUAWoe4-8cS6BIvLlFbrBMhJlcaATBZshK5n_G2RFoLUIQLJhdyfSMilt8ed4yiP1Hih5ielUErC510K7ojCrNR6nBbhlVLKHcizJ93U8V-6_95_yz8JEmHnCJHnUdyi6hqvtiNpD8iIsOV3cyED2eg9-PzWlmOEl59CHrFEkPy4x7KYEXLdxn-WEVppDFOyt3mvlN0dJqnwYjlfaVixjsQBoFIYlQXDTHghYCnkuBEehYjZS8LQLofon464Z4SC5ra5pWz-6cVsKj8Njw1PaJObyyBl1PAssfhqgeCfwzyij7aBxKdYmsOZfVw5jNoML1apytONwddEt1SKZ4SP8WuEpf94diU-Xg-MdmToDgmZX_6M2F3AEUNIS2jn9adDheu1XUQVPoO7UkCzTi_62-QCW9ijCsDeJes8ZLYkdeYv2agG3H2t3OuDXI28GG5kDnMkjA9vB1y_IgWTWpYXVrluKBwppJ4yOOdVf_DW1ILsSE7m6tL9Yt9CkaGSq5bOS47UAqKtSci38HIMoJXzyba_LR_ubTT0CUXTKdUCBTe2sJlFkfUfgVX7o-7Uno82N4hhIje_8wCsPs-WfwD81QUyRmGJOuirpG8ZLZMGlhZzrQOyHDJD3jEWM1sR-kHQd_xAAPYuxcioSN2fvY7ajxwPx8CcokW7gMj0sCwjLObUODpomGEZfboGx6HZMOG6QhXklGe_z8FnWQSGpKS-6anbBcpp4-hQ12fM-9OOXbyiaGLIja5XDWx_yooIGtE-QjTJeuV5-bBSzQ8f&quot; class=&quot;x1i10hfl xjbqb8w x1ejq31n x18oe1m7 x1sy0etr xstzfhl x972fbf x10w94by x1qhh985 x14e42zd x9f619 x1ypdohk xt0psk2 x3ct3a4 xdj266r x14z9mp xat24cr x1lziwak xexx8yu xyri2b x18d9i69 x1c1uobl x16tdsg8 x1hl2dhg xggy1nq x1a2a7pz xkrqix3 x1sur9pj x1fey0fg x1s688f&quot; href=&quot;https://www.facebook.com/hashtag/onceamoistianalwaysamoistian?__eep__=6&amp;amp;__cft__[0]=AZVneVuSdXe6FmjsZyuP8w0iWt9HwtFekAZuq1Lok5cscLQ612MRG5flXOD82yO5QxPxHD14NO9eXiWsVQBPoNoZvSgSS8-g-KGRvBSC_CFXODMwnXI3VIZfKWsMd4hiC7JTSdE_pqbmPzMiGHAhtZNst0ATccc8cVy-KURW4SJTxw&amp;amp;__tn__=*NK-R&quot; role=&quot;link&quot; tabindex=&quot;0&quot; waprocessedanchor=&quot;true&quot; style=&quot;color: rgb(0, 100, 209); cursor: pointer; text-decoration-line: none; border-width: 0px; margin: 0px; text-align: inherit; border-style: none; padding: 0px; list-style-type: none; outline: none; -webkit-tap-highlight-color: transparent; font-weight: 600; touch-action: manipulation; display: inline; font-family: inherit;&quot;&gt;#OnceAMOISTianAlwaysAMOISTian&lt;/a&gt;&lt;/span&gt;&lt;/p&gt;&lt;/p&gt;', '2025-09-12 20:09:00', '1757333400_3_event.jpg', '1', '2025-09-08 20:10:30', '1');
INSERT INTO `events` VALUES ('18', 'Post by  ', 'KP', '2025-09-09 09:20:14', '', NULL, '2025-09-09 09:20:14', '0');
INSERT INTO `events` VALUES ('19', 'Post by  ', 'HUHU', '2025-09-09 09:22:48', '', NULL, '2025-09-09 09:22:48', '0');

-- Table structure for `forum_comments`
DROP TABLE IF EXISTS `forum_comments`;
CREATE TABLE `forum_comments` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `topic_id` int(30) NOT NULL,
  `comment` text NOT NULL,
  `user_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `forum_comments`
INSERT INTO `forum_comments` VALUES ('1', '3', 'Sample updated Comment', '3', '2020-10-15 15:46:03');
INSERT INTO `forum_comments` VALUES ('5', '0', '', '1', '2020-10-16 09:49:34');
INSERT INTO `forum_comments` VALUES ('6', '1', 'Apply ko', '1', '2025-02-26 14:23:46');
INSERT INTO `forum_comments` VALUES ('7', '1', '&lt;h1&gt;&lt;u&gt;ako nalang apply&lt;/u&gt;&lt;/h1&gt;', '4', '2025-02-26 14:31:39');
INSERT INTO `forum_comments` VALUES ('8', '1', '&lt;span style=&quot;font-size:18px;&quot;&gt;Sge ugma&lt;/span&gt;', '1', '2025-02-26 14:32:45');
INSERT INTO `forum_comments` VALUES ('9', '1', '&lt;h2&gt;charliezapicoabejo123@gmail.com e contak ko&lt;/h2&gt;', '5', '2025-02-26 15:50:18');
INSERT INTO `forum_comments` VALUES ('10', '1', 'sge hulat ko', '1', '2025-02-26 15:50:37');
INSERT INTO `forum_comments` VALUES ('11', '1', 'pwedi mag apply ko tig ihaw sa babooy manok lang', '6', '2025-02-26 16:00:09');
INSERT INTO `forum_comments` VALUES ('12', '1', 'sge ikaw&amp;nbsp; akong ihawon una', '1', '2025-02-26 16:01:23');
INSERT INTO `forum_comments` VALUES ('13', '1', '&lt;h1&gt;please klnfg ba&lt;/h1&gt;', '1', '2025-02-26 16:02:12');
INSERT INTO `forum_comments` VALUES ('14', '1', 'ML NASAD TA', '1', '2025-02-27 21:35:50');

-- Table structure for `forum_topics`
DROP TABLE IF EXISTS `forum_topics`;
CREATE TABLE `forum_topics` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `user_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `forum_topics`
INSERT INTO `forum_topics` VALUES ('4', 'Job Post Manager', '&lt;p&gt;CharliePogi is looking for a dedicated &lt;b data-start=&quot;289&quot; data-end=&quot;309&quot;&gt;Job Post Manager&lt;/b&gt; to oversee the creation, management, and optimization of job postings across various platforms. In this role, you will be responsible for drafting clear and engaging job descriptions, ensuring job posts align with the company&amp;#x2019;s recruitment strategy, and monitoring the performance of each post.&lt;/p&gt;&lt;p data-start=&quot;725&quot; data-end=&quot;763&quot; data-is-last-node=&quot;&quot; data-is-only-node=&quot;&quot;&gt;&lt;/p&gt;', '1', '2020-10-16 08:31:45');

-- Table structure for `gallery`
DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `about` text NOT NULL,
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `gallery`
INSERT INTO `gallery` VALUES ('10', 'Alumni homecoming is a special occasion that brings together former students, faculty, and staff to celebrate shared memories and achievements.', '2025-02-27 22:39:27');

-- Table structure for `homecoming_rsvp`
DROP TABLE IF EXISTS `homecoming_rsvp`;
CREATE TABLE `homecoming_rsvp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumni_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `response` enum('attending','not_attending') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  UNIQUE KEY `unique_alumni` (`alumni_id`),
  UNIQUE KEY `unique_token` (`token`),
  KEY `idx_alumni_id` (`alumni_id`),
  KEY `idx_response` (`response`),
  CONSTRAINT `homecoming_rsvp_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `homecoming_rsvp`
INSERT INTO `homecoming_rsvp` VALUES ('2', '254', 'f488ab2ad43e34f74437819df23d717b89eafbbc2990f06a0d504b31be540620', 'attending', '2025-09-26 20:01:58', '2025-09-27 08:48:41', '2025-09-27 08:48:41');

-- Table structure for `id_release_log`
DROP TABLE IF EXISTS `id_release_log`;
CREATE TABLE `id_release_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumni_id` int(11) NOT NULL,
  `release_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `released_by` varchar(100) DEFAULT NULL,
  `release_method` enum('print_button','ctrl_p','manual') DEFAULT 'print_button',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_alumni_release` (`alumni_id`,`release_date`),
  CONSTRAINT `id_release_log_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `id_release_log`
INSERT INTO `id_release_log` VALUES ('1', '254', '2025-09-26 20:00:45', 'test', 'print_button', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36');
INSERT INTO `id_release_log` VALUES ('2', '254', '2025-09-26 21:10:06', 'test', 'print_button', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36');

-- Table structure for `login_logs`
DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `action` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `email_idx` (`email`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `majors`
DROP TABLE IF EXISTS `majors`;
CREATE TABLE `majors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` int(11) NOT NULL,
  `major` varchar(100) NOT NULL,
  `about` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `majors_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `majors`
INSERT INTO `majors` VALUES ('1', '2', 'English', 'Bachelor of Secondary Education Major in English');
INSERT INTO `majors` VALUES ('2', '2', 'Mathematics', 'Bachelor of Secondary Education Major in Mathematics');
INSERT INTO `majors` VALUES ('3', '2', 'Social Studies', 'Bachelor of Secondary Education Major in Social Studies');
INSERT INTO `majors` VALUES ('4', '2', 'Values Education', 'Bachelor of Secondary Education Major in Values Education');

-- Table structure for `otp_history`
DROP TABLE IF EXISTS `otp_history`;
CREATE TABLE `otp_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` datetime DEFAULT NULL,
  `status` enum('success','failed','expired') DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`(191))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `otp_verifications`
DROP TABLE IF EXISTS `otp_verifications`;
CREATE TABLE `otp_verifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `email` (`email`(191),`otp`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=174 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `otp_verifications`
INSERT INTO `otp_verifications` VALUES ('1', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('2', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('3', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('4', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('5', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('6', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('7', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('8', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('9', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('10', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('11', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('12', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('13', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('14', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('15', 'tineemaeeyy@gmail.com', NULL, '982020', '2025-08-17 21:57:49', '1', '2025-08-17 21:52:49');
INSERT INTO `otp_verifications` VALUES ('16', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('17', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('18', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('19', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('20', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('21', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('22', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('23', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('24', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('25', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('26', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('27', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('28', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('29', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('30', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('31', 'tineemaeeyy@gmail.com', NULL, '313477', '2025-08-19 01:03:35', '1', '2025-08-19 00:58:35');
INSERT INTO `otp_verifications` VALUES ('32', 'tineemaeeyy@gmail.com', NULL, '974774', '2025-08-19 01:03:39', '1', '2025-08-19 00:58:39');
INSERT INTO `otp_verifications` VALUES ('33', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('34', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('35', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('36', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('37', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('38', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('39', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('40', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('41', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('42', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('43', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('44', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('45', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('46', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('47', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('48', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('49', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('50', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('51', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('52', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('53', 'marialanievalmoria009@gmail.com', NULL, '841195', '2025-08-29 02:55:03', '1', '2025-08-29 02:50:03');
INSERT INTO `otp_verifications` VALUES ('54', 'marialanievalmoria009@gmail.com', NULL, '143918', '2025-08-29 03:31:34', '1', '2025-08-29 03:26:34');
INSERT INTO `otp_verifications` VALUES ('55', 'marialanievalmoria009@gmail.com', NULL, '908867', '2025-08-29 04:02:50', '1', '2025-08-29 03:57:50');
INSERT INTO `otp_verifications` VALUES ('56', 'marialanievalmoria009@gmail.com', NULL, '223502', '2025-08-29 04:07:27', '1', '2025-08-29 04:02:27');
INSERT INTO `otp_verifications` VALUES ('57', 'marialanievalmoria009@gmail.com', NULL, '556848', '2025-08-29 04:23:02', '1', '2025-08-29 04:18:02');
INSERT INTO `otp_verifications` VALUES ('58', 'marialanievalmoria009@gmail.com', NULL, '116778', '2025-08-29 04:31:44', '1', '2025-08-29 04:26:44');
INSERT INTO `otp_verifications` VALUES ('59', 'marialanievalmoria009@gmail.com', NULL, '153321', '2025-08-29 04:35:57', '1', '2025-08-29 04:30:57');
INSERT INTO `otp_verifications` VALUES ('60', 'capstonee2@gmail.com', NULL, '170842', '2025-09-02 09:52:14', '1', '2025-09-02 09:47:14');
INSERT INTO `otp_verifications` VALUES ('62', '09856122843', NULL, '748532', '2025-09-02 03:39:13', '0', '2025-09-02 09:34:13');
INSERT INTO `otp_verifications` VALUES ('63', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '492697', '2025-09-02 10:01:38', '1', '2025-09-02 09:56:38');
INSERT INTO `otp_verifications` VALUES ('64', '09856122843', '09856122843', '891972', '2025-09-02 10:04:41', '0', '2025-09-02 09:59:41');
INSERT INTO `otp_verifications` VALUES ('65', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '647804', '2025-09-02 10:05:40', '1', '2025-09-02 10:00:40');
INSERT INTO `otp_verifications` VALUES ('66', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '832023', '2025-09-02 10:18:48', '1', '2025-09-02 10:13:48');
INSERT INTO `otp_verifications` VALUES ('67', '09856122843', '09856122843', '237272', '2025-09-02 10:20:05', '1', '2025-09-02 10:15:05');
INSERT INTO `otp_verifications` VALUES ('68', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '403426', '2025-09-02 10:21:03', '1', '2025-09-02 10:16:03');
INSERT INTO `otp_verifications` VALUES ('69', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '859340', '2025-09-02 10:28:57', '1', '2025-09-02 10:23:57');
INSERT INTO `otp_verifications` VALUES ('70', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '455531', '2025-09-02 10:34:11', '1', '2025-09-02 10:29:11');
INSERT INTO `otp_verifications` VALUES ('71', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '494168', '2025-09-02 10:39:23', '1', '2025-09-02 10:34:23');
INSERT INTO `otp_verifications` VALUES ('72', 'paulinmgrayos@gmail.com', 'paulinmgrayos@gmail.', '813598', '2025-09-02 10:48:04', '1', '2025-09-02 10:43:04');
INSERT INTO `otp_verifications` VALUES ('73', 'paulinmgrayos@gmail.com', 'paulinmgrayos@gmail.', '470019', '2025-09-02 10:48:47', '1', '2025-09-02 10:43:47');
INSERT INTO `otp_verifications` VALUES ('74', 'paulinmgrayos@gmail.com', 'paulinmgrayos@gmail.', '880596', '2025-09-02 10:49:32', '1', '2025-09-02 10:44:32');
INSERT INTO `otp_verifications` VALUES ('75', 'paulinmgrayos@gmail.com', 'paulinmgrayos@gmail.', '542650', '2025-09-02 10:57:12', '1', '2025-09-02 10:52:12');
INSERT INTO `otp_verifications` VALUES ('76', '09856122843', '09856122843', '270795', '2025-09-02 10:57:56', '1', '2025-09-02 10:52:56');
INSERT INTO `otp_verifications` VALUES ('77', '09856122843', '09856122843', '831178', '2025-09-02 10:58:31', '1', '2025-09-02 10:53:31');
INSERT INTO `otp_verifications` VALUES ('78', 'paulinmgrayos@gmail.com', NULL, '130964', '2025-09-02 12:50:19', '1', '2025-09-02 12:45:19');
INSERT INTO `otp_verifications` VALUES ('79', 'paulinmgrayos@gmail.com', NULL, '199307', '2025-09-02 13:21:07', '1', '2025-09-02 13:16:07');
INSERT INTO `otp_verifications` VALUES ('80', 'paulinmgrayos@gmail.com', NULL, '431467', '2025-09-02 13:33:06', '1', '2025-09-02 13:28:06');
INSERT INTO `otp_verifications` VALUES ('81', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '239821', '2025-09-02 19:02:23', '1', '2025-09-02 18:57:23');
INSERT INTO `otp_verifications` VALUES ('82', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '628745', '2025-09-02 19:03:26', '1', '2025-09-02 18:58:26');
INSERT INTO `otp_verifications` VALUES ('83', '09856122843', '09856122843', '223221', '2025-09-02 19:04:14', '1', '2025-09-02 18:59:14');
INSERT INTO `otp_verifications` VALUES ('84', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '787574', '2025-09-02 19:09:10', '1', '2025-09-02 19:04:10');
INSERT INTO `otp_verifications` VALUES ('85', 'capstonee2@gmail.com', NULL, '789028', '2025-09-02 19:12:18', '1', '2025-09-02 19:07:18');
INSERT INTO `otp_verifications` VALUES ('86', '09856122843', '09856122843', '678235', '2025-09-03 21:04:25', '1', '2025-09-03 20:59:25');
INSERT INTO `otp_verifications` VALUES ('87', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '918653', '2025-09-03 21:05:03', '1', '2025-09-03 21:00:03');
INSERT INTO `otp_verifications` VALUES ('88', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '780185', '2025-09-03 22:34:29', '1', '2025-09-03 22:29:29');
INSERT INTO `otp_verifications` VALUES ('89', '09856122843', '09856122843', '201090', '2025-09-03 22:35:24', '0', '2025-09-03 22:30:24');
INSERT INTO `otp_verifications` VALUES ('90', '09856122843', '09856122843', '543729', '2025-09-03 22:36:31', '1', '2025-09-03 22:31:31');
INSERT INTO `otp_verifications` VALUES ('91', 'capstonee2@gmail.com', NULL, '806294', '2025-09-03 22:40:50', '1', '2025-09-03 22:35:50');
INSERT INTO `otp_verifications` VALUES ('92', 'capstonee2@gmail.com', NULL, '460301', '2025-09-03 22:50:29', '1', '2025-09-03 22:45:29');
INSERT INTO `otp_verifications` VALUES ('93', 'capstonee2@gmail.com', NULL, '158577', '2025-09-03 22:56:15', '1', '2025-09-03 22:51:15');
INSERT INTO `otp_verifications` VALUES ('94', 'capstonee2@gmail.com', NULL, '495275', '2025-09-03 23:02:12', '1', '2025-09-03 22:57:12');
INSERT INTO `otp_verifications` VALUES ('95', 'capstonee2@gmail.com', NULL, '159038', '2025-09-03 23:05:59', '1', '2025-09-03 23:00:59');
INSERT INTO `otp_verifications` VALUES ('96', 'capstonee2@gmail.com', NULL, '608897', '2025-09-03 23:08:29', '1', '2025-09-03 23:03:29');
INSERT INTO `otp_verifications` VALUES ('97', 'capstonee2@gmail.com', NULL, '715718', '2025-09-03 23:17:41', '1', '2025-09-03 23:12:41');
INSERT INTO `otp_verifications` VALUES ('98', 'capstonee2@gmail.com', NULL, '861205', '2025-09-03 23:36:18', '1', '2025-09-03 23:31:18');
INSERT INTO `otp_verifications` VALUES ('99', 'capstonee2@gmail.com', NULL, '253163', '2025-09-04 00:15:15', '1', '2025-09-04 00:10:15');
INSERT INTO `otp_verifications` VALUES ('100', 'capstonee2@gmail.com', NULL, '586743', '2025-09-04 00:35:02', '1', '2025-09-04 00:30:02');
INSERT INTO `otp_verifications` VALUES ('101', 'capstonee2@gmail.com', NULL, '484415', '2025-09-04 00:56:18', '1', '2025-09-04 00:51:18');
INSERT INTO `otp_verifications` VALUES ('102', 'capstonee2@gmail.com', NULL, '967743', '2025-09-04 01:04:16', '1', '2025-09-04 00:59:16');
INSERT INTO `otp_verifications` VALUES ('103', 'capstonee2@gmail.com', NULL, '824779', '2025-09-04 01:14:40', '1', '2025-09-04 01:09:40');
INSERT INTO `otp_verifications` VALUES ('104', 'capstonee2@gmail.com', NULL, '292951', '2025-09-04 01:31:27', '1', '2025-09-04 01:26:27');
INSERT INTO `otp_verifications` VALUES ('105', 'capstonee2@gmail.com', NULL, '626196', '2025-09-04 02:44:06', '1', '2025-09-04 02:39:06');
INSERT INTO `otp_verifications` VALUES ('106', 'capstonee2@gmail.com', NULL, '947619', '2025-09-04 02:56:12', '1', '2025-09-04 02:51:12');
INSERT INTO `otp_verifications` VALUES ('107', 'capstonee2@gmail.com', NULL, '266801', '2025-09-04 03:11:20', '1', '2025-09-04 03:06:20');
INSERT INTO `otp_verifications` VALUES ('108', 'capstonee2@gmail.com', NULL, '305219', '2025-09-04 03:38:48', '1', '2025-09-04 03:33:48');
INSERT INTO `otp_verifications` VALUES ('109', 'capstonee2@gmail.com', NULL, '899146', '2025-09-04 13:40:07', '1', '2025-09-04 13:35:07');
INSERT INTO `otp_verifications` VALUES ('110', 'capstonee2@gmail.com', NULL, '860708', '2025-09-04 20:45:57', '1', '2025-09-04 20:40:57');
INSERT INTO `otp_verifications` VALUES ('111', 'capstonee2@gmail.com', NULL, '518973', '2025-09-04 20:46:57', '1', '2025-09-04 20:41:57');
INSERT INTO `otp_verifications` VALUES ('112', 'capstonee2@gmail.com', NULL, '591019', '2025-09-04 22:16:08', '1', '2025-09-04 22:11:08');
INSERT INTO `otp_verifications` VALUES ('113', 'capstonee2@gmail.com', NULL, '484265', '2025-09-05 09:33:46', '1', '2025-09-05 09:28:46');
INSERT INTO `otp_verifications` VALUES ('114', '09856122843', '09856122843', '389409', '2025-09-05 13:56:24', '0', '2025-09-05 13:51:24');
INSERT INTO `otp_verifications` VALUES ('115', '09856122843', '09856122843', '770436', '2025-09-05 13:57:47', '0', '2025-09-05 13:52:47');
INSERT INTO `otp_verifications` VALUES ('116', '09856122843', '09856122843', '140047', '2025-09-05 13:59:11', '0', '2025-09-05 13:54:11');
INSERT INTO `otp_verifications` VALUES ('117', '09856122843', '09856122843', '990139', '2025-09-05 14:00:12', '0', '2025-09-05 13:55:12');
INSERT INTO `otp_verifications` VALUES ('118', '09856122843', '09856122843', '511082', '2025-09-05 14:01:44', '0', '2025-09-05 13:56:44');
INSERT INTO `otp_verifications` VALUES ('119', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '945754', '2025-09-05 18:49:20', '1', '2025-09-05 18:44:20');
INSERT INTO `otp_verifications` VALUES ('120', '09856122843', '09856122843', '139800', '2025-09-05 18:52:49', '0', '2025-09-05 18:47:49');
INSERT INTO `otp_verifications` VALUES ('121', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '969726', '2025-09-05 19:48:17', '1', '2025-09-05 19:43:17');
INSERT INTO `otp_verifications` VALUES ('122', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '189658', '2025-09-05 20:12:51', '1', '2025-09-05 20:07:51');
INSERT INTO `otp_verifications` VALUES ('123', 'capstonee2@gmail.com', NULL, '804396', '2025-09-05 20:17:49', '1', '2025-09-05 20:12:49');
INSERT INTO `otp_verifications` VALUES ('124', 'capstonee2@gmail.com', NULL, '943906', '2025-09-05 20:43:05', '1', '2025-09-05 20:38:05');
INSERT INTO `otp_verifications` VALUES ('125', '09856122843', '09856122843', '568314', '2025-09-06 13:16:24', '0', '2025-09-06 13:11:24');
INSERT INTO `otp_verifications` VALUES ('126', '09856122843', '09856122843', '543435', '2025-09-06 13:17:39', '0', '2025-09-06 13:12:39');
INSERT INTO `otp_verifications` VALUES ('127', '09856122843', '09856122843', '219831', '2025-09-06 13:19:13', '0', '2025-09-06 13:14:13');
INSERT INTO `otp_verifications` VALUES ('128', 'capstonee2@gmail.com', NULL, '675102', '2025-09-06 13:29:56', '1', '2025-09-06 13:24:56');
INSERT INTO `otp_verifications` VALUES ('129', 'capstonee2@gmail.com', NULL, '652367', '2025-09-06 13:31:27', '1', '2025-09-06 13:26:27');
INSERT INTO `otp_verifications` VALUES ('130', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '460242', '2025-09-06 13:38:03', '1', '2025-09-06 13:33:03');
INSERT INTO `otp_verifications` VALUES ('131', 'moseslozano20@gmail.com', 'moseslozano20@gmail.', '591725', '2025-09-06 13:48:17', '1', '2025-09-06 13:43:17');
INSERT INTO `otp_verifications` VALUES ('132', 'moseslozano20@gmail.com', NULL, '519275', '2025-09-06 13:53:30', '1', '2025-09-06 13:48:30');
INSERT INTO `otp_verifications` VALUES ('133', 'moseslozano20@gmail.com', NULL, '152112', '2025-09-06 13:54:31', '0', '2025-09-06 13:49:31');
INSERT INTO `otp_verifications` VALUES ('134', 'capstonee2@gmail.com', NULL, '976563', '2025-09-06 13:55:34', '1', '2025-09-06 13:50:34');
INSERT INTO `otp_verifications` VALUES ('135', 'bkimleye@gmail.com', 'bkimleye@gmail.com', '374367', '2025-09-06 14:12:40', '0', '2025-09-06 14:07:40');
INSERT INTO `otp_verifications` VALUES ('136', 'bkimleye@gmail.com', 'bkimleye@gmail.com', '487833', '2025-09-06 14:13:54', '1', '2025-09-06 14:08:54');
INSERT INTO `otp_verifications` VALUES ('137', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '630265', '2025-09-09 07:15:51', '1', '2025-09-09 07:10:51');
INSERT INTO `otp_verifications` VALUES ('138', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '698179', '2025-09-09 07:17:25', '1', '2025-09-09 07:12:25');
INSERT INTO `otp_verifications` VALUES ('139', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '814426', '2025-09-09 07:22:08', '1', '2025-09-09 07:17:08');
INSERT INTO `otp_verifications` VALUES ('140', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '867732', '2025-09-09 07:27:22', '1', '2025-09-09 07:22:22');
INSERT INTO `otp_verifications` VALUES ('141', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '551532', '2025-09-09 07:41:49', '1', '2025-09-09 07:36:49');
INSERT INTO `otp_verifications` VALUES ('142', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '466585', '2025-09-09 07:50:27', '1', '2025-09-09 07:45:27');
INSERT INTO `otp_verifications` VALUES ('143', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '472634', '2025-09-09 08:01:14', '1', '2025-09-09 07:56:14');
INSERT INTO `otp_verifications` VALUES ('144', 'capstonee2@gmail.com', NULL, '107227', '2025-09-09 08:19:52', '1', '2025-09-09 08:14:52');
INSERT INTO `otp_verifications` VALUES ('145', 'capstonee2@gmail.com', NULL, '326770', '2025-09-09 09:03:34', '1', '2025-09-09 08:58:34');
INSERT INTO `otp_verifications` VALUES ('146', 'capstonee2@gmail.com', NULL, '715427', '2025-09-09 09:07:32', '1', '2025-09-09 09:02:32');
INSERT INTO `otp_verifications` VALUES ('147', '09677314559', '09677314559', '441907', '2025-09-09 09:08:58', '0', '2025-09-09 09:03:58');
INSERT INTO `otp_verifications` VALUES ('148', '09677314559', '09677314559', '273717', '2025-09-09 09:10:05', '1', '2025-09-09 09:05:05');
INSERT INTO `otp_verifications` VALUES ('149', 'kennethladra3@gmail.com', NULL, '240065', '2025-09-09 09:13:35', '1', '2025-09-09 09:08:35');
INSERT INTO `otp_verifications` VALUES ('150', 'capstonee2@gmail.com', NULL, '664399', '2025-09-09 10:27:07', '1', '2025-09-09 10:22:07');
INSERT INTO `otp_verifications` VALUES ('151', 'capstonee2@gmail.com', NULL, '983683', '2025-09-09 10:57:06', '1', '2025-09-09 10:52:06');
INSERT INTO `otp_verifications` VALUES ('152', 'capstonee2@gmail.com', NULL, '250772', '2025-09-09 11:02:36', '1', '2025-09-09 10:57:36');
INSERT INTO `otp_verifications` VALUES ('153', '09856122843', '09856122843', '446047', '2025-09-09 13:45:35', '0', '2025-09-09 13:40:35');
INSERT INTO `otp_verifications` VALUES ('154', 'capstonee2@gmail.com', NULL, '159823', '2025-09-09 14:43:00', '1', '2025-09-09 14:38:00');
INSERT INTO `otp_verifications` VALUES ('155', '09856122843', '09856122843', '810064', '2025-09-09 14:47:34', '1', '2025-09-09 14:42:34');
INSERT INTO `otp_verifications` VALUES ('156', 'ampomary01@gmail.com', NULL, '750189', '2025-09-09 14:55:09', '1', '2025-09-09 14:50:09');
INSERT INTO `otp_verifications` VALUES ('157', 'capstonee2@gmail.com', NULL, '904545', '2025-09-09 15:18:18', '1', '2025-09-09 15:13:18');
INSERT INTO `otp_verifications` VALUES ('158', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '868819', '2025-09-20 00:06:33', '1', '2025-09-20 00:01:33');
INSERT INTO `otp_verifications` VALUES ('159', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '953367', '2025-09-20 00:10:23', '1', '2025-09-20 00:05:23');
INSERT INTO `otp_verifications` VALUES ('160', 'capstonee2@gmail.com', NULL, '350913', '2025-09-20 00:14:14', '1', '2025-09-20 00:09:15');
INSERT INTO `otp_verifications` VALUES ('161', 'capstonee2@gmail.com', NULL, '357881', '2025-09-21 12:04:15', '1', '2025-09-21 11:59:15');
INSERT INTO `otp_verifications` VALUES ('162', 'capstonee2@gmail.com', NULL, '552806', '2025-09-21 12:17:24', '1', '2025-09-21 12:12:24');
INSERT INTO `otp_verifications` VALUES ('163', 'capstonee2@gmail.com', 'capstonee2@gmail.com', '530456', '2025-09-23 00:07:58', '1', '2025-09-23 00:02:58');
INSERT INTO `otp_verifications` VALUES ('164', 'capstonee2@gmail.com', NULL, '151311', '2025-09-23 00:27:20', '1', '2025-09-23 00:22:20');
INSERT INTO `otp_verifications` VALUES ('165', 'capstonee2@gmail.com', NULL, '679010', '2025-09-23 00:35:59', '1', '2025-09-23 00:30:59');
INSERT INTO `otp_verifications` VALUES ('166', 'capstonee2@gmail.com', NULL, '786612', '2025-09-26 20:54:21', '1', '2025-09-26 20:49:21');
INSERT INTO `otp_verifications` VALUES ('167', 'capstonee2@gmail.com', NULL, '100655', '2025-09-26 21:21:30', '1', '2025-09-26 21:16:30');
INSERT INTO `otp_verifications` VALUES ('168', 'capstonee2@gmail.com', NULL, '851728', '2025-09-27 15:53:35', '1', '2025-09-27 15:48:35');
INSERT INTO `otp_verifications` VALUES ('169', 'capstonee2@gmail.com', NULL, '297954', '2025-09-29 19:41:18', '1', '2025-09-29 19:36:18');
INSERT INTO `otp_verifications` VALUES ('170', 'capstonee2@gmail.com', NULL, '628371', '2025-09-29 21:43:55', '1', '2025-09-29 21:38:55');
INSERT INTO `otp_verifications` VALUES ('171', 'capstonee2@gmail.com', NULL, '594188', '2025-09-29 21:55:36', '1', '2025-09-29 21:50:36');
INSERT INTO `otp_verifications` VALUES ('172', 'capstonee2@gmail.com', NULL, '585629', '2025-09-30 02:27:01', '1', '2025-09-30 02:22:01');
INSERT INTO `otp_verifications` VALUES ('173', 'capstonee2@gmail.com', NULL, '132461', '2025-10-18 09:20:46', '1', '2025-10-18 09:15:46');

-- Table structure for `password_resets`
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumni_id` varchar(50) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `password_resets`
INSERT INTO `password_resets` VALUES ('1', 'CC-2507', 'capstonee2@gmail.com', '544909', '2025-09-21 11:52:28', '0', '2025-09-21 11:47:28');
INSERT INTO `password_resets` VALUES ('2', 'CC-2507', 'capstonee2@gmail.com', '501290', '2025-09-21 11:52:33', '1', '2025-09-21 11:47:33');
INSERT INTO `password_resets` VALUES ('3', 'CC-2507', '09856122843', '304135', '2025-09-21 11:53:47', '0', '2025-09-21 11:48:47');
INSERT INTO `password_resets` VALUES ('4', 'CC-2507', 'capstonee2@gmail.com', '195447', '2025-09-21 11:56:33', '1', '2025-09-21 11:51:33');
INSERT INTO `password_resets` VALUES ('5', 'CC-2507', '09856122843', '474396', '2025-09-21 12:01:37', '0', '2025-09-21 11:56:37');
INSERT INTO `password_resets` VALUES ('6', 'CC-2507', 'capstonee2@gmail.com', '926113', '2025-09-21 12:02:42', '1', '2025-09-21 11:57:42');
INSERT INTO `password_resets` VALUES ('7', 'CC-2507', '09856122843', '384184', '2025-09-21 12:12:40', '1', '2025-09-21 12:07:40');
INSERT INTO `password_resets` VALUES ('8', 'CC-2507', '09856122843', '154652', '2025-09-21 12:16:42', '1', '2025-09-21 12:11:42');
INSERT INTO `password_resets` VALUES ('9', 'CC-2507', 'capstonee2@gmail.com', '178483', '2025-09-21 12:25:07', '1', '2025-09-21 12:20:07');
INSERT INTO `password_resets` VALUES ('10', 'CC-2507', 'capstonee2@gmail.com', '505677', '2025-09-21 16:03:51', '1', '2025-09-21 15:58:51');
INSERT INTO `password_resets` VALUES ('11', 'CC-2507', '09856122843', '159292', '2025-09-21 16:04:28', '1', '2025-09-21 15:59:28');
INSERT INTO `password_resets` VALUES ('12', 'CC-2507', 'capstonee2@gmail.com', '905948', '2025-09-26 20:52:35', '1', '2025-09-26 20:47:35');
INSERT INTO `password_resets` VALUES ('13', 'CC-2507', 'capstonee2@gmail.com', '826749', '2025-09-29 19:39:20', '1', '2025-09-29 19:34:20');
INSERT INTO `password_resets` VALUES ('14', 'CC-2507', 'capstonee2@gmail.com', '626636', '2025-10-18 09:37:31', '1', '2025-10-18 09:32:31');

-- Table structure for `post_comments`
DROP TABLE IF EXISTS `post_comments`;
CREATE TABLE `post_comments` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `post_id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `comment` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `post_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `user_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `post_likes`
DROP TABLE IF EXISTS `post_likes`;
CREATE TABLE `post_likes` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `post_id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_post_user` (`post_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `user_posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `privacy_agreements`
DROP TABLE IF EXISTS `privacy_agreements`;
CREATE TABLE `privacy_agreements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `agree_date` datetime NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `privacy_agreements_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `remember_tokens`
DROP TABLE IF EXISTS `remember_tokens`;
CREATE TABLE `remember_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumni_id` int(11) NOT NULL,
  `token` varchar(128) NOT NULL,
  `expiry` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `alumni_id` (`alumni_id`),
  CONSTRAINT `remember_tokens_ibfk_1` FOREIGN KEY (`alumni_id`) REFERENCES `alumnus_bio` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for `strands`
DROP TABLE IF EXISTS `strands`;
CREATE TABLE `strands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `strands`
INSERT INTO `strands` VALUES ('1', 'STEM', 'Science, Technology, Engineering, and Mathematics');
INSERT INTO `strands` VALUES ('2', 'HUMSS', 'Humanities and Social Sciences');
INSERT INTO `strands` VALUES ('3', 'ABM', 'Accountancy, Business, and Management');
INSERT INTO `strands` VALUES ('4', 'GAS', 'General Academic Strand');
INSERT INTO `strands` VALUES ('5', 'TVL', 'Technical-Vocational-Livelihood');

-- Table structure for `system_settings`
DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `email` varchar(200) NOT NULL,
  `contact` varchar(20) NOT NULL,
  `cover_img` text NOT NULL,
  `about_content` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `system_settings`
INSERT INTO `system_settings` VALUES ('1', 'MOIST ALUMNI TRACKING SYSTEM', 'capstonee2@gmail.com', '09856122843', '1740794460_1740717360_1602738120_pngtree-purple-hd-business-banner-image_5493.jpg', '&lt;p style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;/p&gt;&lt;article class=&quot;w-full text-token-text-primary focus-visible:outline-2 focus-visible:outline-offset-[-4px]&quot; dir=&quot;auto&quot; data-testid=&quot;conversation-turn-3&quot; data-scroll-anchor=&quot;true&quot;&gt;&lt;p class=&quot;m-auto text-base py-[18px] px-6&quot;&gt;&lt;/p&gt;&lt;p class=&quot;mx-auto flex flex-1 text-base gap-4 md:gap-5 lg:gap-6 md:max-w-3xl&quot;&gt;&lt;/p&gt;&lt;p class=&quot;group/conversation-turn relative flex w-full min-w-0 flex-col agent-turn @xs/thread:px-0 @sm/thread:px-1.5 @md/thread:px-4&quot;&gt;&lt;/p&gt;&lt;p class=&quot;flex-col gap-1 md:gap-3&quot;&gt;&lt;/p&gt;&lt;p class=&quot;flex max-w-full flex-col flex-grow&quot;&gt;&lt;/p&gt;&lt;p data-message-author-role=&quot;assistant&quot; data-message-id=&quot;25acc943-3923-44bf-ace9-29e39872e634&quot; dir=&quot;auto&quot; class=&quot;min-h-8 text-message relative flex w-full flex-col items-end gap-2 whitespace-normal break-words text-start [.text-message+&amp;amp;]:mt-5&quot; data-message-model-slug=&quot;gpt-4o-mini&quot;&gt;&lt;/p&gt;&lt;p class=&quot;flex w-full flex-col gap-1 empty:hidden first:pt-[3px]&quot;&gt;&lt;/p&gt;&lt;p class=&quot;markdown prose w-full break-words dark:prose-invert light&quot;&gt;&lt;/p&gt;&lt;p data-start=&quot;676&quot; data-end=&quot;1165&quot; data-is-last-node=&quot;&quot; data-is-only-node=&quot;&quot;&gt;I am developing the &lt;b data-start=&quot;696&quot; data-end=&quot;790&quot;&gt;MOIST (Misamis Oriental Institute of Science and Technology School Alumni Tracking System)&lt;/b&gt;. This system is designed to help track and manage alumni data, making it easier for the school to stay connected with former students, share updates, and facilitate alumni activities. By implementing MOIST, the school can efficiently maintain a robust alumni network while also offering a platform for alumni to engage with one another and contribute to the school&rsquo;s growth.&lt;/p&gt;&lt;/article&gt;&lt;p style=&quot;text-align: center; background: transparent; position: relative;&quot;&gt;&lt;br&gt;&lt;/p&gt;&lt;p&gt;&lt;/p&gt;');

-- Table structure for `user_comments`
DROP TABLE IF EXISTS `user_comments`;
CREATE TABLE `user_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User who commented',
  `target_user_id` int(11) NOT NULL COMMENT 'User being commented on',
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_target_user` (`target_user_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `user_likes`
DROP TABLE IF EXISTS `user_likes`;
CREATE TABLE `user_likes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'User who liked',
  `target_user_id` int(11) NOT NULL COMMENT 'User being liked',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`user_id`,`target_user_id`),
  KEY `idx_target_user` (`target_user_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table structure for `user_posts`
DROP TABLE IF EXISTS `user_posts`;
CREATE TABLE `user_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `post_type` enum('text','image') NOT NULL DEFAULT 'text',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `user_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data for table `user_posts`
INSERT INTO `user_posts` VALUES ('1', '1', 'Just finished an amazing project at work! 🚀 The feeling of accomplishment when you see your code running smoothly is unbeatable. #DeveloperLife #Success', NULL, 'text', '2025-09-26 20:15:26', '2025-09-27 01:15:26');

-- Table structure for `users`
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 4 COMMENT '1=Admin,2=Alumni officer, 3= alumnus, 4=registrar',
  `auto_generated_pass` text NOT NULL,
  `alumnus_id` int(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data for table `users`
INSERT INTO `users` VALUES ('1', 'Admin', 'admin', '21232f297a57a5a743894a0e4a801fc3', '1', '', '0');
INSERT INTO `users` VALUES ('23', 'test', 'test', '098f6bcd4621d373cade4e832627b4f6', '4', '', '0');
INSERT INTO `users` VALUES ('34', 'Charlie CHARLIEJAMES', 'Ca@gmail.com', '7c0e9a8857289bd39512723e5c3010a4', '3', '', '11');

SET FOREIGN_KEY_CHECKS=1;
