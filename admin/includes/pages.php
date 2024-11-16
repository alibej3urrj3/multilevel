<?php
// pages.php

// Get current page for editing if specified
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_page'])) {
        $page_id = (int)$_POST['page_id'];
        $title = $conn->real_escape_string($_POST['title']);
        $content = $conn->real_escape_string($_POST['content']);
        $meta_description = $conn->real_escape_string($_POST['meta_description']);
        
        $sql = "UPDATE pages SET 
                title = ?, 
                content = ?,
                meta_description = ?,
                updated_at = NOW()
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $title, $content, $meta_description, $page_id);
        
        if ($stmt->execute()) {
            $success_message = "Sahifa muvaffaqiyatli yangilandi!";
        } else {
            $error_message = "Xatolik yuz berdi: " . $conn->error;
        }
    }
    
    // Handle image upload for carousel
    if (isset($_POST['add_carousel_image'])) {
        $target_dir = "../uploads/carousel/";
        $file = $_FILES['carousel_image'];
        $image_title = $conn->real_escape_string($_POST['image_title']);
        
        if ($file['error'] == 0) {
            $file_name = uniqid() . "-" . basename($file['name']);
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $sql = "INSERT INTO carousel_images (title, image_path, created_at) 
                        VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $image_title, $file_name);
                
                if ($stmt->execute()) {
                    $success_message = "Rasm muvaffaqiyatli yuklandi!";
                } else {
                    $error_message = "Xatolik yuz berdi: " . $conn->error;
                }
            }
        }
    }
}

// Get pages list
$pages_sql = "SELECT * FROM pages ORDER BY id ASC";
$pages_result = $conn->query($pages_sql);

// Get carousel images
$carousel_sql = "SELECT * FROM carousel_images ORDER BY created_at DESC";
$carousel_result = $conn->query($carousel_sql);
?>

<!-- Page Management Section -->
<div class="container-fluid">
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Sahifalarni boshqarish</h5>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="pageTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#pages">Sahifalar</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#carousel">Karusel rasmlari</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#branding">Brending</a>
                </li>
            </ul>
            
            <div class="tab-content mt-3">
                <!-- Pages Tab -->
                <div class="tab-pane fade show active" id="pages">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sahifa</th>
                                    <th>So'nggi yangilanish</th>
                                    <th>Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($page = $pages_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($page['title']); ?></td>
                                    <td><?php echo $page['updated_at']; ?></td>
                                    <td>
                                        <a href="?page=pages&edit=<?php echo $page['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Tahrirlash
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($edit_id > 0):
                        $page_sql = "SELECT * FROM pages WHERE id = ?";
                        $stmt = $conn->prepare($page_sql);
                        $stmt->bind_param("i", $edit_id);
                        $stmt->execute();
                        $page = $stmt->get_result()->fetch_assoc();
                    ?>
                    <div class="mt-4">
                        <h5>Sahifani tahrirlash: <?php echo htmlspecialchars($page['title']); ?></h5>
                        <form method="POST" action="">
                            <input type="hidden" name="page_id" value="<?php echo $page['id']; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Sarlavha</label>
                                <input type="text" class="form-control" name="title" 
                                       value="<?php echo htmlspecialchars($page['title']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Kontent</label>
                                <textarea id="editor" name="content" class="form-control" rows="10">
                                    <?php echo htmlspecialchars($page['content']); ?>
                                </textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Meta tavsif</label>
                                <textarea class="form-control" name="meta_description" rows="2"><?php 
                                    echo htmlspecialchars($page['meta_description']); 
                                ?></textarea>
                            </div>
                            
                            <button type="submit" name="update_page" class="btn btn-primary">
                                <i class="fas fa-save"></i> Saqlash
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Carousel Tab -->
                <div class="tab-pane fade" id="carousel">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="POST" action="" enctype="multipart/form-data" class="card p-3">
                                <h6>Yangi karusel rasmi qo'shish</h6>
                                <div class="mb-3">
                                    <label class="form-label">Rasm sarlavhasi</label>
                                    <input type="text" class="form-control" name="image_title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Rasm</label>
                                    <input type="file" class="form-control" name="carousel_image" 
                                           accept="image/*" required>
                                </div>
                                <button type="submit" name="add_carousel_image" class="btn btn-primary">
                                    <i class