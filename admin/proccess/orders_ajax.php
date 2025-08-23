<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

// Delete order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['action']) && $_POST['action'] === 'delete') {
    $order_id = intval($_POST['order_id']);
    // Optionally, you could check if the order exists first
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->bind_param('i', $order_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
    exit;
}

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['new_status'];
    $allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status']);
        exit;
    }
    $stmt = $conn->prepare("UPDATE orders SET status = ?, updated_at = NOW() WHERE order_id = ?");
    $stmt->bind_param('si', $new_status, $order_id);
    $success = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $success]);
    exit;
}

// Search/filter orders
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $where = [];
    $params = [];
    $types = '';
    if ($search !== '') {
        $where[] = "(o.order_id LIKE ? OR u.username LIKE ? OR u.email LIKE ?)";
        $search_param = "%$search%";
        $params[] = &$search_param;
        $params[] = &$search_param;
        $params[] = &$search_param;
        $types .= 'sss';
    }
    if ($status !== '' && in_array($status, ['pending','processing','shipped','delivered','cancelled'])) {
        $where[] = "o.status = ?";
        $params[] = &$status;
        $types .= 's';
    }
    $sql = "SELECT o.*, u.username, u.email FROM orders o JOIN users u ON o.user_id = u.user_id";
    if ($where) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY o.created_at DESC";
    $stmt = $conn->prepare($sql);
    if ($params) {
        array_unshift($params, $types);
        call_user_func_array([$stmt, 'bind_param'], $params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    $stmt->close();
    echo json_encode(['success' => true, 'orders' => $orders]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']); 