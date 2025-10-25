<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";
    
    if (!isset($_GET['id'])) {
        header("Location: tasks.php");
        exit();
    }
    $id = $_GET['id'];
    $task = get_task_by_id($conn, $id);

    if ($task == 0) {
        header("Location: tasks.php?error=" . urlencode("Không tìm thấy nhiệm vụ"));
        exit();
    }
    $users = get_all_users($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Nhiệm Vụ | Hệ Thống Quản Lý</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --primary-hover: #5649c0;
            --success-color: #00b894;
            --danger-color: #d63031;
            --warning-color: #fdcb6e;
            --text-dark: #2d3436;
            --text-medium: #636e72;
            --text-light: #b2bec3;
            --bg-light: #f5f6fa;
            --border-color: #dfe6e9;
            --card-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
            line-height: 1.6;
        }

        .body-container {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        .edit-task-container {
            flex: 1;
            padding: 2rem;
            max-width: 900px;
            margin: 2rem auto;
        }

        .task-card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
            border: 1px solid var(--border-color);
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 1.75rem;
            color: var(--text-dark);
            font-weight: 600;
        }

        .page-title i {
            color: var(--primary-color);
            font-size: 1.8rem;
        }

        .back-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .back-link:hover {
            color: var(--primary-hover);
            transform: translateX(-3px);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.95rem;
        }

        .alert-danger {
            background-color: rgba(214, 48, 49, 0.1);
            color: var(--danger-color);
            border-left: 4px solid var(--danger-color);
        }

        .alert-success {
            background-color: rgba(0, 184, 148, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .alert i {
            font-size: 1.2rem;
        }

        .task-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: var(--text-dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-label i {
            color: var(--primary-color);
            width: 20px;
            text-align: center;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            color: var(--text-dark);
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
        }

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23636e72' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 12px;
        }

        .form-actions {
            grid-column: span 2;
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1rem;
        }

        .btn {
            padding: 0.875rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
        }

        .btn-secondary {
            background-color: white;
            color: var(--text-medium);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: #f8f9fa;
            border-color: var(--text-light);
        }

        .status-badge {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-pending {
            background-color: rgba(253, 203, 110, 0.2);
            color: #e17055;
        }

        .status-in_progress {
            background-color: rgba(129, 236, 236, 0.2);
            color: #00cec9;
        }

        .status-completed {
            background-color: rgba(0, 184, 148, 0.2);
            color: var(--success-color);
        }

        @media (max-width: 768px) {
            .task-form {
                grid-template-columns: 1fr;
            }
            
            .form-group.full-width {
                grid-column: span 1;
            }
            
            .form-actions {
                grid-column: span 1;
                flex-direction: column;
            }
            
            .edit-task-container {
                padding: 1rem;
            }
            
            .task-card {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .task-card {
                padding: 1.25rem;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body-container">
        <?php include "inc/nav.php" ?>
        <main class="edit-task-container">
            <div class="task-card">
                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-edit"></i>
                        <span>Chỉnh Sửa Nhiệm Vụ</span>
                    </h1>
                    <a href="tasks.php" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        Quay lại danh sách
                    </a>
                </div>

                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo stripcslashes($_GET['error']); ?>
                    </div>
                <?php } ?>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <?php echo stripcslashes($_GET['success']); ?>
                    </div>
                <?php } ?>

                <form class="task-form" method="POST" action="app/update-task.php">
                    <div class="form-group full-width">
                        <label for="title" class="form-label">
                            <i class="fas fa-heading"></i>
                            Tiêu đề nhiệm vụ
                        </label>
                        <input type="text" name="title" id="title" class="form-control" 
                               placeholder="Nhập tiêu đề nhiệm vụ" 
                               value="<?=htmlspecialchars($task['title'])?>" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Mô tả chi tiết
                        </label>
                        <textarea name="description" id="description" class="form-control" 
                                  rows="5" placeholder="Mô tả chi tiết nhiệm vụ..." required><?=htmlspecialchars($task['description'])?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="due_date" class="form-label">
                            <i class="fas fa-calendar-day"></i>
                            Ngày hết hạn
                        </label>
                        <input type="date" name="due_date" id="due_date" class="form-control" 
                               value="<?=htmlspecialchars($task['due_date'] ?? '')?>">
                    </div>

                    <div class="form-group">
                        <label for="status" class="form-label">
                            <i class="fas fa-tasks"></i>
                            Trạng thái
                        </label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Đang chờ</option>
                            <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>Đang thực hiện</option>
                            <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="assigned_to" class="form-label">
                            <i class="fas fa-user-tag"></i>
                            Phân công cho
                        </label>
                        <?php if ($users != 0 && !empty($users)) { ?>
                            <select name="assigned_to" id="assigned_to" class="form-control" required>
                                <option value="">Chọn nhân viên</option>
                                <?php foreach ($users as $user) {
                                    $selected = $task['assigned_to'] == $user['id'] ? 'selected' : '';
                                ?>
                                    <option value="<?= $user['id'] ?>" <?= $selected ?>>
                                        <?= htmlspecialchars($user['full_name']) ?>
                                    </option>
                                <?php } ?>
                            </select>
                        <?php } else { ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                Không có nhân viên nào để phân công
                            </div>
                        <?php } ?>
                    </div>

                    <input type="hidden" name="id" value="<?=htmlspecialchars($task['id'])?>">

                    <div class="form-actions">
                        <a href="tasks.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Hủy bỏ
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Cập nhật nhiệm vụ
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script type="text/javascript">
        // Highlight active menu item
        const active = document.querySelector("#navList li:nth-child(4)");
        if (active) {
            active.classList.add("active");
        }

        // Enhance date input with today's date as default if empty
        document.addEventListener('DOMContentLoaded', function() {
            const dueDateInput = document.getElementById('due_date');
            if (dueDateInput && !dueDateInput.value) {
                const today = new Date();
                const formattedDate = today.toISOString().substr(0, 10);
                dueDateInput.value = formattedDate;
            }
        });
    </script>
</body>
</html>
<?php } else { 
    $em = "Vui lòng đăng nhập trước";
    header("Location: login.php?error=" . urlencode($em));
    exit();
} ?>