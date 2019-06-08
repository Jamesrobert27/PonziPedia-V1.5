-- phpMyAdmin SQL Dump
-- version 4.4.15.9
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 03, 2018 at 02:21 AM
-- Server version: 5.6.37
-- PHP Version: 5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ponzidev`
--

-- --------------------------------------------------------

--
-- Table structure for table `activationFee`
--

CREATE TABLE IF NOT EXISTS `activationFee` (
  `id` int(11) NOT NULL,
  `receiver_id` varchar(25) NOT NULL,
  `sender_id` varchar(25) NOT NULL,
  `amount` varchar(250) NOT NULL,
  `payment_status` varchar(25) NOT NULL,
  `ProofPic` varchar(250) NOT NULL,
  `paymentMethod` varchar(250) NOT NULL,
  `senderBank` varchar(250) NOT NULL,
  `accountNumber` varchar(250) NOT NULL,
  `AccountName` varchar(250) NOT NULL,
  `depositorsName` varchar(250) NOT NULL,
  `paymentLocation` varchar(250) NOT NULL,
  `expiringTime` varchar(250) NOT NULL,
  `active` varchar(25) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `activationFee`
--

INSERT INTO `activationFee` (`id`, `receiver_id`, `sender_id`, `amount`, `payment_status`, `ProofPic`, `paymentMethod`, `senderBank`, `accountNumber`, `AccountName`, `depositorsName`, `paymentLocation`, `expiringTime`, `active`) VALUES
(10, '1', '2', '500', 'confirm', '', '', '', '', '', '', '', '', '1');

-- --------------------------------------------------------

--
-- Table structure for table `activationReceiver`
--

CREATE TABLE IF NOT EXISTS `activationReceiver` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `activationReceiver`
--

INSERT INTO `activationReceiver` (`id`, `userid`, `date`, `deleted`) VALUES
(1, 1, '2018-04-02 18:35:47', 0);

-- --------------------------------------------------------

--
-- Table structure for table `bank`
--

CREATE TABLE IF NOT EXISTS `bank` (
  `id` int(11) NOT NULL,
  `userid` varchar(25) NOT NULL,
  `balance` varchar(250) NOT NULL,
  `pending` varchar(250) NOT NULL,
  `accountNum` varchar(250) NOT NULL,
  `confirmed` int(11) NOT NULL,
  `status` varchar(250) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `bank`
--

INSERT INTO `bank` (`id`, `userid`, `balance`, `pending`, `accountNum`, `confirmed`, `status`) VALUES
(1, '1', '8400', 'none', '876545678', 0, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `casereply`
--

CREATE TABLE IF NOT EXISTS `casereply` (
  `id` int(11) NOT NULL,
  `caseid` tinyint(1) NOT NULL DEFAULT '1',
  `replyId` varchar(25) NOT NULL,
  `Message` longtext NOT NULL,
  `proofFile` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `page` varchar(250) NOT NULL,
  `page_url` varchar(250) NOT NULL,
  `page_title` varchar(250) NOT NULL,
  `content` mediumtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `upvotes` int(11) NOT NULL DEFAULT '0',
  `downvotes` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `parent` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


--
-- Table structure for table `contacts`
--

CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL,
  `user1` int(11) NOT NULL,
  `user2` int(11) NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `courtcase`
--

CREATE TABLE IF NOT EXISTS `courtcase` (
  `id` int(11) NOT NULL,
  `userid` varchar(25) NOT NULL,
  `accused` varchar(25) NOT NULL,
  `margin_id` varchar(25) NOT NULL,
  `type` varchar(25) NOT NULL,
  `details` longtext NOT NULL,
  `replys` varchar(25) NOT NULL,
  `proofFile` varchar(250) NOT NULL,
  `status` varchar(25) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


--
-- Table structure for table `marching`
--

CREATE TABLE IF NOT EXISTS `marching` (
  `id` int(11) NOT NULL,
  `receiver_id` varchar(25) NOT NULL,
  `sender_id` varchar(25) NOT NULL,
  `amount` varchar(250) NOT NULL,
  `package_id` varchar(25) NOT NULL,
  `payment_status` varchar(25) NOT NULL,
  `ProofPic` varchar(250) NOT NULL,
  `paymentMethod` varchar(250) NOT NULL,
  `senderBank` varchar(250) NOT NULL,
  `accountNumber` varchar(250) NOT NULL,
  `AccountName` varchar(250) NOT NULL,
  `depositorsName` varchar(250) NOT NULL,
  `paymentLocation` varchar(250) NOT NULL,
  `expiringTime` varchar(250) NOT NULL,
  `report` varchar(25) NOT NULL,
  `active` varchar(25) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;


--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL,
  `from_user` int(11) NOT NULL,
  `to_user` int(11) NOT NULL,
  `message` mediumtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `messagess`
--

CREATE TABLE IF NOT EXISTS `messagess` (
  `id` int(11) NOT NULL,
  `from_user` int(11) NOT NULL,
  `to_user` int(11) NOT NULL,
  `message` mediumtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read` tinyint(1) NOT NULL DEFAULT '0',
  `reply` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;


