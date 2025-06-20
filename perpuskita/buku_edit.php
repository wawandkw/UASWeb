<?php
session_start();
include 'koneksi.php';

// Cek apakah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

// Cek apakah ada ID buku
if (!isset($_GET['id'])) {
  header("Location: buku_admin.php?error=ID buku tidak ditemukan");
  exit();
}

$id_buku = $_GET['id'];

// Ambil data buku
$stmt = $conn->prepare("SELECT * FROM buku WHERE id_buku = ?");
$stmt->bind_param("s", $id_buku);
$stmt->execute();
$result = $stmt->get_result();
$buku = $result->fetch_assoc();

if (!$buku) {
  header("Location: buku_admin.php?error=Buku tidak ditemukan");
  exit();
}

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $judul       = trim($_POST['judul']);
  $penulis     = trim($_POST['penulis']);
  $kategori    = trim($_POST['kategori']);
  $stok        = (int) $_POST['stok'];
  $gambar      = $buku['gambar'];
  $penerbit    = trim($_POST['penerbit']);
  $deskripsi   = trim($_POST['deskripsi']);

  // Cek jika upload gambar baru
  if (!empty($_FILES['gambar']['name'])) {
    $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
    $namaBaru = uniqid() . '.' . $ext;
    $target = 'uploads/' . $namaBaru;

    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
      // Hapus gambar lama jika ada
      if (!empty($gambar) && file_exists('uploads/' . $gambar)) {
        unlink('uploads/' . $gambar);
      }
      $gambar = $namaBaru;
    } else {
      header("Location: buku_admin.php?error=Gagal upload gambar");
      exit();
    }
  }

  // Simpan update
  $update = $conn->prepare("UPDATE buku SET judul = ?, penulis = ?, penerbit = ?, kategori = ?, stok = ?, deskripsi = ?, gambar = ? WHERE id_buku = ?");
  $update->bind_param("ssssisss", $judul, $penulis, $penerbit, $kategori, $stok, $deskripsi, $gambar, $id_buku);

  if ($update->execute()) {
    header("Location: buku_admin.php?success=Buku berhasil diperbarui");
    exit();
  } else {
    header("Location: buku_admin.php?error=Gagal memperbarui data");
    exit();
  }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Buku</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
  <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4 text-blue-700">Edit Buku</h2>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Judul</label>
        <input type="text" name="judul" value="<?= htmlspecialchars($buku['judul']) ?>" required class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Penulis</label>
        <input type="text" name="penulis" value="<?= htmlspecialchars($buku['penulis']) ?>" required class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Penerbit</label>
        <input type="text" name="penerbit" value="<?= htmlspecialchars($buku['penerbit']) ?>" required class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Kategori</label>
        <input type="text" name="kategori" value="<?= htmlspecialchars($buku['kategori']) ?>" required class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Stok</label>
        <input type="number" name="stok" value="<?= htmlspecialchars($buku['stok']) ?>" required min="0" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Deskripsi</label>
        <textarea name="deskripsi" required class="w-full border rounded px-3 py-2"><?= htmlspecialchars($buku['deskripsi']) ?></textarea>
      </div>
      <div>
        <label class="block mb-1 font-medium">Gambar (biarkan kosong jika tidak ingin mengubah)</label>
        <input type="file" name="gambar" accept="image/*" class="w-full">
        <?php if ($buku['gambar']): ?>
          <img src="uploads/<?= htmlspecialchars($buku['gambar']) ?>" alt="cover" class="w-16 h-20 mt-2 object-cover rounded shadow">
        <?php endif; ?>
      </div>
      <div class="flex justify-between items-center">
        <a href="buku_admin.php" class="text-gray-600 hover:underline">← Kembali</a>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Perbarui</button>
      </div>
    </form>
  </div>
</body>
</html>
