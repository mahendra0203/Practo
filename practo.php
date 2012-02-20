<?php

class Practo{
	public $value = "test";
	public $auth_token;
	public $username;
	function Practo(array $ip_cred){
		if($ip_cred){
			if($ip_cred['username'] && $ip_cred['password']){
				$response = $this->call_server($ip_cred);
				//echo $response;
				/*Parse the response*/
				if(!$response){
						throw new Exception("FAILURE:Response not received");
				}
				$json_resp = json_decode($response,true);
				$this->auth_token = $json_resp['auth_token'];
				//echo $this->auth_token;
			}else{
				throw new Exception("Username or password is null");
			}
		}else{
			throw new Exception("Input array is NULL");
		}
		
	}
	
	
	function get_practice_profile(){
		$return_array = $this->make_request('profile');
		return $return_array;
	}
	
	function get_practice_settings(){
		$return_array = $this->make_request('settings');
		return $return_array;
	}
	
	function get_practice_subscription(){
		$return_array = $this->make_request('subscription');
		return $return_array;
	}
	protected function make_request($data){
		if($data == 'profile'){
			$url = "https://solo.practo.com/practice/profile";
		}elseif($data == 'settings'){
			$url = "https://solo.practo.com/practice/settings";
		}elseif($data == 'subscription'){
			$url = "https://solo.practo.com/practice/subscription";
		}
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		$token_header = 'X-AUTH-TOKEN:'. $this->auth_token;
		curl_setopt($ch,CURLOPT_HTTPHEADER,array($token_header));
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
		$resp = curl_exec($ch);
		if(curl_errno($ch))
		{
			curl_close($ch);
			throw new Exception("Curl Error: " . curl_error($ch));
		}
		curl_close($ch);
		//echo $resp;
		$return_array = json_decode($resp,true);
		return $return_array;
	
	}
	protected function call_server(array $ip_cred){
		$url = "https://solo.practo.com/sessions";
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$url);
		//curl_setopt($ch,CURLOPT_HEADER,1);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		$query_str = array('username'=>$ip_cred['username'],'password'=>$ip_cred['password']);
		curl_setopt($ch,CURLOPT_POST,count($query_str));
		curl_setopt($ch,CURLOPT_POSTFIELDS,http_build_query($query_str));
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($ch);
		if(curl_errno($ch))
		{
			echo "Curl Error: " . curl_error($ch);
			curl_close($ch);
			//echo $resp;
			return null;
		}
		curl_close($ch);
		//echo $resp;
		return $resp;
			
	}

}
?>