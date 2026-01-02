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
    <link rel="stylesheet" href="sign-in.css">
</head>
<body>
    <div class="main-content">
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
                    <h2><?php echo $user_data['id_user']; ?> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'dosen pembimbing'): ?>
            <button id="btnDosenPembimbing">
                <a href="dosenPembimbing/dashboard.php">
                    <h1>Dosen Pembimbing</h1>
                <h2><?php echo $user_data['id_user']; ?> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'dosen penguji'): ?>
            <button id="btnDosenPenguji">
                <a href="dosenPenguji/dashboard.php">
                    <h1>Dosen <h2>
                    <h2><?php echo $user_data['id_user']; ?> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'koordinator kp'): ?>
            <button id="btnKoordinator">
                <a href="koordinator/dashboard.php">
                    <h1>Koordinator KP</h1>
                    <h2><?php echo $user_data['id_user']; ?> : <?php echo $user_data['program_studi']; ?></h2>
                </a>
            </button>
            
            <?php elseif ($user_data['role'] === 'kaprodi'): ?>
            <button id="btnKaprodi">
                <a href="kaprodi/dashboard.php">
                    <h1>Kepala Program Studi</h1>
                    <h2><?php echo $user_data['id_user']; ?> : <?php echo $user_data['program_studi']; ?></h2>
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
        <!-- Error Login -->
        <div class="error">
            <p>Login gagal! <?php echo $error_message ? htmlspecialchars($error_message) : 'ID User atau password salah.'; ?></p>
            <a href="sign-in.php">Coba lagi</a>
        </div>
        
        <?php else: ?>
        <!-- Form Login -->
        <form method="post">
            <label for="id_user">User ID</label>
            <br>
            <input type="text" id="id_user" name="id_user" required>
            <br><br>
            <label for="password">Password</label>
            <br>
            <input type="password" id="password" name="password" required>
            <br><br>
            <button type="submit">Masuk</button>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>