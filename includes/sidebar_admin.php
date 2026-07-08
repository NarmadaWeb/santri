<?php
function isActive($page, $current_page) {
    return $page === $current_page ? 'bg-secondary-container/10 border-l-4 border-secondary font-bold' : 'opacity-80 hover:opacity-100 hover:bg-white/10 rounded-lg transition-all';
}
?>
<aside class="h-full w-64 fixed left-0 top-0 bg-primary shadow-md flex flex-col py-6 z-50 overflow-y-auto custom-scrollbar text-white">
    <div class="px-6 mb-8 text-center">
        <h1 class="font-headline-md text-xl font-bold">Al-Misbahul Qur'an</h1>
        <p class="text-xs opacity-70">Sistem Informasi TPQ</p>
    </div>
    <nav class="flex-1 px-3 space-y-1">
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('dashboard', $current_page); ?>" href="dashboard.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'dashboard' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>dashboard</span>Dashboard
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('santri', $current_page); ?>" href="santri.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'santri' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>group</span>Data Santri
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('ustadz', $current_page); ?>" href="ustadz.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'ustadz' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>record_voice_over</span>Data Ustadz
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('kelas', $current_page); ?>" href="kelas.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'kelas' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>school</span>Data Kelas
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('presensi', $current_page); ?>" href="presensi.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'presensi' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>clipboard_check</span>Presensi
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('nilai', $current_page); ?>" href="nilai.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'nilai' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>grade</span>Data Nilai
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('pembayaran', $current_page); ?>" href="pembayaran.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'pembayaran' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>payments</span>Data Pembayaran
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('laporan', $current_page); ?>" href="laporan-akademik.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'laporan' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>assessment</span>Laporan
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('pengumuman', $current_page); ?>" href="pengumuman.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'pengumuman' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>campaign</span>Pengumuman
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('pengaturan', $current_page); ?>" href="pengaturan.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'pengaturan' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>settings</span>Pengaturan
        </a>
    </nav>
    <div class="mt-auto px-6 pt-4 border-t border-white/10">
        <a class="flex items-center gap-3 px-4 py-2 text-red-300 hover:bg-red-500/10 rounded-lg transition-all" href="../logout.php">
            <span class="material-symbols-outlined">logout</span>Keluar
        </a>
    </div>
</aside>
