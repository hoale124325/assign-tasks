-- Cấu hình ban đầu
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Xóa bảng nếu tồn tại (tránh lỗi trùng lặp)
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `tasks`;
DROP TABLE IF EXISTS `users`;


-- Tạo bảng users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    avatar VARCHAR(255) DEFAULT NULL,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(20) DEFAULT NULL,
    role ENUM('employee', 'admin') NOT NULL DEFAULT 'employee',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Thêm dữ liệu mẫu
INSERT INTO users (avatar, full_name, username, email, phone_number, role, password) VALUES
(NULL, 'Nguyen Van A', 'nguyenvana', 'vana.nguyen@example.com', '0901234567', 'admin', '$2y$10$TnyR1Y43m1EIWpb0MiwE8Ocm6rj0F2KojE3PobVfQDo9HYlAHY/7O'),
(NULL, 'Tran Thi B', 'tranthib', 'thib.tran@example.com', '0912345678', 'employee', '$2y$10$8xpI.hVCVd/GKUzcYTxLUO7ICSqlxX5GstSv7WoOYfXuYOO/SZAZ2'),
(NULL, 'Le Van C', 'levanc', 'vanc.le@example.com', '0923456789', 'employee', '$2y$10$CiV/f.jO5vIsSi0Fp1Xe7ubWG9v8uKfC.VfzQr/sjb5/gypWNdlBW'),
(NULL, 'Pham Thi D', 'phamthid', 'thid.pham@example.com', '0934567890', 'employee', '$2y$10$E9Xx8UCsFcw44lfXxiq/5OJtloW381YJnu5lkn6q6uzIPdL5yH3PO');



-- Bảng Tasks
CREATE TABLE `tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `assigned_to` (`assigned_to`),
  CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dữ liệu Tasks
INSERT INTO `tasks` (`id`, `title`, `description`, `assigned_to`, `due_date`, `status`, `created_at`) VALUES
(1, 'Task 1', 'Task Description', 7, NULL, 'completed', '2024-08-29 16:47:37'),
(4, 'Monthly Financial Report Preparation', 'Prepare and review the monthly financial report...', 7, '2024-09-01', 'completed', '2024-08-31 10:50:20'),
(5, 'Customer Feedback Survey Analysis', 'Collect and analyze data...', 7, '2024-09-03', 'in_progress', '2024-08-31 10:50:47'),
(6, 'Website Maintenance and Update', 'Perform regular maintenance...', 7, '2024-09-03', 'pending', '2024-08-31 10:51:12'),
(7, 'Quarterly Inventory Audit', 'Conduct a thorough audit...', 2, '2024-09-03', 'completed', '2024-08-31 10:51:45'),
(8, 'Employee Training Program Development', 'Develop and implement a new training program...', 2, '2024-09-01', 'pending', '2024-08-31 10:52:11'),
(17, 'Prepare monthly sales report', 'Compile and analyze sales data...', 7, '2024-09-06', 'pending', '2024-09-06 08:01:48'),
(18, 'Update client database', 'Ensure all client information is current...', 7, '2024-09-07', 'pending', '2024-09-06 08:02:27'),
(19, 'Fix server downtime issue', 'Investigate and resolve downtime...', 2, '2024-09-07', 'pending', '2024-09-06 08:02:59'),
(20, 'Plan annual marketing strategy', 'Develop a marketing strategy...', 2, '2024-09-04', 'pending', '2024-09-06 08:03:21'),
(21, 'Onboard new employees', 'Complete HR onboarding tasks...', 7, '2024-09-07', 'pending', '2024-09-06 08:03:44'),
(22, 'Design new company website', 'Create wireframes and mockups...', 2, '2024-09-06', 'pending', '2024-09-06 08:04:20'),
(23, 'Conduct software testing', 'Run tests on the latest release...', 7, '2024-09-07', 'pending', '2024-09-06 08:04:39'),
(24, 'Schedule team meeting', 'Organize a project update meeting...', 2, '2024-09-07', 'pending', '2024-09-06 08:04:57'),
(25, 'Prepare budget for Q4', 'Create and review Q4 budget...', 7, '2024-09-07', 'pending', '2024-09-06 08:05:21'),
(26, 'Write blog post on industry trend', 'Draft a blog post about trends...', 7, '2024-09-07', 'pending', '2024-09-06 08:10:50'),
(27, 'Renew software license', 'Renew software licenses in time...', 2, '2024-09-06', 'pending', '2024-09-06 08:11:28');

-- Bảng Notifications
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `recipient` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dữ liệu Notifications
INSERT INTO `notifications` (`id`, `message`, `recipient`, `type`, `date`, `is_read`) VALUES
(1, '\'Customer Feedback Survey Analysis\' has been assigned...', 7, 'New Task Assigned', '2024-09-05', 1),
(2, '\'test task\' has been assigned...', 7, 'New Task Assigned', '2024-09-01', 1),
(3, '\'Example task 2\' has been assigned...', 2, 'New Task Assigned', '2024-09-02', 1),
(4, '\'test\' has been assigned...', 8, 'New Task Assigned', '2024-09-02', 0),
(5, '\'test task 3\' has been assigned...', 7, 'New Task Assigned', '2024-09-06', 1),
(6, '\'Prepare monthly sales report\'...', 7, 'New Task Assigned', '2024-09-06', 1),
(7, '\'Update client database\'...', 7, 'New Task Assigned', '2024-09-06', 1),
(8, '\'Fix server downtime issue\'...', 2, 'New Task Assigned', '2024-09-06', 0),
(9, '\'Plan annual marketing strategy\'...', 2, 'New Task Assigned', '2024-09-06', 0),
(10, '\'Onboard new employees\'...', 7, 'New Task Assigned', '2024-09-06', 0),
(11, '\'Design new company website\'...', 2, 'New Task Assigned', '2024-09-06', 0),
(12, '\'Conduct software testing\'...', 7, 'New Task Assigned', '2024-09-06', 0),
(13, '\'Schedule team meeting\'...', 2, 'New Task Assigned', '2024-09-06', 0),
(14, '\'Prepare budget for Q4\'...', 7, 'New Task Assigned', '2024-09-06', 0),
(15, '\'Write blog post on industry trend\'...', 7, 'New Task Assigned', '2024-09-06', 0),
(16, '\'Renew software license\'...', 2, 'New Task Assigned', '2024-09-06', 0);

COMMIT;
