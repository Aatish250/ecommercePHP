-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 11:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecom_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `cart_details`
-- (See below for the actual view)
--
CREATE TABLE `cart_details` (
`cart_id` int(11)
,`user_id` int(11)
,`product_id` int(11)
,`quantity` int(11)
,`product_name` varchar(50)
,`product_price` decimal(10,2)
,`stock` int(11)
,`image` varchar(50)
,`cart_total` decimal(20,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `shipping_fee` decimal(10,0) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `payment_method` enum('cod','khalti') DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `khalti_token` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_details`
-- (See below for the actual view)
--
CREATE TABLE `order_details` (
`user_id` int(11)
,`order_id` int(11)
,`order_item_id` int(11)
,`product_id` int(11)
,`product_name` varchar(50)
,`image` varchar(50)
,`category` varchar(50)
,`current_stock` int(11)
,`order_price` decimal(10,2)
,`quantity` int(11)
,`sub_total` decimal(15,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_price` decimal(10,2) NOT NULL,
  `sub_total` decimal(15,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `shipping_address` varchar(100) NOT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `gender` enum('male','female') NOT NULL DEFAULT 'male',
  `dob` datetime DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_logged_in` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `shipping_address`, `phone`, `gender`, `dob`, `role`, `status`, `created_at`, `last_logged_in`) VALUES
(1, 'admin', 'admin@ecom.localhost', 'password', '', NULL, 'male', NULL, 'user', 'active', '2025-07-24 09:00:18', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_details`
-- (See below for the actual view)
--
CREATE TABLE `user_details` (
`user_id` int(11)
,`username` varchar(100)
,`email` varchar(100)
,`shipping_address` varchar(100)
,`gender` varchar(6)
,`dob` varchar(19)
,`phone` varchar(15)
,`created_at` timestamp
,`last_logged_in` timestamp
,`user_statu` enum('active','inactive')
,`total_orders` bigint(21)
,`paid_items` decimal(32,0)
,`paid_total` decimal(32,2)
,`pending_items` decimal(32,0)
,`pending_total` decimal(32,2)
,`total_items` decimal(32,0)
,`total_transaction` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Structure for view `cart_details`
--
DROP TABLE IF EXISTS `cart_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `cart_details`  AS SELECT `c`.`cart_id` AS `cart_id`, `c`.`user_id` AS `user_id`, `c`.`product_id` AS `product_id`, `c`.`quantity` AS `quantity`, `p`.`product_name` AS `product_name`, `p`.`product_price` AS `product_price`, `p`.`stock` AS `stock`, `p`.`image` AS `image`, `c`.`quantity`* `p`.`product_price` AS `cart_total` FROM (`cart` `c` join `products` `p` on(`c`.`product_id` = `p`.`product_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `order_details`
--
DROP TABLE IF EXISTS `order_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_details`  AS SELECT `o`.`user_id` AS `user_id`, `o`.`order_id` AS `order_id`, `oi`.`order_item_id` AS `order_item_id`, `oi`.`product_id` AS `product_id`, `p`.`product_name` AS `product_name`, `p`.`image` AS `image`, `p`.`category` AS `category`, `p`.`stock` AS `current_stock`, `oi`.`order_price` AS `order_price`, `oi`.`quantity` AS `quantity`, `oi`.`sub_total` AS `sub_total` FROM ((`orders` `o` join `order_items` `oi` on(`o`.`order_id` = `oi`.`order_id`)) join `products` `p` on(`p`.`product_id` = `oi`.`product_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `user_details`
--
DROP TABLE IF EXISTS `user_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_details`  AS SELECT `u`.`user_id` AS `user_id`, `u`.`username` AS `username`, `u`.`email` AS `email`, coalesce(`u`.`shipping_address`,'') AS `shipping_address`, coalesce(`u`.`gender`,'male') AS `gender`, coalesce(`u`.`dob`,'') AS `dob`, coalesce(`u`.`phone`,0) AS `phone`, `u`.`created_at` AS `created_at`, coalesce(`u`.`last_logged_in`,`u`.`created_at`) AS `last_logged_in`, `u`.`status` AS `user_statu`, coalesce(count(distinct `o`.`order_id`),0) AS `total_orders`, coalesce((select sum(`oi`.`quantity`) from (`order_details` `oi` left join `orders` `o` on(`o`.`order_id` = `oi`.`order_id`)) where `o`.`user_id` = `u`.`user_id` and `o`.`payment_status` = 'paid'),0) AS `paid_items`, coalesce((select sum(`orders`.`total`) from `orders` where `orders`.`payment_status` = 'paid' and `orders`.`user_id` = `u`.`user_id`),0) AS `paid_total`, coalesce((select sum(`oi`.`quantity`) from (`order_details` `oi` left join `orders` `o` on(`o`.`order_id` = `oi`.`order_id`)) where `o`.`user_id` = `u`.`user_id` and `o`.`payment_status` = 'pending'),0) AS `pending_items`, coalesce((select sum(`orders`.`total`) from `orders` where `orders`.`payment_status` = 'pending' and `orders`.`user_id` = `u`.`user_id`),0) AS `pending_total`, coalesce((select sum(`oi`.`quantity`) from (`order_details` `oi` left join `orders` `o` on(`o`.`order_id` = `oi`.`order_id`)) where `o`.`user_id` = `u`.`user_id`),0) AS `total_items`, coalesce(sum(`o`.`total`),0) AS `total_transaction` FROM (`users` `u` left join `orders` `o` on(`u`.`user_id` = `o`.`user_id`)) WHERE `u`.`role` = 'user' GROUP BY `u`.`user_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `fk_order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
