<?php
// Memulai session untuk manajemen login user - digunakan untuk menyimpan data login user
session_start();
// Mengimpor konfigurasi database - menghubungkan ke database MySQL
require_once 'config/database.php';

// Cek apakah user sudah login, jika belum redirect ke halaman login
// Memeriksa session user_id, jika tidak ada maka user belum login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Cek apakah ada parameter id di URL, jika tidak redirect ke halaman utama
// Memastikan ada ID task yang akan diedit
if (!isset($_GET['id'])) {
    header("Location: dashboard.php"); 
    exit;
}

// Mengambil data todo berdasarkan id
// Query untuk mendapatkan detail task dan nama prioritasnya
$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT t.*, p.nama as prioritas_nama 
                             FROM todolist t 
                             LEFT JOIN prioritas p ON t.prioritas_id = p.id 
                             WHERE t.id=$id");
$data = mysqli_fetch_array($query);

// Proses update data ketika form disubmit
// Mengupdate data task ketika form dikirim
if (isset($_POST['edit'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi']; 
    $tgl_mulai = $_POST['tgl_mulai'];
    $tgl_selesai = $_POST['tgl_selesai'];
    $status = $_POST['status'];
    $prioritas_id = $_POST['prioritas_id'];
    
    // Query untuk update data ke database menggunakan prepared statement
    // Menggunakan prepared statement untuk mencegah SQL injection
    $stmt = $conn->prepare("UPDATE todolist SET 
                           judul=?, 
                           deskripsi=?,
                           status=?, 
                           tanggal_mulai=?, 
                           tanggal_selesai=?,
                           prioritas_id=? 
                           WHERE id=?");
    $stmt->bind_param("sssssii", $judul, $deskripsi, $status, $tgl_mulai, $tgl_selesai, $prioritas_id, $id);
    
    if($stmt->execute()) {
        header("Location: dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tasklify - Edit</title>
    <!-- Meta viewport untuk responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Import Bootstrap CSS dan Icons - framework CSS untuk styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        body {
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Overlay gelap pada background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }

        /* Styling untuk navbar */
        .navbar {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }

        .main-content {
            flex: 1 0 auto;
            position: relative;
            z-index: 1;
            padding-bottom: 2rem; /* Menambahkan padding bawah */
        }

        /* Mengatur posisi container */
        .container {
            position: relative;
            z-index: 1;
        }

        /* Styling untuk card */
        .card {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(4px);
            margin: 0 10px;
        }

        /* Styling untuk tombol */
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }

        /* Styling untuk tombol primary */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
        }

        /* Styling untuk form input dan select */
        .form-control, .form-select {
            border-radius: 8px;
            padding: 10px 15px;
        }

        footer {
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        }
        
        /* CSS untuk tampilan mobile */
        @media (max-width: 768px) {
            .navbar-brand img {
                height: 40px;
            }
            
            .navbar .container {
                padding: 8px 15px;
            }
            
            .card {
                margin-top: 10px;
                margin-bottom: 20px;
            }
            
            .form-control, .form-select {
                font-size: 16px; /* Mencegah zoom pada iOS */
            }
            
            .d-flex.gap-2 {
                flex-direction: column;
                gap: 10px !important;
            }
            
            .d-flex.gap-2 .btn {
                width: 100%;
            }
            
            .text-white.me-3 {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar dengan informasi user yang sedang login -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="img/logo.png" alt="Logo" height="50">
            </a>
            <div class="d-flex align-items-center">
                <a href="logout.php" class="btn btn-light">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Form untuk mengedit data todo -->
    <div class="main-content">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Edit Task</h3>
                    

                    <form method="POST">
                        <!-- Input judul - untuk mengubah judul task -->
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="judul" class="form-control" value="<?php echo $data['judul']; ?>" required>
                        </div>
                        <!-- Input deskripsi - untuk mengubah deskripsi task -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" required rows="3"><?php echo $data['deskripsi']; ?></textarea>
                        </div>
                        <!-- Input tanggal mulai - untuk mengubah tanggal mulai task -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tgl_mulai" class="form-control" value="<?php echo $data['tanggal_mulai']; ?>" required>
                        </div>
                        <!-- Input tanggal selesai - untuk mengubah tanggal selesai task -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tgl_selesai" class="form-control" value="<?php echo $data['tanggal_selesai']; ?>" required>
                        </div>
                        <!-- Pilihan status - untuk mengubah status task -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="belum selesai" <?php echo ($data['status'] == 'belum selesai') ? 'selected' : ''; ?>>Belum Selesai</option>
                                <option value="sedang dikerjakan" <?php echo ($data['status'] == 'sedang dikerjakan') ? 'selected' : ''; ?>>Sedang Dikerjakan</option>
                                <option value="sudah selesai" <?php echo ($data['status'] == 'sudah selesai') ? 'selected' : ''; ?>>Sudah Selesai</option>
                            </select>
                        </div>
                        
                        <!-- Pilihan prioritas - untuk mengubah prioritas task -->
                        <div class="mb-4">
                            <label class="form-label">Prioritas</label>
                            <select name="prioritas_id" class="form-select" required>
                                <?php
                                // Query untuk mendapatkan daftar prioritas
                                $query_prioritas = mysqli_query($conn, "SELECT * FROM prioritas");
                                while($prioritas = mysqli_fetch_array($query_prioritas)) {
                                    $selected = ($data['prioritas_id'] == $prioritas['id']) ? 'selected' : '';
                                    echo "<option value='".$prioritas['id']."' ".$selected.">".$prioritas['nama']."</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Tombol aksi - untuk menyimpan perubahan atau kembali -->
                        <div class="d-flex gap-2">
                            <button type="submit" name="edit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update
                            </button>
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-3">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0 text-white">&copy; <?php echo date('Y'); ?> Tasklify. Semua hak dilindungi.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Import Bootstrap JS - untuk fungsionalitas komponen Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script untuk update tanggal dan waktu secara realtime -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update tanggal dan waktu setiap detik
            function updateDateTime() {
                const now = new Date();
                const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
                const dateStr = now.toLocaleDateString('id-ID', options);
                const timeStr = now.toLocaleTimeString('id-ID');
                
                document.getElementById('current-date').textContent = dateStr;
                document.getElementById('current-time').textContent = timeStr;
            }
            
            updateDateTime();
            setInterval(updateDateTime, 1000);

            // Validasi tanggal
            const startDateInput = document.querySelector('input[name="tgl_mulai"]');
            const endDateInput = document.querySelector('input[name="tgl_selesai"]');
            
            // Set minimum date ke hari ini
            const today = new Date().toISOString().split('T')[0];
            startDateInput.min = today;
            endDateInput.min = today;

            // Validasi saat memilih tanggal mulai
            startDateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const currentDate = new Date();
                currentDate.setHours(0, 0, 0, 0);

                if (selectedDate < currentDate) {
                    alert('Tanggal mulai tidak boleh lebih awal dari hari ini!');
                    this.value = today;
                    return;
                }

                // Update minimum tanggal selesai
                endDateInput.min = this.value;
            });

            // Validasi saat memilih tanggal selesai
            endDateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const startDate = new Date(startDateInput.value);
                
                if (selectedDate < startDate) {
                    alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai!');
                    this.value = startDateInput.value;
                    return;
                }
            });
        });
    </script>
</body>
</html> 