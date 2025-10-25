<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    if (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && isset($_POST['due_date']) && isset($_POST['status']) && $_SESSION['role'] == 'admin') {
        include "../DB_connection.php";

        function validate_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $title = validate_input($_POST['title']);
        $description = validate_input($_POST['description']);
        $assigned_to = validate_input($_POST['assigned_to']);
        $id = validate_input($_POST['id']);
        $due_date = validate_input($_POST['due_date']);
        $status = validate_input($_POST['status']);

        if (empty($title)) {
            $em = "Title is required";
            header("Location: ../edit-task.php?error=$em&id=$id");
            exit();
        } else if (empty($description)) {
            $em = "Description is required";
            header("Location: ../edit-task.php?error=$em&id=$id");
            exit();
        } else if ($assigned_to == 0) {
            $em = "Select User";
            header("Location: ../edit-task.php?error=$em&id=$id");
        } else {
            include "Model/Task.php";

            // Xử lý upload file cho attachment nếu có
            $attachment = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $upload_dir = "../uploads/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_name = basename($_FILES['attachment']['name']);
                $target_file = $upload_dir . uniqid() . '_' . $file_name;
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
                    $attachment = $target_file;
                }
            }

            // Xử lý upload file cho completed_file nếu có
            $completed_file = null;
            if (isset($_FILES['completed_file']) && $_FILES['completed_file']['error'] == 0) {
                $upload_dir = "../uploads/completed/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_name = basename($_FILES['completed_file']['name']);
                $target_file = $upload_dir . uniqid() . '_' . $file_name;
                if (move_uploaded_file($_FILES['completed_file']['tmp_name'], $target_file)) {
                    $completed_file = $target_file;
                }
            }

            // Kiểm tra trạng thái hợp lệ
            $valid_statuses = ['pending', 'in_progress', 'completed'];
            if (!in_array(strtolower($status), $valid_statuses)) {
                $em = "Invalid status value";
                header("Location: ../edit-task.php?error=$em&id=$id");
                exit();
            }

            // Lấy thông tin nhiệm vụ hiện tại để xử lý file cũ (nếu cần xóa)
            $current_task = get_task_by_id($conn, $id);
            $old_attachment = $current_task['attachment'] ?? null;
            $old_completed_file = $current_task['completed_file'] ?? null;

            // Chuẩn bị dữ liệu để cập nhật
            $data = [$title, $description, $assigned_to, $due_date, $status, $id];
            if ($attachment !== null) {
                $data = array_merge(array_slice($data, 0, 4), [$attachment], array_slice($data, 4));
                // Xóa file cũ nếu có và khác với file mới
                if ($old_attachment && $old_attachment != $attachment && file_exists($old_attachment)) {
                    unlink($old_attachment);
                }
            }
            if ($completed_file !== null) {
                $data = array_merge(array_slice($data, 0, 5), [$completed_file], array_slice($data, 5));
                // Xóa file hoàn thành cũ nếu có và khác với file mới
                if ($old_completed_file && $old_completed_file != $completed_file && file_exists($old_completed_file)) {
                    unlink($old_completed_file);
                }
            }

            // Cập nhật dữ liệu
            if (update_task($conn, $data)) {
                $due_date_filter = isset($_GET['due_date']) ? $_GET['due_date'] : '';
                $em = "Task updated successfully";
                header("Location: ../tasks.php?due_date=" . urlencode($due_date_filter) . "&success=$em");
                exit();
            } else {
                $em = "Failed to update task";
                header("Location: ../edit-task.php?error=$em&id=$id");
                exit();
            }
        }
    } else {
        $em = "Unknown error occurred";
        header("Location: ../edit-task.php?error=$em");
        exit();
    }
} else {
    $em = "First login";
    header("Location: ../login.php?error=$em");
    exit();
}
?>