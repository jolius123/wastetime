<?php
header('Content-Type: application/json; charset=utf-8');
$host = 'localhost';
$user = 'root';
$pass = ''; 
$db = 'waste_mgmt';


$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
try {
$pdo = new PDO($dsn, $user, $pass, [
PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);
} catch (Exception $e) {
http_response_code(500);
echo json_encode(['error' => 'DB connection failed: ' . $e->getMessage()]);
exit;
}


$action = $_POST['action'] ?? $_GET['action'] ?? '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
if ($action === 'create') {
$user_name = trim($_POST['user'] ?? '');
$waste_type = trim($_POST['wasteType'] ?? '');
$location = trim($_POST['location'] ?? '');


if ($user_name === '' || $waste_type === '' || $location === '') {
echo json_encode(['error' => 'Missing required fields']);
exit;
}


$stmt = $pdo->prepare("INSERT INTO requests (user_name, waste_type, location) VALUES (:u,:w,:l)");
$stmt->execute([':u' => $user_name, ':w' => $waste_type, ':l' => $location]);
echo json_encode(['success' => true, 'message' => 'Request created successfully', 'id' => $pdo->lastInsertId()]);
exit;
}


if ($action === 'cancel') {
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { echo json_encode(['error' => 'Invalid id']); exit; }
$stmt = $pdo->prepare("UPDATE requests SET status='Cancelled' WHERE id = :id");
$stmt->execute([':id' => $id]);
echo json_encode(['success' => true, 'message' => 'Request cancelled']);
exit;
}


if ($action === 'complete') {
$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) { echo json_encode(['error' => 'Invalid id']); exit; }
$stmt = $pdo->prepare("UPDATE requests SET status='Completed' WHERE id = :id");
$stmt->execute([':id' => $id]);
echo json_encode(['success' => true, 'message' => 'Request marked completed']);
exit;

?>
