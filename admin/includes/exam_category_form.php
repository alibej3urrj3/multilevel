<?php
// exam_categories.php
// exam_categories.php
$success_message = '';
$error_message = '';
$edit_id = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
// Helper functions
function createSlug($str) {
    // O'zbek harflarini lotin harflariga o'zgartirish
    $str = mb_strtolower($str, 'UTF-8');
    
    $replacements = [
        'ё' => 'yo', 'й' => 'y', 'ц' => 'ts', 'у' => 'u', 'к' => 'k',
        'е' => 'e', 'н' => 'n', 'г' => 'g', 'ш' => 'sh', 'щ' => 'sch',
        'з' => 'z', 'х' => 'x', 'ъ' => '', 'ф' => 'f', 'ы' => 'i',
        'в' => 'v', 'а' => 'a', 'п' => 'p', 'р' => 'r', 'о' => 'o',
        'л' => 'l', 'д' => 'd', 'ж' => 'j', 'э' => 'e', 'я' => 'ya',
        'ч' => 'ch', 'с' => 's', 'м' => 'm', 'и' => 'i', 'т' => 't',
        'ь' => '', 'б' => 'b', 'ю' => 'yu', 'ў' => 'o', 'ғ' => 'g',
        'қ' => 'q', 'ҳ' => 'h', 'ә' => 'a', 'ө' => 'o', 'ү' => 'u',
        'ң' => 'ng', ' ' => '-'
    ];
    
    // Belgilarni almashtirish
    $str = str_replace(array_keys($replacements), array_values($replacements), $str);
    
    // Faqat lotin harflari, raqamlar va chiziqchani qoldirish
    $str = preg_replace('/[^a-z0-9-]/', '', $str);
    
    // Ketma-ket kelgan chiziqchalarni bitta qilish
    $str = preg_replace('/-+/', '-', $str);
    
    // Boshi va oxiridagi chiziqchalarni olib tashlash
    return trim($str, '-');
}

// Slugni unique qilish uchun funksiya
function makeUniqueSlug($conn, $slug, $id = 0) {
    $originalSlug = $slug;
    $i = 1;
    
    while (true) {
        $sql = "SELECT id FROM exam_categories WHERE slug = ? AND id != ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $slug, $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            break;
        }
        
        $slug = $originalSlug . '-' . $i;
        $i++;
    }
    
    return $slug;
}

// Qolgan kod...





// Sample exam sections
$default_sections = json_encode([
    'listening' => [
        'duration' => 40,
        'questions' => 40,
        'description' => 'Listening comprehension test'
    ],
    'reading' => [
        'duration' => 60,
        'questions' => 40,
        'description' => 'Reading comprehension test'
    ],
    'writing' => [
        'duration' => 60,
        'tasks' => 2,
        'description' => 'Writing tasks'
    ],
    'speaking' => [
        'duration' => 15,
        'tasks' => 3,
        'description' => 'Speaking interview'
    ]
]);

