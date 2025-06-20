<?php
session_start();
include 'koneksi.php';

// Cek apakah ID pengguna tersedia di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
  $_SESSION['pesan_error'] = "ID anggota tidak valid.";
  header("Location: anggota_admin.php");
  exit;
}

$id = $_GET['id'];

// Ambil data anggota
$stmt = $conn->prepare("SELECT * FROM pengguna WHERE id_pengguna = ? AND role = 'anggota'");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();
$anggota = $result->fetch_assoc();

if (!$anggota) {
  $_SESSION['pesan_error'] = "Data anggota tidak ditemukan.";
  header("Location: anggota_admin.php");
  exit;
}

// Tangani form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $bulan_tambahan = intval($_POST['bulan']);

  if ($bulan_tambahan < 1) {
    $_SESSION['pesan_error'] = "Minimal perpanjangan adalah 1 bulan.";
    header("Location: anggota_perpanjang.php?id=$id");
    exit;
  }

  $tanggal_berakhir_lama = $anggota['tanggal_berakhir'];
  $stmt_update = $conn->prepare("UPDATE pengguna SET tanggal_berakhir = DATE_ADD(tanggal_berakhir, INTERVAL ? MONTH) WHERE id_pengguna = ?");
  $stmt_update->bind_param("is", $bulan_tambahan, $id);

  if ($stmt_update->execute()) {
    $_SESSION['pesan_sukses'] = "Perpanjangan berhasil. Masa berlaku diperpanjang $bulan_tambahan bulan.";
    header("Location: anggota_detail.php?id=$id");
    exit;
  } else {
    $_SESSION['pesan_error'] = "Gagal memperpanjang keanggotaan.";
    header("Location: anggota_perpanjang.php?id=$id");
    exit;
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Perpanjang Keanggotaan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white p-6 rounded-lg shadow max-w-md w-full">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Perpanjang Keanggotaan</h2>

    <p class="mb-4 text-gray-700">
      Anggota: <strong><?= htmlspecialchars($anggota['nama_lengkap']) ?></strong><br>
      Masa berlaku sebelumnya: <strong><?= htmlspecialchars(date('d M Y', strtotime($anggota['tanggal_berakhir']))) ?></strong>
    </p>

    <?php if (isset($_SESSION['pesan_error'])): ?>
      <div class="mb-4 bg-red-100 text-red-800 px-4 py-2 rounded">
        <?= htmlspecialchars($_SESSION['pesan_error']) ?>
        <?php unset($_SESSION['pesan_error']); ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label for="bulan" class="block text-sm font-medium text-gray-700">Jumlah Bulan Perpanjangan</label>
        <input type="number" name="bulan" id="bulan" min="1" required
               class="mt-1 w-full border px-4 py-2 rounded focus:outline-none focus:ring focus:border-blue-500" />
      </div>
      <div class="flex justify-end gap-3">
        <a href="anggota_detail.php?id=<?= urlencode($id) ?>"
           class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-sm">Batal</a>
        <button type="submit"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Perpanjang</button>
      </div>
    </form>
  </div>
</body>
</html>
