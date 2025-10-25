
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Navigation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<nav class="side-bar">
    <!-- Toggle Button -->
    <div class="toggle-btn">
        <i class="fas fa-bars"></i>
    </div>

    <!-- User Profile Section -->
    <div class="user-p">
        <img src="img/user.jpg" alt="User Profile" onerror="this.src='https://via.placeholder.com/40x40/666/fff?text=U'">
        <h4>@<?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?></h4>
    </div>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "employee") { ?>
        <!-- Navigation for Employee -->
        <div class="nav-section">
            <div class="nav-section-title">Tổng quan</div>
            <ul class="nav-list">
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>index.php" data-tooltip="Bảng điều khiển">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Bảng điều khiển</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>my_task.php" data-tooltip="Công việc của tôi">
                        <i class="fas fa-tasks"></i>
                        <span>Công việc của tôi</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>profile.php" data-tooltip="Hồ sơ cá nhân">
                        <i class="fas fa-user"></i>
                        <span>Hồ sơ cá nhân</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>notifications.php" data-tooltip="Thông báo">
                        <i class="fas fa-bell"></i>
                        <span>Thông báo</span>
                    </a>
                </li>
                
            </ul>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Tài Khoản</div>
            <ul class="nav-list">
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>logout.php" data-tooltip="Đăng xuất" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </li>
            </ul>
        </div>

    <?php } else { ?>
        <!-- Navigation for Administrator -->
        <div class="nav-section">
            <div class="nav-section-title">Tổng quan</div>
            <ul class="nav-list">
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>index.php" data-tooltip="Bảng điều khiển">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Bảng điều khiển</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>user.php" data-tooltip="Quản lý người dùng">
                        <i class="fas fa-users"></i>
                        <span>Quản lý người dùng</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Nhiệm vụ</div>
            <ul class="nav-list">
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>create_task.php" data-tooltip="Tạo công việc">
                        <i class="fas fa-plus"></i>
                        <span>Tạo công việc</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>tasks.php" data-tooltip="Tất cả công việc">
                        <i class="fas fa-tasks"></i>
                        <span>Tất cả công việc</span>
                    </a>
                </li>
                <li>
                   <a href="<?php echo $base_path ?? ''; ?>statistical.php" data-tooltip="Tất cả công việc">
    <i class="fas fa-chart-line"></i>
    <span>Thống kê</span>
</a>
                </li>
            </ul>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Tài khoản</div>
            <ul class="nav-list">
                <li>
                    <a href="<?php echo $base_path ?? ''; ?>logout.php" data-tooltip="Đăng xuất" class="logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </li>
            </ul>
        </div>
    <?php } ?>
</nav>

<style>
* {
    font-family: 'Inter', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.side-bar {
    width: 260px;
    background: linear-gradient(180deg, #1a1a1a 0%, #2d2d2d 100%);
    min-height: 100vh;
    transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0;
    position: relative;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.side-bar.collapsed {
    width: 70px;
}

/* Toggle Button */
.toggle-btn {
    display: flex;
    justify-content: flex-end;
    padding: 15px 20px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.toggle-btn:hover {
    background: rgba(255, 255, 255, 0.05);
}

.toggle-btn i {
    color: #b0b0b0;
    font-size: 18px;
    transition: all 0.3s ease;
}

.toggle-btn:hover i {
    color: white;
}
.side-bar ul li {
    
    padding-left: 0px; 
} 

/* User Profile */
.user-p {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    margin-bottom: 20px;
}

.user-p img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.user-p h4 {
    color: #e0e0e0;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s ease;
    white-space: nowrap;
    overflow: hidden;
}

.side-bar.collapsed .user-p h4 {
    opacity: 0;
    width: 0;
}

/* Navigation Sections */
.nav-section {
    margin-bottom: 25px;
    padding: 0 15px;
}

.nav-section-title {
    color: #888;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1.2px;
    padding: 15px 10px 10px 10px;
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    margin-bottom: 10px;
}

.side-bar.collapsed .nav-section-title {
    opacity: 0;
    height: 0;
    padding: 0;
    margin: 0;
    overflow: hidden;
    border: none;
}

/* Navigation List */
.nav-list {
    list-style: none;
}

.nav-list li {
    margin-bottom: 3px;
}

.nav-list a {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 14px 15px;
    color: #b0b0b0;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    width: 100%; /* Đảm bảo vùng nhấp chuột lớn */
}

.side-bar.collapsed .nav-list a {
    padding: 14px;
    justify-content: center;
    margin: 0 5px;
}

.nav-list a:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}


/* Icons */
.nav-list a i {
    font-size: 18px;
    min-width: 20px;
    text-align: center;
    transition: all 0.3s ease;
}

.nav-list a:hover i {
    color: #ffffff;
}

/* Text Spans */
.nav-list a span {
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    white-space: nowrap;
    overflow: hidden;
}

.side-bar.collapsed .nav-list a span {
    opacity: 0;
    width: 0;
}

/* Logout Button Special Styling */
.logout-btn:hover {
    background: rgba(239, 68, 68, 0.1) !important;
    color: #ef4444 !important;
}

.logout-btn:hover i {
    color: #ef4444 !important;
}

/* Tooltips for Collapsed State */
.side-bar.collapsed .nav-list a {
    position: relative;
}

.side-bar.collapsed .nav-list a:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 60px;
    top: 50%;
    transform: translateY(-50%);
    background: #333;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 1000;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
    opacity: 0;
    animation: tooltipFade 0.3s ease forwards;
}

.side-bar.collapsed .nav-list a:hover::before {
    content: '';
    position: absolute;
    left: 55px;
    top: 50%;
    transform: translateY(-50%);
    border: 5px solid transparent;
    border-right-color: #333;
    z-index: 1001;
}

@keyframes tooltipFade {
    from {
        opacity: 0;
        transform: translateY(-50%) translateX(-5px);
    }
    to {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .side-bar {
        width: 70px;
    }
    
    .user-p h4,
    .nav-section-title,
    .nav-list a span {
        opacity: 0;
        width: 0;
    }
}

/* Smooth scrollbar for navigation */
.side-bar::-webkit-scrollbar {
    width: 4px;
}

.side-bar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
}

.side-bar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

.side-bar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.side-bar');
    const toggleBtn = document.querySelector('.toggle-btn');
    const currentPath = window.location.pathname;

    // Toggle sidebar functionality
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('collapsed');
        
        // Save state to localStorage (if available)
        try {
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        } catch(e) {
            // Handle case where localStorage is not available
        }
    });

    // Restore sidebar state from localStorage
    try {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
        }
    } catch(e) {
        // Handle case where localStorage is not available
    }

    // Set active navigation item
    document.querySelectorAll('.nav-list a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && currentPath.includes(href.split('/').pop())) {
            link.classList.add('active');
        }
    });

    // Auto-collapse on mobile
    function handleResize() {
        if (window.innerWidth <= 768) {
            sidebar.classList.add('collapsed');
        }
    }

    window.addEventListener('resize', handleResize);
    handleResize(); // Check on initial load
});
</script>

</body>
</html>
