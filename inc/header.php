
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Header</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<header class="header">
    
    <span class="notification" id="notificationBtn">
        <i class="fa fa-bell" aria-hidden="true"></i>
        <span id="notificationNum"></span>
    </span>
</header>

<div class="notification-bar" id="notificationBar">
    <ul id="notifications"></ul>
</div>

<style>
    * {
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 30px;
        background: none;
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
		margin-left: 93%;
    }
	.header i {
    
    color: #363232;
	}
    .u-name {
        font-size: 24px;
        font-weight: 500;
    }

    .u-name b {
        font-weight: 700;
    }

    .notification {
        position: relative;
        cursor: pointer;
    }

    #notificationNum {
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 12px;
    }

    .notification-bar {
        display: none;
        position: absolute;
        top: 60px;
        right: 20px;
        background: #fff;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        width: 300px;
        max-height: 400px;
        overflow-y: auto;
        z-index: 1000;
    }
	#notificationNum {
    position: absolute;
    top: -5px;
    right: -5px;
    background: red;
    color: white;
    border-radius: 50%;
    padding: 10px 6px;
    font-size: 12px;
    left: 10px;
}

    .notification-bar.open-notification {
        display: block;
    }

    #notifications {
        list-style: none;
        padding: 10px;
    }

    #notifications li {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    #notifications li:last-child {
        border-bottom: none;
    }
</style>

<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script type="text/javascript">
    var openNotification = false;

    const notification = () => {
        let notificationBar = document.querySelector("#notificationBar");
        if (openNotification) {
            notificationBar.classList.remove('open-notification');
            openNotification = false;
        } else {
            notificationBar.classList.add('open-notification');
            openNotification = true;
        }
    }

    let notificationBtn = document.querySelector("#notificationBtn");
    notificationBtn.addEventListener("click", notification);

    $(document).ready(function() {
        $("#notificationNum").load("app/notification-count.php");
        $("#notifications").load("app/notification.php");
    });
</script>

</body>
</html>