<?php
function uploadFile($file, $destDir) {
    if (!is_dir($destDir)) mkdir($destDir, 0755, true);
    if ($file['error'] !== UPLOAD_ERR_OK) return null;
    $allowed = ['image/jpeg','image/png','image/jpg'];
    if (!in_array($file['type'], $allowed)) return null;
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
    $dst = rtrim($destDir, '/') . '/' . $newName;
    if (move_uploaded_file($file['tmp_name'], $dst)) return $newName;
    return null;
}
