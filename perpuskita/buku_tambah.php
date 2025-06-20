<?php
session_start();
include 'koneksi.php';

// Hanya izinkan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

// Fungsi buat ID otomatis
function generateIdBuku($conn) {
  $result = $conn->query("SELECT id_buku FROM buku ORDER BY id_buku DESC LIMIT 1");
  if ($result && $row = $result->fetch_assoc()) {
    $lastId = $row['id_buku']; // Misalnya: BK005
    $number = intval(substr($lastId, 2)) + 1;
    return 'BK' . str_pad($number, 3, '0', STR_PAD_LEFT); // BK006
  } else {
    return 'BK001';
  }
}

// Proses submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id_buku     = generateIdBuku($conn);
  $judul       = trim($_POST['judul']);
  $penulis     = trim($_POST['penulis']);
  $penerbit    = trim($_POST['penerbit']);
  $kategori    = trim($_POST['kategori']);
  $stok        = (int) $_POST['stok'];
  $deskripsi   = trim($_POST['deskripsi']);
  $gambar      = null;

  // Proses upload gambar jika ada
  if (!empty($_FILES['gambar']['name'])) {
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION));

    if (in_array($ext, $allowed_ext)) {
      $namaFile = uniqid('', true) . '.' . $ext;
      $target = 'uploads/' . $namaFile;

      if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target)) {
        $gambar = $namaFile;
      } else {
        header("Location: buku_admin.php?error=Gagal mengunggah gambar");
        exit();
      }
    } else {
      header("Location: buku_admin.php?error=Format gambar tidak valid");
      exit();
    }
  }

  // Simpan ke database
  $stmt = $conn->prepare("INSERT INTO buku (id_buku, judul, penulis, penerbit, kategori, stok, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssssiss", $id_buku, $judul, $penulis, $penerbit, $kategori, $stok, $deskripsi, $gambar);


  if ($stmt->execute()) {
    header("Location: buku_admin.php?success=Buku berhasil ditambahkan");
    exit();
  } else {
    header("Location: buku_admin.php?error=Gagal menyimpan data");
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Buku</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
  <div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4 text-blue-700">Tambah Buku Baru</h2>
    <form method="POST" enctype="multipart/form-data" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Judul</label>
        <input type="text" name="judul" required class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Penulis</label>
        <input type="text" name="penulis" required class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Penerbit</label>
        <input type="text" name="penerbit" required class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Kategori</label>
        <input type="text" name="kategori" required class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Stok</label>
        <input type="number" name="stok" required min="0" class="w-full border rounded px-3 py-2">
      </div>
      <div>
        <label class="block mb-1 font-medium">Deskripsi</label>
        <textarea name="deskripsi" required class="w-full border rounded px-3 py-2"></textarea>
      </div>
      <div>
        <label class="block mb-1 font-medium">Gambar (opsional)</label>
        <input type="file" name="gambar" accept="image/*" class="w-full">
      </div>
      <div class="flex justify-between items-center">
        <a href="buku_admin.php" class="text-gray-600 hover:underline">← Kembali</a>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
      </div>
    </form>
  </div>
</body>
</html>
