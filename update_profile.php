<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Ambil data user saat ini
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $new_file_name = $user['profile_picture'];

    // Proses upload foto profil
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file_name = $_FILES['profile_picture']['name'];
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_size = $_FILES['profile_picture']['size'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = $_FILES['profile_picture']['type'];

        if ($file_size > 2000000 || !in_array($file_type, $allowed_types)) {
            $error = "Gagal upload: Ukuran Max 2MB atau format tidak diizinkan.";
        } else {
            // Hapus file lama jika ada
            if (!empty($user['profile_picture']) && $user['profile_picture'] !== 'default_avatar.png') {
                $old_file_path = "uploads/" . $user['profile_picture'];
                if (file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
            }
            
            // Simpan file baru
            $new_file_name = uniqid() . '_' . basename($file_name);
            if (!is_dir('uploads')) { mkdir('uploads', 0777, true); }
            if (!move_uploaded_file($file_tmp, "uploads/" . $new_file_name)) {
                $error = "Gagal memindahkan file yang diunggah.";
            }
        }
    }

    // Update data ke database
    if (empty($error)) {
        $sql = "UPDATE users SET fullname = ?, email = ?, phone = ?, address = ?, profile_picture = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $fullname, $email, $phone, $address, $new_file_name, $user_id); 

        if ($stmt->execute()) {
            $success = "Profil berhasil diperbarui!";
            
            // Ambil ulang data user yang baru untuk tampilan
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user = $stmt->get_result()->fetch_assoc();

        } else {
            if ($conn->errno == 1062) {
                $error = "Email sudah terdaftar.";
            } else {
                $error = "Gagal memperbarui profil: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Update Profil Karyawan</title>
</head>
<body id="update-page">
    <div class="form-container">
        <h2>Update Profil Karyawan</h2>
        <?php if (isset($error) && !empty($error)) echo "<p class='error-message'>". htmlspecialchars($error) ."</p>"; ?>
        <?php if (isset($success) && !empty($success)) echo "<p class='success-message'>". htmlspecialchars($success) ."</p>"; ?>
        
        <p><strong>Foto Saat Ini:</strong></p>
        <?php
        $current_pic = !empty($user['profile_picture']) ? $user['profile_picture'] : 'default_avatar.png';
        $current_pic_path = 'uploads/' . $current_pic;
        ?>
        <img src="<?php echo htmlspecialchars($current_pic_path); ?>" alt="Foto Profil Saat Ini" class="profile-picture-small">
        <br>
        
        <form method="POST" enctype="multipart/form-data">
            <label for="fullname">Nama Lengkap:</label>
            <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Telepon:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

            <label for="address">Alamat:</label>
            <textarea name="address"><?php echo htmlspecialchars($user['address']); ?></textarea>
            
            <label for="profile_picture">Ganti Foto Profil (Max 2MB):</label>
            <input type="file" name="profile_picture" accept="image/jpeg,image/png,image/gif">
            
            <button type="submit">Simpan Perubahan</button>
            <a href="dashboard.php" class="button-link back-link">Kembali ke Dashboard</a>
        </form>
    </div>
</body>
</html>