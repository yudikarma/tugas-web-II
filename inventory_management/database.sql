CREATE DATABASE IF NOT EXISTS inventory_management
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE inventory_management;

CREATE TABLE IF NOT EXISTS items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(120) NOT NULL,
    sku VARCHAR(40) NOT NULL UNIQUE,
    category VARCHAR(80) NOT NULL,
    stock_level INT UNSIGNED NOT NULL DEFAULT 0,
    unit VARCHAR(40) NOT NULL,
    reorder_level INT UNSIGNED NOT NULL DEFAULT 0,
    price DECIMAL(12,2) NOT NULL DEFAULT 0,
    supplier VARCHAR(120) NOT NULL,
    image_url VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO items (item_name, sku, category, stock_level, unit, reorder_level, price, supplier, image_url) VALUES
('Espresso Roast', 'ESP-001', 'Coffee Beans', 84, '5lb Bags', 20, 185000, 'PT Kopi Nusantara', 'https://images.unsplash.com/photo-1442512595331-e89e73853f31?auto=format&fit=crop&w=200&q=80'),
('Whole Milk', 'DRY-042', 'Dairy', 12, 'Gallons', 15, 32000, 'CV Segar Dairy', 'https://images.unsplash.com/photo-1563636619-e9143da7973b?auto=format&fit=crop&w=200&q=80'),
('Butter Croissant', 'PAS-112', 'Pastries', 4, 'Trays', 10, 95000, 'Bakery Central', 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?auto=format&fit=crop&w=200&q=80'),
('Vanilla Syrup', 'SYR-009', 'Syrups', 45, 'Bottles', 12, 78000, 'Sweet Supply Co', 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?auto=format&fit=crop&w=200&q=80')
ON DUPLICATE KEY UPDATE
    item_name = VALUES(item_name),
    category = VALUES(category),
    stock_level = VALUES(stock_level),
    unit = VALUES(unit),
    reorder_level = VALUES(reorder_level),
    price = VALUES(price),
    supplier = VALUES(supplier),
    image_url = VALUES(image_url);
