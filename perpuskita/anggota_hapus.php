<?php
session_start();
include 'koneksi.php';

// Hanya admin yang boleh mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

// Pastikan ID tersedia
if (isset($_GET['id'])) {
  $id = $_GET['id'];

  // Cegah admin menghapus dirinya sendiri
  if ($id === $_SESSION['id_pengguna']) {
    $_SESSION['pesan_error'] = "Tidak bisa menghapus akun Anda sendiri.";
    header("Location: anggota_admin.php");
    exit();
  }

  // Hapus data
  $stmt = $conn->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
  $stmt->bind_param("s", $id);

  if ($stmt->execute()) {
    $_SESSION['pesan_sukses'] = "Data anggota berhasil dihapus.";
  } else {
    $_SESSION['pesan_error'] = "Gagal menghapus anggota.";
  }

  $stmt->close();
} else {
  $_SESSION['pesan_error'] = "ID anggota tidak ditemukan.";
}

header("Location: anggota_admin.php");
exit();
?>
