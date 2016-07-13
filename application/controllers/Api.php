<?php
/**
 * Application Programming Interface
 *
 * @subpackage	Controller
 * @author		Budi Pratama<boedipratama19@gmail.com> or <irezpratama90@gmail.com>
 *
 */

error_reporting("E_NOTICE");
class Api extends CI_Controller
{
	protected $userName;
	protected $password;
	protected $paidAmmount;
	
	public function authentication()
	{
		header('WWW-Authenticate: Basic realm="My Realm"');
		header('HTTP/1.0 401 Unauthorized');
		$response = ['status'=>'failed','message' => 'Not Auth'];
		echo json_encode($response);
		exit;
	}

	public function debug($var)
	{
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}
	
	public function log($message,$path)
	{
		error_log($message,3,PATH_LOG.'logs/inquiry'.date('Y-m-d').'.log');	
	}

	public function json_skeleton($val)
	{
		switch ($val) {
			case 'inquiry':
				return true;
				break;
			case 'paidAmmount':
				return true;
				break;
			case 'merchantName':
				return true;
				break;
			case 'userAPIKey':
				return true;
				break;
			case 'tipAmount':
				return true;
				break;
			case 'discAmount':
				return true;
				break;
			case 'NOC':
				return true;
				break;
			case 'discType':
				return true;
				break;
			case 'loyaltyName':
				return true;
				break;
			case 'pointsRedeemed':
				return true;
				break;
			case 'amountRedeemed':
				return true;
				break;
			case 'prodCode':
				return true;
				break;
			case 'pinCode':
				return true;
				break;
			case 'userName':
				return true;
				break;
			case 'password':
				return true;
				break;
			default:
				return false;
				break;
		}
	}

	public function validationFormat($data)
	{
		if ($data['type'] != "inquiry") 
		{
			$response = ['status'=>'failed','message' => 'JSON Format Incorret u'];
			echo json_encode($response);
			exit;
		}
		foreach ($data['contents'] as $value) 
		{
			if ($this->json_skeleton($value['id']) == false) 
			{
				$response = ['status'=>'failed','message' => 'JSON Format Incorret'];
				echo json_encode($response);
				exit;
			}
		}
	}

	public function response($message)
	{
		echo json_encode($message);
		exit;
	}

