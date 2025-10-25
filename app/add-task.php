<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {

    if (isset($_POST['title']) && isset($_POST['description']) && isset($_POST['assigned_to']) && $_SESSION['role'] == 'admin' && isset($_POST['due_date'])) {
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
        $due_date = validate_input($_POST['due_date']);

        if (empty($title)) {
            $em = "Title is required";
            header("Location: ../create_task.php?error=$em");
            exit();
        } else if (empty($description)) {
            $em = "Description is required";
            header("Location: ../create_task.php?error=$em");
            exit();
        } else if ($assigned_to == 0) {
            $em = "Select User";
            header("Location: ../create_task.php?error=$em");
            exit();
        } else {
            include "Model/Task.php";
            include "Model/Notification.php";

            // Xử lý upload file
            $attachment = null;
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
                $upload_dir = "../uploads/"; // Thư mục lưu file, đảm bảo tồn tại và có quyền ghi
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_name = basename($_FILES['attachment']['name']);
                $target_file = $upload_dir . uniqid() . '_' . $file_name; // Tạo tên file duy nhất
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
                    $attachment = $target_file; // Lưu đường dẫn file
                }
            }

            // Chèn nhiệm vụ với attachment
            $data = array($title, $description, $assigned_to, $due_date, $attachment);
            insert_task($conn, $data);

            $notif_data = array("'$title' đã được giao cho bạn. Vui lòng xem lại và bắt đầu làm việc", $assigned_to, 'Nhiệm vụ mới được giao');
            insert_notification($conn, $notif_data);

            $em = "Task created successfully";
            header("Location: ../create_task.php?success=$em");
            exit();
        }
    } else {
        $em = "Unknown error occurred";
        header("Location: ../create_task.php?error=$em");
        exit();
    }
} else { 
    $em = "First login";
    header("Location: ../create_task.php?error=$em");
    exit();
}
?>