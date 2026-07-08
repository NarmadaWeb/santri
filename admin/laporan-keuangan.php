<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'laporan';

// Mock data for finance charts
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'];
$income = [40, 60, 55, 80, 95];

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Laporan Keuangan - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Laporan Keuangan</h2>
                <p class="text-outline">Analisis arus kas dan tunggakan pembayaran.</p>
            </div>
            <button class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow-sm hover:brightness-110 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">analytics</span> Generate Report
            </button>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                <h3 class="font-bold text-primary mb-4">Ringkasan Pemasukan per Bulan</h3>
                <div class="h-48 flex items-end gap-2 px-4">
                    <?php foreach($income as $val): ?>
                    <div class="flex-1 bg-primary/20 rounded-t transition-all hover:bg-primary/40" style="height: <?php echo $val; ?>%"></div>
                    <?php endforeach; ?>
                </div>
                <div class="flex justify-between mt-4 px-4 text-[10px] font-bold text-outline uppercase">
                    <?php foreach($months as $m): ?>
                    <span><?php echo $m; ?></span>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                <h3 class="font-bold text-primary mb-4">Persentase Pelunasan SPP</h3>
                <div class="flex flex-col items-center justify-center h-48">
                    <div class="w-32 h-32 rounded-full border-[12px] border-primary flex items-center justify-center">
                        <span class="text-2xl font-bold text-primary">85%</span>
                    </div>
                    <p class="mt-4 text-xs font-bold text-outline">124 dari 145 Santri</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
