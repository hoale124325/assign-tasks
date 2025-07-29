<?php
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";

    // Lấy thông tin người dùng cần chỉnh sửa
    $user_id = $_GET['id'] ?? 0;
    $user = get_user_by_id($conn, $user_id);
    
    if (!$user) {
        $error = "Không tìm thấy người dùng.";
        header("Location: manage-user.php?error=" . urlencode($error));
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Lấy và kiểm tra dữ liệu từ form
        $full_name = trim($_POST['full_name'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone_number = trim($_POST['phone_number'] ?? null);
        $role = $_POST['role'] ?? 'employee';
        $avatar = $user['avatar']; // Giữ avatar cũ nếu không upload mới

        // Xử lý upload avatar mới nếu có
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $avatar_dir = 'img/';
            $avatar_file = $avatar_dir . basename($_FILES['avatar']['name']);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar_file)) {
                $avatar = $avatar_file;
            }
        }

        // Kiểm tra dữ liệu bắt buộc
        if (empty($full_name) || empty($username) || empty($email)) {
            $error = "Vui lòng điền đầy đủ các trường bắt buộc.";
        } else {
            try {
                // Cập nhật thông tin người dùng
                $data = [$full_name, $username, $email, $phone_number, $role, $avatar, $user_id];
                update_user($conn, $data);
                
                $success = "Cập nhật thông tin người dùng thành công.";
                header("Location: manage-user.php?success=" . urlencode($success));
                exit();
            } catch (Exception $e) {
                $error = "Lỗi khi cập nhật người dùng: " . $e->getMessage();
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Người Dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass-effect { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9); }
        .input-focus:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .avatar-preview { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #e5e7eb; }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-gray-100 min-h-screen">
    <div class="flex">
        <?php include "inc/nav.php" ?>



        
        
        <div class="flex-1 p-4 lg:p-8">
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center gap-4 mb-4">
                    <a href="user.php" class="w-10 h-10 bg-gray-200 rounded-xl flex items-center justify-center hover:bg-gray-300 transition-colors">
                        <i class="fas fa-arrow-left text-gray-600"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-edit text-white text-lg"></i>
                            </div>
                            Chỉnh Sửa Người Dùng
                        </h1>
                        <p class="text-gray-600 mt-1">Cập nhật thông tin cho: <strong><?php echo htmlspecialchars($user['full_name']); ?></strong></p>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <?php if (isset($error)) { ?>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                    </div>
                    <span class="text-red-700 font-medium"><?php echo htmlspecialchars($error); ?></span>
                </div>
            <?php } ?>

            <!-- Form Card -->
            <div class="max-w-2xl mx-auto">
                <div class="glass-effect rounded-2xl shadow-xl p-8">
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <!-- Avatar Upload -->
                        <div class="flex flex-col items-center mb-8">
                            <div class="relative mb-4">
                                <img id="avatarPreview" 
                                     src="<?php echo !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'img/default-avatar.png'; ?>" 
                                     alt="Avatar Preview" class="avatar-preview">
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 gradient-bg rounded-full flex items-center justify-center cursor-pointer" onclick="document.getElementById('avatar').click()">
                                    <i class="fas fa-camera text-white text-sm"></i>
                                </div>
                            </div>
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                            <p class="text-sm text-gray-500">Nhấp vào biểu tượng máy ảnh để thay đổi avatar</p>
                        </div>

                        <!-- Form Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Họ và Tên -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-2 text-gray-400"></i>Họ và Tên *
                                </label>
                                <input type="text" name="full_name" required 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all duration-200"
                                       placeholder="Nhập họ và tên đầy đủ">
                            </div>

                            <!-- Tên Đăng Nhập -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-at mr-2 text-gray-400"></i>Tên Đăng Nhập *
                                </label>
                                <input type="text" name="username" required 
                                       value="<?php echo htmlspecialchars($user['username']); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all duration-200"
                                       placeholder="username">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-gray-400"></i>Email *
                                </label>
                                <input type="email" name="email" required 
                                       value="<?php echo htmlspecialchars($user['email']); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all duration-200"
                                       placeholder="example@email.com">
                            </div>

                            <!-- Số Điện Thoại -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-2 text-gray-400"></i>Số Điện Thoại
                                </label>
                                <input type="text" name="phone_number" 
                                       value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all duration-200"
                                       placeholder="0123456789">
                            </div>

                            <!-- Vai Trò -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user-shield mr-2 text-gray-400"></i>Vai Trò *
                                </label>
                                <select name="role" required 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all duration-200">
                                    <option value="employee" <?php echo $user['role'] == 'employee' ? 'selected' : ''; ?>>
                                        Nhân Viên
                                    </option>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>
                                        Quản Trị Viên
                                    </option>
                                </select>
                            </div>

                            <!-- Thông tin bổ sung -->
                            <div class="md:col-span-2 bg-gray-50 rounded-xl p-4">
                                <h3 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                                    Thông tin bổ sung
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                    <div>
                                        <strong>ID:</strong> #<?php echo htmlspecialchars($user['id']); ?>
                                    </div>
                                    <div>
                                        <strong>Ngày tạo:</strong> <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="flex-1 gradient-bg text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all duration-300 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i>
                                Lưu Thay Đổi
                            </button>
                            <a href="manage-user.php" 
                               class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-xl hover:bg-gray-300 transition-all duration-300 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i>
                                Hủy Bỏ
                            </a>
                        </div>

                        <!-- Password Reset Section -->
                        <div class="pt-6 border-t border-gray-200">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                                        <i class="fas fa-key text-white text-sm"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-yellow-800">Đặt lại mật khẩu</p>
                                        <p class="text-sm text-yellow-700">Gửi liên kết đặt lại mật khẩu cho người dùng</p>
                                    </div>
                                </div>
                                <button type="button" onclick="resetPassword(<?php echo $user['id']; ?>)"
                                        class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors text-sm font-medium">
                                    Đặt lại
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const active = document.querySelector("#navList li:nth-child(2)");
        if (active) {
            active.classList.add("active");
        }

        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatarPreview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function resetPassword(userId) {
            if (confirm('Bạn có chắc chắn muốn đặt lại mật khẩu cho người dùng này không?')) {
                // Gửi request đặt lại mật khẩu
                window.location.href = `reset-password.php?id=${userId}`;
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fullName = document.querySelector('input[name="full_name"]').value.trim();
            const username = document.querySelector('input[name="username"]').value.trim();
            const email = document.querySelector('input[name="email"]').value.trim();
            
            if (!fullName || !username || !email) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ các trường bắt buộc');
                return false;
            }
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