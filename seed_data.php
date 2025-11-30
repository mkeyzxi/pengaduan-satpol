<?php
// seed_data.php
require 'db.php';
$labels = [
    'Penertiban Pasar' => [
        "Pedagang berjualan di luar area lapak resmi",
        "PKL menutup akses jalan di sekitar pasar",
        "Pedagang liar memenuhi trotoar dekat pasar",
        "Aktivitas bongkar muat mengganggu kelancaran pasar",
        "Lapak liar dibangun di area parkir pasar",
        "Pedagang menempati badan jalan di depan pasar",
        "Pedagang menggelar dagangan di jalur evakuasi pasar",
        "Pedagang sayur liar mengganggu arus kendaraan di pasar",
        "Kerumunan tidak teratur di pintu masuk pasar",
        "Penimbunan barang dagangan yang menghambat jalur pejalan kaki",
        "Kios liar berdiri tanpa izin di dalam area pasar",
        "PKL menolak ditertibkan di area luar pagar pasar"
    ],

    'Orang Bolos' => [
        "Petugas tidak berada di pos jaga saat jam kerja",
        "ASN terlihat berada di warung saat jam dinas",
        "Pegawai sering meninggalkan kantor tanpa izin",
        "Petugas ronda tidak hadir saat jadwal piket",
        "Pegawai berkeliaran di luar kantor pada jam kerja",
        "Karyawan pemerintah mangkir dari tugas lapangan",
        "Staf tidak mengikuti apel pagi tanpa alasan jelas",
        "Petugas pelayanan publik tidak berada di loket",
        "Pegawai terlihat nongkrong di tempat umum saat jam kerja",
        "Petugas posko tidak standby sesuai jadwal piket",
        "Petugas terlihat tidur saat jam dinas",
        "Pegawai kedapatan tidak memakai atribut dinas dan tidak berada di kantor"
    ],

    'Sapi Liar' => [
        "Sapi berkeliaran di jalan raya mengganggu lalu lintas",
        "Hewan ternak dilepas bebas di area pemukiman",
        "Sapi masuk ke halaman rumah warga",
        "Sapi liar merusak tanaman dan kebun warga",
        "Kawanan sapi melintas di jalan tanpa pengawasan pemilik",
        "Sapi menghalangi akses jalan desa",
        "Sapi berkeliaran di area sekolah",
        "Sapi berada di area pasar mengganggu pengunjung",
        "Sapi tidur di bahu jalan pada malam hari",
        "Hewan ternak dilepas tanpa kandang di area terbuka",
        "Sapi berkumpul di dekat jalan utama dan membahayakan pengendara",
        "Sapi liar menimbulkan bau dan kotoran di area publik"
    ]
];


$pdo->beginTransaction();
$insert = $pdo->prepare("INSERT INTO training_data (text,label) VALUES (?, ?)");

// generate variasi otomatis — kita buat 25 contoh per kelas
foreach ($labels as $label => $templates) {
    for ($i = 0; $i < 25; $i++) {
        $tpl = $templates[array_rand($templates)];
        // tambahkan variasi kata, nomor rumah, lokasi dll.
        $var = $tpl;
        $addendum = [
            " di depan blok " . rand(1,20),
            " dekat sekolah " . ["SMA","SD","TK"][rand(0,2)],
            " setiap pagi",
            " sejak 2 minggu lalu",
            " sudah lama tidak diangkut",
            " menimbulkan bau tidak sedap"
        ];
        // pakai 0..2 addendum acak
        $k = rand(0,2);
        $suf = [];
        for ($j=0;$j<$k;$j++) $suf[] = $addendum[array_rand($addendum)];
        $var .= implode('', $suf);
        $insert->execute([$var, $label]);
    }
}

$pdo->commit();
echo "Seed selesai: ditambahkan ~" . (25 * count($labels)) . " baris data latih.\n";
