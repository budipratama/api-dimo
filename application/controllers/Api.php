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
	protected $trans_id;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

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
	
	public function log($message)
	{
		error_log($message,3,PATH_LOG.'transaction_'.date('Y-m-d').'.log');	
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
		$url 	= "https://sandbox.flashiz.co.id/api/createInvoice?apiKey=7a5d03ff2c7f38c22e304899476035b42252d707&amount={$this->paidAmmount}&pinCode=1234&invoiceTagStart=true";
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
			$this->log(date('Y-m-d H:i:s')."[WARNING] Operation time out when generate invoice\t\n");
		    $this->response($response);
		}

		if (SHOW_DEBUG_API) 
		{
			echo "\n\nResult GENERATE INVOICE\n";
			echo $resp;
		}

		curl_close($curl);
		return json_decode($resp,true);
	}

	public function save_transaction($data)
	{
		$this->db->insert('tbl_transaction_history',$data);
	}

	public function action_inquiry()
    {
    	header('Content-Type: application/json');

    	$user = isset($_SERVER['PHP_AUTH_USER'])?$_SERVER['PHP_AUTH_USER']:"";
		$pass = isset($_SERVER['PHP_AUTH_PW'])?$_SERVER['PHP_AUTH_PW']:"";

    	if (!isset($_SERVER['PHP_AUTH_USER']) && !isset($_SERVER['PHP_AUTH_PW'])) 
    	{
	    	// recorded transaction failed in log (username, password and ip)
    		$this->log(date('Y-m-d H:i:s')."[LOGIN][WARNING] IP : {$_SERVER['HTTP_HOST']} \tUsername : $user \tPassword : $pass\t\n");
		} 
		else 
		{
			if ($_SERVER['PHP_AUTH_USER'] == USERNAME_API && $_SERVER['PHP_AUTH_PW'] == PASSWORD_API) 
			{
				$this->trans_id = date('YmdHis').rand(1,10000000);

				$requestBody  = file_get_contents('php://input');
	    		// recorded transaction in log (username, password and request body)
	    		$this->log(date('Y-m-d H:i:s')."[LOGIN] IP : {$_SERVER['HTTP_HOST']} \tUsername : $user \tPassword : $pass\t\n");
	    		$this->log(date('Y-m-d H:i:s')."[REQUEST] Parameter in mobile : $requestBody\t\n");

	    		// convert JSON into array
				$input 		= json_decode($requestBody, TRUE ); 
				
				// check json format in type
				if ($this->json_skeleton($input['type']) == false) 
				{
					$response = ['status'=>'failed','message' => 'JSON Format Incorret'];
					$this->log(date('Y-m-d H:i:s')."[FAILED] JSON Format Incorret\t\n");
					$this->response($response);
				}
				$content = [];

				// check json format in contents
				foreach ($input['contents'] as $key => $value) 
				{
					if ($this->json_skeleton($value['id']) == false) 
					{
						$response = ['status'=>'failed','message' => 'JSON Format Incorret'];
						$this->log(date('Y-m-d H:i:s')."[FAILED] JSON Format Incorret\t\n");
						$this->response($response);
					}

					$content[$value['id']] = $value['value'];
				}

				// $de62 = "                                        ".trim($content['discAmount']).trim($content['NOC']).trim($content['discType']).trim($content['loyaltyName']).trim($content['pointsRedeemed']).trim($content['amountRedeemed']);
				$de62 = "                                        ".trim($content['discAmount']).trim($content['NOC']).$content['discType'].$content['loyaltyName'].$content['pointsRedeemed'].$content['amountRedeemed'];
				// echo "jumlah length de62 : ".strlen($de62);die();
				$this->userName 	= $content['userName'];
				$this->password 	= $content['password'];
				$this->paidAmmount 	= $content['paidAmmount'];

				$invoiceID = $this->getInvoiceID();
				// request JSON Format for dimmo
				$contents 	= [
								["id" => "DE4" , "value" => $this->add_decimal(trim($content['paidAmmount']))], // Paid Amount 
								["id" => "DE7" , "value" => date('mdHis')], // Transmission Date and Time (GMT) date MMDDhhmmss
								["id" => "DE32" , "value" => "912"], // Acquiring institution identification code
								["id" => "DE33" , "value" => "912"], // Forwarding institution code
								["id" => "DE43" , "value" => trim($content['merchantName'])], // Merchant Name
								["id" => "DE48" , "value" => trim($content['userAPIKey'])], // User Api Key
								["id" => "DE54" , "value" => $this->add_decimal(trim($content['tipAmount']))], // Tipping Amount
								["id" => "DE61" , "value" => trim($invoiceID['invoiceId'])], // API GENERATE INVOICE 
								["id" => "DE62" , "value" => $de62], // white space 40 character + Discount Amount + Number of Coupons + Discount Type + Loyalty Name + Points Redeemed + Amount Redeemed
								// ["id" => "DE98" , "value" => trim($content['prodCode'])], // Product Code								
								["id" => "DE98" , "value" => 12], // Product Code								
				]; 
				$data        = ["type"=>"requestInquiry","contents"=>$contents];
				$data_string = json_encode($data,true);
				
				if (SHOW_DEBUG_API) 
				{
					echo "\n\nRequest body inquiry\n";
					echo json_encode($data,JSON_PRETTY_PRINT);
				}
				
				$this->log(date('Y-m-d H:i:s')."[INQUIRY] Request body inquiry : $data_string\t\n",PATH_LOG.'logs/transaction__'.date('Y-m-d').'.log');

				$url 	= "https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=AUTHREQ&trxid=requestInquiry&host_id=TLKMIDJA";
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
				$error_no = curl_errno($curl);
			
				// Check if any error occurred
				if(curl_errno($curl) == 28)
				{
					$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no" => 28];
					$this->log(date('Y-m-d H:i:s')."[WARNING] Operation time out when hit inquiry\t\n");
					$this->response($response);
				}

				curl_close($curl);
				
				$result = json_decode($resp,true);

				if (SHOW_DEBUG_API) 
				{
					echo "\n\nResponse inquiry\n";
					echo json_encode($result,JSON_PRETTY_PRINT);
				}
				$this->log(date('Y-m-d H:i:s')."[INQUIRY] Response body inquiry : $resp\t\n",PATH_LOG.'logs/transaction__'.date('Y-m-d').'.log');
				$this->save_transaction(['date'=>'NOW()','trans_id'=>$this->trans_id,'api'=>'INQUIRY','request_body'=>$data_string,'response_body'=>$resp,'ip'=>$_SERVER['HTTP_HOST']]);
				// response negatif
				if ($result['items'][9]['value'] != "00") 
				{
					$response = ["status" => "failed", "message" => "Response Negatif", "err_no" => $result['items'][9]['value']];
					$this->log(date('Y-m-d H:i:s')."[INQUIRY] Response Negatif error {$result['items'][9]['value']}\t\n");
					$this->response($response);
				}
				
				$login 		= $this->action_user_login_tmoney();
				$resp_topup = $this->action_topup_balance($login['user']['idTmoney'],$login['user']['idFusion'],$login['user']['token'],$content['pinCode']);
				
				$this->action_acknowledgment($result['items'][6]['value'],$result['items'][3]['value'],$result['items'][4]['value'],$result['items'][5]['value'],$resp_topup['reffNo'],$content['merchantName'],$content['userAPIKey'],$content['tipAmount'],$invoiceID['invoiceId'],$content['discAmount'],$content['NOC'],$de62);
			}
			else
			{
	    		$this->log(date('Y-m-d H:i:s')." IP : {$_SERVER['HTTP_HOST']} \tUsername : $user \tPassword : $pass\t\n");
				$this->authentication();
			}
			
		}
    }


    /**
     * [action_acknowledgment description]
     * 
     * @param  [type] $de15 Settlement Date
     * @param  [type] $de11 System Trace Audit Number
     * @param  [type] $de12 Local Transaction Time
     * @param  [type] $de13 Local Transaction Date
     * @param  [type] $de37 Retrieval Reference Number
     * @param  [type] $de43 Acceptor Name - QR Payment value is Merchant Name
     * @param  [type] $de48 User API Key
     * @param  [type] $de54 Tipping Amount. With 2 (two) decimal places.
     * @param  [type] $de61 Reserved – Private, information of the invoice ID.
     * @param  [type] $de62
     * @return [type]
     */
    public function action_acknowledgment($de15,$de11,$de12,$de13,$de37,$de43,$de48,$de54,$de61,$de62)
    {
    	// request JSON Format for dimmo
		$contents = [
						["id" => "DE4", "value" => $this->add_decimal(trim($this->paidAmmount))],
						["id" => "DE7", "value" => date('mdHis')],
						["id" => "DE11", "value" => trim($de11)],
						["id" => "DE12", "value" => trim($de12)],
						["id" => "DE13", "value" => trim($de13)],
						["id" => "DE15", "value" => trim($de15)],
						["id" => "DE32", "value" => "912"],
						["id" => "DE33", "value" => "912"],
						["id" => "DE37", "value" => trim($de37)],
						// ["id" => "DE37", "value" => 109876543210],
						["id" => "DE43", "value" => trim($de43)],
						["id" => "DE48", "value" => trim($de48)],
						["id" => "DE54", "value" => $this->add_decimal(trim($de54))],
						["id" => "DE61", "value" => trim($de61)],
						["id" => "DE62", "value" => $de62],
						["id" => "DE98", "value" => 12],
		];

		$data 	= ['type' => "requestPayment","contents" => $contents];

		$data_string = json_encode($data,true);
    	
    	$this->log(date('Y-m-d H:i:s')."[ACKNOWLEDGMENT] Request body inquiry : $data_string\t\n",PATH_LOG.'logs/transaction__'.date('Y-m-d').'.log');

    	if (SHOW_DEBUG_API) 
		{
			echo "\n\nRequest body acknowledgment\n";
			echo json_encode($data,JSON_PRETTY_PRINT);	
		}

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

		// Check if any error occurred
		if(curl_errno($curl) == 28)
		{
			$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no" => 28];
			$this->log(date('Y-m-d H:i:s')."[WARNING] Operation time out when hit acknowledgment\t\n");
			$this->response($response);
		}

		curl_close($curl);

		$result = json_decode($resp,true);

		if (SHOW_DEBUG_API) 
		{
			echo "\n\nResponse acknowledgment\n";
			echo json_encode($result,JSON_PRETTY_PRINT);	
		}
		$this->log(date('Y-m-d H:i:s')."[ACKNOWLEDGMENT] Response body inquiry : $resp\t\n",PATH_LOG.'logs/transaction__'.date('Y-m-d').'.log');
		$this->save_transaction(['date'=>'NOW()','trans_id'=>$this->trans_id,'api'=>'ACKNOWLEDGMENT','request_body'=>$data_string,'response_body'=>$resp,'ip'=>$_SERVER['HTTP_HOST']]);
		// response negatif
		if ($result['items'][9]['value'] != "00") 
		{
			$response = ["status" => "failed", "message" => "Response Negatif", "err_no" => $result['items'][9]['value']];
			$this->log(date('Y-m-d H:i:s')."[ACKNOWLEDGMENT] Response Negatif error {$result['items'][9]['value']}\t\n");
			$this->response($response);
		}
		
    }

    public function action_topup_balance($idTmoney,$idFusion,$token,$pin)
    {
		// step 1
		$url_api_login = "https://prodapi-app.tmoney.co.id/api/topup-balance";

		$curl 	= curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER 	=> 1,
			CURLOPT_URL 			=> $url_api_login,
			CURLOPT_TIMEOUT 		=> 25, 
			CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => array(
			    'transactionType' => 1,
			    'terminal' => "ANDROID-TMONEY",
			    'idTmoney' => trim($idTmoney),
			    'idFusion' => trim($idFusion),
			    'token' => trim($token),
			    'destAccount' => "ekoselaluceria@gmail.com",
			    // 'amount' => trim($this->paidAmmount),
			    'amount' => 5,//trim($this->paidAmmount),
			),
			CURLOPT_SSL_VERIFYHOST 	=> 1,
			CURLOPT_SSL_VERIFYPEER 	=> false,
		));
		// log post TOPUP BALANCE
		$this->log(date('Y-m-d H:i:s')."[TOPUP BALANCE TMONEY] POST idTmoney: $idTmoney, idFusion : $idFusion, token : $token}, destAccount : $userName, amount : $this->paidAmmount \t\n");
		$resp   	= curl_exec($curl);
		$error_no 	= curl_errno($curl);
			
			// Check if any error occurred
		if(curl_errno($curl) == 28)
		{
			$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no" => 28];
			$this->log(date('Y-m-d H:i:s')."[WARNING] Operation time out when topup balance\t\n");
			$this->response($response);
		}

		curl_close($curl);
		
		
		$this->log(date('Y-m-d H:i:s')."[TOPUP BALANCE TMONEY] Response : $resp \t\n");
		$result = json_decode($resp,true);

		if (SHOW_DEBUG_API) 
		{
			echo "\n\nResponse API TOPUP BALANCE \n";
			echo json_encode($result,JSON_PRETTY_PRINT);
		}

		// statement for failed
		if ($result['resultCode'] != "0" || $result['resultCode'] != "00") {
			$response = ["status" => "failed", "message" => $result['resultDesc'], "err_no" => $result['resultCode']];
			$this->log(date('Y-m-d H:i:s')."[WARNING][TOPUP-BALANCE] Error {$result['resultCode']}, Message {$result['resultDesc']}\t\n");
			$this->response($response);
		}

		$this->save_transaction(['date'=>'NOW()','trans_id'=>$this->trans_id,'api'=>'TOPUP-BALANCE','request_body'=>"POST transactionType 1, idTmoney $idTmoney, idFusion $idFusion, token $token, destAccount ekoselaluceria@gmail.com, amount {$this->paidAmmount}",'response_body'=>$resp,'ip'=>$_SERVER['HTTP_HOST']]);
		// step 2
		$curl 	= curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER 	=> 2,
			CURLOPT_URL 			=> $url_api_login,
			CURLOPT_TIMEOUT 		=> 25,
			CURLOPT_POST => 1, 
			CURLOPT_POSTFIELDS => array(
			    'transactionType' => 2,
			    'terminal' => "ANDROID-TMONEY",
			    'idTmoney' => trim($idTmoney),
			    'idFusion' => trim($idFusion),
			    'token' => trim($token),
			    'destAccount' => "ekoselaluceria@gmail.com",
			    // 'amount' => trim($this->paidAmmount),
			    'amount' => 5,//trim($this->paidAmmount),
			    'pin' => $pin,//trim($this->paidAmmount),
			    'transactionID' => $result['transactionID'],//trim($this->paidAmmount),
			    'refNo' => $result['refNo'],//trim($this->paidAmmount),
			),
			CURLOPT_SSL_VERIFYHOST 	=> 1,
			CURLOPT_SSL_VERIFYPEER 	=> false,
		));
		
		// log post TOPUP BALANCE
		$this->log(date('Y-m-d H:i:s')."[TOPUP BALANCE TMONEY] POST idTmoney: $idTmoney, idFusion : $idFusion, token : $token}, destAccount : $userName, amount : $this->paidAmmount \t\n");
		$resp   	= curl_exec($curl);
		$error_no 	= curl_errno($curl);
			
			// Check if any error occurred
		if(curl_errno($curl) == 28)
		{
			$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no" => 28];
			$this->log(date('Y-m-d H:i:s')."[WARNING] Operation time out when topup balance\t\n");
			$this->response($response);
		}

		curl_close($curl);
		
		
		$this->log(date('Y-m-d H:i:s')."[TOPUP BALANCE TMONEY] Response : $resp \t\n");
		$result = json_decode($resp,true);

		if (SHOW_DEBUG_API) 
		{
			echo "\n\nResponse API TOPUP BALANCE STEP 2\n";
			echo json_encode($result,JSON_PRETTY_PRINT);
		}

		// statement for failed
		if ($result['resultCode'] != "0" || $result['resultCode'] != "00") {
			$response = ["status" => "failed", "message" => $result['resultDesc'], "err_no" => $result['resultCode']];
			$this->log(date('Y-m-d H:i:s')."[WARNING][TOPUP-BALANCE] Error {$result['resultCode']}, Message {$result['resultDesc']}\t\n");
			$this->response($response);
		}

		$this->save_transaction(['date'=>'NOW()','trans_id'=>$this->trans_id,'api'=>'TOPUP-BALANCE','request_body'=>"POST transactionType 2, idTmoney $idTmoney, idFusion $idFusion, token $token, destAccount ekoselaluceria@gmail.com, amount {$this->paidAmmount}, pin $pin, transactionID {$result['transactionID']}, refNo {$result['refNo']}",'response_body'=>$resp,'ip'=>$_SERVER['HTTP_HOST']]);

		return $result;
    }

    public function action_user_login_tmoney()
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
		$this->log(date('Y-m-d H:i:s')."[LOGIN][TMONEY] POST userName: {$this->userName}, password : {$this->password}\t\n");
			
		// Check if any error occurred
		if(curl_errno($curl) == 28)
		{
			$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no" => 28];
			$this->log(date('Y-m-d H:i:s')."[WARNING] Operation time out when login at tmoney\t\n");
			$this->response($response);
		}

		curl_close($curl);
		
		
		$result = json_decode($resp,true);

		// log response
		$this->log(date('Y-m-d H:i:s')."[LOGIN][TMONEY] Response $resp \t\n");
		
		if (SHOW_DEBUG_API) 
		{
			echo "\n\nResponse API LOGIN \n";
			echo json_encode($result,JSON_PRETTY_PRINT);
		}

		// statement for failed
		if ($result['resultCode'] != "0" || $result['resultCode'] != "00") {
			$response = ["status" => "failed", "message" => $result['resultDesc'], "err_no" => $result['resultCode']];
			$this->log(date('Y-m-d H:i:s')."[WARNING][LOGIN-TMONEY] Error {$result['resultCode']}, Message {$result['resultDesc']}\t\n");
			$this->response($response);
		}
		$this->save_transaction(['date'=>'NOW()','trans_id'=>$this->trans_id,'api'=>'LOGIN-TMONEY','request_body'=>"POST userName: {$this->userName}, password : {$this->password}",'response_body'=>$resp,'ip'=>$_SERVER['HTTP_HOST']]);
		return $result;
    }

    /*public function action_dimo_login_tmoney()
    {
    	// hit API LOGIN TMONEY
		$url_api_login = "https://prodapi-app.tmoney.co.id/api/sign-in";

		$curl 	= curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_RETURNTRANSFER 	=> 1,
			CURLOPT_URL 			=> $url_api_login,
			CURLOPT_TIMEOUT 		=> 25, 
			CURLOPT_POSTFIELDS => array(
			    'userName' => "ekoselaluceria@gmail.com",
			    'password' => "Malang14",
			    'terminal' => "ANDROID-TMONEY",
			),
			CURLOPT_SSL_VERIFYHOST 	=> 1,
			CURLOPT_SSL_VERIFYPEER 	=> false,
		));

		$resp   = curl_exec($curl);
		$error_no = curl_errno($curl);

		// log post login tmoney
		$this->log(date('Y-m-d H:i:s')."[LOGIN][TMONEY] POST userName: ekoselaluceria@gmail.com, password : Malang14\t\n");
			
		// Check if any error occurred
		if(curl_errno($curl) == 28)
		{
			$response = ["status" => "failed", "message" => "OPERATION TIME OUT", "err_no" => 28];
			$this->log(date('Y-m-d H:i:s')."[WARNING] Operation time out when login at tmoney\t\n");
			$this->response($response);
		}

		curl_close($curl);
		
		
		$result = json_decode($resp,true);

		// log response
		$this->log(date('Y-m-d H:i:s')."[LOGIN][TMONEY] Response $resp \t\n");
		
		if (SHOW_DEBUG_API) 
		{
			echo "\n\nResponse API LOGIN DIMO \n";
			echo json_encode($result,JSON_PRETTY_PRINT);
		}

		// statement for failed
		if ($result['resultCode'] != "0" || $result['resultCode'] != "00") {
			$response = ["status" => "failed", "message" => $result['resultDesc'], "err_no" => $result['resultCode']];
			$this->log(date('Y-m-d H:i:s')."[WARNING][LOGIN-TMONEY] Error {$result['resultCode']}, Message {$result['resultDesc']}\t\n");
			$this->response($response);
		}

		return $result;
    }*/

    public function add_decimal($val)
    {
    	$length = strlen($val);
    	$price 	= substr($val,0,$length-2);
    	$suffix = substr($val,$length-2,2);
    	$result = $price.".".$suffix;

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