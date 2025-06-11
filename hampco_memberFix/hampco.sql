-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 04:36 PM
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
-- Database: `hampco`
--

-- --------------------------------------------------------

--
-- Table structure for table `assigned_tasks`
--

CREATE TABLE IF NOT EXISTS `assigned_tasks` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `prod_line_id` INT NOT NULL,
    `member_id` INT NOT NULL,
    `role` VARCHAR(50) NOT NULL,
    `status` ENUM('Pending', 'Accepted', 'Declined', 'In Progress', 'Completed') DEFAULT 'Pending',
    `deadline` DATE NOT NULL,
    `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`prod_line_id`) REFERENCES `production_line`(`prod_line_id`) ON DELETE CASCADE,
    FOREIGN KEY (`member_id`) REFERENCES `user_member`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `task_progress`
--

CREATE TABLE IF NOT EXISTS `task_progress` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `assigned_task_id` INT NOT NULL,
    `progress_note` TEXT,
    `completion_percentage` INT DEFAULT 0,
    `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`assigned_task_id`) REFERENCES `assigned_tasks`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `cart_user_id` int(11) NOT NULL,
  `cart_prod_id` int(11) NOT NULL,
  `cart_Qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `cart_user_id`, `cart_prod_id`, `cart_Qty`) VALUES
(4, 6, 8, 12),
(5, 6, 7, 5);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `prod_id` int(11) NOT NULL,
  `prod_category_id` int(11) NOT NULL,
  `prod_name` varchar(255) NOT NULL,
  `prod_image` varchar(255) NOT NULL,
  `prod_stocks` int(11) NOT NULL,
  `prod_price` decimal(10,2) NOT NULL,
  `prod_description` text DEFAULT NULL,
  `prod_status` int(11) NOT NULL DEFAULT 1 COMMENT '0=archived,1=exist'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`prod_id`, `prod_category_id`, `prod_name`, `prod_image`, `prod_stocks`, `prod_price`, `prod_description`, `prod_status`) VALUES
(7, 1, 'Plain and design', 'product_683851b61f1762.82027357.jpg', 5, 4680.00, 'Piña Seda Dyed 36"W', 1),
(8, 2, 'Barong tagalog', 'product_6838526ad1dff7.75750727.jpg', 12, 999.00, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(60) NOT NULL,
  `category_description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`category_id`, `category_name`, `category_description`) VALUES
(1, 'Linawan', NULL),
(2, 'Pina fiber', NULL),
(3, 'Bastos', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_stock`
--

CREATE TABLE `product_stock` (
  `pstock_id` int(11) NOT NULL,
  `pstock_user_id` int(11) NOT NULL,
  `pstock_prod_id` varchar(60) NOT NULL,
  `pstock_stock_type` varchar(60) NOT NULL,
  `pstock_stock_outQty` int(11) NOT NULL,
  `pstock_stock_changes` text NOT NULL,
  `pstock_stock_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_stock`
--

INSERT INTO `product_stock` (`pstock_id`, `pstock_user_id`, `pstock_prod_id`, `pstock_stock_type`, `pstock_stock_outQty`, `pstock_stock_changes`, `pstock_stock_date`) VALUES
(3, 1, '7', 'Stock In', 100, '0 -> 100', '2025-05-29 12:57:07'),
(4, 1, '8', 'Stock In', 100, '0 -> 100', '2025-05-29 12:57:17');

-- --------------------------------------------------------

--
-- Table structure for table `production_line`
--

CREATE TABLE `production_line` (
  `prod_line_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `length_m` DECIMAL(10,3) NOT NULL,
  `width_m` DECIMAL(10,3) NOT NULL,
  `weight_g` DECIMAL(10,3) NOT NULL DEFAULT 0,
  `quantity` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`prod_line_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_assignments`
--

CREATE TABLE IF NOT EXISTS `task_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prod_line_id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `role` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `estimated_time` int NOT NULL COMMENT 'Estimated time in days',
  `deadline` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `prod_line_id` (`prod_line_id`),
  KEY `member_id` (`member_id`),
  CONSTRAINT `task_assignments_ibfk_1` FOREIGN KEY (`prod_line_id`) REFERENCES `production_line` (`prod_line_id`) ON DELETE CASCADE,
  CONSTRAINT `task_assignments_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `user_member` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `raw_materials_name` varchar(60) NOT NULL,
  `category` text DEFAULT NULL,
  `rm_quantity` DECIMAL(10,3) NOT NULL,
  `rm_unit` varchar(20) NOT NULL,
  `rm_status` varchar(60) NOT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`id`, `raw_materials_name`, `category`, `rm_quantity`, `rm_unit`, `rm_status`, `supplier_name`) VALUES
