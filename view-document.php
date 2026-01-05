<?php
session_start();
require_once 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: sign-in.php");
    exit();
}

// Ambil ID dokumen dari parameter
$doc_id = isset($_GET['id']) ? $_GET['id'] : '';

if (empty($doc_id)) {
    die("ID dokumen tidak valid");
}

// Query untuk mengambil data dokumen dari database
$stmt = $connection->prepare("SELECT * FROM dokumen_kp WHERE id_dokumen = ?");
$stmt->bind_param("s", $doc_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Dokumen tidak ditemukan");
}

$document = $result->fetch_assoc();

// Tentukan MIME type berdasarkan ekstensi dari nama dokumen
$file_extension = '';
if (strpos($document['nama_dokumen'], '.pdf') !== false || strpos(strtolower($document['nama_dokumen']), 'pdf') !== false) {
    $file_extension = 'pdf';
    $mime_type = 'application/pdf';
} elseif (strpos(strtolower($document['nama_dokumen']), 'template') !== false || strpos(strtolower($document['nama_dokumen']), 'word') !== false) {
    $file_extension = 'docx';
    $mime_type = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
} else {
    // Default ke PDF jika tidak dapat menentukan
    $file_extension = 'pdf';
    $mime_type = 'application/pdf';
}

// Jika parameter download ada, download file langsung dari database
if (isset($_GET['download'])) {
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: attachment; filename="' . $document['nama_dokumen'] . '"');
    header('Content-Length: ' . strlen($document['file_dokumen']));
    echo $document['file_dokumen'];
    exit();
}

// Untuk view file, buat temporary file dan tampilkan
if ($file_extension === 'pdf') {
    header('Content-Type: ' . $mime_type);
    header('Content-Disposition: inline; filename="' . $document['nama_dokumen'] . '"');
    echo $document['file_dokumen'];
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($document['nama_dokumen']); ?> - Sistem Kerja Profesi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }
        
        .document-header {
            background-color: #6d1028;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .document-title {
            font-size: 24px;
            font-weight: 600;
        }
        
        .document-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-download {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-download:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .btn-back {
            background-color: white;
            color: #6d1028;
        }
        
        .btn-back:hover {
            background-color: #f8f9fa;
        }
        
        .no-preview {
            text-align: center;
            padding: 50px;
            color: #666;
        }
        
        .no-preview h3 {
            margin-bottom: 15px;
            color: #6d1028;
        }
    </style>
</head>
<body>
    <div class="document-header">
        <h1 class="document-title"><?php echo htmlspecialchars($document['nama_dokumen']); ?></h1>
        <div class="document-actions">
            <a href="?id=<?php echo $doc_id; ?>&download=1" class="btn btn-download">
                📥 Download
            </a>
            <a href="dashboard.php" class="btn btn-back">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
    
    <!-- Untuk file Word atau jenis lain, tampilkan pesan dan tombol download -->
    <div class="no-preview">
        <h3>📄 <?php echo htmlspecialchars($document['nama_dokumen']); ?></h3>
        <p>File ini tidak dapat ditampilkan langsung di browser.</p>
        <p>Silakan download file untuk membukanya.</p>
        <br>
        <a href="?id=<?php echo $doc_id; ?>&download=1" class="btn btn-download" style="color: white;">
            📥 Download File
        </a>
    </div>
</body>
</html>