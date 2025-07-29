<?php 
session_start();
if (isset($_SESSION['role']) && isset($_SESSION['id'])) {
    include "../DB_connection.php";
    include "Model/Notification.php";

    $notifications = get_all_my_notifications($conn, $_SESSION['id']);
    
    if (empty($notifications)) { ?>
        <li>
        <a href="#">
            Bạn không có thông báo nào
        </a>
        </li>
    <?php } else {
        foreach ($notifications as $notification) { ?>
            <li>
                <a href="app/notification-read.php?notification_id=<?= $notification['id'] ?>">
                    <?php if ($notification['is_read'] == 0): ?>
                        <mark><?= $notification['type'] ?></mark>: 
                    <?php else: ?>
                        <?= $notification['type'] ?>: 
                    <?php endif; ?>
                    <?= $notification['message'] ?>
                    &nbsp;&nbsp;<small><?= $notification['date'] ?></small>
                </a>
            </li>
    <?php } } 
} else {
    echo "";
}
?>
