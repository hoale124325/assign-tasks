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
    <title>Thêm Người Dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glass-effect { backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.9); }
        .input-focus:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
        .file-upload { position: relative; overflow: hidden; display: inline-block; cursor: pointer; }
        .file-upload input[type=file] { position: absolute; left: -9999px; }
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
                                <i class="fas fa-user-plus text-white text-lg"></i>
                            </div>
                            Thêm Người Dùng Mới
                        </h1>
                        <p class="text-gray-600 mt-1">Điền thông tin để tạo tài khoản người dùng mới</p>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <?php if (isset($_GET['error'])) { ?>
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6 flex items-center gap-3">
                    <div class="w-8 h-8 bg-red-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-sm"></i>
                    </div>
                    <span class="text-red-700 font-medium"><?php echo htmlspecialchars(stripcslashes($_GET['error'])); ?></span>
                </div>
            <?php } ?>

            <!-- Form Card -->
            <div class="max-w-2xl mx-auto">
                <div class="glass-effect rounded-2xl shadow-xl p-8">
                    <form method="POST" enctype="multipart/form-data" class="space-y-6">
                        <!-- Avatar Upload -->
                        <div class="flex flex-col items-center mb-8">
                            <div class="relative mb-4">
                                <img id="avatarPreview" src="img/default-avatar.png" alt="Avatar Preview" class="avatar-preview">
                                <div class="absolute -bottom-2 -right-2 w-8 h-8 gradient-bg rounded-full flex items-center justify-center cursor-pointer" onclick="document.getElementById('avatar').click()">
                                    <i class="fas fa-camera text-white text-sm"></i>
                                </div>
                            </div>
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                            <p class="text-sm text-gray-500">Nhấp vào biểu tượng máy ảnh để tải lên avatar</p>
                        </div>

                        <!-- Form Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Họ và Tên -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-user mr-2 text-gray-400"></i>Họ và Tên *
                                </label>
                                <input type="text" name="full_name" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all duration-200"
                                       placeholder="Nhập họ và tên đầy đủ">
                            </div>

                            <!-- Tên Đăng Nhập -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-at mr-2 text-gray-400"></i>Tên Đăng Nhập *
                                </label>
                                <input type="text" name="username" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all duration-200"
                                       placeholder="username">
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-envelope mr-2 text-gray-400"></i>Email *
                                </label>
                                <input type="email" name="email" required 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus transition-all duration-200"
                                       placeholder="example@email.com">
                            </div>

                            <!-- Số Điện Thoại -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-phone mr-2 text-gray-400"></i>Số Điện Thoại
                                </label>
                                <input type="text" name="phone_number" 
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
                                    <option value="employee">
                                        <i class="fas fa-user-tie"></i> Nhân Viên
                                    </option>
                                    <option value="admin">
                                        <i class="fas fa-crown"></i> Quản Trị Viên
                                    </option>
                                </select>
                            </div>

                            <!-- Mật Khẩu -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-lock mr-2 text-gray-400"></i>Mật Khẩu *
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" required 
                                           class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl input-focus transition-all duration-200"
                                           placeholder="Nhập mật khẩu mạnh">
                                    <button type="button" onclick="togglePassword()" 
                                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i id="passwordIcon" class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Mật khẩu nên có ít nhất 8 ký tự</p>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                            <button type="submit" 
                                    class="flex-1 gradient-bg text-white px-6 py-3 rounded-xl hover:shadow-lg transition-all duration-300 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-plus"></i>
                                Tạo Người Dùng
                            </button>
                            <a href="user.php" 
                               class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-xl hover:bg-gray-300 transition-all duration-300 font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-times"></i>
                                Hủy Bỏ
                            </a>
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
            if (password.length < 3) {
                e.preventDefault();
                alert('Mật khẩu phải có ít nhất 3 ký tự');
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