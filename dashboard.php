<?php
session_start(); // Memulai session untuk menyimpan data user yang login
// Include koneksi database
require_once 'config/database.php'; // Mengimpor file konfigurasi database

// Cek login - Memastikan user sudah login sebelum mengakses halaman
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Proses hapus todolist - Menambahkan validasi keamanan
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "DELETE FROM todolist WHERE id=$id AND user_id=$user_id");
}

// Proses edit status - Menambahkan validasi keamanan
if (isset($_GET['selesai'])) {
    $id = (int)$_GET['selesai'];
    $user_id = $_SESSION['user_id'];
    mysqli_query($conn, "UPDATE todolist SET status='selesai' WHERE id=$id AND user_id=$user_id");
}

// Cek dan update status tugas yang sudah lewat tanggalnya
$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');
$update_query = "UPDATE todolist SET status='terlambat mengerjakan' 
                 WHERE user_id=$user_id 
                 AND tanggal_selesai < '$current_date' 
                 AND status != 'selesai'";
mysqli_query($conn, $update_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tasklify</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Import Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Tambahkan CSS kustom -->
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

        /* Menambahkan overlay untuk meningkatkan keterbacaan */
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

        .main-content {
            flex: 1 0 auto;
            position: relative;
            z-index: 1;
        }

        footer {
            flex-shrink: 0;
            position: relative;
            z-index: 1;
            width: 100%;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
        }

        .navbar {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
            z-index: 1;
            padding: 10px;
        }

        .container {
            position: relative;
            z-index: 1;
            padding: 10px;
        }

        .card {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
            backdrop-filter: blur(4px);
            margin-bottom: 20px;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .table tr {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border-radius: 8px;
        }

        .table td, .table th {
            border: none;
            padding: 10px;
            vertical-align: middle;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            margin: 2px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
        }

        .badge {
            padding: 6px 10px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.8rem;
        }

        .search-input {
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1);
            color: white;
        }

        .search-input::placeholder {
            color: rgba(255,255,255,0.7);
        }

        .search-btn {
            border-radius: 8px;
            padding: 8px 12px;
        }

        /* Task card untuk tampilan mobile */
        .task-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .task-card .task-title {
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 8px;
        }
        
        .task-card .task-desc {
            color: #666;
            margin-bottom: 12px;
        }
        
        .task-card .task-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .task-card .action-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        /* Tanggal realtime */
        .current-date {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .current-date i {
            margin-right: 8px;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .navbar-brand img {
                height: 40px;
            }
            
            .search-form {
                width: 100%;
                margin: 10px 0;
            }
            
            .search-input {
                width: 100%;
            }
            
            .btn {
                padding: 6px 12px;
            }
            
            .desktop-table {
                display: none;
            }
            
            .mobile-tasks {
                display: block;
            }
            
            .navbar-collapse .ms-auto {
                flex-direction: column;
                width: 100%;
                gap: 10px !important;
            }
            
            .navbar-collapse form {
                width: 100%;
                margin-bottom: 10px;
            }
        }

        @media (min-width: 769px) {
            .desktop-table {
                display: block;
            }
            
            .mobile-tasks {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navbar untuk navigasi -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="img/logo.png" alt="Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <div class="ms-auto d-flex align-items-center gap-2">
                    <form method="GET" class="d-flex me-2">
                        <input type="text" name="search" class="form-control search-input me-2" placeholder="Cari tugas..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                        <button type="submit" class="btn btn-light search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                    <div class="d-flex align-items-center">
                        <span class="text-white me-3">Hai, <?php echo $_SESSION['username']; ?></span>
                        <a href="logout.php" class="btn btn-light">Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Tanggal Realtime -->
                    <div class="current-date">
                        <div>
                            <i class="bi bi-calendar-check"></i>
                            <span id="current-date"><?php echo date('d F Y'); ?></span>
                        </div>
                        <div>
                            <i class="bi bi-clock"></i>
                            <span id="current-time"><?php echo date('H:i:s'); ?></span>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12 text-center text-md-end">
                            <a href="tambah.php" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Tambah Todolist
                            </a>
                        </div>
                    </div>

                    <!-- Tabel untuk tampilan desktop -->
                    <div class="table-responsive desktop-table">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th>Status</th>
                                    <th>Prioritas</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Bagian query pencarian - Menggunakan prepared statement untuk mencegah SQL injection
                                $user_id = $_SESSION['user_id'];
                                $search = isset($_GET['search']) ? $_GET['search'] : '';
                                
                                if (!empty($search)) {
                                    $stmt = $conn->prepare("SELECT t.*, p.nama as prioritas_nama 
                                                          FROM todolist t 
                                                          LEFT JOIN prioritas p ON t.prioritas_id = p.id 
                                                          WHERE t.user_id=? AND t.judul LIKE ? 
                                                          ORDER BY t.prioritas_id ASC, t.id DESC");
                                    $searchParam = "%$search%";
                                    $stmt->bind_param("is", $user_id, $searchParam);
                                    $stmt->execute();
                                    $query = $stmt->get_result();
                                } else {
                                    $stmt = $conn->prepare("SELECT t.*, p.nama as prioritas_nama 
                                                          FROM todolist t 
                                                          LEFT JOIN prioritas p ON t.prioritas_id = p.id 
                                                          WHERE t.user_id=? 
                                                          ORDER BY t.prioritas_id ASC, t.id DESC");
                                    $stmt->bind_param("i", $user_id);
                                    $stmt->execute();
                                    $query = $stmt->get_result();
                                }
                                
                                $no = 1;
                                
                                // Simpan hasil query untuk digunakan di kedua tampilan
                                $taskData = [];
                                while ($data = mysqli_fetch_array($query)) {
                                    $taskData[] = $data;
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td class="fw-bold"><?php echo $data['judul']; ?></td>
                                    <td><?php echo $data['deskripsi']; ?></td>
                                    <td>
                                        <?php 
                                            // Menentukan warna badge berdasarkan status
                                            $badgeClass = '';
                                            switch($data['status']) {
                                                case 'belum selesai':
                                                    $badgeClass = 'bg-danger';
                                                    break;
                                                case 'sedang dikerjakan':
                                                    $badgeClass = 'bg-warning';
                                                    break;
                                                case 'terlambat mengerjakan':
                                                    $badgeClass = 'bg-dark';
                                                    break;
                                                case 'sudah selesai':
                                                case 'selesai':
                                                    $badgeClass = 'bg-success';
                                                    break;
                                            }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $data['status']; ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                            $prioritasClass = $data['prioritas_id'] == 1 ? 'bg-danger' : 'bg-secondary';
                                        ?>
                                        <span class="badge <?php echo $prioritasClass; ?>">
                                            <?php echo $data['prioritas_nama']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $data['tanggal_mulai']; ?></td>
                                    <td><?php echo $data['tanggal_selesai']; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="edit.php?id=<?php echo $data['id']; ?>" class="btn btn-warning btn-sm">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="dashboard.php?hapus=<?php echo $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                <?php 
                                if (count($taskData) == 0) { ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Tidak ada data yang ditemukan</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tampilan card untuk mobile -->
                    <div class="mobile-tasks">
                        <?php
                        if (count($taskData) == 0) { ?>
                            <div class="text-center py-4">
                                <p>Tidak ada data yang ditemukan</p>
                            </div>
                        <?php } else {
                            $no = 1;
                            foreach ($taskData as $data) {
                                // Menentukan warna badge berdasarkan status
                                $badgeClass = '';
                                switch($data['status']) {
                                    case 'belum selesai':
                                        $badgeClass = 'bg-danger';
                                        break;
                                    case 'sedang dikerjakan':
                                        $badgeClass = 'bg-warning';
                                        break;
                                    case 'terlambat mengerjakan':
                                        $badgeClass = 'bg-dark';
                                        break;
                                    case 'sudah selesai':
                                    case 'selesai':
                                        $badgeClass = 'bg-success';
                                        break;
                                }
                                
                                $prioritasClass = $data['prioritas_id'] == 1 ? 'bg-danger' : 'bg-secondary';
                        ?>
                        <div class="task-card">
                            <div class="task-title"><?php echo $no++; ?>. <?php echo $data['judul']; ?></div>
                            <div class="task-desc"><?php echo $data['deskripsi']; ?></div>
                            <div class="task-meta">
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $data['status']; ?></span>
                                <span class="badge <?php echo $prioritasClass; ?>"><?php echo $data['prioritas_nama']; ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="dates">
                                    <small class="text-muted d-block">Mulai: <?php echo $data['tanggal_mulai']; ?></small>
                                    <small class="text-muted d-block">Selesai: <?php echo $data['tanggal_selesai']; ?></small>
                                </div>
                                <div class="action-buttons">
                                    <a href="edit.php?id=<?php echo $data['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="dashboard.php?hapus=<?php echo $data['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php } 
                        } ?>
                    </div>
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

    <!-- Import Bootstrap JS untuk fungsionalitas komponen Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Script untuk update tanggal dan waktu secara realtime -->
    <script>
        function updateDateTime() {
            const now = new Date();
            
            // Format tanggal
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            const dateStr = now.toLocaleDateString('id-ID', options);
            
            // Format waktu
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const timeStr = `${hours}:${minutes}:${seconds}`;
            
            // Update elemen
            document.getElementById('current-date').textContent = dateStr;
            document.getElementById('current-time').textContent = timeStr;
        }
        
        // Update setiap detik
        setInterval(updateDateTime, 1000);
        
        // Update pertama kali
        updateDateTime();
    </script>
</body>
</html>