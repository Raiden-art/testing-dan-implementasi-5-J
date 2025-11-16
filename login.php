<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data user dari database
    $sql = "SELECT id, email, password, role, failed_login_attempts, last_login_attempt FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Cek apakah akun sedang terkunci (lockout)
        if ($user['failed_login_attempts'] >= 3 && strtotime($user['last_login_attempt']) > strtotime('-5 minutes')) {
            $error = "Akun Anda terkunci. Silakan coba lagi setelah 5 menit.";
        } else {
            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Login berhasil
                $reset_attempts_sql = "UPDATE users SET failed_login_attempts = 0 WHERE id = ?";
                $reset_stmt = $conn->prepare($reset_attempts_sql);
                $reset_stmt->bind_param("i", $user['id']);
                $reset_stmt->execute();
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                header("Location: dashboard.php");
                exit();
            } else {
                // Password salah, tingkatkan penghitung percobaan gagal
                $update_attempts_sql = "UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_login_attempt = NOW() WHERE id = ?";
                $update_stmt = $conn->prepare($update_attempts_sql);
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();

                $error = "Email atau password salah. Percobaan gagal ke-" . ($user['failed_login_attempts'] + 1) . ".";
            }
        }
    } else {
        // Email tidak ditemukan
        $error = "Email atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Login Karyawan</title>
</head>
<body id="login-page">
    <div class="form-container">
        <h2>Silahkan Login Akun Anda</h2>
        <?php if (isset($error)) echo "<p class='error-message'>". htmlspecialchars($error) ."</p>"; ?>
        <form method="POST">
            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <a href="register.php">Belum punya akun? Register di sini</a>
    </div>
</body>
</html>