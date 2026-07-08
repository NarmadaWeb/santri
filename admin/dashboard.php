<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'dashboard';

// Fetch stats
$total_santri = $conn->query("SELECT COUNT(*) as count FROM santri")->fetch_assoc()['count'];
$total_ustadz = $conn->query("SELECT COUNT(*) as count FROM ustadz")->fetch_assoc()['count'];
$total_kelas = $conn->query("SELECT COUNT(*) as count FROM kelas")->fetch_assoc()['count'];
// Simple placeholder for payment percentage
$pembayaran_pct = "86%";

// Fetch recent activity (e.g., recent grades or santri)
$recent_activities = $conn->query("
    (SELECT nama as name, 'Pendaftaran Baru' as action, 'Santri' as type, id FROM santri ORDER BY id DESC LIMIT 3)
    UNION
    (SELECT materi as name, 'Input Nilai' as action, 'Nilai' as type, id FROM nilai ORDER BY id DESC LIMIT 3)
    LIMIT 5
");

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Dashboard Admin - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8 min-h-screen relative">
        <div class="absolute inset-0 islamic-pattern pointer-events-none"></div>

        <header class="flex justify-between items-center mb-8 relative z-10">
            <div>
                <h2 class="text-3xl font-bold text-primary">Assalamu'alaikum, <?php echo $_SESSION['username']; ?></h2>
                <p class="text-outline">Selamat datang kembali di panel manajemen Al-Misbahul Qur'an.</p>
            </div>
            <div class="flex items-center gap-4 bg-white p-2 px-4 rounded-full shadow-sm border border-outline/10">
                <span class="font-medium"><?php echo date('l, d F Y'); ?></span>
                <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center text-white font-bold">A</div>
            </div>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 relative z-10">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-primary hover:-translate-y-1 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <span class="p-2 bg-primary/10 rounded-lg text-primary material-symbols-outlined">groups</span>
                </div>
                <p class="text-sm text-outline font-bold uppercase tracking-wider">Total Santri</p>
                <h3 class="text-3xl font-bold text-on-surface"><?php echo $total_santri; ?></h3>
                <p class="text-xs text-green-600 mt-2 flex items-center gap-1"><span class="material-symbols-outlined text-xs">trending_up</span> Aktif</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-secondary hover:-translate-y-1 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <span class="p-2 bg-secondary/10 rounded-lg text-secondary material-symbols-outlined">record_voice_over</span>
                </div>
                <p class="text-sm text-outline font-bold uppercase tracking-wider">Total Ustadz</p>
                <h3 class="text-3xl font-bold text-on-surface"><?php echo $total_ustadz; ?></h3>
                <p class="text-xs text-outline mt-2">Aktif mengajar</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-tertiary hover:-translate-y-1 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <span class="p-2 bg-tertiary/10 rounded-lg text-tertiary material-symbols-outlined">school</span>
                </div>
                <p class="text-sm text-outline font-bold uppercase tracking-wider">Total Kelas</p>
                <h3 class="text-3xl font-bold text-on-surface"><?php echo $total_kelas; ?></h3>
                <p class="text-xs text-outline mt-2">Kelompok belajar</p>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-error hover:-translate-y-1 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <span class="p-2 bg-error/10 rounded-lg text-error material-symbols-outlined">account_balance_wallet</span>
                </div>
                <p class="text-sm text-outline font-bold uppercase tracking-wider">Pembayaran</p>
                <h3 class="text-3xl font-bold text-on-surface"><?php echo $pembayaran_pct; ?></h3>
                <p class="text-xs text-outline mt-2">Bulan berjalan</p>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 relative z-10">
            <section class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
                <div class="p-6 border-b border-outline/10 flex justify-between items-center">
                    <h3 class="text-xl font-bold text-primary">Aktivitas Terbaru</h3>
                    <button class="text-secondary font-bold text-sm hover:underline">Lihat Semua</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-primary text-white text-sm">
                            <tr>
                                <th class="p-4 px-6">Nama / Materi</th>
                                <th class="p-4">Kegiatan</th>
                                <th class="p-4">Tipe</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-outline/10">
                            <?php while($act = $recent_activities->fetch_assoc()): ?>
                            <tr class="hover:bg-background transition-colors">
                                <td class="p-4 px-6 font-bold"><?php echo $act['name']; ?></td>
                                <td class="p-4 text-sm"><?php echo $act['action']; ?></td>
                                <td class="p-4 text-xs text-outline"><?php echo $act['type']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                <h3 class="text-xl font-bold text-primary mb-6">Agenda Mendatang</h3>
                <div class="space-y-4">
                    <div class="flex gap-4 p-4 rounded-xl bg-background border-l-4 border-secondary">
                        <div class="text-center min-w-[48px]">
                            <p class="text-xs uppercase text-outline"><?php echo date('M'); ?></p>
                            <p class="text-xl font-bold text-primary"><?php echo date('d', strtotime('+2 days')); ?></p>
                        </div>
                        <div>
                            <h4 class="font-bold">Ujian Tahfidz Semester</h4>
                            <p class="text-xs text-outline flex items-center gap-1"><span class="material-symbols-outlined text-xs">schedule</span> 08:00 - Selesai</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
