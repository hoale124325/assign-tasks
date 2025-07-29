<?php  

// Lấy thông tin từ biến môi trường
$sName = getenv('DB_HOST') ?: '127.0.0.1'; // Mặc định localhost nếu không có biến
$uName = getenv('DB_USERNAME') ?: 'root';
$pass  = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'task_management_db';

try {
    // Sử dụng driver PostgreSQL
    $conn = new PDO("pgsql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}