<?php
require_once __DIR__.'/../includes/Mpesa.php';

header('Content-Type: application/json');

$mpesa = new MpesaService();
$callbackData = json_decode(file_get_contents('php://input'), true);

try {
    $mpesa->logTransaction($callbackData);
    
    if (isset($callbackData['Body']['stkCallback']['ResultCode'])) {
        $resultCode = $callbackData['Body']['stkCallback']['ResultCode'];
        
        if ($resultCode === 0) {
            // Payment was successful
            $metadata = $callbackData['Body']['stkCallback']['CallbackMetadata']['Item'];
            $paymentData = [
                'amount' => $metadata[0]['Value'],
                'receipt_number' => $metadata[1]['Value'],
                'transaction_date' => $metadata[3]['Value'],
                'phone_number' => $metadata[4]['Value']
            ];
            
            // Here you would:
            // 1. Update your database
            // 2. Send confirmation email
            // 3. Any other business logic
            
            http_response_code(200);
            echo json_encode(['status' => 'success']);
        } else {
            // Payment failed
            $errorMessage = $callbackData['Body']['stkCallback']['ResultDesc'];
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => $errorMessage]);
        }
    } else {
        throw new Exception('Invalid callback data');
    }
} catch (Exception $e) {
    $mpesa->logError($e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}