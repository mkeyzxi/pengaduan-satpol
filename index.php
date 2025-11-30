<?php
require __DIR__ . '/vendor/autoload.php';


?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pengaduan Satpol PP</title>
<style>
body{font-family:Arial; padding:16px; max-width:720px; margin:0 auto;}
.container{display:flex;flex-direction:column;gap:10px}
textarea{width:100%;min-height:120px;padding:8px;font-size:16px}
input,button{padding:10px;font-size:16px}
button{background:#2b6cb0;color:#fff;border:none;border-radius:6px}
.result{padding:12px;border-radius:6px;background:#f1f5f9}
.small{font-size:13px;color:#666}
.preview{max-width:100%;height:auto;border-radius:6px}
</style>
</head>
<body>
<h2>Form Pengaduan Satpol PP</h2>
<div class="container">
  <input id="name" placeholder="Nama (opsional)" />
  <input id="phone" placeholder="No. HP (opsional)" />
  <textarea id="text" placeholder="Jelaskan pengaduan Anda..."></textarea>
  <input id="photo" type="file" accept="image/*" />
  <img id="preview" class="preview" style="display:none" />
  <div>
    <button id="getloc">Ambil Lokasi Sekarang</button>
    <span id="locstatus" class="small"></span>
  </div>
  <button id="send">Kirim Pengaduan</button>
  <div id="status" class="small"></div>
  <div id="output" class="result" style="display:none"></div>
</div>

<script>
let currentLat = null, currentLon = null, currentLocName = null;
document.getElementById('getloc').addEventListener('click', function(){
  const s = document.getElementById('locstatus');
  if (!navigator.geolocation) { s.textContent = 'Geolocation tidak didukung'; return; }
  s.textContent = 'Mencari lokasi...';
  navigator.geolocation.getCurrentPosition(function(pos){
    currentLat = pos.coords.latitude;
    currentLon = pos.coords.longitude;
    s.textContent = 'Lat: ' + currentLat.toFixed(6) + ', Lon: ' + currentLon.toFixed(6);
    // optional: tampilkan string lokasi (reverse geocoding) — but offline here
  }, function(err){
    s.textContent = 'Gagal ambil lokasi: ' + err.message;
  }, { enableHighAccuracy: true, timeout: 10000 });
});

document.getElementById('photo').addEventListener('change', function(e){
  const f = e.target.files[0];
  if (!f) return;
  const url = URL.createObjectURL(f);
  const img = document.getElementById('preview');
  img.src = url;
  img.style.display = 'block';
});

document.getElementById('send').addEventListener('click', async function(){
  const name = document.getElementById('name').value;
  const phone = document.getElementById('phone').value;
  const text = document.getElementById('text').value.trim();
  const status = document.getElementById('status');
  const output = document.getElementById('output');

  if (!text) { status.textContent = 'Tulis pengaduan terlebih dahulu.'; return; }
  status.textContent = 'Mengirim...';
  output.style.display = 'none';

  const form = new FormData();
  form.append('user_name', name);
  form.append('user_phone', phone);
  form.append('text', text);
  if (currentLat !== null) {
    form.append('latitude', currentLat);
    form.append('longitude', currentLon);
    form.append('location', 'Lat:' + currentLat + ',Lon:' + currentLon);
  }
  const photo = document.getElementById('photo').files[0];
  if (photo) form.append('photo', photo);

  try {
    const res = await fetch('classify.php', {
      method:'POST',
      body: form
    });
    const j = await res.json();
    if (res.ok && j.status === 'ok') {
      status.textContent = 'Terkirim. Hasil klasifikasi:';
      output.style.display = 'block';
      output.innerHTML = '<strong>Label:</strong> ' + j.predicted_label + '<br><strong>Pesan:</strong> ' + j.text;
      document.getElementById('text').value = '';
      document.getElementById('photo').value = '';
      document.getElementById('preview').style.display = 'none';
    } else {
      status.textContent = 'Error: ' + (j.error || 'unknown');
    }
  } catch(e) {
    status.textContent = 'Gagal menghubungi server.';
  }
});
</script>
</body>
</html>
