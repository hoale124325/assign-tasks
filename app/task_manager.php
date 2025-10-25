<?php
class Task {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Lấy danh sách công việc theo user_id
    public function getTasksByUser($user_id) {
        $query = "SELECT * FROM tasks WHERE assigned_to = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm công việc mới (bao gồm attachment)
    public function createTask($title, $description, $assigned_to, $due_date, $attachment = null) {
        try {
            $query = "INSERT INTO tasks (title, description, assigned_to, due_date, attachment, status) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($query);
            $status = 'pending'; // Giá trị mặc định
            $stmt->execute([$title, $description, $assigned_to, $due_date, $attachment, $status]);
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi createTask: " . $e->getMessage());
            return false;
        }
    }

    // Cập nhật công việc (bao gồm attachment)
    public function updateTask($id, $title, $description, $assigned_to, $due_date, $attachment = null, $status = 'pending') {
        try {
            $query = "UPDATE tasks SET title = ?, description = ?, assigned_to = ?, due_date = ?, attachment = ?, status = ? WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$title, $description, $assigned_to, $due_date, $attachment, $status, $id]);
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi updateTask: " . $e->getMessage());
            return false;
        }
    }

    // Lấy thông tin công việc theo ID
    public function getTaskById($id) {
        $query = "SELECT * FROM tasks WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // Xóa công việc
    public function deleteTask($id) {
        try {
            $query = "DELETE FROM tasks WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$id]);
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi deleteTask: " . $e->getMessage());
            return false;
        }
    }
}
?>