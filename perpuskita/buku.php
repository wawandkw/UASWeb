<?php
session_start();
include 'koneksi.php';

$search = $_GET['search'] ?? '';
$keywords = array_filter(explode(" ", $search));
$conditions = [];
$params = [];

foreach ($keywords as $word) {
  $word = "%$word%";
  $conditions[] = "(judul LIKE ? OR penulis LIKE ? OR kategori LIKE ?)";
  $params[] = $word;
  $params[] = $word;
  $params[] = $word;
}

$sql = "SELECT * FROM buku";
if (!empty($conditions)) {
  $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY judul ASC";
$stmt = $conn->prepare($sql);

if (!empty($params)) {
  $types = str_repeat("s", count($params));
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PerpusKita - Daftar Buku</title>
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
      <a href="dashboard.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
        <i data-feather="home"></i> Home
      </a>
      <a href="buku.php" class="flex items-center gap-1 text-blue-700 font-semibold">
        <i data-feather="book-open"></i> Buku
      </a>
      <a href="riwayat.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
        <i data-feather="clock"></i> Riwayat
      </a>
      <a href="profil.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
        <i data-feather="user"></i> Profil
      </a>
    </nav>
  </div>
</header>

<!-- Judul -->
<section class="py-10 px-4 text-center">
  <h1 class="text-3xl font-bold text-blue-800 mb-2">📚 Daftar Buku</h1>
  <p class="text-gray-700">Jelajahi koleksi buku yang tersedia di PerpusKita</p>
</section>

<!-- Form Pencarian -->
<section class="max-w-3xl mx-auto px-4 mb-6">
  <form method="get" class="flex items-center gap-2">
    <input type="text" name="search" placeholder="Cari judul, penulis, atau kategori..."
      class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400"
      value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Cari</button>
  </form>
</section>

<!-- Daftar Buku -->
<section class="max-w-6xl mx-auto px-4 pb-16">
  <div class="grid md:grid-cols-3 gap-6">
    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="bg-white shadow rounded overflow-hidden cursor-pointer" onclick="bukaModal('<?= $row['id_buku'] ?>')">
          <img src="uploads/<?= htmlspecialchars($row['gambar'] ?? 'buku.png') ?>" alt="Buku"
               class="w-full h-40 object-cover">
          <div class="p-4">
            <h2 class="font-bold text-lg"><?= htmlspecialchars($row['judul']) ?></h2>
            <p class="text-gray-600 text-sm">Penulis: <?= htmlspecialchars($row['penulis']) ?></p>
            <p class="text-sm mt-2 text-gray-700"><?= htmlspecialchars(substr($row['deskripsi'], 0, 60)) ?>...</p>
            <p class="text-sm text-blue-600 mt-2">Stok: <?= $row['stok'] ?></p>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center col-span-3 text-gray-500">Tidak ada buku ditemukan.</p>
    <?php endif; ?>
  </div>
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
    fetch('api_buku.php?id=' + id)
      .then(res => res.text())
      .then(html => {
        document.getElementById('kontenModal').innerHTML = html;
        document.getElementById('modalBuku').classList.remove('hidden');
      });
  }

  function tutupModal() {
    document.getElementById('modalBuku').classList.add('hidden');
  }
</script>
</body>
</html>
