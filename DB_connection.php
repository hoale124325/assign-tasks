<?php  
$sName = getenv('DB_HOST') ?: 'localhost';
$uName = getenv('DB_USERNAME') ?: 'root';
$pass  = getenv('DB_PASSWORD') ?: '';
$db_name = getenv('DB_NAME') ?: 'task_management_db';

try {
    // ĐÃ SỬA: mysql thay vì pgsql
    $conn = new PDO("mysql:host=$sName;dbname=$db_name;charset=utf8", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>