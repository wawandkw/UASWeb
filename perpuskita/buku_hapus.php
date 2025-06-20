<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: dashboard.php");
  exit();
}

// Pastikan parameter ID buku tersedia
if (!isset($_GET['id'])) {
  header("Location: buku_admin.php?error=ID buku tidak ditemukan");
  exit();
}

$id_buku = $_GET['id'];

// Ambil nama file gambar untuk dihapus jika ada
$stmt = $conn->prepare("SELECT gambar FROM buku WHERE id_buku = ?");
$stmt->bind_param("s", $id_buku);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header("Location: buku_admin.php?error=Buku tidak ditemukan");
  exit();
}

$data = $result->fetch_assoc();
$gambar = $data['gambar'];

// Hapus data dari database
$delete = $conn->prepare("DELETE FROM buku WHERE id_buku = ?");
$delete->bind_param("s", $id_buku);

if ($delete->execute()) {
  // Hapus file gambar jika ada
  if (!empty($gambar) && file_exists('uploads/' . $gambar)) {
    unlink('uploads/' . $gambar);
  }
  header("Location: buku_admin.php?success=Buku berhasil dihapus");
  exit();
} else {
  header("Location: buku_admin.php?error=Gagal menghapus buku");
  exit();
}
?>
