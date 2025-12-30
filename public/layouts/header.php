<?php
// Dynamic path calculation for nested directories
$basePath = '';
$currentDir = dirname($_SERVER['SCRIPT_NAME']);
$pathParts = explode('/', trim($currentDir, '/'));
// Find position of 'public' in path and calculate depth from there
$publicIndex = array_search('public', $pathParts);
if ($publicIndex !== false) {
    $depth = count($pathParts) - $publicIndex - 1;
    for ($i = 0; $i < $depth; $i++) {
        $basePath .= '../';
    }
}
if (empty($basePath)) $basePath = './';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Pengaduan Masyarakat Satpol PP">

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="<?= $basePath ?>manifest.json">
    <meta name="theme-color" content="#4a5d23">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Pengaduan Satpol PP">
    <link rel="apple-touch-icon" href="<?= $basePath ?>icons/icon-192x192.png">

    <title><?= $title ?? 'Pengaduan Masyarakat - Satpol PP' ?></title>
    <link rel="stylesheet" href="<?= $basePath ?>style/style.css">

    <!-- PWA Service Worker Registration -->
    <script src="<?= $basePath ?>js/pwa-register.js" defer></script>
</head>

<body class="min-h-screen flex flex-col bg-primary-50 text-gray-800">