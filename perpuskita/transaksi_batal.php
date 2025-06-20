<?php
session_start();
include 'koneksi.php';

// Validasi sesi dan input
if (!isset($_SESSION['id_pengguna']) || !isset($_POST['id_transaksi'])) {
  http_response_code(400);
  echo "Permintaan tidak valid.";
  exit();
}

$id_transaksi = $_POST['id_transaksi'];
$id_pengguna  = $_SESSION['id_pengguna'];

// Ambil transaksi untuk validasi
$stmt = $conn->prepare("SELECT id_buku, status FROM transaksi WHERE id_transaksi = ? AND id_pengguna = ?");
$stmt->bind_param("ss", $id_transaksi, $id_pengguna);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  http_response_code(403);
  echo "Transaksi tidak ditemukan atau bukan milik Anda.";
  exit();
}

$transaksi = $result->fetch_assoc();

if ($transaksi['status'] !== 'menunggu') {
  http_response_code(400);
  echo "Hanya transaksi dengan status 'menunggu' yang bisa dibatalkan.";
  exit();
}

$id_buku = $transaksi['id_buku'];

// Update status menjadi 'dibatalkan'
$update = $conn->prepare("UPDATE transaksi SET status = 'dibatalkan' WHERE id_transaksi = ?");
$update->bind_param("s", $id_transaksi);

if ($update->execute()) {
  $restore = $conn->prepare("UPDATE buku SET stok = stok + 1 WHERE id_buku = ?");
  $restore->bind_param("s", $id_buku);
  $restore->execute();
  echo "Transaksi berhasil dibatalkan.";
} else {
  echo "Gagal membatalkan: " . $conn->error;
}
?>
