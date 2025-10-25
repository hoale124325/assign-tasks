<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['full_name']) && isset($_POST['user_name']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password'])) {
    include "../DB_connection.php";

    function validate_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $full_name = validate_input($_POST['full_name']);
    $user_name = validate_input($_POST['user_name']);
    $email = validate_input($_POST['email']);
    $password = validate_input($_POST['password']);
    $confirm_password = validate_input($_POST['confirm_password']);

    // Kiểm tra các trường bắt buộc
    if (empty($full_name)) {
        $em = "Họ và tên là bắt buộc";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    } elseif (empty($user_name)) {
        $em = "Tên người dùng là bắt buộc";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    } elseif (empty($email)) {
        $em = "Email là bắt buộc";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    } elseif (empty($password)) {
        $em = "Mật khẩu là bắt buộc";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    } elseif ($password !== $confirm_password) {
        $em = "Mật khẩu và xác nhận mật khẩu không khớp";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    }

    // Kiểm tra email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $em = "Email không hợp lệ";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    }

    // Kiểm tra trùng username
    $sql = "SELECT username FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_name]);
    if ($stmt->rowCount() > 0) {
        $em = "Tên người dùng đã tồn tại";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    }

    // Kiểm tra trùng email
    $sql = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $em = "Email đã được sử dụng";
        header("Location: register.php?error=" . urlencode($em));
        exit();
    }

    // Mã hóa mật khẩu và thêm user
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);
    $data = array($full_name, $user_name, $email, null, "admin", $password_hashed, null); // Thay "employee" bằng "admin"
    include "Model/User.php";
    insert_user($conn, $data);

    $sm = "Đăng ký thành công! Vui lòng đăng nhập.";
    header("Location: register.php?success=" . urlencode($sm));
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng Ký | Hệ Thống Quản Lý Nhiệm Vụ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* (Giữ nguyên toàn bộ CSS như mã gốc của bạn) */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'SF Pro Display', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 350px;
            padding: 25px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, #a67c52, #8b5a3c, #a67c52);
            background-size: 200% 100%;
            animation: shimmer 3s linear infinite;
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        .brand-title {
            font-size: 36px;
            font-weight: 700;
            color: #a67c52;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .brand-subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
            font-weight: 400;
            margin-top: -5px;
        }

        .bear-character {
            margin-bottom: 25px;
            position: relative;
            display: inline-block;
        }

        .bear-face {
            width: 100px;
            height: 100px;
            background: #a67c52;
            border-radius: 50%;
            position: relative;
            margin: 0 auto 15px;
            box-shadow: 0 6px 20px rgba(166, 117, 82, 0.3);
            transition: transform 0.3s ease;
        }

        .bear-face:hover {
            transform: scale(1.05);
        }

        .bear-ear {
            width: 30px;
            height: 30px;
            background: #8b5a3c;
            border-radius: 50%;
            position: absolute;
            top: -8px;
        }

        .bear-ear.left {
            left: 8px;
        }

        .bear-ear.right {
            right: 8px;
        }

        .bear-inner-ear {
            width: 18px;
            height: 18px;
            background: #a67c52;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .bear-eyes {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 20px;
        }

        .bear-eye {
            width: 7px;
            height: 10px;
            background: #333;
            border-radius: 3px;
            animation: blink 4s infinite;
        }

        @keyframes blink {
            0%, 90%, 100% { height: 10px; }
            95% { height: 2px; }
        }

        .bear-nose {
            width: 10px;
            height: 7px;
            background: #333;
            border-radius: 50%;
            position: absolute;
            top: 42px;
            left: 50%;
            transform: translateX(-50%);
        }

        .bear-mouth {
            position: absolute;
            top: 55px;
            left: 50%;
            transform: translateX(-50%);
            width: 25px;
            height: 12px;
            border: 2px solid #333;
            border-top: none;
            border-radius: 0 0 12px 12px;
        }

        .bear-hat {
            width: 70px;
            height: 35px;
            background: #9b59b6;
            border-radius: 35px 35px 0 0;
            position: absolute;
            top: -18px;
            left: 50%;
            transform: translateX(-50%);
            box-shadow: 0 3px 12px rgba(155, 89, 182, 0.3);
        }

        .bear-hat::after {
            content: '';
            width: 12px;
            height: 12px;
            background: #8e44ad;
            border-radius: 50%;
            position: absolute;
            top: -7px;
            left: 50%;
            transform: translateX(-50%);
        }

        .bear-body {
            width: 85px;
            height: 65px;
            background: #8b5a3c;
            border-radius: 42px 42px 15px 15px;
            margin: 0 auto;
            position: relative;
        }

        .bear-arm {
            width: 20px;
            height: 50px;
            background: #8b5a3c;
            border-radius: 10px;
            position: absolute;
            top: 8px;
        }

        .bear-arm.left {
            left: -12px;
            transform: rotate(-15deg);
        }

        .bear-arm.right {
            right: -12px;
            transform: rotate(15deg);
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            font-size: 14px;
            background: #f8f9fa;
            transition: all 0.3s ease;
            outline: none;
            color: #333;
        }

        .form-input:focus {
            border-color: #a67c52;
            background: white;
            box-shadow: 0 0 0 3px rgba(166, 117, 82, 0.1);
        }

        .form-input::placeholder {
            color: #999;
        }

        .password-input {
            padding-right: 40px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #a67c52;
        }

        .register-button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #a67c52, #8b5a3c);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
        }

        .register-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(166, 117, 82, 0.4);
        }

        .register-button:active {
            transform: translateY(0);
        }

        .register-button.loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .register-button.loading::after {
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

        .login-link {
            color: #999;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
            display: inline-block;
            margin: 5px 0;
        }

        .login-link:hover {
            color: #a67c52;
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            margin-bottom: 15px;
            border: none;
            padding: 10px 14px;
            animation: slideIn 0.3s ease;
            font-size: 13px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .footer-text {
            margin-top: 20px;
            color: #999;
            font-size: 11px;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 20px 15px;
                margin: 8px;
            }
            
            .brand-title {
                font-size: 30px;
            }
            
            .bear-face {
                width: 90px;
                height: 90px;
            }
            
            .bear-ear {
                width: 25px;
                height: 25px;
            }
        }

        .bear-character {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="brand-title">bear.</div>
        <div class="brand-subtitle">Đăng ký tài khoản mới :)</div>
        
        <div class="bear-character">
            <div class="bear-face">
                <div class="bear-hat"></div>
                <div class="bear-ear left">
                    <div class="bear-inner-ear"></div>
                </div>
                <div class="bear-ear right">
                    <div class="bear-inner-ear"></div>
                </div>
                <div class="bear-eyes">
                    <div class="bear-eye"></div>
                    <div class="bear-eye"></div>
                </div>
                <div class="bear-nose"></div>
                <div class="bear-mouth"></div>
            </div>
            <div class="bear-body">
                <div class="bear-arm left"></div>
                <div class="bear-arm right"></div>
            </div>
        </div>

        <form method="POST" action="register.php" id="registerForm">
            <?php
            // Hiển thị thông báo lỗi nếu có
            if (isset($_GET['error'])) {
            ?>
                <div id="errorAlert" class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="errorMessage"><?php echo htmlspecialchars(stripslashes($_GET['error'])); ?></span>
                </div>
            <?php
            }
            // Hiển thị thông báo thành công nếu có
            if (isset($_GET['success'])) {
            ?>
                <div id="successAlert" class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="successMessage"><?php echo htmlspecialchars(stripslashes($_GET['success'])); ?></span>
                </div>
            <?php
            }
            ?>

            <div class="form-group">
                <input type="text" name="full_name" class="form-input" placeholder="Họ và tên" required>
            </div>

            <div class="form-group">
                <input type="text" name="user_name" class="form-input" placeholder="Tên người dùng" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" class="form-input" placeholder="bear@gmail.com" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" class="form-input password-input" placeholder="Mật khẩu" id="passwordInput" required>
                <button type="button" class="toggle-password" id="togglePassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" class="form-input password-input" placeholder="Xác nhận mật khẩu" id="confirmPasswordInput" required>
                <button type="button" class="toggle-password" id="toggleConfirmPassword">
                    <i class="fas fa-eye"></i>
                </button>
            </div>

            <button type="submit" class="register-button" id="registerBtn">
                <span class="button-text">ĐĂNG KÝ</span>
            </button>

            <a href="login.php" class="login-link">Đã có tài khoản? Đăng nhập</a>
        </form>

        <div class="footer-text">
            © <?php echo date('Y'); ?> Bear System - Hệ thống quản lý nhiệm vụ
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
    <script>
        // Toggle password visibility
        function togglePasswordVisibility(inputId, toggleId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = document.getElementById(toggleId);
            const icon = toggleButton.querySelector('i');
            
            toggleButton.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }

        togglePasswordVisibility('passwordInput', 'togglePassword');
        togglePasswordVisibility('confirmPasswordInput', 'toggleConfirmPassword');

        // Handle form submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('registerBtn');
            const btnText = btn.querySelector('.button-text');
            const password = document.getElementsByName('password')[0].value;
            const confirmPassword = document.getElementsByName('confirm_password')[0].value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Mật khẩu và xác nhận mật khẩu không khớp!');
                return;
            }

            // Thêm hiệu ứng loading và cho phép submit
            btn.classList.add('loading');
            btnText.style.opacity = '0';

            // Gửi form mà không chặn
            // Không cần setTimeout để loại bỏ loading, vì server sẽ xử lý redirect
        });

        // Handle URL parameters for error/success messages
        window.addEventListener('load', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            const success = urlParams.get('success');
            
            if (error) {
                const errorAlert = document.getElementById('errorAlert');
                const errorMessage = document.getElementById('errorMessage');
                errorMessage.textContent = decodeURIComponent(error);
                errorAlert.style.display = 'block';
                
                const bearFace = document.querySelector('.bear-face');
                bearFace.style.animation = 'shake 0.5s ease-in-out';
                
                setTimeout(() => {
                    errorAlert.style.display = 'none';
                    bearFace.style.animation = '';
                }, 5000);
            }
            
            if (success) {
                const successAlert = document.getElementById('successAlert');
                const successMessage = document.getElementById('successMessage');
                successMessage.textContent = decodeURIComponent(success);
                successAlert.style.display = 'block';
                
                const bearFace = document.querySelector('.bear-face');
                bearFace.style.animation = 'bounce 0.6s ease-in-out';
                
                setTimeout(() => {
                    successAlert.style.display = 'none';
                    bearFace.style.animation = '';
                }, 5000);
            }
        });

        // Bear interactions
        document.querySelector('.bear-face').addEventListener('click', function() {
            this.style.animation = 'bounce 0.6s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 600);
        });

        // Focus effects
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                const bearEyes = document.querySelectorAll('.bear-eye');
                bearEyes.forEach(eye => {
                    eye.style.background = '#a67c52';
                });
            });
            
            input.addEventListener('blur', function() {
                const bearEyes = document.querySelectorAll('.bear-eye');
                bearEyes.forEach(eye => {
                    eye.style.background = '#333';
                });
            });
        });
    </script>
</body>
</html>
<?php
?>