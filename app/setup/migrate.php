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
            prediksi_label VARCHAR(100) DEFAULT 'tidak diketahui',
            status ENUM('diajukan','diproses','selesai','tidak sesuai','ditolak') DEFAULT 'diajukan',
            koreksi_petugas VARCHAR(100) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS training_data(
            id INT AUTO_INCREMENT PRIMARY KEY,
            text_data TEXT,
            label VARCHAR(100)
        );

        
        CREATE TABLE IF NOT EXISTS tindak_lanjut_pengaduan(
            id INT AUTO_INCREMENT PRIMARY KEY,
            pengaduan_id INT NOT NULL,
            petugas_id INT NOT NULL,
            catatan TEXT NOT NULL,
            status_akhir ENUM('diproses','selesai','tidak sesuai','ditolak') DEFAULT 'diproses',
            foto_bukti VARCHAR(255) NULL,
            tanggal_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (pengaduan_id) REFERENCES pengaduan(id) ON DELETE CASCADE,
            FOREIGN KEY (petugas_id) REFERENCES users(id)
        );
    ");

    echo "✔ Database updated successfully.\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
