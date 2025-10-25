<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "admin") {
    include "DB_connection.php";
    include "app/Model/User.php";

    $users = get_all_users($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tạo Nhiệm Vụ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }

        .section-1 {
            background: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin: 20px;
        }

        .title {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eaeaea;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .title i {
            color: #3498db;
        }

        .form-1 {
            background: #ffffff;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #eaeaea;
        }

        .input-holder {
            margin-bottom: 20px;
        }

        .input-holder label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }

        .input-1 {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            background: #f9f9f9;
            transition: all 0.3s ease;
            outline: none;
            color: #333;
        }

        .input-1:focus {
            border-color: #3498db;
            background: #fff;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.1);
        }

        textarea.input-1 {
            min-height: 100px;
            resize: vertical;
        }

        .edit-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            font-size: 15px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
            font-weight: 500;
            margin-top: 10px;
        }

        .edit-btn:hover {
            background: #2980b9;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            display: flex;
            align-items: center;
        }

        .alert-danger {
            background: #fdecea;
            color: #d32f2f;
            border-left: 3px solid #d32f2f;
        }

        .alert-success {
            background: #e8f5e9;
            color: #388e3c;
            border-left: 3px solid #388e3c;
        }

        select.input-1 {
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 15px;
        }

        @media (max-width: 768px) {
            .section-1 {
                padding: 20px;
                margin: 10px;
            }
            
            .form-1 {
                padding: 15px;
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
            <h2 class="title"><i class="fa fa-tasks"></i>Tạo Nhiệm Vụ</h2>
            <form class="form-1"
                  method="POST"
                  action="app/add-task.php"
                  enctype="multipart/form-data">
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fa fa-exclamation-circle"></i>
                        <?php echo stripcslashes($_GET['error']); ?>
                    </div>
                <?php } ?>

                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fa fa-check-circle"></i>
                        <?php echo stripcslashes($_GET['success']); ?>
                    </div>
                <?php } ?>
                <div class="input-holder">
                    <label>Tiêu Đề</label>
                    <input type="text" name="title" class="input-1" placeholder="Nhập tiêu đề nhiệm vụ" required>
                </div>
                <div class="input-holder">
                    <label>Mô Tả</label>
                    <textarea name="description" class="input-1" placeholder="Mô tả chi tiết nhiệm vụ" required></textarea>
                </div>
                <div class="input-holder">
                    <label>Ngày Hết Hạn</label>
                    <input type="date" name="due_date" class="input-1" required>
                </div>
                <div class="input-holder">
                    <label>Phân Công Cho</label>
                    <select name="assigned_to" class="input-1" required>
                        <option value="">-- Chọn Nhân Viên --</option>
                        <?php if ($users != 0) { 
                            foreach ($users as $user) {
                        ?>
                        <option value="<?=htmlspecialchars($user['id'])?>"><?=htmlspecialchars($user['full_name'])?></option>
                        <?php } } ?>
                    </select>
                </div>
                <div class="input-holder">
                    <label>Tệp Đính Kèm (nếu có)</label>
                    <input type="file" name="attachment" class="input-1">
                </div>
                <button type="submit" class="edit-btn">
                    <i class="fa fa-plus"></i> Tạo Nhiệm Vụ
                </button>
            </form>
        </section>
    </div>

    <script type="text/javascript">
        var active = document.querySelector("#navList li:nth-child(3)");
        if (active) {
            active.classList.add("active");
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