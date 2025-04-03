<?php
require_once __DIR__.'/../includes/Mpesa.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate input
if (empty($data['phone']) || empty($data['amount']) || empty($data['reference'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$mpesa = new MpesaService();
$response = $mpesa->stkPush(
    $data['phone'],
    $data['amount'],
    $data['reference'],
    $data['description'] ?? 'Rent Payment'
);

if (isset($response['error'])) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $response['error']]);
} else {
    echo json_encode([
        'status' => 'success',
        'message' => 'Payment request sent to your phone',
        'data' => $response
    ]);
}