(9, 'Silk', NULL, 802, 'gram', 'Available', NULL),
(10, 'Piña Loose', 'Liniwan/Washout', 65, 'gram', 'Available', NULL),
(11, 'Piña Loose', 'Bastos', 56, 'gram', 'Available', NULL),
(12, 'silk 21d', '', 86670, 'gram', 'Available', NULL),
(13, 'fiver', 'dawdawd', 100004, 'kg', 'Available', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_history`
--

CREATE TABLE `stock_history` (
  `stock_id` int(11) NOT NULL,
  `stock_user_type` varchar(60) NOT NULL,
  `stock_raw_id` int(11) NOT NULL,
  `stock_user_id` int(11) NOT NULL,
  `stock_type` varchar(60) NOT NULL,
  `stock_outQty` DECIMAL(10,2) NOT NULL,
  `stock_changes` text NOT NULL,
  `stock_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_history`
--

INSERT INTO `stock_history` (`stock_id`, `stock_user_type`, `stock_raw_id`, `stock_user_id`, `stock_type`, `stock_outQty`, `stock_changes`, `stock_date`) VALUES
(31, 'Administrator', 9, 1, 'Stock In', 10, '792 -> 802', '2025-05-29 12:42:13');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `task_user_id` int(11) NOT NULL,
  `task_name` varchar(60) NOT NULL,
  `task_material` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`task_material`)),
  `task_category` varchar(60) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date DEFAULT NULL,
  `status` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

-- No data for task table since we're removing the existing process

-- --------------------------------------------------------

--
-- Table structure for table `user_admin`
--

CREATE TABLE `user_admin` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_admin`
--

INSERT INTO `user_admin` (`id`, `fullname`, `username`, `password`, `date_created`) VALUES
(1, 'John Doe', 'admin', '$2y$10$JQ1lmgWTeqdSVD3DFIibqeE.0BAjjBrhaBNt5qdLOXV5Fa6os7me.', '2025-05-04 09:54:34');

-- --------------------------------------------------------

--
-- Table structure for table `user_customer`
--

CREATE TABLE `user_customer` (
  `customer_id` int(11) NOT NULL,
  `customer_fullname` varchar(60) NOT NULL,
  `customer_email` varchar(60) NOT NULL,
  `customer_phone` varchar(50) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `customer_status` int(11) NOT NULL DEFAULT 1 COMMENT '0=restricted,1=active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_customer`
--

INSERT INTO `user_customer` (`customer_id`, `customer_fullname`, `customer_email`, `customer_phone`, `customer_password`, `customer_status`) VALUES
(6, 'Joshua Anderson Padilla', 'jcustom@gmail.com', '09454454741', '$2y$10$Ehvc1AwmVnjhfMT.arbdEuseTS2l9bR9P0eQRjpDOXHHh7eZ9Shx6', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_member`
--

CREATE TABLE `user_member` (
  `id` int(11) NOT NULL,
  `id_number` varchar(60) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(60) NOT NULL,
  `phone` varchar(60) NOT NULL,
  `role` varchar(60) NOT NULL,
  `sex` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0=NonVerify,1=Verified,2=disabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_member`
--



-- --------------------------------------------------------

--
-- Table structure for table `weaver`
--

CREATE TABLE `weaver` (
  `id` int(11) NOT NULL,
  `category` varchar(60) NOT NULL,
  `product` varchar(60) NOT NULL,
  `product_details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`product_details`)),
  `status` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`prod_id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `product_stock`
--
ALTER TABLE `product_stock`
  ADD PRIMARY KEY (`pstock_id`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_history`
--
ALTER TABLE `stock_history`
  ADD PRIMARY KEY (`stock_id`),
  ADD KEY `stock_raw_id` (`stock_raw_id`),
  ADD KEY `stock_user_id` (`stock_user_id`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `user_admin`
--
ALTER TABLE `user_admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_customer`
--
ALTER TABLE `user_customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `user_member`
--
ALTER TABLE `user_member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `weaver`
--
ALTER TABLE `weaver`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `prod_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `product_category`
--
ALTER TABLE `product_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `product_stock`
--
ALTER TABLE `product_stock`
  MODIFY `pstock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `stock_history`
--
ALTER TABLE `stock_history`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_admin`
--
ALTER TABLE `user_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_customer`
--
ALTER TABLE `user_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_member`
--
ALTER TABLE `user_member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `weaver`
--
ALTER TABLE `weaver`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Table structure for product raw material requirements
CREATE TABLE IF NOT EXISTS `product_raw_materials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(60) NOT NULL,
  `raw_material_name` varchar(60) NOT NULL,
  `raw_material_category` varchar(60) DEFAULT NULL,
  `consumption_rate` DECIMAL(10,3) NOT NULL,
  `consumption_unit` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert product raw material requirements with area-based consumption rates
INSERT INTO `product_raw_materials` (`product_name`, `raw_material_name`, `raw_material_category`, `consumption_rate`, `consumption_unit`) VALUES
('Piña Seda', 'Piña Loose', 'Bastos', 20.000, 'g/m²'),
('Piña Seda', 'Silk', NULL, 9.000, 'g/m²'),
('Pure Piña Cloth', 'Piña Loose', 'Liniwan/Washout', 30.000, 'g/m²'),
('Knotted Piña Loose', 'Piña Loose', 'Bastos', 1.000, 'g/g'),
('Warped Silk', 'Silk', NULL, 1.000, 'g/g');