// Sample features
$default_features = json_encode([
    'mock_tests' => true,
    'practice_materials' => true,
    'video_lessons' => true,
    'personal_tutor' => false,
    'score_analysis' => true
]);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_category']) || isset($_POST['update_category'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $slug = $conn->real_escape_string(createSlug($_POST['name']));
    $description = $conn->real_escape_string($_POST['description']);
    $exam_type = $conn->real_escape_string($_POST['exam_type']);
    $icon = $conn->real_escape_string($_POST['icon']);
    $status = 'active'; // default status
    $price = (float)$_POST['price'];
    $duration = (int)$_POST['duration'];
    $passing_score = (int)$_POST['passing_score'];
    $certificate_type = $conn->real_escape_string($_POST['certificate_type']);
    $validity_period = (int)$_POST['validity_period'];
    $requirements = $conn->real_escape_string($_POST['requirements']);
    
    // Process sections
    $sections = [];
    foreach ($_POST['sections'] as $section => $details) {
        $sections[$section] = [
            'duration' => (int)$details['duration'],
            'description' => $details['description']
        ];
        
        if ($section == 'listening' || $section == 'reading') {
            $sections[$section]['questions'] = (int)$details['questions'];
        } else {
            $sections[$section]['tasks'] = (int)$details['tasks'];
        }
    }
    $sections_json = json_encode($sections);

    // Process features
    $features = [];
    foreach ($_POST['features'] as $feature => $value) {
        $features[$feature] = (bool)$value;
    }
    $features_json = json_encode($features);

    if (isset($_POST['update_category'])) {
        $id = (int)$_POST['category_id'];
        $sql = "UPDATE exam_categories SET 
                name = ?, 
                slug = ?, 
                description = ?, 
                exam_type = ?, 
                icon = ?,
                price = ?,
                duration = ?,
                passing_score = ?,
                certificate_type = ?,
                validity_period = ?,
                requirements = ?,
                sections = ?,
                features = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssdiisisssi", 
            $name, 
            $slug, 
            $description, 
            $exam_type,
            $icon, 
            $price, 
            $duration, 
            $passing_score,
            $certificate_type, 
            $validity_period, 
            $requirements,
            $sections_json, 
            $features_json,
            $id
        );
    } else {
        $sql = "INSERT INTO exam_categories 
                (name, slug, description, exam_type, icon, price, duration,
                passing_score, certificate_type, validity_period, requirements,
                sections, features) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssdiisisss", 
            $name, 
            $slug, 
            $description, 
            $exam_type,
            $icon, 
            $price, 
            $duration, 
            $passing_score,
            $certificate_type, 
            $validity_period, 
            $requirements,
            $sections_json, 
            $features_json
        );
    }

    if ($stmt->execute()) {
        $category_id = isset($_POST['update_category']) ? $id : $conn->insert_id;
        
        // Handle levels
        if (isset($_POST['levels'])) {
            foreach ($_POST['levels'] as $level) {
                if (!empty($level['name'])) {
                    $sql = "INSERT INTO exam_levels (category_id, name, description, min_score, max_score) 
                            VALUES (?, ?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE 
                            description = VALUES(description),
                            min_score = VALUES(min_score),
                            max_score = VALUES(max_score)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("issii", 
                        $category_id, 
                        $level['name'], 
                        $level['description'], 
                        $level['min_score'], 
                        $level['max_score']
                    );
                    $stmt->execute();
                }
            }
        }

        $success_message = isset($_POST['update_category']) ? 
                        "Imtihon ma'lumotlari yangilandi!" : 
                        "Yangi imtihon qo'shildi!";
    } else {
        $error_message = "Xatolik yuz berdi: " . $stmt->error;
    }
}
}

// Get example data
$example_exams = [
    [
        'name' => 'IELTS',
        'description' => 'International English Language Testing System',
        'exam_type' => 'language',
        'icon' => 'fas fa-language',
        'price' => 200,
        'duration' => 180,
        'passing_score' => 0, // IELTS uses band scores
        'certificate_type' => 'IELTS Certificate',
        'validity_period' => 24,
        'levels' => [
            ['name' => 'Band 9', 'description' => 'Expert', 'min_score' => 9, 'max_score' => 9],
            ['name' => 'Band 8', 'description' => 'Very Good', 'min_score' => 8, 'max_score' => 8.5],
            ['name' => 'Band 7', 'description' => 'Good', 'min_score' => 7, 'max_score' => 7.5],
            ['name' => 'Band 6', 'description' => 'Competent', 'min_score' => 6, 'max_score' => 6.5],
            ['name' => 'Band 5', 'description' => 'Modest', 'min_score' => 5, 'max_score' => 5.5]
        ]
    ],
    [
        'name' => 'TOEFL',
        'description' => 'Test of English as a Foreign Language',
        'exam_type' => 'language',
        'icon' => 'fas fa-graduation-cap',
        'price' => 180,
        'duration' => 180,
        'passing_score' => 80,
        'certificate_type' => 'TOEFL iBT Score Report',
        'validity_period' => 24,
        'levels' => [
            ['name' => 'Advanced', 'description' => 'Advanced Level', 'min_score' => 95, 'max_score' => 120],
            ['name' => 'Upper Intermediate', 'description' => 'Upper Intermediate Level', 'min_score' => 72, 'max_score' => 94],
            ['name' => 'Intermediate', 'description' => 'Intermediate Level', 'min_score' => 42, 'max_score' => 71],
            ['name' => 'Basic', 'description' => 'Basic Level', 'min_score' => 0, 'max_score' => 41]
        ]
    ],
    // Boshqa imtihon turlarini ham qo'shish mumkin
];
?>

