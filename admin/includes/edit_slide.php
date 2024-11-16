<?php
// Ma'lumotlar bazasi ulanishi
$conn = new mysqli("localhost", "root", "", "exam_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$slide = [];
$success_message = '';
$error_message = '';

if (isset($_GET['id'])) {
    $slide_id = $_GET['id'];
    $sql = "SELECT * FROM carousel_images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $slide_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $slide = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_slide'])) {
    // ... (rasm yuklash va ma'lumotlarni yangilash kodi)
}

$conn->close(); // Ma'lumotlar bazasi ulanishini yopish
?>

<!DOCTYPE html>
<html>
<head>
    <title>Slaydni tahrirlash</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #f8f9fa;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .sidebar a {
            display: block;
            color: #333;
            padding: 10px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #e9ecef;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="settings.php"><i class="fas fa-cog me-2"></i> Sayt sozlamalari</a>
    <a href="users.php"><i class="fas fa-users me-2"></i> Foydalanuvchilar</a>
    <a href="pages.php"><i class="fas fa-file me-2"></i> Sahifalar</a>
    </div>

<div class="content">
    <h1>Slaydni tahrirlash</h1>

    <?php if ($success_message || $error_message): ?>
        <div class="alert alert-<?php echo $success_message ? 'success' : 'danger'; ?> alert-dismissible fade show">
            <?php echo $success_message . $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        </form>
</div>

</body>
</html>