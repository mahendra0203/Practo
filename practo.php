<?php
	function call_server($auth_token,$url,$method,$data = null){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		if($method == 'POST'){
			$request_url = "https://solo.practo.com". $url;
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		}else if ($method == 'PATCH'){
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PATCH");
			$request_url = "https://solo.practo.com".$url;
		}else if($method == 'DELETE'){
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
			$request_url = "https://solo.practo.com".$url;
		}else{
			$request_url = "https://solo.practo.com".$url;
		}
		$token_header = 'X-AUTH-TOKEN:'. $auth_token;
		curl_setopt($ch,CURLOPT_HTTPHEADER,array($token_header));
		curl_setopt($ch,CURLOPT_URL,$request_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$resp = curl_exec($ch);
		if(curl_errno($ch))
		{
			curl_close($ch);
			throw new Exception("Curl Error: " . curl_error($ch));
		}
		curl_close($ch);
		return $resp;
	 }

class Patient{
	private $_auth_token;
	
	/*Constructor*/
	function Patient($auth_token){
		$this->auth_token = $auth_token;
	}
	
	function update($auth_token){
		$this->auth_token = $auth_token;
	}

	function create(array $patient_info){
		$url = "/patients";
		if((!$patient_info['name']) || (strlen($patient_info['name']) == 0)){
			throw new Exception("Patient info array does not contain name");
		}
		$data = http_build_query($patient_info);
		$response = call_server($this->auth_token,$url,"POST",$data);
		//echo $response;
		return json_decode($response,true);
	}
	
	function read($patient_id){
		$url = '/patients/' .$patient_id;
		$response = call_server($this->auth_token,$url,"GET");
		//echo $response;
		return json_decode($response, true);
	}
	
	function read_all(){
		$url = '/patients' ;
		$response = call_server($this->auth_token,$url,"GET");
		//echo $response;
		return json_decode($response, true);
	}
	
	function edit($patient_id, array $info){
		$data = http_build_query($info);
		$url = "/patients/".$patient_id ."?".$data;
		$response = call_server($this->auth_token,$url,"PATCH",$data);
		//echo $response;
		return json_decode($response,true);
	}
	
	function delete($patient_id){
		$url = "/patients/". $patient_id;
		$response = call_server($this->auth_token,$url,"DELETE");
		//echo $response;
		return json_decode($response,true);
	
	}
	
	function modified_before(DateTime $datetime){
		$date_str = $datetime->format("Y-m-d H:i:s");
		$url = "/patients?modified_before=". $date_str;
		$response = call_server($this->auth_token,$url,"GET");
		////echo $response;
		return json_decode($response,true);
	}
	
	function modified_after(DateTime $datetime){
		$date_str = $datetime->format("Y-m-d H:i:s");
		$url = "/patients?modified_after=". $date_str;
		$response = call_server($this->auth_token,$url,"GET");
		return json_decode($response,true);
	}
	
	function count($with_deleted=null){
		if($with_deleted !== null){
			if(!is_bool($with_deleted)){
				throw new Exception("Value of with_deleted is not boolean");
			}
			$val = $with_deleted? 'true':'false';
			$url = "/patients/count?with_deleted=". $val;
			$response = call_server($this->auth_token,$url,"GET");
			////echo $response;
			return json_decode($response,true);
		}else{
			$url = "/patients/count";
			$response = call_server($this->auth_token,$url,"GET");
			////echo $response;
			return json_decode($response,true);
		}
	}
	/*Can be improved*/
	function get_custom($args){
		$arg_keys = array();
		$arg_values = array();
		if(array_key_exists('modified_before',$args) && (get_class($args['modified_before']) == "DateTime")){
			array_push($arg_keys,"modified_before");
			array_push($arg_values,$args['modified_before']->format("Y-m-d H:i:s"));
		}
		if (array_key_exists('modified_after',$args) && (get_class($args['modified_after']) == "DateTime")){
			array_push($arg_keys,"modified_after");
			array_push($arg_values,$args['modified_after']->format("Y-m-d H:i:s"));
		}

		if(array_key_exists('with_deleted',$args) && is_bool($args['with_deleted'])){
			array_push($arg_keys,"with_deleted");
			array_push($arg_values,$args['with_deleted']);
		}
		if(array_key_exists('limit',$args) && is_int($args['limit'])){
			array_push($arg_keys,"limit");
			array_push($arg_values,$args['limit']);
		}
		$arg_array = array_combine($arg_keys,$arg_values);
		$data = http_build_query($arg_array);
		$url = "/patients?".$data;
		$response = call_server($this->auth_token,$url,"GET");
		////echo $response;
		return json_decode($response,true);
	}
	
	function read_till($number){
		$url = "/patients?limit=".$number;
		$response = call_server($this->auth_token,$url,"GET");
		////echo $response;
		return json_decode($response,true);
	}
	
	function soft_deleted($ip){
		if(!is_bool($ip)){
			throw new Exception("Input is not a boolean");
		}
		$val = $ip? 'true':'false';
		$url = "/patients?with_deleted=". $val;
		$response = call_server($this->auth_token,$url,"GET");
		////echo $response;
		return json_decode($response,true);
	}
}

class Appointment{
	private $_auth_token;
	function Appointment($auth_token){
		$this->auth_token = $auth_token;
	}
	
	function update($auth_token){
		$this->auth_token = $auth_token;
	}
	function sample(){
		call_server("hi");
	}	
}
class Practo{
	public $value = "test";
	public $auth_token;
	public $username;
	
	public $patient;
	public $appointment;
	function Practo(array $ip_cred){
		if($ip_cred){
			if($ip_cred['username'] && $ip_cred['password']){
				$response = $this->login_request($ip_cred);
				////echo $response;
				/*Parse the response*/
				if(!$response){
						throw new Exception("FAILURE:Response not received");
				}
				$json_resp = json_decode($response,true);
				$this->auth_token = $json_resp['auth_token'];
				//echo $this->auth_token;
				
				
				/*Initialise the patient and appointment classes*/
				$this->patient = new Patient($this->auth_token);
				$this->appointment = new Appointment($this->auth_token);
				
				
				
			}else{
				throw new Exception("Username or password is null");
			}
		}else{
			throw new Exception("Input array is NULL");
		}
		
	}
	
	
	function get_practice_profile(){
		$url = "/practice/profile";
		$return_array = call_server($this->auth_token,$url,"GET");
		return json_decode($return_array,true);
	}
	
	function get_practice_settings(){
		$url = "/practice/settings";
		$return_array = call_server($this->auth_token,$url,"GET");
		return json_decode($return_array,true);
	}
	
	function get_practice_subscription(){
		$url = "/practice/subscription";
		$return_array = call_server($this->auth_token,$url,"GET");
		return json_decode($return_array,true);
	}
	
	protected function login_request(array $ip_cred){
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