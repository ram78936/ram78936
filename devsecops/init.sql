CREATE DATABASE IF NOT EXISTS foodgo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE foodgo;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS restaurants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    cuisine VARCHAR(100) NOT NULL,
    rating DECIMAL(2,1) DEFAULT 0.0,
    image VARCHAR(500)
);
CREATE TABLE IF NOT EXISTS menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    restaurant_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(500),
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status VARCHAR(50) DEFAULT 'Order Placed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
);

INSERT INTO restaurants (name, cuisine, rating, image)
SELECT 'Pizza Palace', 'Italian', 4.5, 'https://images.unsplash.com/photo-1513104890138-7c749659a591'
WHERE NOT EXISTS (SELECT 1 FROM restaurants WHERE name='Pizza Palace');
INSERT INTO restaurants (name, cuisine, rating, image)
SELECT 'Burger Hub', 'Fast Food', 4.3, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd'
WHERE NOT EXISTS (SELECT 1 FROM restaurants WHERE name='Burger Hub');
INSERT INTO restaurants (name, cuisine, rating, image)
SELECT 'Spice Garden', 'Indian', 4.7, 'https://images.unsplash.com/photo-1585937421612-70a008356fbe'
WHERE NOT EXISTS (SELECT 1 FROM restaurants WHERE name='Spice Garden');

INSERT INTO menu_items (restaurant_id, name, price, image)
SELECT r.id, x.name, x.price, x.image FROM restaurants r JOIN (
 SELECT 'Pizza Palace' restaurant, 'Margherita Pizza' name, 249.00 price, 'https://images.unsplash.com/photo-1574071318508-1cdbab80d002' image UNION ALL
 SELECT 'Pizza Palace', 'Farmhouse Pizza', 349.00, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38' UNION ALL
 SELECT 'Pizza Palace', 'Cheese Burst Pizza', 399.00, 'https://images.unsplash.com/photo-1594007654729-407eedc4be65' UNION ALL
 SELECT 'Burger Hub', 'Classic Burger', 149.00, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd' UNION ALL
 SELECT 'Burger Hub', 'Cheese Burger', 199.00, 'https://images.unsplash.com/photo-1550547660-d9450f859349' UNION ALL
 SELECT 'Burger Hub', 'Double Burger', 249.00, 'https://images.unsplash.com/photo-1586190848861-99aa4a171e90' UNION ALL
 SELECT 'Spice Garden', 'Paneer Butter Masala', 220.00, 'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398' UNION ALL
 SELECT 'Spice Garden', 'Biryani', 250.00, 'https://images.unsplash.com/photo-1563379091339-03246963d96c' UNION ALL
 SELECT 'Spice Garden', 'Butter Naan', 60.00, 'https://images.unsplash.com/photo-1601050690597-df0568f70950'
) x ON x.restaurant = r.name
WHERE NOT EXISTS (SELECT 1 FROM menu_items m WHERE m.restaurant_id=r.id AND m.name=x.name);
