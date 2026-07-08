<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'ustadz';

$success = '';
$error = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create') {
            $nama = $_POST['nama'];
            $bidang = $_POST['bidang'];
            $no_hp = $_POST['no_hp'];

            $stmt = $conn->prepare("INSERT INTO ustadz (nama, bidang, no_hp) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nama, $bidang, $no_hp);
            if ($stmt->execute()) {
                $success = 'Data ustadz berhasil ditambahkan.';
            } else {
                $error = 'Gagal menambahkan data ustadz.';
            }
        } elseif ($_POST['action'] == 'update') {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $bidang = $_POST['bidang'];
            $no_hp = $_POST['no_hp'];

            $stmt = $conn->prepare("UPDATE ustadz SET nama=?, bidang=?, no_hp=? WHERE id=?");
            $stmt->bind_param("sssi", $nama, $bidang, $no_hp, $id);
            if ($stmt->execute()) {
                $success = 'Data ustadz berhasil diperbarui.';
            } else {
                $error = 'Gagal memperbarui data ustadz.';
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM ustadz WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Data ustadz berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus data ustadz.';
    }
}

// Fetch ustadz
$result = $conn->query("SELECT * FROM ustadz");

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Data Ustadz - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Data Ustadz</h2>
                <p class="text-outline">Kelola data tenaga pengajar TPQ Al-Misbahul Qur'an.</p>
            </div>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow-sm hover:brightness-110 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">person_add</span> Tambah Ustadz
            </button>
        </header>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $success; ?></div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-primary text-white text-sm">
                        <tr>
                            <th class="p-4 px-6 w-16">No</th>
                            <th class="p-4">Nama Ustadz</th>
                            <th class="p-4">Bidang</th>
                            <th class="p-4">No. HP</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10">
                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-background transition-colors">
                            <td class="p-4 px-6"><?php echo str_pad($no++, 2, '0', STR_PAD_LEFT); ?></td>
                            <td class="p-4 font-bold"><?php echo $row['nama']; ?></td>
                            <td class="p-4 text-sm"><span class="px-3 py-1 bg-primary/10 text-primary rounded-full text-xs font-semibold"><?php echo $row['bidang']; ?></span></td>
                            <td class="p-4 text-sm"><?php echo $row['no_hp']; ?></td>
                            <td class="p-4">
                                <div class="flex justify-center gap-2">
                                    <button onclick="editUstadz(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="p-1.5 text-primary hover:bg-primary/10 rounded"><span class="material-symbols-outlined text-lg">edit</span></button>
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
            <h3 class="text-xl font-bold text-primary mb-4">Tambah Ustadz Baru</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create">
                <div>
                    <label class="block text-sm font-bold mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Bidang</label>
                    <input type="text" name="bidang" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">No. HP</label>
                    <input type="text" name="no_hp" class="w-full border rounded-lg p-2 text-sm">
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
            <h3 class="text-xl font-bold text-primary mb-4">Edit Data Ustadz</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <div>
                    <label class="block text-sm font-bold mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" id="edit_nama" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Bidang</label>
                    <input type="text" name="bidang" id="edit_bidang" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">No. HP</label>
                    <input type="text" name="no_hp" id="edit_no_hp" class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="px-4 py-2 text-sm font-bold">Batal</button>
                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function editUstadz(u) {
            document.getElementById('edit_id').value = u.id;
            document.getElementById('edit_nama').value = u.nama;
            document.getElementById('edit_bidang').value = u.bidang;
            document.getElementById('edit_no_hp').value = u.no_hp;
            document.getElementById('modalEdit').classList.remove('hidden');
        }
    </script>
</body>
</html>
