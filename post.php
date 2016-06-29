<?php
$username 	= 'Dimo';
$password 	= 'DimoMantapSepanjangMasa';
$curl   = curl_init();
$url 	=	"http://localhost/api-dimo/index.php/api/user";
// $url 	=	"http://localhost/api-dimo/get_all_header.php";
// $url 	=	"https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=TOKENREQ&host_id=BKKBIDJA";

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERPWD => $username.":".$password,
    CURLOPT_CONNECTTIMEOUT => 30, 
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => array(
        'id' => 5,
    ),
    /*CURLOPT_HTTPHEADER => array(
    	'Content-Type: application/json',
    ),*/
    // CURLOPT_HTTPAUTH => CURLAUTH_ANY,
    CURLOPT_SSL_VERIFYHOST => 1,
    CURLOPT_SSL_VERIFYPEER => false,
));

$server_output = curl_exec ($curl);
// $header = curl_getinfo($curl,CURLINFO_HEADER_SIZE);
curl_close ($curl);
echo "kamu\n\ndia";
echo $server_output;
echo substr();
echo "\nganteng";
print_r(json_decode($server_output, true));
/*$c = '{"content":"e3690c0dcc1e8f67dce45c9b753fcda9724b403757856fff70fdf8ec5c3ce024","message":"Token Was Create","status":"success"}';
print_r(json_decode($c,true));*/