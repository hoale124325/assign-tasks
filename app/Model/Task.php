<?php

function insert_task($conn, $data) {
    try {
        // Kiểm tra số lượng tham số, giả sử $data là [title, description, assigned_to, due_date, attachment]
        if (count($data) < 4 || count($data) > 5) {
            throw new Exception("Số lượng tham số không đúng cho insert_task");
        }
        $attachment = isset($data[4]) ? $data[4] : null; // Nếu không có attachment, gán null
        $sql = "INSERT INTO tasks (title, description, assigned_to, due_date, attachment, status) VALUES(?,?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $status = 'pending'; // Giá trị mặc định cho status
        $stmt->execute(array_merge(array_slice($data, 0, 4), [$attachment, $status]));
    } catch (PDOException | Exception $e) {
        error_log("Lỗi insert_task: " . $e->getMessage());
        return false;
    }
    return true;
}

function get_all_tasks($conn, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT * FROM tasks";
        if (!$include_completed) {
            $sql .= " WHERE status != 'completed'";
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);

        if ($stmt->rowCount() > 0) {
            $tasks = $stmt->fetchAll();
        } else {
            $tasks = 0;
        }
    } catch (PDOException $e) {
        error_log("Lỗi get_all_tasks: " . $e->getMessage());
        $tasks = 0;
    }
    return $tasks;
}

function get_all_tasks_due_today($conn, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT * FROM tasks WHERE due_date = CURDATE()";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);

        if ($stmt->rowCount() > 0) {
            $tasks = $stmt->fetchAll();
        } else {
            $tasks = 0;
        }
    } catch (PDOException $e) {
        error_log("Lỗi get_all_tasks_due_today: " . $e->getMessage());
        $tasks = 0;
    }
    return $tasks;
}

function count_tasks_due_today($conn, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT id FROM tasks WHERE due_date = CURDATE()";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_tasks_due_today: " . $e->getMessage());
        return 0;
    }
}

function get_all_tasks_overdue($conn, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT * FROM tasks WHERE due_date < CURDATE()";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);

        if ($stmt->rowCount() > 0) {
            $tasks = $stmt->fetchAll();
        } else {
            $tasks = 0;
        }
    } catch (PDOException $e) {
        error_log("Lỗi get_all_tasks_overdue: " . $e->getMessage());
        $tasks = 0;
    }
    return $tasks;
}

function count_tasks_overdue($conn, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT id FROM tasks WHERE due_date < CURDATE()";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_tasks_overdue: " . $e->getMessage());
        return 0;
    }
}

function get_all_tasks_NoDeadline($conn, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT * FROM tasks WHERE (due_date IS NULL OR due_date = '0000-00-00')";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);

        if ($stmt->rowCount() > 0) {
            $tasks = $stmt->fetchAll();
        } else {
            $tasks = 0;
        }
    } catch (PDOException $e) {
        error_log("Lỗi get_all_tasks_NoDeadline: " . $e->getMessage());
        $tasks = 0;
    }
    return $tasks;
}

function count_tasks_NoDeadline($conn, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT id FROM tasks WHERE (due_date IS NULL OR due_date = '0000-00-00')";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_tasks_NoDeadline: " . $e->getMessage());
        return 0;
    }
}

function delete_task($conn, $data) {
    try {
        $sql = "DELETE FROM tasks WHERE id=? ";
        $stmt = $conn->prepare($sql);
        $stmt->execute($data);
    } catch (PDOException $e) {
        error_log("Lỗi delete_task: " . $e->getMessage());
        return false;
    }
    return true;
}

function get_task_by_id($conn, $id) {
    try {
        $sql = "SELECT * FROM tasks WHERE id =? ";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $task = $stmt->fetch();
        } else {
            $task = 0;
        }
    } catch (PDOException $e) {
        error_log("Lỗi get_task_by_id: " . $e->getMessage());
        $task = 0;
    }
    return $task;
}

function count_tasks($conn, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT id FROM tasks";
        if (!$include_completed) {
            $sql .= " WHERE status != 'completed'";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_tasks: " . $e->getMessage());
        return 0;
    }
}

