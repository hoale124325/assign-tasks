<?php
session_start();
if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    header("Location: login.php?error=" . urlencode("Vui lòng đăng nhập trước"));
    exit();
}

include "DB_connection.php";
include "app/Model/Task.php";

if (isset($_GET['file']) && !empty($_GET['file'])) {
    $file_path = filter_var($_GET['file'], FILTER_SANITIZE_STRING); // Lọc để tránh đường dẫn nguy hiểm
    $task_id = isset($_GET['task_id']) ? filter_var($_GET['task_id'], FILTER_VALIDATE_INT) : null;
    
    // Kiểm tra file tồn tại và thuộc về nhiệm vụ của người dùng
    if ($task_id) {
        $task = get_task_by_id($conn, $task_id);
        if ($task && $task['attachment'] === $file_path && ($task['assigned_to'] == $_SESSION['id'] || $_SESSION['role'] == 'admin')) {
            if (file_exists($file_path)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                header('Content-Length: ' . filesize($file_path));
                readfile($file_path);
                exit;
            } else {
                header("Location: my_task.php?error=" . urlencode("File không tồn tại"));
                exit;
            }
        } else {
            header("Location: my_task.php?error=" . urlencode("Bạn không có quyền tải file này"));
            exit;
        }
    }
}

header("Location: my_task.php?error=" . urlencode("Yêu cầu không hợp lệ"));
exit;
?>