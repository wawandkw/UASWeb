<?php
session_start();
include 'koneksi.php';

// Cek apakah ada ID anggota
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $_SESSION['pesan_error'] = "ID anggota tidak valid.";
  header("Location: anggota_admin.php");
  exit;
}

$id = $_GET['id'];

// Ambil data anggota
$stmt = $conn->prepare("SELECT * FROM pengguna WHERE id_pengguna = ? AND role = 'anggota'");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$anggota = $result->fetch_assoc();

if (!$anggota) {
  $_SESSION['pesan_error'] = "Anggota tidak ditemukan.";
  header("Location: anggota_admin.php");
  exit;
}

// Jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $password_baru = $_POST['password'];
  $konfirmasi = $_POST['konfirmasi'];

  if (strlen($password_baru) < 6) {
    $_SESSION['pesan_error'] = "Password minimal 6 karakter.";
  } elseif ($password_baru !== $konfirmasi) {
    $_SESSION['pesan_error'] = "Konfirmasi password tidak cocok.";
  } else {
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
    $stmt_update = $conn->prepare("UPDATE pengguna SET password = ? WHERE id_pengguna = ?");
    $stmt_update->bind_param("ss", $password_hash, $id);
    if ($stmt_update->execute()) {
      $_SESSION['pesan_sukses'] = "Password berhasil direset.";
      header("Location: anggota_detail.php?id=$id");
      exit;
    } else {
      $_SESSION['pesan_error'] = "Gagal mereset password.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password Anggota</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white p-6 rounded-lg shadow max-w-md w-full">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Reset Password Anggota</h2>

    <p class="mb-4 text-gray-700">
      Nama: <strong><?= htmlspecialchars($anggota['nama_lengkap']) ?></strong><br>
      ID: <strong><?= htmlspecialchars($anggota['id_pengguna']) ?></strong>
    </p>

    <?php if (isset($_SESSION['pesan_error'])): ?>
      <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['pesan_error']) ?>
        <?php unset($_SESSION['pesan_error']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Password Baru</label>
        <input type="password" name="password" required minlength="6"
               class="mt-1 w-full border px-4 py-2 rounded focus:outline-none focus:ring focus:border-blue-500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
        <input type="password" name="konfirmasi" required
               class="mt-1 w-full border px-4 py-2 rounded focus:outline-none focus:ring focus:border-blue-500">
      </div>
      <div class="flex justify-end gap-3">
        <a href="anggota_detail.php?id=<?= $id ?>" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Batal</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Reset Password</button>
      </div>
    </form>
  </div>
</body>
</html>
