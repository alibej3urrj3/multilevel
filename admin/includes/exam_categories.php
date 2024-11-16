<?php
// exam_categories.php
// exam_categories.php
$success_message = '';
$error_message = '';
?>
<div class="container-fluid">
    <?php if ($success_message || $error_message): ?>
        <div class="alert alert-<?php echo $success_message ? 'success' : 'danger'; ?> alert-dismissible fade show">
            <?php echo $success_message . $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!isset($_GET['add']) && !isset($_GET['edit'])): ?>
    <!-- Categories List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Imtihon kategoriyalari</h5>
            <a href="?page=exam_categories&add=true" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Yangi kategoriya
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Icon</th>
                            <th>Nomi</th>
                            <th>Turi</th>
                            <th>Narxi</th>
                            <th>Davomiyligi</th>
                            <th>Darajalar</th>
                            <th>Status</th>
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT ec.*, 
                               (SELECT COUNT(*) FROM exam_levels WHERE category_id = ec.id) as levels_count 
                               FROM exam_categories ec ORDER BY ec.id DESC";
                        $result = $conn->query($sql);
                        while ($category = $result->fetch_assoc()):
                            $sections = json_decode($category['sections'], true);
                        ?>
                        <tr>
                            <td><i class="<?php echo $category['icon']; ?>"></i></td>
                            <td><?php echo $category['name']; ?></td>
                            <td>
                                <?php 
                                $types = [
                                    'language' => 'Til imtihoni',
                                    'academic' => 'Akademik',
                                    'professional' => 'Professional',
                                    'other' => 'Boshqa'
                                ];
                                echo $types[$category['exam_type']];
                                ?>
                            </td>
                            <td><?php echo number_format($category['price'], 2); ?> $</td>
                            <td>
                                <?php echo $category['duration']; ?> daqiqa
                                <small class="d-block text-muted">
                                    <?php
                                    if ($sections) {
                                        $parts = [];
                                        foreach ($sections as $name => $section) {
                                            $parts[] = ucfirst($name) . ": " . $section['duration'] . "m";
                                        }
                                        echo implode(", ", $parts);
                                    }
                                    ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-info"><?php echo $category['levels_count']; ?> daraja</span>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $category['status'] == 'active' ? 'success' : 'danger'; ?>">
                                    <?php echo $category['status'] == 'active' ? 'Faol' : 'Nofaol'; ?>
                                </span>
                            </td>
                            <td>
                                <a href="?page=exam_categories&edit=<?php echo $category['id']; ?>" 
                                   class="btn btn-sm btn-primary" title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?page=exam_categories&view=<?php echo $category['id']; ?>" 
                                   class="btn btn-sm btn-info" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" title="O'chirish"
                                        onclick="deleteCategory(<?php echo $category['id']; ?>)">
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

    <?php else: ?>
    <!-- Add/Edit Form -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <?php echo isset($_GET['edit']) ? 'Kategoriyani tahrirlash' : 'Yangi kategoriya qo\'shish'; ?>
            </h5>
            <a href="?page=exam_categories" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
        </div>
        <div class="card-body">
            <!-- Form kodlari... -->
            <?php include 'exam_category_form.php'; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
// O'chirish funksiyasi
function deleteCategory(id) {
    if (confirm('Ushbu kategoriyani o\'chirmoqchimisiz?')) {
        window.location.href = `?page=exam_categories&delete=${id}`;
    }
}
</script>