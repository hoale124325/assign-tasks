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
    <title>Quản Lý Người Dùng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"> <!-- Link tới file CSS thuần -->
</head>
<style>
    body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to bottom right, #f8fafc, #e2e8f0);
    margin: 0;
    padding: 0;
}

.container {
    display: flex;
}

.main {
    flex: 1.7;
    padding: 30px;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;

}
.p{
    margin-top: 30px;
}
.header h1 {
  
    display: flex;
    align-items: center;
    gap: 10px;
    color: #1f2937;
}

.header .icon {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 10px;
    border-radius: 10px;
}

.btn {
    padding: 10px 20px;
    border-radius: 10px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    font-weight: bold;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.add-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.edit-btn {
    background-color: #3b82f6;
    color: white;
    padding: 8px;
    border-radius: 6px;
}

.delete-btn {
    background-color: #ef4444;
    color: white;
    padding: 8px;
    border-radius: 6px;
}

.alert {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.alert.success {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.card {
    background-color: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.badge {
    padding: 5px 12px;
    border-radius: 9999px;
    font-size: 13px;
    font-weight: 600;
    display: inline-block;
}

.badge.admin {
    background: #ede9fe;
    color: #6b21a8;
}

.badge.employee {
    background: #dbeafe;
    color: #1e40af;
}

.badge.other {
    background: #f3f4f6;
    color: #374151;
}

.user-table {
    width: 100%;
    border-collapse: collapse;
}

.user-table th,
.user-table td {
    padding: 12px 10px;
    text-align: left;
    border-bottom: 1px solid #e5e7eb;
}

.user-table tr:hover {
    background-color: #f9fafb;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-info img {
    width: 40px;
    height: 40px;
    border-radius: 9999px;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.no-data {
    text-align: center;
    padding: 40px;
}

.no-data i {
    font-size: 36px;
    color: #9ca3af;
    margin-bottom: 10px;
}
</style>
<body>
    <div class="container">
        <?php include "inc/nav.php"; ?>

        <div class="main">
            <div class="header">
                <div>
                    <h1><i class="fas fa-users icon"></i> Quản Lý Người Dùng</h1>
                    <p>Quản lý thông tin người dùng trong hệ thống</p>
                </div>
                <a href="add-user.php" class="btn add-btn"><i class="fas fa-plus"></i> Thêm Người Dùng</a>
            </div>

            <?php if (isset($_GET['success'])) { ?>
                <div class="alert success">
                    <i class="fas fa-check"></i>
                    <span><?php echo htmlspecialchars(stripcslashes($_GET['success'])); ?></span>
                </div>
            <?php } ?>

            <div class="card">
                <?php if ($users != 0) { ?>
                    <div class="card-header">
                        <h2>Danh sách người dùng</h2>
                        <span class="badge"><?php echo count($users); ?> người dùng</span>
                    </div>
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
                                        <img src="<?php echo !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'img/default-avatar.png'; ?>" alt="Avatar">
                                        <div>
                                            <strong><?php echo htmlspecialchars($user['full_name']); ?></strong><br>
                                            <small>@<?php echo htmlspecialchars($user['username']); ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($user['email']); ?><br>
                                    <small><?php echo htmlspecialchars($user['phone_number']); ?></small>
                                </td>
                                <td>
                                    <?php if ($user['role'] == "admin") { ?>
                                        <span class="badge admin"><i class="fas fa-crown"></i> Admin</span>
                                    <?php } elseif ($user['role'] == "employee") { ?>
                                        <span class="badge employee"><i class="fas fa-user-tie"></i> Nhân viên</span>
                                    <?php } else { ?>
                                        <span class="badge other"><?php echo htmlspecialchars($user['role']); ?></span>
                                    <?php } ?>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn edit-btn"><i class="fas fa-edit"></i></a>
                                    <button onclick="confirmDelete(<?php echo $user['id']; ?>)" class="btn delete-btn"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="no-data">
                        <i class="fas fa-users"></i>
                        <h3>Chưa có người dùng</h3>
                        <p>Bắt đầu bằng cách thêm người dùng đầu tiên</p>
                        <a href="add-user.php" class="btn add-btn"><i class="fas fa-plus"></i> Thêm Người Dùng</a>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(userId) {
            if (confirm('Bạn có chắc chắn muốn xóa người dùng này không?')) {
                window.location.href = `delete-user.php?id=${userId}`;
            }
        }
    </script>
</body>
</html>
<?php } else { 
    $em = "Vui lòng đăng nhập trước";
    header("Location: login.php?error=$em");
    exit();
}
?>
