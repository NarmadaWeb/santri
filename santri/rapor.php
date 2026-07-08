<?php
require_once '../includes/auth.php';
checkSantri();
require_once '../includes/db.php';

$current_page = 'rapor';

// Fetch santri data
$stmt = $conn->prepare("SELECT id FROM santri WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$santri_id = $stmt->get_result()->fetch_assoc()['id'];

// Fetch grades
$stmt = $conn->prepare("SELECT * FROM nilai WHERE santri_id = ? ORDER BY tanggal_input DESC");
$stmt->bind_param("i", $santri_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Rapor Digital - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_santri.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Rapor Digital</h2>
                <p class="text-outline">Hasil belajar Anda di TPQ Al-Misbahul Qur'an.</p>
            </div>
            <button class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow-md hover:brightness-110 flex items-center gap-2">
                <span class="material-symbols-outlined">print</span> Cetak Rapor
            </button>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
                <div class="p-6 bg-primary text-white">
                    <h3 class="font-bold">Detail Nilai Akademik</h3>
                </div>
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-background font-bold text-[10px] uppercase tracking-widest text-outline">
                            <th class="p-4 px-6">Mata Pelajaran / Materi</th>
                            <th class="p-4 text-center">Nilai</th>
                            <th class="p-4 text-center">Predikat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td class="p-4 px-6 font-semibold"><?php echo $row['materi']; ?></td>
                            <td class="p-4 text-center font-bold text-lg text-primary"><?php echo $row['nilai']; ?></td>
                            <td class="p-4 text-center"><span class="px-2 py-1 bg-primary/10 text-primary rounded text-xs font-bold"><?php echo $row['predikat']; ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="space-y-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10 text-center">
                    <h4 class="font-bold text-primary mb-4">Progres Hafalan</h4>
                    <div class="flex flex-col items-center py-4">
                        <div class="w-24 h-24 rounded-full border-[8px] border-primary flex items-center justify-center">
                            <span class="text-xl font-bold text-primary">85%</span>
                        </div>
                        <p class="mt-4 text-xs font-bold text-outline">Juz 30 (On Progress)</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
