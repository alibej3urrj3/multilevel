<?php
session_start();
$conn = new mysqli("localhost", "root", "", "exam_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Login check
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit();
}

$current_page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - ExamPrep</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
        }
        .nav-link {
            color: rgba(255,255,255,.8);
            padding: 10px 15px;
            margin: 5px 0;
        }
        .nav-link:hover, .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,.1);
        }
        .main-content {
            min-height: 100vh;
            background: #f8f9fa;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        .step-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: transform 0.3s;
        }
        .step-card:hover {
            transform: translateY(-5px);
        }
        .step-icon {
            font-size: 2rem;
            color: #0d6efd;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 position-fixed sidebar">
                <div class="d-flex flex-column p-3">
                    <a href="admin-panel.php" class="d-flex align-items-center mb-3 text-white text-decoration-none">
                        <i class="fas fa-graduation-cap me-2"></i>
                        <span class="fs-4">ExamPrep</span>
                    </a>
                    <hr class="text-white">
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="?page=dashboard" class="nav-link <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </li>
						<li class="nav-item">
        <a href="?page=exam_categories" class="nav-link <?php echo $current_page == 'exam_categories' ? 'active' : ''; ?>">
            <i class="fas fa-list me-2"></i>Imtihon kategoriyalari
        </a>
    </li>
                        <li>
                            <a href="?page=question_bank" class="nav-link <?php echo $current_page == 'question_bank' ? 'active' : ''; ?>">
                                <i class="fas fa-book me-2"></i>Savollar banki
                            </a>
                        </li>
                        <li>
                            <a href="?page=exam_scheduler" class="nav-link <?php echo $current_page == 'exam_scheduler' ? 'active' : ''; ?>">
                                <i class="fas fa-calendar me-2"></i>Imtihon jadvali
                            </a>
                        </li>
                        <li>
                            <a href="?page=candidates" class="nav-link <?php echo $current_page == 'candidates' ? 'active' : ''; ?>">
                                <i class="fas fa-users me-2"></i>Nomzodlar
                            </a>
                        </li>
                        <li>
                            <a href="?page=results" class="nav-link <?php echo $current_page == 'results' ? 'active' : ''; ?>">
                                <i class="fas fa-chart-bar me-2"></i>Natijalar
                            </a>
                        </li>
                        <li>
                            <a href="?page=pages" class="nav-link <?php echo $current_page == 'pages' ? 'active' : ''; ?>">
                                <i class="fas fa-file me-2"></i>Sahifalar
                            </a>
                        </li>
                        <li>
                            <a href="?page=settings" class="nav-link <?php echo $current_page == 'settings' ? 'active' : ''; ?>">
                                <i class="fas fa-cog me-2"></i>Sozlamalar
                            </a>
                        </li>
                    </ul>
                    <hr class="text-white">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle me-2"></i>
                            <strong><?php echo $_SESSION['admin_username']; ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
                            <li><a class="dropdown-item" href="?page=profile">Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Chiqish</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 ms-auto main-content p-4">
                <?php
                switch($current_page) {
                    case 'dashboard':
                        include 'includes/dashboard.php';
                        break;
                    case 'pages':
                        include 'includes/pages.php';
                        break;
                    case 'settings':
                        include 'includes/settings.php';
                        break;
					case 'exam_categories':
                        include 'includes/exam_categories.php';
                        break;	
                    // ... boshqa sahifalar
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/27.1.0/classic/ckeditor.js"></script>
</body>
</html>