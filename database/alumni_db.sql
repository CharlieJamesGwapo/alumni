-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2025 at 05:33 AM
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
-- Table structure for table `alumnus_bio`
--

CREATE TABLE `alumnus_bio` (
  `id` int(30) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `middlename` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `suffixname` varchar(10) DEFAULT NULL,
  `gender` varchar(10) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `batch` year(4) NOT NULL,
  `course_id` int(30) NOT NULL,
  `email` varchar(250) NOT NULL,
  `connected_to` text NOT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `company_email` varchar(200) DEFAULT NULL,
  `avatar` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0= Unverified, 1= Verified',
  `date_created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `alumnus_bio`
--

INSERT INTO `alumnus_bio` (`id`, `firstname`, `middlename`, `lastname`, `suffixname`, `gender`, `birthdate`, `address`, `batch`, `course_id`, `email`, `connected_to`, `company_name`, `company_address`, `contact_no`, `company_email`, `avatar`, `status`, `date_created`) VALUES
(10, 'Charlie James', 'Zapico', 'Abejo', NULL, 'Male', NULL, NULL, '2029', 1, 'anneampo5@gmail.com', 'IT', NULL, NULL, NULL, NULL, '1740744060_1602730260_avatar.jpg', 1, '2025-02-28'),
(11, 'CharlieJames', 'Zapico', 'Abejo', NULL, 'Male', NULL, NULL, '2025', 6, 'Johnbang1@gmail.com', 'CRIMINOLOGY', NULL, NULL, NULL, NULL, '1740794580_1740713820_1602730260_avatar.jpg', 1, '2025-03-01'),
(12, 'CharlieJames', 'Zapico', 'Abejo', NULL, 'Male', NULL, NULL, '2029', 7, 'Charliezapicoabejo1234@gmail.com', 'IT', NULL, NULL, NULL, NULL, '1740794760_1740713820_1602730260_avatar.jpg', 1, '2025-03-01'),
(13, 'Anne', 'Ampo', 'Mary', NULL, 'Female', NULL, NULL, '2029', 1, 'Mary1@gmail.com', 'Developer', NULL, NULL, NULL, NULL, '1740795360_1740713820_1602730260_avatar.jpg', 1, '2025-03-01');

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
(1, 'BS Information Technology', 'Sample'),
(3, 'BSED', ''),
(4, 'BEED', ''),
(5, 'MIDWIFERY', ''),
(6, 'CRIMINOLOGY', ''),
(7, 'DSHT', '');

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
  `type` tinyint(1) NOT NULL DEFAULT 3 COMMENT '1=Admin,2=Alumni officer, 3= alumnus',
  `auto_generated_pass` text NOT NULL,
  `alumnus_id` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `type`, `auto_generated_pass`, `alumnus_id`) VALUES
(1, 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 1, '', 0),
(15, 'Charlie James Abejo', 'anneampo5@gmail.com', '7c0e9a8857289bd39512723e5c3010a4', 3, '', 10),
(16, 'CharlieJames Abejo', 'Johnbang1@gmail.com', 'af8ab4b17d298f8d71669a70d066b5a7', 3, '', 11),
(17, 'CharlieJames Abejo', 'Charliezapicoabejo1234@gmail.com', '76d7e1e72a7a25cfd730549943052b51', 3, '', 12),
(18, 'Anne Mary', 'Mary1@gmail.com', '7c0e9a8857289bd39512723e5c3010a4', 3, '', 13),
(19, 'Charlie Abejooo', 'anneampo56@gmail.com', '76d7e1e72a7a25cfd730549943052b51', 3, '', 0),
(20, 'Charlie Abejooo', 'abejocherrymae@gmail.com', '7c0e9a8857289bd39512723e5c3010a4', 3, '', 0),
(21, 'Bboy Abejo', 'nezabatistill@yahoo.com', '6dcfa4d739100a60f57b0db2add5c31a', 3, '', 0),
(22, 'CharlieJames Abejo', 'charrrr@gmail.com', '7c0e9a8857289bd39512723e5c3010a4', 3, '', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumnus_bio`
--
ALTER TABLE `alumnus_bio`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_commits`
--
ALTER TABLE `event_commits`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `alumnus_bio`
--
ALTER TABLE `alumnus_bio`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `careers`
--
ALTER TABLE `careers`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `event_commits`
--
ALTER TABLE `event_commits`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `forum_comments`
--
ALTER TABLE `forum_comments`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `forum_topics`
--
ALTER TABLE `forum_topics`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
