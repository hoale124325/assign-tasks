<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id']) && $_SESSION['role'] == "employee") {
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
         header("Location: tasks.php");
         exit();
    }
   $users = get_all_users($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Chỉnh Sửa Nhiệm Vụ</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <input type="checkbox" id="checkbox">
    <?php include "inc/header.php" ?>
    <div class="body">
        <?php include "inc/nav.php" ?>
        <section class="section-1">
            <h4 class="title">Chỉnh Sửa Nhiệm Vụ <a href="my_task.php">Danh Sách Nhiệm Vụ</a></h4>
            <form class="form-1"
                  method="POST"
                  action="app/update-task-employee.php">
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
                    <label></label>
                    <p><b>Tiêu Đề: </b><?=$task['title']?></p>
                </div>
                <div class="input-holder">
                    <label></label>
                    <p><b>Mô Tả: </b><?=$task['description']?></p>
                </div><br>
                <div class="input-holder">
                    <label>Trạng Thái</label>
                    <select name="status" class="input-1">
                        <option 
                        <?php if( $task['status'] == "pending") echo"selected"; ?> >Đang Chờ</option>
                        <option <?php if( $task['status'] == "in_progress") echo"selected"; ?>>Đang Thực Hiện</option>
                        <option <?php if( $task['status'] == "completed") echo"selected"; ?>>Hoàn Thành</option>
                    </select><br>
                </div>
                <input type="text" name="id" value="<?=$task['id']?>" hidden>

                <button class="edit-btn">Cập Nhật</button>
            </form>
        </section>
    </div>

<script type="text/javascript">
    var active = document.querySelector("#navList li:nth-child(2)");
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