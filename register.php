<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    // Selalu hash password sebelum disimpan
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); 
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    // Menggunakan user sebagai default role
    $role = $_POST['role'] === 'admin' ? 'admin' : 'user'; 

    $error = '';
    $profile_picture = '';
    
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
            // Buat nama file unik dan folder 'uploads' jika belum ada
            $profile_picture = uniqid() . '_' . basename($file_name);
            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }
            if (!move_uploaded_file($file_tmp, "uploads/" . $profile_picture)) {
                 $error = "Gagal memindahkan file yang diunggah.";
            }
        }
    }

    // Menyimpan data ke database hanya jika tidak ada error upload
    if (empty($error)) {
        $sql = "INSERT INTO users (fullname, email, password, phone, address, profile_picture, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bind_param("sssssss", $fullname, $email, $password, $phone, $address, $profile_picture, $role); 
        
        if ($stmt->execute()) {
            // Redirect setelah registrasi berhasil
            header("Location: login.php?register=success");
            exit();
        } else {
            if ($conn->errno == 1062) {
                $error = "Email sudah terdaftar. Silakan gunakan email lain.";
            } else {
                $error = "Pendaftaran gagal: " . $conn->error;
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
    <title>Daftar Karyawan Baru</title>
</head>
<body id="register-page">
    <div class="form-container">
        <h2>Daftar Akun Karyawan</h2>
        <?php if (isset($error)) echo "<p class='error-message'>". htmlspecialchars($error) ."</p>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="fullname">Nama Lengkap:</label>
            <input type="text" name="fullname" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <label for="phone">Telepon:</label>
            <input type="text" name="phone">

            <label for="address">Alamat:</label>
            <textarea name="address"></textarea>

            <label for="role">Role:</label>
            <select name="role">
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>

            <label for="profile_picture">Upload Foto Profil:</label>
            <input type="file" name="profile_picture" id="profile_picture" accept="image/jpeg,image/png,image/gif">
            <small class="hint">Max 2MB (JPG, PNG, GIF)</small>

            <button type="submit">Daftar</button>
        </form>
        <a href="login.php">Sudah punya akun? Login di sini</a>
    </div>
</body>
</html>