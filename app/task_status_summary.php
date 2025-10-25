<?php
header('Content-Type: application/json');
include "DB_connection.php";

session_start();
if (!isset($_SESSION['role']) || !isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$isAdmin = ($_SESSION['role'] == 'admin');
$userId = $_SESSION['id'];

$data = json_decode(file_get_contents('php://input'), true);
$category_id = isset($data['category_id']) && $data['category_id'] !== '' ? (int)$data['category_id'] : null;
$project_id = isset($data['project_id']) && $data['project_id'] !== '' ? (int)$data['project_id'] : null;

$query = "SELECT 
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'pending' AND (due_date >= CURDATE() OR due_date IS NULL) THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status != 'completed' AND due_date < CURDATE() THEN 1 ELSE 0 END) as overdue
    FROM tasks";
    
$params = [];
if (!$isAdmin) {
    $query .= " WHERE assigned_to = ?";
    $params[] = $userId;
    if ($category_id) {
        $query .= " AND category_id = ?";
        $params[] = $category_id;
    }
    if ($project_id) {
        $query .= " AND project_id = ?";
        $params[] = $project_id;
    }
} else {
    if ($category_id || $project_id) {
        $query .= " WHERE 1=1";
        if ($category_id) {
            $query .= " AND category_id = ?";
            $params[] = $category_id;
        }
        if ($project_id) {
            $query .= " AND project_id = ?";
            $params[] = $project_id;
        }
    }
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'labels' => ['Hoàn thành', 'Đang thực hiện', 'Chờ xử lý', 'Quá hạn'],
    'data' => [
        $data['completed'] ?? 0,
        $data['in_progress'] ?? 0,
        $data['pending'] ?? 0,
        $data['overdue'] ?? 0
    ],
    'colors' => ['#59a14f', '#edc948', '#4e79a7', '#e15759']
]);
?>