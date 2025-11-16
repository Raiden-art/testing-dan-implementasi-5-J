<?php
session_start();
include 'db_connection.php';

// Cek Hak Akses Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

$sql = "SELECT id, fullname, email, phone, role FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Kelola Data Karyawan</title>
</head>
<body id="manage-page">
    <div class="manage-users-container">
        <h2>Kelola Data Karyawan</h2>
        <?php
        // Menampilkan pesan umpan balik setelah delete
        if (isset($_GET['delete'])) {
            if ($_GET['delete'] == 'success') {
                echo "<p class='success-message'>Pengguna berhasil dihapus!</p>";
            } elseif ($_GET['delete'] == 'error') {
                echo "<p class='error-message'>Gagal menghapus pengguna.</p>";
            }
        }
        ?>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['phone']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td class="action-links">
                        <a href="update_profile.php?id=<?php echo $user['id']; ?>">Edit</a>
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>" 
                           onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <a href="dashboard.php" class="button-link primary" style="margin-top: 20px;">Kembali ke Dashboard</a>
    </div>
</body>
</html>