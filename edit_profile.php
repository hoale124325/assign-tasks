<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "employee") {
    include "DB_connection.php";
    include "app/Model/User.php";
    $user = get_user_by_id($conn, $_SESSION['id']);
    if (!$user) {
        $em = "Không tìm thấy thông tin người dùng.";
        header("Location: login.php?error=" . urlencode($em));
        exit();
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Hồ Sơ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .form-container {
            max-width: 500px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 2rem;
        }
        .input-field:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-100 to-gray-200 min-h-screen font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include "inc/nav.php" ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <?php include "inc/header.php" ?>
            
            <!-- Main Section -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                <div class="container mx-auto">
                    <div class="flex justify-between items-center mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                            <i class="fas fa-user-edit"></i>
                            Chỉnh Sửa Hồ Sơ
                        </h1>
                        <a href="profile.php" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i> Quay Về Hồ Sơ
                        </a>
                    </div>
                    
                    <!-- Form Container -->
                    <div class="form-container fade-in">
                        <form method="POST" action="app/update-profile.php" class="space-y-6">
                            <!-- Messages -->
                            <?php if (isset($_GET['error'])): ?>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center gap-3 text-red-700">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?php echo htmlspecialchars(stripcslashes($_GET['error'])); ?>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_GET['success'])): ?>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3 text-green-700">
                                    <i class="fas fa-check-circle"></i>
                                    <?php echo htmlspecialchars(stripcslashes($_GET['success'])); ?>
                                </div>
                            <?php endif; ?>

                            <!-- Full Name -->
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-user mr-2"></i> Họ và Tên *
                                </label>
                                <input type="text" id="full_name" name="full_name" required 
                                       value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg input-field transition-all duration-200"
                                       placeholder="Nhập họ và tên">
                            </div>

                            <!-- Old Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-lock mr-2"></i> Mật Khẩu Cũ *
                                </label>
                                <input type="password" id="password" name="password" required 
                                       value="**********"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg input-field transition-all duration-200"
                                       placeholder="Nhập mật khẩu cũ">
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-lock-open mr-2"></i> Mật Khẩu Mới
                                </label>
                                <input type="password" id="new_password" name="new_password" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg input-field transition-all duration-200"
                                       placeholder="Nhập mật khẩu mới">
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-check-double mr-2"></i> Xác Nhận Mật Khẩu
                                </label>
                                <input type="password" id="confirm_password" name="confirm_password" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg input-field transition-all duration-200"
                                       placeholder="Xác nhận mật khẩu">
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Thay Đổi
                            </button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const active = document.querySelector("#navList li:nth-child(3)");
            if (active) {
                active.classList.add("bg-blue-100", "text-blue-700");
            }

            // Basic form validation
            document.querySelector('form').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const newPassword = document.getElementById('new_password').value;
                const confirmPassword = document.getElementById('confirm_password').value;

                if (password === '**********' || password.length < 6) {
                    e.preventDefault();
                    alert('Mật khẩu cũ phải hợp lệ (ít nhất 6 ký tự).');
                    return;
                }
                if (newPassword && newPassword.length < 6) {
                    e.preventDefault();
                    alert('Mật khẩu mới phải ít nhất 6 ký tự.');
                    return;
                }
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Mật khẩu mới và xác nhận không khớp.');
                    return;
                }
            });
        });
    </script>
</body>
</html>
<?php } else { 
    $em = "Vui lòng đăng nhập trước";
    header("Location: login.php?error=" . urlencode($em));
    exit();
}
?>