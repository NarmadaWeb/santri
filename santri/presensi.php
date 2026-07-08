<?php
require_once '../includes/auth.php';
checkSantri();
require_once '../includes/db.php';

$current_page = 'presensi';

// Fetch santri data
$stmt = $conn->prepare("SELECT id FROM santri WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$santri_id = $stmt->get_result()->fetch_assoc()['id'];

// Fetch attendance stats
$total_sesi = $conn->query("SELECT COUNT(*) as count FROM presensi WHERE santri_id = $santri_id")->fetch_assoc()['count'];
$hadir = $conn->query("SELECT COUNT(*) as count FROM presensi WHERE santri_id = $santri_id AND status = 'hadir'")->fetch_assoc()['count'];
$izin_sakit = $conn->query("SELECT COUNT(*) as count FROM presensi WHERE santri_id = $santri_id AND status IN ('izin', 'sakit')")->fetch_assoc()['count'];
$alpha = $conn->query("SELECT COUNT(*) as count FROM presensi WHERE santri_id = $santri_id AND status = 'alpha'")->fetch_assoc()['count'];

// Fetch history
$stmt = $conn->prepare("SELECT * FROM presensi WHERE santri_id = ? ORDER BY tanggal DESC");
$stmt->bind_param("i", $santri_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Riwayat Kehadiran - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_santri.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="mb-8">
            <h2 class="text-3xl font-bold text-primary">Riwayat Presensi</h2>
            <p class="text-outline">Pantau catatan kehadiran Anda setiap sesi.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-outline/5 text-center">
                <p class="text-[10px] font-bold text-outline uppercase mb-1">Total Sesi</p>
                <h3 class="text-2xl font-bold text-primary"><?php echo $total_sesi; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-outline/5 text-center">
                <p class="text-[10px] font-bold text-outline uppercase mb-1">Hadir</p>
                <h3 class="text-2xl font-bold text-primary"><?php echo $hadir; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-outline/5 text-center">
                <p class="text-[10px] font-bold text-outline uppercase mb-1">Izin/Sakit</p>
                <h3 class="text-2xl font-bold text-secondary"><?php echo $izin_sakit; ?></h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-outline/5 text-center">
                <p class="text-[10px] font-bold text-outline uppercase mb-1">Alpha</p>
                <h3 class="text-2xl font-bold text-error"><?php echo $alpha; ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
            <table class="w-full text-left text-sm">
                <thead class="bg-primary text-white uppercase font-bold text-[10px] tracking-widest">
                    <tr>
                        <th class="p-4 px-6">Tanggal</th>
                        <th class="p-4">Keterangan</th>
                        <th class="p-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline/10">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td class="p-4 px-6"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td class="p-4"><?php echo $row['keterangan'] ?: '-'; ?></td>
                        <td class="p-4 text-center">
                            <span class="px-3 py-1 <?php echo $row['status'] == 'hadir' ? 'bg-primary/10 text-primary' : ($row['status'] == 'alpha' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600'); ?> rounded-full text-[10px] font-bold uppercase"><?php echo $row['status']; ?></span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
