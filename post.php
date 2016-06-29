<?php
$curl   = curl_init();
$url 	=	"http://localhost/api-dimo/index.php/api/user";

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    // CURLOPT_USERPWD => $username.":".$password,
    CURLOPT_HEADER => 1, 
    CURLOPT_VERBOSE => 1, 
    CURLOPT_CONNECTTIMEOUT => 30, 
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => array(
        'id' => 5,
    ),
));

$server_output = curl_exec ($curl);
curl_close ($curl);
echo $server_output;