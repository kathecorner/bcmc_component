<?php

$url = "https://checkout-test.adyen.com/v66/payments";

$payments_data = $_POST;

//echo $payments_data;

$additional_data = [
    //'reference' => 'KenjiW001',
    'reference' => 'playGround'.date("m/d/y"),
    'merchantAccount' => 'KenjiW',
    'amount' => [
        'value' => 1000,
        'currency' => 'USD'
    ],
    'returnUrl' => './return.php',
    'channel' => 'Web',
    'additionalData' => [
        'allow3DS2' => 'true'
    ],
    'origin' => 'http://127.0.0.1:8080',
    //'paymentMethod' => $payments_data->PaymentMethod,
    'storePaymentMethod'=> true,
    'shopperInteraction'=> 'Ecommerce',
    'recurringProcessingModel'=> 'CardOnFile',
    'shopperReference'=> 'Shopper_100'
];

$final_payment_data = array_merge($payments_data, $additional_data);

$curl_http_header = array(
    "X-API-Key: AQEyhmfxL4PJahZCw0m/n3Q5qf3VaY9UCJ1+XWZe9W27jmlZiv4PD4jhfNMofnLr2K5i8/0QwV1bDb7kfNy1WIxIIkxgBw==-lUKXT9IQ5GZ6d6RH4nnuOG4Bu//eJZxvoAOknIIddv4=-<anpTLkW{]ZgGy,7",
    "Content-Type: application/json"
);

$curl = curl_init();

curl_setopt_array(
    $curl,
    [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => json_encode($final_payment_data),
        CURLOPT_HTTPHEADER     => $curl_http_header,
        CURLOPT_VERBOSE        => true
    ]
);

$payments_response = curl_exec($curl);
$file = 'paymentsCallResponse.txt';
$current = $payments_response;
file_put_contents($file, $current);

header('Content-Type: application/json');
echo $payments_response;

curl_close($curl);
