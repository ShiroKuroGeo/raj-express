-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 10, 2024 at 07:18 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `raj-express`
--

-- --------------------------------------------------------

--
-- Table structure for table `addons`
--

CREATE TABLE `addons` (
  `addOn_id` int(11) NOT NULL,
  `ao_name` varchar(225) NOT NULL,
  `ao_price` int(11) NOT NULL,
  `ao_status` varchar(225) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addons`
--

INSERT INTO `addons` (`addOn_id`, `ao_name`, `ao_price`, `ao_status`, `created_at`, `updated_at`) VALUES
(1, 'Rice', 10, 'Available', '2024-10-29 04:09:43', '2024-10-29 04:09:43'),
(2, 'Lemon', 15, 'Available', '2024-10-29 04:09:43', '2024-10-29 04:09:43');

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `personName` varchar(225) NOT NULL,
  `phoneNumber` varchar(225) NOT NULL,
  `latitude` text NOT NULL,
  `longitude` text NOT NULL,
  `deliveryAddress` varchar(225) NOT NULL,
  `streetNumber` varchar(225) NOT NULL,
  `landmark` varchar(225) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`address_id`, `user_id`, `personName`, `phoneNumber`, `latitude`, `longitude`, `deliveryAddress`, `streetNumber`, `landmark`, `created_at`, `updated_at`) VALUES
(1, 12, 'george alfeser', '9484750030', '10.280548894584', '123.98963928223', 'Hello world', 'poblacion', 'amoa dapit', '2024-10-28 10:56:52', '2024-10-28 10:56:52'),
(2, 12, 'George alfeser', '9484750030', '', '', 'qweqweq', 'weqweqweqwe', 'qweqwe', '2024-10-31 08:00:05', '2024-10-31 08:00:05'),
(3, 12, 'shiro', '123123', '10.305313687645', '123.95251750946', '1231', '23123123', '', '2024-10-31 08:01:22', '2024-10-31 08:01:22'),
(4, 13, 'Shiro', '9484750030', '10.311330415284', '123.95607948303', '123123', '123123', '123123', '2024-11-05 00:33:55', '2024-11-05 00:33:55');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('pending','order') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`cart_id`, `product_id`, `user_id`, `quantity`, `status`, `created_at`, `updated_at`) VALUES
(19, 30, 13, 2, 'order', '2024-11-05 03:45:49', '2024-11-05 03:45:49'),
(20, 29, 13, 3, 'order', '2024-11-06 05:06:06', '2024-11-06 05:06:06'),
(21, 30, 12, 2, 'pending', '2024-11-07 08:17:54', '2024-11-07 08:17:54');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_status` enum('Active','Inactive') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `category_status`, `created_at`, `updated_at`) VALUES
(16, 'Today\'s Special', 'Active', '2024-10-24 03:20:29', '2024-10-24 03:21:37');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `msg_id` int(11) NOT NULL,
  `sender_id` bigint(20) NOT NULL,
  `receiver_id` bigint(20) NOT NULL,
  `content` text NOT NULL,
  `read_status` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('sent','delivered') NOT NULL DEFAULT 'sent',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`msg_id`, `sender_id`, `receiver_id`, `content`, `read_status`, `status`, `created_at`, `updated_at`) VALUES
