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
    <title>Chỉnh Sửa Nhiệm Vụ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(145deg, #f4f5f7 0%, #e2e8f0 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            color: #1a202c;
        }

        .body {
            display: flex;
            min-height: calc(100vh - 80px);
        }

        .section-1 {
            flex: 1;
            max-width: 800px;
            margin: 40px auto;
            padding: 32px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }

        .title {
            font-size: 32px;
            font-weight: 700;
            color: #2d3748;
            margin: 0 0 32px 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 2px solid #805ad5;
            padding-bottom: 12px;
        }

        .title a {
            font-size: 16px;
            font-weight: 600;
            color: #805ad5;
            text-decoration: none;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .title a:hover {
            color: #6b46c1;
            transform: translateX(4px);
        }

        .title i {
            margin-right: 12px;
            color: #805ad5;
            font-size: 36px;
        }

        .form-1 {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .input-holder {
            display: flex;
            flex-direction: column;
            gap: 12px;
            position: relative;
        }

        .input-holder label {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .input-holder label i {
            color: #805ad5;
            font-size: 18px;
        }

        .input-1 {
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            color: #2d3748;
            width: 100%;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .input-1:focus {
            outline: none;
            border-color: #805ad5;
            box-shadow: 0 0 0 4px rgba(128, 90, 213, 0.15);
            background: #ffffff;
        }

        textarea.input-1 {
            resize: vertical;
            min-height: 120px;
            max-height: 300px;
        }

        select.input-1 {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234a5568'%3E%3Cpath d='M7 10l5 5 5-5H7z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .edit-btn {
            padding: 14px 32px;
            background: #805ad5;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            align-self: flex-start;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-btn:hover {
            background: #6b46c1;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 70, 193, 0.4);
        }

        .edit-btn i {
            font-size: 18px;
        }

        .danger, .success {
            padding: 16px 24px;
            border-radius: 8px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .danger {
            background: #fef1f1;
            color: #c53030;
            border-left: 4px solid #c53030;
        }

        .success {
            background: #e6fffa;
            color: #2f855a;
            border-left: 4px solid #2f855a;
        }

        .danger i, .success i {
            font-size: 20px;
        }

        @media (max-width: 768px) {
            .section-1 {
                margin: 24px 16px;
                padding: 24px;
            }

            .title {
                font-size: 24px;
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .title a {
                font-size: 14px;
            }

            .input-1 {
                font-size: 14px;
                padding: 12px;
            }

            .input-holder label {
                font-size: 14px;
            }

            .edit-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .section-1 {
                margin: 16px;
                padding: 16px;
            }

            .title {
                font-size: 20px;
            }

            .title i {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">
                <span><i class="fas fa-edit"></i> Chỉnh Sửa Nhiệm Vụ</span>
                <a href="tasks.php"><i class="fas fa-list"></i> Danh Sách Nhiệm Vụ</a>
            </h4>
            <form class="form-1" method="POST" action="app/update-task.php">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="danger" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo stripcslashes($_GET['error']); ?>
                    </div>
                <?php } ?>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="success" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <?php echo stripcslashes($_GET['success']); ?>
                    </div>
                <?php } ?>

                <div class="input-holder">
                    <label for="title"><i class="fas fa-heading"></i> Tiêu Đề</label>
                    <input type="text" name="title" id="title" class="input-1" placeholder="Nhập tiêu đề nhiệm vụ" value="<?=htmlspecialchars($task['title'])?>" required>
                </div>
                <div class="input-holder">
                    <label for="description"><i class="fas fa-align-left"></i> Mô Tả</label>
                    <textarea name="description" id="description" rows="6" class="input-1" placeholder="Nhập mô tả nhiệm vụ" required><?=htmlspecialchars($task['description'])?></textarea>
                </div>
                <div class="input-holder">
                    <label for="due_date"><i class="fas fa-calendar-alt"></i> Ngày Hết Hạn</label>
                    <input type="date" name="due_date" id="due_date" class="input-1" value="<?=htmlspecialchars($task['due_date'] ?? '')?>">
                </div>
                <div class="input-holder">
                    <label for="assigned_to"><i class="fas fa-user"></i> Phân Công Cho</label>
                    <?php if ($users != 0 && !empty($users)) { ?>
                        <select name="assigned_to" id="assigned_to" class="input-1" required>
                            <option value="0">Chọn Nhân Viên</option>
                            <?php foreach ($users as $user) {
                                $selected = $task['assigned_to'] == $user['id'] ? 'selected' : '';
                            ?>
                                <option value="<?= $user['id'] ?>" <?= $selected ?>><?= htmlspecialchars($user['full_name']) ?></option>
                            <?php } ?>
                        </select>
                    <?php } else { ?>
                        <p class="danger" role="alert"><i class="fas fa-exclamation-circle"></i> Không có nhân viên nào để phân công.</p>
                    <?php } ?>
                </div>
                <div class="input-holder">
                    <label for="status"><i class="fas fa-check-circle"></i> Trạng Thái</label>
                    <select name="status" id="status" class="input-1" required>
                        <option value="pending" <?= $task['status'] == 'pending' ? 'selected' : '' ?>>Đang Chờ</option>
                        <option value="in_progress" <?= $task['status'] == 'in_progress' ? 'selected' : '' ?>>Đang Thực Hiện</option>
                        <option value="completed" <?= $task['status'] == 'completed' ? 'selected' : '' ?>>Hoàn Thành</option>
                    </select>
                </div>
                <input type="hidden" name="id" value="<?=htmlspecialchars($task['id'])?>">
                <button class="edit-btn"><i class="fas fa-save"></i> Cập Nhật Nhiệm Vụ</button>
            </form>
        </section>
    </div>

    <script type="text/javascript">
        const active = document.querySelector("#navList li:nth-child(4)");
        if (active) {
            active.classList.add("active");
        }
    </script>
</body>
</html>
<?php } else { 
    $em = "Vui lòng đăng nhập trước";
    header("Location: login.php?error=" . urlencode($em));
    exit();
}