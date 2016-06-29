<?php
$username 	= 'Dimo';
$password 	= 'DimoMantapSepanjangMasa';
$url        = "https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=TOKENREQ&trxid=requestInquiry&host_id=BKKBIDJA";
// $url 		= "https://localhost/api-dimo/get_all_header.php";
$contents   = [
                ["id"=>"DE4","value" => "3000000"],// jumlah pembayaran
                ["id"=>"DE7","value"=>"0518172429"],// Transmission Date and Time (GMT)
                ["id"=>"DE32","value"=>"153"],//Acquiring institution identification code 
                ["id"=>"DE33","value"=>"153"],//Forwarding institution code 
                ["id"=>"DE43","value"=>"Salon Meicy"],//Card acceptor name/location – QR payment value is store name a
                ["id"=>"DE48","value"=>"e784efa0d52df24b7d76da5f7780a03fb3130bfa"],// User API Key
                ["id"=>"DE54","value"=>"000000000000000000"],//Tipping Amount. With 2 (two) decimal places. 
                ["id"=>"DE61","value"=>"cqmWWFWsOILw"], //Reserved – Private, information of the invoice ID. 
                ["id"=>"DE62","value"=>"000000000000000000000000000000000000000"], //
                ["id"=>"DE98","value"=>"02"],// Product Code – for each issuer, this value will be different.
            ];

$data       = ["type"=>"requestInquiry","contents"=>$contents];
$data_string = json_encode($data);

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERPWD => $username.":".$password,
    // CURLOPT_HEADER => 1, 
    CURLOPT_CONNECTTIMEOUT => 30, 
    // CURLOPT_HTTPAUTH => CURLAUTH_ANY, 
    CURLOPT_POST => 1,    
    CURLOPT_SSL_VERIFYHOST => 1,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string)   
    ),
    CURLOPT_POSTFIELDS => $data_string,
));
$status_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
$error_no = curl_errno($curl);
$resp 	= curl_exec($curl);
echo "\nresult";
print_r($resp);

curl_close($curl);

