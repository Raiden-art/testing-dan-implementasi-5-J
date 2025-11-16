<?php
session_start();
include 'db_connection.php';

// 1. Cek Hak Akses Admin
// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// 2. Ambil ID Pengguna yang Akan Dihapus
// Mengambil ID pengguna dari URL parameter (query string)
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$user_id_to_delete = $_GET['id'];

// 3. Ambil Nama File Foto Profil untuk Dihapus dari Server
// Perlu menghapus file fisik di folder 'uploads' sebelum menghapus data di DB
$get_pic_sql = "SELECT profile_picture FROM users WHERE id = ?";
$stmt_pic = $conn->prepare($get_pic_sql);
$stmt_pic->bind_param("i", $user_id_to_delete);
$stmt_pic->execute();
$result_pic = $stmt_pic->get_result();
$user_to_delete = $result_pic->fetch_assoc();

if ($user_to_delete && !empty($user_to_delete['profile_picture'])) {
    $file_path = "uploads/" . $user_to_delete['profile_picture'];
    // Hapus file fisik jika ada
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}
$stmt_pic->close();


// 4. Hapus Data dari Database
$delete_sql = "DELETE FROM users WHERE id = ?";
$stmt_delete = $conn->prepare($delete_sql);
$stmt_delete->bind_param("i", $user_id_to_delete);

if ($stmt_delete->execute()) {
    // Redirect kembali ke halaman manajemen dengan pesan sukses
    // Anda bisa menggunakan SESSION untuk pesan, tapi di sini kita redirect sederhana
    header("Location: manage_users.php?delete=success");
    exit();
} else {
    // Redirect kembali ke halaman manajemen dengan pesan error
    header("Location: manage_users.php?delete=error");
    exit();
}

$stmt_delete->close();
$conn->close();
?>