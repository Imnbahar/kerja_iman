-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 06, 2023 at 04:51 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `iman`
--

-- --------------------------------------------------------

--
-- Table structure for table `data`
--

CREATE TABLE `data` (
  `id` int NOT NULL,
  `scan1` varchar(255) DEFAULT NULL,
  `scan2` varchar(255) DEFAULT NULL,
  `qr_scanned` varchar(255) DEFAULT NULL,
  `text_extracted` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `data`
--

INSERT INTO `data` (`id`, `scan1`, `scan2`, `qr_scanned`, `text_extracted`) VALUES
(1, 'QR', 'Image', 'https://www.youtube.com/watch?v=zvt4hTAbURM', 'Question :\nAnne owns and operates a flower shop. She only sells flowers in her shop, but since the pandemic has\nstruck, she has to close down the flower shop operation. As a web developer, you are required to\ndevelop a website for online flower shop using HTML, CSS, JavaScript and ASP.net to assist Anne in\ntransitioning her flower shop from physical to online. The flower shop should have minimum of 5 main\nfeatures below:\n\n1. Register and Login page\n\n2. Display all the options to customize the flowers to make bouquet\n\n3. Display the price\n\n4. Able to add/edit and delete the cart\n\n5. Make a purchase\n*You may include any additional features where necessary.\n*Bootstraps usage are allowed\n*Deploy your application to Azure (no need to implement but show the steps to deploy the application\nIn azure)\n');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data`
--
ALTER TABLE `data`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data`
--
ALTER TABLE `data`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
