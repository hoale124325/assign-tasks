<?php  
$sName = '127.0.0.1';         // hoặc localhost nếu vẫn chạy tốt
$uName = 'root';              // tài khoản MySQL
$pass  = '';                  // để trống nếu chưa đặt mật khẩu
$db_name = 'task_management_db';  // tên cơ sở dữ liệu

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name;charset=utf8", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connected successfully to local database!";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage();
    exit;
}
?>
