-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2025 at 06:51 AM
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
,`image` varchar(100)
,`cart_total` decimal(20,2)
);

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `img_id` int(11) NOT NULL,
  `image` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`img_id`, `image`) VALUES
(1, '192-200x300.jpg'),
(2, '602-200x300.jpg'),
(3, '610-200x200.jpg'),
(4, '875-500x500.jpg'),
(5, '610-200x200.jpg'),
(6, '192-200x300.jpg'),
(7, 'u1f44d_u1f602.png'),
(8, ''),
(9, 'u1f44d_u1f925.png'),
(10, 'u1f44d_u1f631.png'),
(11, 'Screenshot_20250602_223545_Instagram.jpg'),
(12, '20250428_235623.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
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

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `uid`, `shipping_fee`, `total`, `status`, `shipping_address`, `phone`, `payment_method`, `payment_status`, `khalti_token`, `notes`, `admin_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 13.21, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, 'Test 1', NULL, '2025-07-23 04:34:12', NULL),
(2, 1, 0, 153.00, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, '', NULL, '2025-07-23 04:43:37', NULL),
(3, 1, 10, 13.24, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, '', NULL, '2025-07-23 04:44:40', NULL),
(4, 1, 10, 41.00, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, '', NULL, '2025-07-23 04:45:25', NULL),
(5, 1, 10, 11.62, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, '', NULL, '2025-07-23 04:45:35', NULL),
(6, 1, 0, 1200.00, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, '', NULL, '2025-07-23 04:45:52', NULL),
(7, 1, 0, 153.00, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, '', NULL, '2025-07-23 04:47:15', NULL),
(8, 1, 10, 11.00, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, '', NULL, '2025-07-23 04:49:26', NULL),
(9, 1, 10, 10.03, 'pending', 'Bhaktapur, Suryabinayak', '9800000000', 'cod', 'pending', NULL, '', NULL, '2025-07-23 04:49:55', NULL);

-- --------------------------------------------------------

--
-- Stand-in structure for view `order_details`
-- (See below for the actual view)
--
CREATE TABLE `order_details` (
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

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_id`, `quantity`, `order_price`, `sub_total`) VALUES
(96, 1, 5, 7, 0.03, 0),
(97, 1, 11, 3, 1.00, 3),
(98, 2, 4, 1, 153.00, 153),
(99, 3, 6, 2, 1.62, 3),
(100, 4, 11, 1, 1.00, 1),
(101, 4, 10, 1, 30.00, 30),
(102, 5, 6, 1, 1.62, 2),
(103, 6, 1, 1, 1200.00, 1200),
(104, 7, 4, 1, 153.00, 153),
(105, 8, 11, 1, 1.00, 1),
(106, 9, 5, 1, 0.03, 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` varchar(100) NOT NULL,
  `img_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `product_price`, `stock`, `category`, `description`, `img_id`) VALUES
(1, 'product name1', 1200.00, 994, 'Game', 'product', 1),
(4, 'product name4', 153.00, 975, 'earphone', 'fadshjke', 4),
(5, 'product name', 0.03, 930, 'laptop', 'cxz', 5),
(6, 'product name', 1.62, 979, 'speaker', 'zX', 6),
(7, 'Apple', 100.00, 999, 'fruit', 'Apple', 7),
(9, 'Carrot', 10.00, 999, 'Vegitable', 'Orange', 9),
(10, 'Banana', 30.00, 998, 'Fruit', 'Banana', 10),
(11, 'OAAAE!', 1.00, 966, 'Game', 'Ke hereko handium! ?', 11),
(12, 'Test2', 13.00, 991, 'Category2', 'Testing categories', 12);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(1, 'Aatish', 'a@example.com', 'password', 'user', 'active', '2025-06-27 04:42:45');

-- --------------------------------------------------------

--
-- Structure for view `cart_details`
--
DROP TABLE IF EXISTS `cart_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `cart_details`  AS SELECT `c`.`cart_id` AS `cart_id`, `c`.`user_id` AS `user_id`, `c`.`product_id` AS `product_id`, `c`.`quantity` AS `quantity`, `p`.`product_name` AS `product_name`, `p`.`product_price` AS `product_price`, `p`.`stock` AS `stock`, `i`.`image` AS `image`, `c`.`quantity`* `p`.`product_price` AS `cart_total` FROM ((`cart` `c` join `products` `p` on(`c`.`product_id` = `p`.`product_id`)) join `image` `i` on(`p`.`img_id` = `i`.`img_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `order_details`
--
DROP TABLE IF EXISTS `order_details`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `order_details`  AS SELECT `o`.`uid` AS `uid`, `o`.`order_id` AS `order_id`, `oi`.`order_item_id` AS `order_item_id`, `oi`.`product_id` AS `product_id`, `p`.`product_name` AS `product_name`, `i`.`image` AS `image`, `oi`.`price` AS `price`, `oi`.`quantity` AS `quantity`, `o`.`total` AS `total` FROM (((`orders` `o` join `order_items` `oi` on(`o`.`order_id` = `oi`.`order_id`)) join `products` `p` on(`p`.`product_id` = `oi`.`product_id`)) join `image` `i` on(`i`.`img_id` = `p`.`img_id`)) ;

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
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`img_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`uid`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `FK_ProductImage` (`img_id`);

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
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `img_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `FK_ProductImage` FOREIGN KEY (`img_id`) REFERENCES `image` (`img_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
