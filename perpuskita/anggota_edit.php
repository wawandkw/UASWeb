<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

if (!isset($_GET['id'])) {
  echo "ID tidak ditemukan.";
  exit();
}

$id = $_GET['id'];

// Ambil data pengguna
$query = $conn->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$query->bind_param("s", $id);
$query->execute();
$result = $query->get_result();
$anggota = $result->fetch_assoc();

if (!$anggota) {
  echo "Data tidak ditemukan.";
  exit();
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama'];
  $email = $_POST['email'];
  $telepon = $_POST['telepon'];
  $alamat = $_POST['alamat'];

  $update = $conn->prepare("UPDATE pengguna SET nama_lengkap=?, email=?, telepon=?, alamat=? WHERE id_pengguna=?");
  $update->bind_param("sssss", $nama, $email, $telepon, $alamat, $id);
  if ($update->execute()) {
    header("Location: anggota_detail.php?id=" . $id . "&update=success");
    exit();
  } else {
    $error = "Gagal memperbarui data.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Anggota</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-xl">
    <h2 class="text-2xl font-bold text-blue-700 mb-6 text-center">Edit Data Anggota</h2>

    <?php if (isset($error)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($anggota['nama_lengkap']) ?>" required class="mt-1 block w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($anggota['email']) ?>" required class="mt-1 block w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Telepon</label>
        <input type="text" name="telepon" value="<?= htmlspecialchars($anggota['telepon']) ?>" class="mt-1 block w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500" />
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Alamat</label>
        <textarea name="alamat" rows="3" class="mt-1 block w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500"><?= htmlspecialchars($anggota['alamat']) ?></textarea>
      </div>
      <div class="flex justify-end gap-3">
        <a href="anggota_detail.php?id=<?= $id ?>" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800">Batal</a>
        <button type="submit" class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white font-semibold">Simpan</button>
      </div>
    </form>
  </div>
</body>
</html>
