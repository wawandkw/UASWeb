<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_transaksi'], $_POST['status'])) {
  $id = $_POST['id_transaksi'];
  $status = $_POST['status'];

  $stmt = $conn->prepare("UPDATE transaksi SET status = ? WHERE id_transaksi = ?");
  $stmt->bind_param("ss", $status, $id);
  $stmt->execute();
}

header("Location: transaksi_admin.php");
exit();
?>
