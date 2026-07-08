<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'laporan';

// Fetch santri for dropdown
$santri_res = $conn->query("SELECT id, nama FROM santri WHERE status='aktif'");
$santri_list = [];
while($s = $santri_res->fetch_assoc()) $santri_list[] = $s;

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Laporan Akademik - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Laporan Akademik</h2>
                <p class="text-outline">Cetak rapor dan rekap kehadiran santri.</p>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-8 rounded-2xl shadow-sm border border-outline/10 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center text-primary mb-4">
                    <span class="material-symbols-outlined text-3xl">contact_page</span>
                </div>
                <h3 class="text-xl font-bold text-primary mb-2">Cetak Rapor Digital</h3>
                <p class="text-sm text-outline mb-6">Hasilkan dokumen rapor hasil belajar santri per semester dalam format PDF.</p>
                <div class="w-full flex gap-2">
                    <select class="flex-1 bg-background border-none rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none">
                        <option>Pilih Santri...</option>
                        <?php foreach($santri_list as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo $s['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button class="bg-primary text-white px-4 py-2 rounded-lg font-bold hover:brightness-110 transition-all"><span class="material-symbols-outlined">print</span></button>
                </div>
            </div>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-outline/10 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-secondary/10 rounded-full flex items-center justify-center text-secondary mb-4">
                    <span class="material-symbols-outlined text-3xl">summarize</span>
                </div>
                <h3 class="text-xl font-bold text-primary mb-2">Rekap Kehadiran</h3>
                <p class="text-sm text-outline mb-6">Unduh data persentase kehadiran santri dalam periode tertentu.</p>
                <div class="w-full flex gap-2">
                    <input class="flex-1 bg-background border-none rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none" type="month" value="<?php echo date('Y-m'); ?>"/>
                    <button class="bg-primary text-white px-4 py-2 rounded-lg font-bold hover:brightness-110 transition-all flex items-center gap-2"><span class="material-symbols-outlined">download</span></button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
