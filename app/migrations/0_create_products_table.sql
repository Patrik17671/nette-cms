CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  file VARCHAR(255),
  url VARCHAR(255),
  categories VARCHAR(255),
  price DECIMAL(10, 2),
  sizes VARCHAR(255),
  colors VARCHAR(255)
);