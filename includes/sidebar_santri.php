<?php
function isActive($page, $current_page) {
    return $page === $current_page ? 'bg-secondary/20 border-l-4 border-secondary font-bold' : 'rounded-lg transition-all hover:bg-white/10 opacity-80 hover:opacity-100';
}
?>
<aside class="h-full w-64 fixed left-0 top-0 bg-primary shadow-md flex flex-col py-6 z-50 overflow-y-auto text-white">
    <div class="px-6 mb-10">
        <h1 class="font-headline-md text-xl font-bold">Al-Misbahul Qur'an</h1>
        <p class="text-xs opacity-60">Sistem Informasi Santri</p>
    </div>
    <nav class="flex-1 px-3 space-y-1">
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('dashboard', $current_page); ?>" href="dashboard.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'dashboard' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>dashboard</span>Dashboard
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('jadwal', $current_page); ?>" href="jadwal.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'jadwal' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>calendar_month</span>Jadwal Pelajaran
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('rapor', $current_page); ?>" href="rapor.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'rapor' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>grade</span>Rapor Digital
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('presensi', $current_page); ?>" href="presensi.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'presensi' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>clipboard_check</span>Riwayat Presensi
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('pembayaran', $current_page); ?>" href="pembayaran.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'pembayaran' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>payments</span>Riwayat Bayar
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('pengumuman', $current_page); ?>" href="pengumuman.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'pengumuman' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>campaign</span>Pengumuman
        </a>
        <a class="flex items-center gap-3 px-4 py-3 <?php echo isActive('profil', $current_page); ?>" href="profil.php">
            <span class="material-symbols-outlined" <?php echo $current_page === 'profil' ? 'style="font-variation-settings: \'FILL\' 1;"' : ''; ?>>person</span>Profil Saya
        </a>
    </nav>
    <div class="mt-auto px-6 pt-4 border-t border-white/10">
        <a class="flex items-center gap-3 px-4 py-2 text-red-300 hover:bg-red-500/10 rounded-lg transition-all" href="../logout.php">
            <span class="material-symbols-outlined">logout</span>Keluar
        </a>
    </div>
</aside>
