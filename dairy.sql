--
-- Database: `yadav_dairy`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `image` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--
-- Dumping data for table `products`
--
INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`) VALUES
(1, 'Fresh Cow Milk', '100% pure, unadulterated cow milk. Packed with nutrients.', 70.00, 'https://thumbs.dreamstime.com/b/fresh-dairy-products-cow-background-milk-glass-butter-wooden-tab-ai-generated-fresh-dairy-products-cow-383777400.jpg?w=992'),
(2, 'Fresh Buffalo Milk', '100% pure, unadulterated Buffalo milk. Packed with nutrients.', 80.00, 'https://thumbs.dreamstime.com/b/pouring-milk-glass-background-nature-30352089.jpg?w=768'),
(3, 'Paneer', 'Fresh homemade paneer made from pure cow milk.', 300.00, 'https://thumbs.dreamstime.com/b/fresh-paneer-cheese-cubes-basil-leaves-transparent-background-close-up-isolated-ideal-food-cooking-projects-384442391.jpg?w=992'),
(4, 'Cow Ghee', '100% Pure and fresh desi Cow ghee made using traditional methods.', 1500.00, 'https://thumbs.dreamstime.com/b/ghee-jar-white-background-ai-generated-ghee-jar-white-background-342157013.jpg?w=992'),
(5, 'Buffalo Ghee', '100% Pure and fresh desi Buffalo ghee made using traditional methods.', 1800.00, 'https://thumbs.dreamstime.com/b/fresh-lemon-curd-glass-jars-covered-burlap-fresh-lemon-curd-glass-jars-covered-rustic-burlap-fabric-twine-set-350545769.jpg?w=992'),
(6, 'Curd', 'Fresh and creamy curd made from pure milk.', 100.00, 'https://thumbs.dreamstime.com/b/two-beautifully-crafted-bowls-filled-creamy-curd-garnished-spice-resting-rustic-wooden-surface-391625269.jpg?w=992'),
(7, 'Butter', 'Fresh homemade butter from cow milk.', 150.00, 'https://thumbs.dreamstime.com/b/block-fresh-butter-wooden-cutting-board-sliced-against-blue-background-119564035.jpg?w=768'),
(8, 'Lassi', 'Refreshing sweet lassi made from fresh curd.', 50.00, 'https://thumbs.dreamstime.com/b/lassi-garnished-pistachios-top-created-generative-ai-307270041.jpg?w=768'),
(9, 'Milk Sweets', 'Traditional milk sweets fresh milk, and colorful laddu sweets made with pure ingredients.', 300.00, 'https://thumbs.dreamstime.com/b/brass-plate-features-steamed-white-rice-fresh-milk-colorful-laddu-sweets-creating-vibrant-culinary-contrast-397329932.jpg?w=992');


-- --------------------------------------------------------

--
-- Table structure for table `cart`
--
CREATE TABLE `cart` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `order_uid` VARCHAR(50) NOT NULL UNIQUE,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `delivery_name` VARCHAR(255) NOT NULL,
  `delivery_phone` VARCHAR(20) NOT NULL,
  `delivery_address` TEXT NOT NULL,
  `delivery_instructions` TEXT,
  `payment_method` VARCHAR(50) NOT NULL,
  `status` VARCHAR(50) DEFAULT 'Pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--
CREATE TABLE `contacts` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20),
    `message` TEXT NOT NULL,
    `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);