<?php
session_start();
include 'koneksi.php';

// Cek login dan ID transaksi
if (!isset($_SESSION['id_pengguna']) || !isset($_POST['id_transaksi'])) {
  http_response_code(400);
  echo "Permintaan tidak valid.";
  exit();
}

$id_transaksi = $_POST['id_transaksi'];
$id_pengguna = $_SESSION['id_pengguna'];

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

// Cek status
if ($transaksi['status'] !== 'menunggu') {
  http_response_code(400);
  echo "Hanya transaksi dengan status 'menunggu' yang bisa dibatalkan.";
  exit();
}

$id_buku = $transaksi['id_buku'];

// Hapus transaksi
$delete = $conn->prepare("DELETE FROM transaksi WHERE id_transaksi = ?");
$delete->bind_param("s", $id_transaksi);

if ($delete->execute()) {
  // Tambahkan kembali stok buku
  $conn->query("UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");
  echo "Booking berhasil dibatalkan.";
} else {
  http_response_code(500);
  echo "Gagal membatalkan booking.";
}
?>
