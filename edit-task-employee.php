<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "employee") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";
    
    if (!isset($_GET['id'])) {
        header("Location: my_task.php");
        exit();
    }
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($id === false || $id <= 0) {
        header("Location: my_task.php");
        exit();
    }
    $task = get_task_by_id($conn, $id);

    if ($task == 0 || $task['assigned_to'] != $_SESSION['id']) {
        header("Location: my_task.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Nhiệm Vụ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --danger-color: #dc3545;
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
            background-color: #ffffff;
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
            background-color: #ffffff;
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
        
        .back-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: #ffffff;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 108, 247, 0.1);
            outline: none;
        }
        
        .form-text {
            color: var(--secondary-color);
            font-size: 14px;
            margin-top: 5px;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 15px;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            border: none;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: #ffffff;
        }
        
        .btn-primary:hover {
            background-color: #3a5bd9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 108, 247, 0.2);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }
        
        .task-info {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .task-info-item {
            margin-bottom: 10px;
        }
        
        .task-info-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-right: 5px;
        }
        
        /* Style for file upload */
        .file-upload-wrapper {
            margin-top: 15px;
            border: 1px dashed var(--border-color);
            padding: 20px;
            border-radius: 6px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .file-upload-wrapper:hover {
            border-color: var(--primary-color);
            background-color: rgba(74, 108, 247, 0.05);
        }
        
        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            cursor: pointer;
        }
        
        .file-upload-icon {
            font-size: 24px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .file-upload-text {
            color: var(--secondary-color);
            font-size: 14px;
        }
        
        .file-name {
            margin-top: 10px;
            font-size: 14px;
            color: var(--dark-color);
            word-break: break-all;
        }
        
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .card {
                padding: 20px;
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
                        <i class="fas fa-edit"></i>
                        Chỉnh Sửa Nhiệm Vụ
                    </h1>
                    <a href="my_task.php" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại danh sách
                    </a>
                </div>
                
                <div class="card">
                    <form method="POST" action="app/update-task-employee.php" enctype="multipart/form-data">
                        <?php if (isset($_GET['error'])) { ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i>
                                <?php echo htmlspecialchars(stripcslashes($_GET['error'])); ?>
                            </div>
                        <?php } ?>

                        <?php if (isset($_GET['success'])) { ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <?php echo htmlspecialchars(stripcslashes($_GET['success'])); ?>
                            </div>
                        <?php } ?>
                        
                        <div class="task-info">
                            <div class="task-info-item">
                                <span class="task-info-label">Tiêu đề:</span>
                                <?= htmlspecialchars($task['title'] ?? '') ?>
                            </div>
                            <div class="task-info-item">
                                <span class="task-info-label">Mô tả:</span>
                                <?= htmlspecialchars($task['description'] ?? '') ?>
                            </div>
                            <div class="task-info-item">
                                <span class="task-info-label">Hạn chót:</span>
                                <?= !empty($task['due_date']) ? date('d/m/Y', strtotime($task['due_date'])) : 'Không có' ?>
                            </div>
                            <?php if (!empty($task['completed_file'])): ?>
                            <div class="task-info-item">
                                <span class="task-info-label">File đã gửi:</span>
                                <a href="<?= htmlspecialchars($task['completed_file']) ?>" target="_blank" download>
                                    <i class="fas fa-file-download"></i> Tải xuống
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        
                        <div class="form-group">
                            <label for="status" class="form-label">Trạng thái *</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="pending" <?= ($task['status'] == "pending") ? 'selected' : '' ?>>Đang Chờ</option>
                                <option value="in_progress" <?= ($task['status'] == "in_progress") ? 'selected' : '' ?>>Đang Thực Hiện</option>
                                <option value="completed" <?= ($task['status'] == "completed") ? 'selected' : '' ?>>Hoàn Thành</option>
                            </select>
                        </div>
                        
                        <div class="form-group" id="fileUploadGroup" style="<?= ($task['status'] == 'completed') ? 'display: block;' : 'display: none;' ?>">
                            <label class="form-label">File hoàn thành</label>
                            <div class="file-upload-wrapper">
                                <label for="task_file" class="file-upload-label">
                                    <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                    <span class="file-upload-text">Nhấn để chọn file hoặc kéo thả vào đây</span>
                                    <span id="fileName" class="file-name">
                                        <?= (!empty($task['completed_file'])) ? basename($task['completed_file']) : 'Chưa có file nào được chọn' ?>
                                    </span>
                                </label>
                                <input type="file" name="task_file" id="task_file" class="form-control" style="display: none;">
                            </div>
                            <p class="form-text">Chỉ chấp nhận file PDF, Word, Excel hoặc hình ảnh (tối đa 5MB)</p>
                        </div>
                        
                        <input type="hidden" name="id" value="<?= htmlspecialchars($task['id'] ?? '') ?>">
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Cập Nhật
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Đánh dấu menu active
            var activeItem = document.querySelector("#navList li:nth-child(2)");
            if (activeItem) {
                activeItem.classList.add("active");
            }
            
            // Xử lý hiển thị file upload khi chọn trạng thái Hoàn thành
            document.getElementById('status').addEventListener('change', function() {
                const fileUploadGroup = document.getElementById('fileUploadGroup');
                if (this.value === 'completed') {
                    fileUploadGroup.style.display = 'block';
                } else {
                    fileUploadGroup.style.display = 'none';
                }
            });
            
            // Hiển thị tên file khi chọn file
            document.getElementById('task_file').addEventListener('change', function(e) {
                const fileName = document.getElementById('fileName');
                if (this.files.length > 0) {
                    fileName.textContent = this.files[0].name;
                } else {
                    fileName.textContent = 'Chưa có file nào được chọn';
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
} ?>