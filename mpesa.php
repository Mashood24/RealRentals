<?php
$url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
$headers = [
    'Authorization: Bearer YOUR_ACCESS_TOKEN',
    'Content-Type: application/json'
];
$data = [
    'BusinessShortCode' => '174379', // Sandbox Lipa Na M-Pesa code
    'Password' => base64_encode('YOUR_PASSWORD'),
    'Timestamp' => date('YmdHis'),
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => '100',
    'PartyA' => '254712345678', // Customer phone
    'PartyB' => '174379',
    'PhoneNumber' => '254712345678',
    'CallBackURL' => 'https://yourdomain.com/callback',
    'AccountReference' => 'Test',
    'TransactionDesc' => 'Payment for Order #123'
];
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);
echo $response;
?>