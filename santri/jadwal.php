<?php
require_once '../includes/auth.php';
checkSantri();
require_once '../includes/db.php';

$current_page = 'jadwal';

// Fetch santri data
$stmt = $conn->prepare("SELECT kelas_id FROM santri WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$santri = $stmt->get_result()->fetch_assoc();

$days_order = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
$jadwal = [];

if ($santri['kelas_id']) {
    $stmt = $conn->prepare("SELECT * FROM jadwal WHERE kelas_id = ?");
    $stmt->bind_param("i", $santri['kelas_id']);
    $stmt->execute();
    $res = $stmt->get_result();
    while($j = $res->fetch_assoc()) {
        $jadwal[$j['hari']][] = $j;
    }
}

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Jadwal Pelajaran - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_santri.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="mb-8">
            <h2 class="text-3xl font-bold text-primary">Jadwal Pelajaran</h2>
            <p class="text-outline">Rencana aktivitas belajar Anda pekan ini.</p>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($days_order as $hari): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-outline/10 overflow-hidden">
                <div class="bg-primary p-4 text-white font-bold text-center"><?php echo $hari; ?></div>
                <div class="p-6 space-y-4">
                    <?php if(isset($jadwal[$hari])): ?>
                        <?php foreach($jadwal[$hari] as $j): ?>
                        <div class="p-4 bg-background rounded-xl border border-outline/5">
                            <p class="text-[10px] font-bold text-secondary uppercase mb-1"><?php echo date('H:i', strtotime($j['jam_mulai'])); ?> - <?php echo date('H:i', strtotime($j['jam_selesai'])); ?></p>
                            <p class="font-bold text-primary"><?php echo $j['materi']; ?></p>
                            <p class="text-xs text-outline">Ruang Belajar</p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-sm text-outline italic text-center py-4">Tidak ada jadwal.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </main>
</body>
</html>
