<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['id_pengguna'])) {
  header("Location: login.php");
  exit();
}

$id_pengguna = $_SESSION['id_pengguna'];
$alert = "";

// Proses jika ada pengiriman form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama = $_POST['nama_lengkap'];
  $email = $_POST['email'];
  $telepon = $_POST['telepon'];
  $alamat = $_POST['alamat'];

  // Update informasi profil
  $update = $conn->prepare("UPDATE pengguna SET nama_lengkap=?, email=?, telepon=?, alamat=? WHERE id_pengguna=?");
  $update->bind_param("sssss", $nama, $email, $telepon, $alamat, $id_pengguna);

  if ($update->execute()) {
    $alert .= "<div class='bg-green-100 text-green-800 px-4 py-2 rounded mb-4'>Profil berhasil diperbarui.</div>";
  } else {
    $alert .= "<div class='bg-red-100 text-red-800 px-4 py-2 rounded mb-4'>Gagal memperbarui profil.</div>";
  }

  // Ganti password jika diisi
  if (!empty($_POST['password_lama']) || !empty($_POST['password_baru']) || !empty($_POST['konfirmasi_password'])) {
    $lama = $_POST['password_lama'];
    $baru = $_POST['password_baru'];
    $konfirmasi = $_POST['konfirmasi_password'];

    if (empty($lama) || empty($baru) || empty($konfirmasi)) {
      $alert .= "<div class='bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4'>Semua kolom password harus diisi.</div>";
    } elseif ($baru !== $konfirmasi) {
      $alert .= "<div class='bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4'>Password baru dan konfirmasi tidak cocok.</div>";
    } else {
      $cek = $conn->prepare("SELECT password FROM pengguna WHERE id_pengguna = ?");
      $cek->bind_param("s", $id_pengguna);
      $cek->execute();
      $res = $cek->get_result()->fetch_assoc();

      if (!password_verify($lama, $res['password'])) {
        $alert .= "<div class='bg-yellow-100 text-yellow-800 px-4 py-2 rounded mb-4'>Password lama salah.</div>";
      } else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);
        $ganti = $conn->prepare("UPDATE pengguna SET password=? WHERE id_pengguna=?");
        $ganti->bind_param("ss", $hash, $id_pengguna);
        if ($ganti->execute()) {
          $alert .= "<div class='bg-green-100 text-green-800 px-4 py-2 rounded mb-4'>Password berhasil diubah.</div>";
        } else {
          $alert .= "<div class='bg-red-100 text-red-800 px-4 py-2 rounded mb-4'>Gagal mengubah password.</div>";
        }
      }
    }
  }
}

// Ambil data terbaru
$query = $conn->prepare("SELECT * FROM pengguna WHERE id_pengguna = ?");
$query->bind_param("s", $id_pengguna);
$query->execute();
$data = $query->get_result()->fetch_assoc();

$is_expired = (strtotime($data['tanggal_berakhir']) < strtotime(date('Y-m-d')));
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PerpusKita | Profil Pengguna</title>
  <link rel="icon" href="logo.png" type="image/png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-800">

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
        <a href="riwayat.php" class="flex items-center gap-1 text-gray-700 hover:text-blue-700">
          <i data-feather="clock"></i> Riwayat
        </a>
        <a href="profil.php" class="flex items-center gap-1 text-blue-700 font-semibold">
          <i data-feather="user"></i> Profil
        </a>
      </nav>
    </div>
  </header>

  <!-- Konten -->
  <section class="max-w-2xl mx-auto px-4 py-10">
    <h1 class="text-3xl font-bold text-blue-800 text-center mb-6">👤 Profil Pengguna</h1>

    <?= $alert ?>

    <form method="POST" class="bg-white shadow rounded-lg p-6 space-y-4">
      <div class="border-b pb-2">
        <p class="text-gray-500 text-sm uppercase">ID Anggota</p>
        <p class="font-semibold text-lg text-blue-700"><?= htmlspecialchars($data['id_pengguna']) ?></p>
      </div>

      <div class="pt-4">
        <p class="text-gray-500 text-sm uppercase">Anggota Sejak</p>
        <p class="text-gray-800"><?= date('d F Y', strtotime($data['tanggal_daftar'])) ?></p>
      </div>

      <div>
        <p class="text-gray-500 text-sm uppercase">Keanggotaan Berakhir</p>
        <p class="text-gray-800 <?= $is_expired ? 'text-red-600 font-semibold' : '' ?>">
          <?= date('d F Y', strtotime($data['tanggal_berakhir'])) ?>
          <?= $is_expired ? '(Keanggotaan Berakhir)' : '' ?>
        </p>
      </div>


      <div class= "border-t">
        <label class="text-sm text-gray-600">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" class="w-full mt-1 border rounded px-3 py-2" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" required>
      </div>

      <div>
        <label class="text-sm text-gray-600">Email</label>
        <input type="email" name="email" class="w-full mt-1 border rounded px-3 py-2" value="<?= htmlspecialchars($data['email']) ?>" required>
      </div>

      <div>
        <label class="text-sm text-gray-600">Nomor Telepon</label>
        <input type="text" name="telepon" class="w-full mt-1 border rounded px-3 py-2" value="<?= htmlspecialchars($data['telepon']) ?>">
      </div>

      <div>
        <label class="text-sm text-gray-600">Alamat</label>
        <textarea name="alamat" class="w-full mt-1 border rounded px-3 py-2"><?= htmlspecialchars($data['alamat']) ?></textarea>
      </div>

      <!-- Ubah Password -->
      <div class="border-t pt-6">
        <p class="text-gray-700 font-semibold mb-2">Ganti Password</p>
        <div class="space-y-3">
          <div>
            <label class="text-sm text-gray-600">Password Lama</label>
            <input type="password" name="password_lama" class="w-full mt-1 border rounded px-3 py-2">
          </div>
          <div>
            <label class="text-sm text-gray-600">Password Baru</label>
            <input type="password" name="password_baru" class="w-full mt-1 border rounded px-3 py-2">
          </div>
          <div>
            <label class="text-sm text-gray-600">Konfirmasi Password Baru</label>
            <input type="password" name="konfirmasi_password" class="w-full mt-1 border rounded px-3 py-2">
          </div>
        </div>
      </div>

      <!-- Tombol Aksi -->
      <div class="pt-6 flex justify-between">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          <i data-feather="save" class="inline mr-1"></i>Simpan Perubahan
        </button>
        <a href="logout.php" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-sm">
          <i data-feather="log-out" class="inline mr-1"></i>Logout
        </a>
      </div>
    </form>
  </section>

  <footer class="bg-white text-center py-4 border-t">
    <p class="text-sm text-gray-600">&copy; 2025 PerpusKita. All rights reserved.</p>
  </footer>

  <script>
    feather.replace();
  </script>
</body>
</html>
