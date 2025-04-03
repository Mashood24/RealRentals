<?php
require_once __DIR__.'/../config/mpesa.php';

class MpesaService {
    private $config;
    
    public function __construct() {
        $this->config = include(__DIR__.'/../config/mpesa.php');
    }
    
    public function getAccessToken() {
        $credentials = base64_encode($this->config['consumer_key'].':'.$this->config['consumer_secret']);
        
        $url = ($this->config['env'] == 'sandbox')
            ? 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
        
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => ['Authorization: Basic '.$credentials],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response)->access_token;
    }
    
    public function stkPush($phone, $amount, $reference, $description = 'Rent Payment') {
        try {
            $accessToken = $this->getAccessToken();
            $timestamp = date('YmdHis');
            $password = base64_encode($this->config['shortcode'].$this->config['passkey'].$timestamp);
            
            $payload = [
                'BusinessShortCode' => $this->config['shortcode'],
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => 'CustomerPayBillOnline',
                'Amount' => $amount,
                'PartyA' => $this->formatPhone($phone),
                'PartyB' => $this->config['shortcode'],
                'PhoneNumber' => $this->formatPhone($phone),
                'CallBackURL' => $this->config['callback_url'],
                'AccountReference' => $reference,
                'TransactionDesc' => substr($description, 0, 13)
            ];
            
            $url = ($this->config['env'] == 'sandbox')
                ? 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
                : 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';
            
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer '.$accessToken,
                    'Content-Type: application/json'
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30
            ]);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response, true);
            
        } catch (Exception $e) {
            $this->logError($e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
    
    private function formatPhone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (strlen($phone) === 9 && $phone[0] === '7') {
            return '254'.$phone;
        } elseif (strlen($phone) === 10 && $phone[0] === '0') {
            return '254'.substr($phone, 1);
        }
        
        return $phone;
    }
    
    public function logTransaction($data) {
        $logFile = __DIR__.'/../logs/transactions.log';
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'data' => $data
        ];
        file_put_contents($logFile, json_encode($logData).PHP_EOL, FILE_APPEND);
    }
    
    private function logError($message) {
        $logFile = __DIR__.'/../logs/error.log';
        file_put_contents($logFile, date('Y-m-d H:i:s').' - '.$message.PHP_EOL, FILE_APPEND);
    }
}