<?php  
// Thông tin kết nối MySQL/MariaDB cho XAMPP
$sName = getenv('DB_HOST') ?: '127.0.0.1';
$uName = getenv('DB_USERNAME') ?: 'root';
$pass  = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'task_management_db';

try {
    // Đổi từ "pgsql" sang "mysql"
    $conn = new PDO("mysql:host=$sName;dbname=$db_name;charset=utf8", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>