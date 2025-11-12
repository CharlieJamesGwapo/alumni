-- Create the alumni_ids table
CREATE TABLE `alumni_ids` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alumni_id` varchar(50) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `suffixname` varchar(20) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `gender` varchar(20) NOT NULL,
  `batch` year(4) NOT NULL,
  `course_id` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `alumni_id` (`alumni_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `alumni_ids_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert your actual data
INSERT INTO `alumni_ids` (`alumni_id`, `lastname`, `firstname`, `middlename`, `suffixname`, `birthdate`, `gender`, `batch`, `course_id`, `date_created`) VALUES
('CC-2025', 'Ampo', 'Mary', 'Anne', NULL, '2000-05-15', 'Female', 2020, 1, '2025-08-09 21:44:19'),
('CC-2022', 'James', 'Charlie', NULL, NULL, '1999-07-10', 'Male', 2020, 1, '2025-08-09 21:44:19');
