<?php
// Ma'lumotlar bazasi ulanishi
$conn = new mysqli("localhost", "root", "", "exam_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $slide_id = $_GET['id'];

    // Slayd rasmini olish (o'chirish uchun)
    $sql = "SELECT image_path FROM carousel_images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $slide_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $slide = $result->fetch_assoc();

    // Slaydni ma'lumotlar bazasidan o'chirish
    $sql = "DELETE FROM carousel_images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $slide_id);

    if ($stmt->execute()) {
        // Slayd rasmini o'chirish
        if (!empty($slide['image_path'])) {
            unlink("../uploads/carousel/" . $slide['image_path']);
        }
        echo "Slayd muvaffaqiyatli o'chirildi!";
    } else {
        echo "Xatolik yuz berdi: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>