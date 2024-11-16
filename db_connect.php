<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "exam_site";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("Ma'lumotlar bazasiga ulanish amalga oshmadi: " . mysqli_connect_error());
}
?>
