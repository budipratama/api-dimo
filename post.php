<?php
$username 	= 'Dimo';
$password 	= 'DimoMantapSepanjangMasa';
$curl   = curl_init();
$url    =   "http://localhost/api-dimo/index.php/api_master/user";
$url 	=	"http://devapi.tmoney.co.id/api/sign-in";
// $url 	=	"http://localhost/api-dimo/get_all_header.php";
// $url 	=	"https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=TOKENREQ&host_id=BKKBIDJA";

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    // CURLOPT_USERPWD => $username.":".$password,
    CURLOPT_CONNECTTIMEOUT => 30, 
    CURLOPT_POST => 1,
    CURLOPT_HTTPHEADER => 1,
    CURLOPT_POSTFIELDS => array(
        'userName' => "ekoselaluceria@gmail.com",
        'password' => "T3lkom123",
        'terminal' => "ANDROID-TMONEY",
    ),
    CURLOPT_HTTPHEADER => array(
    	'Content-Type: application/json',
    ),
    // CURLOPT_HTTPAUTH => CURLAUTH_ANY,
    CURLOPT_SSL_VERIFYHOST => 1,
    CURLOPT_SSL_VERIFYPEER => false,
));

$resp   = curl_exec($curl);
$error_no = curl_errno($curl);
echo "\nresult";
print_r($resp);
// Check if any error occurred
if(curl_errno($curl))
{echo curl_errno($curl)."\n";
    echo 'Curl error: Time out '.curl_error($curl);
}
curl_close($curl);