
<?php
include('includes/koneksi.php');
$sql = "SELECT profile_pic FROM users WHERE id = 6";  // Sesuaikan ID pengguna
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
$profile_pic = $row['profile_pic'];
?>
<img src="uploads/avatar.jpg .png" alt="Foto Profil" class="profile-picture">
