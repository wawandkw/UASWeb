<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
  header("Location: login.php");
  exit();
}

$id_pengguna = $_SESSION['id_pengguna'];

// Ambil semua transaksi pengguna
$query = $conn->prepare("SELECT t.*, b.judul FROM transaksi t JOIN buku b ON t.id_buku = b.id_buku WHERE t.id_pengguna = ? ORDER BY t.tanggal_booking DESC");
$query->bind_param("s", $id_pengguna);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PerpusKita | Riwayat Peminjaman</title>
  <link rel="icon" href="logo.png" type="image/png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="flex flex-col min-h-screen bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <header class="bg-white shadow">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
      <div class="flex items-center gap-2">
        <img src="logo.png" alt="Logo" class="w-8 h-8">
        <span class="text-xl font-semibold text-blue-700">PerpusKita</span>
      </div>
      <nav class="flex gap-6 items-center">
        <a href="dashboard.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
          <i data-feather="home"></i> Home
        </a>
        <a href="buku.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
          <i data-feather="book-open"></i> Buku
        </a>
        <a href="riwayat.php" class="flex items-center gap-1 text-blue-700 font-semibold">
          <i data-feather="clock"></i> Riwayat
        </a>
        <a href="profil.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
          <i data-feather="user"></i> Profil
        </a>
      </nav>
    </div>
  </header>

  <!-- Konten Utama -->
  <main class="flex-1 p-6">
    <h1 class="text-2xl font-bold text-blue-800 mb-4">Riwayat Peminjaman</h1>
    <div class="overflow-x-auto bg-white rounded shadow">
      <table class="w-full table-auto text-sm">
        <thead class="bg-gray-200 text-gray-600">
          <tr>
            <th class="px-4 py-2 text-left">Judul Buku</th>
            <th class="px-4 py-2 text-center">Tanggal Booking</th>
            <th class="px-4 py-2 text-center">Status</th>
            <th class="px-4 py-2 text-center">Batas Waktu</th>
            <th class="px-4 py-2 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $today = date('Y-m-d');
          while ($row = $result->fetch_assoc()):
            $status = $row['status'];
            $batas_waktu = $row['batas_waktu'];

            $is_late = ($status === 'dipinjam' && $batas_waktu < $today);
            $batas_class = $is_late ? 'text-red-600 font-semibold' : 'text-gray-800';

            $emoji = [
              'menunggu' => '🟡',
              'dipinjam' => '🟢',
              'dikembalikan' => '🔵',
              'dibatalkan' => '🔴'
            ];
          ?>
          <tr class="border-t">
            <td class="px-4 py-2"><?= htmlspecialchars($row['judul']) ?></td>
            <td class="px-4 py-2 text-center"><?= htmlspecialchars($row['tanggal_booking']) ?></td>
            <td class="px-4 py-2 text-center"><?= $emoji[$status] . ' ' . ucfirst($status) ?></td>
            <td class="px-4 py-2 text-center <?= $batas_class ?>"><?= htmlspecialchars($batas_waktu) ?></td>
            <td class="px-4 py-2 text-center">
              <button onclick="bukaDetail('<?= $row['id_transaksi'] ?>')" class="text-indigo-600 hover:underline">Detail</button>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- Modal Pop-up -->
  <div id="modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg max-w-lg w-full relative max-h-[90vh]">
      <button onclick="tutupModal()" class="absolute top-2 right-2 text-gray-600 hover:text-red-600 text-2xl">&times;</button>
      <div id="kontenModal" class="overflow-y-auto max-h-[75vh] pr-2">
        <!-- Konten transaksi_detail.php akan dimuat di sini -->
      </div>
    </div>
  </div>

  <script>
    feather.replace();

    function bukaDetail(id) {
      fetch('transaksi_detail.php?id=' + id)
        .then(res => res.text())
        .then(html => {
          document.getElementById('kontenModal').innerHTML = html;
          document.getElementById('modal').classList.remove('hidden');
        });
    }

    function tutupModal() {
      document.getElementById('modal').classList.add('hidden');
    }

    function batalkan(id) {
      if (confirm("Yakin ingin membatalkan booking?")) {
        fetch('transaksi_batal.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: 'id_transaksi=' + encodeURIComponent(id)
        })
        .then(res => res.text())
        .then(msg => {
          alert(msg);
          location.reload();
        });
      }
    }
  </script>
</body>
</html>
