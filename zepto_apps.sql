-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 28, 2024 at 10:25 PM
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
-- Database: `zepto_apps`
--

-- --------------------------------------------------------

--
-- Table structure for table `font_groups`
--

CREATE TABLE `font_groups` (
  `id` int(11) NOT NULL,
  `group_title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `font_groups`
--

INSERT INTO `font_groups` (`id`, `group_title`) VALUES
(17, 'test data group');

-- --------------------------------------------------------

--
-- Table structure for table `group_fonts`
--

CREATE TABLE `group_fonts` (
  `id` int(11) NOT NULL,
  `group_id` int(11) DEFAULT NULL,
  `font_name` varchar(255) DEFAULT NULL,
  `font_style` varchar(255) DEFAULT NULL,
  `specific_size` int(11) DEFAULT NULL,
  `price_change` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_fonts`
--

INSERT INTO `group_fonts` (`id`, `group_id`, `font_name`, `font_style`, `specific_size`, `price_change`) VALUES
(139, 17, 'test data group 1', 'Arial', 1, 1),
(140, 17, 'test data group 2', 'Times New Roman', 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `uploaded_fonts`
--

CREATE TABLE `uploaded_fonts` (
  `id` int(11) NOT NULL,
  `font_name` varchar(255) DEFAULT NULL,
  `upload_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `uploaded_fonts`
--

INSERT INTO `uploaded_fonts` (`id`, `font_name`, `upload_time`) VALUES
(50, '1727549693_OpenSans-Light.ttf', '2024-09-28 18:54:53'),
(52, '1727555035_OpenSans-Light.ttf', '2024-09-28 20:23:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `font_groups`
--
ALTER TABLE `font_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_fonts`
--
ALTER TABLE `group_fonts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploaded_fonts`
--
ALTER TABLE `uploaded_fonts`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `font_groups`
--
ALTER TABLE `font_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `group_fonts`
--
ALTER TABLE `group_fonts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `uploaded_fonts`
--
ALTER TABLE `uploaded_fonts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
