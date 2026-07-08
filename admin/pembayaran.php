<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'pembayaran';

$success = '';
$error = '';

// Handle Transaksi Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        $santri_id = $_POST['santri_id'];
        $tanggal = $_POST['tanggal'];
        $jenis = $_POST['jenis'];
        $jumlah = $_POST['jumlah'];
        $status = 'lunas'; // Admin input usually direct lunas

        $stmt = $conn->prepare("INSERT INTO pembayaran (santri_id, tanggal, jenis, jumlah, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $santri_id, $tanggal, $jenis, $jumlah, $status);
        if ($stmt->execute()) {
            $success = 'Transaksi berhasil ditambahkan.';
        } else {
            $error = 'Gagal menambahkan transaksi.';
        }
    }
}

// Fetch stats
$total_terbayar = $conn->query("SELECT SUM(jumlah) as total FROM pembayaran WHERE status = 'lunas' AND MONTH(tanggal) = MONTH(CURRENT_DATE)")->fetch_assoc()['total'] ?: 0;
$pending_konfirmasi = $conn->query("SELECT SUM(jumlah) as total FROM pembayaran WHERE status = 'pending'")->fetch_assoc()['total'] ?: 0;
$belum_bayar_count = 15; // Simplified

// Fetch transactions
$query = "SELECT p.*, s.nama as nama_santri FROM pembayaran p JOIN santri s ON p.santri_id = s.id ORDER BY p.tanggal DESC";
$result = $conn->query($query);

// Fetch santri for dropdown
$santri_res = $conn->query("SELECT id, nama FROM santri WHERE status='aktif'");
$santri_list = [];
while($s = $santri_res->fetch_assoc()) $santri_list[] = $s;

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Data Pembayaran - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Data Pembayaran</h2>
                <p class="text-outline">Kelola iuran SPP dan infaq santri.</p>
            </div>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow-sm hover:brightness-110 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">add_card</span> Tambah Transaksi
            </button>
        </header>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-primary">
                <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Total Terbayar (Bulan Ini)</p>
                <h3 class="text-2xl font-bold text-primary">Rp <?php echo number_format($total_terbayar, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-secondary">
                <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Pending Konfirmasi</p>
                <h3 class="text-2xl font-bold text-secondary">Rp <?php echo number_format($pending_konfirmasi, 0, ',', '.'); ?></h3>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-error">
                <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Belum Bayar</p>
                <h3 class="text-2xl font-bold text-error"><?php echo $belum_bayar_count; ?> Santri</h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
            <div class="p-6 border-b border-outline/10 flex flex-col md:flex-row justify-between items-center gap-4">
                <h4 class="font-bold text-primary">Riwayat Transaksi Terbaru</h4>
                <div class="relative w-full md:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">search</span>
                    <input class="w-full pl-10 pr-4 py-2 bg-background border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all" placeholder="Cari santri..." type="text"/>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-primary text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="p-4 px-6">ID</th>
                            <th class="p-4">Nama Santri</th>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Jenis</th>
                            <th class="p-4 text-right">Jumlah</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10 text-sm">
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-background transition-colors">
                            <td class="p-4 px-6 font-mono text-outline">#TRX-<?php echo $row['id']; ?></td>
                            <td class="p-4 font-bold text-primary"><?php echo $row['nama_santri']; ?></td>
                            <td class="p-4"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></td>
                            <td class="p-4"><?php echo $row['jenis']; ?></td>
                            <td class="p-4 text-right font-bold">Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
                            <td class="p-4">
                                <span class="px-3 py-1 <?php echo $row['status'] == 'lunas' ? 'bg-primary/10 text-primary' : ($row['status'] == 'pending' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600'); ?> rounded-full text-[10px] font-bold uppercase"><?php echo $row['status']; ?></span>
                            </td>
                            <td class="p-4 text-center">
                                <button class="p-1 text-primary hover:bg-primary/10 rounded"><span class="material-symbols-outlined text-lg">print</span></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Add -->
    <div id="modalAdd" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-primary mb-4">Tambah Transaksi</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create">
                <div>
                    <label class="block text-sm font-bold mb-1">Santri</label>
                    <select name="santri_id" required class="w-full border rounded-lg p-2 text-sm">
                        <option value="">Pilih Santri</option>
                        <?php foreach($santri_list as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo $s['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Tanggal</label>
                    <input type="date" name="tanggal" required class="w-full border rounded-lg p-2 text-sm" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Jenis Pembayaran</label>
                    <input type="text" name="jenis" required class="w-full border rounded-lg p-2 text-sm" placeholder="Contoh: SPP Mei 2024">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 text-sm font-bold">Batal</button>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
