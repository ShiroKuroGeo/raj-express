-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 15, 2024 at 07:03 AM
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
(1, 1, 'user', '123123123', '10.311351526406', '123.96130442619', 'sant dominic', '12123', 'basta', '2024-11-12 04:00:49', '2024-11-12 04:00:49');

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
(1, 32, 16, 2, 'order', '2024-11-12 03:57:12', '2024-11-12 03:57:12'),
(2, 32, 16, 2, 'order', '2024-11-12 09:52:23', '2024-11-12 09:52:23');

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
(17, 'Today\'s Special', 'Active', '2024-11-12 03:50:33', '2024-11-12 03:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `category_name` varchar(125) NOT NULL,
  `description` text NOT NULL,
  `amount` int(11) NOT NULL,
  `date` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `category_name`, `description`, `amount`, `date`, `created_at`, `updated_at`) VALUES
(1, 'Tailoer', 'Tailoer ni siya guys', 124, '2024-11-08', '2024-11-15 05:20:56', '2024-11-15 05:20:56'),
(2, 'Special Child', 'This special child is sale.', 10, '2024-11-16', '2024-11-15 05:53:33', '2024-11-15 05:53:33'),
(3, 'Tailoer', 'Tailoer ni siya guys', 124, '2024-11-015', '2024-11-15 05:20:56', '2024-11-15 05:20:56');

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
(1, 16, 1, 'Hello world', '0', 'sent', '2024-11-12 10:01:06', '2024-11-12 10:01:06'),
(2, 15, 16, 'HI TEST ADMIN', '0', 'sent', '2024-11-12 10:02:54', '2024-11-12 10:02:54');

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
(1, 16, 'cusord2483', 'Good Day, your has been delivered', '2024-11-12 04:27:38', '2024-11-12 04:27:38'),
(2, 16, NULL, 'Good Day, Raj Express send you a message!', '2024-11-12 10:02:54', '2024-11-12 10:02:54');

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
  `status` enum('returned','cancelled','delivered','pending','processing','over-the-counter') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `product_id`, `address_id`, `payment_id`, `customer_reference`, `order_qty`, `extra`, `status`, `created_at`, `updated_at`) VALUES
(94, 1, 32, 1, 99, 'cusord2499', 1, '', 'over-the-counter', '2024-11-15 04:17:18', '2024-11-15 04:17:18');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `pos_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(125) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` int(11) NOT NULL,
  `payment_status` varchar(125) NOT NULL,
  `status` varchar(125) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_At` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`pos_id`, `order_id`, `product_id`, `product_name`, `quantity`, `price`, `payment_status`, `status`, `created_at`, `updated_At`) VALUES
(5, 94, 32, 'Pancit Cantoon', 1, 25, 'Paid', 'cash', '2024-11-15 04:17:18', '2024-11-15 04:17:18');

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
(83, 16, 'cash', 50, 'paid'),
(84, 16, 'cash', 50, 'pending'),
(85, 1, 'cash', 25, 'paid'),
(86, 1, 'cash', 25, 'paid'),
(87, 1, 'cash', 25, 'paid'),
(88, 1, 'cash', 25, 'paid'),
(89, 1, 'walk-in', 25, 'over-the-counter'),
(90, 1, 'walk-in', 25, 'over-the-counter'),
(91, 1, 'walk-in', 25, 'over-the-counter'),
(92, 1, 'walk-in', 25, 'over-the-counter'),
(93, 1, 'walk-in', 25, 'over-the-counter'),
(94, 1, 'walk-in', 25, 'over-the-counter'),
(95, 1, 'walk-in', 25, 'over-the-counter'),
(96, 1, 'walk-in', 25, 'over-the-counter'),
(97, 1, 'walk-in', 25, 'over-the-counter'),
(98, 1, 'walk-in', 25, 'over-the-counter'),
(99, 1, 'walk-in', 25, 'over-the-counter');

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
(32, 17, 'Pancit Cantoon', 'This is pancit cantoon.', 25.00, 'Available', '6732d17db6592_pancit Cantoon.png', '2024-11-12 03:54:37', '2024-11-12 03:54:37');

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
(1, 'Raj', 'Express', 'Basak, Sudtongan', '09123456789', 'rajexpress514@gmail.com', '$2y$10$Sp7zKFUx38LYLEVM8bQqnefzYr7XGSr3JepLpLU.ArmGJHG9Sj0Km', 'admin', 'profile.jpg', 'active', NULL, '2024-11-12 03:48:42', '2024-11-12 17:26:33'),
(16, 'user', 'user', 'unknown', '09123456789', 'user@gmail.com', '$2y$10$qsVacdqsPmbSyeYyEhA0f.lpUooEX.Ub6zG6wFtTogaLSnDI3Ypoe', 'customer', 'profile.jpg', 'active', NULL, '2024-11-12 03:55:24', '2024-11-12 03:55:24'),
(17, '123', '123', '123', '123', 'inoc@gmail.com', '$2y$10$o/FF1oX7oR7wrQWRnGLKve8UE1Iuj7GkPHfH02c54kDhBZbgHiOlG', 'customer', 'profile.jpg', 'active', NULL, '2024-11-12 08:13:17', '2024-11-12 08:13:17'),
(18, '123', '123', '123', '123', '123@gmail.cm', '$2y$10$h89Q9.AIgO35rvs.XzMAf.jfqgqgCRGZczbD7RbNSBU6A4B2eXILi', 'customer', 'profile.jpg', 'active', NULL, '2024-11-12 08:13:27', '2024-11-12 08:13:27'),
(19, '123', '123', '123', '123', '123@gmail,com', '$2y$10$D7q1KyWZTh1sarQAvonDC.TGfca8pHNfvB3rPHEGYfVj9CH2QVzpO', 'customer', 'profile.jpg', 'active', NULL, '2024-11-12 08:15:06', '2024-11-12 08:15:06'),
(20, '123', '123', '123', '123', '123@gmail.com', '$2y$10$j/Y..78AAB0QezatLHTaKeDYKWqktroUzMzQJQ6qBnveoRF1F/Fim', 'customer', 'profile.jpg', 'active', NULL, '2024-11-12 08:16:08', '2024-11-12 08:16:08'),
(21, 'Hrirh', 'Hdhdh', 'Hdhdh', '91748494', 'Inoc123@gmail.com', '$2y$10$eSf4d3lZ406LtCBmZ0ETlOJBGL6YFbucfpmDthyAirE/5Ew4WjQMe', 'customer', 'profile.jpg', 'active', NULL, '2024-11-12 08:18:33', '2024-11-12 08:18:33');

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
(4, 16, 32, '2024-11-12 10:00:50', '2024-11-12 10:00:50');

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
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`);

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
  ADD PRIMARY KEY (`pos_id`);

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
  MODIFY `addOn_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `msg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notif_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `pos_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `wishlist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
