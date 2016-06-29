<?php
$username 	= 'Dimo';
$password 	= 'DimoMantapSepanjangMasa';
$url 		= "https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=TOKENREQ&host_id=BKKBIDJA";
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_USERPWD => $username.":".$password,
    CURLOPT_HEADER => 1, 
    CURLOPT_VERBOSE => 1, 
    CURLOPT_CONNECTTIMEOUT => 30,
     
));

$resp 	= curl_exec($curl);
$header = curl_getinfo($curl,CURLINFO_HEADER_SIZE);
echo "result";
print_r($resp);
echo "\nheader";
print_r($header);

curl_close($curl);

