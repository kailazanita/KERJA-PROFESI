<?php
require_once 'koneksi.php';

$user_data = null;
$login_error = false;

if ($_POST) {
    $id_user = $_POST['id_user'];
    $password = $_POST['password'];
    
    $stmt = $connection->prepare("SELECT id_user, nama_user, password, program_studi, role FROM user WHERE id_user = ?");
    $stmt->bind_param("s", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if ($password === $user['password']) { 
            $user_data = $user;
        } else {
            $login_error = true;
        }
    } else {
        $login_error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kerja Profesi</title>
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
                <a href="admin/dashboard.php">
                    <h1>Admin</h1>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'mahasiswa'): ?>
            <button id="btnMahasiswa">
                <a href="mahasiswa/dashboard.php">
                    <h1>Mahasiswa</h1>
                    <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'dosen pembimbing'): ?>
            <button id="btnDosenPembimbing">
                <a href="dosenPembimbing/dashboard.php">
                    <h1>Dosen Pembimbing</h1>
                <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'dosen penguji'): ?>
            <button id="btnDosenPenguji">
                <a href="dosenPenguji/dashboard.php">
                    <h1>Dosen <h2>
                    <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'koordinator kp'): ?>
            <button id="btnKoordinator">
                <a href="koordinator/dashboard.php">
                    <h1>Koordinator KP</h1>
                    <h2><strong><?php echo $user_data['id_user']; ?></strong> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'kaprodi'): ?>
            <button id="btnKaprodi">
                <a href="kaprodi/dashboard.php">
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