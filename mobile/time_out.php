<?php
// error_reporting("E_NOTICE");
$url = "http://localhost/api-dimo/api/sleep";
$url = "https://sandbox.flashiz.co.id/api/createInvoice?apiKey=7a5d03ff2c7f38c22e304899476035b42252d707&amount=1350&pinCode=1234&invoiceTagStart=true";
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => $url,
    CURLOPT_TIMEOUT => 25, 
    CURLOPT_SSL_VERIFYHOST => 1,
    CURLOPT_SSL_VERIFYPEER => false,
));
$resp   = curl_exec($curl);
$error_no = curl_errno($curl);
echo "\nresult";
print_r($resp);
// Check if any error occurred
if(curl_errno($curl) == 28)
{
    echo 'Curl error: Time out';
}
curl_close($curl);