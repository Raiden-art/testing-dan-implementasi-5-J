<?php
// Cek apakah variabel lingkungan PHPUnit ada
if (getenv('karyawan_db')) {
    $servername = getenv('');
    $username = getenv('');
    $password = getenv('');
    $dbname = getenv('');
} else {
    // Konfigurasi DEFAULT / Produksi
    $servername = "localhost";
    $username = "root"; 
    $password = "";     
    $dbname = "karyawan_db"; 
}

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>