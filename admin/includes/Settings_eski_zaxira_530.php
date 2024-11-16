
<?php
// Initialize messages and settings
$success_message = '';
$error_message = '';
$settings = [];

// Get current settings
$sql = "SELECT * FROM site_settings";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // General settings
    if (isset($_POST['update_general'])) {
        $general_settings = [
            'site_title' => $_POST['site_title'],
            'site_description' => $_POST['site_description']
        ];
        if(updateSettings($conn, $general_settings)) {
            $success_message = "Umumiy sozlamalar saqlandi";
        }
    }

    // Contact settings
    if (isset($_POST['update_contact'])) {
        $contact_settings = [
            'contact_email' => $_POST['contact_email'],
            'contact_phone' => $_POST['contact_phone']
        ];
        if(updateSettings($conn, $contact_settings)) {
            $success_message = "Kontakt ma'lumotlari saqlandi";
        }
    }

    // Social settings
    if (isset($_POST['update_social'])) {
        $social_settings = [
            'social_facebook' => $_POST['social_facebook'],
            'social_telegram' => $_POST['social_telegram'],
            'social_instagram' => $_POST['social_instagram']
        ];
        if(updateSettings($conn, $social_settings)) {
            $success_message = "Ijtimoiy tarmoqlar saqlandi";
        }
    }

    // Logo settings
    if (isset($_POST['update_logo'])) {
        // Handle logo dimensions
        $logo_settings = [
            'logo_max_width' => $_POST['logo_max_width'],
            'logo_max_height' => $_POST['logo_max_height']
        ];
        updateSettings($conn, $logo_settings);

        // Handle logo upload
        // Logo upload handling with directory creation
if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
   // Define directories
   $upload_dir = "../uploads";
   $carousel_dir = "../uploads/carousel"; 
   
   // Create directories if they don't exist
   if (!file_exists($upload_dir)) {
       mkdir($upload_dir, 0755, true);
       chmod($upload_dir, 0755);
   }
   
   if (!file_exists($carousel_dir)) {
       mkdir($carousel_dir, 0755, true);
       chmod($carousel_dir, 0755);
   }
   
   $file_extension = strtolower(pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION));
   $new_file_name = "logo." . $file_extension;
   $target_file = $upload_dir . "/" . $new_file_name;

   $allowed_types = ['jpg', 'jpeg', 'png', 'svg'];
   if (in_array($file_extension, $allowed_types)) {
       if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target_file)) {
           $logo_settings = ['site_logo' => $new_file_name];
           if(updateSettings($conn, $logo_settings)) {
               $success_message = "Logo muvaffaqiyatli yangilandi!";
           }
       } else {
           $error_message = "Faylni yuklashda xatolik: " . error_get_last()['message'];
       }
   } else {
       $error_message = "Faqat JPG, JPEG, PNG va SVG formatlar qabul qilinadi";
   }
}
    }
	
	// Carousel settings
if (isset($_POST['add_carousel_slide'])) {
    $upload_dir = "../uploads";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $title = $conn->real_escape_string($_POST['carousel_title']);
    $description = $conn->real_escape_string($_POST['carousel_description']);
    $link = $conn->real_escape_string($_POST['carousel_link']);
    
    // Update carousel text content
    $settings_updates = [
        'carousel_title' => $title,
        'carousel_description' => $description,
        'carousel_link' => $link
    ];
    
    foreach ($settings_updates as $key => $value) {
        $sql = "UPDATE site_settings SET setting_value = ? WHERE setting_key = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $value, $key);
        $stmt->execute();
    }
    
    // Handle image upload
    if (isset($_FILES['carousel_image']) && $_FILES['carousel_image']['error'] == 0) {
        $file = $_FILES['carousel_image'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $new_file_name = "carousel." . $file_extension;  // Fixed name instead of uniqid()
        $target_file = $upload_dir . "/" . $new_file_name;
        
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            $sql = "UPDATE site_settings SET setting_value = ? WHERE setting_key = 'carousel_image'";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $new_file_name);
            
            if ($stmt->execute()) {
                $success_message = "Karusel muvaffaqiyatli yangilandi!";
            } else {
                $error_message = "Xatolik yuz berdi: " . $stmt->error;
            }
        }
    }
}

// Helper function for checking/updating carousel settings
function checkCarouselSettings($conn) {
    $required_settings = [
        'carousel_title' => ['text', 'Karusel sarlavhasi'],
        'carousel_description' => ['text', 'Karusel tavsifi'],
        'carousel_link' => ['url', 'Karusel havolasi'],
        'carousel_image' => ['file', 'Karusel rasmi']
    ];
    
    foreach ($required_settings as $key => $details) {
        $sql = "INSERT IGNORE INTO site_settings (setting_key, setting_type, setting_value, setting_description) 
                VALUES (?, ?, '', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $key, $details[0], $details[1]);
        $stmt->execute();
    }
}

// Run this function when needed
checkCarouselSettings($conn);
	
	
}

// Helper function
function updateSettings($conn, $settings) {
    foreach ($settings as $key => $value) {
        $value = $conn->real_escape_string($value);
        $sql = "UPDATE site_settings SET setting_value = ? WHERE setting_key = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $value, $key);
        if (!$stmt->execute()) return false;
    }
    return true;
}
?>

<!-- HTML -->
<!DOCTYPE html>
<html>
<head>
    <style>
    .card {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }
    .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #eee;
    }
    .form-label.required:after {
        content: "*";
        color: red;
        margin-left: 4px;
    }
    .settings-preview img {
        max-width: 200px;
        height: auto;
    }
    </style>
