<?php
return [
    'env' => getenv('MPESA_ENV'),
    'consumer_key' => getenv('MPESA_CONSUMER_KEY'),
    'consumer_secret' => getenv('MPESA_CONSUMER_SECRET'),
    'shortcode' => getenv('MPESA_SHORTCODE'),
    'passkey' => getenv('MPESA_PASSKEY'),
    'callback_url' => getenv('MPESA_CALLBACK_URL')
];