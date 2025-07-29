<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "DB_connection.php";
    include "app/Model/Task.php";
    include "app/Model/User.php";

    // Lấy username từ session
    $username = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
    
    // Lấy thời gian hiện tại để hiển thị lời chào phù hợp
    $hour = date('H');
    $greeting = '';
    if ($hour < 12) {
        $greeting = 'Chào buổi sáng';
    } elseif ($hour < 18) {
        $greeting = 'Chào buổi chiều';
    } else { // Sửa lỗi syntax ở đây
        $greeting = 'Chào buổi tối';
    }

    if ($_SESSION['role'] == "admin") {
        $todaydue_task = count_tasks_due_today($conn);
        $overdue_task = count_tasks_overdue($conn);
        $nodeadline_task = count_tasks_NoDeadline($conn);
        $num_task = count_tasks($conn);
        $num_users = count_users($conn);
        $pending = count_pending_tasks($conn);
        $in_progress = count_in_progress_tasks($conn);
        $completed = count_completed_tasks($conn);
    } else {
        $num_my_task = count_my_tasks($conn, $_SESSION['id']);
        $overdue_task = count_my_tasks_overdue($conn, $_SESSION['id']);
        $nodeadline_task = count_my_tasks_NoDeadline($conn, $_SESSION['id']);
        $pending = count_my_pending_tasks($conn, $_SESSION['id']);
        $in_progress = count_my_in_progress_tasks($conn, $_SESSION['id']);
        $completed = count_my_completed_tasks($conn, $_SESSION['id']);
    }
} else { 
    $em = "Vui lòng đăng nhập trước";
    header("Location: login.php?error=" . urlencode($em));
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <!-- Greeting section -->
            <div class="greeting-section">
                <h2><?php echo $greeting; ?>, <?php echo $username; ?>!</h2>
                <p>Chúc bạn một ngày làm việc hiệu quả! 
                    <?php 
                    if ($_SESSION['role'] == "admin") {
                        echo number_format($num_users) . " nhân viên và " . number_format($num_task) . " nhiệm vụ đang chờ bạn hôm nay.";
                    } else {
                        echo number_format($num_my_task) . " nhiệm vụ đang chờ bạn hôm nay.";
                    }
                    ?>
                </p>
            </div>

            <?php if ($_SESSION['role'] == "admin") { ?>
                <div class="dashboard">
                    <div class="dashboard-item item-1 card1" data-aos="fade-up" data-aos-delay="100">
                        <div class="dashboard-icon">
                            <i class="fa fa-users"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($num_users) ?></div>
                            <div class="dashboard-label">Nhân Viên</div>
                        </div>
                        <div class="dashboard-decoration"><i class="fa fa-arrow-up"></i></div>
                    </div>
                    
                    <div class="dashboard-item item-2 card2" data-aos="fade-up" data-aos-delay="200">
                        <div class="dashboard-icon">
                            <i class="fa fa-tasks"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($num_task) ?></div>
                            <div class="dashboard-label">Tất Cả Nhiệm Vụ</div>
                        </div>
                        <div class="dashboard-decoration"><i class="fa fa-heart"></i></div>
                    </div>
                    
                    <div class="dashboard-item item-3 warning card3" data-aos="fade-up" data-aos-delay="300">
                        <div class="dashboard-icon"> <!-- Sửa lỗi tag không đóng -->
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($overdue_task) ?></div>
                            <div class="dashboard-label">Quá Hạn</div>
                        </div>
                        <?php if ($overdue_task > 0): ?>
                        <div class="pulse-animation"></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="dashboard-item item-4 card4" data-aos="fade-up" data-aos-delay="400">
                        <div class="dashboard-icon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($nodeadline_task) ?></div>
                            <div class="dashboard-label">Không Có Hạn Chót</div>
                        </div>
                    </div>
                    
                    <div class="dashboard-item item-5 urgent card5" data-aos="fade-up" data-aos-delay="500">
                        <div class="dashboard-icon">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($todaydue_task) ?></div>
                            <div class="dashboard-label">Đến Hạn Hôm Nay</div>
                        </div>
                        <?php if ($todaydue_task > 0): ?>
                        <div class="pulse-animation orange"></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="dashboard-item item-6 card6" data-aos="fade-up" data-aos-delay="600">
                        <div class="dashboard-icon">
                            <i class="fa fa-bell"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($overdue_task) ?></div>
                            <div class="dashboard-label">Thông Báo</div>
                        </div>
                        <?php if ($overdue_task > 0): ?>
                        <div class="notification-badge"><?= number_format($overdue_task) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="dashboard-item item-7 pending card7" data-aos="fade-up" data-aos-delay="700">
                        <div class="dashboard-icon">
                            <i class="fa fa-hourglass-start"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($pending) ?></div>
                            <div class="dashboard-label">Đang Chờ</div>
                        </div>
                    </div>
                    
                    <div class="dashboard-item item-8 progress card8" data-aos="fade-up" data-aos-delay="800">
                        <div class="dashboard-icon">
                            <i class="fa fa-spinner fa-spin"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($in_progress) ?></div>
                            <div class="dashboard-label">Đang Thực Hiện</div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $num_task > 0 ? ($in_progress / $num_task) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="dashboard-item item-9 success card9" data-aos="fade-up" data-aos-delay="900">
                        <div class="dashboard-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($completed) ?></div>
                            <div class="dashboard-label">Hoàn Thành</div>
                        </div>
                        <div class="success-checkmark">
                            <i class="fa fa-check"></i>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="dashboard user-dashboard"> <!-- Thêm class phân biệt -->
                    <div class="dashboard-item item-1 card1" data-aos="fade-up" data-aos-delay="100">
                        <div class="dashboard-icon">
                            <i class="fa fa-tasks"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($num_my_task) ?></div>
                            <div class="dashboard-label">Nhiệm Vụ Của Tôi</div>
                        </div>
                        <div class="dashboard-decoration"><i class="fa fa-user"></i></div>
                    </div>
                    
                    <div class="dashboard-item item-2 warning card2" data-aos="fade-up" data-aos-delay="200">
                        <div class="dashboard-icon">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($overdue_task) ?></div>
                            <div class="dashboard-label">Quá Hạn</div>
                        </div>
                        <?php if ($overdue_task > 0): ?>
                        <div class="pulse-animation"></div>
                        <?php endif; ?>
                        <div class="dashboard-decoration"><i class="fa fa-heart"></i></div>
                    </div>
                    
                    <div class="dashboard-item item-3 card3" data-aos="fade-up" data-aos-delay="300"> <!-- Sửa attribute bị cắt -->
                        <div class="dashboard-icon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($nodeadline_task) ?></div>
                            <div class="dashboard-label">Không Có Hạn Chót</div>
                        </div>
                    </div>
                    
                    <div class="dashboard-item item-4 pending card4" data-aos="fade-up" data-aos-delay="400">
                        <div class="dashboard-icon">
                            <i class="fa fa-hourglass-start"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($pending) ?></div>
                            <div class="dashboard-label">Đang Chờ</div>
                        </div>
                    </div>
                    
                    <div class="dashboard-item item-5 progress card5" data-aos="fade-up" data-aos-delay="500">
                        <div class="dashboard-icon">
                            <i class="fa fa-spinner fa-spin"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($in_progress) ?></div>
                            <div class="dashboard-label">Đang Thực Hiện</div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= $num_my_task > 0 ? ($in_progress / $num_my_task) * 100 : 0 ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="dashboard-item item-6 success card6" data-aos="fade-up" data-aos-delay="600">
                        <div class="dashboard-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <div class="dashboard-content">
                            <div class="dashboard-number"><?= number_format($completed) ?></div>
                            <div class="dashboard-label">Hoàn Thành</div>
                        </div>
                        <div class="completion-percentage">
                            <?= $num_my_task > 0 ? round(($completed / $num_my_task) * 100, 1) : 0 ?>%
                        </div>
                        <div class="success-checkmark">
                            <i class="fa fa-check"></i>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </section>
    </div>

<style>
    .section-1 {
        width: 100%;
            background:  rgba(255, 255, 255, 0.95);
        min-height: calc(100vh - 80px);
        padding: 30px 20px;
    }
    .side-bar ul li:hover {
    background: none;
    }

    * {
        font-family: 'Quicksand', sans-serif;
        box-sizing: border-box;
    }

    /* Greeting Section */
    .greeting-section {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        width: 100%;
    }

    .greeting-section h2 {
        font-size: 32px;
        color: #2c3e50;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .greeting-section p {
        font-size: 16px;
        color: #7f8c8d;
        margin: 0;
    }

    /* Dashboard Grid */
    .dashboard {
        
        grid-template-columns: repeat(3, 1fr);
        gap: 5px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* User dashboard - 2 columns layout */
    .user-dashboard {
        grid-template-columns: repeat(2, 1fr);
    }

    /* Dashboard Items */
    .dashboard-item {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 20px;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .dashboard-item:hover {
        transform: translateY(-10px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    .dashboard-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        
        transition: height 0.3s ease;
        Width: 300px;
    }

    .dashboard-item:hover::before {
        height: 8px;
    }

    /* Card Colors - Neutral tones */
    /* Card Colors */
    .card1 { background: #FFF3E0; }
    .card2 { background: #FCE4EC; }
    .card3 { background: #E8F5E9; }
    .card4 { background: #E0F7FA; }
    .card5 { background: #FFFDE7; }
    .card6 { background: #F8BBD0; }
    .card7 { background: #C8E6C9; }
    .card8 { background: #B3E5FC; }
    .card9 { background: #FFF9C4; }

    /* Icon Section */
    .dashboard-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, #808080, #696969);
        color: white;
        font-size: 28px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }
   .dashboard-item {   
    width: 300px;
}

    .dashboard-item:hover .dashboard-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
    }

    /* Content Section */
    .dashboard-content {
        flex: 1;
        margin-left: 25px;
    }

    .dashboard-number {
        font-size: 36px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .dashboard-label {
        font-size: 16px;
        color: #7f8c8d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Decoration */
    .dashboard-decoration {
        font-size: 24px;
        color: #808080;
        opacity: 0.6;
        transition: all 0.3s ease;
    }

    .dashboard-item:hover .dashboard-decoration {
        opacity: 1;
        transform: scale(1.2);
    }

    /* Special Item Styles */
    .dashboard-item.warning {
        border-left: 5px solid #e74c3c;
    }

    .dashboard-item.warning .dashboard-icon {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        animation: shake 2s infinite;
    }

    .dashboard-item.urgent {
        border-left: 5px solid #f39c12;
    }

    .dashboard-item.urgent .dashboard-icon {
        background: linear-gradient(135deg, #f39c12, #d68910);
    }

    .dashboard-item.pending {
        border-left: 5px solid #808080;
    }

    .dashboard-item.pending .dashboard-icon {
        background: linear-gradient(135deg, #808080, #696969);
    }

    .dashboard-item.progress {
        border-left: 5px solid #9b59b6;
    }

    .dashboard-item.progress .dashboard-icon {
        background: linear-gradient(135deg, #9b59b6, #8e44ad);
    }

    .dashboard-item.success {
        border-left: 5px solid #27ae60;
    }

    .dashboard-item.success .dashboard-icon {
        background: linear-gradient(135deg, #27ae60, #229954);
    }

    /* Pulse Animation */
    .pulse-animation {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background: #e74c3c;
        animation: pulse 2s infinite;
    }

    .pulse-animation.orange {
        background: #f39c12;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(231, 76, 60, 0); }
        100% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
    }

    /* Notification Badge */
    .notification-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: #e74c3c;
        color: white;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        animation: bounce 1s infinite;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }

    /* Progress Bar */
    .progress-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: rgba(155, 89, 182, 0.2);
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #9b59b6, #8e44ad);
        transition: width 1s ease;
        animation: progressAnimation 2s ease-in-out;
    }

    @keyframes progressAnimation {
        0% { width: 0; }
    }

    /* Success Elements */
    .success-checkmark {
        position: absolute;
        top: 15px;
        right: 15px;
        color: #27ae60;
        font-size: 18px;
        animation: checkmark 2s ease-in-out infinite;
    }

    @keyframes checkmark {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }

    .completion-percentage {
        position: absolute;
        top: 15px;
        right: 50px;
        background: #27ae60;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
    }

    /* Shake Animation */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-2px); }
        75% { transform: translateX(2px); }
    }
    .user-p img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .section-1 {
            padding: 20px 10px;
        }

        .greeting-section {
            padding: 20px;
        }

        .greeting-section h2 {
            font-size: 24px;
        }

        .dashboard {
            grid-template-columns: 1fr !important;
            gap: 20px;
        }

        .dashboard-item {
            padding: 20px;
        }

        .dashboard-number {
            font-size: 28px;
        }

        .dashboard-icon {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }
    }

    /* Loading Animation */
    .dashboard-item {
        opacity: 0;
        transform: translateY(30px);
        animation: fadeInUp 0.6s ease forwards;
    }

    .item-1 { animation-delay: 0.1s; }
    .item-2 { animation-delay: 0.2s; }
    .item-3 { animation-delay: 0.3s; }
    .item-4 { animation-delay: 0.4s; }
    .item-5 { animation-delay: 0.5s; }
    .item-6 { animation-delay: 0.6s; }
    .item-7 { animation-delay: 0.7s; }
    .item-8 { animation-delay: 0.8s; }
    .item-9 { animation-delay: 0.9s; }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        // Set active navigation
        const active = document.querySelector("#navList li:nth-child(1)");
        if (active) {
            active.classList.add("active");
        }

        // Add click effects to dashboard items
        const dashboardItems = document.querySelectorAll('.dashboard-item');
        dashboardItems.forEach(item => {
            item.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            });
        });

        // Auto refresh every 5 minutes
        let lastRefresh = Date.now();
        setInterval(() => {
            if (Date.now() - lastRefresh >= 300000) {
                // Check if page is visible before refreshing
                if (document.visibilityState === 'visible') {
                    location.reload();
                    lastRefresh = Date.now();
                }
            }
        }, 60000);

        // Add ripple effect
        dashboardItems.forEach(item => {
            item.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255,255,255,0.3);
                    border-radius: 50%;
                    pointer-events: none;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    z-index: 1;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    if (ripple.parentNode) {
                        ripple.remove();
                    }
                }, 600);
            });
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
            .dashboard-item {
                position: relative;
            }
        `;
        document.head.appendChild(style);

        // Handle visibility change for better performance
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                // Re-enable animations when page becomes visible
                dashboardItems.forEach(item => {
                    item.style.animationPlayState = 'running';
                });
            } else {
                // Pause animations when page is hidden
                dashboardItems.forEach(item => {
                    item.style.animationPlayState = 'paused';
                });
            }
        });
    });
</script>
</body>
</html>