</head>
<body>

<div class="container-fluid">
    <?php if ($success_message || $error_message): ?>
        <div class="alert alert-<?php echo $success_message ? 'success' : 'danger'; ?> alert-dismissible fade show">
            <?php echo $success_message . $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Sayt sozlamalari</h5>
        </div>
        <div class="card-body">
            <!-- Tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#general">
                        <i class="fas fa-cog me-2"></i>Umumiy
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#contact">
                        <i class="fas fa-address-card me-2"></i>Bog'lanish
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#social">
                        <i class="fas fa-share-alt me-2"></i>Ijtimoiy tarmoqlar
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#logo">
                        <i class="fas fa-image me-2"></i>Logo
                    </a>
                </li>
				
				
<li class="nav-item">
   <a class="nav-link" data-bs-toggle="tab" href="#carousel">
       <i class="fas fa-image me-2"></i>Banner reklamalar
   </a>
</li>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content py-4">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general">
                    <form method="POST" action="" class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Sayt ma'lumotlari</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label required">Sayt nomi</label>
                                        <input type="text" class="form-control" name="site_title" 
                                               value="<?php echo htmlspecialchars($settings['site_title'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Sayt tavsifi</label>
                                        <textarea class="form-control" name="site_description" rows="3" required><?php 
                                            echo htmlspecialchars($settings['site_description'] ?? ''); 
                                        ?></textarea>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" name="update_general" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Saqlash
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Contact Settings -->
                <div class="tab-pane fade" id="contact">
                    <form method="POST" action="" class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Kontakt ma'lumotlari</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label required">Email</label>
                                        <input type="email" class="form-control" name="contact_email"
                                               value="<?php echo htmlspecialchars($settings['contact_email'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label required">Telefon</label>
                                        <input type="text" class="form-control" name="contact_phone"
                                               value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" name="update_contact" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Saqlash
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Social Settings -->
                <div class="tab-pane fade" id="social">
                    <form method="POST" action="" class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Ijtimoiy tarmoqlar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Facebook</label>
                                        <input type="url" class="form-control" name="social_facebook"
                                               value="<?php echo htmlspecialchars($settings['social_facebook'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Telegram</label>
                                        <input type="url" class="form-control" name="social_telegram"
                                               value="<?php echo htmlspecialchars($settings['social_telegram'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Instagram</label>
                                        <input type="url" class="form-control" name="social_instagram"
                                               value="<?php echo htmlspecialchars($settings['social_instagram'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" name="update_social" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Saqlash
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Logo Settings -->
                <div class="tab-pane fade" id="logo">
                    <form method="POST" action="" enctype="multipart/form-data" class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Logo sozlamalari</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label required">Maksimal kenglik (px)</label>
                                            <input type="number" class="form-control" name="logo_max_width" 
                                                   value="<?php echo $settings['logo_max_width'] ?? '200'; ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label required">Maksimal balandlik (px)</label>
                                            <input type="number" class="form-control" name="logo_max_height" 
                                                   value="<?php echo $settings['logo_max_height'] ?? '60'; ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Joriy logo</label>
                                        <div class="settings-preview mb-3">
                                            <img src="../uploads/<?php echo $settings['site_logo'] ?? 'no-image.png'; ?>" 
                                                 alt="Site Logo" id="logoPreview" class="img-thumbnail">
                                        </div>
                                        <label class="form-label">Yangi logo</label>
                                        <input type="file" class="form-control" name="site_logo" accept="image/*" 
                                               onchange="previewLogo(this)">
                                        <small class="text-muted">PNG yoki SVG format tavsiya etiladi</small>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <button type="submit" name="update_logo" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Saqlash
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
				
				<div class="tab-pane fade" id="carousel">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Yangi slayd qo'shish</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label required">Sarlavha</label>
                            <input type="text" class="form-control" name="carousel_title" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tavsif</label>
                            <textarea class="form-control" name="carousel_description" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Havola</label>
                            <input type="url" class="form-control" name="carousel_link">
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Rasm</label>
                            <input type="file" class="form-control" name="carousel_image" 
                                   accept="image/*" required>
                        </div>
                        <button type="submit" name="add_carousel_slide" class="btn btn-primary">
                            Qo'shish
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Mavjud slaydlar</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Rasm</th>
                                    <th>Sarlavha</th>
                                    <th>Status</th>
                                    <th>Tartib</th>
                                    <th>Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $slides = $conn->query("SELECT * FROM carousel_images ORDER BY sort_order ASC");
                                while ($slide = $slides->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td>
                                        <img src="../uploads/carousel/<?php echo $slide['image_path']; ?>" 
                                             alt="Slide" style="height: 50px; object-fit: cover;">
                                    </td>
                                    <td><?php echo $slide['title']; ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" 
                                                   onchange="updateSlideStatus(<?php echo $slide['id']; ?>, this.checked)"
                                                   <?php echo $slide['status'] == 'active' ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm w-75" 
                                               value="<?php echo $slide['sort_order']; ?>"
                                               onchange="updateSlideOrder(<?php echo $slide['id']; ?>, this.value)">
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-danger" onclick="deleteSlide(<?php echo $slide['id']; ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
				
				
				
            </div>
        </div>
    </div>
</div>

<script>
// Logo preview function
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logoPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredInputs = form.querySelectorAll('[required]');
            let isValid = true;

            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Iltimos, barcha majburiy maydonlarni to\'ldiring');
            }
        });
    });
});
</script>

</body>
</html>