--
-- Table structure for table `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `id` int(11) NOT NULL,
  `userid` varchar(25) NOT NULL,
  `type` varchar(25) NOT NULL,
  `details` varchar(250) NOT NULL,
  `status` varchar(25) NOT NULL,
  `faIcon` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `userid`, `type`, `details`, `status`, `faIcon`, `date`) VALUES,
(1, '1', 'pack', 'You have pending activation payment ₦2000', 'verify', 'fa fa-usd', '2018-04-29 01:08:17');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `id` int(11) NOT NULL,
  `group` varchar(100) NOT NULL,
  `item` varchar(100) NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE IF NOT EXISTS `packages` (
  `id` int(11) NOT NULL,
  `packname` varchar(250) NOT NULL,
  `price` varchar(250) NOT NULL,
  `profit` varchar(250) NOT NULL,
  `days` varchar(250) NOT NULL,
  `codes` varchar(50) NOT NULL,
  `status` varchar(25) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Table structure for table `referral`
--

CREATE TABLE IF NOT EXISTS `referral` (
  `id` int(11) NOT NULL,
  `userid` varchar(250) NOT NULL,
  `sponsor` varchar(250) NOT NULL,
  `parent` varchar(25) NOT NULL,
  `package` varchar(250) NOT NULL,
  `amount` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;


--
-- Table structure for table `requestHelp`
--

CREATE TABLE IF NOT EXISTS `requestHelp` (
  `id` int(11) NOT NULL,
  `userid` varchar(25) NOT NULL,
  `package_id` varchar(250) NOT NULL,
  `pack_name` varchar(250) NOT NULL,
  `amount` varchar(250) NOT NULL,
  `profit` varchar(250) NOT NULL,
  `timeReq` varchar(250) NOT NULL,
  `balance` varchar(25) NOT NULL,
  `status` varchar(25) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;


--
-- Table structure for table `requestMaching`
--

CREATE TABLE IF NOT EXISTS `requestMaching` (
  `id` int(11) NOT NULL,
  `userid` varchar(25) NOT NULL,
  `package_id` varchar(25) NOT NULL,
  `pack_name` varchar(250) NOT NULL,
  `amount` varchar(250) NOT NULL,
  `profit` varchar(250) NOT NULL,
  `balance` varchar(25) NOT NULL,
  `timeReq` varchar(250) NOT NULL,
  `status` varchar(25) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `requestMaching`

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `permissions` mediumtext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `permissions`) VALUES
(1, 'Administrator', '*'),
(2, 'User', ''),
(3, '3', 'blocked'),
(4, 'guider', 'guider');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(32) NOT NULL,
  `payload` text,
  `last_activity` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL,
  `margintype` int(11) NOT NULL,
  `profit` int(11) NOT NULL,
  `days` int(11) NOT NULL,
  `timeMargin` int(11) NOT NULL,
  `getHelpDay` int(11) NOT NULL,
  `ProvideHelpday` int(11) NOT NULL,
  `activationFee` int(11) NOT NULL,
  `activationPrice` varchar(25) NOT NULL,
  `activationFeeExp` varchar(250) NOT NULL,
  `currency` varchar(25) NOT NULL,
  `referralProfit` varchar(25) NOT NULL,
  `guiderProfit` varchar(25) NOT NULL,
  `GuiderMin` varchar(25) NOT NULL,
  `site_status` varchar(2) NOT NULL,
  `reccomitment` varchar(10) NOT NULL,
  `registration` varchar(5) NOT NULL,
  `invitecode` varchar(255) NOT NULL,
  `smsallow` varchar(5) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `margintype`, `profit`, `days`, `timeMargin`, `getHelpDay`, `ProvideHelpday`, `activationFee`, `activationPrice`, `activationFeeExp`, `currency`, `referralProfit`, `guiderProfit`, `GuiderMin`, `site_status`, `reccomitment`, `registration`, `invitecode`, `smsallow`) VALUES
(1, 1, 40, 5, 1, 1, 1, 1, '2000', '1', '₦', '10', '5', '5000', '1', '10', '0', '3bkEtKSJptdB', '1');

-- --------------------------------------------------------

--
-- Table structure for table `subscriber`
--

CREATE TABLE IF NOT EXISTS `subscriber` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `subscriber`
--

INSERT INTO `subscriber` (`id`, `email`, `date`, `deleted`) VALUES
(12, 'hackmininglord@outlook.com', '2018-04-03 18:19:10', 0),
(13, 'hackhubpro@gmail.com', '2018-04-03 18:54:10', 0),
(15, 'dvlmob@gmail.com', '2018-04-03 18:56:25', 0);

-- --------------------------------------------------------

--
-- Table structure for table `testimoneytvotes`
--

CREATE TABLE IF NOT EXISTS `testimoneytvotes` (
  `id` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `userid` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `testimony`
--

CREATE TABLE IF NOT EXISTS `testimony` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0',
  `Title` varchar(250) NOT NULL,
  `content` mediumtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `upvotes` int(11) NOT NULL DEFAULT '0',
  `downvotes` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

--
-- Table structure for table `testimonyforce`
--

CREATE TABLE IF NOT EXISTS `testimonyforce` (
  `id` int(11) NOT NULL,
  `margin_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;


