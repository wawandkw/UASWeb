<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
  header("Location: login.php");
  exit();
}

$id_pengguna = $_SESSION['id_pengguna'];

// Cek tanggal berakhir keanggotaan
$cek_pengguna = $conn->prepare("SELECT tanggal_berakhir FROM pengguna WHERE id_pengguna = ?");
$cek_pengguna->bind_param("s", $id_pengguna);
$cek_pengguna->execute();
$res_pengguna = $cek_pengguna->get_result();

if ($res_pengguna->num_rows === 0) {
  header("Location: riwayat.php?error=Akun tidak ditemukan");
  exit();
}

$tgl_berakhir = $res_pengguna->fetch_assoc()['tanggal_berakhir'];

if (strtotime($tgl_berakhir) < strtotime(date('Y-m-d'))) {
  header("Location: riwayat.php?error=Keanggotaan Anda telah berakhir, perpanjang terlebih dahulu");
  exit();
}

// Validasi input
if (!isset($_POST['id_buku'])) {
  header("Location: dashboard.php?error=ID buku tidak valid");
  exit();
}

$id_buku = $_POST['id_buku'];

// Cek stok
$stmt = $conn->prepare("SELECT stok FROM buku WHERE id_buku = ?");
$stmt->bind_param("s", $id_buku);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: dashboard.php?error=Buku tidak ditemukan");
  exit();
}

$buku = $result->fetch_assoc();

if ($buku['stok'] < 1) {
  header("Location: dashboard.php?error=Stok buku habis");
  exit();
}

// Buat ID transaksi khusus: TRX + Tahun + Nomor Urut
$prefix = "TRX" . date("Y");
$cek_id = $conn->query("SELECT id_transaksi FROM transaksi WHERE id_transaksi LIKE '$prefix%' ORDER BY id_transaksi DESC LIMIT 1");

if ($cek_id->num_rows > 0) {
  $last_id = $cek_id->fetch_assoc()['id_transaksi'];
  $urutan = (int)substr($last_id, -4) + 1;
} else {
  $urutan = 1;
}

$id_transaksi = $prefix . str_pad($urutan, 4, '0', STR_PAD_LEFT);

// Simpan transaksi
$tanggal_booking = date('Y-m-d');
$batas_waktu = date('Y-m-d', strtotime('+7 days'));

$insert = $conn->prepare("INSERT INTO transaksi (id_transaksi, id_pengguna, id_buku, tanggal_booking, batas_waktu, status) VALUES (?, ?, ?, ?, ?, 'menunggu')");
$insert->bind_param("sssss", $id_transaksi, $id_pengguna, $id_buku, $tanggal_booking, $batas_waktu);

if ($insert->execute()) {
  // Kurangi stok
  $update = $conn->prepare("UPDATE buku SET stok = stok - 1 WHERE id_buku = ?");
  $update->bind_param("s", $id_buku);
  $update->execute();

  header("Location: riwayat.php?success=Booking berhasil dengan ID $id_transaksi");
  exit();
} else {
  header("Location: riwayat.php?error=Gagal melakukan booking");
  exit();
}
?>
