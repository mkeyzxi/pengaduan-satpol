<?php
require __DIR__ . "/../config/database.php";

try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users(
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama VARCHAR(100),
            email VARCHAR(120) UNIQUE,
            password VARCHAR(255),
            role ENUM('admin','petugas','masyarakat'),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS pengaduan(
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            deskripsi TEXT,
            lokasi VARCHAR(255),
            foto VARCHAR(200),
            prediksi_label VARCHAR(100) DEFAULT 'unknown',
            status ENUM('diajukan','diproses','selesai','tidak sesuai','ditolak') DEFAULT 'diajukan',
            koreksi_petugas VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS training_data(
            id INT AUTO_INCREMENT PRIMARY KEY,
            text_data TEXT,
            label VARCHAR(100)
        );
    ");

    echo "✔ Database updated successfully.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
