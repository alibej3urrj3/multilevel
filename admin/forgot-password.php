<?php
session_start();
$conn = new mysqli("localhost", "root", "", "exam_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$messageType = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username'])) {
        $username = $conn->real_escape_string($_POST['username']);
        $newPassword = generatePassword(); // 8 belgili yangi parol yaratish
        
        // Check if username exists
        $sql = "SELECT id, email FROM admins WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            
            // Update password
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql = "UPDATE admins SET password_hash = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $passwordHash, $admin['id']);
            
            if ($stmt->execute()) {
                $message = "Yangi parolingiz: <strong>" . $newPassword . "</strong><br>
                           Iltimos, bu parolni saqlab qo'ying va tizimga kirgandan so'ng o'zgartiring.";
                $messageType = 'success';
            } else {
                $message = "Xatolik yuz berdi. Iltimos, qayta urinib ko'ring.";
                $messageType = 'danger';
            }
        } else {
            $message = "Bunday foydalanuvchi topilmadi.";
            $messageType = 'danger';
        }
    }
}

function generatePassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[rand(0, strlen($chars) - 1)];
    }
    return $password;
}
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parolni Tiklash - ExamPrep</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .forgot-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .forgot-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .forgot-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .forgot-logo i {
            font-size: 48px;
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-container">
            <div class="forgot-card">
                <div class="forgot-logo">
                    <i class="fas fa-lock"></i>
                    <h3 class="mt-3">Parolni Tiklash</h3>
                    <p class="text-muted">Foydalanuvchi nomingizni kiriting va yangi parol yaratiladi.</p>
                </div>
                
                <?php if($message): ?>
                <div class="alert alert-<?php echo $messageType; ?> mb-4">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="forgotForm">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Username" required>
                        <label for="username">Username</label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-key me-2"></i>Yangi parol yaratish
                    </button>
                    
                    <div class="text-center">
                        <a href="admin-login.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i>Login sahifasiga qaytish
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>