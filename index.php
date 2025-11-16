<?php
session_start();

// Jika user sudah memiliki session aktif (sudah login), 
// arahkan langsung ke dashboard untuk menghindari login ganda.
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Sistem Karyawan</title>
    <link rel="stylesheet" href="css/style.css"> 
</head>
<body id="index-page">
    <div class="welcome-container">
        <h1>Sistem Informasi Karyawan</h1>
        <p>Akses data Anda atau daftarkan akun baru.</p>
        
        <div class="button-group">
            <a href="login.php" class="button-link primary">Login Karyawan</a>
            <a href="register.php" class="button-link secondary">Daftar Baru</a>
        </div>
        
        <p class="admin-note">
            Jika Anda Admin, silakan Login dan gunakan tautan "Kelola Pengguna" di Dashboard.
        </p>
    </div>
</body>
</html>