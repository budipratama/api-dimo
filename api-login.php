<?php
$username 	= 'ekoselaluceria@gmail.com';
$password 	= 'Telkom2016';
$curl   = curl_init();
$url 	=	"http://prodapi.tmoney.co.id/api/sign-in";
// $url 	=	"http://localhost/api-dimo/get_all_header.php";
// $url 	=	"https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=TOKENREQ&host_id=BKKBIDJA";

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERPWD => $username.":".$password,
    CURLOPT_CONNECTTIMEOUT => 30, 
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => array(
        'userName' => $username,
        'password' => $password,
        'terminal' => "ANDROID-TMONEY",
    ),
    CURLOPT_HTTPHEADER => array(
    	'Content-Type: application/json',
    ),
    // CURLOPT_HTTPAUTH => CURLAUTH_ANY,
    CURLOPT_SSL_VERIFYHOST => 1,
    CURLOPT_SSL_VERIFYPEER => false,
));

$server_output = curl_exec ($curl);
$err = curl_errno($curl);
// $header = curl_getinfo($curl,CURLINFO_HEADER_SIZE);
curl_close ($curl);
echo "kamu\n\ndia $err";
var_dump(curl_errno($curl));
var_dump($server_output);
echo $server_output;
echo "\nganteng";
print_r(json_decode($server_output, true));
/*$c = '{"content":"e3690c0dcc1e8f67dce45c9b753fcda9724b403757856fff70fdf8ec5c3ce024","message":"Token Was Create","status":"success"}';
print_r(json_decode($c,true));*/