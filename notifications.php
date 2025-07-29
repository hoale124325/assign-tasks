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
    <title>Thông Báo</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .notification-container {
            max-width: 900px;
            margin: 0 auto;
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }
        .table-container {
            max-height: 600px;
            overflow-y: auto;
        }
        .table-header {
            position: sticky;
            top: 0;
            background: #ffffff;
            z-index: 10;
        }
        .notification-row:hover {
            background-color: #f9fafb;
            transition: background-color 0.2s ease;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .status-tag {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        .refresh-btn {
            transition: transform 0.2s ease, background-color 0.2s ease;
        }
        .refresh-btn:hover {
            transform: rotate(360deg);
            background-color: #3b82f6;
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
                            <i class="fas fa-bell"></i>
                            Tất Cả Thông Báo
                        </h1>
                        <button onclick="location.reload();" class="refresh-btn inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:text-white">
                            <i class="fas fa-sync-alt mr-2"></i> Làm Mới
                        </button>
                    </div>
                    
                    <!-- Success Message -->
                    <?php if (isset($_GET['success'])): ?>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center gap-3 text-green-700 fade-in">
                            <i class="fas fa-check-circle"></i>
                            <?php echo htmlspecialchars(stripcslashes($_GET['success'])); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Notification Table -->
                    <div class="notification-container fade-in">
                        <?php if ($notifications != 0): ?>
                            <div class="table-container">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="table-header">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">STT</th>
                                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Thông Điệp</th>
                                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php $i = 0; foreach ($notifications as $notification): ?>
                                            <tr class="notification-row">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= ++$i ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo htmlspecialchars($notification['message']); ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <?php
                                                    $type = $notification['type'];
                                                    $tagClass = '';
                                                    $tagText = $type;
                                                    if ($type == "info") {
                                                        $tagClass = "bg-blue-100 text-blue-800";
                                                        $tagText = "Thông Tin";
                                                    } elseif ($type == "warning") {
                                                        $tagClass = "bg-yellow-100 text-yellow-800";
                                                        $tagText = "Cảnh Báo";
                                                    } elseif ($type == "error") {
                                                        $tagClass = "bg-red-100 text-red-800";
                                                        $tagText = "Lỗi";
                                                    }
                                                    ?>
                                                    <span class="status-tag <?= $tagClass ?>"><?= htmlspecialchars($tagText) ?></span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php
                                                    $date = new DateTime($notification['date']);
                                                    echo $date->format('d/m/Y H:i');
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12 text-gray-500">
                                <i class="fas fa-bell-slash text-4xl mb-4"></i>
                                <h3 class="text-lg font-medium">Bạn không có thông báo nào</h3>
                                <p class="text-sm">Hãy kiểm tra lại sau!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const active = document.querySelector("#navList li:nth-child(4)");
            if (active) {
                active.classList.add("bg-blue-100", "text-blue-700");
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