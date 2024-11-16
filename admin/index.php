<?php
session_start();
// Database connection
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

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_exam'])) {
        $title = $conn->real_escape_string($_POST['title']);
        $category = $conn->real_escape_string($_POST['category']);
        $description = $conn->real_escape_string($_POST['description']);
        
        $sql = "INSERT INTO exams (title, category, description) VALUES ('$title', '$category', '$description')";
        $conn->query($sql);
    }
    
    // Handle other form submissions...
}
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
            color: #fff;
        }
        .nav-link:hover {
            background: #495057;
        }
        .main-content {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column flex-shrink-0 p-3 text-white">
                    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-4">ExamPrep Admin</span>
                    </a>
                    <hr>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link active" aria-current="page">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white">
                                <i class="fas fa-book me-2"></i>Imtihonlar
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white">
                                <i class="fas fa-users me-2"></i>Foydalanuvchilar
                            </a>
                        </li>
                        <li>
                            <a href="#" class="nav-link text-white">
                                <i class="fas fa-comments me-2"></i>Sharhlar
                            </a>
                        </li>
                        <li>