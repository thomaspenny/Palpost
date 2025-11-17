-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: fdb1034.awardspace.net
-- Generation Time: Nov 17, 2025 at 04:26 PM
-- Server version: 8.0.32
-- PHP Version: 8.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `4707790_palpostthomaspenny`
--

-- --------------------------------------------------------

--
-- Table structure for table `Comments`
--

CREATE TABLE `Comments` (
  `commentID` bigint NOT NULL,
  `userID` bigint NOT NULL,
  `Text` varchar(300) NOT NULL,
  `postID` bigint NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Likes`
--

CREATE TABLE `Likes` (
  `likeID` bigint NOT NULL,
  `postID` bigint NOT NULL,
  `userID` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Likes`
--

INSERT INTO `Likes` (`likeID`, `postID`, `userID`) VALUES
(152, 50, 30),
(153, 52, 30);

-- --------------------------------------------------------

--
-- Table structure for table `Media`
--

CREATE TABLE `Media` (
  `mediaID` bigint NOT NULL,
  `postID` bigint NOT NULL,
  `mediaPath` varchar(255) NOT NULL,
  `mediaType` varchar(50) NOT NULL,
  `mediaCaption` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Media`
--

INSERT INTO `Media` (`mediaID`, `postID`, `mediaPath`, `mediaType`, `mediaCaption`) VALUES
(45, 50, 'uploads/post_content/post_50_1763395714_0.png', 'image/png', 'main section'),
(46, 50, 'uploads/post_content/post_50_1763395714_1.png', 'image/png', 'bottom section'),
(47, 51, 'uploads/post_content/post_51_1763396108_0.png', 'image/png', 'code snippet'),
(48, 51, 'uploads/post_content/post_51_1763396108_1.png', 'image/png', 'the app in use'),
(49, 51, 'uploads/post_content/post_51_1763396108_2.png', 'image/png', 'the finished visual analysis after all points are plotted (you also get a CSV file of the results fo'),
(50, 52, 'uploads/post_content/post_52_1763396475_0.png', 'image/png', 'Real time processing performance'),
(51, 52, 'uploads/post_content/post_52_1763396475_1.png', 'image/png', 'Cross Comparison'),
(52, 52, 'uploads/post_content/post_52_1763396475_2.png', 'image/png', 'SHAP Global Analysis'),
(53, 52, 'uploads/post_content/post_52_1763396475_3.png', 'image/png', 'LIME Local Analysis'),
(54, 52, 'uploads/post_content/post_52_1763396475_4.png', 'image/png', 'Feature Variance');

-- --------------------------------------------------------

--
-- Table structure for table `Posts`
--

CREATE TABLE `Posts` (
  `postID` bigint NOT NULL,
  `userID` bigint NOT NULL,
  `TextContent` varchar(300) DEFAULT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Posts`
--

INSERT INTO `Posts` (`postID`, `userID`, `TextContent`, `CreatedAt`) VALUES
(50, 30, 'Here is my more professional portfolio website\r\n\r\nhttps://thomaspenny.github.io/Thomas-Penny-Portfolio/', '2025-11-17 16:08:34'),
(51, 30, 'Heres some pictures of an application I built in Python for an Orthodontic Researcher to aid her in rapid Lateral Cephalogram analysis, you can find out more below: \r\n\r\nhttps://github.com/thomaspenny/Lateral-Cephalogram-App', '2025-11-17 16:15:08'),
(52, 30, 'Some pictures of an XAI and ML Credit Card Fraud Detection Application I built, which can compare 6 ML models, with insights from LIME and SHAP built in.', '2025-11-17 16:21:15');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userID` bigint NOT NULL,
  `userRank` int NOT NULL,
  `userName` varchar(20) NOT NULL,
  `userEmail` varchar(100) NOT NULL,
  `userPassword` varchar(255) NOT NULL,
  `userJoingingDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `userImagePath` varchar(255) NOT NULL DEFAULT 'uploads/profiles/default.png',
  `userImageType` varchar(50) DEFAULT 'image/png',
  `userBio` varchar(300) DEFAULT 'I haven''t updated my Bio yet!'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userID`, `userRank`, `userName`, `userEmail`, `userPassword`, `userJoingingDate`, `userImagePath`, `userImageType`, `userBio`) VALUES
(9, 1, 'Thomas', 'thomas@hotel.com', '$2y$10$9fcNTNEkG1APp/PEE5KmCOrJsh5RWyJALjQWg6okdilkHrm5J7sfy', '2025-06-12 15:09:06', 'uploads/profiles/user_9_1749740946.jpg', 'image/jpeg', 'I like all sorts of dogs, midfvdane especially'),
(10, 1, 'Matthew', 'matthew@hotel.com', '$2y$10$jGUMIr2AoVgyzKIlR3gg7eiRh2NHF.kus1X1UbHmyCpVfe/UzepRa', '2025-06-07 13:03:42', 'uploads/profiles/user_10_1749301422.png', 'image/png', 'Hi I like pancakes'),
(23, 2, 'Robert', 'robert@hotel.com', '$2y$10$6HZHaVx/wKQS2dLFkeNk/.22MZAO6K6UtsrVRsBHU19f.j8l0m9v.', '2025-06-07 11:36:50', 'uploads/profiles/default.png', 'image/png', 'I haven\'t updated my Bio yet!'),
(27, 2, 'michael', 'michael@hotel.com', '$2y$10$vjt4HSjLVDAWyN784AcSFONXxuhzPG6l/ygQaXAw0Qyiqw5a77tZ2', '2025-06-11 19:04:17', 'uploads/profiles/default.png', 'image/png', 'I haven\'t updated my Bio yet!'),
(29, 2, 'thomthom', 'thom@thom.com', '$2y$10$6gxti5ZCyL0CQkpZMoUUwO9k8nroRckybyVubSVX0bcbqP.vNBIb.', '2025-11-17 14:58:07', 'uploads/profiles/default.png', 'image/png', 'I haven\'t updated my Bio yet!'),
(30, 1, 'thomaspenny', 'thomaspenny@admin.com', '$2y$10$WIQQ0Fmrm.kYLaTlHh9dEejezSSrz2ey/QlcajjIGn.65KrSQ3IPm', '2025-11-17 16:01:34', 'uploads/profiles/user_30_1763395294.jpg', 'image/jpeg', 'Check out some of my portfolio work via posts I\\\'ve made here!'),
(31, 1, 'backupadmin', 'backupadmin@admin.com', '$2y$10$Is.rpe8kbjl4vVfN92LwFOB48Jv5czWpb2xKsOZk0qmw6.Bft9dJO', '2025-11-17 16:03:36', 'uploads/profiles/default.png', 'image/png', 'I haven\'t updated my Bio yet!'),
(32, 2, 'generaluser', 'General@admin.com', '$2y$10$IFLf7YQORikFWnpdJzCD3eKs67uCKSh5iaGxapjJ48hpo79fXl4vK', '2025-11-17 16:05:11', 'uploads/profiles/default.png', 'image/png', 'I haven\'t updated my Bio yet!');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Comments`
--
ALTER TABLE `Comments`
  ADD PRIMARY KEY (`commentID`),
  ADD KEY `postID` (`postID`);

--
-- Indexes for table `Likes`
--
ALTER TABLE `Likes`
  ADD UNIQUE KEY `likeID` (`likeID`) USING BTREE,
  ADD KEY `fk_likes_post` (`postID`);

--
-- Indexes for table `Media`
--
ALTER TABLE `Media`
  ADD PRIMARY KEY (`mediaID`),
  ADD KEY `postID` (`postID`);

--
-- Indexes for table `Posts`
--
ALTER TABLE `Posts`
  ADD PRIMARY KEY (`postID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userID`),
  ADD KEY `userRank` (`userRank`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Comments`
--
ALTER TABLE `Comments`
  MODIFY `commentID` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `Likes`
--
ALTER TABLE `Likes`
  MODIFY `likeID` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;

--
-- AUTO_INCREMENT for table `Media`
--
ALTER TABLE `Media`
  MODIFY `mediaID` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `Posts`
--
ALTER TABLE `Posts`
  MODIFY `postID` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userID` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Comments`
--
ALTER TABLE `Comments`
  ADD CONSTRAINT `fk_comments_post` FOREIGN KEY (`postID`) REFERENCES `Posts` (`postID`) ON DELETE CASCADE;

--
-- Constraints for table `Likes`
--
ALTER TABLE `Likes`
  ADD CONSTRAINT `fk_likes_post` FOREIGN KEY (`postID`) REFERENCES `Posts` (`postID`) ON DELETE CASCADE;

--
-- Constraints for table `Media`
--
ALTER TABLE `Media`
  ADD CONSTRAINT `Media_ibfk_1` FOREIGN KEY (`postID`) REFERENCES `Posts` (`postID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Posts`
--
ALTER TABLE `Posts`
  ADD CONSTRAINT `Posts_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