	public function getInvoiceID()
	{
		$url 	= "https://sandbox.flashiz.co.id/api/createInvoice?apiKey=7a5d03ff2c7f38c22e304899476035b42252d707&amount=1350&pinCode=1234&invoiceTagStart=true";
		$curl 	= curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER 	=> 1,
		    CURLOPT_URL 			=> $url,
		    CURLOPT_TIMEOUT 		=> 25, 
		    CURLOPT_SSL_VERIFYHOST 	=> 1,
		    CURLOPT_SSL_VERIFYPEER 	=> false,
		));

		$resp   = curl_exec($curl);
		$error_no = curl_errno($curl);
		
		// Check if any error occurred
		if(curl_errno($curl) == 28)
		{
			$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no"=>28];
		    $this->response($response);
		}

		curl_close($curl);
		return json_decode($resp,true);
	}

	public function action_inquiry()
    {
    	header('Content-Type: application/json');

    	$user = isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:"";
		$pass = isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:"";

    	if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) 
    	{
	    	// recorded transaction failed in log (username, password and ip)
    		$this->log(date('Y-m-d H:i:s')." IP : {$_SERVER['HTTP_HOST']} \tUsername : $user \tPassword : $pass\t\n",PATH_LOG.'logs/inquiry'.date('Y-m-d').'.log');
		} 
		else 
		{
			if ($_SERVER['PHP_AUTH_USER'] == "contoh" && $_SERVER['PHP_AUTH_PW'] == "contoh") 
			{
				$requestBody  = file_get_contents('php://input');
	    		// recorded transaction in log (username, password and request body)
	    		$this->log(date('Y-m-d H:i:s')." IP : {$_SERVER['HTTP_HOST']} \tUsername : $user \tPassword : $pass\t Parameter in mobile : $requestBody\t\n",PATH_LOG.'logs/inquiry'.date('Y-m-d').'.log');

	    		// convert JSON into array
				$input 		= json_decode($requestBody, TRUE ); 
				
				// check json format in type
				if ($this->json_skeleton($input['type']) == false) 
				{
					$response = ['status'=>'failed','message' => 'JSON Format Incorret'];
					$this->response($response);
				}
				$content = [];

				// check json format in contents
				foreach ($input['contents'] as $key => $value) 
				{
					if ($this->json_skeleton($value['id']) == false) 
					{
						$response = ['status'=>'failed','message' => 'JSON Format Incorret'];

						$this->response($response);
					}

					$content[$value['id']] = $value['value'];
				}

				// $de62 = "                                        ".trim($content['discAmount']).trim($content['NOC']).trim($content['discType']).trim($content['loyaltyName']).trim($content['pointsRedeemed']).trim($content['amountRedeemed']);
				$de62 = "                                        ".trim($content['discAmount']).trim($content['NOC'])."                    "."                                        "."000000000000"."000000000000";
				$invoiceID = $this->getInvoiceID();

				// request JSON Format for dimmo
				$contents 	= [
								["id" => "DE4" , "value" => trim($content['paidAmmount'])], // Paid Amount 
								["id" => "DE7" , "value" => date('mdHis')], // Transmission Date and Time (GMT) date MMDDhhmmss
								["id" => "DE32" , "value" => "912"], // Acquiring institution identification code
								["id" => "DE33" , "value" => "912"], // Forwarding institution code
								["id" => "DE43" , "value" => trim($content['merchantName'])], // Merchant Name
								["id" => "DE48" , "value" => trim($content['userAPIKey'])], // User Api Key
								["id" => "DE54" , "value" => trim($content['tipAmount'])], // Tipping Amount
								["id" => "DE61" , "value" => trim($invoiceID['invoiceId'])], // API GENERATE INVOICE 
								["id" => "DE62" , "value" => trim($de62)], // white space 40 character + Discount Amount + Number of Coupons + Discount Type + Loyalty Name + Points Redeemed + Amount Redeemed
								// ["id" => "DE98" , "value" => trim($content['prodCode'])], // Product Code								
								["id" => "DE98" , "value" => 12], // Product Code								
				]; 
				$data       = ["type"=>"requestInquiry","contents"=>$contents];
				echo "\n\n";

				echo $data_string = json_encode($data,true);
				$this->log(date('Y-m-d H:i:s')." Request body inquiry : $data_string\t\n",PATH_LOG.'logs/inquiry'.date('Y-m-d').'.log');
				
				$url = "https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=AUTHREQ&trxid=requestInquiry&host_id=TLKMIDJA";
				$curl = curl_init();

				curl_setopt_array($curl, array(
				    CURLOPT_RETURNTRANSFER => 1,
				    CURLOPT_URL => $url,
				    CURLOPT_USERPWD => "Dimo:c7416bd25c29f3a164eed3cfc86ea70897b255e02feb97dac7afab7cbd1a6318",
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
				
				// Check if any error occurred
				if(curl_errno($curl))
				{
				    echo 'Curl error: '.curl_errno($curl) . curl_error($curl);
				}

				$resp   = curl_exec($curl);
				echo "\nresponse inquiry\n";
				echo $resp;

				$this->userName = $content['userName'];
				$this->password = $content['password'];
				$this->paidAmmount = $content['paidAmmount'];

				$login = $this->action_login_tmoney();
				$this->action_topup_balance($login['user']['idTmoney'],$login['user']['idFusion'],$login['user']['token']);
			}
			else
			{
	    		$this->log(date('Y-m-d H:i:s')." IP : {$_SERVER['HTTP_HOST']} \tUsername : $user \tPassword : $pass\t\n",PATH_LOG.'logs/inquiry'.date('Y-m-d').'.log');
				$this->authentication();
			}
			
		}
    }
    
    public function action_acknowledgment()
    {
    	// request JSON Format for dimmo
		$contents = [
						["id" => "DE4", "value" => trim()],
						["id" => "DE7", "value" => trim()],
						["id" => "DE11", "value" => trim()],
						["id" => "DE12", "value" => trim()],
						["id" => "DE13", "value" => trim()],
						["id" => "DE15", "value" => trim()],
						["id" => "DE32", "value" => trim()],
						["id" => "DE33", "value" => trim()],
						["id" => "DE37", "value" => trim()],
						["id" => "DE43", "value" => trim()],
						["id" => "DE48", "value" => trim()],
						["id" => "DE54", "value" => trim()],
						["id" => "DE61", "value" => trim()],
						["id" => "DE62", "value" => trim()],
						["id" => "DE98", "value" => trim()],
		];

		$data 	= ['type' => "requestPayment","contents" => $contents];

		$data_string = json_encode($data,true);
    	
    	$url 	= "https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=AUTHREQ&trxid=requestPayment&host_id=TLKMIDJA";
		$curl 	= curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_URL => $url,
			CURLOPT_USERPWD => "Dimo:c7416bd25c29f3a164eed3cfc86ea70897b255e02feb97dac7afab7cbd1a6318",
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
		echo "\nresponse inquiry\n";
		echo $resp;
    }

    public function action_topup_balance($idTmoney,$idFusion,$token)
    {
		$url_api_login = "https://prodapi-app.tmoney.co.id/api/topup-balance";

		$curl 	= curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER 	=> 1,
			CURLOPT_URL 			=> $url_api_login,
			CURLOPT_TIMEOUT 		=> 25, 
			CURLOPT_POSTFIELDS => array(
			    'transactionType' => 1,
			    'terminal' => "ANDROID-TMONEY",
			    'idTmoney' => trim($idTmoney),
			    'idFusion' => trim($idFusion),
			    'token' => trim($token),
			    'destAccount' => trim($this->userName),
			    'amount' => trim($this->paidAmmount),
			),
			CURLOPT_SSL_VERIFYHOST 	=> 1,
			CURLOPT_SSL_VERIFYPEER 	=> false,
		));
		// log post TOPUP BALANCE
		$this->log(date('Y-m-d H:i:s')."[TOPUP BALANCE TMONEY] POST idTmoney: $idTmoney, idFusion : $idFusion, token : $token}, destAccount : $userName, amount : $this->paidAmmount \t\n",PATH_LOG.'logs/inquiry'.date('Y-m-d').'.log');
		$resp   = curl_exec($curl);
		$error_no = curl_errno($curl);
			
			// Check if any error occurred
		if(curl_errno($curl) == 28)
		{
			$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no" => 28];
			   $this->response($response);
		}

		curl_close($curl);
		echo "\nresponse API TOPUP BALANCE \n";
		echo $resp;
		echo "\nencode json\n";
		$result = json_decode($resp,true);
		print_r($result);
		// statement for failed
		if ($result['resultCode'] != "0" || $result['resultCode'] != "00") {
				# gagal
		}
    }

    public function action_login_tmoney()
    {
    	// hit API LOGIN TMONEY
		$url_api_login = "https://prodapi-app.tmoney.co.id/api/sign-in";

		$curl 	= curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER 	=> 1,
			CURLOPT_URL 			=> $url_api_login,
			CURLOPT_TIMEOUT 		=> 25, 
			CURLOPT_POSTFIELDS => array(
			    'userName' => trim($this->userName),
			    'password' => trim($this->password),
			    'terminal' => "ANDROID-TMONEY",
			),
			CURLOPT_SSL_VERIFYHOST 	=> 1,
			CURLOPT_SSL_VERIFYPEER 	=> false,
		));

		$resp   = curl_exec($curl);
		$error_no = curl_errno($curl);
		// log post login tmoney
		$this->log(date('Y-m-d H:i:s')."[Login TMONEY] POST userName: {$this->userName}, password : {$this->password}\t\n",PATH_LOG.'logs/inquiry'.date('Y-m-d').'.log');
			
		// Check if any error occurred
		if(curl_errno($curl) == 28)
		{
			$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no" => 28];
			$this->response($response);
		}

		curl_close($curl);
		echo "\nresponse API LOGIN \n";
		echo $resp;
		echo "\nencode json\n";
		$result = json_decode($resp,true);

		// log response
		$this->log(date('Y-m-d H:i:s')."[Login TMONEY] Response $resp \t\n",PATH_LOG.'logs/inquiry'.date('Y-m-d').'.log');
			
		print_r($result);
		// statement for failed
		if ($result['resultCode'] != "0" || $result['resultCode'] != "00") {
				# gagal
		}

		return $result;
    }

    /*public function action_tmoney_login()
    {
    	header("Content-Type : application/json");
    	$user = ["lastLogin" => "0000-00-00 00:00:00","balance"=>"0","idTmoney"=>"195100001238","idFusion" => "+6219567166556","custName" => "R. Eko Permono Jati", "token" => "951074e548c481cf1b8539174525e44ee2e7dd72","tokenExpiry"=>"2016-04-30 11:05:27"];
    	$data = ["login" => true,"resultCode"=>0,"resultDesc"=>"SUKSES & di-approve oleh sistem","sessionId"=>"2bd198a7198b831b0a4086d4db12ebe8","timeStamp"=>"2016-04-29 11:05:27.117835","user"=>$user];
    	echo json_encode($data);
    }

    public function action_sleep()
    {
    	sleep(30);
    	echo "masuk";
    }*/
}		
?>