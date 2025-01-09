<?php
// Cek dan mulai session jika belum aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'koneksi.php'; // File koneksi ke database
include 'upload_foto.php'; // Fungsi upload foto

// Debugging sementara untuk username user
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'fikri'; // Set manual, ganti dengan username yang valid
}

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION['username']; // Username user yang sedang login
    $password = $_POST['password'] ?? ''; // Menggunakan name="password"
    $foto = $_FILES['foto'] ?? null;

    // Update password jika diisi
    if (!empty($password)) {
        $password = md5($password); // Enkripsi menggunakan MD5
        $query = "UPDATE user SET password = '$password' WHERE username = '$username'";
        mysqli_query($conn, $query); // Menggunakan $conn
    }

    // Upload foto jika ada
    if (!empty($foto['name'])) {
        $upload = upload_foto($foto);
        if ($upload['status']) {
            $nama_foto = $upload['message'];
            $query = "UPDATE user SET foto = '$nama_foto' WHERE username = '$username'";
            mysqli_query($conn, $query); // Menggunakan $conn
        } else {
            echo "Error: " . $upload['message'];
        }
    }

    echo "Profil berhasil diperbarui!";
}

// Ambil data user dari database
$username = $_SESSION['username'];
$query = "SELECT * FROM user WHERE username = '$username'";
$result = mysqli_query($conn, $query); // Menggunakan $conn
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil User</title>
</head>
<body>
    <form method="POST" action="" enctype="multipart/form-data">
    <div class="modal-body">
    <div class="mb-3">
        <label for="formGroupExampleInput" class="form-label">Ganti Password</label>
        <input type="text" name="password" class="form-control" placeholder="Tulis password baru jika ingin mengganti password">
    </div>
    <div class="mb-3">
        <label for="formGroupExampleInput" class="form-label">Ganti Foto Profile</label>
        <input type="file" class="form-control" name="foto">
    </div>
    <div class="mb-3">
        <label>Foto Profil Saat Ini:</label><br>
        <?php if (!empty($user['foto'])): ?>
            <img src="img/<?php echo $user['foto']; ?>" width="100"><br>
        <?php else: ?>
            <p>Belum ada foto</p>
        <?php endif; ?>
    </div>
    </div>
    <div class="footer">
        <input type="submit" value="simpan" name="simpan" class="btn btn-primary">
    </div>
</form>
</body>
</html>

