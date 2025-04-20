<?php
// Memulai session untuk manajemen login user
session_start();
// Mengimpor konfigurasi database dari file config/database.php
require_once 'config/database.php';

// Cek apakah user sudah login dengan memeriksa session user_id
// Jika sudah login, redirect ke halaman dashboard.php
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Proses registrasi ketika form disubmit dengan memeriksa POST register
if (isset($_POST['register'])) {
    // Mengambil data dari form yang disubmit
    $username = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Cek username sudah ada atau belum di database
    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Username sudah digunakan!";
    } 
    // Cek apakah password dan konfirmasi password sama
    elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak sesuai!";
    } 
    // Jika semua validasi berhasil, simpan data user baru
    else {
        // Enkripsi password sebelum disimpan ke database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        // Insert data user baru ke database
        mysqli_query($conn, "INSERT INTO users (username, nama_lengkap, password) VALUES ('$username', '$nama_lengkap', '$hashed_password')");
        // Redirect ke halaman login setelah berhasil registrasi
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tasklify - Register</title>
    <!-- Setting viewport untuk responsive design -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Import CSS Bootstrap dan Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Styling untuk background halaman */
        body {
            background: url('img/background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
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

        /* Container untuk form login */
        .login-container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            align-items: center;
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

        /* Styling untuk header card */
        .card-header {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
            color: white;
            border-radius: 20px 20px 0 0 !important;
            padding: 20px;
        }

        /* Styling untuk form input */
        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid rgba(0,0,0,0.1);
        }

        /* Styling untuk tombol primary */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
        }

        /* Styling untuk tombol link */
        .btn-link {
            width: 100%;
            margin-top: 10px;
            color: #4f46e5;
        }
        
        /* CSS untuk tampilan mobile */
        @media (max-width: 768px) {
            .col-md-5 {
                width: 100%;
            }
            
            .card {
                margin-top: 20px;
                margin-bottom: 20px;
            }
            
            .form-control {
                font-size: 16px; /* Mencegah zoom pada iOS */
            }
            
            .card-header h3 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Container utama -->
    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <!-- Card untuk form register -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-center mb-0">
                                <i class="bi bi-person-plus-fill me-2"></i>Register
                            </h3>
                        </div>
                        <div class="card-body p-4">
                            <!-- Menampilkan pesan error jika ada -->
                            <?php if(isset($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Form register -->
                            <form method="POST">
                                <!-- Input username -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-person-fill me-2"></i>Username
                                    </label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <!-- Input nama lengkap -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-person-badge-fill me-2"></i>Nama Lengkap
                                    </label>
                                    <input type="text" name="nama_lengkap" class="form-control" required>
                                </div>
                                <!-- Input password -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-lock-fill me-2"></i>Password
                                    </label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <!-- Input konfirmasi password -->
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="bi bi-lock-fill me-2"></i>Konfirmasi Password
                                    </label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                                <!-- Tombol submit -->
                                <button type="submit" name="register" class="btn btn-primary">
                                    <i class="bi bi-person-plus-fill me-2"></i>Register
                                </button>
                                <!-- Link ke halaman login -->
                                <a href="login.php" class="btn btn-link text-decoration-none">
                                    Sudah punya akun? Login
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Import JavaScript Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 