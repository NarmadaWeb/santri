<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'nilai';

$success = '';
$error = '';

// Handle Input Nilai
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        $santri_id = $_POST['santri_id'];
        $materi = $_POST['materi'];
        $nilai = $_POST['nilai'];
        $tanggal_input = date('Y-m-d');

        // Simple predikat logic
        $predikat = 'E';
        if ($nilai >= 85) $predikat = 'A';
        elseif ($nilai >= 75) $predikat = 'B';
        elseif ($nilai >= 65) $predikat = 'C';
        elseif ($nilai >= 50) $predikat = 'D';

        $stmt = $conn->prepare("INSERT INTO nilai (santri_id, materi, nilai, predikat, tanggal_input) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isiss", $santri_id, $materi, $nilai, $predikat, $tanggal_input);
        if ($stmt->execute()) {
            $success = 'Nilai berhasil diinput.';
        } else {
            $error = 'Gagal menginput nilai.';
        }
    }
}

// Fetch nilai
$query = "SELECT n.*, s.nama as nama_santri FROM nilai n JOIN santri s ON n.santri_id = s.id ORDER BY n.tanggal_input DESC";
$result = $conn->query($query);

// Fetch santri for dropdown
$santri_res = $conn->query("SELECT id, nama FROM santri WHERE status='aktif'");
$santri_list = [];
while($s = $santri_res->fetch_assoc()) $santri_list[] = $s;

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Data Nilai - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Rekap Nilai Santri</h2>
                <p class="text-outline">Kelola skor hafalan dan bacaan Al-Quran.</p>
            </div>
            <div class="flex gap-3">
                <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow-sm hover:brightness-110 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined">add_circle</span> Input Nilai Baru
                </button>
            </div>
        </header>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-primary text-white text-xs uppercase tracking-wider">
                        <tr>
                            <th class="p-4 px-6">No</th>
                            <th class="p-4">Nama Santri</th>
                            <th class="p-4">Materi</th>
                            <th class="p-4 text-center">Nilai</th>
                            <th class="p-4 text-center">Predikat</th>
                            <th class="p-4 text-center">Tanggal Input</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10 text-sm">
                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-background transition-colors">
                            <td class="p-4 px-6"><?php echo str_pad($no++, 2, '0', STR_PAD_LEFT); ?></td>
                            <td class="p-4 font-bold text-primary"><?php echo $row['nama_santri']; ?></td>
                            <td class="p-4"><?php echo $row['materi']; ?></td>
                            <td class="p-4 text-center font-bold text-lg"><?php echo $row['nilai']; ?></td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-[10px] font-bold uppercase"><?php echo $row['predikat']; ?></span>
                            </td>
                            <td class="p-4 text-center text-outline"><?php echo date('d M Y', strtotime($row['tanggal_input'])); ?></td>
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
            <h3 class="text-xl font-bold text-primary mb-4">Input Nilai Baru</h3>
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
                    <label class="block text-sm font-bold mb-1">Materi</label>
                    <input type="text" name="materi" required class="w-full border rounded-lg p-2 text-sm" placeholder="Contoh: Tahsin Al-Baqarah">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Nilai (0-100)</label>
                    <input type="number" name="nilai" required min="0" max="100" class="w-full border rounded-lg p-2 text-sm">
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