<!-- HTML Template -->
<div class="container-fluid">
    <!-- Success/Error Messages -->
    <?php if ($success_message || $error_message): ?>
        <div class="alert alert-<?php echo $success_message ? 'success' : 'danger'; ?> alert-dismissible fade show">
            <?php echo $success_message . $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Example Exams -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Namunali imtihon turlari</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($example_exams as $exam): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <i class="<?php echo $exam['icon']; ?> me-2"></i>
                                        <?php echo $exam['name']; ?>
                                    </h5>
                                    <p class="card-text"><?php echo $exam['description']; ?></p>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-clock me-2"></i>Davomiyligi: <?php echo $exam['duration']; ?> daqiqa</li>
                                        <li><i class="fas fa-certificate me-2"></i>Sertifikat: <?php echo $exam['certificate_type']; ?></li>
                                        <li><i class="fas fa-calendar-alt me-2"></i>Amal qilish muddati: <?php echo $exam['validity_period']; ?> oy</li>
                                    </ul>
                                    <button class="btn btn-primary" onclick="useExampleExam(<?php echo htmlspecialchars(json_encode($exam)); ?>)">
                                        Namuna sifatida ishlatish
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Exam Category Form -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <?php echo $edit_id ? 'Imtihon ma\'lumotlarini tahrirlash' : 'Yangi imtihon qo\'shish'; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="" id="examForm">
                        <!-- Basic Information -->
                        <h6 class="mb-3">Asosiy ma'lumotlar</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Imtihon nomi</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tavsif</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
								<div class="mb-3">
                                    <label class="form-label">Imtihon turi</label>
                                    <select class="form-select" name="exam_type" required>
                                        <option value="language">Til imtihoni</option>
                                        <option value="academic">Akademik imtihon</option>
                                        <option value="professional">Professional sertifikatsiya</option>
                                        <option value="other">Boshqa</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Icon (FontAwesome class)</label>
                                    <input type="text" class="form-control" name="icon" value="fas fa-language">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Narx ($)</label>
                                    <input type="number" class="form-control" name="price" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Davomiyligi (daqiqa)</label>
                                    <input type="number" class="form-control" name="duration" required>
                                </div>
                            </div>
                        </div>

                        <!-- Exam Details -->
                        <h6 class="mb-3">Imtihon tafsilotlari</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">O'tish bali</label>
                                    <input type="number" class="form-control" name="passing_score">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sertifikat turi</label>
                                    <input type="text" class="form-control" name="certificate_type">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Amal qilish muddati (oy)</label>
                                    <input type="number" class="form-control" name="validity_period">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Talablar</label>
                                    <textarea class="form-control" name="requirements" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Exam Sections -->
