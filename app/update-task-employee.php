<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    if (isset($_POST['id']) && isset($_POST['status']) && $_SESSION['role'] == 'employee') {
        include "../DB_connection.php";

        function validate_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $status = validate_input($_POST['status']);
        $id = validate_input($_POST['id']);

        if (empty($status)) {
            $em = "Status is required";
            header("Location: ../edit-task-employee.php?error=$em&id=$id");
            exit();
        } else {
            // Kiểm tra giá trị status hợp lệ (phù hợp với enum trong DB)
            $valid_statuses = ['pending', 'in_progress', 'completed'];
            if (!in_array(strtolower($status), $valid_statuses)) {
                $em = "Invalid status value";
                header("Location: ../edit-task-employee.php?error=$em&id=$id");
                exit();
            }

            include "Model/Task.php";

            // Xử lý upload file nếu trạng thái là completed
            $completed_file = null;
            if (strtolower($status) === 'completed' && isset($_FILES['task_file']) && $_FILES['task_file']['error'] == 0) {
                $upload_dir = "../uploads/completed/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_name = basename($_FILES['task_file']['name']);
                $target_file = $upload_dir . uniqid() . '_' . $file_name;
                $allowed_types = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/jpeg', 'image/png'];
                if (in_array(mime_content_type($_FILES['task_file']['tmp_name']), $allowed_types) && $_FILES['task_file']['size'] <= 5 * 1024 * 1024) {
                    if (move_uploaded_file($_FILES['task_file']['tmp_name'], $target_file)) {
                        $completed_file = $target_file;
                    } else {
                        $em = "Lỗi khi tải file lên";
                        header("Location: ../edit-task-employee.php?error=$em&id=$id");
                        exit();
                    }
                } else {
                    $em = "File không hợp lệ hoặc quá lớn (tối đa 5MB)";
                    header("Location: ../edit-task-employee.php?error=$em&id=$id");
                    exit();
                }
            }

            // Lấy thông tin nhiệm vụ hiện tại để xử lý file cũ (nếu cần xóa)
            $current_task = get_task_by_id($conn, $id);
            $old_completed_file = $current_task['completed_file'] ?? null;

            // Chuẩn bị dữ liệu để cập nhật
            if ($completed_file !== null) {
                $data = [$status, $completed_file, $id];
                if (update_task_with_file($conn, $data)) {
                    // Xóa file cũ nếu có và khác với file mới
                    if ($old_completed_file && $old_completed_file != $completed_file && file_exists($old_completed_file)) {
                        unlink($old_completed_file);
                    }
                    $em = "Task updated successfully";
                    header("Location: ../my_task.php?success=$em");
                    exit();
                }
            } else {
                $data = [$status, $id];
                if (update_task_status($conn, $data)) {
                    $em = "Task updated successfully";
                    header("Location: ../my_task.php?success=$em");
                    exit();
                }
            }

            $em = "Failed to update task";
            header("Location: ../edit-task-employee.php?error=$em&id=$id");
            exit();
        }
    } else {
        $em = "Unknown error occurred";
        header("Location: ../edit-task-employee.php?error=$em");
        exit();
    }
} else { 
    $em = "First login";
    header("Location: ../login.php?error=$em");
    exit();
}
?>