<?php
// เปิดใช้งานข้อผิดพลาด
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตั้งค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homework_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "❌ Connection failed: " . $conn->connect_error]));
}
$conn->set_charset("utf8mb4");

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT id, file_name, file_path, upload_time FROM homework_files ORDER BY upload_time DESC";
$result = $conn->query($sql);

$homeworkFiles = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $homeworkFiles[] = $row;
    }
}

// ส่ง JSON กลับไปให้ JavaScript
echo json_encode($homeworkFiles);

$conn->close();
?>
