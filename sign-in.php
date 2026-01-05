<?php
session_start();
require_once 'koneksi.php';

$user_data = null;
$login_error = false;
$error_message = "";

// Cek koneksi database
if ($connection->connect_error) {
    $error_message = "Database connection failed: " . $connection->connect_error;
}

if ($_POST && !$error_message) {
    try {
        $id_user = $_POST['id_user'];
        $password = $_POST['password'];
        
        // Cek di tabel users
        $stmt = $connection->prepare("SELECT id_user, nama_user, password, program_studi, role FROM user WHERE id_user = ?");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $connection->error);
        }
        
        $stmt->bind_param("s", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Verifikasi password
            if ($password === $user['password']) {
                $user_data = $user;
                // Simpan data user ke session
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['user_name'] = $user['nama_user'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['program_studi'] = $user['program_studi'];
            } else {
                $login_error = true;
                $error_message = "Password salah";
            }
        } else {
            $login_error = true;
            $error_message = "User tidak ditemukan";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        $login_error = true;
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Kerja Profesi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="sign-in.css?v=<?php echo time(); ?>">
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('fill', 'none');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('fill', 'currentColor');
            }
        }
    </script>
</head>
<body>
    <div class="main-content">
        <div class="logo-container">
            <div class="logo"></div>
        </div>
        <h1 class="title">Sistem Informasi Kerja Profesi</h1>
        
        <?php if ($error_message): ?>
        <div class="debug-info">
            Debug Info: <?php echo $error_message; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($user_data): ?>
        <div class="role-select">
            <h1>Selamat Datang, <?php echo $user_data['nama_user']; ?>!</h1>
            
            <?php if ($user_data['role'] === 'admin'): ?>
            <button id="btnAdmin">
                <a href="dashboard.php">
                    <h1>Admin</h1>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'mahasiswa'): ?>
            <button id="btnMahasiswa">
                <a href="dashboard.php">
                    <h1>Mahasiswa</h1>
                    <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'dosen_pembimbing'): ?>
            <button id="btnDosenPembimbing">
                <a href="dashboard.php">
                    <h1>Dosen Pembimbing</h1>
                    <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'dosen_penguji'): ?>
            <button id="btnDosenPenguji">
                <a href="dashboard.php">
                    <h1>Dosen Penguji</h1>
                    <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'koordinator'): ?>
            <button id="btnKoordinator">
                <a href="dashboard.php">
                    <h1>Koordinator KP</h1>
                    <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'kaprodi'): ?>
            <button id="btnKaprodi">
                <a href="dashboard.php">
                    <h1>Kepala Program Studi</h1>
                    <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php else: ?>
            <div class="error">
                <p>Role tidak dikenali: <?php echo htmlspecialchars($user_data['role']); ?></p>
                <a href="sign-in.php">Kembali</a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php elseif ($login_error): ?>
        <div class="error">
            <p>Login gagal! <?php echo $error_message ? htmlspecialchars($error_message) : 'ID User atau password salah.'; ?></p>
            <a href="sign-in.php">Coba lagi</a>
        </div>
        
        <?php else: ?>
        <!-- Form Login -->
        <div class="login-form">
            <form method="post">
                <div class="form-group">
                    <label for="id_user">NIM/NIDN</label>
                    <input type="text" id="id_user" name="id_user" placeholder="Masukkan NIM/NIDN" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            <svg class="eye-icon" id="eyeIcon" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                <path d="M1 12C1 12 5 4 12 4C19 4 23 12 23 12C23 12 19 20 12 20C5 20 1 12 1 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-login">Masuk</button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <footer>
            <p>&copy; 2026 Sistem Informasi Kerja Profesi</p>
        </footer>
    </div>
</body>
</html>