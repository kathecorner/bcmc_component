<?php

$url = "https://checkout-test.adyen.com/v66/payments/details";

$payments_data = $_POST;

echo $payments_data;

$file = 'paymentsCallResponse.txt';
$current = file_get_contents($file);

$payments_call_resp = json_decode($current, True);

$paymentData = $payments_call_resp['action']['paymentData'];
$MD = $_POST['MD'];
$PaRes = $_POST['PaRes'];


$params = [
    "paymentData" => $paymentData,
    "details" => [
        "MD" => $MD,
        "PaRes" => $PaRes
    ]
];


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
        CURLOPT_POSTFIELDS     => json_encode($params),
        CURLOPT_HTTPHEADER     => $curl_http_header,
        CURLOPT_VERBOSE        => true
    ]
);

$payment_details_response = curl_exec($curl);

var_dump($payment_details_response);
