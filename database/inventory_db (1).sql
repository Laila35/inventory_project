-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 02, 2025 at 03:04 PM
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
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`, `status`) VALUES
(1, 'SOAP', '2025-09-26 21:34:33', 'active'),
(2, 'saima', '2025-09-26 21:37:11', 'active'),
(3, 'shayan', '2025-09-26 21:45:38', 'inactive'),
(4, 'Manager', '2025-10-01 09:59:25', 'active'),
(5, 'Employer', '2025-10-01 09:59:57', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `short_name` varchar(50) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `short_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'code vista', 'cv', 'active', '2025-09-27 05:11:46', '2025-09-26 20:11:46'),
(2, 'code vista', 'cv', 'inactive', '2025-09-27 05:42:09', '2025-09-26 20:42:15'),
(3, 'Cyber Div 101', 'CV 101', 'active', '2025-10-01 17:00:47', '2025-10-01 08:00:47'),
(4, 'Fouji Foundation Fartilizar', 'FFC', 'active', '2025-10-01 17:01:54', '2025-10-01 08:01:54'),
(5, 'Inotech Solution', 'Inotech', 'active', '2025-10-01 17:02:45', '2025-10-01 08:02:45'),
(6, '5D solution', '5D', 'active', '2025-10-01 17:03:10', '2025-10-01 08:03:10');

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`id`, `name`, `created_at`) VALUES
(1, 'ABC PVT LTD', '2025-09-25 13:28:19');

-- --------------------------------------------------------

--
-- Table structure for table `location_rack`
--

CREATE TABLE `location_rack` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `location_rack`
--

INSERT INTO `location_rack` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'ahmad', 'inactive', '2025-09-30 18:52:20', '2025-09-30 09:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `location_racks`
--

CREATE TABLE `location_racks` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `location_racks`
--

INSERT INTO `location_racks` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Laila Batool ', 'active', '2025-09-26 21:11:52', '2025-09-26 21:12:06');

-- --------------------------------------------------------

--
-- Table structure for table `new_products`
--

CREATE TABLE `new_products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `location_rack` varchar(100) NOT NULL,
  `available_quantity` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `new_products`
--

INSERT INTO `new_products` (`id`, `product_name`, `company`, `location_rack`, `available_quantity`, `status`, `created_at`, `updated_at`) VALUES
(1, 'KNN', 'CNN', 'SDF', 32, 'active', '2025-09-27 18:02:10', '2025-09-27 09:36:05');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_name` varchar(255) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_name`, `customer_name`, `total_amount`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'saphire', 'all', 1200.00, '', 10, '2025-09-27 18:16:20', '2025-09-27 18:41:23'),
(2, 'bonanza', 'sattar', 3400.00, '', 10, '2025-09-27 18:17:01', '2025-09-27 18:41:21'),
(3, 'bonanza', 'abbas', 3000.00, 'Completed', 10, '2025-09-30 09:58:11', '2025-09-30 09:58:11');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `rack_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_purchase`
--

CREATE TABLE `product_purchase` (
  `id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `batch_no` varchar(100) NOT NULL,
  `supplier` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `available_qty` int(11) NOT NULL,
  `price_per_unit` decimal(10,2) NOT NULL,
  `total_cost` decimal(10,2) NOT NULL,
  `mfr_date` date NOT NULL,
  `expiry_date` date NOT NULL,
  `sales_price` decimal(10,2) NOT NULL,
  `purchase_date` date NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `product_purchase`
--

