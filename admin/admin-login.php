<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "exam_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    
    // Get admin data from database
    $sql = "SELECT id, username, password_hash FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $admin['password_hash'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            
            // Set last login time
            $update_sql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $admin['id']);
            $update_stmt->execute();
            
            // Redirect to admin panel
            header("Location: admin-panel.php");
            exit();
        } else {
            $error = 'Noto\'g\'ri username yoki parol';
        }
    } else {
        $error = 'Noto\'g\'ri username yoki parol';
    }
}
?>

<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ExamPrep</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        .login-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo i {
            font-size: 48px;
            color: #0d6efd;
        }
        .form-floating {
            margin-bottom: 15px;
        }
        .error-message {
            color: #dc3545;
            margin-bottom: 15px;
            text-align: center;
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
        <div class="login-container">
            <div class="login-card">
                <div class="login-logo">
                    <i class="fas fa-user-shield"></i>
                    <h3 class="mt-3">ExamPrep Admin</h3>
                </div>
                
                <?php if($error): ?>
                <div class="error-message">
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="" id="loginForm">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Username" required>
                        <label for="username">Username</label>
                    </div>
                    
                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Password" required>
                        <label for="password">Password</label>
                        <i class="fas fa-eye password-toggle" id="passwordToggle"></i>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="rememberMe" name="remember">
                        <label class="form-check-label" for="rememberMe">
                            Meni eslab qol
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-2"></i>Kirish
                    </button>
                </form>
                
                <div class="text-center mt-3">
                    <a href="forgot-password.php" class="text-decoration-none">Parolni unutdingizmi?</a>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="../index.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Asosiy sahifaga qaytish
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password visibility toggle
        document.getElementById('passwordToggle').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this;
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (username === '' || password === '') {
                e.preventDefault();
                alert('Iltimos, barcha maydonlarni to\'ldiring');
            }
        });

        // Remember me functionality
        if (localStorage.getItem('adminUsername')) {
            document.getElementById('username').value = localStorage.getItem('adminUsername');
            document.getElementById('rememberMe').checked = true;
        }

        document.getElementById('loginForm').addEventListener('submit', function() {
            if (document.getElementById('rememberMe').checked) {
                localStorage.setItem('adminUsername', document.getElementById('username').value);
            } else {
                localStorage.removeItem('adminUsername');
            }
        });
    </script>
</body>
</html>