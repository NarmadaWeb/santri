<?php
require_once 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nama = $_POST['nama'];
    $nis = $_POST['nis'];
    $role = 'santri';

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $conn->begin_transaction();

    try {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hashed_password, $role);
        $stmt->execute();
        $user_id = $conn->insert_id;

        // Insert into santri table
        $stmt = $conn->prepare("INSERT INTO santri (user_id, nis, nama) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $nis, $nama);
        $stmt->execute();

        $conn->commit();
        $success = 'Pendaftaran berhasil! Silakan <a href="login.php" class="underline">login</a>.';
    } catch (Exception $e) {
        $conn->rollback();
        $error = 'Pendaftaran gagal: ' . $e->getMessage();
    }
}
?>
<!-- Register - TPQ Al-Misbahul Qur'an -->
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Daftar Santri - TPQ Al-Misbahul Qur'an</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script id="tailwind-config">
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              "primary": "#004326",
              "primary-container": "#1a5c3a",
              "secondary": "#755b00",
              "background": "#f8faf5",
              "outline": "#707971",
            },
            fontFamily: { "headline-md": ["Montserrat"], "body-md": ["Inter"] }
          },
        },
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f5f7f0; }
        .islamic-pattern {
            background-color: #f5f7f0;
            background-image: url("https://www.transparenttextures.com/patterns/arabesque.png");
        }
    </style>
</head>
<body class="islamic-pattern min-h-screen flex items-center justify-center p-4">
    <main class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg border border-outline/10">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-container rounded-full mb-4 shadow-lg border-2 border-secondary text-white">
                <span class="material-symbols-outlined text-3xl">person_add</span>
            </div>
            <h1 class="font-headline-lg text-2xl font-bold text-primary">Daftar Santri</h1>
            <p class="font-body-md text-sm text-outline">TPQ Al-Misbahul Qur'an</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $success; ?></span>
            </div>
        <?php endif; ?>

        <form class="space-y-4" method="POST" action="">
            <div>
                <label class="block font-bold text-primary mb-2 text-sm" for="nama">Nama Lengkap</label>
                <input name="nama" class="block w-full px-3 py-2 border border-outline/20 rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" id="nama" placeholder="Masukkan nama lengkap" required type="text"/>
            </div>
            <div>
                <label class="block font-bold text-primary mb-2 text-sm" for="nis">NIS</label>
                <input name="nis" class="block w-full px-3 py-2 border border-outline/20 rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" id="nis" placeholder="Masukkan NIS" required type="text"/>
            </div>
            <div>
                <label class="block font-bold text-primary mb-2 text-sm" for="username">Username</label>
                <input name="username" class="block w-full px-3 py-2 border border-outline/20 rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" id="username" placeholder="Masukkan username" required type="text"/>
            </div>
            <div>
                <label class="block font-bold text-primary mb-2 text-sm" for="password">Kata Sandi</label>
                <input name="password" class="block w-full px-3 py-2 border border-outline/20 rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" id="password" placeholder="Masukkan password" required type="password"/>
            </div>
            <button class="w-full bg-primary-container text-white py-3 px-4 rounded-lg font-bold hover:brightness-110 active:scale-[0.98] transition-all" type="submit">Daftar Sekarang</button>
        </form>
        <div class="mt-6 text-center">
            <p class="text-sm text-outline">Sudah punya akun? <a href="login.php" class="text-primary font-bold hover:underline">Login</a></p>
        </div>
    </main>
</body>
</html>
