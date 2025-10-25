<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";

    $users = get_all_users($conn);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng | Hệ thống Quản lý</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --primary-hover: #4f46e5;
            --danger-color: #ef4444;
            --danger-hover: #dc2626;
            --success-color: #10b981;
            --success-hover: #059669;
            --warning-color: #f59e0b;
            --warning-hover: #d97706;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-700: #374151;
            --gray-900: #111827;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --rounded-lg: 0.5rem;
            --rounded-xl: 0.75rem;
            --transition: all 0.2s ease-in-out;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f9fafb;
            color: var(--gray-900);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .content-wrapper {
            flex: 1;
            padding: 2rem;
            overflow-y: auto;
        }

        /* Header Styles */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray-200);
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .page-title i {
            color: var(--primary-color);
            font-size: 1.5rem;
        }

        .page-description {
            color: var(--gray-700);
            margin-top: 0.5rem;
            font-size: 0.875rem;
        }

        /* Button Styles */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: var(--rounded-lg);
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.25rem;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: var(--danger-hover);
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
        }

        .btn-success:hover {
            background-color: var(--success-hover);
        }

        .btn-icon {
            padding: 0.5rem;
            border-radius: var(--rounded-lg);
        }

        /* Card Styles */
        .card {
            background-color: white;
            border-radius: var(--rounded-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            overflow: hidden;
        }

        .card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .user-table th {
            background-color: var(--gray-100);
            color: var(--gray-700);
            font-weight: 600;
            text-align: left;
            padding: 0.75rem 1rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .user-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--gray-200);
            vertical-align: middle;
        }

        .user-table tr:last-child td {
            border-bottom: none;
        }

        .user-table tr:hover td {
            background-color: var(--gray-100);
        }

        /* User Info Styles */
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--gray-200);
        }

        .user-name {
            font-weight: 500;
            color: var(--gray-900);
        }

        .user-username {
            color: var(--gray-700);
            font-size: 0.75rem;
            margin-top: 0.125rem;
        }

        .user-contact {
            color: var(--gray-700);
            font-size: 0.875rem;
        }

        .user-phone {
            color: var(--gray-700);
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        /* Badge Styles */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-admin {
            background-color: #ede9fe;
            color: #6b21a8;
        }

        .badge-employee {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-other {
            background-color: var(--gray-100);
            color: var(--gray-700);
        }

        /* Alert Styles */
        .alert {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.875rem 1.25rem;
            border-radius: var(--rounded-lg);
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .alert-success {
            background-color: #ecfdf5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state-icon {
            font-size: 3rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
        }

        .empty-state-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .empty-state-description {
            color: var(--gray-700);
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            max-width: 28rem;
            margin-left: auto;
            margin-right: auto;
        }

        /* Action Buttons */
        .actions {
            display: flex;
            gap: 0.5rem;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .content-wrapper {
                padding: 1rem;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .user-info {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.25rem;
            }
            
            .user-avatar {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <?php include "inc/nav.php"; ?>
        
        <div class="main-content">
            <div class="content-wrapper">
                <div class="page-header">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-users"></i>
                            Quản Lý Người Dùng
                        </h1>
                        <p class="page-description">Quản lý thông tin người dùng trong hệ thống</p>
                    </div>
                    <a href="add-user.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Thêm Người Dùng
                    </a>
                </div>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($_GET['success']); ?></span>
                    </div>
                <?php } ?>

                <div class="card">
                    <?php if ($users != 0) { ?>
                        <div class="card-header">
                            <h2 class="card-title">Danh sách người dùng</h2>
                            <span class="badge badge-other"><?php echo count($users); ?> người dùng</span>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="user-table">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Người dùng</th>
                                        <th>Liên hệ</th>
                                        <th>Vai trò</th>
                                        <th>Ngày tạo</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 0; foreach ($users as $user) { ?>
                                    <tr>
                                        <td><?php echo ++$i; ?></td>
                                        <td>
                                            <div class="user-info">
                                                <img src="<?php echo !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'img/default-avatar.png'; ?>" 
                                                     alt="Avatar" class="user-avatar">
                                                <div>
                                                    <div class="user-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                                    <div class="user-username">@<?php echo htmlspecialchars($user['username']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-contact"><?php echo htmlspecialchars($user['email']); ?></div>
                                            <div class="user-phone"><?php echo htmlspecialchars($user['phone_number']); ?></div>
                                        </td>
                                        <td>
                                            <?php if ($user['role'] == "admin") { ?>
                                                <span class="badge badge-admin">
                                                    <i class="fas fa-crown"></i> Admin
                                                </span>
                                            <?php } elseif ($user['role'] == "employee") { ?>
                                                <span class="badge badge-employee">
                                                    <i class="fas fa-user-tie"></i> Nhân viên
                                                </span>
                                            <?php } else { ?>
                                                <span class="badge badge-other">
                                                    <?php echo htmlspecialchars($user['role']); ?>
                                                </span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <div class="actions">
                                                <a href="edit-user.php?id=<?php echo $user['id']; ?>" 
                                                   class="btn btn-primary btn-icon" 
                                                   title="Chỉnh sửa">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?php echo $user['id']; ?>)" 
                                                        class="btn btn-danger btn-icon" 
                                                        title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } else { ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-users-slash"></i>
                            </div>
                            <h3 class="empty-state-title">Không có người dùng nào</h3>
                            <p class="empty-state-description">
                                Bạn chưa có người dùng nào trong hệ thống. Hãy bắt đầu bằng cách thêm người dùng mới.
                            </p>
                            <a href="add-user.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm Người Dùng
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(userId) {
            Swal.fire({
                title: 'Xác nhận xóa?',
                text: "Bạn có chắc chắn muốn xóa người dùng này không?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete-user.php?id=${userId}`;
                }
            });
        }
    </script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>
</html>
<?php } else { 
    $em = "Vui lòng đăng nhập trước";
    header("Location: login.php?error=" . urlencode($em));
    exit();
} ?>