<?php
include 'koneksi.php';
session_start();

if (!isset($_GET['id'])) exit('ID tidak ditemukan');

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM buku WHERE id_buku = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$buku = $result->fetch_assoc();

if (!$buku) exit('Buku tidak ditemukan');
?>

<div class="flex gap-4">
  <img src="uploads/<?= $buku['gambar'] ?>" class="w-24 h-32 object-cover rounded shadow">
  <div>
    <h2 class="text-xl font-bold text-blue-800 mb-2"><?= $buku['judul'] ?></h2>
    <p class="text-gray-700"><strong>Penulis:</strong> <?= $buku['penulis'] ?></p>
    <p class="text-gray-700"><strong>Penerbit:</strong> <?= $buku['penerbit'] ?></p>
    <p class="text-gray-700"><strong>Kategori:</strong> <?= $buku['kategori'] ?></p>
    <p class="text-gray-700"><strong>Stok:</strong> <?= $buku['stok'] ?></p>
    <p class="text-gray-700 mt-2"><?= nl2br(htmlspecialchars($buku['deskripsi'])) ?></p>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'anggota'): ?>
    <form action="booking_proses.php" method="POST" class="mt-4">
      <input type="hidden" name="id_buku" value="<?= $buku['id_buku'] ?>">
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Booking Buku</button>
    </form>
    <?php endif; ?>
  </div>
</div>
