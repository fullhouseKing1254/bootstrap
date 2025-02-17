<?php
// เปิดการแสดงข้อผิดพลาด
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ตั้งค่าการเชื่อมต่อ MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homework_db";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าเป็นคำขอแบบ POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["homeworkFile"]) && $_FILES["homeworkFile"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = "uploads/"; // โฟลเดอร์เก็บไฟล์

        $fileTmpPath = $_FILES["homeworkFile"]["tmp_name"];
        $fileName = basename($_FILES["homeworkFile"]["name"]);
        $file_name = $_FILES["homeworkFile"]["name"]; // ✅ กำหนดค่าให้ $file_name
        $file_name = preg_replace('/[^\wก-๙.-]/u', '_', $file_name); // รองรับภาษาไทย
        $destPath = $uploadDir . $file_name;

        // ตรวจสอบโฟลเดอร์ ถ้าไม่มีให้สร้าง
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            die("❌ ไม่สามารถสร้างโฟลเดอร์อัปโหลด");
        }

        // ย้ายไฟล์ไปยังโฟลเดอร์ที่กำหนด
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            // เตรียมคำสั่ง SQL เพื่อบันทึกลงฐานข้อมูล
            $sql = "INSERT INTO homework_files (file_name, file_path, upload_time) VALUES (?, ?, NOW())";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("❌ SQL Prepare Failed: " . $conn->error);
            }

            $stmt->bind_param("ss", $fileName, $destPath);

            // Execute และตรวจสอบผลลัพธ์
            if ($stmt->execute()) {
                echo "อัปโหลดเสร็จสิ้น"; // ✅ ส่งข้อความนี้กลับไป
            } else {
                echo "❌ เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "❌ เกิดข้อผิดพลาดในการบันทึกไฟล์!";
        }
    } else {
        echo "❌ ไม่พบไฟล์ที่อัปโหลด หรือเกิดข้อผิดพลาด: " . ($_FILES["homeworkFile"]["error"] ?? "ไม่ทราบข้อผิดพลาด");
    }
} else {
    echo "❌ วิธีการส่งข้อมูลไม่ถูกต้อง";
}

$conn->close();
?>
