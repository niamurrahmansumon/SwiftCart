-- Create database
CREATE DATABASE IF NOT EXISTS swiftcart;
USE swiftcart;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Products table
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    category_id INT,
    image VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Cart table
CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Orders table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) DEFAULT 'pending',
    shipping_address TEXT,
    payment_method VARCHAR(50),
    tracking_number VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Order items table
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample categories
INSERT INTO categories (name, slug, image) VALUES
('Mobile', 'mobile', 'images/categories/mobile.jpg'),
('Laptop', 'laptop', 'images/categories/laptop.jpg'),
('Smartwatch', 'smartwatch', 'images/categories/smartwatch.jpg'),
('Gadgets', 'gadgets', 'images/categories/gadgets.jpg');

-- Insert sample products
INSERT INTO products (name, description, price, original_price, category_id, image, stock_quantity, featured) VALUES
('iPhone 16 Pro', 'Latest iPhone with A18 Pro chip', 127500.00, 175000.00, 1, 'images/products/iphone15.jpg', 50, TRUE),
('Samsung Galaxy S25', 'Flagship Android smartphone', 899.99, 999.99, 1, 'images/products/galaxy-s24.jpg', 30, TRUE),
('MacBook Pro M3', 'Professional laptop with M3 chip', 1999.99, 2199.99, 2, 'images/products/macbook-pro.jpg', 20, TRUE),
('Dell XPS 13', 'Ultra-portable Windows laptop', 1299.99, 1399.99, 2, 'images/products/dell-xps13.jpg', 25, FALSE),
('Apple Watch Series 9', 'Advanced health and fitness tracking', 399.99, 449.99, 3, 'images/products/apple-watch.jpg', 40, TRUE),
('Samsung Galaxy Watch 6', 'Smart fitness companion', 329.99, 379.99, 3, 'images/products/galaxy-watch.jpg', 35, FALSE),
('AirPods Pro 2', 'Premium wireless earbuds', 249.99, 279.99, 4, 'images/products/airpods-pro.jpg', 60, TRUE),
('iPad Air', 'Versatile tablet for work and play', 599.99, 649.99, 4, 'images/products/ipad-air.jpg', 45, FALSE);
