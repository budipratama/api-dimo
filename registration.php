<?php
$username 	= 'Dimo';
$password 	= 'DimoMantapSepanjangMasa';
$curl   = curl_init();
// $url 	=	"http://localhost/api-dimo/index.php/api/user";
// $url 	=	"http://localhost/api-dimo/get_all_header.php";
$url 	=	"https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=TOKENREQ&host_id=BKKBIDJA";

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERPWD => $username.":".$password,
    // CURLOPT_HTTPAUTH => CURLAUTH_ANY, 
    CURLOPT_CONNECTTIMEOUT => 30, 
    CURLOPT_SSL_VERIFYHOST => 1,
    CURLOPT_SSL_VERIFYPEER => false,
));

$server_output = curl_exec ($curl);
// $header = curl_getinfo($curl,CURLINFO_HEADER_SIZE);
curl_close ($curl);
echo $server_output;
echo "\nbudi";
print_r(json_decode($server_output));
// $json = json_decode($server_output);
// print_r($json);