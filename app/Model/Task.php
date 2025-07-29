<?php

function insert_task($conn, $data) {
    try {
        $sql = "INSERT INTO tasks (title, description, assigned_to, due_date, status) VALUES(?,?,?,?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute(array_merge($data, ['pending'])); // Giả sử $data là [title, description, assigned_to, due_date]
    } catch (PDOException $e) {
        // Ghi log lỗi hoặc xử lý theo cách phù hợp
        error_log("Lỗi insert_task: " . $e->getMessage());
        return false;
    }
    return true;
}

function get_all_tasks($conn) {
    try {
        $sql = "SELECT * FROM tasks WHERE status != 'completed' ORDER BY id DESC";
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

function get_all_tasks_due_today($conn) {
    try {
        $sql = "SELECT * FROM tasks WHERE due_date = CURDATE() AND status != 'completed' ORDER BY id DESC";
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

function count_tasks_due_today($conn) {
    try {
        $sql = "SELECT id FROM tasks WHERE due_date = CURDATE() AND status != 'completed'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_tasks_due_today: " . $e->getMessage());
        return 0;
    }
}

function get_all_tasks_overdue($conn) {
    try {
        $sql = "SELECT * FROM tasks WHERE due_date < CURDATE() AND status != 'completed' ORDER BY id DESC";
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

function count_tasks_overdue($conn) {
    try {
        $sql = "SELECT id FROM tasks WHERE due_date < CURDATE() AND status != 'completed'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_tasks_overdue: " . $e->getMessage());
        return 0;
    }
}

function get_all_tasks_NoDeadline($conn) {
    try {
        $sql = "SELECT * FROM tasks WHERE status != 'completed' AND (due_date IS NULL OR due_date = '0000-00-00') ORDER BY id DESC";
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

function count_tasks_NoDeadline($conn) {
    try {
        $sql = "SELECT id FROM tasks WHERE status != 'completed' AND (due_date IS NULL OR due_date = '0000-00-00')";
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

function count_tasks($conn) {
    try {
        $sql = "SELECT id FROM tasks WHERE status != 'completed'";
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
        $stmt->execute($data);
    } catch (PDOException $e) {
        error_log("Lỗi update_task_status: " . $e->getMessage());
        return false;
    }
    return true;
}

function get_all_tasks_by_id($conn, $id) {
    try {
        $sql = "SELECT * FROM tasks WHERE assigned_to=? AND status != 'completed' ORDER BY id DESC";
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

function count_my_tasks($conn, $id) {
    try {
        $sql = "SELECT id FROM tasks WHERE assigned_to=? AND status != 'completed'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_my_tasks: " . $e->getMessage());
        return 0;
    }
}

function count_my_tasks_overdue($conn, $id) {
    try {
        $sql = "SELECT id FROM tasks WHERE due_date < CURDATE() AND status != 'completed' AND assigned_to=? AND due_date != '0000-00-00'";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    } catch (PDOException $e) {
        error_log("Lỗi count_my_tasks_overdue: " . $e->getMessage());
        return 0;
    }
}

function count_my_tasks_NoDeadline($conn, $id) {
    try {
        $sql = "SELECT id FROM tasks WHERE assigned_to=? AND status != 'completed' AND (due_date IS NULL OR due_date = '0000-00-00')";
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