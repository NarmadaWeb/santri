<?php
require_once '../includes/auth.php';
checkSantri();
require_once '../includes/db.php';

$current_page = 'profil';

// Fetch santri data
$stmt = $conn->prepare("SELECT s.*, k.nama as nama_kelas FROM santri s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$santri = $stmt->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Profil Saya - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_santri.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="mb-8">
            <h2 class="text-3xl font-bold text-primary">Profil Saya</h2>
            <p class="text-outline">Data pribadi dan informasi akademik Anda.</p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="space-y-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10 text-center">
                    <div class="w-32 h-32 rounded-full border-4 border-primary/20 overflow-hidden mx-auto mb-4">
                        <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDxjYVAR5PRliI5let-LbIWHTVmGrSLCyjPapUTUbXSF4EslAcY0dTxXdm2NDyyWlgy9REnom-Ex5zHtpo0dPtPoJ1bVYmkcuYFJbkiYcdnFqEytUHJz1FQBDzOap0Kx5inQLetS9M3Zkdsj4eFIxXC-MRxNeXSoLIk9Gf_G72m82lF3HtT746h6cLfLNdpDz534LZLUb0jKHITEH8DL9u2AyLdKT5fmzKH0Qy-fAsIP5_tlCz3lGa0vKetN68XT37tJL6Seautwl57"/>
                    </div>
                    <h3 class="text-xl font-bold text-primary"><?php echo $santri['nama']; ?></h3>
                    <p class="text-xs font-bold text-outline uppercase tracking-widest mb-4">NIS: <?php echo $santri['nis']; ?></p>
                    <span class="px-4 py-1 bg-primary/10 text-primary rounded-full text-xs font-bold uppercase"><?php echo $santri['status']; ?></span>
                </div>
            </div>

            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-outline/10">
                    <h4 class="font-bold text-primary mb-6 flex items-center gap-2"><span class="material-symbols-outlined text-secondary">info</span> Detail Personal</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">Nama Lengkap</p>
                            <p class="font-semibold"><?php echo $santri['nama']; ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">Kelas</p>
                            <p class="font-semibold"><?php echo $santri['nama_kelas'] ?: '-'; ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">Nama Orang Tua</p>
                            <p class="font-semibold"><?php echo $santri['orang_tua'] ?: '-'; ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">No. WhatsApp</p>
                            <p class="font-semibold"><?php echo $santri['no_hp'] ?: '-'; ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-[10px] font-bold text-outline uppercase tracking-wider mb-1">Alamat</p>
                            <p class="font-semibold"><?php echo $santri['alamat'] ?: '-'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
