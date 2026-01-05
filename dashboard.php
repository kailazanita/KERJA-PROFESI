<?php
session_start();
require_once 'koneksi.php';

// Inisialisasi variabel
$user_role = 'Guest';
$user_name = 'User';

// Cek apakah user sudah login melalui session
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    $user_name = $_SESSION['user_name'];
    
    // Konversi role dari database ke format display yang user-friendly
    switch ($_SESSION['user_role']) {
        case 'mahasiswa':
            $user_role = 'Dashboard Mahasiswa';
            break;
        case 'admin':
            $user_role = 'Dashboard Admin';
            break;
        case 'dosen_pembimbing':
            $user_role = 'Dashboard Dosen Pembimbing';
            break;
        case 'dosen_penguji':
            $user_role = 'Dashboard Dosen Penguji';
            break;
        case 'koordinator':
            $user_role = 'Dashboard Koordinator KP';
            break;
        case 'kaprodi':
            $user_role = 'Dashboard Kepala Program Studi';
            break;
        default:
            $user_role = 'Dashboard User';
    }
} else {
    // Jika tidak ada session, redirect ke login
    header("Location: sign-in.php");
    exit();
}

// Ambil daftar dokumen dari database
$dokumen_list = [];
try {
    $stmt = $connection->prepare("SELECT id_dokumen, nama_dokumen, file_dokumen FROM dokumen_kp ORDER BY id_dokumen");
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $dokumen_list[] = $row;
    }
    $stmt->close();
} catch (Exception $e) {
    // Jika tabel belum ada atau ada error, buat array kosong
    $dokumen_list = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Kerja Profesi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css?v=<?php echo time(); ?>">
</head>
<body>
    <header>
        <img src="assets/upj.png" alt="Logo">
        <div class="header-title">
            <h1>Sistem Informasi Kerja Profesi</h1>
            <h2>Universitas Pembangunan Jaya</h2>
            <p>Jl. Cendrawasih Raya, Bintaro, Tangerang Selatan</p>
        </div>
        <div class="user">
            <h1><?php echo $user_role; ?></h1>
            <button>
                <a href="logout.php">
                    <svg class="logout-icon" width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="16,17 21,12 16,7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="21" y1="12" x2="9" y2="12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h2>Logout</h2>
                </a>
            </button>
        </div>
    </header>
    <main>
        <!-- Konten dashboard akan disesuaikan berdasarkan role user -->
        <?php if ($_SESSION['user_role'] === 'mahasiswa'): ?>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active">Beranda</a></li>
                <li><a href="mahasiswa/pengajuan.php">Pengajuan Kerja Profesi</a></li>
                <li><a href="mahasiswa/logbook.php">Logbook Harian</a></li>
                <li><a href="mahasiswa/bimbingan.php">Bimbingan</a></li>
                <li><a href="mahasiswa/pengajuan-sidang.php">Pengajuan Sidang</a></li>
                <li><a href="mahasiswa/revisi.php">Revisi Laporan</a></li>
                <li><a href="mahasiswa/tanda-terima.php">Tanda Terima Laporan</a></li>
            </ul>
        </nav>
        
        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active">Beranda</a></li>
                <li><a href="admin/kelola-user.php">Kelola User</a></li>
                <li><a href="admin/kelola-dokumen.php">Kelola Dokumen</a></li>
            </ul>
        </nav>
        
        <?php elseif ($_SESSION['user_role'] === 'dosen pembimbing'): ?>
        <nav>
            <ul>
                <li><a href="dosen-pembimbing/dashboard.php" class="active">Beranda</a></li>
                <li><a href="dosen-pembimbing/pengajuan.php">Pengajuan Kerja Profesi</a></li>
                <li><a href="dosen-pembimbing/logbook.php">Logbook Harian</a></li>
                <li><a href="dosen-pembimbing/bimbingan.php">Bimbingan</a></li>
                <li><a href="dosen-pembimbing/penilaian.php">Penilaian</a></li>
            </ul>
        </nav>

        <?php elseif ($_SESSION['user_role'] === 'dosen penguji'): ?>
        <nav>
            <ul>
                <li><a href="dosen-penguji/dashboard.php" class="active">Beranda</a></li>
                <li><a href="dosen-penguji/pengajuan-sidang.php">Pengajuan Sidang</a></li>
                <li><a href="dosen-penguji/penilaian.php">Penilaian</a></li>
                <li><a href="dosen-penguji/revisi.php">Revisi Laporan</a></li>
            </ul>
        </nav>

        <?php elseif ($_SESSION['user_role'] === 'koordinator kp'): ?>
        <nav>
            <ul>
                <li><a href="koordinator-kp/dashboard.php" class="active">Beranda</a></li>
                <li><a href="koordinator-kp/pengajuan.php">Pengajuan</a></li>
                <li><a href="koordinator-kp/pengajuan-sidang.php">Pengajuan Sidang</a></li>
                <li><a href="koordinator-kp/penilaian-pk.php">Penilaian Pembimbing Kerja</a></li>
                <li><a href="koordinator-kp/tanda-terima.php">Tanda Terima Laporan</a></li>
            </ul>
        </nav>

        <?php elseif ($_SESSION['user_role'] === 'koordinator kp'): ?>
        <nav>
            <ul>
                <li><a href="koordinator-kp/dashboard.php" class="active">Beranda</a></li>
                <li><a href="koordinator-kp/pengajuan.php">Pengajuan</a></li>
                <li><a href="koordinator-kp/pengajuan-sidang.php">Pengajuan Sidang</a></li>
            </ul>
        </nav>
        
        <?php else: ?>
        <nav>
            <ul>
                <li><a href="dashboard.php" class="active">Beranda</a></li>
                <li><a href="profil.php">Profil</a></li>
            </ul>
        </nav>
        <?php endif; ?>
        
        <section>
            <h2>Selamat Datang, <?php echo htmlspecialchars($user_name); ?>!</h2>
            <p>Role Anda: <strong><?php echo htmlspecialchars($_SESSION['user_role']); ?></strong></p>
            
            <?php if ($_SESSION['user_role'] === 'mahasiswa'): ?>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Status Pengajuan</h3>
                    <p>Lihat status pengajuan kerja profesi Anda</p>
                </div>
                <div class="card">
                    <h3>Pengajuan Baru</h3>
                    <p>Buat pengajuan kerja profesi baru</p>
                </div>
            </div>
            
            <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Total Pengajuan</h3>
                    <p>Kelola semua pengajuan kerja profesi</p>
                </div>
                <div class="card">
                    <h3>Manajemen User</h3>
                    <p>Kelola data mahasiswa dan dosen</p>
                </div>
            </div>
            
            <?php else: ?>
            <div class="dashboard-cards">
                <div class="card">
                    <h3>Dashboard</h3>
                    <p>Selamat datang di sistem informasi kerja profesi</p>
                </div>
            </div>
            <?php endif; ?>
        </section>
        <aside>
            <h1>Dokumen Kerja Profesi</h1>
            <ul>
                <?php if (!empty($dokumen_list)): ?>
                    <?php foreach ($dokumen_list as $dokumen): ?>
                        <li>
                            <div class="document-item">
                                <a href="view-document.php?id=<?php echo $dokumen['id_dokumen']; ?>" target="_blank" class="document-link">
                                    <?php echo htmlspecialchars($dokumen['nama_dokumen']); ?>
                                </a>
                                <a href="view-document.php?id=<?php echo $dokumen['id_dokumen']; ?>&download=1" class="download-btn" title="Download">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                        <polyline points="7,10 12,15 17,10"/>
                                        <line x1="12" y1="15" x2="12" y2="3"/>
                                    </svg>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="no-documents">
                        <p>Belum ada dokumen yang tersedia</p>
                    </li>
                <?php endif; ?>
            </ul>   
        </aside>
    </main>
    <footer>
        <div class="social-media">
            <div class="contact-info">
                <div class="contact-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
                    <span>(021) 745 5555</span>
                </div>
                <div class="contact-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <span>info@upj.ac.id</span>
                </div>
            </div>
        </div>
        <p>&copy; 2026 Sistem Informasi Kerja Profesi</p>
        <div class="nama-web">
            <h1>Sistem Informasi Kerja Profesi</h1>
            <h2>Universitas Pembangunan Jaya</h2>
        </div>
    </footer>
</body>
</html>