-- Table structure for table `tickets`
--

CREATE TABLE IF NOT EXISTS `tickets` (
  `id` int(11) NOT NULL,
  `userid` varchar(250) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `description` mediumtext NOT NULL,
  `replied` varchar(250) NOT NULL,
  `moderator` varchar(250) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--

--
-- Table structure for table `ticketsreply`
--

CREATE TABLE IF NOT EXISTS `ticketsreply` (
  `id` int(11) NOT NULL,
  `ticketid` varchar(250) NOT NULL,
  `admin` varchar(250) NOT NULL,
  `userid` varchar(250) NOT NULL,
  `reply` mediumtext NOT NULL,
  `replied` varchar(25) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;


--
-- Table structure for table `transactions`
--

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int(11) NOT NULL,
  `userid` int(11) NOT NULL DEFAULT '0',
  `amount` varchar(250) NOT NULL,
  `type` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` mediumtext NOT NULL,
  `dateNow` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userdetails`
--

CREATE TABLE IF NOT EXISTS `userdetails` (
  `id` int(11) NOT NULL,
  `userid` varchar(25) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `phonenumber` varchar(250) NOT NULL,
  `bankname` varchar(250) NOT NULL,
  `accounttype` varchar(250) NOT NULL,
  `accountname` varchar(250) NOT NULL,
  `accountnumber` varchar(250) NOT NULL,
  `country` varchar(250) NOT NULL,
  `state` varchar(250) NOT NULL,
  `bitcoinwallet` varchar(250) NOT NULL,
  `refid` varchar(50) NOT NULL,
  `avater` varchar(250) NOT NULL,
  `status` varchar(25) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `userdetails`
--

INSERT INTO `userdetails` (`id`, `userid`, `firstname`, `lastname`, `phonenumber`, `bankname`, `accounttype`, `accountname`, `accountnumber`, `country`, `state`, `bitcoinwallet`, `refid`, `avater`, `status`, `date`) VALUES
(1, '1', 'Amanda', 'Williams', '+234907656789', 'Diamond Bank', 'Savings', 'Amanda Williams', '345676545', 'Nigeria', 'Lagos', '9876545678987654567', '87524881', 'http://localhost/ponzi.dev/uploads/2.png?1525171691', '1', '2018-03-29 20:10:13');

-- --------------------------------------------------------

--
-- Table structure for table `usermeta`
--

CREATE TABLE IF NOT EXISTS `usermeta` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `meta_key` varchar(100) NOT NULL,
  `meta_value` mediumtext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `usermeta`
--

INSERT INTO `usermeta` (`id`, `user_id`, `meta_key`, `meta_value`) VALUES
(1, 2, 'last_login', '2018-05-01 09:40:26'),
(2, 2, 'last_login_ip', '::1'),
(3, 5, 'last_login', '2018-05-01 09:43:09'),
(4, 5, 'last_login_ip', '::1'),
(5, 3, 'last_login', '2018-04-02 18:03:12'),
(6, 3, 'last_login_ip', '::1'),
(7, 6, 'last_login', '2018-03-31 21:10:18'),
(8, 6, 'last_login_ip', '::1'),
(9, 7, 'last_login', '2018-05-01 01:28:04'),
(10, 7, 'last_login_ip', '::1'),
(11, 5, 'first_name', 'James'),
(12, 5, 'last_name', 'Daniel'),
(13, 5, 'gender', 'M'),
(14, 5, 'birthday', ''),
(15, 5, 'url', ''),
(16, 5, 'phone', ''),
(17, 5, 'location', ''),
(18, 5, 'about', ''),
(19, 5, 'avatar_type', ''),
(20, 8, 'last_login', '2018-04-02 20:34:51'),
(21, 8, 'last_login_ip', '::1'),
(22, 9, 'last_login', '2018-04-12 13:59:03'),
(23, 9, 'last_login_ip', '::1'),
(24, 10, 'last_login', '2018-04-12 14:06:03'),
(25, 10, 'last_login_ip', '::1'),
(26, 5, 'locale', 'en'),
(27, 2, 'locale', 'en'),
(28, 11, 'last_login', '2018-04-17 01:51:24'),
(29, 11, 'last_login_ip', '::1'),
(30, 12, 'last_login', '2018-04-21 21:34:27'),
(31, 12, 'last_login_ip', '::1'),
(32, 1, 'last_login', '2018-05-02 23:43:39'),
(33, 1, 'last_login_ip', '::1'),
(34, 13, 'last_login', '2018-05-01 19:22:31'),
(35, 13, 'last_login_ip', '::1'),
(36, 14, 'last_login', '2018-04-29 00:54:34'),
(37, 14, 'last_login_ip', '::1'),
(38, 15, 'last_login', '2018-04-29 01:08:03'),
(39, 15, 'last_login_ip', '::1'),
(40, 2, 'avatar_image', '2.png'),
(41, 2, 'avatar_type', 'image'),
(42, 7, 'avatar_image', '7.jpg'),
(43, 7, 'avatar_type', 'image'),
(44, 16, 'last_login', '2018-05-01 02:20:51'),
(45, 16, 'last_login_ip', '::1'),
(46, 16, 'avatar_image', '16.jpg'),
(47, 16, 'avatar_type', 'image'),
(48, 13, 'avatar_image', '13.png'),
(49, 13, 'avatar_type', 'image'),
(50, 17, 'last_login', '2018-05-01 10:42:22'),
(51, 17, 'last_login_ip', '::1'),
(52, 18, 'last_login', '2018-05-03 01:48:46'),
(53, 18, 'last_login_ip', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(64) DEFAULT NULL,
  `display_name` varchar(200) DEFAULT NULL,
  `joined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `role_id` int(5) NOT NULL DEFAULT '0',
  `reminder` varchar(50) DEFAULT NULL,
  `remember` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `display_name`, `joined`, `status`, `role_id`, `reminder`, `remember`) VALUES
(1, 'admin', 'webmaster@localhost.dev', '$2y$10$trJyrB8x2V/hKKeKJvNF0Otz6OqFgisd0fiLc7B1ssHzSvpE0ADYu', 'Admin', '2014-08-07 07:44:27', 1, 1, '1525174870WlAR2JxjCpgvsBy2i7FRXVQ6PYrpAvMv', '1527896619LoCIB52r02EYz5qQrPsBRKSREehakVU1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activationFee`
--
ALTER TABLE `activationFee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `activationReceiver`
--
ALTER TABLE `activationReceiver`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bank`
--
ALTER TABLE `bank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `casereply`
--
ALTER TABLE `casereply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courtcase`
--
ALTER TABLE `courtcase`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `marching`
--
ALTER TABLE `marching`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messagess`
--
ALTER TABLE `messagess`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);



--
-- Indexes for table `referral`
--
ALTER TABLE `referral`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requestHelp`
--
ALTER TABLE `requestHelp`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requestMaching`
--
ALTER TABLE `requestMaching`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriber`
--
ALTER TABLE `subscriber`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimoneytvotes`
--
ALTER TABLE `testimoneytvotes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimony`
--
ALTER TABLE `testimony`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonyforce`
--
ALTER TABLE `testimonyforce`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ticketsreply`
--
ALTER TABLE `ticketsreply`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `userdetails`
--
ALTER TABLE `userdetails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usermeta`
--
ALTER TABLE `usermeta`
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
-- AUTO_INCREMENT for table `activationFee`
--
ALTER TABLE `activationFee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT for table `activationReceiver`
--
ALTER TABLE `activationReceiver`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `bank`
--
ALTER TABLE `bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `casereply`
--
ALTER TABLE `casereply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `courtcase`
--
ALTER TABLE `courtcase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `marching`
--
ALTER TABLE `marching`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `messagess`
--
ALTER TABLE `messagess`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=118;
--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `referral`
--
ALTER TABLE `referral`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `requestHelp`
--
ALTER TABLE `requestHelp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `requestMaching`
--
ALTER TABLE `requestMaching`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `subscriber`
--
ALTER TABLE `subscriber`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `testimoneytvotes`
--
ALTER TABLE `testimoneytvotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=44;
--
-- AUTO_INCREMENT for table `testimony`
--
ALTER TABLE `testimony`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `testimonyforce`
--
ALTER TABLE `testimonyforce`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `ticketsreply`
--
ALTER TABLE `ticketsreply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `userdetails`
--
ALTER TABLE `userdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=30;
--
-- AUTO_INCREMENT for table `usermeta`
--
ALTER TABLE `usermeta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=54;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=19;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
