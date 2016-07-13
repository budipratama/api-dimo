<?php
error_reporting("E_NOTICE");

$username 	= '';
$password 	= '';
$curl       = curl_init();
$url        = "http://localhost/api-dimo/api/inquiry";

$contents   = [
                ["id" => "paidAmmount", "value" => "10"],// Paid Payment
                ["id" => "merchantName", "value"=> "Salon Meicy"],//Card acceptor name/location – QR payment value is store name a
                ["id" => "userAPIKey", "value"=> "c2774d5254477ad7e1faad21f3f29d3cd5143398"],// User API Key
                ["id" => "tipAmount", "value"=> "0"],//Tipping Amount. With 2 (two) decimal places. 
                ["id" => "discAmount", "value"=> "000000000000"], // Discount Amount
                ["id" => "NOC", "value"=> "000"],// number of coupons
                ["id" => "discType", "value"=> "00000000000000000000"],// discount type
                ["id" => "loyaltyName", "value"=> "0000000000000000000000000000000000000000"],// loyalty name
                ["id" => "pointsRedeemed", "value"=> "0000000000000000000000"],// Points Redeemed
                ["id" => "amountRedeemed", "value"=> "0000000000000000000000"],// Amount Redeemed
                ["id" => "prodCode", "value"=> "12"],// Product Code – for each issuer, this value will be different.
                ["id" => "pinCode", "value"=> "137109"],// Pin Code 
                ["id" => "userName","value" => "tmoney.testing2@gmail.com"],// Username For TMONEY
                ["id" => "password","value" => "Telkom2016"],// Username For TMONEY
            ];

$data       = ["type"=>"inquiry","contents"=>$contents];
$data_string = json_encode($data,true);
// echo json_encode($data_string);die();
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERPWD => $username.":".$password,
    CURLOPT_TIMEOUT => 25, 
    CURLOPT_POST => 1,    
    CURLOPT_SSL_VERIFYHOST => 1,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)   
    ),
    CURLOPT_POSTFIELDS => $data_string,
));

$resp   = curl_exec($curl);
$status_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
$error_no = curl_errno($curl);
// echo "\nresult";
// print_r($resp);
// Check if any error occurred
if(curl_errno($curl))
{
    echo 'Curl error: '.curl_errno($curl) . curl_error($curl);
}
curl_close($curl);