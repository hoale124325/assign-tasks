<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Lấy và kiểm tra dữ liệu từ form
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? null);
        $role = $_POST['role'] ?? 'employee';
        $password = password_hash(trim($_POST['password'] ?? ''), PASSWORD_DEFAULT);
        $avatar = null;

        // Xử lý upload avatar nếu có
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $avatar_dir = 'img/';
            $avatar_file = $avatar_dir . basename($_FILES['avatar']['name']);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_file)) {
                $avatar = $avatar_file;
            }
        }

        // Kiểm tra dữ liệu bắt buộc
        if (empty($full_name) || empty($username) || empty($email) || empty($_POST['password'])) {
            $error = "Vui lòng điền đầy đủ các trường bắt buộc.";
            header("Location: add-user.php?error=" . urlencode($error));
            exit();
        }

        // Tạo mảng dữ liệu
        $data = [$full_name, $username, $email, $phone_number, $role, $password, $avatar];

        try {
            insert_user($conn, $data);
            $success = "Thêm người dùng thành công.";
            header("Location: user.php?success=" . urlencode($success));
            exit();
        } catch (Exception $e) {
            $error = "Lỗi khi thêm người dùng: " . $e->getMessage();
            header("Location: add-user.php?error=" . urlencode($error));
            exit();
        }
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Người Dùng | Hệ thống Quản lý</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-add-user">
    <div class="app-container">
        <?php include "inc/nav.php"; ?>
        
        <div class="main-content">
            <div class="content-wrapper">
                <!-- Page Header -->
                <header class="page-header">
                    <div>
                        <div class="header-flex">
                            <a href="user.php" class="back-button">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <h1 class="page-title">
                                <i class="fas fa-user-plus"></i>
                                Thêm Người Dùng Mới
                            </h1>
                        </div>
                        <p class="page-description">Thêm tài khoản người dùng mới vào hệ thống</p>
                    </div>
                </header>

                <!-- Error Message -->
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                    </div>
                <?php } ?>

                <!-- Form Card -->
                <div class="card">
                    <div class="card-header">
                        <h2>Thông tin người dùng</h2>
                    </div>
                    
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" class="user-form">
                            <!-- Avatar Upload -->
                            <div class="avatar-upload">
                                <div class="avatar-preview-container">
                                    <img id="avatarPreview" src="img/default-avatar.png" alt="Avatar Preview" class="avatar-preview">
                                    <div class="avatar-change" onclick="document.getElementById('avatar').click()">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                                <p class="avatar-upload-text">Nhấn để thay đổi ảnh đại diện</p>
                            </div>

                            <!-- Form Grid -->
                            <div class="form-grid">
                                <!-- Full Name -->
                                <div class="form-group full-width">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i>Họ và tên *
                                    </label>
                                    <input type="text" name="full_name" required 
                                           class="form-control" 
                                           placeholder="Nhập họ và tên đầy đủ">
                                </div>

                                <!-- Username -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-at"></i>Tên đăng nhập *
                                    </label>
                                    <input type="text" name="username" required 
                                           class="form-control" 
                                           placeholder="username">
                                    <p class="form-text">Tên đăng nhập không dấu, không khoảng cách</p>
                                </div>

                                <!-- Email -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-envelope"></i>Email *
                                    </label>
                                    <input type="email" name="email" required 
                                           class="form-control" 
                                           placeholder="example@email.com">
                                </div>

                                <!-- Phone Number -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-phone"></i>Số điện thoại
                                    </label>
                                    <input type="text" name="phone_number" 
                                           class="form-control" 
                                           placeholder="0123456789">
                                </div>

                                <!-- Role -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user-tag"></i>Vai trò *
                                    </label>
                                    <select name="role" required class="form-control">
                                        <option value="employee">Nhân viên</option>
                                        <option value="admin">Quản trị viên</option>
                                    </select>
                                </div>

                                <!-- Password -->
                                <div class="form-group full-width">
                                    <label class="form-label">
                                        <i class="fas fa-lock"></i>Mật khẩu *
                                    </label>
                                    <div class="password-wrapper">
                                        <input type="password" id="password" name="password" required 
                                               class="form-control" 
                                               placeholder="Nhập mật khẩu">
                                        <span class="password-toggle" onclick="togglePassword()">
                                            <i id="passwordIcon" class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <p class="form-text">Mật khẩu phải có ít nhất 8 ký tự</p>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Lưu Người Dùng
                                </button>
                                <a href="user.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Hủy Bỏ
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Set active nav item
        const activeNavItem = document.querySelector("#navList li:nth-child(2)");
        if (activeNavItem) {
            activeNavItem.classList.add("active");
        }

        // Avatar preview
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            if (password.length < 8) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 8 ký tự');
                return false;
            }
        });
    </script>

    <style>
        /* Base Styles */
        .admin-add-user {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f9fafb;
            color: #111827;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-wrapper {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        /* Header Styles */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-flex {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .page-title i {
            color: #4f46e5;
            font-size: 1.5rem;
        }

        .page-description {
            color: #374151;
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        .back-button {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 0.5rem;
            background-color: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            color: #4b5563;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #e5e7eb;
        }

        /* Card Styles */
        .card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
            overflow: hidden;
            max-width: 48rem;
            margin: 0 auto;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form Styles */
        .user-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: #9ca3af;
            font-size: 1rem;
            width: 1rem;
            text-align: center;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            background-color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        /* Avatar Upload */
        .avatar-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .avatar-preview-container {
            position: relative;
            display: inline-block;
            margin-bottom: 0.75rem;
        }

        .avatar-preview {
            width: 6rem;
            height: 6rem;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e5e7eb;
        }

        .avatar-change {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 2rem;
            height: 2rem;
            background-color: #4f46e5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            border: 2px solid white;
        }

        .avatar-change i {
            font-size: 0.75rem;
        }

        .avatar-upload-text {
            font-size: 0.875rem;
            color: #6b7280;
            text-align: center;
        }

        .hidden {
            position: absolute;
            left: -9999px;
        }

        /* Password Toggle */
        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            cursor: pointer;
        }

        .password-toggle:hover {
            color: #4b5563;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background-color: #6366f1;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        /* Alert Styles */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .alert-danger {
            background-color: #fef2f2;
            color: #b91c1c;
            border-left: 4px solid #dc2626;
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        @media (min-width: 640px) {
            .form-actions {
                flex-direction: row;
            }
        }

        /* Responsive Adjustments */
        @media (max-width: 640px) {
            .content-wrapper {
                padding: 1rem;
            }
            
            .card {
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
        }
    </style>
</body>
</html>
<?php } else {
    $em = "Vui lòng đăng nhập trước";
    header("Location: login.php?error=" . urlencode($em));
    exit();
}
?>