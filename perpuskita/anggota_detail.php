<?php
session_start();
include 'koneksi.php';

// Cek apakah sudah login
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "ID tidak ditemukan.";
  exit();
}

$id = $_GET['id'];
$query = $conn->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$query->bind_param("s", $id);
$query->execute();
$result = $query->get_result();
$anggota = $result->fetch_assoc();

if (!$anggota) {
  echo "Data tidak ditemukan.";
  exit();
}

// Validasi ID anggota
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $_SESSION['pesan_error'] = "ID tidak valid.";
  header("Location: anggota_admin.php");
  exit;
}

$id = $_GET['id'];

// Ambil data anggota
$stmt = $conn->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$anggota = $result->fetch_assoc();

if (!$anggota) {
  $_SESSION['pesan_error'] = "Anggota tidak ditemukan.";
  header("Location: anggota_admin.php");
  exit;
}

// Fungsi generate password acak
function generatePassword($length = 8) {
  return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
}

// Jika admin menekan tombol reset password
if (isset($_POST['reset_password'])) {
  $password_baru = generatePassword();
  $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

  $update = $conn->prepare("UPDATE pengguna SET password = ? WHERE id_pengguna = ?");
  $update->bind_param("ss", $password_hash, $id);
  if ($update->execute()) {
    $_SESSION['pesan_sukses'] = "Password berhasil direset. Password baru: <strong>$password_baru</strong>";
    header("Location: anggota_detail.php?id=" . $id);
    exit;
  } else {
    $_SESSION['pesan_error'] = "Gagal mereset password.";
  }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Anggota</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 min-h-screen px-6 py-10">
  <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow">
    <h1 class="text-2xl font-bold text-blue-700 mb-6">Detail Pengguna</h1>

    <div class="space-y-4">
      <p><strong>ID Pengguna:</strong> <?= htmlspecialchars($anggota['id_pengguna']) ?></p>
      <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($anggota['nama_lengkap']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($anggota['email']) ?></p>
      <p><strong>Telepon:</strong> <?= htmlspecialchars($anggota['telepon']) ?></p>
      <p><strong>Alamat:</strong> <?= htmlspecialchars($anggota['alamat']) ?></p>
      <p><strong>Role:</strong> <?= htmlspecialchars($anggota['role']) ?></p>
      <p><strong>Tanggal Daftar:</strong> <?= htmlspecialchars(date('d M Y', strtotime($anggota['tanggal_daftar']))) ?></p>
      <p><strong>Tanggal Berakhir:</strong> <?= htmlspecialchars(date('d M Y', strtotime($anggota['tanggal_berakhir']))) ?></p>
    </div>

    <div class="mt-8">
        <?php if (isset($_SESSION['pesan_sukses'])): ?>
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            <?= $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
        </div>
        <?php elseif (isset($_SESSION['pesan_error'])): ?>
        <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
            <?= $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="mt-8 flex gap-4">
      <a href="anggota_edit.php?id=<?= $anggota['id_pengguna'] ?>" class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500">
        <i data-feather="edit" class="inline w-4 h-4 mr-1"></i>Edit
      </a>
      <a href="anggota_hapus.php?id=<?= $anggota['id_pengguna'] ?>" onclick="return confirm('Yakin ingin menghapus data ini?')" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
        <i data-feather="trash" class="inline w-4 h-4 mr-1"></i>Hapus
      </a>
      <a href="anggota_perpanjang.php?id=<?= $anggota['id_pengguna'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        <i data-feather="clock" class="inline w-4 h-4 mr-1"></i>Perpanjang Waktu
      </a>
      <form method="POST" onsubmit="return confirm('Reset password anggota ini? Password akan digenerate otomatis.')">
        <button type="submit" name="reset_password" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
            <i data-feather="key" class="inline w-4 h-4 mr-1"></i> Reset Password
        </button>
</form>


    </div>

    <div class="mt-6">
      <a href="anggota_admin.php" class="text-blue-600 hover:underline">&larr; Kembali ke Daftar Anggota</a>
    </div>
  </div>

  <script>
    feather.replace();
  </script>
</body>
</html>
