<?php
class MyTools{
	protected $username;
	protected $password;
	public $status_code;
	public $response;

	public function run()
	{
		$curl = curl_init();

		curl_setopt_array($curl, array(
		    CURLOPT_RETURNTRANSFER => 1,
		    CURLOPT_URL => $this->getUrl(),
		    CURLOPT_USERPWD => $this->getUsername().":".$this->getPassword(),
		    // CURLOPT_HEADER => 1, 
		    CURLOPT_CONNECTTIMEOUT => 30, 
		    // CURLOPT_HTTPAUTH => CURLAUTH_ANY, 
		    CURLOPT_POST => 1,
		    CURLOPT_HTTPHEADER => array(
		        'Content-Type: application/json',
		        'Content-Length: ' . strlen($this->getData())
		    ),
		    CURLOPT_POSTFIELDS => $this->getData(),
		));
		$this->status_code 	= curl_getinfo($curl,CURLINFO_HTTP_CODE);
		$this->response 	= curl_exec($curl);
		curl_close($curl);
	}

	public function setUsername($username)
	{
		$this->username = $username;
	}

	public function setPassword($pass)
	{
		$this->password = $pass;
	}

	public function getUsername()
	{
		return $this->username;
	}

	public function getPassword()
	{
		return $this->password;
	}

	public function setUrl($url)
	{
		$this->url = $url;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setData($data)
	{	
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}
}
$contents   = [
                ["id"=>"DE4","value" => "000000000003000000"],
                ["id"=>"DE7","value"=>"0518172429"],
                ["id"=>"DE32","value"=>"153"],
                ["id"=>"DE33","value"=>"153"],
                ["id"=>"DE43","value"=>"Salon Meicy"],
                ["id"=>"DE48","value"=>"e784efa0d52df24b7d76da5f7780a03fb3130bfa"],
                ["id"=>"DE54","value"=>"000000000000000000"],
                ["id"=>"DE61","value"=>"cqmWWFWsOILw"],
                ["id"=>"DE62","value"=>"000000000000000000000000000000000000000"],
                ["id"=>"DE98","value"=>"02"],
            ];

$data       = ["type"=>"requestInquiry","contents"=>$contents];
$json_data  = json_encode($data);

$obj = new MyTools;
$obj->setUsername("Dimo"); 
$obj->setPassword("DimoMantapSepanjangMasa"); 
// $obj->setUrl("https://sandbox.flashiz.co.id/oauth/v1/as/request/AppOauth?ID=TOKENREQ&trxid=requestInquiry&host_id=BKKBIDJA"); 
$obj->setUrl("https://localhost/api-dimo/get_all_header.php"); 
$obj->setData($json_data);
echo "Status Code :";
echo $obj->status_code; 

echo "\nResponse :";
echo $obj->response; 
