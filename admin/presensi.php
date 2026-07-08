<?php
require_once '../includes/auth.php';
checkAdmin();
require_once '../includes/db.php';

$current_page = 'presensi';

$success = '';
$error = '';

$kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Handle Save Presensi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_presensi'])) {
    $att = $_POST['att']; // Array of santri_id => status
    $conn->begin_transaction();
    try {
        foreach ($att as $santri_id => $status) {
            // Check if exists for today
            $check = $conn->prepare("SELECT id FROM presensi WHERE santri_id = ? AND tanggal = ?");
            $check->bind_param("is", $santri_id, $tanggal);
            $check->execute();
            $res = $check->get_result();
            if ($res->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE presensi SET status = ? WHERE santri_id = ? AND tanggal = ?");
                $stmt->bind_param("sis", $status, $santri_id, $tanggal);
            } else {
                $stmt = $conn->prepare("INSERT INTO presensi (santri_id, tanggal, status) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $santri_id, $tanggal, $status);
            }
            $stmt->execute();
        }
        $conn->commit();
        $success = 'Presensi berhasil disimpan.';
    } catch (Exception $e) {
        $conn->rollback();
        $error = 'Gagal menyimpan presensi.';
    }
}

// Fetch classes for dropdown
$kelas_result = $conn->query("SELECT id, nama FROM kelas");
$classes = [];
while($k = $kelas_result->fetch_assoc()) $classes[] = $k;

// Fetch santri if kelas is selected
$santri_list = [];
if ($kelas_id) {
    $stmt = $conn->prepare("
        SELECT s.id, s.nama, p.status
        FROM santri s
        LEFT JOIN presensi p ON s.id = p.santri_id AND p.tanggal = ?
        WHERE s.kelas_id = ?
    ");
    $stmt->bind_param("si", $tanggal, $kelas_id);
    $stmt->execute();
    $santri_list = $stmt->get_result();
}

?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <title>Presensi Santri - TPQ Al-Misbahul Qur'an</title>
    <?php include '../includes/header.php'; ?>
    <style>
        .status-radio:checked + label { border-color: currentColor; background-color: currentColor; color: white; }
    </style>
</head>
<body class="bg-background font-body-md text-on-surface min-h-screen flex">
    <?php include '../includes/sidebar_admin.php'; ?>

    <main class="flex-1 md:ml-64  p-8">
        <header class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-bold text-primary">Presensi Santri</h2>
                <p class="text-outline">Catat kehadiran harian santri per halaqah.</p>
            </div>
            <form method="GET" class="flex flex-wrap gap-4">
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-outline uppercase ml-1">Pilih Kelas</label>
                    <select name="kelas_id" onchange="this.form.submit()" class="bg-white border border-outline/10 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none">
                        <option value="">Pilih Kelas</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php echo $kelas_id == $c['id'] ? 'selected' : ''; ?>><?php echo $c['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex flex-col gap-1">
                    <label class="text-[10px] font-bold text-outline uppercase ml-1">Tanggal</label>
                    <input name="tanggal" onchange="this.form.submit()" class="bg-white border border-outline/10 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary outline-none" type="date" value="<?php echo $tanggal; ?>"/>
                </div>
            </form>
        </header>

        <?php if($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"><?php echo $success; ?></div>
        <?php endif; ?>

        <?php if ($kelas_id): ?>
        <form method="POST">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline/10">
                <table class="w-full text-left">
                    <thead class="bg-primary text-white text-sm">
                        <tr>
                            <th class="p-4 px-6 w-16">No</th>
                            <th class="p-4">Nama Santri</th>
                            <th class="p-4 text-center">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-outline/10">
                        <?php $no = 1; while($row = $santri_list->fetch_assoc()): ?>
                        <tr class="hover:bg-background transition-colors">
                            <td class="p-4 px-6"><?php echo str_pad($no++, 2, '0', STR_PAD_LEFT); ?></td>
                            <td class="p-4 font-semibold text-primary"><?php echo $row['nama']; ?></td>
                            <td class="p-4">
                                <div class="flex justify-center gap-4">
                                    <?php
                                    $statuses = [
                                        'hadir' => ['color' => 'text-secondary', 'hover' => 'hover:bg-secondary/10'],
                                        'sakit' => ['color' => 'text-blue-600', 'hover' => 'hover:bg-blue-100'],
                                        'izin'  => ['color' => 'text-amber-600', 'hover' => 'hover:bg-amber-100'],
                                        'alpha' => ['color' => 'text-error', 'hover' => 'hover:bg-error/10']
                                    ];
                                    foreach($statuses as $s => $cfg):
                                    ?>
                                    <div class="relative">
                                        <input class="hidden status-radio" id="<?php echo $s.'_'.$row['id']; ?>" name="att[<?php echo $row['id']; ?>]" type="radio" value="<?php echo $s; ?>" <?php echo $row['status'] == $s ? 'checked' : ''; ?> required/>
                                        <label class="flex items-center justify-center px-4 py-1.5 rounded-full border border-outline/20 text-outline font-bold cursor-pointer text-xs <?php echo $cfg['hover'].' '.$cfg['color']; ?> transition-all" for="<?php echo $s.'_'.$row['id']; ?>"><?php echo ucfirst($s); ?></label>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <div class="p-6 bg-background flex justify-end">
                    <button type="submit" name="save_presensi" class="bg-primary text-white px-8 py-2 rounded-lg font-bold shadow-md hover:brightness-110 active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined">save</span> Simpan Presensi
                    </button>
                </div>
            </div>
        </form>
        <?php else: ?>
            <div class="bg-white p-12 rounded-2xl text-center border border-dashed border-outline/30">
                <p class="text-outline">Silakan pilih kelas terlebih dahulu untuk mencatat presensi.</p>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
