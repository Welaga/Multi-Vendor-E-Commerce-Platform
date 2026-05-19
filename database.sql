-- ============================================================
-- Multi-Vendor E-Commerce Platform - Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS multivendor_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE multivendor_db;

-- -----------------------------------------------
-- Roles Table
-- -----------------------------------------------
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- Users Table
-- -----------------------------------------------
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    avatar VARCHAR(255) DEFAULT 'default.png',
    status ENUM('active','inactive','pending') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- -----------------------------------------------
-- Seller Profiles Table
-- -----------------------------------------------
CREATE TABLE seller_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    shop_name VARCHAR(150) NOT NULL,
    shop_description TEXT,
    shop_logo VARCHAR(255) DEFAULT 'default_shop.png',
    approval_status ENUM('pending','approved','rejected') DEFAULT 'pending',
    total_earnings DECIMAL(12,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- -----------------------------------------------
-- Categories Table
-- -----------------------------------------------
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(120) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT 'bi-tag',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- Products Table
-- -----------------------------------------------
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    discount_price DECIMAL(10,2) DEFAULT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default_product.png',
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- -----------------------------------------------
-- Product Images Table (multiple images)
-- -----------------------------------------------
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- -----------------------------------------------
-- Orders Table
-- -----------------------------------------------
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    discount_amount DECIMAL(10,2) DEFAULT 0.00,
    final_amount DECIMAL(12,2) NOT NULL,
    coupon_code VARCHAR(50) DEFAULT NULL,
    shipping_address TEXT NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'cash_on_delivery',
    payment_status ENUM('unpaid','paid') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id)
);

-- -----------------------------------------------
-- Order Items Table
-- -----------------------------------------------
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    seller_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (seller_id) REFERENCES users(id)
);

-- -----------------------------------------------
-- Reviews & Ratings Table
-- -----------------------------------------------
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    buyer_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (product_id, buyer_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id)
);

-- -----------------------------------------------
-- Coupons Table
-- -----------------------------------------------
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_type ENUM('percentage','fixed') DEFAULT 'percentage',
    discount_value DECIMAL(10,2) NOT NULL,
    minimum_order DECIMAL(10,2) DEFAULT 0.00,
    usage_limit INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    expiry_date DATE NOT NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------
-- Seller Earnings Table
-- -----------------------------------------------
CREATE TABLE seller_earnings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seller_id INT NOT NULL,
    order_id INT NOT NULL,
    order_item_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    commission DECIMAL(10,2) DEFAULT 0.00,
    net_earning DECIMAL(10,2) NOT NULL,
    payout_status ENUM('pending','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id),
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (order_item_id) REFERENCES order_items(id)
);

-- -----------------------------------------------
-- Cart Table
-- -----------------------------------------------
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_cart_item (buyer_id, product_id),
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ===============================
-- SAMPLE DATA
-- ===============================

-- Roles
INSERT INTO roles (name) VALUES ('admin'), ('seller'), ('buyer');

-- Admin User (password: admin123)
INSERT INTO users (role_id, name, email, password, status) VALUES
(1, 'Super Admin', 'admin@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active');

-- Sample Seller (password: seller123)
INSERT INTO users (role_id, name, email, password, phone, status) VALUES
(2, 'John Vendor', 'seller@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0241234567', 'active'),
(2, 'Mary Stores', 'mary@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0557654321', 'active');

-- Sample Buyer (password: buyer123)
INSERT INTO users (role_id, name, email, password, phone, status) VALUES
(3, 'Alice Buyer', 'buyer@shop.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0209876543', 'active');

-- Seller Profiles
INSERT INTO seller_profiles (user_id, shop_name, shop_description, approval_status) VALUES
(2, 'John Tech Hub', 'Your one-stop electronics store', 'approved'),
(3, 'Mary Fashion World', 'Trendy fashion for everyone', 'approved');

-- Categories
INSERT INTO categories (name, slug, icon) VALUES
('Electronics', 'electronics', 'bi-phone'),
('Fashion', 'fashion', 'bi-bag'),
('Home & Garden', 'home-garden', 'bi-house'),
('Sports', 'sports', 'bi-bicycle'),
('Books', 'books', 'bi-book'),
('Beauty', 'beauty', 'bi-stars');

-- Products (seller_id=2 is John Vendor)
INSERT INTO products (seller_id, category_id, name, slug, description, price, discount_price, stock, status) VALUES
(2, 1, 'Wireless Bluetooth Headphones', 'wireless-bluetooth-headphones', 'Premium sound quality with 30hr battery life', 299.99, 249.99, 50, 'active'),
(2, 1, 'Smartphone 128GB', 'smartphone-128gb', 'Latest Android smartphone with great camera', 1200.00, 999.00, 30, 'active'),
(2, 1, 'USB-C Laptop Charger', 'usb-c-laptop-charger', '65W fast charging compatible with most laptops', 89.99, NULL, 100, 'active'),
(3, 2, 'Men Casual T-Shirt', 'men-casual-t-shirt', '100% cotton comfortable daily wear', 49.99, 39.99, 200, 'active'),
(3, 2, 'Women Summer Dress', 'women-summer-dress', 'Light and breezy summer collection', 79.99, NULL, 150, 'active'),
(3, 2, 'Leather Wallet', 'leather-wallet', 'Genuine leather slim wallet', 69.99, 59.99, 80, 'active');

-- Coupons
INSERT INTO coupons (code, discount_type, discount_value, minimum_order, usage_limit, expiry_date, status) VALUES
('SAVE10', 'percentage', 10.00, 100.00, 100, DATE_ADD(CURDATE(), INTERVAL 30 DAY), 'active'),
('FLAT50', 'fixed', 50.00, 300.00, 50, DATE_ADD(CURDATE(), INTERVAL 60 DAY), 'active'),
('NEWUSER', 'percentage', 15.00, 0.00, 200, DATE_ADD(CURDATE(), INTERVAL 90 DAY), 'active');
