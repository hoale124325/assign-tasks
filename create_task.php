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
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
           
            <form class="form-1"
                  method="POST"
                  action="app/add-task.php"
                  enctype="multipart/form-data">
                  <?php if (isset($_GET['error'])) {?>
                <div class="danger" role="alert">
                    <?php echo stripcslashes($_GET['error']); ?>
                </div>
                  <?php } ?>

                  <?php if (isset($_GET['success'])) {?>
                <div class="success" role="alert">
                    <?php echo stripcslashes($_GET['success']); ?>
                </div>
                  <?php } ?>
                <div class="input-holder">
                    <label>Tiêu Đề</label>
                    <input type="text" name="title" class="input-1" placeholder="Tiêu Đề"><br>
                </div>
                <div class="input-holder">
                    <label>Mô Tả</label>
                    <textarea type="text" name="description" class="input-1" placeholder="Mô Tả"></textarea><br>
                </div>
                <div class="input-holder">
                    <label>Ngày Hết Hạn</label>
                    <input type="date" name="due_date" class="input-1" placeholder="Ngày Hết Hạn"><br>
                </div>
                <div class="input-holder">
                    <label>Phân Công Cho</label>
                    <select name="assigned_to" class="input-1">
                        <option value="0">Chọn Nhân Viên</option>
                        <?php if ($users != 0) { 
                            foreach ($users as $user) {
                        ?>
                        <option value="<?=$user['id']?>"><?=$user['full_name']?></option>
                        <?php } } ?>
                    </select><br>
                </div>
                <div class="input-holder">
                    <label>Tệp Đính Kèm</label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="attachment" id="attachment" class="input-1">
                        <label for="attachment" class="file-upload-label">Chọn tệp để tải lên</label>
                    </div>
                </div>
                <button class="edit-btn">Tạo Nhiệm Vụ</button>
            </form>
        </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(3)");
    active.classList.add("active");
</script>
</body>
</html>
<?php }else{ 
   $em = "Vui lòng đăng nhập trước";
   header("Location: login.php?error=$em");
   exit();
}
?>
    <style>

    .section-1 {

	background: #F0E4D3;
}

        .title {
            color: #2c3e50;
            font-size: 28px;
            margin-bottom: 30px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .form-1 {
            background: white;
            padding: 30px;
            border-radius: 10px;
           
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
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s ease;
        }

        .edit-btn:hover {
            background: #2980b9;
        }

        .danger, .success {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }

        .danger {
            background: #ffebee;
            color: #c0392b;
            border-left: 4px solid #c0392b;
        }

        .success {
            background: #e8f5e9;
            color: #2ecc71;
            border-left: 4px solid #2ecc71;
        }

        
        @media (max-width: 768px) {
            .section-1 {
                padding: 20px;
            }

            .form-1 {
                padding: 20px;
            }
        }
        
        .title {
            font-size: 24px;
            font-weight: 600;
            color: #a67c52;
            margin-bottom: 15px;
            text-align: left;
            position: relative;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .title i {
            color: #9b59b6;
            font-size: 20px;
        }

       

        .input-holder {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .input-holder label {
            font-size: 14px;
            font-weight: 500;
            
        }

        .input-1 {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            font-size: 14px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            outline: none;
            color: #333;
            resize: none;
        }

        .input-1:focus {
            border-color: #a67c52;
            background: white;
            box-shadow: 0 0 0 3px rgba(166, 117, 82, 0.1);
        }

        .input-1::placeholder {
            color: #999;
        }

        .input-1[type="file"] {
            padding: 8px;
            cursor: pointer;
        }

        textarea.input-1 {
            min-height: 80px;
            border-radius: 10px;
        }

        .edit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #a67c52, #8b5a3c);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            margin-top: 10px;
        }

        .edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(166, 117, 82, 0.4);
        }

        .edit-btn:active {
            transform: translateY(0);
        }

        .edit-btn.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .edit-btn.loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 15px;
            border: none;
            padding: 10px 12px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
            animation: slideIn 0.3s ease;
        }

        .alert-danger {
            background: #ffe6e6;
            color: #d63031;
            border-left: 3px solid #d63031;
        }

        .alert-success {
            background: #e6f7e6;
            color: #00b894;
            border-left: 3px solid #00b894;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }

        
    </style>


?>