INSERT INTO `product_purchase` (`id`, `product_name`, `batch_no`, `supplier`, `quantity`, `available_qty`, `price_per_unit`, `total_cost`, `mfr_date`, `expiry_date`, `sales_price`, `purchase_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'nuggets', '123', 'ALI', 200, 5, 123.00, 24600.00, '2025-09-17', '2025-10-10', 300.00, '2025-09-24', 'active', '2025-09-30 11:13:25', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` int(11) NOT NULL,
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_no` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `address`, `contact_no`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'AW', 'hr', '2346890', 'aw@gmail.com', 'inactive', '2025-09-26 14:37:22', '2025-09-30 03:14:22'),
(2, 'ayesha', 'peshawar', '234560', 'ayesha@gmail.com', 'active', '2025-09-30 03:14:49', '2025-09-30 03:14:49'),
(3, 'Asfand', 'Islamabad , Pakistan', '031968744622', 'asfand@gmail.com', 'active', '2025-10-01 01:04:56', '2025-10-01 01:04:56');

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percentage` decimal(5,2) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taxes`
--

INSERT INTO `taxes` (`id`, `name`, `percentage`, `status`, `created_at`, `updated_at`) VALUES
(1, 'traffic light', 10.00, 'active', '2025-09-26 14:43:36', '2025-09-26 14:43:36'),
(2, 'Electricity', 20.00, 'active', '2025-10-01 01:06:00', '2025-10-01 01:06:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `status`, `created_at`) VALUES
(10, 'Admin', 'admin@gmail.com', 'admin123', 1, 'inactive', '2025-09-25 15:13:23'),
(12, 'ayesha', 'ayesha@gmail.com', '$2y$10$fmtqdgWvm7RHo0NuyccQWu9OK/eoj8kyl6T/HCnWn7A89bdmuDneS', 0, 'active', '2025-09-26 17:39:56'),
(13, 'ali', 'ali@gmail.com', '$2y$10$t56qMWoxMUvQge/oYKs8w.cT/YNtyBlcJD2zapsUvodx9BBBtmCIq', 0, 'inactive', '2025-09-26 17:49:41'),
(14, 'Ahmad', 'ahmad@gmail.com', '$2y$10$qpHYm9nQ5Vw60kggbj5pqu6BJQhzLsZelHAukdzOuH4wgm.j3eCFq', 0, 'active', '2025-09-26 19:15:26'),
(15, 'asma', 'asma515@gmail.com', '$2y$10$pFMHe2Wvvr2BfP1r7JPHPea6h.D35OcytaGEx3.TY0K8KYW.qE3ky', 0, 'active', '2025-09-29 20:09:35'),
(16, 'SOAP', 'soap@gmail.com', '$2y$10$FuF.x52IHsGeHqP.wfAOLeX4K0gQhAgqUDSpi0.xjvdlQ2NfDmWw2', 0, 'active', '2025-09-29 20:11:09'),
(17, '123', '123@gmail.com', '$2y$10$9F9S77KvVhZe/Zw6mwXVWumasI65e/.lDmTOUEWzOBYoxUY1i8jOe', 0, 'active', '2025-09-29 22:31:14'),
(18, 'Rameen', 'Rameen@gmail.com', '$2y$10$M/4ugT2Rd87lVSJk731wMOF9Yrx9XOpYJViw1FrgZkWAVuvS76Bsi', 0, 'active', '2025-09-30 08:24:06'),
(19, 'washma', 'washma@gmail.com', '$2y$10$YND/jrp2CXPYyKyPr0.JeucPs7UwuuF63wA9WGqmUED6im926Q5ju', 0, 'active', '2025-09-30 09:59:13'),
(20, 'alian', 'alian@gmail.com', '$2y$10$akXXFqJbpN20XAAnBDovce0D33U0bUEOzvM.imqyp8GGwtBmhluzq', 0, 'active', '2025-09-30 10:11:12'),
(21, 'Ahsan', 'ahsan@gmail.com', '$2y$10$ci83a8Mk1Ri81EQeFRmICOsGr.2ja349DZMmD3w1ZSKtBC7M8BEIy', 1, 'active', '2025-10-01 07:57:52'),
(22, 'alia', 'alia@gmail.com', '$2y$10$jXfecc0RbN5U3.xJ7h.1eOW9kLnJYDWBq8ecnnGG5A274E9B6roBK', 0, 'active', '2025-10-02 12:35:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location_rack`
--
ALTER TABLE `location_rack`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location_racks`
--
ALTER TABLE `location_racks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `new_products`
--
ALTER TABLE `new_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_purchase`
--
ALTER TABLE `product_purchase`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_id` (`sale_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `location_rack`
--
ALTER TABLE `location_rack`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `location_racks`
--
ALTER TABLE `location_racks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `new_products`
--
ALTER TABLE `new_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_purchase`
--
ALTER TABLE `product_purchase`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
