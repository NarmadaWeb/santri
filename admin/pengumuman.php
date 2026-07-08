<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'pengumuman';

$success = '';
$error = '';

// Handle CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        $judul = $_POST['judul'];
        $konten = $_POST['konten'];
        $kategori = $_POST['kategori'];
        $tanggal = date('Y-m-d');

        $stmt = $conn->prepare("INSERT INTO pengumuman (judul, konten, kategori, tanggal) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $judul, $konten, $kategori, $tanggal);
        if ($stmt->execute()) {
            $success = 'Pengumuman berhasil dibuat.';
        } else {
            $error = 'Gagal membuat pengumuman.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM pengumuman WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $success = 'Pengumuman berhasil dihapus.';
    } else {
        $error = 'Gagal menghapus pengumuman.';
    }
}

// Fetch pengumuman
$result = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal DESC");

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Manajemen Pengumuman - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Manajemen Pengumuman</h2>
                <p class="text-outline">Kirim informasi penting ke santri dan orang tua.</p>
            </div>
            <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="bg-primary text-white px-6 py-2 rounded-lg font-bold shadow-sm hover:brightness-110 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">add</span> Buat Pengumuman
            </button>
        </header>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="space-y-6">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-outline/10">
                <div class="flex justify-between items-start mb-4">
                    <span class="px-2 py-1 bg-secondary/10 text-secondary text-[10px] font-bold rounded uppercase"><?php echo $row['kategori']; ?></span>
                    <span class="text-xs text-outline font-bold"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></span>
                </div>
                <h3 class="text-lg font-bold text-primary mb-2"><?php echo $row['judul']; ?></h3>
                <p class="text-sm text-outline mb-4"><?php echo $row['konten']; ?></p>
                <div class="flex justify-end gap-2">
                    <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="p-1.5 text-error hover:bg-error/10 rounded"><span class="material-symbols-outlined text-lg">delete</span></a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <!-- Modal Add -->
    <div id="modalAdd" class="hidden fixed inset-0 bg-black/50 z-[60] flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-primary mb-4">Buat Pengumuman Baru</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="create">
                <div>
                    <label class="block text-sm font-bold mb-1">Judul</label>
                    <input type="text" name="judul" required class="w-full border rounded-lg p-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Kategori</label>
                    <select name="kategori" class="w-full border rounded-lg p-2 text-sm">
                        <option value="Akademik">Akademik</option>
                        <option value="Kegiatan">Kegiatan</option>
                        <option value="Penting">Penting</option>
                        <option value="Umum">Umum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-bold mb-1">Konten</label>
                    <textarea name="konten" required class="w-full border rounded-lg p-2 text-sm" rows="5"></textarea>
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
