<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";

    // Thêm tham số $include_completed dựa trên query string
    $include_completed = isset($_GET['show_completed']) && $_GET['show_completed'] == 'true';
    $tasks = get_all_tasks_by_id($conn, $_SESSION['id'], $include_completed);

    // Kiểm tra quyền truy cập
    if ($_SESSION['role'] != "employee") {
        header("Location: tasks.php"); // Redirect admin về tasks.php
        exit();
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhiệm Vụ Của Tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .table-container {
            max-height: 600px;
            overflow-y: auto;
        }
        .table-header {
            position: sticky;
            top: 0;
            background-color: #f9fafb;
            z-index: 10;
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .truncate-text {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .nowrap {
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <?php include "inc/nav.php" ?>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <?php include "inc/header.php" ?>
            
            <!-- Main Section -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                <div class="container mx-auto">
                    <h1 class="text-2xl font-bold text-gray-800 mb-6">Nhiệm Vụ Của Tôi</h1>
                    
                    <!-- Bộ lọc hiển thị nhiệm vụ hoàn thành -->
                    <div class="mb-4">
                        <a href="?show_completed=true" class="text-blue-600 hover:text-blue-800 <?php echo $include_completed ? 'font-bold' : ''; ?>">Hiển thị tất cả (bao gồm hoàn thành)</a>
                        <a href="?show_completed=false" class="ml-4 text-blue-600 hover:text-blue-800 <?php echo !$include_completed ? 'font-bold' : ''; ?>">Chỉ hiển thị chưa hoàn thành</a>
                    </div>

                    <!-- Success Message -->
                    <?php if (isset($_GET['success'])) { ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 fade-in" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars(stripcslashes($_GET['success'])); ?>
                        </div>
                    <?php } ?>
                    
                    <!-- Tasks Table -->
                    <?php if ($tasks != 0) { ?>
                        <div class="table-container bg-white shadow-md rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="table-header">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider nowrap">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider nowrap">Tiêu Đề</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider nowrap">Mô Tả</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider nowrap">Trạng Thái</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider nowrap">Hạn chót</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider nowrap">Tệp</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider nowrap">File Hoàn Thành</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider nowrap">Hành Động</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $i = 0; foreach ($tasks as $task) { ?>
                                        <tr class="hover:bg-gray-50 fade-in">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?= ++$i ?></td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 max-w-xs truncate"><?= htmlspecialchars($task['title']) ?></td>
                                            <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate-text"><?= htmlspecialchars($task['description']) ?></td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                <?php 
                                                    if ($task['status'] == "pending") {
                                                        echo "<span class='inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800'>Đang Chờ</span>";
                                                    } elseif ($task['status'] == "in_progress") {
                                                        echo "<span class='inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800'>Đang Làm</span>";
                                                    } elseif ($task['status'] == "completed") {
                                                        echo "<span class='inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800'>Xong</span>";
                                                    }
                                                ?>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?= !empty($task['due_date']) ? date('d/m/Y', strtotime($task['due_date'])) : 'Không hạn' ?></td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                <?php if (!empty($task['attachment'])) { ?>
                                                    <a href="download.php?file=<?= urlencode($task['attachment']) ?>&task_id=<?= htmlspecialchars($task['id']) ?>" class="text-indigo-600 hover:text-indigo-900 truncate block max-w-xs" title="<?= basename(htmlspecialchars($task['attachment'])) ?>">
                                                        <i class="fas fa-paperclip"></i> <?= basename(htmlspecialchars($task['attachment'])) ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-gray-400">Không có</span>
                                                <?php } ?>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                <?php if (!empty($task['completed_file'])) { ?>
                                                    <a href="download.php?file=<?= urlencode($task['completed_file']) ?>&task_id=<?= htmlspecialchars($task['id']) ?>" class="text-green-600 hover:text-green-900 truncate block max-w-xs" title="<?= basename(htmlspecialchars($task['completed_file'])) ?>">
                                                        <i class="fas fa-file-alt"></i> <?= basename(htmlspecialchars($task['completed_file'])) ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-gray-400">Chưa có</span>
                                                <?php } ?>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                <a href="edit-task-employee.php?id=<?= htmlspecialchars($task['id']) ?>" class="text-indigo-600 hover:text-indigo-900 nowrap"><i class="fas fa-edit"></i> Sửa</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="bg-white shadow-md rounded-lg p-6 text-center">
                            <h3 class="text-lg font-medium text-gray-900">Không có nhiệm vụ nào</h3>
                            <p class="mt-2 text-sm text-gray-500">Hiện tại bạn chưa có nhiệm vụ nào được giao.</p>
                        </div>
                    <?php } ?>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const active = document.querySelector("#navList li:nth-child(2)");
            if (active) {
                active.classList.add("bg-indigo-100", "text-indigo-700");
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