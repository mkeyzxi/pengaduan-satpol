CREATE DATABASE satpolpp_complaint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE satpolpp_complaint;

-- tabel data latih (label + text)
CREATE TABLE training_data (
  id INT AUTO_INCREMENT PRIMARY KEY,
  text TEXT NOT NULL,
  label VARCHAR(100) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- tabel pengaduan pengguna
CREATE TABLE complaints (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_name VARCHAR(100),
  user_phone VARCHAR(30),
  text TEXT NOT NULL,
  predicted_label VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
