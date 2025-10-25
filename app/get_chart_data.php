<?php
header('Content-Type: application/json');
include 'DB_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$category_id = $data['category_id'] ?? null;
$project_id = $data['project_id'] ?? null;
$is_admin = $data['is_admin'] ?? false;
$user_id = $data['user_id'] ?? null;

$query = "SELECT 
    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
    SUM(CASE WHEN status = 'pending' AND (due_date >= CURDATE() OR due_date IS NULL) THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status != 'completed' AND due_date < CURDATE() THEN 1 ELSE 0 END) as overdue
    FROM tasks";

$params = [];
$where = [];

if (!$is_admin) {
    $where[] = "assigned_to = ?";
    $params[] = $user_id;
}

if ($category_id) {
    $where[] = "category_id = ?";
    $params[] = $category_id;
}

if ($project_id) {
    $where[] = "project_id = ?";
    $params[] = $project_id;
}

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode([
    'labels' => ['Hoàn thành', 'Đang thực hiện', 'Chờ xử lý', 'Quá hạn'],
    'data' => [
        $result['completed'] ?? 0,
        $result['in_progress'] ?? 0,
        $result['pending'] ?? 0,
        $result['overdue'] ?? 0
    ],
    'colors' => ['#59a14f', '#edc948', '#4e79a7', '#e15759']
]);
?>