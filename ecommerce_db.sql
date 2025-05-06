-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2025 at 01:05 AM
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
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `added_at`) VALUES
(2, 2, 100, 3, '2025-04-30 20:37:49'),
(3, 3, 101, 2, '2025-04-30 20:37:49'),
(4, 4, 102, 1, '2025-04-30 20:37:50'),
(5, 4, 105, 4, '2025-04-30 20:37:50'),
(6, 5, 112, 2, '2025-04-30 20:37:50'),
(13, 2, 78, 1, '2025-04-30 22:06:20'),
(14, 2, 122, 8, '2025-04-30 22:31:50'),
(15, 2, 124, 35, '2025-04-30 22:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Smartphones', NULL),
(2, 'Laptops', NULL),
(3, 'Tablets', NULL),
(4, 'Mobile Accessories', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `total_amount` decimal(10,2) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_name`, `status`, `total_amount`, `order_date`) VALUES
(1, 1, 'Quantum Laptop', 'Delivered', 1299.99, '2023-10-15 14:30:00'),
(2, 1, 'Wireless Headphones', 'Shipped', 199.99, '2023-11-05 09:15:00'),
(3, 1, 'Smart Watch', 'Processing', 249.99, '2023-11-20 16:45:00'),
(4, 2, 'Bluetooth Speaker', 'Delivered', 89.99, '2023-10-22 11:20:00'),
(5, 2, 'USB-C Cable', 'Delivered', 19.99, '2023-11-10 13:10:00'),
(6, 3, 'Bluetooth Headphones Pro', 'Shipped', 129.99, '2025-03-18 16:30:15'),
(7, 4, '4K Smart TV 55\"', 'Delivered', 599.99, '2025-01-22 08:15:42'),
(8, 5, 'Fitness Tracker V3', 'Delivered', 79.99, '2025-02-28 19:20:37'),
(9, 6, 'Coffee Maker Elite', 'Shipped', 149.99, '2025-03-22 10:05:18'),
(10, 7, 'Gaming Mouse RGB', 'Pending', 49.99, '2025-04-01 13:40:29'),
(11, 8, 'External SSD 1TB', 'Processing', 129.99, '2025-03-27 17:25:11'),
(12, 9, 'Wireless Earbuds', 'Delivered', 59.99, '2025-02-14 12:15:08'),
(13, 10, 'Smart Watch Pro', 'Shipped', 199.99, '2025-03-15 09:30:45'),
(14, 11, 'Air Fryer XL', 'Delivered', 89.99, '2025-01-30 15:20:33'),
(15, 12, 'Noise Cancelling Headphones', 'Processing', 179.99, '2025-04-05 11:10:27'),
(16, 13, 'Robot Vacuum Cleaner', 'Pending', 299.99, '2025-04-10 14:45:19'),
(17, 14, 'Electric Toothbrush', 'Delivered', 39.99, '2025-02-20 08:30:52'),
(18, 15, 'Portable Bluetooth Speaker', 'Shipped', 69.99, '2025-03-25 16:15:41'),
(19, 16, 'Smart Light Bulbs (4-pack)', 'Processing', 49.99, '2025-04-03 10:20:38'),
(20, 17, 'Wireless Charging Pad', 'Delivered', 29.99, '2025-02-05 13:25:17'),
(21, 18, 'Laptop Stand', 'Pending', 34.99, '2025-04-12 09:15:26'),
(22, 19, 'Smart Doorbell', 'Shipped', 199.99, '2025-03-20 17:40:53'),
(23, 20, 'Electric Kettle', 'Delivered', 45.99, '2025-01-28 11:30:44'),
(24, 21, 'Action Camera 4K', 'Processing', 159.99, '2025-04-08 14:20:39'),
(25, 22, 'Wireless Gaming Controller', 'Shipped', 59.99, '2025-03-17 10:45:22'),
(26, 23, 'Smart Plug', 'Delivered', 24.99, '2025-02-10 08:15:33'),
(27, 24, 'Desk Lamp with USB', 'Pending', 39.99, '2025-04-15 12:30:18'),
(28, 25, 'External Hard Drive 2TB', 'Processing', 89.99, '2025-04-02 16:40:27'),
(29, 2, 'Tablet 10\"', 'Shipped', 249.99, '2025-03-05 14:25:16'),
(30, 3, 'Smartphone Case', 'Delivered', 19.99, '2025-02-18 09:40:55'),
(31, 5, 'Yoga Mat', 'Delivered', 29.99, '2025-01-10 17:15:42'),
(32, 7, 'Mechanical Keyboard', 'Processing', 99.99, '2025-04-06 11:30:28'),
(33, 9, 'Smart Scale', 'Shipped', 49.99, '2025-03-12 15:20:37'),
(34, 11, 'Air Purifier', 'Pending', 149.99, '2025-04-11 10:45:19'),
(35, 13, 'Smart Thermostat', 'Processing', 199.99, '2025-03-28 13:15:46'),
(36, 15, 'Wireless Mouse', 'Delivered', 24.99, '2025-02-22 08:30:33'),
(37, 17, 'HDMI Cable', 'Shipped', 12.99, '2025-03-14 16:40:22'),
(38, 19, 'Smart Lock', 'Processing', 179.99, '2025-04-07 09:25:17'),
(39, 21, 'Drone with Camera', 'Pending', 349.99, '2025-04-13 14:15:38'),
(40, 23, 'Phone Stand', 'Delivered', 14.99, '2025-02-25 11:20:44'),
(41, 25, 'USB-C Hub', 'Shipped', 39.99, '2025-03-24 17:30:29'),
(42, 4, 'Wireless Printer', 'Processing', 129.99, '2025-04-20 10:15:42'),
(43, 8, 'Smart Bulb Starter Kit', 'Pending', 59.99, '2025-04-18 13:45:31'),
(44, 12, 'Portable Monitor', 'Shipped', 229.99, '2025-04-16 15:20:28'),
(45, 16, 'Webcam HD', 'Processing', 69.99, '2025-04-17 11:30:19'),
(46, 20, 'Noise Cancelling Earbuds', 'Pending', 129.99, '2025-04-19 09:40:53'),
(47, 24, 'Laptop Backpack', 'Shipped', 49.99, '2025-04-14 14:25:37');
-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text NOT NULL,
  `stock` int(11) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `description`, `stock`, `brand`, `image`) VALUES
(78, 'Apple MacBook Pro 14 Inch Space Grey', 1999.99, 'The MacBook Pro 14 Inch in Space Grey is a powerful and sleek laptop, featuring Apple\'s M1 Pro chip for exceptional performance and a stunning Retina display.', 39, 'Apple', 'https://cdn.dummyjson.com/products/images/laptops/Apple%20MacBook%20Pro%2014%20Inch%20Space%20Grey/1.png'),
(79, 'Asus Zenbook Pro Dual Screen Laptop', 1799.99, 'The Asus Zenbook Pro Dual Screen Laptop is a high-performance device with dual screens, providing productivity and versatility for creative professionals.', 38, 'Asus', 'https://cdn.dummyjson.com/products/images/laptops/Asus%20Zenbook%20Pro%20Dual%20Screen%20Laptop/1.png'),
(80, 'Huawei Matebook X Pro', 1399.99, 'The Huawei Matebook X Pro is a slim and stylish laptop with a high-resolution touchscreen display, offering a premium experience for users on the go.', 34, 'Huawei', 'https://cdn.dummyjson.com/products/images/laptops/Huawei%20Matebook%20X%20Pro/1.png'),
(81, 'Lenovo Yoga 920', 1099.99, 'The Lenovo Yoga 920 is a 2-in-1 convertible laptop with a flexible hinge, allowing you to use it as a laptop or tablet, offering versatility and portability.', 71, 'Lenovo', 'https://cdn.dummyjson.com/products/images/laptops/Lenovo%20Yoga%20920/1.png'),
(82, 'New DELL XPS 13 9300 Laptop', 1499.99, 'The New DELL XPS 13 9300 Laptop is a compact and powerful device, featuring a virtually borderless InfinityEdge display and high-end performance for various tasks.', 18, 'Dell', 'https://cdn.dummyjson.com/products/images/laptops/New%20DELL%20XPS%2013%209300%20Laptop/1.png'),
(99, 'Amazon Echo Plus', 99.99, 'The Amazon Echo Plus is a smart speaker with built-in Alexa voice control. It features premium sound quality and serves as a hub for controlling smart home devices.', 50, 'Amazon', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Amazon%20Echo%20Plus/1.png'),
(100, 'Apple Airpods', 129.99, 'The Apple Airpods offer a seamless wireless audio experience. With easy pairing, high-quality sound, and Siri integration, they are perfect for on-the-go listening.', 93, 'Apple', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20Airpods/1.png'),
(101, 'Apple AirPods Max Silver', 549.99, 'The Apple AirPods Max in Silver are premium over-ear headphones with high-fidelity audio, adaptive EQ, and active noise cancellation. Experience immersive sound in style.', 7, 'Apple', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20AirPods%20Max%20Silver/1.png'),
(102, 'Apple Airpower Wireless Charger', 79.99, 'The Apple AirPower Wireless Charger provides a convenient way to charge your compatible Apple devices wirelessly. Simply place your devices on the charging mat for effortless charging.', 78, 'Apple', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20Airpower%20Wireless%20Charger/1.png'),
(103, 'Apple HomePod Mini Cosmic Grey', 99.99, 'The Apple HomePod Mini in Cosmic Grey is a compact smart speaker that delivers impressive audio and integrates seamlessly with the Apple ecosystem for a smart home experience.', 65, 'Apple', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20HomePod%20Mini%20Cosmic%20Grey/1.png'),
(104, 'Apple iPhone Charger', 19.99, 'The Apple iPhone Charger is a high-quality charger designed for fast and efficient charging of your iPhone. Ensure your device stays powered up and ready to go.', 4, 'Apple', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20iPhone%20Charger/1.png'),
(105, 'Apple MagSafe Battery Pack', 99.99, 'The Apple MagSafe Battery Pack is a portable and convenient way to add extra battery life to your MagSafe-compatible iPhone. Attach it magnetically for a secure connection.', 80, 'Apple', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20MagSafe%20Battery%20Pack/1.png'),
(106, 'Apple Watch Series 4 Gold', 349.99, 'The Apple Watch Series 4 in Gold is a stylish and advanced smartwatch with features like heart rate monitoring, fitness tracking, and a beautiful Retina display.', 68, 'Apple', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Apple%20Watch%20Series%204%20Gold/1.png'),
(107, 'Beats Flex Wireless Earphones', 49.99, 'The Beats Flex Wireless Earphones offer a comfortable and versatile audio experience. With magnetic earbuds and up to 12 hours of battery life, they are ideal for everyday use.', 49, 'Beats', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Beats%20Flex%20Wireless%20Earphones/1.png'),
(108, 'iPhone 12 Silicone Case with MagSafe Plum', 29.99, 'The iPhone 12 Silicone Case with MagSafe in Plum is a stylish and protective case designed for the iPhone 12. It features MagSafe technology for easy attachment of accessories.', 51, 'Apple', 'https://cdn.dummyjson.com/products/images/mobile-accessories/iPhone%2012%20Silicone%20Case%20with%20MagSafe%20Plum/1.png'),
(109, 'Monopod', 19.99, 'The Monopod is a versatile camera accessory for stable and adjustable shooting. Perfect for capturing selfies, group photos, and videos with ease.', 96, 'TechGear', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Monopod/1.png'),
(110, 'Selfie Lamp with iPhone', 14.99, 'The Selfie Lamp with iPhone is a portable and adjustable LED light designed to enhance your selfies and video calls. Attach it to your iPhone for well-lit photos.', 89, 'GadgetMaster', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Selfie%20Lamp%20with%20iPhone/1.png'),
(111, 'Selfie Stick Monopod', 12.99, 'The Selfie Stick Monopod is a extendable and foldable device for capturing the perfect selfie or group photo. Compatible with smartphones and cameras.', 33, 'SnapTech', 'https://cdn.dummyjson.com/products/images/mobile-accessories/Selfie%20Stick%20Monopod/1.png'),
(112, 'TV Studio Camera Pedestal', 499.99, 'The TV Studio Camera Pedestal is a professional-grade camera support system for smooth and precise camera movements in a studio setting. Ideal for broadcast and production.', 45, 'ProVision', 'https://cdn.dummyjson.com/products/images/mobile-accessories/TV%20Studio%20Camera%20Pedestal/1.png'),
(121, 'iPhone 5s', 199.99, 'The iPhone 5s is a classic smartphone known for its compact design and advanced features during its release. While it\'s an older model, it still provides a reliable user experience.', 65, 'Apple', 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%205s/1.png'),
(122, 'iPhone 6', 299.99, 'The iPhone 6 is a stylish and capable smartphone with a larger display and improved performance. It introduced new features and design elements, making it a popular choice in its time.', 99, 'Apple', 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%206/1.png'),
(123, 'iPhone 13 Pro', 1099.99, 'The iPhone 13 Pro is a cutting-edge smartphone with a powerful camera system, high-performance chip, and stunning display. It offers advanced features for users who demand top-notch technology.', 26, 'Apple', 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%2013%20Pro/1.png'),
(124, 'iPhone X', 899.99, 'The iPhone X is a flagship smartphone featuring a bezel-less OLED display, facial recognition technology (Face ID), and impressive performance. It represents a milestone in iPhone design and innovation.', 99, 'Apple', 'https://cdn.dummyjson.com/products/images/smartphones/iPhone%20X/1.png'),
(125, 'Oppo A57', 249.99, 'The Oppo A57 is a mid-range smartphone known for its sleek design and capable features. It offers a balance of performance and affordability, making it a popular choice.', 76, 'Oppo', 'https://cdn.dummyjson.com/products/images/smartphones/Oppo%20A57/1.png'),
(126, 'Oppo F19 Pro Plus', 399.99, 'The Oppo F19 Pro Plus is a feature-rich smartphone with a focus on camera capabilities. It boasts advanced photography features and a powerful performance for a premium user experience.', 92, 'Oppo', 'https://cdn.dummyjson.com/products/images/smartphones/Oppo%20F19%20Pro%20Plus/1.png'),
(128, 'Realme C35', 149.99, 'The Realme C35 is a budget-friendly smartphone with a focus on providing essential features for everyday use. It offers a reliable performance and user-friendly experience.', 81, 'Realme', 'https://cdn.dummyjson.com/products/images/smartphones/Realme%20C35/1.png'),
(129, 'Realme X', 299.99, 'The Realme X is a mid-range smartphone known for its sleek design and impressive display. It offers a good balance of performance and camera capabilities for users seeking a quality device.', 87, 'Realme', 'https://cdn.dummyjson.com/products/images/smartphones/Realme%20X/1.png'),
(130, 'Realme XT', 349.99, 'The Realme XT is a feature-rich smartphone with a focus on camera technology. It comes equipped with advanced camera sensors, delivering high-quality photos and videos for photography enthusiasts.', 53, 'Realme', 'https://cdn.dummyjson.com/products/images/smartphones/Realme%20XT/1.png'),
(131, 'Samsung Galaxy S7', 299.99, 'The Samsung Galaxy S7 is a flagship smartphone known for its sleek design and advanced features. It features a high-resolution display, powerful camera, and robust performance.', 55, 'Samsung', 'https://cdn.dummyjson.com/products/images/smartphones/Samsung%20Galaxy%20S7/1.png'),
(132, 'Samsung Galaxy S8', 499.99, 'The Samsung Galaxy S8 is a premium smartphone with an Infinity Display, offering a stunning visual experience. It boasts advanced camera capabilities and cutting-edge technology.', 75, 'Samsung', 'https://cdn.dummyjson.com/products/images/smartphones/Samsung%20Galaxy%20S8/1.png'),
(133, 'Samsung Galaxy S10', 699.99, 'The Samsung Galaxy S10 is a flagship device featuring a dynamic AMOLED display, versatile camera system, and powerful performance. It represents innovation and excellence in smartphone technology.', 40, 'Samsung', 'https://cdn.dummyjson.com/products/images/smartphones/Samsung%20Galaxy%20S10/1.png'),
(159, 'iPad Mini 2021 Starlight', 499.99, 'The iPad Mini 2021 in Starlight is a compact and powerful tablet from Apple. Featuring a stunning Retina display, powerful A-series chip, and a sleek design, it offers a premium tablet experience.', 32, 'Apple', 'https://cdn.dummyjson.com/products/images/tablets/iPad%20Mini%202021%20Starlight/1.png'),
(160, 'Samsung Galaxy Tab S8 Plus Grey', 599.99, 'The Samsung Galaxy Tab S8 Plus in Grey is a high-performance Android tablet by Samsung. With a large AMOLED display, powerful processor, and S Pen support, it\'s ideal for productivity and entertainment.', 76, 'Samsung', 'https://cdn.dummyjson.com/products/images/tablets/Samsung%20Galaxy%20Tab%20S8%20Plus%20Grey/1.png'),
(161, 'Samsung Galaxy Tab White', 349.99, 'The Samsung Galaxy Tab in White is a sleek and versatile Android tablet. With a vibrant display, long-lasting battery, and a range of features, it offers a great user experience for various tasks.', 4, 'Samsung', 'https://cdn.dummyjson.com/products/images/tablets/Samsung%20Galaxy%20Tab%20White/1.png'),
(162, 'RTX 4090', 1100.00, '', 10, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_category`
--

CREATE TABLE `product_category` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_category`
--

INSERT INTO `product_category` (`product_id`, `category_id`) VALUES
(78, 2),
(79, 2),
(80, 2),
(81, 2),
(82, 2),
(99, 4),
(100, 4),
(101, 4),
(102, 4),
(103, 4),
(104, 4),
(105, 4),
(106, 4),
(107, 4),
(108, 4),
(109, 4),
(110, 4),
(111, 4),
(112, 4),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 1),
(159, 3),
(160, 3),
(161, 3);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `phone`, `created_at`) VALUES
(1, 'admin', 'admin@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Admin', 'User', 'admin', '555-000-0000', '2025-04-28 20:17:14'),
(2, 'user1', 'user1@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'John', 'Smith', 'customer', '555-010-1001', '2025-04-28 20:17:14'),
(3, 'user2', 'user2@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Sarah', 'Johnson', 'customer', '555-010-1002', '2025-04-28 20:17:14'),
(4, 'user3', 'user3@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Michael', 'Williams', 'customer', '555-010-1003', '2025-04-28 20:17:14'),
(5, 'user4', 'user4@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Emily', 'Brown', 'customer', '555-010-1004', '2025-04-28 20:17:14'),
(6, 'user5', 'user5@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'David', 'Jones', 'customer', '555-010-1005', '2025-04-28 20:17:14'),
(7, 'user6', 'user6@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Jessica', 'Garcia', 'customer', '555-010-1006', '2025-04-28 20:17:14'),
(8, 'user7', 'user7@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Robert', 'Miller', 'customer', '555-010-1007', '2025-04-28 20:17:14'),
(9, 'user8', 'user8@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Amanda', 'Davis', 'customer', '555-010-1008', '2025-04-28 20:17:14'),
(10, 'user9', 'user9@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Daniel', 'Rodriguez', 'customer', '555-010-1009', '2025-04-28 20:17:14'),
(11, 'user10', 'user10@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Olivia', 'Martinez', 'customer', '555-010-1010', '2025-04-28 20:17:14'),
(12, 'user11', 'user11@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'James', 'Hernandez', 'customer', '555-010-1011', '2025-04-28 20:17:14'),
(13, 'user12', 'user12@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Sophia', 'Lopez', 'customer', '555-010-1012', '2025-04-28 20:17:14'),
(14, 'user13', 'user13@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'William', 'Gonzalez', 'customer', '555-010-1013', '2025-04-28 20:17:14'),
(15, 'user14', 'user14@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Emma', 'Wilson', 'customer', '555-010-1014', '2025-04-28 20:17:14'),
(16, 'user15', 'user15@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Benjamin', 'Anderson', 'customer', '555-010-1015', '2025-04-28 20:17:14'),
(17, 'user16', 'user16@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Ava', 'Thomas', 'customer', '555-010-1016', '2025-04-28 20:17:14'),
(18, 'user17', 'user17@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Jacob', 'Taylor', 'customer', '555-010-1017', '2025-04-28 20:17:14'),
(19, 'user18', 'user18@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Mia', 'Moore', 'customer', '555-010-1018', '2025-04-28 20:17:14'),
(20, 'user19', 'user19@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Ethan', 'Jackson', 'customer', '555-010-1019', '2025-04-28 20:17:14'),
(21, 'user20', 'user20@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Charlotte', 'Martin', 'customer', '555-010-1020', '2025-04-28 20:17:14'),
(22, 'user21', 'user21@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Alexander', 'Lee', 'customer', '555-010-1021', '2025-04-28 20:17:14'),
(23, 'user22', 'user22@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Abigail', 'Perez', 'customer', '555-010-1022', '2025-04-28 20:17:14'),
(24, 'user23', 'user23@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Daniel', 'Thompson', 'customer', '555-010-1023', '2025-04-28 20:17:14'),
(25, 'user24', 'user24@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Elizabeth', 'White', 'customer', '555-010-1024', '2025-04-28 20:17:14'),
(26, 'user25', 'user25@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Matthew', 'Harris', 'customer', '555-010-1025', '2025-04-28 20:17:14'),
(27, 'user26', 'user26@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Sofia', 'Sanchez', 'customer', '555-010-1026', '2025-04-28 20:17:14'),
(28, 'user27', 'user27@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Andrew', 'Clark', 'customer', '555-010-1027', '2025-04-28 20:17:14'),
(29, 'user28', 'user28@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Victoria', 'Ramirez', 'customer', '555-010-1028', '2025-04-28 20:17:14'),
(30, 'user29', 'user29@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Joshua', 'Lewis', 'customer', '555-010-1029', '2025-04-28 20:17:14'),
(31, 'user30', 'user30@example.com', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 'Grace', 'Robinson', 'customer', '555-010-1030', '2025-04-28 20:17:14'),
(32, 'عبد الله سعد', 'abdulrahman@example.com', 'e210c1f4a9e0901ced057eddbaf99a5a294e1be3', NULL, NULL, 'customer', NULL, '2025-04-29 16:21:27'),
(34, 'asd', 'sad@example.com', '$2y$10$bZeNn3PlKqp9fPyrPGa.2OiFOIY7R827cmL2ESOztyz0/ojyteqby', NULL, NULL, 'customer', '1011585670', '2025-04-29 17:54:52'),
(35, 'hamada', 'hamada@example.com', '$2y$10$zdDVYcc8I9UL8yq3Zv3fNe3w2.FA.fGCWNnaToeM9lYW3rmihYIqW', NULL, NULL, 'customer', '1545454544', '2025-04-29 17:58:26');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(5, 2, 102, '2025-04-29 21:19:31'),
(6, 2, 132, '2025-04-29 21:19:31'),
(7, 2, 108, '2025-04-29 21:18:57'),
(8, 3, 125, '2025-04-28 20:41:26'),
(9, 3, 128, '2025-04-28 20:41:26'),
(11, 4, 122, '2025-04-28 20:41:26'),
(12, 4, 126, '2025-04-28 20:41:26'),
(13, 4, 130, '2025-04-28 20:41:26'),
(14, 4, 131, '2025-04-28 20:41:26'),
(16, 5, 125, '2025-04-28 20:41:26'),
(17, 5, 128, '2025-04-28 20:41:26'),
(19, 31, 121, '2025-04-28 20:41:26'),
(20, 31, 133, '2025-04-28 20:41:26'),
(21, 26, 123, '2025-04-28 20:41:26'),
(22, 11, 123, '2025-04-28 20:41:26'),
(23, 10, 123, '2025-04-28 20:41:26'),
(24, 24, 123, '2025-04-28 20:41:26'),
(25, 29, 123, '2025-04-28 20:41:26'),
(26, 30, 123, '2025-04-28 20:41:26'),
(27, 18, 123, '2025-04-28 20:41:26'),
(28, 17, 123, '2025-04-28 20:41:26'),
(29, 7, 123, '2025-04-28 20:41:26'),
(30, 28, 123, '2025-04-28 20:41:26'),
(31, 12, 123, '2025-04-28 20:41:26'),
(32, 8, 123, '2025-04-28 20:41:26'),
(33, 20, 123, '2025-04-28 20:41:26'),
(34, 23, 123, '2025-04-28 20:41:26'),
(35, 22, 123, '2025-04-28 20:41:26'),
(36, 14, 123, '2025-04-28 20:41:26'),
(37, 27, 123, '2025-04-28 20:41:26'),
(38, 19, 123, '2025-04-28 20:41:26'),
(39, 9, 123, '2025-04-28 20:41:26'),
(40, 15, 123, '2025-04-28 20:41:26'),
(46, 2, 101, '2025-04-30 21:55:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_category`
--
ALTER TABLE `product_category`
  ADD PRIMARY KEY (`product_id`,`category_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_category`
--
ALTER TABLE `product_category`
  ADD CONSTRAINT `product_category_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_category_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
