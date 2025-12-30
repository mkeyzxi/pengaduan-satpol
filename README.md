# 📋 Sistem Pengaduan Satpol PP

Sistem Pengaduan Masyarakat berbasis web untuk Satuan Polisi Pamong Praja (Satpol PP). Aplikasi ini memungkinkan masyarakat untuk mengajukan pengaduan terkait pelanggaran ketertiban umum, yang kemudian diklasifikasikan secara otomatis menggunakan **Machine Learning**.

---

## 🎯 Fitur Utama

- **Pengaduan Online** - Masyarakat dapat mengajukan pengaduan dengan deskripsi, lokasi, dan foto
- **Klasifikasi Otomatis** - Pengaduan diklasifikasikan menggunakan PHP-ML (kategori: Penertiban Pasar, Sapi Liar, Orang Bolos)
- **Multi-Role System** - Tiga jenis pengguna: Admin, Petugas, dan Masyarakat
- **Tindak Lanjut** - Petugas dapat memproses dan memberikan update pada pengaduan
- **PWA Support** - Dapat diinstal sebagai aplikasi di perangkat mobile
- **Machine Learning Training** - Admin dapat melatih model dengan data baru

---

## 🛠️ Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP >= 7.4 |
| Database | MySQL / MariaDB |
| Frontend | HTML, JavaScript, Tailwind CSS 4.x |
| ML Library | [PHP-ML](https://php-ml.readthedocs.io/) |
| NLP | [Sastrawi](https://github.com/sastrawi/sastrawi) (Stemming Bahasa Indonesia) |
| Export | [PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet) |

---

## 📦 Prasyarat (Requirements)

Pastikan sistem Anda sudah terinstall:

- **XAMPP** v7.4+ atau **WAMP** / **MAMP** (PHP >= 7.4 + MySQL/MariaDB)
- **Composer** - [Download & Install](https://getcomposer.org/download/)
- **Node.js** >= 16 & **npm** - [Download & Install](https://nodejs.org/)
- **Git** - [Download & Install](https://git-scm.com/)

---

## 🚀 Langkah-Langkah Instalasi

### 1. Clone Repository

```bash
# Masuk ke folder htdocs (XAMPP) atau www (WAMP)
cd C:/xampp/htdocs

# Clone repository dari GitHub
git clone https://github.com/mkeyzxi/pengaduan-satpol.git pengaduan

# Masuk ke folder project
cd pengaduan
```

### 2. Install Dependencies PHP (Composer)

```bash
# Install semua package PHP yang dibutuhkan
composer install
```

Ini akan menginstall:
- `php-ai/php-ml` - Machine Learning library
- `sastrawi/sastrawi` - Stemming Bahasa Indonesia
- `phpoffice/phpspreadsheet` - Export data ke Excel

### 3. Install Dependencies JavaScript (NPM)

```bash
# Install semua package Node.js
npm install
```

Ini akan menginstall:
- `tailwindcss` - CSS Framework
- `@tailwindcss/cli` - CLI untuk compile Tailwind

### 4. Buat Database MySQL

#### Opsi A: Melalui phpMyAdmin

1. Buka browser dan akses: `http://localhost/phpmyadmin`
2. Klik tab **"Databases"** / **"Basis Data"**
3. Masukkan nama database: `satpolpp_complaint`
4. Pilih collation: `utf8mb4_unicode_ci`
5. Klik **"Create"** / **"Buat"**

#### Opsi B: Melalui Terminal/Command Line

```bash
# Login ke MySQL
mysql -u root -p

# Buat database baru
CREATE DATABASE satpolpp_complaint CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Keluar dari MySQL
EXIT;
```

### 5. Konfigurasi Database

Edit file konfigurasi database sesuai dengan setup lokal Anda:

📁 **File:** `app/config/database.php`

```php
<?php
$DB_HOST = '127.0.0.1';     // Host database
$DB_NAME = 'satpolpp_complaint';  // Nama database
$DB_USER = 'root';          // Username MySQL (default XAMPP: root)
$DB_PASS = '';              // Password MySQL (default XAMPP: kosong)
```

> ⚠️ **Catatan:** Sesuaikan `$DB_USER` dan `$DB_PASS` jika menggunakan kredensial berbeda.

### 6. Jalankan Migrasi Database

Migrasi akan membuat semua tabel yang dibutuhkan:

```bash
# Jalankan migrasi untuk membuat tabel
php app/setup/migrate.php
```

**Tabel yang akan dibuat:**
| Tabel | Deskripsi |
|-------|-----------|
| `users` | Data pengguna (admin, petugas, masyarakat) |
| `pengaduan` | Data pengaduan masyarakat |
| `training_data` | Data training untuk Machine Learning |
| `tindak_lanjut_pengaduan` | Log tindak lanjut oleh petugas |

### 7. Seed Data Awal

#### 7.1 Seed Users (Pengguna Default)

```bash
php app/setup/seed_users.php
```

**Default Users yang dibuat:**

| Nama | Email | Password | Role |
|------|-------|----------|------|
| Admin Satpol | `admin@satpolpp.go.id` | `123456` | Admin |
| Petugas Lapangan | `petugas@satpolpp.go.id` | `123456` | Petugas |
| Warga Contoh | `user@test.com` | `123456` | Masyarakat |

#### 7.2 Seed Training Data (Data Latihan ML)

```bash
php app/setup/seed.php
```

Data training ini berisi contoh pengaduan dengan label klasifikasi untuk melatih model Machine Learning.

### 8. Compile Tailwind CSS

#### Development Mode (dengan auto-reload)

```bash
npx @tailwindcss/cli -i ./public/style/input.css -o ./public/style/style.css --watch
```

> 💡 **Tips:** Biarkan terminal ini tetap berjalan selama development. Setiap perubahan pada file CSS/HTML akan otomatis di-compile.

#### Production Mode (build sekali)

```bash
npx @tailwindcss/cli -i ./public/style/input.css -o ./public/style/style.css --minify
```

### 9. Jalankan Aplikasi

1. **Pastikan XAMPP Apache & MySQL sudah running**
   - Buka XAMPP Control Panel
   - Start **Apache**
   - Start **MySQL**

2. **Akses aplikasi melalui browser:**

   ```
   http://localhost/pengaduan/
   ```

   atau

   ```
   http://localhost/pengaduan/public/
   ```

---

## 📁 Struktur Folder

```
pengaduan/
├── app/
│   ├── auth/           # Authentication helpers
│   ├── config/         # Konfigurasi (database.php)
│   ├── controllers/    # Controller logic
│   ├── core/           # Core functions
│   ├── helpers/        # Helper functions
│   ├── ml/             # Machine Learning (train, predict)
│   ├── models/         # Database models
│   ├── roles/          # Role-based access control
│   └── setup/          # Migration & Seeding
│       ├── migrate.php
│       ├── seed.php
│       └── seed_users.php
├── model/              # Trained ML model files
├── public/
│   ├── admin/          # Halaman admin
│   ├── icons/          # PWA icons
│   ├── js/             # JavaScript files
│   ├── layouts/        # Shared layouts (navbar, footer)
│   ├── masyarakat/     # Halaman masyarakat
│   ├── petugas/        # Halaman petugas
│   ├── style/          # CSS files
│   │   ├── input.css   # Tailwind source
│   │   └── style.css   # Compiled CSS
│   ├── uploads/        # File uploads (foto pengaduan)
│   ├── index.php       # Entry point
│   ├── login.php       # Halaman login
│   ├── register.php    # Halaman registrasi
│   └── manifest.json   # PWA manifest
├── vendor/             # Composer dependencies
├── node_modules/       # NPM dependencies
├── composer.json       # PHP dependencies
├── package.json        # Node.js dependencies
└── README.md           # Dokumentasi ini
```

---

## 👥 Akun Default untuk Testing

Setelah menjalankan `seed_users.php`, gunakan akun berikut untuk login:

### 🔴 Admin
- **Email:** `admin@satpolpp.go.id`
- **Password:** `123456`
- **Akses:** Kelola users, training data, lihat semua pengaduan

### 🟡 Petugas
- **Email:** `petugas@satpolpp.go.id`
- **Password:** `123456`
- **Akses:** Proses pengaduan, beri tindak lanjut

### 🟢 Masyarakat
- **Email:** `user@test.com`
- **Password:** `123456`
- **Akses:** Buat pengaduan, lihat status pengaduan sendiri

---

## 🤖 Machine Learning

### Melatih Model

1. Login sebagai **Admin**
2. Masuk ke menu **Training Data**
3. Tambah data training baru atau upload dari Excel
4. Klik tombol **"Train Model"**

Model akan disimpan di:
- `classifier_model.json` - Model klasifikasi
- `vectorizer.json` - TF-IDF Vectorizer

### Kategori Klasifikasi

| Label | Deskripsi |
|-------|-----------|
| Penertiban Pasar | PKL, lapak ilegal, pedagang di area terlarang |
| Sapi Liar | Hewan ternak berkeliaran di jalan/pemukiman |
| Orang Bolos | Pelajar/pegawai membolos dari sekolah/kerja |

---

## 🔧 Troubleshooting

### Error: "DB connection failed"
- Pastikan MySQL sudah running di XAMPP
- Cek kredensial di `app/config/database.php`
- Pastikan database `satpolpp_complaint` sudah dibuat

### Error: "Class not found" (PHP-ML)
```bash
# Jalankan ulang composer install
composer install
```

### Tailwind CSS tidak ter-compile
```bash
# Pastikan npm dependencies terinstall
npm install

# Jalankan compile ulang
npx @tailwindcss/cli -i ./public/style/input.css -o ./public/style/style.css
```

### Foto tidak bisa diupload
- Pastikan folder `public/uploads/` memiliki permission write
- Di Windows: Klik kanan folder > Properties > Security > Edit > Allow Full Control
- Di Linux: `chmod 777 public/uploads/`

---

## 📝 Perintah Lengkap (Quick Reference)

```bash
# Clone & masuk folder
git clone https://github.com/mkeyzxi/pengaduan-satpol.git pengaduan
cd pengaduan

# Install dependencies
composer install
npm install

# Setup database
php app/setup/migrate.php
php app/setup/seed_users.php
php app/setup/seed.php

# Development (Tailwind watch mode)
npx @tailwindcss/cli -i ./public/style/input.css -o ./public/style/style.css --watch

# Production build
npx @tailwindcss/cli -i ./public/style/input.css -o ./public/style/style.css --minify
```

---

## 📄 Lisensi

ISC License - Lihat file [LICENSE](LICENSE) untuk detail.

---

## 🤝 Kontribusi

Kontribusi selalu diterima! Silakan buat **Pull Request** atau buka **Issue** untuk diskusi.

---

<p align="center">
  Made with ❤️ by <a href="https://github.com/mkeyzxi">mkeyzxi</a>
</p>
