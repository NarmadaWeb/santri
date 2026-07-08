<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'santri';

$success = '';
$error = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create') {
            $nama = $_POST['nama'];
            $nis = $_POST['nis'];
            $orang_tua = $_POST['orang_tua'];
            $kelas_id = $_POST['kelas_id'] ?: null;

            $stmt = $conn->prepare("INSERT INTO santri (nis, nama, orang_tua, kelas_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nis, $nama, $orang_tua, $kelas_id);
            if ($stmt->execute()) {
                $success = 'Data santri berhasil ditambahkan.';
            } else {
                $error = 'Gagal menambahkan data santri.';
            }
        } elseif ($_POST['action'] == 'update') {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $nis = $_POST['nis'];
            $orang_tua = $_POST['orang_tua'];
            $kelas_id = $_POST['kelas_id'] ?: null;
            $status = $_POST['status'];

            $stmt = $conn->prepare("UPDATE santri SET nis=?, nama=?, orang_tua=?, kelas_id=?, status=? WHERE id=?");
            $stmt->bind_param("sssssi", $nis, $nama, $orang_tua, $kelas_id, $status, $id);
            if ($stmt->execute()) {
                $success = 'Data santri berhasil diperbarui.';
            } else {
                $error = 'Gagal memperbarui data santri.';
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM santri WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Data santri berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus data santri.';
    }
}

// Fetch santri
$query = "SELECT s.*, k.nama as nama_kelas FROM santri s LEFT JOIN kelas k ON s.kelas_id = k.id";
$result = $conn->query($query);

// Fetch classes for dropdown
$kelas_result = $conn->query("SELECT id, nama FROM kelas");
$classes = [];
while($k = $kelas_result->fetch_assoc()) $classes[] = $k;

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Data Santri - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Data Santri</h2>
                <p class="text-outline">Kelola data seluruh santri TPQ Al-Misbahul Qur'an.</p>
            </div>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow-sm hover:brightness-110 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">person_add</span> Tambah Santri
            </button>
        </header>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
            <div class="p-6 border-b border-outline/10 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-4 w-full md:w-auto">
                    <select class="bg-background border-none rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none">
                        <option>Semua Kelas</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo $c['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="relative w-full md:w-64">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-sm">search</span>
                    <input class="w-full pl-10 pr-4 py-2 bg-background border-none rounded-lg text-sm focus:ring-2 focus:ring-primary transition-all" placeholder="Cari nama santri..." type="text"/>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-primary text-white text-sm">
                        <tr>
                            <th class="p-4 px-6 w-16">No</th>
                            <th class="p-4">Nama Santri</th>
                            <th class="p-4">Orang Tua</th>
                            <th class="p-4">Kelas</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10">
                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-background transition-colors">
                            <td class="p-4 px-6"><?php echo str_pad($no++, 2, '0', STR_PAD_LEFT); ?></td>
                            <td class="p-4 font-bold text-primary"><?php echo $row['nama']; ?></td>
                            <td class="p-4 text-sm text-outline"><?php echo $row['orang_tua']; ?></td>
                            <td class="p-4 text-sm"><?php echo $row['nama_kelas'] ?: '-'; ?></td>
                            <td class="p-4"><span class="px-3 py-1 <?php echo $row['status'] == 'aktif' ? 'bg-primary/10 text-primary' : 'bg-red-100 text-red-600'; ?> rounded-full text-[10px] font-bold uppercase"><?php echo $row['status']; ?></span></td>
                            <td class="p-4">
                                <div class="flex justify-center gap-2">
                                    <button onclick="editSantri(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="p-1.5 text-primary hover:bg-primary/10 rounded"><span class="material-symbols-outlined text-lg">edit</span></button>
                                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="p-1.5 text-error hover:bg-error/10 rounded"><span class="material-symbols-outlined text-lg">delete</span></a>
                                </div>
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
            <h3 class="text-xl font-bold text-primary mb-4">Tambah Santri Baru</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create">
                <div>
                    <label class="block text-sm font-bold mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">NIS</label>
                    <input type="text" name="nis" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Orang Tua</label>
                    <input type="text" name="orang_tua" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full border rounded-lg p-2 text-sm">
                        <option value="">Pilih Kelas</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo $c['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
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
            <h3 class="text-xl font-bold text-primary mb-4">Edit Data Santri</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-bold mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit_nama" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">NIS</label>
                    <input type="text" name="nis" id="edit_nis" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Orang Tua</label>
                    <input type="text" name="orang_tua" id="edit_orang_tua" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Kelas</label>
                    <select name="kelas_id" id="edit_kelas_id" class="w-full border rounded-lg p-2 text-sm">
                        <option value="">Pilih Kelas</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo $c['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Status</label>
                    <select name="status" id="edit_status" class="w-full border rounded-lg p-2 text-sm">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="px-4 py-2 text-sm font-bold">Batal</button>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editSantri(santri) {
            document.getElementById('edit_id').value = santri.id;
            document.getElementById('edit_nama').value = santri.nama;
            document.getElementById('edit_nis').value = santri.nis;
            document.getElementById('edit_orang_tua').value = santri.orang_tua;
            document.getElementById('edit_kelas_id').value = santri.kelas_id;
            document.getElementById('edit_status').value = santri.status;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>
</body>
</html>
