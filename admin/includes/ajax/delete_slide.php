<?php
$conn = new mysqli("localhost", "root", "", "exam_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['slide_index'])) {
    $index = $_POST['slide_index'];
    
    // Get current carousel data
    $sql = "SELECT setting_value FROM site_settings WHERE setting_key IN 
           ('carousel_images', 'carousel_titles', 'carousel_descriptions', 'carousel_links', 'carousel_statuses')";
    $result = $conn->query($sql);
    
    $carousel_data = [];
    while($row = $result->fetch_assoc()) {
        $data = json_decode($row['setting_value'], true);
        if($data && isset($data[$index])) {
            unset($data[$index]);
            $data = array_values($data); // Reindex array
            $carousel_data[$row['setting_key']] = json_encode($data);
        }
    }
    
    // Update settings
    $success = true;
    foreach($carousel_data as $key => $value) {
        $sql = "UPDATE site_settings SET setting_value = ? WHERE setting_key = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $value, $key);
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }
    
    echo json_encode(['success' => $success]);
}
?>