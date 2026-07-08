<?php
require_once '../includes/auth.php';
checkSantri();
require_once '../includes/db.php';

$current_page = 'pengumuman';

// Fetch pengumuman
$result = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal DESC");

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Pengumuman - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_santri.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="mb-8">
            <h2 class="text-3xl font-bold text-primary">Pengumuman</h2>
            <p class="text-outline">Informasi terbaru dari manajemen TPQ.</p>
        </header>

        <div class="space-y-6">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-2 py-1 bg-secondary/10 text-secondary text-[10px] font-bold rounded uppercase"><?php echo $row['kategori']; ?></span>
                    <span class="text-xs text-outline font-bold"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                </div>
                <h3 class="text-xl font-bold text-primary mb-2"><?php echo $row['judul']; ?></h3>
                <p class="text-sm text-outline leading-relaxed mb-4"><?php echo $row['konten']; ?></p>
                <button class="text-primary font-bold text-sm hover:underline">Baca Selengkapnya →</button>
            </div>
            <?php endwhile; ?>
            <?php if ($result->num_rows == 0): ?>
                <div class="bg-white p-12 rounded-2xl text-center border border-dashed border-outline/30">
                    <p class="text-outline">Belum ada pengumuman untuk saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
