<?php
session_start();
include 'koneksi.php';

// Cek jika bukan admin
if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// Ambil data transaksi dengan nama pengguna dan judul buku
$query = $conn->query("SELECT 
    t.id_transaksi, t.tanggal_booking, t.batas_waktu, t.status,
    p.nama_lengkap, b.judul 
  FROM transaksi t
  JOIN pengguna p ON t.id_pengguna = p.id_pengguna
  JOIN buku b ON t.id_buku = b.id_buku
  ORDER BY t.tanggal_booking DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PerpusKita - Transaksi Admin</title>
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
      <a href="transaksi_admin.php" class="flex items-center gap-1 text-blue-700 font-semibold">
        <i data-feather="file-text"></i> Transaksi
      </a>
      <a href="anggota_admin.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
        <i data-feather="users"></i> Anggota
      </a>
      <a href="buku_admin.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
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
  <h2 class="text-2xl font-bold text-gray-800 mb-6">Manajemen Transaksi</h2>

  <!-- Tabel Transaksi -->
  <div class="overflow-x-auto bg-white shadow rounded-xl">
    <table class="min-w-full text-sm text-left">
      <thead class="bg-blue-100 text-blue-700 uppercase text-xs font-semibold">
        <tr>
          <th class="px-6 py-4">#</th>
          <th class="px-6 py-4">Nama Anggota</th>
          <th class="px-6 py-4">Judul Buku</th>
          <th class="px-6 py-4">Tanggal Booking</th>
          <th class="px-6 py-4">Batas Waktu</th>
          <th class="px-6 py-4">Status</th>
          <th class="px-6 py-4">Aksi</th>
        </tr>
      </thead>
      <tbody class="text-gray-700">
        <?php $no = 1; while ($row = $query->fetch_assoc()): ?>
        <tr class="border-b">
          <td class="px-6 py-4"><?= $no++ ?></td>
          <td class="px-6 py-4"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
          <td class="px-6 py-4"><?= htmlspecialchars($row['judul']) ?></td>
          <td class="px-6 py-4"><?= $row['tanggal_booking'] ?></td>
          <td class="px-6 py-4"><?= $row['batas_waktu'] ?></td>
          <td class="px-6 py-4">
            <form action="ubah_status.php" method="post" class="flex items-center gap-2">
              <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
              <select name="status" class="border text-sm rounded px-2 py-1 bg-gray-50">
                <option value="menunggu" <?= $row['status'] === 'menunggu' ? 'selected' : '' ?>>Menunggu 🟡</option>
                <option value="dipinjam" <?= $row['status'] === 'dipinjam' ? 'selected' : '' ?>>Dipinjam 🟢</option>
                <option value="dikembalikan" <?= $row['status'] === 'dikembalikan' ? 'selected' : '' ?>>Dikembalikan 🔵</option>
                <option value="dibatalkan" <?= $row['status'] === 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan 🔴</option>
              </select>
              <button type="submit" class="bg-blue-600 text-white px-3 py-1 text-xs rounded hover:bg-blue-700">Simpan</button>
            </form>
          </td>
          <td class="px-6 py-4 text-sm text-red-600">
            <form method="post" action="hapus_transaksi.php" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?')">
              <input type="hidden" name="id_transaksi" value="<?= $row['id_transaksi'] ?>">
              <button type="submit" class="hover:underline">Hapus</button>
            </form>
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
