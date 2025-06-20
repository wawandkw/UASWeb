<?php
session_start();

if (!isset($_SESSION['id_pengguna'])) {
  header("Location: login.php");
  exit();
}

if ($_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

include 'koneksi.php';

// Hitung jumlah anggota (role = 'anggota')
$anggota = $conn->query("SELECT COUNT(*) AS total FROM pengguna WHERE role = 'anggota'");
$jumlah_anggota = $anggota->fetch_assoc()['total'];

// Hitung jumlah buku
$buku = $conn->query("SELECT COUNT(*) AS total FROM buku");
$jumlah_buku = $buku->fetch_assoc()['total'];

// Hitung transaksi aktif (status = 'dipinjam')
$aktif = $conn->query("SELECT COUNT(*) AS total FROM transaksi WHERE status = 'dipinjam'");
$jumlah_aktif = $aktif->fetch_assoc()['total'];

// Hitung transaksi terlambat (status = 'dipinjam' dan batas waktu sudah lewat)
$hari_ini = date('Y-m-d');
$terlambat = $conn->prepare("SELECT COUNT(*) AS total FROM transaksi WHERE status = 'dipinjam' AND batas_waktu < ?");
$terlambat->bind_param("s", $hari_ini);
$terlambat->execute();
$hasil = $terlambat->get_result();
$jumlah_terlambat = $hasil->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PerpusKita - Beranda</title>
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
        <a href="dashboard_admin.php" class="flex items-center gap-1 text-blue-700 font-semibold">
          <i data-feather="grid"></i> Dashboard
        </a>
        <a href="transaksi_admin.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
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
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Selamat Datang di Dashboard Admin</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="text-sm text-gray-500">Jumlah Anggota</h3>
        <p class="text-2xl font-bold text-primary"><?= $jumlah_anggota ?></p>
      </div>
      <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="text-sm text-gray-500">Jumlah Buku</h3>
        <p class="text-2xl font-bold text-primary"><?= $jumlah_buku ?></p>
      </div>
      <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="text-sm text-gray-500">Transaksi Aktif</h3>
        <p class="text-2xl font-bold text-primary"><?= $jumlah_aktif ?></p>
      </div>
      <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="text-sm text-gray-500">Buku Terlambat</h3>
        <p class="text-2xl font-bold text-primary"><?= $jumlah_terlambat ?></p>
      </div>
    </div>
  </main>

  <script>
    feather.replace(); // Aktifkan ikon Heroicons (Feather)
  </script>
</body>
</html>
