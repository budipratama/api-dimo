<?php
error_reporting("E_NOTICE");

class Mobile extends CI_Controller{
	
	public function action_inquiry()
	{
		header('Content-Type: application/json');
		$username 	= USERNAME_API;
		$password 	= PASSWORD_API;
		$curl       = curl_init();
		$url        = "http://localhost/api-dimo/api/inquiry";

		$contents   = [
		                ["id" => "paidAmmount", "value" => "10000000"],// Paid Payment
		                ["id" => "merchantName", "value"=> "Salon Meicy"],//Card acceptor name/location – QR payment value is store name a
		                ["id" => "userAPIKey", "value"=> "9a2b9c40c167d58af112965340d84535d2ecfee4"],// User API Key
		                ["id" => "tipAmount", "value"=> "1"],//Tipping Amount. With 2 (two) decimal places. 
		                ["id" => "discAmount", "value"=> "000000000000"], // Discount Amount length 12 
		                ["id" => "NOC", "value"=> "000"],// number of coupons length 3
		                ["id" => "discType", "value"=> "                    "],// discount type -> Left justified, padded with space 20
		                ["id" => "loyaltyName", "value"=> "                                        "],// loyalty name -> Left justified, padded withspace 40
		                ["id" => "pointsRedeemed", "value"=> "000000000000"],// Points Redeemed
		                ["id" => "amountRedeemed", "value"=> "000000000000"],// Amount Redeemed
		                ["id" => "pinCode", "value"=> "137109"],// Pin Code 
		                ["id" => "idTmoney","value" => "195100001470"],// ID Tmoney For TMONEY 
		                ["id" => "idFusion","value" => "+6219565046162"],// ID Fusion For TMONEY 
		                ["id" => "token","value" => "546257923021e207b657ef98ce52b514316eb2a7151e00cad78142294756deeded558773d4bf4319"],// Username For TMONEY 
		];

		$data       = ["type"=>"inquiry","contents"=>$contents];
		$data_string = json_encode($data,true);
		// echo "request body mobile\n";
		// echo json_encode($data,JSON_PRETTY_PRINT);
		// echo json_encode($data_string);die();
		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		    CURLOPT_USERPWD => $username.":".$password,
		    CURLOPT_TIMEOUT => 30, 
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
		print_r($resp);
		// Check if any error occurred
		if(curl_errno($curl))
		{
		    echo 'Curl error: '.curl_errno($curl) . curl_error($curl);
		}
		curl_close($curl);
	}

	public function action_generate_token()
	{
		header('Content-Type: application/json');
		$username 	= USERNAME_API;
		$password 	= PASSWORD_API;
		$curl       = curl_init();
		$url        = "http://localhost/api-dimo/api/token";

		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		    CURLOPT_USERPWD => $username.":".$password,
		    CURLOPT_TIMEOUT => 30, 
		    CURLOPT_SSL_VERIFYHOST => 1,
		    CURLOPT_SSL_VERIFYPEER => false,
		));

		$resp   = curl_exec($curl);
		$status_code = curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$error_no = curl_errno($curl);
		print_r($resp);
		// Check if any error occurred
		if(curl_errno($curl))
		{
		    echo 'Curl error: '.curl_errno($curl) . curl_error($curl);
		}
		curl_close($curl);
	}

	public function action_req_user_api_key()
	{
		header('Content-Type: application/json');
		$username 	= USERNAME_API;
		$password 	= PASSWORD_API;
		$curl       = curl_init();
		$url        = "http://localhost/api-dimo/api/user-api-key";

		$contents   = [
		                ["id" => "idTmoney","value" => "195100001470"],// Username For TMONEY 
		];

		$data       = ["type"=>"userAPIKey","contents"=>$contents];
		$data_string = json_encode($data,true);


		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $url,
		    CURLOPT_POST => 1, 
		    CURLOPT_USERPWD => $username.":".$password,
		    CURLOPT_TIMEOUT => 30, 
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
		print_r($resp);
		// Check if any error occurred
		if(curl_errno($curl))
		{
		    echo 'Curl error: '.curl_errno($curl) . curl_error($curl);
		}
		curl_close($curl);
	}
}