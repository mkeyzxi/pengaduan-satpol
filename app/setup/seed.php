<?php
require __DIR__ . "/../config/database.php";

$users = [
    ['Admin Satpol',  'admin@satpolpp.go.id', password_hash('123456', PASSWORD_DEFAULT), 'admin'],
    ['Petugas Lapangan',  'petugas@satpolpp.go.id', password_hash('123456', PASSWORD_DEFAULT), 'petugas'],
    ['Warga Contoh',  'user@test.com', password_hash('123456', PASSWORD_DEFAULT), 'masyarakat'],
];

foreach ($users as $u) {
    $pdo->prepare("INSERT IGNORE INTO users(nama,email,password,role) 
                   VALUES (?,?,?,?)")
        ->execute([$u[0], $u[1], password_hash($u[2], PASSWORD_BCRYPT), $u[3]]);
}

echo "✔ Default users inserted.\n";

// Dummy training dataset
$data = [
    // --- Klasifikasi: Penertiban Pasar ---
    ['Pedagang kaki lima (PKL) berjualan di bahu jalan raya', 'Penertiban Pasar'],
    ['Lapak dagangan didirikan di atas saluran air', 'Penertiban Pasar'],
    ['PKL menggelar tikar di depan pintu masuk ruko/toko', 'Penertiban Pasar'],
    ['Penjual buah menumpuk dagangan hingga menutupi trotoar', 'Penertiban Pasar'],
    ['Jual beli dilakukan di luar batas area pasar yang ditentukan', 'Penertiban Pasar'],
    ['Gerobak dagangan diparkir sembarangan hingga menimbulkan kemacetan', 'Penertiban Pasar'],
    ['Penjual sayur menaruh keranjang di tengah gang pasar', 'Penertiban Pasar'],
    ['Bangunan liar semi-permanen dijadikan tempat berdagang', 'Penertiban Pasar'],
    ['Papan promosi dipasang di tiang listrik area pasar', 'Penertiban Pasar'],
    ['Pedagang ayam potong membuang limbah di selokan pasar', 'Penertiban Pasar'],
    ['Kios permanen dibangun melebihi batas zonasi pasar', 'Penertiban Pasar'],
    ['Barang dagangan diletakkan di lorong utama pasar', 'Penertiban Pasar'],
    ['Penjual ikan menggunakan area parkir sebagai tempat mencuci', 'Penertiban Pasar'],
    ['PKL mendirikan tenda tanpa izin di fasilitas umum', 'Penertiban Pasar'],
    ['Penyewaan lapak ilegal oleh oknum tertentu', 'Penertiban Pasar'],
    ['Penempatan etalase permanen di area pejalan kaki', 'Penertiban Pasar'],

    // --- Klasifikasi: Sapi Liar ---
    ['Ada seekor sapi berkeliaran di area perumahan', 'Sapi Liar'],
    ['Sekelompok sapi terlihat memakan rumput di taman kota', 'Sapi Liar'],
    ['Sapi-sapi berkeliaran di jalanan desa dan mengganggu lalu lintas', 'Sapi Liar'],
    ['Sapi lepas dari kandang dan merusak tanaman warga', 'Sapi Liar'],
    ['Seekor anak sapi ditemukan tersesat di pemukiman', 'Sapi Liar'],
    ['Laporan warga tentang sapi liar yang buang kotoran di jalan umum', 'Sapi Liar'],
    ['Kawanan sapi melintas di jalan tol yang belum dibuka', 'Sapi Liar'],
    ['Pemilik sapi didenda karena membiarkan ternaknya berkeliaran bebas', 'Sapi Liar'],
    ['Sapi-sapi terlihat sedang minum air di kolam retensi', 'Sapi Liar'],
    ['Pengendara sepeda motor hampir menabrak sapi yang tiba-tiba menyeberang', 'Sapi Liar'],
    ['Sapi-sapi merobohkan pagar pembatas kebun warga', 'Sapi Liar'],
    ['Warga kesulitan melintas karena ada sapi yang tidur di tengah jalan', 'Sapi Liar'],
    ['Penemuan kandang sapi yang tidak terurus dan ditinggalkan', 'Sapi Liar'],
    ['Tim SAR mengevakuasi sapi yang terperosok ke selokan', 'Sapi Liar'],
    ['Sapi memakan sampah di tempat pembuangan sementara (TPS)', 'Sapi Liar'],
    ['Jejak kaki sapi terlihat di lapangan bola', 'Sapi Liar'],

    // --- Klasifikasi: Orang Bolos ---
    ['Seorang siswa SMP tidak masuk sekolah tanpa keterangan', 'Orang Bolos'],
    ['Tiga orang pegawai negeri sipil (PNS) tidak hadir apel pagi', 'Orang Bolos'],
    ['Mahasiswa Fakultas X tidak mengikuti kuliah wajib dan terlihat di mall', 'Orang Bolos'],
    ['Anak sekolah berseragam tertangkap sedang merokok di warung dekat sekolah', 'Orang Bolos'],
    ['Karyawan perusahaan mangkir dari pekerjaan selama tiga hari berturut-turut', 'Orang Bolos'],
    ['Siswa-siswi SMA bolos pelajaran sejarah dan pergi ke warnet', 'Orang Bolos'],
    ['Tidak ada keterangan resmi dari guru yang absen mengajar hari ini', 'Orang Bolos'],
    ['Sejumlah pekerja konstruksi tidak datang bekerja tanpa pemberitahuan', 'Orang Bolos'],
    ['Murid SD dijemput Satpol PP saat sedang bermain di sungai', 'Orang Bolos'],
    ['Pegawai administrasi terlambat lebih dari 3 jam dan dicatat sebagai bolos', 'Orang Bolos'],
    ['Pelajar yang seharusnya ujian malah nongkrong di kafe', 'Orang Bolos'],
    ['Petugas kebersihan tidak masuk kerja tanpa izin', 'Orang Bolos'],
    ['Pekerja lepas harian (buruh) tidak datang ke lokasi proyek', 'Orang Bolos'],
    ['Siswa SMK memalsukan surat sakit untuk menghindari praktik kerja', 'Orang Bolos'],
    ['Dua staf bank ditemukan tidak di tempat saat jam kerja', 'Orang Bolos'],
    ['Seorang perawat absen tanpa pemberitahuan di rumah sakit', 'Orang Bolos'],
];

foreach ($data as $row) {
    $pdo->prepare("INSERT INTO training_data(text_data, kategori_label) VALUES(?,?)")
        ->execute($row);
}

echo "✔ Training dataset inserted.\n";
