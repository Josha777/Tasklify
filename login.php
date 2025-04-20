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

// Proses login ketika form disubmit dengan memeriksa POST login
if (isset($_POST['login'])) {
    // Mengambil data username dan password dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Query untuk mencari user dengan username yang diinput
    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    
    // Cek apakah user ditemukan dan password sesuai
    if ($user = mysqli_fetch_assoc($query)) {
        if (password_verify($password, $user['password'])) {
            // Jika sesuai, set session dan redirect ke dashboard.php
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        }
    }
    
    // Jika login gagal, set pesan error
    $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tasklify - Login</title>
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        /* Styling untuk card form login */
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

        /* Styling untuk input form */
        .form-control {
            border-radius: 8px;
            padding: 12px;
        }

        /* Styling untuk tombol primary */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            font-weight: 500;
        }

        /* Styling untuk tombol link */
        .btn-link {
            width: 100%;
            margin-top: 10px;
            color: #4f46e5;
        }

        /* Styling untuk tampilan mobile */
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
                    <!-- Card form login -->
                    <div class="card">
                        <!-- Header card -->
                        <div class="card-header">
                            <h3 class="text-center mb-0">
                                <i class="bi bi-person-circle me-2"></i>Login
                            </h3>
                        </div>
                        <!-- Body card -->
                        <div class="card-body p-4">
                            <!-- Menampilkan pesan error jika ada -->
                            <?php if(isset($error)): ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <?php echo $error; ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Form login -->
                            <form method="POST">
                                <!-- Input username -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="bi bi-person-fill me-2"></i>Username
                                    </label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <!-- Input password -->
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="bi bi-lock-fill me-2"></i>Password
                                    </label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <!-- Tombol login -->
                                <button type="submit" name="login" class="btn btn-primary">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </button>
                                <!-- Link ke halaman register -->
                                <a href="register.php" class="btn btn-link text-decoration-none">
                                    Belum punya akun? Register
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