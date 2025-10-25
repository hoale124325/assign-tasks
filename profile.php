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
    <title>Hồ Sơ Cá Nhân | Hệ Thống Quản Lý</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6cf7;
            --primary-light: #eef2ff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #e9ecef;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #212529;
            line-height: 1.6;
        }
        
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .content-wrapper {
            flex: 1;
            padding: 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-title i {
            color: var(--primary-color);
            font-size: 28px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
            gap: 8px;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 2px 8px rgba(74, 108, 247, 0.3);
        }
        
        .btn-primary:hover {
            background-color: #3a5bd9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.3);
        }
        
        .profile-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .avatar-container {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            background-color: var(--primary-light);
        }
        
        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .avatar-default {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 48px;
        }
        
        .profile-info {
            margin-left: 25px;
        }
        
        .profile-name {
            font-size: 22px;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
        }
        
        .profile-role {
            font-size: 16px;
            color: var(--secondary-color);
            background-color: var(--primary-light);
            padding: 4px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .detail-item {
            padding: 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .detail-item:hover {
            background-color: var(--primary-light);
        }
        
        .detail-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .detail-value {
            font-size: 16px;
            color: var(--dark-color);
            font-weight: 500;
            padding-left: 26px;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
                padding-bottom: 15px;
            }
            
            .profile-info {
                margin-left: 0;
                margin-top: 20px;
            }
            
            .profile-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <?php include "inc/nav.php" ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include "inc/header.php" ?>
            
            <!-- Content -->
            <div class="content-wrapper">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-user-circle"></i>
                        Hồ Sơ Cá Nhân
                    </h1>
                    <a href="edit_profile.php" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Chỉnh Sửa
                    </a>
                </div>
                
                <div class="profile-card fade-in">
                    <div class="profile-header">
                        <div class="avatar-container">
                            <?php if (!empty($user['avatar'])): ?>
                                <img src="<?php echo htmlspecialchars($user['avatar']); ?>" alt="Avatar" class="avatar-img">
                            <?php else: ?>
                                <div class="avatar-default">
                                    <i class="fas fa-user"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="profile-info">
                            <h2 class="profile-name"><?php echo htmlspecialchars($user['full_name'] ?? 'Chưa cập nhật'); ?></h2>
                            <span class="profile-role"><?php echo htmlspecialchars($user['role'] ?? 'Chưa xác định'); ?></span>
                        </div>
                    </div>
                    
                    <div class="profile-details">
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-user"></i> Họ và Tên
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['full_name'] ?? 'Chưa cập nhật'); ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-at"></i> Tên Đăng Nhập
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['username'] ?? 'Chưa cập nhật'); ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-envelope"></i> Email
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['email'] ?? 'Chưa cập nhật'); ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-phone"></i> Số Điện Thoại
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['phone_number'] ?? 'Chưa cập nhật'); ?></div>
                        </div>
                        
                        <div class="detail-item">
                            <div class="detail-label">
                                <i class="fas fa-user-tag"></i> Vai Trò
                            </div>
                            <div class="detail-value"><?php echo htmlspecialchars($user['role'] ?? 'Chưa xác định'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Active menu item
            const activeItem = document.querySelector("#navList li:nth-child(3)");
            if (activeItem) {
                activeItem.classList.add("active");
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