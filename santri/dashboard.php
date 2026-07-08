<?php
require_once '../includes/auth.php';
checkSantri();
require_once '../includes/db.php';

$current_page = 'dashboard';

// Fetch santri data
$stmt = $conn->prepare("SELECT s.*, k.nama as nama_kelas FROM santri s LEFT JOIN kelas k ON s.kelas_id = k.id WHERE s.user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$santri = $stmt->get_result()->fetch_assoc();

// Fetch today's schedule
$today = date('l');
// Translate day to Indonesian
$days = [
    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'
];
$today_id = $days[$today];

$jadwal_res = [];
if ($santri['kelas_id']) {
    $stmt = $conn->prepare("SELECT * FROM jadwal WHERE kelas_id = ? AND hari = ?");
    $stmt->bind_param("is", $santri['kelas_id'], $today_id);
    $stmt->execute();
    $jadwal_res = $stmt->get_result();
}

// Fetch latest announcement
$pengumuman = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal DESC LIMIT 1")->fetch_assoc();

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Dashboard Santri - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_santri.php'; ?>

    <main class="flex-1 md:ml-64  p-8 flex flex-col">
        <header class="flex justify-between items-center mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-primary/10 overflow-hidden border-2 border-primary/20">
                    <img class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBjKOlp2lm_KSr_S1QsZjqS3K6LTnKQGLLuO_khYgULAnwgatVvScM7CzDhP7TnlXA-Q6YP0TR_uBLuncGa1b3AgJpIFyDVSZEX6m8an3fa3ILB4uv8spUZnlX_uVsEUGHf_AhV5EHwoMCrGvx-pOLFXKK88-yv1l3xlCQAm7zmVGn9A9YeaPb7wvYt6AZLVvEu41I_f6Ya9eU1mQ9_xEve3uIkQlDM8c4X6e2B20MsNHcnQdPmWl4ghY6PpUjXmykbkSpi1rNUC2sd"/>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-primary">Assalamu'alaikum, <?php echo $santri['nama']; ?>!</h2>
                    <p class="text-outline text-sm"><?php echo date('l, d F Y'); ?></p>
                </div>
            </div>
        </header>

        <section class="relative rounded-2xl overflow-hidden bg-primary p-8 mb-8 text-white islamic-pattern shadow-lg">
            <div class="relative z-10">
                <h3 class="text-xl font-bold mb-2">Muroja'ah Hari Ini</h3>
                <p class="opacity-80 max-w-lg mb-6">"Sebaik-baik kalian adalah yang mempelajari Al-Qur'an dan mengajarkannya." (HR. Bukhari)</p>
                <div class="flex gap-4">
                    <div class="bg-white/20 backdrop-blur-md p-4 rounded-xl">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Kelas</p>
                        <p class="font-bold"><?php echo $santri['nama_kelas'] ?: 'Belum ada kelas'; ?></p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-md p-4 rounded-xl">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mb-1">Status</p>
                        <p class="font-bold text-secondary-container uppercase"><?php echo $santri['status']; ?></p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                    <h4 class="font-bold text-primary mb-4">Jadwal Hari Ini (<?php echo $today_id; ?>)</h4>
                    <div class="space-y-4">
                        <?php if ($jadwal_res && $jadwal_res->num_rows > 0): ?>
                            <?php while($j = $jadwal_res->fetch_assoc()): ?>
                            <div class="flex items-center gap-4 p-4 bg-background rounded-xl border border-outline/5">
                                <div class="w-16 h-12 rounded-lg bg-primary/10 flex flex-col items-center justify-center text-primary">
                                    <span class="text-xs font-bold"><?php echo date('H:i', strtotime($j['jam_mulai'])); ?></span>
                                    <span class="text-[8px] uppercase">WIB</span>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-primary"><?php echo $j['materi']; ?></p>
                                    <p class="text-xs text-outline">Ruang Belajar</p>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-sm text-outline italic">Tidak ada jadwal untuk hari ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="space-y-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                    <h4 class="font-bold text-primary mb-4">Pengumuman</h4>
                    <?php if($pengumuman): ?>
                    <div class="p-4 bg-secondary/5 border border-secondary/20 rounded-xl">
                        <p class="text-xs font-bold text-secondary uppercase mb-1"><?php echo $pengumuman['kategori']; ?></p>
                        <p class="text-sm font-bold text-primary mb-1"><?php echo $pengumuman['judul']; ?></p>
                        <p class="text-xs text-outline leading-relaxed line-clamp-3"><?php echo $pengumuman['konten']; ?></p>
                    </div>
                    <?php else: ?>
                        <p class="text-sm text-outline italic">Belum ada pengumuman.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