<h6 class="mb-3">Imtihon bo'limlari</h6>
<div class="row mb-4" id="sectionContainer">
    <!-- Listening Section -->
    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Listening</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label">Davomiyligi (daqiqa)</label>
                    <input type="number" class="form-control" name="sections[listening][duration]">
                </div>
                <div class="mb-2">
                    <label class="form-label">Savollar soni</label>
                    <input type="number" class="form-control" name="sections[listening][questions]">
                </div>
                <div class="mb-2">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="sections[listening][description]" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reading Section -->
    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Reading</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label">Davomiyligi (daqiqa)</label>
                    <input type="number" class="form-control" name="sections[reading][duration]">
                </div>
                <div class="mb-2">
                    <label class="form-label">Savollar soni</label>
                    <input type="number" class="form-control" name="sections[reading][questions]">
                </div>
                <div class="mb-2">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="sections[reading][description]" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Writing Section -->
    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Writing</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label">Davomiyligi (daqiqa)</label>
                    <input type="number" class="form-control" name="sections[writing][duration]">
                </div>
                <div class="mb-2">
                    <label class="form-label">Topshiriqlar soni</label>
                    <input type="number" class="form-control" name="sections[writing][tasks]">
                </div>
                <div class="mb-2">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="sections[writing][description]" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Speaking Section -->
    <div class="col-md-3">
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0">Speaking</h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <label class="form-label">Davomiyligi (daqiqa)</label>
                    <input type="number" class="form-control" name="sections[speaking][duration]">
                </div>
                <div class="mb-2">
                    <label class="form-label">Topshiriqlar soni</label>
                    <input type="number" class="form-control" name="sections[speaking][tasks]">
                </div>
                <div class="mb-2">
                    <label class="form-label">Tavsif</label>
                    <textarea class="form-control" name="sections[speaking][description]" rows="2"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>

                        <!-- Exam Levels -->
                        <h6 class="mb-3">Imtihon darajalari</h6>
                        <div class="row mb-4" id="levelContainer">
                            <!-- Level template will be added here by JavaScript -->
                        </div>
                        <button type="button" class="btn btn-outline-primary mb-4" onclick="addLevel()">
                            <i class="fas fa-plus me-2"></i>Daraja qo'shish
                        </button>

                        <!-- Features -->
                        <h6 class="mb-3">Imtihon imkoniyatlari</h6>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="features[mock_tests]" checked>
                                    <label class="form-check-label">Namunaviy testlar</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="features[practice_materials]" checked>
                                    <label class="form-check-label">O'quv materiallari</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="features[video_lessons]">
                                    <label class="form-check-label">Video darslar</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="features[personal_tutor]">
                                    <label class="form-check-label">Shaxsiy o'qituvchi</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" name="<?php echo $edit_id ? 'update_category' : 'add_category'; ?>" 
                                    class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                <?php echo $edit_id ? 'Saqlash' : 'Qo\'shish'; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Level template for adding new levels
function getLevelHtml(index) {
    return `
        <div class="col-md-3 level-item">
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Daraja ${index + 1}</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLevel(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="form-label">Nomi</label>
                        <input type="text" class="form-control" name="levels&#91;${index}&#93;&#91;name&#93;" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Tavsif</label>
                        <input type="text" class="form-control" name="levels&#91;${index}&#93;&#91;description&#93;">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Minimal ball</label>
                        <input type="number" class="form-control" name="levels&#91;${index}&#93;&#91;min_score&#93;" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Maksimal ball</label>
                        <input type="number" class="form-control" name="levels&#91;${index}&#93;&#91;max_score&#93;" required>
                    </div>
                </div>
            </div>
        </div>
    `;
}


// Add new level
function addLevel() {
    const container = document.getElementById('levelContainer');
    const index = container.getElementsByClassName('level-item').length;
    container.insertAdjacentHTML('beforeend', getLevelHtml(index));
}

// Remove level
function removeLevel(button) {
    button.closest('.level-item').remove();
}

// Use example exam data
function useExampleExam(exam) {
    document.querySelector('input[name="name"]').value = exam.name;
    document.querySelector('textarea[name="description"]').value = exam.description;
    document.querySelector('select[name="exam_type"]').value = exam.exam_type;
    document.querySelector('input[name="icon"]').value = exam.icon;
    document.querySelector('input[name="price"]').value = exam.price;
    document.querySelector('input[name="duration"]').value = exam.duration;
    document.querySelector('input[name="passing_score"]').value = exam.passing_score;
    document.querySelector('input[name="certificate_type"]').value = exam.certificate_type;
    document.querySelector('input[name="validity_period"]').value = exam.validity_period;

    // Clear existing levels
    const levelContainer = document.getElementById('levelContainer');
    levelContainer.innerHTML = '';

    // Add example levels
    exam.levels.forEach((level, index) => {
        levelContainer.insertAdjacentHTML('beforeend', getLevelHtml(index));
        const lastLevel = levelContainer.lastElementChild;
        lastLevel.querySelector('input[name^="levels"][name$="[name]"]').value = level.name;
        lastLevel.querySelector('input[name^="levels"][name$="[description]"]').value = level.description;
        lastLevel.querySelector('input[name^="levels"][name$="[min_score]"]').value = level.min_score;
        lastLevel.querySelector('input[name^="levels"][name$="[max_score]"]').value = level.max_score;
    });
}

// Add initial level on page load
document.addEventListener('DOMContentLoaded', function() {
    addLevel();
});
</script>