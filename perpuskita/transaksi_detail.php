<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna']) || !isset($_GET['id'])) {
  echo "<p class='text-red-600'>Akses tidak sah.</p>";
  exit();
}

$id_transaksi = $_GET['id'];
$id_pengguna = $_SESSION['id_pengguna'];

// Ambil detail transaksi
$stmt = $conn->prepare("SELECT t.*, b.judul, b.penulis, b.deskripsi FROM transaksi t 
                        JOIN buku b ON t.id_buku = b.id_buku 
                        WHERE t.id_transaksi = ? AND t.id_pengguna = ?");
$stmt->bind_param("ss", $id_transaksi, $id_pengguna);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "<p class='text-red-600'>Transaksi tidak ditemukan atau bukan milik Anda.</p>";
  exit();
}

$data = $result->fetch_assoc();
?>

<h2 class="text-xl font-bold mb-2 text-blue-800">Detail Transaksi</h2>
<div class="mb-2">
  <p><strong>Judul:</strong> <?= htmlspecialchars($data['judul']) ?></p>
  <p><strong>Penulis:</strong> <?= htmlspecialchars($data['penulis']) ?></p>
  <p><strong>Deskripsi:</strong> <?= nl2br(htmlspecialchars($data['deskripsi'])) ?></p>
</div>
<hr class="my-3">
<div class="mb-2">
  <p><strong>ID Transaksi:</strong> <?= $data['id_transaksi'] ?></p>
  <p><strong>Tanggal Booking:</strong> <?= $data['tanggal_booking'] ?></p>
  <p><strong>Batas Waktu:</strong> <?= $data['batas_waktu'] ?></p>
  <p><strong>Status:</strong> 
    <?php
      $emoji = [
        'menunggu' => '🟡',
        'dipinjam' => '🟢',
        'dikembalikan' => '🔵',
        'ditolak' => '🔴'
      ];
      echo $emoji[$data['status']] . ' ' . ucfirst($data['status']);
    ?>
  </p>
</div>

<?php if ($data['status'] === 'menunggu'): ?>
  <div class="mt-4 text-right">
    <button onclick="batalkan('<?= $data['id_transaksi'] ?>')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
      Batalkan Booking
    </button>
  </div>
<?php endif; ?>
