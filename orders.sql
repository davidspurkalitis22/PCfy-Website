-- Create tables for orders system

-- Table for storing orders
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) NOT NULL,
    user_id INT DEFAULT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    county VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    country VARCHAR(2) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    order_date DATETIME NOT NULL,
    order_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    order_total DECIMAL(10, 2) NOT NULL,
    UNIQUE KEY (order_number)
);

-- Table for storing order items
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    order_number VARCHAR(20) NOT NULL,
    product_id VARCHAR(20) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Table for storing customer addresses (for future use)
CREATE TABLE IF NOT EXISTS customer_addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_type VARCHAR(20) NOT NULL DEFAULT 'shipping',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    county VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10) NOT NULL,
    country VARCHAR(2) NOT NULL,
    is_default BOOLEAN NOT NULL DEFAULT 0
);

-- Sample data for testing (optional)
INSERT INTO orders (order_number, first_name, last_name, email, phone, address, city, county, postal_code, country, payment_method, order_date, order_status, order_total)
VALUES ('ORD-12345678', 'John', 'Doe', 'john@example.com', '0851234567', '123 Main St', 'Galway', 'Galway', 'H91 FT6H', 'IE', 'credit_card', NOW(), 'completed', 239.97);

INSERT INTO order_items (order_id, order_number, product_id, product_name, quantity, price)
VALUES 
(1, 'ORD-12345678', '1', 'Intel DC S3710 800GB 2.5" SATA 6Gb/s 256MB Cache', 1, 130.00),
(1, 'ORD-12345678', '7', 'Razer DeathAdder Essential', 1, 40.00),
(1, 'ORD-12345678', '8', 'HyperX Cloud Stinger 1', 1, 70.00); 