function update_task($conn, $data) {
    try {
        if (count($data) != 6) {
            throw new Exception("Số lượng tham số không đúng cho update_task");
        }
        $sql = "UPDATE tasks SET title=?, description=?, assigned_to=?, due_date=?, status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($data); // Giả sử $data là [title, description, assigned_to, due_date, status, id]
    } catch (PDOException | Exception $e) {
        error_log("Lỗi update_task: " . $e->getMessage());
        return false;
    }
    return true;
}

function update_task_status($conn, $data) {
    try {
        $sql = "UPDATE tasks SET status=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $status = $data[0];
        $id = $data[1];

        // Kiểm tra giá trị status hợp lệ với enum
        $valid_statuses = ['pending', 'in_progress', 'completed'];
        if (!in_array($status, $valid_statuses)) {
            error_log("Invalid status value: $status");
            return false;
        }

        $stmt->execute($data);
    } catch (PDOException $e) {
        error_log("Lỗi update_task_status: " . $e->getMessage());
        return false;
    }
    return true;
}

function get_all_tasks_by_id($conn, $id, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT * FROM tasks WHERE assigned_to=?";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $sql .= " ORDER BY id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);

        if ($stmt->rowCount() > 0) {
            $tasks = $stmt->fetchAll();
        } else {
            $tasks = 0;
        }
    } catch (PDOException $e) {
        error_log("Lỗi get_all_tasks_by_id: " . $e->getMessage());
        $tasks = 0;
    }
    return $tasks;
}

function count_pending_tasks($conn) {
    try {
        $sql = "SELECT id FROM tasks WHERE status = 'pending'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_pending_tasks: " . $e->getMessage());
        return 0;
    }
}

function count_in_progress_tasks($conn) {
    try {
        $sql = "SELECT id FROM tasks WHERE status = 'in_progress'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_in_progress_tasks: " . $e->getMessage());
        return 0;
    }
}

function count_completed_tasks($conn) {
    try {
        $sql = "SELECT id FROM tasks WHERE status = 'completed'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_completed_tasks: " . $e->getMessage());
        return 0;
    }
}

function count_my_tasks($conn, $id, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT id FROM tasks WHERE assigned_to=?";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_my_tasks: " . $e->getMessage());
        return 0;
    }
}

function count_my_tasks_overdue($conn, $id, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT id FROM tasks WHERE due_date < CURDATE() AND status != 'completed' AND assigned_to=? AND due_date != '0000-00-00'";
        if ($include_completed) {
            $sql = "SELECT id FROM tasks WHERE due_date < CURDATE() AND assigned_to=? AND due_date != '0000-00-00'";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_my_tasks_overdue: " . $e->getMessage());
        return 0;
    }
}

function count_my_tasks_NoDeadline($conn, $id, $include_completed = false) { // Thêm tham số $include_completed
    try {
        $sql = "SELECT id FROM tasks WHERE assigned_to=? AND (due_date IS NULL OR due_date = '0000-00-00')";
        if (!$include_completed) {
            $sql .= " AND status != 'completed'";
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_my_tasks_NoDeadline: " . $e->getMessage());
        return 0;
    }
}

function count_my_pending_tasks($conn, $id) {
    try {
        $sql = "SELECT id FROM tasks WHERE status = 'pending' AND assigned_to=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_my_pending_tasks: " . $e->getMessage());
        return 0;
    }
}

function count_my_in_progress_tasks($conn, $id) {
    try {
        $sql = "SELECT id FROM tasks WHERE status = 'in_progress' AND assigned_to=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_my_in_progress_tasks: " . $e->getMessage());
        return 0;
    }
}

function count_my_completed_tasks($conn, $id) {
    try {
        $sql = "SELECT id FROM tasks WHERE status = 'completed' AND assigned_to=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_my_completed_tasks: " . $e->getMessage());
        return 0;
    }
}

// Hàm mới để cập nhật cả status và completed_file
function update_task_with_file($conn, $data) {
    try {
        if (count($data) != 3) {
            throw new Exception("Số lượng tham số không đúng cho update_task_with_file");
        }
        $sql = "UPDATE tasks SET status=?, completed_file=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($data); // Giả sử $data là [status, completed_file, id]
    } catch (PDOException | Exception $e) {
        error_log("Lỗi update_task_with_file: " . $e->getMessage());
        return false;
    }
    return true;
}

?>