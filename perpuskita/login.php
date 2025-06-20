<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $query = $conn->prepare("SELECT * FROM pengguna WHERE email = ?");
  $query->bind_param("s", $email);
  $query->execute();
  $result = $query->get_result();
  $user = $result->fetch_assoc();

  if ($user) {
    if (password_verify($password, $user['password'])) {
      // Simpan data session
      $_SESSION['id_pengguna'] = $user['id_pengguna'];
      $_SESSION['nama'] = $user['nama_lengkap'];
      $_SESSION['role'] = $user['role'];

      // Arahkan sesuai peran
      if ($user['role'] === 'admin') {
        header("Location: dashboard_admin.php");
        exit();
      } elseif ($user['role'] === 'anggota') {
        header("Location: dashboard.php");
        exit();
      } else {
        $error = "Peran tidak dikenali.";
      }
    } else {
      $error = "Password salah.";
    }
  } else {
    $error = "Email tidak ditemukan.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PerpusKita - Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: "#1E3A8A",
          },
        },
      },
    };
  </script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
  <div class="bg-white shadow-lg rounded-xl p-8 w-full max-w-md">
    <img src="logo.png" alt="Logo Perpustakaan" class="h-24 w-24 mx-auto" />
    <h2 class="text-2xl font-bold text-primary text-center mb-6">Login PerpusKita</h2>

    <?php if (isset($error)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'logout'): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
        Anda berhasil logout.
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-5">
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" id="email" required
          class="mt-1 block w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary" />
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" id="password" required
          class="mt-1 block w-full border rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary" />
      </div>
      <button type="submit"
        class="w-full bg-primary text-white font-semibold py-2 rounded-lg hover:bg-blue-800 transition">
        Login
      </button>
    </form>
  </div>
</body>
</html>
