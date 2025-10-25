<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $em = "Vui lòng đăng nhập với vai trò admin!";
    header("Location: register.php?error=" . urlencode($em));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['full_name']) && isset($_POST['user_name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
    include "../DB_connection.php";

    function validate_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $full_name = validate_input($_POST['full_name']);
    $user_name = validate_input($_POST['user_name']);
    $email = validate_input($_POST['email']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);

    // Kiểm tra các trường bắt buộc
    if (empty($full_name)) {
        $em = "Họ và tên là bắt buộc";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    } elseif (empty($user_name)) {
        $em = "Tên người dùng là bắt buộc";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    } elseif (empty($email)) {
        $em = "Email là bắt buộc";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    } elseif (empty($password)) {
        $em = "Mật khẩu là bắt buộc";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    } elseif ($password !== $confirm_password) {
        $em = "Mật khẩu và xác nhận mật khẩu không khớp";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    }

    // Kiểm tra email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $em = "Email không hợp lệ";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    }

    // Kiểm tra trùng username
    $sql = "SELECT username FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_name]);
    if ($stmt->rowCount() > 0) {
        $em = "Tên người dùng đã tồn tại";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    }

    // Kiểm tra trùng email
    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $em = "Email đã được sử dụng";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    }

    // Mã hóa mật khẩu và thêm user
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $data = array($full_name, $user_name, $email, $password_hashed, "employee");
    include "Model/User.php";
    insert_user($conn, $data);

    $sm = "Đăng ký thành công! Vui lòng đăng nhập.";
    header("Location: register.php?success=" . urlencode($sm));
    exit();
} else {
    $em = "Dữ liệu gửi đi không hợp lệ!";
    header("Location: register.php?error=" . urlencode($em));
    exit();
}
?>