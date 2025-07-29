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
    <title>Hồ Sơ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 700px;
            margin: 0 auto;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 1.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        .avatar-wrapper {
            width: 120px;
            height: 120px;
            border: 4px solid #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .avatar-wrapper:hover {
            transform: scale(1.05);
        }
        .profile-info {
            border-left: 1px solid #e5e7eb;
            padding-left: 2rem;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .info-item {
            transition: color 0.2s ease;
        }
        .info-item:hover {
            color: #3b82f6;
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
                        <h1 class="text-4xl font-bold text-gray-800 flex items-center gap-3">
                            <i class="fas fa-user-circle text-2xl"></i>
                            Hồ Sơ Của Tôi
                        </h1>
                        <a href="edit_profile.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-md">
                            <i class="fas fa-edit mr-2"></i> Chỉnh Sửa Hồ Sơ
                        </a>
                    </div>
                    
                    <!-- Profile Section -->
                    <div class="profile-container p-8 fade-in">
                        <!-- Avatar and Basic Info -->
                        <div class="flex items-center mb-8">
                            <div class="avatar-wrapper rounded-full overflow-hidden">
                                <?php if (!empty($user['avatar'])): ?>
                                    <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-user text-gray-500 text-5xl"></i>
                                <?php endif; ?>
                            </div>
                            <div class="profile-info ml-6">
                                <h2 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($user['full_name'] ?? 'Chưa cập nhật'); ?></h2>
                                <p class="text-lg text-gray-600"><?php echo htmlspecialchars($user['role'] ?? 'Chưa xác định'); ?></p>
                            </div>
                        </div>
                        
                        <!-- User Details -->
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="info-item">
                                <dt class="text-sm font-medium text-gray-600 flex items-center">
                                    <i class="fas fa-user mr-2"></i> Họ và Tên
                                </dt>
                                <dd class="text-lg text-gray-800"><?php echo htmlspecialchars($user['full_name'] ?? 'Chưa cập nhật'); ?></dd>
                            </div>
                            <div class="info-item">
                                <dt class="text-sm font-medium text-gray-600 flex items-center">
                                    <i class="fas fa-at mr-2"></i> Tên Đăng Nhập
                                </dt>
                                <dd class="text-lg text-gray-800"><?php echo htmlspecialchars($user['username'] ?? 'Chưa cập nhật'); ?></dd>
                            </div>
                            <div class="info-item">
                                <dt class="text-sm font-medium text-gray-600 flex items-center">
                                    <i class="fas fa-envelope mr-2"></i> Email
                                </dt>
                                <dd class="text-lg text-gray-800"><?php echo htmlspecialchars($user['email'] ?? 'Chưa cập nhật'); ?></dd>
                            </div>
                            <div class="info-item">
                                <dt class="text-sm font-medium text-gray-600 flex items-center">
                                    <i class="fas fa-phone mr-2"></i> Số Điện Thoại
                                </dt>
                                <dd class="text-lg text-gray-800"><?php echo htmlspecialchars($user['phone_number'] ?? 'Chưa cập nhật'); ?></dd>
                            </div>
                            <div class="info-item md:col-span-2">
                                <dt class="text-sm font-medium text-gray-600 flex items-center">
                                    <i class="fas fa-user-tag mr-2"></i> Vai Trò
                                </dt>
                                <dd class="text-lg text-gray-800"><?php echo htmlspecialchars($user['role'] ?? 'Chưa xác định'); ?></dd>
                            </div>
                        </dl>
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
        });
    </script>
</body>
</html>
<?php } else { 
    $em = "Vui lòng đăng nhập trước";
    header("Location: login.php?error=$em");
    exit();
}
?>