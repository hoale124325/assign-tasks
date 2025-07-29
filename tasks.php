<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";
    
    $text = "Tất Cả Nhiệm Vụ";
    if (isset($_GET['due_date']) && $_GET['due_date'] == "Due Today") {
        $text = "Đến Hạn Hôm Nay";
        $tasks = get_all_tasks_due_today($conn);
        $num_task = count_tasks_due_today($conn);
    } else if (isset($_GET['due_date']) && $_GET['due_date'] == "Overdue") {
        $text = "Quá Hạn";
        $tasks = get_all_tasks_overdue($conn);
        $num_task = count_tasks_overdue($conn);
    } else if (isset($_GET['due_date']) && $_GET['due_date'] == "No Deadline") {
        $text = "Không Có Hạn Chót";
        $tasks = get_all_tasks_NoDeadline($conn);
        $num_task = count_tasks_NoDeadline($conn);
    } else {
        $tasks = get_all_tasks($conn);
        $num_task = count_tasks($conn);
    }
    $users = get_all_users($conn);
    
    // Tạo mảng ánh xạ id => full_name
    $user_map = [];
    if ($users != 0 && !empty($users)) {
        foreach ($users as $user) {
            $user_map[$user['id']] = $user['full_name'];
        }
    }
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhiệm Vụ</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .main-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 24px;
        }

        .page-header {
            background: #ffffff;
            border-radius: 12px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            margin: 0 0 24px 0;
            display: flex;
            align-items: center;
            color: #2d3748;
        }

        .page-title i {
            margin-right: 12px;
            color: #805ad5;
            font-size: 32px;
        }

        .filter-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }

        .nav-btn {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .nav-btn i {
            margin-right: 8px;
        }

        .create-btn {
            background: #f56565;
            color: white;
            border: none;
        }

        .create-btn:hover {
            background: #e53e3e;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(245, 101, 101, 0.3);
        }

        .filter-btn {
            background: #ffffff;
            color: #4a5568;
        }

        .filter-btn:hover {
            background: #edf2f7;
            color: #2d3748;
        }

        .current-filter {
            background: #805ad5 !important;
            color: white !important;
            border-color: #805ad5 !important;
        }

        .stats-section {
            background: #ffffff;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .stats-info h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #2d3748;
        }

        .stats-info p {
            margin: 8px 0 0 0;
            color: #718096;
            font-size: 14px;
        }

        .stats-icon {
            font-size: 40px;
            color: #805ad5;
            opacity: 0.9;
        }

        .alert {
            background: #48bb78;
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 8px rgba(72, 187, 120, 0.2);
        }

        .alert i {
            margin-right: 10px;
            font-size: 20px;
        }

        .table-container {
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        .table-header {
            background: #f7fafc;
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #2d3748;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
        }

        .modern-table th {
            background: #f7fafc;
            color: #4a5568;
            padding: 16px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            border-bottom: 1px solid #e2e8f0;
        }

        .modern-table td {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 14px;
        }

        .modern-table tr {
            transition: background 0.2s ease;
        }

        .modern-table tr:hover {
            background: #f7fafc;
        }

        .task-title {
            font-weight: 500;
            color: #2d3748;
        }

        .task-description {
            color: #718096;
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .assigned-user {
            display: inline-flex;
            align-items: center;
            background: #edf2f7;
            color: #4a5568;
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 500;
        }

        .assigned-user i {
            margin-right: 6px;
        }

        .due-date {
            font-weight: 500;
            color: #4a5568;
        }

        .due-date.overdue {
            color: #e53e3e;
            font-weight: 600;
        }

        .due-date.today {
            color: #ed8936;
            font-weight: 600;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .status-pending {
            background: #fed7d7;
            color: #c53030;
        }

        .status-progress {
            background: #feebc8;
            color: #dd6b20;
        }

        .status-completed {
            background: #c6f6d5;
            color: #2f855a;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
        }

        .action-btn i {
            margin-right: 6px;
        }

        .edit-btn {
            background: #805ad5;
            color: white;
        }

        .edit-btn:hover {
            background: #6b46c1;
            transform: translateY(-1px);
        }

        .delete-btn {
            background: #f56565;
            color: white;
        }

        .delete-btn:hover {
            background: #e53e3e;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: #718096;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.6;
        }

        .empty-state h3 {
            font-size: 20px;
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .empty-state p {
            font-size: 14px;
            margin: 0;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 16px;
            }
            
            .page-header {
                padding: 24px;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .filter-nav {
                flex-direction: column;
                align-items: stretch;
            }
            
            .nav-btn {
                justify-content: center;
            }
            
            .stats-section {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }
            
            .modern-table {
                font-size: 13px;
            }
            
            .modern-table th,
            .modern-table td {
                padding: 12px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 6px;
            }
        }

        @media (max-width: 1024px) {
            .modern-table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
        }
    </style>
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    
    <div class="body">
        <?php include "inc/nav.php" ?>
        
        <div class="main-container">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-tasks"></i>
                    Quản Lý Nhiệm Vụ
                </h1>
                
                <div class="filter-nav">
                    <a href="create_task.php" class="nav-btn create-btn">
                        <i class="fas fa-plus-circle"></i>
                        Tạo Nhiệm Vụ Mới
                    </a>
                    
                    <a href="tasks.php" class="nav-btn filter-btn <?= !isset($_GET['due_date']) ? 'current-filter' : '' ?>">
                        <i class="fas fa-list"></i>
                        Tất Cả Nhiệm Vụ
                    </a>
                    
                    <a href="tasks.php?due_date=Due Today" class="nav-btn filter-btn <?= (isset($_GET['due_date']) && $_GET['due_date'] == 'Due Today') ? 'current-filter' : '' ?>">
                        <i class="fas fa-clock"></i>
                        Đến Hạn Hôm Nay
                    </a>
                    
                    <a href="tasks.php?due_date=Overdue" class="nav-btn filter-btn <?= (isset($_GET['due_date']) && $_GET['due_date'] == 'Overdue') ? 'current-filter' : '' ?>">
                        <i class="fas fa-exclamation-triangle"></i>
                        Quá Hạn
                    </a>
                    
                    <a href="tasks.php?due_date=No Deadline" class="nav-btn filter-btn <?= (isset($_GET['due_date']) && $_GET['due_date'] == 'No Deadline') ? 'current-filter' : '' ?>">
                        <i class="fas fa-calendar-times"></i>
                        Không Có Hạn Chót
                    </a>
                </div>
            </div>

            <div class="stats-section">
                <div class="stats-info">
                    <h2><?=$text?></h2>
                    <p>Tổng cộng <?=$num_task?> nhiệm vụ</p>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-chart-pie"></i>
                </div>
            </div>

            <?php if (isset($_GET['success'])) { ?>
                <div class="alert">
                    <i class="fas fa-check-circle"></i>
                    <?php echo stripcslashes($_GET['success']); ?>
                </div>
            <?php } ?>

            <?php if ($tasks != 0) { ?>
                <div class="table-container">
                    <div class="table-header">
                        <h3><i class="fas fa-table"></i> Danh Sách Nhiệm Vụ</h3>
                    </div>
                    
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tiêu Đề</th>
                                <th>Mô Tả</th>
                                <th>Phân Công</th>
                                <th>Hạn Chót</th>
                                <th>Trạng Thái</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=0; foreach ($tasks as $task) { ?>
                            <tr>
                                <td><strong><?=++$i?></strong></td>
                                <td>
                                    <div class="task-title"><?=htmlspecialchars($task['title'])?></div>
                                </td>
                                <td>
                                    <div class="task-description" title="<?=htmlspecialchars($task['description'])?>">
                                        <?=htmlspecialchars($task['description'])?>
                                    </div>
                                </td>
                                <td>
                                    <span class="assigned-user">
                                        <i class="fas fa-user"></i>
                                        <?= isset($user_map[$task['assigned_to']]) ? htmlspecialchars($user_map[$task['assigned_to']]) : 'Không xác định' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                        if (empty($task['due_date']) || $task['due_date'] == '0000-00-00') {
                                            echo '<span class="due-date">Không có hạn chót</span>';
                                        } else {
                                            $due_date = date('d/m/Y', strtotime($task['due_date']));
                                            $today = date('Y-m-d');
                                            $class = '';
                                            if ($task['due_date'] < $today) {
                                                $class = 'overdue';
                                            } else if ($task['due_date'] == $today) {
                                                $class = 'today';
                                            }
                                            echo '<span class="due-date '.$class.'">'.$due_date.'</span>';
                                        }
                                    ?>
                                </td>
                                <td>
                                    <?php 
                                        $status_class = '';
                                        $status_text = '';
                                        if ($task['status'] == "pending") {
                                            $status_class = 'status-pending';
                                            $status_text = 'Đang Chờ';
                                        } elseif ($task['status'] == "in_progress") {
                                            $status_class = 'status-progress';
                                            $status_text = 'Đang Thực Hiện';
                                        } elseif ($task['status'] == "completed") {
                                            $status_class = 'status-completed';
                                            $status_text = 'Hoàn Thành';
                                      
                                        }
                                    ?>
                                    <span class="status-badge <?=htmlspecialchars($status_class)?>">
                                        <?=htmlspecialchars($status_text)?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit-task.php?id=<?=htmlspecialchars($task['id'])?>" class="action-btn edit-btn">
                                            <i class="fas fa-edit"></i>
                                            Sửa
                                        </a>
                                        <a href="delete-task.php?id=<?=htmlspecialchars($task['id'])?>" class="action-btn delete-btn" 
                                           onclick="return confirm('Bạn có chắc muốn xóa nhiệm vụ này?')">
                                            <i class="fas fa-trash"></i>
                                            Xóa
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="table-container">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Không có nhiệm vụ nào</h3>
                        <p>Hiện tại chưa có nhiệm vụ nào trong danh mục này</p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

    <script type="text/javascript">
        const active = document.querySelector("#navList li:nth-child(4)");
        if (active) {
            active.classList.add("active");
        }

        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Bạn có chắc chắn muốn xóa nhiệm vụ này không?')) {
                    e.preventDefault();
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
}