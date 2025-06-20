<?php
session_start();
include 'koneksi.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

$query = "SELECT *, 
  (SELECT COUNT(*) FROM transaksi WHERE transaksi.id_pengguna = pengguna.id_pengguna) AS total_pinjam 
  FROM pengguna ORDER BY tanggal_daftar DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>PerpusKita - Data Anggota</title>
  <link rel="icon" href="logo.png" type="image/png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
  <!-- Navbar -->
  <header class="bg-blue-100 shadow">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center gap-2">
        <img src="logo.png" alt="Logo" class="w-8 h-8">
        <span class="text-xl font-semibold text-blue-700">PerpusKita</span>
      </div>
      <nav class="flex gap-6 items-center">
        <a href="dashboard_admin.php" class="text-gray-700 hover:text-blue-700 flex items-center gap-1">
          <i data-feather="grid"></i> Dashboard
        </a>
        <a href="transaksi_admin.php" class="text-gray-700 hover:text-blue-700 flex items-center gap-1">
          <i data-feather="file-text"></i> Transaksi
        </a>
        <a href="anggota_admin.php" class="text-blue-700 font-semibold flex items-center gap-1">
          <i data-feather="users"></i> Anggota
        </a>
        <a href="buku_admin.php" class="text-gray-700 hover:text-blue-700 flex items-center gap-1">
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

    <?php if (isset($_SESSION['pesan_error'])): ?>
      <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded">
        <?= htmlspecialchars($_SESSION['pesan_error']) ?>
      </div>
      <?php unset($_SESSION['pesan_error']); ?>

    <?php elseif (isset($_SESSION['pesan_sukses'])): ?>
      <div class="mb-4 bg-green-100 text-green-800 px-4 py-3 rounded">
        <?= htmlspecialchars($_SESSION['pesan_sukses']) ?>
      </div>
      <?php unset($_SESSION['pesan_sukses']); ?>
    <?php endif; ?>

    <div class="flex justify-between items-center mb-6">
      <h2 class="text-2xl font-bold text-gray-800">Data Pengguna</h2>
      <a href="anggota_tambah.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        <i data-feather="user-plus" class="inline mr-2"></i>Tambah Pengguna
      </a>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded-xl">
      <table class="min-w-full text-sm text-left">
        <thead class="bg-blue-50 text-blue-700 uppercase text-xs font-bold">
          <tr>
            <th class="px-6 py-4">ID</th>
            <th class="px-6 py-4">Nama</th>
            <th class="px-6 py-4">Email</th>
            <th class="px-6 py-4">Telepon</th>
            <th class="px-6 py-4">Role</th>
            <th class="px-6 py-4">Daftar</th>
            <th class="px-6 py-4">Berakhir</th>
            <th class="px-6 py-4 text-center">Total Pinjam</th>
            <th class="px-6 py-4">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 font-mono"><?= $row['id_pengguna'] ?></td>
              <td class="px-6 py-4"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
              <td class="px-6 py-4"><?= $row['email'] ?></td>
              <td class="px-6 py-4"><?= $row['telepon'] ?></td>
              <td class="px-6 py-4"><?= ucfirst($row['role']) ?></td>
              <td class="px-6 py-4"><?= date('d M Y', strtotime($row['tanggal_daftar'])) ?></td>
              <td class="px-6 py-4"><?= date('d M Y', strtotime($row['tanggal_berakhir'])) ?></td>
              <td class="px-6 py-4 text-center"><?= $row['total_pinjam'] ?></td>
              <td class="px-6 py-4 flex gap-2 text-xs">
                <a href="anggota_detail.php?id=<?= $row['id_pengguna'] ?>" class="text-blue-600 hover:underline"><i data-feather="eye" class="inline w-4 h-4"></i> Lihat</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

  <script>feather.replace();</script>
</body>
</html>
