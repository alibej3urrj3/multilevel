<?php
session_start();
$conn = new mysqli("localhost", "root", "", "exam_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$messageType = '';
$validToken = false;
$token = '';

if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    
    // Check if token is valid and not expired
    $sql = "SELECT pr.*, a.email 
            FROM password_resets pr 
            JOIN admins a ON pr.admin_id = a.id 
            WHERE pr.token = ? 
            AND pr.used = 0 
            AND pr.expires_at > NOW()";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $validToken = true;
    } else {
        $message = "Yaroqsiz yoki muddati o'tgan havola. Iltimos, yangi so'rov yuboring.";
        $messageType = 'danger';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $validToken) {
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if (strlen($password) < 8) {
        $message = "Parol kamida 8 ta belgidan iborat bo'lishi kerak.";
        $messageType = 'danger';
    } elseif ($password !== $confirmPassword) {
        $message = "Parollar mos kelmadi.";
        $messageType = 'danger';
    } else {
        // Get admin info
        $resetInfo = $result->fetch_assoc();
        
        // Update password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE admins SET password_hash = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $passwordHash, $resetInfo['admin_id']);
        
        if ($stmt->execute()) {
            // Mark token as used
            $sql = "UPDATE password_resets SET used = 1 WHERE token = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $token);
            $stmt->execute();
            
            $message = "Parol muvaffaqiyatli o'zgartirildi. Endi yangi parol bilan tizimga kirishingiz mumkin.";
            $messageType = 'success';
            
            // Redirect to login page after 3 seconds
            header("refresh:3;url=admin-login.php");
        } else {
            $message = "Xatolik yuz berdi. Iltimos, qayta urinib ko'ring.";
            $messageType = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parolni O'zgartirish - ExamPrep</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .reset-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .reset-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .reset-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .reset-logo i {
            font-size: 48px;
            color: #0d6efd;
        }
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <div class="reset-card">
                <div class="reset-logo">
                    <i class="fas fa-key"></i>
                    <h3 class="mt-3">Yangi Parol O'rnatish</h3>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> mb-4">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <?php if($validToken): ?>
                <form method="POST" action="" id="resetForm">
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required minlength="8">
                        <label for="password">Yangi parol</label>
                        <i class="fas fa-eye password-toggle" data-target="password"></i>
                    </div>
                    
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="confirm_password" 
                               name="confirm_password" placeholder="Confirm Password" required minlength="8">
                        <label for="confirm_password">Parolni tasdiqlang</label>
                        <i class="fas fa-eye password-toggle" data-target="confirm_password"></i>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-save me-2"></i>Parolni o'zgartirish
                    </button>
                </form>
                <?php endif; ?>
                
                <div class="text-center">
                    <a href="admin-login.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-1"></i>Login sahifasiga qaytish
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.querySelectorAll('.password-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('fa-eye');
                    this.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('fa-eye-slash');
                    this.classList.add('fa-eye');
                }
            });
        });

        // Form validation
        document.getElementById('resetForm')?.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Parol kamida 8 ta belgidan iborat bo\'lishi kerak');
                return;