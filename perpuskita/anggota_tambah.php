<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_pengguna']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit();
}

function generateId($conn, $role) {
  $prefix = $role === 'admin' ? 'ADM' : 'ANG';
  $stmt = $conn->prepare("SELECT id_pengguna FROM pengguna WHERE id_pengguna LIKE ? ORDER BY id_pengguna DESC LIMIT 1");
  $like = $prefix.'%';
  $stmt->bind_param("s", $like);
  $stmt->execute();
  $last = $stmt->get_result()->fetch_assoc();
  $next = $last ? ((int)substr($last['id_pengguna'],3))+1 : 1;
  return $prefix . str_pad($next, 5, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nama     = $_POST['nama'];
  $email    = $_POST['email'];
  $telepon  = $_POST['telepon'];
  $alamat   = $_POST['alamat'];
  $role     = $_POST['role'];
  $password = $_POST['password'];
  $confirm  = $_POST['confirm'];

  // validasi sederhana
  if ($password !== $confirm) {
    $error = "Konfirmasi password tidak cocok.";
  } else {
    $id_pengguna      = generateId($conn, $role);
    $tanggal_daftar   = date('Y-m-d');
    $tanggal_berakhir = date('Y-m-d', strtotime('+1 years'));
    $hash             = password_hash($password, PASSWORD_DEFAULT);

    $ins = $conn->prepare("INSERT INTO pengguna 
     (id_pengguna,nama_lengkap,email,password,telepon,alamat,tanggal_daftar,tanggal_berakhir,role)
     VALUES (?,?,?,?,?,?,?,?,?)");
    $ins->bind_param("sssssssss", $id_pengguna,$nama,$email,$hash,$telepon,$alamat,
                     $tanggal_daftar,$tanggal_berakhir,$role);

    if ($ins->execute()) {
      header("Location: anggota_detail.php?id=$id_pengguna&add=success");
      exit();
    } else {
      $error = "Gagal menambahkan pengguna. Email mungkin sudah dipakai.";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Tambah Pengguna Baru</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-xl shadow max-w-xl w-full">
    <h2 class="text-2xl font-bold text-blue-700 mb-6 text-center">Tambah Pengguna Baru</h2>

    <?php if(isset($error)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <input name="nama" placeholder="Nama Lengkap" required class="w-full border rounded px-4 py-2"/>
      <input name="email" type="email" placeholder="Email" required class="w-full border rounded px-4 py-2"/>
      <input name="telepon" placeholder="Telepon" class="w-full border rounded px-4 py-2"/>
      <textarea name="alamat" placeholder="Alamat" rows="3" class="w-full border rounded px-4 py-2"></textarea>

      <div class="grid md:grid-cols-2 gap-4">
        <input name="password" type="password" placeholder="Password" required class="border rounded px-4 py-2"/>
        <input name="confirm" type="password" placeholder="Konfirmasi Password" required class="border rounded px-4 py-2"/>
      </div>

      <select name="role" class="w-full border rounded px-4 py-2" required>
        <option disabled selected value="">Pilih Role</option>
        <option value="anggota">Anggota</option>
        <option value="admin">Admin</option>
      </select>

      <div class="flex justify-end gap-3">
        <a href="anggota_admin.php" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Batal</a>
        <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
      </div>
    </form>
  </div>
</body>
</html>