(1, 13, 1, 'Hello world', '0', 'sent', '2024-11-06 02:54:37', '2024-11-06 02:54:37'),
(2, 1, 12, 'Sender Admin', '0', 'sent', '2024-11-06 03:08:17', '2024-11-06 03:08:17'),
(3, 12, 1, 'Sender user', '0', 'sent', '2024-11-06 03:09:10', '2024-11-06 03:09:10'),
(4, 1, 13, '123123123', '0', 'sent', '2024-11-06 03:09:14', '2024-11-06 03:09:14'),
(5, 13, 1, 'qweqwe', '0', 'sent', '2024-11-06 03:10:10', '2024-11-06 03:10:10'),
(6, 12, 1, 'I am Shiro Geo, The sender of this message.', '0', 'sent', '2024-11-07 02:26:33', '2024-11-07 02:26:33'),
(7, 1, 12, 'Hello, this is your admin. Raj express', '0', 'sent', '2024-11-07 02:29:25', '2024-11-07 02:29:25'),
(8, 1, 12, 'Again this is me. the admin.', '0', 'sent', '2024-11-07 02:29:52', '2024-11-07 02:29:52'),
(9, 12, 1, 'Hello world', '0', 'sent', '2024-11-07 02:32:35', '2024-11-07 02:32:35'),
(10, 12, 1, 'Testing', '0', 'sent', '2024-11-07 02:32:47', '2024-11-07 02:32:47'),
(11, 1, 12, 'Hello admin testing.', '0', 'sent', '2024-11-07 02:33:19', '2024-11-07 02:33:19'),
(12, 1, 12, 'Testing for Notification', '0', 'sent', '2024-11-08 11:44:38', '2024-11-08 11:44:38'),
(13, 1, 12, 'Testing again for Notification.', '0', 'sent', '2024-11-08 11:46:21', '2024-11-08 11:46:21'),
(14, 1, 12, 'Final Testing.', '0', 'sent', '2024-11-08 11:47:27', '2024-11-08 11:47:27');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notif_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `customer_ref` text DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notif_id`, `user_id`, `customer_ref`, `content`, `created_at`, `updated_at`) VALUES
(1, 12, 'cusord2430', 'Your order has been processed!', '2024-11-06 03:46:38', '2024-11-06 03:46:38'),
(2, 1, 'cusord2439', 'Good Day, your has been delivered', '2024-11-08 11:39:03', '2024-11-08 11:39:03'),
(3, 12, NULL, 'Good Day, Raj Express send you a message!', '2024-11-08 11:47:28', '2024-11-08 11:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `customer_reference` text DEFAULT NULL,
  `order_qty` int(11) NOT NULL,
  `extra` text DEFAULT NULL,
  `status` enum('returned','cancelled','delivered','pending','processing') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `product_id`, `address_id`, `payment_id`, `customer_reference`, `order_qty`, `extra`, `status`, `created_at`, `updated_at`) VALUES
(22, 13, 30, 4, 36, 'cusord2436', 2, '[{\"name\":\"Rice\",\"price\":10,\"quantity\":3},{\"name\":\"Lemon\",\"price\":15,\"quantity\":2}]', 'delivered', '2024-11-07 04:22:08', '2024-11-05 04:22:08'),
(23, 13, 30, 4, 37, 'cusord2437', 2, '[{\"name\":\"Rice\",\"price\":10,\"quantity\":3},{\"name\":\"Lemon\",\"price\":15,\"quantity\":2}]', 'delivered', '2024-11-05 04:28:08', '2024-11-05 04:28:08'),
(24, 1, 29, 1, 39, 'cusord2439', 1, 'null', 'delivered', '2024-11-08 10:33:05', '2024-11-08 10:33:05'),
(25, 1, 29, 1, 40, 'cusord2440', 1, 'null', 'pending', '2024-11-08 10:33:36', '2024-11-08 10:33:36'),
(26, 13, 29, 4, 41, 'cusord2441', 3, 'null', 'pending', '2024-11-10 05:51:58', '2024-11-10 05:51:58'),
(27, 13, 29, 0, 42, 'cusord2442', 3, 'null', 'pending', '2024-11-10 05:58:42', '2024-11-10 05:58:42'),
(28, 13, 29, 0, 43, 'cusord2443', 3, 'null', 'pending', '2024-11-10 05:59:10', '2024-11-10 05:59:10'),
(29, 13, 29, 0, 44, 'cusord2444', 3, 'null', 'pending', '2024-11-10 06:04:33', '2024-11-10 06:04:33'),
(30, 13, 29, 4, 45, 'cusord2445', 3, 'null', 'pending', '2024-11-10 06:16:10', '2024-11-10 06:16:10');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `orderitems_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_method` varchar(225) NOT NULL,
  `payment_total` int(11) NOT NULL,
  `payment_status` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `user_id`, `payment_method`, `payment_total`, `payment_status`) VALUES
(45, 13, 'online', 60, 'Paid on Online');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_description` text NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `product_status` enum('Available','Not Available') NOT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `product_description`, `product_price`, `product_status`, `product_image`, `created_at`, `updated_at`) VALUES
(29, 16, 'Denoldoug', 'Kalabasa nga Ginitaan', 20.00, 'Available', '6719bdf3394f4_squash_in_coconut_milk_1.jpg', '2024-10-24 03:24:35', '2024-10-24 03:24:35'),
(30, 16, 'Pancit', 'w/pork', 30.00, 'Available', '6719bf8359381_pancit-bihon-10.jpg', '2024-10-24 03:31:15', '2024-10-24 03:31:15'),
(31, 16, 'George Unique Food', 'Basta unique', 100.00, 'Available', '67272a8a0c1b0_WIN_20240708_19_42_20_Pro.jpg', '2024-11-03 07:47:22', '2024-11-03 07:47:22');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `feedback` varchar(225) NOT NULL,
  `fb_description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `user_id`, `product_id`, `feedback`, `fb_description`, `created_at`, `updated_at`) VALUES
(1, 13, 30, '4', 'Hello world', '2024-11-05 14:00:38', '2024-11-05 14:00:38'),
(2, 13, 30, '2', 'Ambo ang ', '2024-11-05 14:12:53', '2024-11-05 14:12:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `user_type` enum('admin','customer') NOT NULL,
  `profile_img` text DEFAULT 'profile.jpg',
  `status` enum('active','inactive') DEFAULT NULL,
  `date_deletion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `address`, `contact_number`, `email`, `password_hash`, `user_type`, `profile_img`, `status`, `date_deletion`, `created_at`, `updated_at`) VALUES
