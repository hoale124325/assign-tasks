<?php
// Start session
session_start();

// Database connection
$sName = "localhost";
$uName = "root";
$pass = "";
$db_name = "task_management_db";

try {
    $conn = new PDO("mysql:host=$sName;dbname=$db_name", $uName, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}

// Initialize session variables if not set
$greeting = isset($_SESSION['greeting']) ? $_SESSION['greeting'] : 'Chào';
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';
$num_users = 0;
$num_task = 0;
$num_my_task = 0;

// Calculate stats for greeting section
try {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $num_users = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'employee'")->fetchColumn();
        $num_task = $conn->query("SELECT COUNT(*) FROM tasks")->fetchColumn();
    } else {
        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = ?");
        $stmt->execute([$user_id]);
        $num_my_task = $stmt->fetchColumn();
    }
} catch (PDOException $e) {
    // Log error or handle silently
}

// Prevent function redefinition
if (!function_exists('get_all_users')) {
    function get_all_users($conn) {
        try {
            return $conn->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

if (!function_exists('getDashboardStats')) {
    function getDashboardStats($conn) {
        try {
            $taskStats = $conn->query("
                SELECT 
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                FROM tasks
            ")->fetch(PDO::FETCH_ASSOC);

            $employeeCount = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'employee'")->fetchColumn();

            $completionRate = ($taskStats['total_tasks'] > 0) 
                ? round(($taskStats['completed'] / $taskStats['total_tasks']) * 100, 2) 
                : 0;

            return [
                'title' => 'ERMM - Task Management System',
                'month' => date('F Y'),
                'team_size' => (int)$employeeCount,
                'completion_rate' => $completionRate,
                'total_tasks' => (int)$taskStats['total_tasks'],
                'completed_tasks' => (int)$taskStats['completed'],
                'in_progress_tasks' => (int)$taskStats['in_progress'],
                'pending_tasks' => (int)$taskStats['pending']
            ];
        } catch (PDOException $e) {
            return [
                'title' => 'ERMM - Task Management System',
                'month' => date('F Y'),
                'team_size' => 0,
                'completion_rate' => 0,
                'total_tasks' => 0,
                'completed_tasks' => 0,
                'in_progress_tasks' => 0,
                'pending_tasks' => 0
            ];
        }
    }
}

if (!function_exists('getStatsCards')) {
    function getStatsCards($conn) {
        try {
            $taskStats = $conn->query("
                SELECT 
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as todo,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as incomplete,
                    SUM(CASE WHEN due_date < CURDATE() AND status != 'completed' THEN 1 ELSE 0 END) as overdue,
                    COUNT(*) as total_tasks,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                FROM tasks
            ")->fetch(PDO::FETCH_ASSOC);

            $taskStats = array_map('intval', $taskStats);

            return [
                [
                    'id' => 'todo',
                    'icon' => 'fas fa-exclamation-triangle',
                    'value' => $taskStats['todo'],
                    'label' => 'Cần làm',
                    'color' => '#e15759'
                ],
                [
                    'id' => 'incomplete',
                    'icon' => 'fas fa-clock',
                    'value' => $taskStats['incomplete'],
                    'label' => 'Đang thực hiện',
                    'color' => '#edc948'
                ],
                [
                    'id' => 'overdue',
                    'icon' => 'fas fa-exclamation-circle',
                    'value' => $taskStats['overdue'],
                    'label' => 'Quá hạn',
                    'color' => '#d37295'
                ],
                [
                    'id' => 'tasks',
                    'icon' => 'fas fa-tasks',
                    'value' => $taskStats['total_tasks'],
                    'label' => 'Tổng Task',
                    'color' => '#4e79a7'
                ],
                [
                    'id' => 'completed',
                    'icon' => 'fas fa-check-circle',
                    'value' => $taskStats['completed'],
                    'label' => 'Hoàn thành',
                    'color' => '#59a14f'
                ]
            ];
        } catch (PDOException $e) {
            return [
                [
                    'id' => 'todo',
                    'icon' => 'fas fa-exclamation-triangle',
                    'value' => 0,
                    'label' => 'Cần làm',
                    'color' => '#e15759'
                ],
                [
                    'id' => 'incomplete',
                    'icon' => 'fas fa-clock',
                    'value' => 0,
                    'label' => 'Đang thực hiện',
                    'color' => '#edc948'
                ],
                [
                    'id' => 'overdue',
                    'icon' => 'fas fa-exclamation-circle',
                    'value' => 0,
                    'label' => 'Quá hạn',
                    'color' => '#d37295'
                ],
                [
                    'id' => 'tasks',
                    'icon' => 'fas fa-tasks',
                    'value' => 0,
                    'label' => 'Tổng Task',
                    'color' => '#4e79a7'
                ],
                [
                    'id' => 'completed',
                    'icon' => 'fas fa-check-circle',
                    'value' => 0,
                    'label' => 'Hoàn thành',
                    'color' => '#59a14f'
                ]
            ];
        }
    }
}

if (!function_exists('getEmployees')) {
    function getEmployees($conn) {
        try {
            return $conn->query("
                SELECT 
                    u.id,
                    u.full_name as name,
                    CASE 
                        WHEN LOCATE(' ', TRIM(u.full_name)) > 0 THEN
                            CONCAT(LEFT(TRIM(u.full_name), 1), SUBSTRING(TRIM(u.full_name), LOCATE(' ', TRIM(u.full_name)) + 1, 1))
                        ELSE LEFT(TRIM(u.full_name), 2)
                    END as avatar,
                    COUNT(t.id) as total_tasks,
                    SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN t.status != 'completed' THEN 1 ELSE 0 END) as incomplete,
                    SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'completed' THEN 1 ELSE 0 END) as errors,
                    CASE 
                        WHEN COUNT(t.id) = 0 THEN 0 
                        ELSE ROUND(SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) / COUNT(t.id) * 100, 2) 
                    END as completion_rate,
                    CASE 
                        WHEN COUNT(t.id) = 0 THEN 0 
                        ELSE ROUND((SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END) / COUNT(t.id)) * 
                                   (1 - (SUM(CASE WHEN t.due_date < CURDATE() AND t.status != 'completed' THEN 1 ELSE 0 END) / COUNT(t.id))) * 100, 2)
                    END as efficiency
                FROM users u
                LEFT JOIN tasks t ON u.id = t.assigned_to
                WHERE u.role = 'employee' AND u.full_name IS NOT NULL AND TRIM(u.full_name) != ''
                GROUP BY u.id, u.full_name
                ORDER BY completion_rate DESC, u.full_name ASC
            ")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}

if (!function_exists('getChartData')) {
    function getChartData($conn) {
        try {
            $data = $conn->query("
                SELECT 
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'pending' AND due_date >= CURDATE() THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status != 'completed' AND due_date < CURDATE() THEN 1 ELSE 0 END) as overdue
                FROM tasks
            ")->fetch(PDO::FETCH_ASSOC);

            $data = array_map('intval', $data);

            return [
                'labels' => ['Hoàn thành', 'Đang thực hiện', 'Chờ xử lý', 'Quá hạn'],
                'data' => [
                    $data['completed'],
                    $data['in_progress'],
                    $data['pending'],
                    $data['overdue']
                ],
                'colors' => ['#59a14f', '#edc948', '#4e79a7', '#e15759']
            ];
        } catch (PDOException $e) {
            return [
                'labels' => ['Hoàn thành', 'Đang thực hiện', 'Chờ xử lý', 'Quá hạn'],
                'data' => [0, 0, 0, 0],
                'colors' => ['#59a14f', '#edc948', '#4e79a7', '#e15759']
            ];
        }
    }
}

if (!function_exists('formatNumber')) {
    function formatNumber($number) {
        return number_format((int)$number, 0, ',', '.');
    }
}

if (!function_exists('getProgressColor')) {
    function getProgressColor($percentage) {
        if ($percentage >= 90) return '#59a14f';
        if ($percentage >= 70) return '#edc948';
        return '#e15759';
    }
}

// Fetch data
$dashboard_config = getDashboardStats($conn);
$stats = getStatsCards($conn);
$employees = getEmployees($conn);
$chart_data = getChartData($conn);

// Calculate totals with null checks
$totalTasks = 0;
$totalCompleted = 0;
$totalIncomplete = 0;
$totalErrors = 0;
$avgEfficiency = 0;

if (!empty($employees)) {
    $totalTasks = array_sum(array_column($employees, 'total_tasks'));
    $totalCompleted = array_sum(array_column($employees, 'completed'));
    $totalIncomplete = array_sum(array_column($employees, 'incomplete'));
    $totalErrors = array_sum(array_column($employees, 'errors'));
    
    $efficiencyValues = array_column($employees, 'efficiency');
    $avgEfficiency = count($efficiencyValues) > 0 ? round(array_sum($efficiencyValues) / count($efficiencyValues), 2) : 0;
}

$totals = [
    'total_tasks' => $totalTasks,
    'completed' => $totalCompleted,
    'incomplete' => $totalIncomplete,
    'errors' => $totalErrors,
    'completion_rate' => $dashboard_config['completion_rate'],
    'efficiency' => $avgEfficiency
];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/Chart.umd.js"></script>
    <style>
       

        .date-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 0.95rem;
            font-weight: 500;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(20px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
            margin-top: -830px;
            margin-left: 261px
        }
        

        .stat-card {
            background: white;
            border-radius: 12px;
            
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--accent-color);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            background: var(--accent-color);
            opacity: 0.1;
            border-radius: 0 12px 0 50px;
        }

        .stat-icon {
            color: var(--accent-color);
            font-size: 1.5rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
                margin-left: 10px;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #2c3e50;
            position: relative;
            z-index: 1;
                margin-left: 70px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
            position: relative;
            z-index: 1;
                margin-left: 25px;
        }
        .side-bar {
            width: 260px;
            background: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0;
            position: relative;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            height: 860px;
        }
        /* Progress Section */
        .progress-section {
            background: white;
            border-radius: 12px;
            padding: 5px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            margin-left: 263px;
                margin-top: -15px;
        }
        

        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .progress-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .progress-percentage {
            font-size: 2rem;
            font-weight: 700;
            color: var(--progress-color);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .progress-bar-container {
            width: 100%;
            height: 12px;
            background: #ecf0f1;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--progress-color), var(--progress-color));
            border-radius: 6px;
            width: 0%;
            transition: width 2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .team-section {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }

        .chart-container, .table-container {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
                margin-left: 260px;
                    width: 1000px;
        }

        .chart-title, .table-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #ecf0f1;
        }

        .chart-wrapper {
            position: relative;
            height: 350px;
        }

        /* Table Styles */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #ecf0f1;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.9rem;
            min-width: 800px;
        }

        .modern-table th {
            background: #F5BABB;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .modern-table th:first-child {
            border-top-left-radius: 8px;
        }

        .modern-table th:last-child {
            border-top-right-radius: 8px;
        }

        .modern-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
        }

        .modern-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .modern-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }

        .employee-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .percentage-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            display: inline-block;
            min-width: 45px;
            text-align: center;
        }

        .total-row {
            font-weight: 600;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef) !important;
            border-top: 2px solid #dee2e6;
        }

        .total-row td {
            color: #2c3e50;
            font-size: 0.95rem;
        }

        .no-data {
            text-align: center;
            color: #7f8c8d;
            padding: 60px 20px;
        }

        .no-data i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #bdc3c7;
            opacity: 0.7;
        }

        .no-data p {
            font-size: 1.1rem;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .header-info h1 {
                font-size: 1.8rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .stat-value {
                font-size: 2rem;
            }

            .progress-header {
                flex-direction: column;
                text-align: center;
                gap: 10px;
            }

            .chart-container, .table-container {
                padding: 20px 15px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .modern-table {
                font-size: 0.8rem;
            }

            .modern-table th,
            .modern-table td {
                padding: 10px 8px;
            }
        }

        /* Loading Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .stat-card, .progress-section, .chart-container, .table-container {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    
    <?php include "inc/header.php" ?>
    
    <div class="body">
        <?php include "inc/nav.php" ?>

                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <?php foreach ($stats as $stat): ?>
                        <div class="stat-card" style="--accent-color: <?php echo htmlspecialchars($stat['color']); ?>">
                            <div class="stat-icon">
                                <i class="<?php echo htmlspecialchars($stat['icon']); ?>"></i>
                            </div>
                            <div class="stat-value" data-value="<?php echo (int)$stat['value']; ?>">
                                <?php echo formatNumber($stat['value']); ?>
                            </div>
                            <div class="stat-label"><?php echo htmlspecialchars($stat['label']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Progress Section -->
                    <div class="progress-section" style="--progress-color: <?php echo getProgressColor($dashboard_config['completion_rate']); ?>">
                        <div class="progress-header">
                            <div class="progress-title">
                                <i class="fas fa-chart-pie"></i>
                                Tỷ lệ hoàn thành công việc
                            </div>
                            <div class="progress-percentage">
                            <?php
                                $rate = !empty($dashboard_config['completion_rate']) ? number_format($dashboard_config['completion_rate'], 1) : 0;
                                echo $rate;
                            ?>
                            <i class="fas fa-percentage"></i>
                        </div>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar" data-width="<?php echo $dashboard_config['completion_rate']; ?>"></div>
                        </div>
                        <div class="team-section">
                            <i class="fas fa-users"></i>
                            <span><?php echo $dashboard_config['team_size']; ?> thành viên trong nhóm</span>
                        </div>
                    </div>

                    <!-- Content Grid -->
                    <div class="content-grid">
                        <!-- Chart Section -->
                        <!-- <div class="chart-container">
                            <div class="chart-title">
                                <i class="fas fa-pie-chart"></i>
                                Phân bổ công việc
                            </div>
                            <div class="chart-wrapper">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div> -->

                        <!-- Table Section -->
                        <div class="table-container">
                            <div class="table-title">
                                <i class="fas fa-table"></i>
                                Hiệu suất nhân viên
                            </div>
                            <?php if (!empty($employees)): ?>
                            <div class="table-wrapper">
                                <table class="modern-table">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Nhân viên</th>
                                            <th>Tổng CV</th>
                                            <th>Hoàn thành</th>
                                            <th>Chưa hoàn thành</th>
                                            <th>Quá hạn</th>
                                            <th>Tỷ lệ HT</th>
                                            <th>Hiệu suất</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($employees as $index => $employee): 
                                            $completion_color = getProgressColor((float)$employee['completion_rate']);
                                            $efficiency_color = getProgressColor((float)$employee['efficiency']);
                                        ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td>
                                                <div class="employee-name">
                                                    <?php echo htmlspecialchars($employee['name']); ?>
                                                </div>
                                            </td>
                                            <td><?php echo formatNumber($employee['total_tasks']); ?></td>
                                            <td><?php echo formatNumber($employee['completed']); ?></td>
                                            <td><?php echo formatNumber($employee['incomplete']); ?></td>
                                            <td><?php echo formatNumber($employee['errors']); ?></td>
                                            <td>
                                                <span class="percentage-badge" style="background-color: <?php echo $completion_color; ?>">
                                                    <?php echo (float)$employee['completion_rate']; ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="percentage-badge" style="background-color: <?php echo $efficiency_color; ?>">
                                                    <?php echo (float)$employee['efficiency']; ?>%
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <tr class="total-row">
                                            <td colspan="2"><strong>TỔNG CỘNG</strong></td>
                                            <td><strong><?php echo formatNumber($totals['total_tasks']); ?></strong></td>
                                            <td><strong><?php echo formatNumber($totals['completed']); ?></strong></td>
                                            <td><strong><?php echo formatNumber($totals['incomplete']); ?></strong></td>
                                            <td><strong><?php echo formatNumber($totals['errors']); ?></strong></td>
                                            <td>
                                                <span class="percentage-badge" style="background-color: <?php echo getProgressColor($totals['completion_rate']); ?>">
                                                    <?php echo $totals['completion_rate']; ?>%
                                                </span>
                                            </td>
                                            <td>
                                                <span class="percentage-badge" style="background-color: <?php echo getProgressColor($totals['efficiency']); ?>">
                                                    <?php echo $totals['efficiency']; ?>%
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="no-data">
                                <i class="fas fa-users"></i>
                                <p>Không có dữ liệu nhân viên để hiển thị</p>
                            </div>
                            <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Configuration from PHP
        const chartData = <?php echo json_encode($chart_data); ?>;
        const completionRate = <?php echo $dashboard_config['completion_rate']; ?>;

        // Animate progress bar and stat values on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Progress bar animation
            const progressBar = document.querySelector('.progress-bar');
            if (progressBar) {
                setTimeout(() => {
                    progressBar.style.width = completionRate + '%';
                }, 500);
            }

            // Animate stat values with counter effect
            document.querySelectorAll('.stat-value').forEach(element => {
                const value = parseInt(element.getAttribute('data-value')) || 0;
                animateValue(element, 0, value, 2000);
            });

            // Initialize Chart with enhanced options
            const ctx = document.getElementById('pieChart');
            if (ctx) {
                const context = ctx.getContext('2d');
                new Chart(context, {
                    type: 'doughnut',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            data: chartData.data,
                            backgroundColor: chartData.colors,
                            borderWidth: 0,
                            hoverBorderWidth: 3,
                            hoverBorderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    pointStyle: 'circle',
                                    font: {
                                        size: 12,
                                        family: "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif"
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleFont: {
                                    size: 14,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 13
                                },
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        if (total === 0) return context.label + ': 0 (0%)';
                                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                                        return `${context.label}: ${context.parsed.toLocaleString('vi-VN')} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            duration: 2000,
                            easing: 'easeInOutCubic'
                        },
                        hover: {
                            animationDuration: 300
                        }
                    }
                });
            }

            // Add scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all animated elements
            document.querySelectorAll('.stat-card, .progress-section, .chart-container, .table-container').forEach(el => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
        });

        // Enhanced animate value function with easing
        function animateValue(element, start, end, duration) {
            if (end === 0) {
                element.textContent = '0';
                return;
            }
            
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                
                // Easing function for smooth animation
                const easeOutCubic = 1 - Math.pow(1 - progress, 3);
                const current = Math.floor(easeOutCubic * (end - start) + start);
                
                element.textContent = new Intl.NumberFormat('vi-VN').format(current);
                
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    element.textContent = new Intl.NumberFormat('vi-VN').format(end);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Add table row hover effects
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('.modern-table tbody tr:not(.total-row)');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'transform 0.2s ease';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });

        // Add loading states
        window.addEventListener('load', function() {
            document.body.classList.add('loaded');
        });

        // Error handling for chart
        window.addEventListener('error', function(e) {
            console.error('Dashboard Error:', e.error);
            const chartContainer = document.querySelector('.chart-container');
            if (chartContainer && e.error.message.includes('Chart')) {
                chartContainer.innerHTML = `
                    <div class="chart-title">
                        <i class="fas fa-pie-chart"></i>
                        Phân bổ công việc
                    </div>
                    <div class="no-data">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Không thể tải biểu đồ</p>
                    </div>
                `;
            }
        });

        // Add responsive table handling
        function handleResponsiveTable() {
            const table = document.querySelector('.modern-table');
            const container = document.querySelector('.table-wrapper');
            
            if (table && container) {
                if (window.innerWidth < 768) {
                    container.style.overflowX = 'auto';
                    table.style.minWidth = '800px';
                } else {
                    container.style.overflowX = 'visible';
                    table.style.minWidth = 'auto';
                }
            }
        }

        window.addEventListener('resize', handleResponsiveTable);
        document.addEventListener('DOMContentLoaded', handleResponsiveTable);
    </script>
</body>
</html>