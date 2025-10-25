<?php
header('Content-Type: application/json');
include 'DB_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$category_id = $data['category_id'] ?? null;
$project_id = $data['project_id'] ?? null;
$user_id = $data['user_id'] ?? null;

$query = "
    SELECT t.id, t.title, t.status, t.due_date, t.priority, 
           c.name as category_name, p.name as project_name
    FROM tasks t
    LEFT JOIN task_categories c ON t.category_id = c.id
    LEFT JOIN projects p ON t.project_id = p.id
    WHERE t.assigned_to = ?
";

$params = [$user_id];

if ($category_id) {
    $query .= " AND t.category_id = ?";
    $params[] = $category_id;
}

if ($project_id) {
    $query .= " AND t.project_id = ?";
    $params[] = $project_id;
}

$query .= " ORDER BY 
    CASE 
        WHEN t.status = 'in_progress' THEN 1
        WHEN t.status = 'pending' AND t.due_date < CURDATE() THEN 2
        WHEN t.status = 'pending' THEN 3
        ELSE 4
    END,
    t.due_date ASC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($tasks);
?>