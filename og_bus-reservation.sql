-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2025 at 11:26 AM
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
-- Database: `bus_reservation`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `bus_no` varchar(50) DEFAULT NULL,
  `route` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `travel_date` date DEFAULT NULL,
  `num_people` int(11) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `bus_no`, `route`, `price`, `phone`, `travel_date`, `num_people`, `status`) VALUES
(1, 16, '102', 'Kathmandu to Surkhet', 700.00, '9863164952', '2024-12-05', 8, 'confirmed'),
(3, 16, '101', 'Nepalgunj to Kathmandu', 500.00, '9865347826', '2024-12-05', 4, 'confirmed'),
(4, 4, '101', 'Nepalgunj to Kathmandu', 500.00, '9870809845', '2024-12-04', 7, 'confirmed'),
(6, 4, '2', 'Kathmandu to Pokhara', 9000.00, '9823455568', '2025-01-21', 6, 'confirmed'),
(7, 4, '2', 'Kathmandu to Pokhara', 6000.00, '9762267691', '2025-01-21', 4, 'confirmed'),
(8, 4, '73', 'Kathmandu to Pokhara', 41400.00, '9863164952', '2025-03-01', 23, 'confirmed'),
(9, 17, '22', 'Kathmandu to Dhangadhi', 34500.00, '9863164952', '2025-02-08', 23, 'confirmed'),
(10, 17, '108', 'Kathmandu to Pokhara', 39000.00, '9762267691', '2025-01-22', 13, 'confirmed'),
(11, 4, '127', 'Kathmandu to Pokhara', 46000.00, '9762267691', '2025-01-22', 20, 'confirmed'),
(12, 17, '3', 'Chitwan to Kathmandu', 6000.00, '9762267691', '2025-04-13', 4, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `bus_name` varchar(100) DEFAULT NULL,
  `from_city` varchar(100) DEFAULT NULL,
  `to_city` varchar(100) DEFAULT NULL,
  `departure_date` date DEFAULT NULL,
  `seats_available` int(11) DEFAULT NULL,
  `ticket_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `bus_name`, `from_city`, `to_city`, `departure_date`, `seats_available`, `ticket_price`) VALUES
(1, 'Green Line Express', 'Kathmandu', 'Pokhara', '2025-01-22', 38, 1500.00),
(2, 'Blue Sky Travels', 'Kathmandu', 'Pokhara', '2025-01-21', 25, 1500.00),
(3, 'Mountain Express', 'Chitwan', 'Kathmandu', '2025-04-13', 26, 1500.00),
(4, 'City Bus Service', 'Biratnagar', 'Kathmandu', '2025-01-31', 20, 1000.00),
(5, 'Himalayan Travels', 'Butwal', 'Pokhara', '2024-12-09', 25, 1500.00),
(6, 'Everest Express', 'Kathmandu', 'Chitwan', '2025-01-23', 40, 1200.00),
(7, 'Sunrise Travels', 'Pokhara', 'Kathmandu', '2025-01-24', 35, 1500.00),
(8, 'Valley Express', 'Birgunj', 'Itahari', '2025-01-25', 28, 1500.00),
(9, 'Himalaya Travels', 'Nepalgunj', 'Biratnagar', '2025-01-26', 32, 1500.00),
(10, 'River Side Bus', 'Butwal', 'Pokhara', '2025-01-27', 40, 1500.00),
(11, 'Fast Track Travels', 'Itahari', 'Janakpur', '2025-01-28', 20, 1500.00),
(12, 'Royal Bus Service', 'Chitwan', 'Dhangadhi', '2025-01-29', 45, 1500.00),
(13, 'Mountain Ride', 'Dhangadhi', 'Nepalgunj', '2025-01-30', 38, 1500.00),
(14, 'City Express', 'Kathmandu', 'Biratnagar', '2025-01-31', 25, 1500.00),
(15, 'Blue Sky Travels', 'Birgunj', 'Butwal', '2025-02-01', 30, 1500.00),
(16, 'Starline Travels', 'Janakpur', 'Kathmandu', '2025-02-02', 40, 1500.00),
(17, 'Green Line Service', 'Pokhara', 'Chitwan', '2025-02-03', 22, 1000.00),
(18, 'Nepal Express', 'Itahari', 'Nepalgunj', '2025-02-04', 35, 1500.00),
(19, 'Speedline Bus', 'Dhangadhi', 'Kathmandu', '2025-02-05', 27, 1500.00),
(20, 'Prime Travels', 'Biratnagar', 'Pokhara', '2025-02-06', 36, 1500.00),
(21, 'City Connect', 'Butwal', 'Birgunj', '2025-02-07', 30, 1500.00),
(22, 'Himalayan Ride', 'Kathmandu', 'Dhangadhi', '2025-02-08', 19, 1500.00),
(23, 'Sunrise Deluxe', 'Nepalgunj', 'Chitwan', '2025-02-09', 25, 1500.00),
(24, 'Green Express', 'Biratnagar', 'Janakpur', '2025-02-10', 28, 1500.00),
(25, 'Fast Track Travels', 'Pokhara', 'Birgunj', '2025-02-11', 26, 1500.00),
(26, 'Starline Express', 'Chitwan', 'Itahari', '2025-02-12', 24, 1500.00),
(27, 'Valley Connect', 'Birgunj', 'Dhangadhi', '2025-02-13', 40, 1500.00),
(28, 'Prime Travels', 'Janakpur', 'Kathmandu', '2025-02-14', 35, 1500.00),
(29, 'Royal Line', 'Pokhara', 'Biratnagar', '2025-02-15', 20, 1500.00),
(30, 'Speedline Deluxe', 'Birgunj', 'Nepalgunj', '2025-02-16', 38, 1500.00),
(31, 'Everest Travels', 'Kathmandu', 'Butwal', '2025-02-17', 25, 1500.00),
(32, 'Himalaya Bus Service', 'Biratnagar', 'Itahari', '2025-02-18', 30, 1500.00),
(33, 'Sunrise Connect', 'Chitwan', 'Birgunj', '2025-02-19', 36, 1500.00),
(34, 'Mountain Ride', 'Janakpur', 'Dhangadhi', '2025-02-20', 45, 1500.00),
(35, 'Sky High Travels', 'Kathmandu', 'Janakpur', '2025-01-21', 30, 1500.00),
(36, 'Golden Line', 'Pokhara', 'Butwal', '2025-01-22', 25, 1500.00),
(37, 'Royal Travels', 'Chitwan', 'Nepalgunj', '2025-01-23', 40, 1500.00),
(38, 'Everest Deluxe', 'Itahari', 'Biratnagar', '2025-01-24', 20, 1500.00),
(39, 'Sunshine Express', 'Birgunj', 'Dhangadhi', '2025-01-25', 35, 1500.00),
(40, 'Fast Connect', 'Nepalgunj', 'Kathmandu', '2025-01-26', 50, 1500.00),
(41, 'Green Valley Bus', 'Janakpur', 'Itahari', '2025-01-27', 45, 1500.00),
(42, 'Himalayan Ride', 'Biratnagar', 'Pokhara', '2025-01-28', 38, 1500.00),
(43, 'Skyline Express', 'Kathmandu', 'Birgunj', '2025-01-29', 28, 1500.00),
(44, 'Rapid Bus Service', 'Chitwan', 'Biratnagar', '2025-01-30', 30, 1500.00),
(45, 'Golden Express', 'Dhangadhi', 'Kathmandu', '2025-01-31', 40, 1500.00),
(46, 'Royal Deluxe', 'Butwal', 'Janakpur', '2025-02-01', 32, 1500.00),
(47, 'Fastline Bus', 'Pokhara', 'Nepalgunj', '2025-02-02', 35, 1500.00),
(48, 'Mountain Connect', 'Kathmandu', 'Chitwan', '2025-02-03', 45, 1200.00),
(49, 'Blue Star', 'Itahari', 'Butwal', '2025-02-04', 20, 1500.00),
(50, 'Silver Line Travels', 'Pokhara', 'Janakpur', '2025-02-05', 30, 1500.00),
(51, 'Green Hill Bus', 'Dhangadhi', 'Itahari', '2025-02-06', 28, 1500.00),
(52, 'Royal Express', 'Biratnagar', 'Butwal', '2025-02-07', 40, 1500.00),
(53, 'Golden Star', 'Birgunj', 'Nepalgunj', '2025-02-08', 50, 1500.00),
(54, 'Sunshine Line', 'Itahari', 'Chitwan', '2025-02-09', 35, 1500.00),
(55, 'Rapid Connect', 'Janakpur', 'Dhangadhi', '2025-02-10', 45, 1500.00),
(56, 'Blue Line', 'Kathmandu', 'Biratnagar', '2025-02-11', 25, 1500.00),
(57, 'Mountain Express', 'Chitwan', 'Pokhara', '2025-02-12', 20, 1500.00),
(58, 'Valley Travels', 'Birgunj', 'Kathmandu', '2025-02-13', 30, 1500.00),
(59, 'Fast Valley', 'Pokhara', 'Itahari', '2025-02-14', 40, 1500.00),
(60, 'Green Deluxe', 'Nepalgunj', 'Butwal', '2025-02-15', 38, 1500.00),
(61, 'Sky Connect', 'Dhangadhi', 'Birgunj', '2025-02-16', 32, 1500.00),
(62, 'Rapid Line', 'Janakpur', 'Biratnagar', '2025-02-17', 36, 1500.00),
(63, 'Silver Star', 'Butwal', 'Chitwan', '2025-02-18', 45, 1500.00),
(64, 'Valley Express', 'Kathmandu', 'Itahari', '2025-02-19', 28, 1500.00),
(65, 'Blue Deluxe', 'Pokhara', 'Janakpur', '2025-02-20', 30, 1500.00),
(66, 'Greenline Connect', 'Chitwan', 'Butwal', '2025-02-21', 35, 1500.00),
(67, 'Sky Valley', 'Biratnagar', 'Pokhara', '2025-02-22', 45, 1500.00),
(68, 'Rapid Express', 'Dhangadhi', 'Janakpur', '2025-02-23', 20, 1500.00),
(69, 'Blue Star Travels', 'Itahari', 'Nepalgunj', '2025-02-24', 40, 1500.00),
(70, 'Mountain Line', 'Kathmandu', 'Birgunj', '2025-02-25', 38, 1500.00),
(71, 'Himalayan Deluxe', 'Pokhara', 'Kathmandu', '2025-02-26', 50, 1500.00),
(72, 'Fast Track Travels', 'Birgunj', 'Dhangadhi', '2025-02-27', 28, 1500.00),
(73, 'Royal Mountain Express', 'Kathmandu', 'Pokhara', '2025-03-01', 22, 1800.00),
(74, 'Deluxe Night Rider', 'Pokhara', 'Kathmandu', '2025-03-01', 35, 1800.00),
(75, 'Sunrise Valley Express', 'Biratnagar', 'Dhangadhi', '2025-03-02', 30, 2500.00),
(76, 'Golden Path Travels', 'Dhangadhi', 'Biratnagar', '2025-03-02', 32, 2500.00),
(77, 'Mountain King Bus', 'Butwal', 'Nepalgunj', '2025-03-03', 28, 1200.00),
(78, 'Heritage Express', 'Janakpur', 'Pokhara', '2025-03-03', 40, 2000.00),
(79, 'Silver Moon Travels', 'Chitwan', 'Kathmandu', '2025-03-04', 36, 1200.00),
(80, 'Diamond Express', 'Kathmandu', 'Janakpur', '2025-03-04', 42, 1500.00),
(81, 'Himalayan Swift', 'Nepalgunj', 'Itahari', '2025-03-05', 38, 2200.00),
(82, 'Valley Star Bus', 'Birgunj', 'Pokhara', '2025-03-05', 34, 1800.00),
(83, 'Royal Comfort Line', 'Itahari', 'Butwal', '2025-03-06', 44, 1600.00),
(84, 'Green Valley Express', 'Pokhara', 'Dhangadhi', '2025-03-06', 40, 2300.00),
(85, 'Blue Mountain Bus', 'Kathmandu', 'Nepalgunj', '2025-03-07', 35, 2000.00),
(86, 'Everest Line Express', 'Biratnagar', 'Chitwan', '2025-03-07', 38, 1700.00),
(87, 'Golden Eagle Bus', 'Janakpur', 'Birgunj', '2025-03-08', 42, 1100.00),
(88, 'Swift Night Express', 'Dhangadhi', 'Kathmandu', '2025-03-08', 36, 2500.00),
(89, 'Royal Safari Bus', 'Chitwan', 'Pokhara', '2025-03-09', 40, 1300.00),
(90, 'Mountain View Express', 'Butwal', 'Biratnagar', '2025-03-09', 34, 1900.00),
(91, 'Valley King Travels', 'Nepalgunj', 'Janakpur', '2025-03-10', 45, 2100.00),
(92, 'Sunrise Deluxe Plus', 'Birgunj', 'Chitwan', '2025-03-10', 38, 1400.00),
(93, 'Royal Gurung Express', 'Pokhara', 'Butwal', '2025-03-11', 42, 1200.00),
(94, 'Heritage Valley Bus', 'Itahari', 'Dhangadhi', '2025-03-11', 36, 2400.00),
(95, 'Mountain Eagle Line', 'Kathmandu', 'Biratnagar', '2025-03-12', 40, 1800.00),
(96, 'Blue Safari Express', 'Janakpur', 'Nepalgunj', '2025-03-12', 35, 2200.00),
(97, 'Golden Path Plus', 'Dhangadhi', 'Pokhara', '2025-03-13', 44, 2300.00),
(98, 'Swift Valley Bus', 'Chitwan', 'Birgunj', '2025-03-13', 38, 1400.00),
(99, 'Royal Mountain Plus', 'Butwal', 'Kathmandu', '2025-03-14', 42, 1600.00),
(100, 'Green Eagle Express', 'Biratnagar', 'Janakpur', '2025-03-14', 36, 1300.00),
(101, 'Diamond Star Bus', 'Nepalgunj', 'Dhangadhi', '2025-03-15', 40, 1500.00),
(102, 'Himalayan King Line', 'Pokhara', 'Itahari', '2025-03-15', 45, 1700.00),
(103, 'Narayani Express', 'Biratnagar', 'Nepalgunj', '2025-02-08', 34, 2300.00),
(104, 'Gelek', 'Butwal', 'Nepalgunj', '2025-01-24', 34, 200.00),
(105, 'Mountain King Express', 'Kathmandu', 'Pokhara', '2025-01-22', 25, 2000.00),
(106, 'Green Valley Express', 'Kathmandu', 'Pokhara', '2025-01-22', 45, 1200.00),
(107, 'Royal Mountain Line', 'Kathmandu', 'Pokhara', '2025-01-22', 20, 2500.00),
(108, 'Himalayan Deluxe', 'Kathmandu', 'Pokhara', '2025-01-22', 2, 3000.00),
(109, 'Blue Mountain Express', 'Pokhara', 'Kathmandu', '2025-01-24', 22, 2200.00),
(110, 'Valley Express', 'Pokhara', 'Kathmandu', '2025-01-24', 48, 1000.00),
(111, 'Everest Line', 'Pokhara', 'Kathmandu', '2025-01-24', 18, 2800.00),
(112, 'Green Line Plus', 'Kathmandu', 'Chitwan', '2025-01-23', 25, 1800.00),
(113, 'Mountain Express', 'Kathmandu', 'Chitwan', '2025-01-23', 40, 900.00),
(114, 'Royal Star Line', 'Kathmandu', 'Chitwan', '2025-01-23', 15, 2200.00),
(115, 'Blue Sky Plus', 'Chitwan', 'Kathmandu', '2025-02-19', 20, 1900.00),
(116, 'Valley Line Express', 'Chitwan', 'Kathmandu', '2025-02-19', 45, 850.00),
(117, 'Himalayan Express', 'Chitwan', 'Kathmandu', '2025-02-19', 18, 2400.00),
(118, 'Mountain Night Express', 'Kathmandu', 'Biratnagar', '2025-01-31', 22, 2000.00),
(119, 'Green Valley Line', 'Kathmandu', 'Biratnagar', '2025-01-31', 45, 1200.00),
(120, 'Royal Express Plus', 'Kathmandu', 'Biratnagar', '2025-01-31', 18, 2500.00),
(121, 'Blue Line Express', 'Butwal', 'Pokhara', '2025-01-27', 25, 1800.00),
(122, 'Valley Star Express', 'Butwal', 'Pokhara', '2025-01-27', 48, 1000.00),
(123, 'Mountain Valley Line', 'Butwal', 'Pokhara', '2025-01-27', 20, 2200.00),
(124, 'Green Star Express', 'Kathmandu', 'Pokhara', '2025-02-01', 15, 3500.00),
(125, 'Royal Mountain Express', 'Pokhara', 'Kathmandu', '2025-02-01', 12, 3800.00),
(126, 'Himalayan Star Line', 'Kathmandu', 'Chitwan', '2025-02-01', 18, 2800.00),
(127, 'Mountain Night Line', 'Kathmandu', 'Pokhara', '2025-01-22', 0, 2300.00),
(128, 'Green Express Night', 'Kathmandu', 'Pokhara', '2025-01-22', 45, 1100.00),
(129, 'Royal Night Express', 'Pokhara', 'Kathmandu', '2025-01-24', 16, 2600.00),
(130, 'Blue Star Morning', 'Kathmandu', 'Pokhara', '2025-01-22', 25, 1900.00),
(131, 'Valley Morning Express', 'Pokhara', 'Kathmandu', '2025-01-24', 40, 1300.00),
(132, 'Mountain Morning Line', 'Chitwan', 'Kathmandu', '2025-02-19', 35, 1400.00);

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`) VALUES
(5, 'Biratnagar'),
(8, 'Birgunj'),
(4, 'Butwal'),
(3, 'Chitwan'),
(9, 'Dhangadhi'),
(10, 'Itahari'),
(6, 'Janakpur'),
(1, 'Kathmandu'),
(7, 'Nepalgunj'),
(2, 'Pokhara');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(10) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone_number`, `dob`, `gender`, `password`, `role`, `created_at`) VALUES
(3, 'Pema Lama', 'pema123@gmail.com', '9868043230', '2004-03-25', 'Male', '$2y$10$sNZpamWkclu5q2v4r6w55OBrKgedeREfPEMLZ6Or4o1XHA7AMauSy', 'admin', '2024-11-25 10:20:36'),
(4, 'Hisi Maharjan', 'hisimaharjan1@gmail.com', '9848503066', '2003-03-26', 'Female', '$2y$10$qhaboriw8aChVg5/BD/OMu.sfrjfMaj.C1WFAvIu8V42DqDWmq0F6', 'user', '2024-11-25 10:57:50'),
(5, 'Gelek Namgyal Tamang', 'geleknamgyal51@gmail.com', '9863164952', '2003-05-08', 'Male', '$2y$10$ykjmSnJZd0AHYzSmJJftZ.uWi/tYCC19.lrpmXR5GH0efznFBqZoW', 'user', '2024-11-25 10:58:51'),
(7, 'Bibek karki', 'bibek123@gmail.com', '9845673452', '2005-06-07', 'Male', '$2y$10$.dVvk1kTZQ7NfKCOBnKzOOyqV/HkDFfoCIO2xCjs3a/j50C.2F78K', 'admin', '2024-11-25 11:00:35'),
(16, 'Ram lala', 'ram@gmail.com', '9876543212', '2024-01-31', 'Male', '$2y$10$M0AvQnfdcp.vDehzSxj9r.1IhHqhNmOX4JKVCaAZ6AohV2n3z5wLS', 'user', '2024-12-03 16:43:44'),
(17, 'Kunsang Lhamu Tamang', 'kunsangt107@gmail.com', '9762267691', '2006-03-02', 'Female', '$2y$10$qIydgPtCzugfZhNGQLNsCeM4RxRuUGtznqvQwI.uvP/umGe/BteSC', 'user', '2025-01-21 03:13:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_city_name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
