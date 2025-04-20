<?php
// Memulai session untuk manajemen login user
session_start();
// Mengimpor konfigurasi database dari file config/database.php
require_once 'config/database.php';

// Cek apakah user sudah login dengan memeriksa session user_id
// Jika belum login, redirect ke halaman login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Proses tambah data ketika form disubmit dengan memeriksa POST submit
if (isset($_POST['submit'])) {
    // Mengambil data dari form yang disubmit
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $prioritas_id = $_POST['prioritas_id'];
    $user_id = $_SESSION['user_id'];
    
    // Cek apakah tanggal selesai sudah lewat dari tanggal sekarang
    // Jika lewat dan status bukan sudah selesai, ubah status jadi terlambat
    $current_date = date('Y-m-d');
    if ($tanggal_selesai < $current_date && $status != 'sudah selesai') {
        $status = 'terlambat mengerjakan';
    }

    // Menyiapkan dan mengeksekusi query INSERT dengan prepared statement
    // untuk mencegah SQL injection
    $stmt = $conn->prepare("INSERT INTO todolist (judul, deskripsi, status, tanggal_mulai, tanggal_selesai, prioritas_id, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssii", $judul, $deskripsi, $status, $tanggal_mulai, $tanggal_selesai, $prioritas_id, $user_id);
    $stmt->execute();
    
    // Redirect ke halaman dashboard.php setelah berhasil menambah data
    header("Location: dashboard.php");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tasklify - Tambah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Import CSS Bootstrap dan Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }

        /* CSS untuk mengatur background image dan overlay */
        body {
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Overlay hitam transparan di atas background */
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
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
        }

        .main-content {
            flex: 1 0 auto;
            position: relative;
            z-index: 1;
            padding-bottom: 2rem; /* Menambahkan padding bawah */
        }

        /* Mengatur posisi container di atas overlay */
        .container {
            position: relative;
            z-index: 1;
        }

        /* Styling untuk card */
        .card {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 15px;
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

        /* Styling khusus untuk tombol primary */
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
        }
    </style>
</head>
<body>
    <!-- Navbar dengan logo dan tombol logout -->
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

    <!-- Form untuk menambah data todo baru -->
    <div class="main-content">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title mb-4">Tambah Task Baru</h3>
                    <form method="POST">
                        <!-- Input judul task -->
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" name="judul" class="form-control" required>
                        </div>
                        <!-- Input deskripsi task -->
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" required rows="3"></textarea>
                        </div>
                        <!-- Input tanggal mulai task -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" required>
                        </div>
                        <!-- Input tanggal selesai task -->
                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" required>
                        </div>
                        <!-- Dropdown untuk memilih status task -->
                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="belum selesai">Belum Selesai</option>
                                <option value="sedang dikerjakan">Sedang Dikerjakan</option>
                                <option value="sudah selesai">Sudah Selesai</option>
                            </select>
                        </div>
                        <!-- Dropdown untuk memilih prioritas task -->
                        <div class="mb-3">
                            <label class="form-label">Prioritas</label>
                            <select name="prioritas_id" class="form-select" required>
                                <?php
                                // Mengambil dan menampilkan data prioritas dari database
                                $query_prioritas = mysqli_query($conn, "SELECT * FROM prioritas");
                                while($prioritas = mysqli_fetch_array($query_prioritas)) {
                                    echo "<option value='".$prioritas['id']."'>".$prioritas['nama']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <!-- Tombol aksi untuk submit dan kembali -->
                        <div class="d-flex gap-2">
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Simpan
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

    <!-- Import Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script JavaScript untuk validasi tanggal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set tanggal minimal untuk input tanggal mulai dan selesai ke hari ini
            const today = new Date().toISOString().split('T')[0];
            document.querySelector('input[name="tanggal_mulai"]').min = today;
            document.querySelector('input[name="tanggal_selesai"]').min = today;
            
            // Validasi tanggal selesai harus lebih besar atau sama dengan tanggal mulai
            const tanggalMulaiInput = document.querySelector('input[name="tanggal_mulai"]');
            const tanggalSelesaiInput = document.querySelector('input[name="tanggal_selesai"]');
            
            tanggalMulaiInput.addEventListener('change', function() {
                tanggalSelesaiInput.min = this.value;
                if (tanggalSelesaiInput.value < this.value) {
                    tanggalSelesaiInput.value = this.value;
                }
            });
            
            // Menampilkan peringatan jika tanggal selesai sudah lewat
            document.querySelector('form').addEventListener('submit', function(e) {
                const tanggalSelesai = new Date(tanggalSelesaiInput.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (tanggalSelesai < today) {
                    alert('Perhatian: Tanggal selesai yang Anda pilih sudah lewat. Status akan otomatis diubah menjadi "Terlambat Mengerjakan".');
                }
            });
        });
    </script>
</body>
</html> 