<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'kelas';

$success = '';
$error = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create') {
            $nama = $_POST['nama'];
            $ustadz_id = $_POST['ustadz_id'] ?: null;
            $level = $_POST['level'];
            $kapasitas = $_POST['kapasitas'];

            $stmt = $conn->prepare("INSERT INTO kelas (nama, ustadz_id, level, kapasitas) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sisi", $nama, $ustadz_id, $level, $kapasitas);
            if ($stmt->execute()) {
                $success = 'Data kelas berhasil ditambahkan.';
            } else {
                $error = 'Gagal menambahkan data kelas.';
            }
        } elseif ($_POST['action'] == 'update') {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $ustadz_id = $_POST['ustadz_id'] ?: null;
            $level = $_POST['level'];
            $kapasitas = $_POST['kapasitas'];

            $stmt = $conn->prepare("UPDATE kelas SET nama=?, ustadz_id=?, level=?, kapasitas=? WHERE id=?");
            $stmt->bind_param("sisii", $nama, $ustadz_id, $level, $kapasitas, $id);
            if ($stmt->execute()) {
                $success = 'Data kelas berhasil diperbarui.';
            } else {
                $error = 'Gagal memperbarui data kelas.';
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM kelas WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Data kelas berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus data kelas.';
    }
}

// Fetch kelas with ustadz name and student count
$query = "SELECT k.*, u.nama as nama_ustadz, (SELECT COUNT(*) FROM santri s WHERE s.kelas_id = k.id) as student_count
          FROM kelas k
          LEFT JOIN ustadz u ON k.ustadz_id = u.id";
$result = $conn->query($query);

// Fetch ustadz for dropdown
$ustadz_result = $conn->query("SELECT id, nama FROM ustadz");
$ustadz_list = [];
while($u = $ustadz_result->fetch_assoc()) $ustadz_list[] = $u;

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Data Kelas - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Data Kelas / Halaqah</h2>
                <p class="text-outline">Kelola pembagian kelompok belajar santri.</p>
            </div>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow-sm hover:brightness-110 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">add_circle</span> Tambah Kelas
            </button>
        </header>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="bg-white rounded-xl shadow-sm border border-outline/10 p-6 hover:-translate-y-1 transition-all">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-2 py-1 bg-primary/10 text-primary text-[10px] font-bold rounded uppercase"><?php echo $row['level']; ?></span>
                    <div class="flex gap-2">
                        <button onclick="editKelas(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="text-primary hover:brightness-75"><span class="material-symbols-outlined text-lg">edit</span></button>
                        <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="text-error hover:brightness-75"><span class="material-symbols-outlined text-lg">delete</span></a>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-primary mb-1"><?php echo $row['nama']; ?></h3>
                <p class="text-sm text-outline mb-4"><?php echo $row['nama_ustadz'] ?: 'Belum ada ustadz'; ?></p>
                <div class="flex items-center justify-between pt-4 border-t border-outline/10">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm text-outline">group</span>
                        <span class="text-sm font-semibold"><?php echo $row['student_count']; ?> / <?php echo $row['kapasitas']; ?> Santri</span>
                    </div>
                    <?php if($row['student_count'] >= $row['kapasitas']): ?>
                        <span class="text-[10px] font-bold text-error uppercase">Penuh</span>
                    <?php else: ?>
                        <span class="text-[10px] font-bold text-primary uppercase">Sisa <?php echo $row['kapasitas'] - $row['student_count']; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <!-- Modal Add -->
    <div id="modalAdd" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-primary mb-4">Tambah Kelas Baru</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create">
                <div>
                    <label class="block text-sm font-bold mb-1">Nama Kelas</label>
                    <input type="text" name="nama" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Ustadz</label>
                    <select name="ustadz_id" class="w-full border rounded-lg p-2 text-sm">
                        <option value="">Pilih Ustadz</option>
                        <?php foreach($ustadz_list as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo $u['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Level</label>
                    <input type="text" name="level" placeholder="Contoh: Iqra Level 1" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Kapasitas</label>
                    <input type="number" name="kapasitas" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 text-sm font-bold">Batal</button>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="modalEdit" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-primary mb-4">Edit Data Kelas</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-bold mb-1">Nama Kelas</label>
                    <input type="text" name="nama" id="edit_nama" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Ustadz</label>
                    <select name="ustadz_id" id="edit_ustadz_id" class="w-full border rounded-lg p-2 text-sm">
                        <option value="">Pilih Ustadz</option>
                        <?php foreach($ustadz_list as $u): ?>
                            <option value="<?php echo $u['id']; ?>"><?php echo $u['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Level</label>
                    <input type="text" name="level" id="edit_level" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Kapasitas</label>
                    <input type="number" name="kapasitas" id="edit_kapasitas" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="px-4 py-2 text-sm font-bold">Batal</button>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editKelas(k) {
            document.getElementById('edit_id').value = k.id;
            document.getElementById('edit_nama').value = k.nama;
            document.getElementById('edit_ustadz_id').value = k.ustadz_id;
            document.getElementById('edit_level').value = k.level;
            document.getElementById('edit_kapasitas').value = k.kapasitas;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>
</body>
</html>