(1, 'Raj', 'express', 'Basak, Sudtonggan', '09123456789', 'rajexpress514@gmail.com', '$2y$10$Js0itdryd3LmLGDESkhbQecjBydaq15VRItNfyRFMjJ16BOTmQaP.', 'admin', 'profile.jpg', 'active', NULL, '2024-10-24 05:59:56', '2024-11-09 14:17:42'),
(12, 'George Alfeser', 'Inoc', '123', '123', 'inocgeorgealfeser@gmail.com', '$2y$10$8xulLStFVqIGhiX5rCXZ8ukfgW5zCc3N4I/hKkMPhtqVqnt2xbT.G', 'customer', '67230201c5192-WIN_20240708_19_43_20_Pro.jpg', 'active', NULL, '2024-10-28 01:28:31', '2024-10-31 08:34:27'),
(13, 'tqweytqy', 'tqyetyq', 'tqwteyq', 'qtwyetqy', '1@1', '$2y$10$bL.pjXjjuU7zYvmGQO7F9.WXyQrwkv9aSUHcuaJT7Bu4Eq4NhjjV.', 'customer', 'profile.jpg', 'active', NULL, '2024-10-31 09:54:27', '2024-11-09 14:09:57');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `wishlist_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created-at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`wishlist_id`, `user_id`, `product_id`, `created-at`, `updated_at`) VALUES
(3, 13, 30, '2024-11-06 02:28:47', '2024-11-06 02:28:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addons`
--
ALTER TABLE `addons`
  ADD PRIMARY KEY (`addOn_id`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`address_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`msg_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notif_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`orderitems_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_category_id` (`category_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`wishlist_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addons`
--
ALTER TABLE `addons`
  MODIFY `addOn_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `orderitems_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
