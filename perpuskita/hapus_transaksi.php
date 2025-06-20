<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_transaksi'])) {
  $id = $_POST['id_transaksi'];
  $conn->query("DELETE FROM transaksi WHERE id_transaksi = '$id'");
}

header("Location: transaksi_admin.php");
exit();
?>
