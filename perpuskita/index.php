<?php
session_start();
include 'koneksi.php';

// Ambil data buku populer (misalnya berdasarkan stok tertinggi)
$populer = $conn->query("SELECT * FROM buku ORDER BY stok DESC LIMIT 3");

// Ambil koleksi terbaru (misalnya berdasarkan ID terbaru)
$terbaru = $conn->query("SELECT * FROM buku ORDER BY id_buku DESC LIMIT 5");
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
<header class="bg-white shadow">
  <div class="container mx-auto px-4 py-4 flex justify-between items-center">
    <div class="flex items-center gap-2">
      <img src="logo.png" alt="Logo" class="w-8 h-8">
      <span class="text-xl font-semibold text-blue-700">PerpusKita</span>
    </div>
    <nav class="flex gap-6 items-center">
      <a href="dashboard.php" class="flex items-center gap-1 text-blue-700 font-semibold">
        <i data-feather="home"></i> Home
      </a>
      <a href="buku.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
        <i data-feather="book-open"></i> Buku
      </a>
      <a href="login.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
        <i data-feather="user"></i> Login
      </a>
    </nav>
  </div>
</header>

<!-- Hero -->
<section class="bg-blue-100 py-10 text-center">
  <h1 class="text-3xl font-bold text-blue-800 mb-2">Selamat Datang di PerpusKita</h1>
  <p class="text-gray-700">Temukan dan pinjam buku favoritmu dengan mudah.</p>
</section>

<!-- Buku Populer -->
<section class="py-10 px-4 max-w-6xl mx-auto">
  <h2 class="text-2xl font-semibold mb-6 text-blue-800">📚 Buku Populer</h2>
  <div class="grid md:grid-cols-3 gap-6">
    <?php while ($row = $populer->fetch_assoc()): ?>
      <div class="bg-white rounded shadow p-4 cursor-pointer" onclick="bukaModal('<?= $row['id_buku'] ?>')">
        <img src="uploads/<?= htmlspecialchars($row['gambar'] ?? 'buku.png') ?>" alt="Buku" class="w-full h-40 object-cover mb-2 rounded">
        <h3 class="text-lg font-bold"><?= htmlspecialchars($row['judul']) ?></h3>
        <p class="text-sm text-gray-600">Penulis: <?= htmlspecialchars($row['penulis']) ?></p>
        <p class="mt-2 text-gray-700 text-sm"><?= htmlspecialchars(substr($row['deskripsi'], 0, 60)) ?>...</p>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- Koleksi Terbaru -->
<section class="py-10 px-4 bg-white max-w-6xl mx-auto">
  <h2 class="text-2xl font-semibold mb-6 text-blue-800">🆕 Koleksi Terbaru</h2>
  <div class="grid md:grid-cols-2 gap-4">
    <?php while ($row = $terbaru->fetch_assoc()): ?>
      <div class="flex items-center gap-4 border rounded p-4 bg-gray-50 cursor-pointer" onclick="bukaModal('<?= $row['id_buku'] ?>')">
        <img src="uploads/<?= htmlspecialchars($row['gambar'] ?? 'buku.png') ?>" alt="Buku" class="w-16 h-20 object-cover rounded">
        <div>
          <h3 class="font-semibold text-lg"><?= htmlspecialchars($row['judul']) ?></h3>
          <p class="text-gray-600 text-sm">Penulis: <?= htmlspecialchars($row['penulis']) ?></p>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <div class="text-right mt-4">
    <a href="buku.php" class="text-blue-600 hover:underline">Lihat lebih banyak →</a>
  </div>
</section>

<!-- Kontak -->
<section class="py-10 px-4 bg-gray-100 max-w-6xl mx-auto">
  <h2 class="text-2xl font-semibold mb-4 text-blue-800">📍 Kontak & Lokasi</h2>
  <p class="mb-2 flex items-center gap-2"><i data-feather="map-pin" class="text-blue-600"></i> Jalan Pendidikan No. 10, Kota Buku</p>
  <p class="mb-2 flex items-center gap-2"><i data-feather="phone" class="text-blue-600"></i> (021) 12345678</p>
  <p class="mb-2 flex items-center gap-2"><i data-feather="mail" class="text-blue-600"></i> perpus@perpuskita.id</p>
</section>

<!-- Footer -->
<footer class="bg-white text-center py-4 border-t">
  <p class="text-sm text-gray-600">&copy; 2025 PerpusKita. All rights reserved.</p>
</footer>


<!-- Modal Detail Buku -->
<div id="modalBuku" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
  <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full relative max-h-[90vh] overflow-y-auto">
    <button onclick="tutupModal()" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">&times;</button>
    <div id="kontenModal" class="overflow-y-auto max-h-[75vh] pr-2">
      <!-- Konten detail buku akan dimuat di sini -->
    </div>
  </div>
</div>


<script>
feather.replace();

function bukaModal(id) {
  fetch(`api_buku.php?id=${id}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('kontenModal').innerHTML = html;
      document.getElementById('modalBuku').classList.remove('hidden');
    })
    .catch(() => alert('Gagal memuat data buku'));
}

function tutupModal() {
  document.getElementById('modalBuku').classList.add('hidden');
}
</script>
</body>
</html>
