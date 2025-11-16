<?php
session_start();
include 'db_connection.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// FIX: Tentukan path gambar profil.
$default_avatar = 'default_avatar.png';
$profile_picture_name = empty($user['profile_picture']) ? $default_avatar : $user['profile_picture'];
$final_image_src = 'uploads/' . $profile_picture_name; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Dashboard Karyawan</title>
</head>
<body id="dashboard-page">
    <div class="dashboard-container">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($final_image_src); ?>" alt="Foto Profil" class="profile-picture">
            <h2>Selamat Datang, <?php echo htmlspecialchars($user['fullname']); ?>!</h2>
        </div>
        
        <div class="user-info">
            <h3>Informasi Akun</h3>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Telepon:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
            <p><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($user['address'])); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>

        <div class="action-buttons">
            <a href="update_profile.php" class="button-link primary">Update Profil</a>
            <a href="logout.php" class="button-link secondary">Logout</a>
        </div>

        <?php if ($user['role'] === 'admin'): ?>
            <div class="admin-panel">
                <h3>Admin Panel</h3>
                <a href="manage_users.php" class="button-link admin-link">Kelola Pengguna</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>