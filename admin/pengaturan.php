<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'pengaturan';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_password'])) {
        $password = $_POST['password'];
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed, $_SESSION['user_id']);
        if ($stmt->execute()) {
            $success = 'Password berhasil diperbarui.';
        } else {
            $error = 'Gagal memperbarui password.';
        }
    }
}

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Pengaturan Sistem - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="mb-8">
            <h2 class="text-3xl font-bold text-primary">Pengaturan Sistem</h2>
            <p class="text-outline">Konfigurasi institusi dan profil admin.</p>
        </header>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <section class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                    <h3 class="font-bold text-primary mb-6 flex items-center gap-2"><span class="material-symbols-outlined text-secondary">domain</span> Informasi TPQ</h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-outline uppercase ml-1">Nama Institusi</label>
                                <input class="w-full p-2 bg-background border-none rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" type="text" value="TPQ Al-Misbahul Qur'an"/>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-outline uppercase ml-1">Email Resmi</label>
                                <input class="w-full p-2 bg-background border-none rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" type="email" value="info@almisbahulquran.id"/>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-outline uppercase ml-1">Alamat</label>
                            <textarea class="w-full p-2 bg-background border-none rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" rows="3">Jl. Kebon Jeruk No. 45, Jakarta</textarea>
                        </div>
                    </div>
                </section>

                <form method="POST" class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                    <h3 class="font-bold text-primary mb-6 flex items-center gap-2"><span class="material-symbols-outlined text-secondary">lock</span> Keamanan</h3>
                    <div class="space-y-4">
                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-outline uppercase ml-1">Ganti Password</label>
                            <input name="password" class="w-full p-2 bg-background border-none rounded-lg text-sm focus:ring-2 focus:ring-primary outline-none" placeholder="Password Baru" type="password" required/>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6">
                        <button type="submit" name="update_password" class="bg-primary text-white px-8 py-2 rounded-lg font-bold shadow-md hover:brightness-110 active:scale-95 transition-all">Simpan Perubahan</button>
                    </div>
                </form>
            </div>

            <div class="space-y-8">
                <section class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10 flex flex-col items-center text-center">
                    <h3 class="font-bold text-primary mb-6 w-full text-left">Logo Institusi</h3>
                    <div class="w-32 h-32 rounded-full border-4 border-background overflow-hidden mb-4 shadow-inner">
                        <img class="w-full h-full object-cover" data-alt="TPQ Logo" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAO9jpYjY4EdmEckiNR9YzI0p7BnWdYJCCtAbYtTbDhrDhXfOBBemObqiYW8GOISUs76I1HXJEWLZ-8z06YnuviS_QO8iIq0qsnKlq3HbIyiMcoq1E4ujx-jrLHxpXDVg2W9bghxV089xZYMCRCOmD08Qm-n9tmsLnbP7OiSlMGzzovSDZMfxOKC3YqZ8YLC3oFMcJ79GuRZlut64IZv9EEIvokkCIiwMbsIfakaxGhyzUndqKreE7idz4bAxhjNFkQwrRxCU5ooH5i"/>
                    </div>
                    <button class="text-xs font-bold text-primary hover:underline">Ganti Foto Logo</button>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
