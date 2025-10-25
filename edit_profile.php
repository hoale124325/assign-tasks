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
    <title>Chỉnh Sửa Hồ Sơ | Hệ Thống Quản Lý Nhân Sự</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --error-color: #ef4444;
            --success-color: #10b981;
        }
        
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 2.5rem;
        }
        
        .input-field {
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            width: 100%;
        }
        
        .input-field:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            outline: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            transition: all 0.3s ease;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6b7280;
        }
        
        .password-container {
            position: relative;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include "inc/nav.php" ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <?php include "inc/header.php" ?>
            
            <!-- Main Section -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto p-6">
                <div class="container mx-auto px-4">
                    <!-- Breadcrumb and Title -->
                    <div class="mb-8">
                        <nav class="flex mb-4" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                               
                                <li>
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                        <a href="profile.php" class="text-gray-700 hover:text-blue-600 text-sm font-medium ml-3">Hồ sơ cá nhân</a>
                                    </div>
                                </li>
                                <li aria-current="page">
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                                        <span class="text-blue-600 text-sm font-medium ml-3">Chỉnh sửa hồ sơ</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        
                        <div class="flex justify-between items-center">
                            <h1 class="text-1xl md:text-1xl font-bold text-gray-800 flex items-center gap-3">
                                <i class="fas fa-user-edit text-blue-600"></i>
                                Chỉnh Sửa Hồ Sơ Cá Nhân
                            </h1>
                            <a href="profile.php" class="btn-primary text-white flex items-center gap-2 text-sm">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    
                    <!-- Form Container -->
                    <div class="form-container fade-in">
                        <form method="POST" action="app/update-profile.php" class="space-y-6" id="profileForm">
                            <!-- Status Messages -->
                            <?php if (isset($_GET['error'])): ?>
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-start gap-3 text-red-700">
                                    <i class="fas fa-exclamation-circle mt-1"></i>
                                    <div>
                                        <h3 class="font-medium">Có lỗi xảy ra</h3>
                                        <p class="text-sm"><?php echo htmlspecialchars(stripcslashes($_GET['error'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (isset($_GET['success'])): ?>
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-start gap-3 text-green-700">
                                    <i class="fas fa-check-circle mt-1"></i>
                                    <div>
                                        <h3 class="font-medium">Thành công</h3>
                                        <p class="text-sm"><?php echo htmlspecialchars(stripcslashes($_GET['success'])); ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Personal Information Section -->
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-id-card text-blue-600"></i>
                                    Thông tin cá nhân
                                </h2>
                                
                                <div class="space-y-4">
                                    <!-- Full Name -->
                                    <div>
                                        <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">
                                            Họ và tên <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" id="full_name" name="full_name" required 
                                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                                               class="input-field"
                                               placeholder="Nhập họ và tên đầy đủ">
                                    </div>
                                    
                                    <!-- Email (readonly) -->
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                            Email
                                        </label>
                                        <input type="email" id="email" name="email" readonly
                                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                               class="input-field bg-gray-100 cursor-not-allowed">
                                    </div>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div>
                                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <i class="fas fa-lock text-blue-600"></i>
                                    Bảo mật tài khoản
                                </h2>
                                
                                <div class="space-y-4">
                                    <!-- Current Password -->
                                    <div class="password-container">
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            Mật khẩu hiện tại <span class="text-red-500">*</span>
                                        </label>
                                        <input type="password" id="password" name="password" required 
                                               class="input-field pr-10"
                                               placeholder="Nhập mật khẩu hiện tại">
                                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                                    </div>
                                    
                                    <!-- New Password -->
                                    <div class="password-container">
                                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                                            Mật khẩu mới
                                        </label>
                                        <input type="password" id="new_password" name="new_password" 
                                               class="input-field pr-10"
                                               placeholder="Nhập mật khẩu mới (nếu muốn đổi)">
                                        <i class="fas fa-eye password-toggle" id="toggleNewPassword"></i>
                                        <p class="text-xs text-gray-500 mt-1">Mật khẩu phải có ít nhất 8 ký tự</p>
                                    </div>
                                    
                                    <!-- Confirm Password -->
                                    <div class="password-container">
                                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">
                                            Xác nhận mật khẩu mới
                                        </label>
                                        <input type="password" id="confirm_password" name="confirm_password" 
                                               class="input-field pr-10"
                                               placeholder="Nhập lại mật khẩu mới">
                                        <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="pt-4">
                                <button type="submit" class="btn-primary text-white w-full flex items-center justify-center gap-2 py-3">
                                    <i class="fas fa-save"></i> Lưu thay đổi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Highlight active nav item
            const activeNavItem = document.querySelector("#navList li:nth-child(3)");
            if (activeNavItem) {
                activeNavItem.classList.add("bg-blue-100", "text-blue-700");
            }

            // Password toggle functionality
            const setupPasswordToggle = (toggleId, inputId) => {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                
                if (toggle && input) {
                    toggle.addEventListener('click', () => {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        toggle.classList.toggle('fa-eye');
                        toggle.classList.toggle('fa-eye-slash');
                    });
                }
            };
            
            setupPasswordToggle('togglePassword', 'password');
            setupPasswordToggle('toggleNewPassword', 'new_password');
            setupPasswordToggle('toggleConfirmPassword', 'confirm_password');

            // Form validation
            const form = document.getElementById('profileForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const password = document.getElementById('password').value;
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    
                    let isValid = true;
                    
                    // Validate current password
                    if (password.length < 8) {
                        alert('Mật khẩu hiện tại phải có ít nhất 8 ký tự.');
                        isValid = false;
                    }
                    
                    // Validate new password if provided
                    if (newPassword) {
                        if (newPassword.length < 8) {
                            alert('Mật khẩu mới phải có ít nhất 8 ký tự.');
                            isValid = false;
                        }
                        
                        if (newPassword !== confirmPassword) {
                            alert('Mật khẩu mới và xác nhận mật khẩu không khớp.');
                            isValid = false;
                        }
                    }
                    
                    if (!isValid) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>
<?php } else { 
    $em = "Vui lòng đăng nhập để tiếp tục";
    header("Location: login.php?error=" . urlencode($em));
    exit();
}
?>