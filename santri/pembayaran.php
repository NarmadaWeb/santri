<?php
require_once '../includes/auth.php';
checkSantri();
require_once '../includes/db.php';

$current_page = 'pembayaran';

// Fetch santri data
$stmt = $conn->prepare("SELECT id FROM santri WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$santri_id = $stmt->get_result()->fetch_assoc()['id'];

// Fetch total paid
$total_paid = $conn->query("SELECT SUM(jumlah) as total FROM pembayaran WHERE santri_id = $santri_id AND status = 'lunas'")->fetch_assoc()['total'] ?: 0;

// Fetch monthly status (mock for simplicity, checking current month)
$current_month_status = $conn->query("SELECT status FROM pembayaran WHERE santri_id = $santri_id AND MONTH(tanggal) = MONTH(CURRENT_DATE) AND YEAR(tanggal) = YEAR(CURRENT_DATE)")->fetch_assoc()['status'] ?: 'Belum Bayar';

// Fetch history
$stmt = $conn->prepare("SELECT * FROM pembayaran WHERE santri_id = ? ORDER BY tanggal DESC");
$stmt->bind_param("i", $santri_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Riwayat Pembayaran - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_santri.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="mb-8">
            <h2 class="text-3xl font-bold text-primary">Riwayat Pembayaran</h2>
            <p class="text-outline">Pantau status iuran SPP Anda.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-primary">
                <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Total Terbayar</p>
                <h3 class="text-2xl font-bold text-primary">Rp <?php echo number_format($total_paid, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-secondary">
                <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Status Bulan Ini</p>
                <h3 class="text-2xl font-bold text-secondary uppercase"><?php echo $current_month_status; ?></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
            <table class="w-full text-left text-sm">
                <thead class="bg-primary text-white uppercase font-bold text-[10px] tracking-widest">
                    <tr>
                        <th class="p-4 px-6">Bulan / Jenis</th>
                        <th class="p-4">Tanggal Bayar</th>
                        <th class="p-4 text-right">Jumlah</th>
                        <th class="p-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline/10">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr class="hover:bg-background transition-colors">
                        <td class="p-4 px-6 font-bold text-primary"><?php echo $row['jenis']; ?></td>
                        <td class="p-4"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                        <td class="p-4 text-right font-bold">Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                        <td class="p-4">
                            <div class="flex justify-center">
                                <span class="px-3 py-1 <?php echo $row['status'] == 'lunas' ? 'bg-primary/10 text-primary' : ($row['status'] == 'pending' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600'); ?> rounded-full text-[10px] font-bold uppercase"><?php echo $row['status']; ?></span>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
