<?php
// Cek apakah variabel lingkungan PHPUnit ada
if (getenv('DB_SERVERNAME')) {
    $servername = getenv('DB_SERVERNAME');
    $username = getenv('DB_USERNAME');
    $password = getenv('DB_PASSWORD');
    $dbname = getenv('DB_NAME');
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