<?php
session_start();
include 'koneksi.php';

// Hanya izinkan admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

// Ambil data buku dari database
$query = $conn->query("SELECT * FROM buku ORDER BY judul ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PerpusKita - Buku (Admin)</title>
  <link rel="icon" href="logo.png" type="image/png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">
  <!-- Navbar -->
  <header class="bg-blue-100 shadow">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center gap-2">
        <img src="logo.png" alt="Logo" class="w-8 h-8">
        <span class="text-xl font-semibold text-blue-700">PerpusKita</span>
      </div>
      <nav class="flex gap-6 items-center">
        <a href="dashboard_admin.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
          <i data-feather="grid"></i> Dashboard
        </a>
        <a href="transaksi_admin.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
          <i data-feather="file-text"></i> Transaksi
        </a>
        <a href="anggota_admin.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
          <i data-feather="users"></i> Anggota
        </a>
        <a href="buku_admin.php" class="flex items-center gap-1 text-blue-700 font-semibold">
          <i data-feather="book"></i> Buku
        </a>
        <a href="logout.php" class="flex items-center gap-1 text-red-600 hover:underline">
          <i data-feather="log-out"></i> Logout
        </a>
      </nav>
    </div>
  </header>

  <!-- Konten -->
  <main class="max-w-6xl mx-auto mt-10 px-4">
    <div class="flex items-center justify-between mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Daftar Buku</h2>
      <a href="buku_tambah.php" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
        <i data-feather="plus" class="inline-block w-4 h-4 mr-1"></i> Tambah Buku
      </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
      <div class="bg-green-100 text-green-800 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_GET['success']) ?>
      </div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_GET['error']) ?>
      </div>
    <?php endif; ?>

    <div class="overflow-x-auto bg-white rounded-xl shadow">
      <table class="min-w-full text-sm">
        <thead class="bg-blue-100 text-left">
          <tr>
            <th class="px-4 py-3 font-semibold">ID Buku</th>
            <th class="px-4 py-3 font-semibold">Judul</th>
            <th class="px-4 py-3 font-semibold">Penulis</th>
            <th class="px-4 py-3 font-semibold">Kategori</th>
            <th class="px-4 py-3 font-semibold">Stok</th>
            <th class="px-4 py-3 font-semibold">Gambar</th>
            <th class="px-4 py-3 font-semibold text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($buku = $query->fetch_assoc()): ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-3"><?= htmlspecialchars($buku['id_buku']) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($buku['judul']) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($buku['penulis']) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($buku['kategori']) ?></td>
            <td class="px-4 py-3"><?= htmlspecialchars($buku['stok']) ?></td>
            <td class="px-4 py-3">
              <?php if ($buku['gambar']): ?>
                <img src="uploads/<?= htmlspecialchars($buku['gambar']) ?>" alt="cover" class="w-12 h-16 object-cover rounded shadow" />
              <?php else: ?>
                <span class="text-gray-400 italic">Tidak ada</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-center flex justify-center gap-2">
              <a href="buku_edit.php?id=<?= $buku['id_buku'] ?>" class="text-blue-600 hover:text-blue-800" title="Edit">
                <i data-feather="edit" class="w-4 h-4"></i>
              </a>
              <a href="buku_hapus.php?id=<?= $buku['id_buku'] ?>" class="text-red-600 hover:text-red-800" title="Hapus"
                onclick="return confirm('Yakin ingin menghapus buku ini?')">
                <i data-feather="trash" class="w-4 h-4"></i>
              </a>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

  <script>
    feather.replace();
  </script>
</body>
</html>
