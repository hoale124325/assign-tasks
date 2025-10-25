<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "DB_connection.php";
    include "app/Model/Notification.php";
    $notifications = get_all_my_notifications($conn, $_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Báo | Hệ Thống Quản Lý</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #e9ecef;
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
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--dark-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .page-title i {
            color: var(--primary-color);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 6px;
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
        }
        
        .btn-primary:hover {
            background-color: #3a5bd9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.2);
        }
        
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }
        
        .notification-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .notification-table thead {
            background-color: #f8f9fa;
            position: sticky;
            top: 0;
        }
        
        .notification-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: var(--dark-color);
            border-bottom: 2px solid var(--border-color);
        }
        
        .notification-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }
        
        .notification-table tr:last-child td {
            border-bottom: none;
        }
        
        .notification-table tr:hover {
            background-color: #f8fafd;
        }
        
        .notification-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .badge-info {
            background-color: rgba(23, 162, 184, 0.1);
            color: var(--info-color);
        }
        
        .badge-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-color);
        }
        
        .badge-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }
        
        .badge-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--secondary-color);
        }
        
        .empty-state i {
            font-size: 48px;
            color: #dee2e6;
            margin-bottom: 15px;
        }
        
        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .table-container {
            max-height: 600px;
            overflow-y: auto;
        }
        
        .refresh-icon {
            transition: transform 0.5s ease;
        }
        
        .refresh-icon.rotate {
            transform: rotate(360deg);
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
            
            .notification-table {
                display: block;
                overflow-x: auto;
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
                        <i class="fas fa-bell"></i>
                        Thông Báo Của Tôi
                    </h1>
                    <button id="refreshBtn" class="btn btn-primary">
                        <i id="refreshIcon" class="fas fa-sync-alt refresh-icon"></i>
                        Làm Mới
                    </button>
                </div>
                
                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success" style="background-color: rgba(40, 167, 69, 0.1); color: var(--success-color); padding: 15px; border-radius: 6px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; border-left: 4px solid var(--success-color);">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars(stripcslashes($_GET['success'])); ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <?php if ($notifications != 0): ?>
                        <div class="table-container">
                            <table class="notification-table">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Nội Dung</th>
                                        <th>Loại</th>
                                        <th>Thời Gian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0; foreach ($notifications as $notification): ?>
                                        <tr>
                                            <td><?= ++$i ?></td>
                                            <td><?= htmlspecialchars($notification['message']) ?></td>
                                            <td>
                                                <?php
                                                $type = $notification['type'];
                                                $badgeClass = '';
                                                $badgeText = $type;
                                                
                                                switch ($type) {
                                                    case 'info':
                                                        $badgeClass = 'badge-info';
                                                        $badgeText = 'Thông tin';
                                                        break;
                                                    case 'warning':
                                                        $badgeClass = 'badge-warning';
                                                        $badgeText = 'Cảnh báo';
                                                        break;
                                                    case 'error':
                                                        $badgeClass = 'badge-danger';
                                                        $badgeText = 'Lỗi';
                                                        break;
                                                    default:
                                                        $badgeClass = 'badge-info';
                                                }
                                                ?>
                                                <span class="notification-badge <?= $badgeClass ?>">
                                                    <?= $badgeText ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $date = new DateTime($notification['date']);
                                                echo $date->format(' d/m/Y');
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <h3>Không có thông báo nào</h3>
                            <p>Khi có thông báo mới, chúng sẽ xuất hiện tại đây</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Active menu item
            const activeItem = document.querySelector("#navList li:nth-child(4)");
            if (activeItem) {
                activeItem.classList.add("active");
            }
            
            // Refresh button animation
            const refreshBtn = document.getElementById('refreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');
            
            refreshBtn.addEventListener('click', function() {
                refreshIcon.classList.add('rotate');
                setTimeout(() => {
                    location.reload();
                }, 500);
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