<?php
	function callServer($auth_token,$url,$method,$data = null){
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		if($method == 'POST'){
			$request_url = "https://solo.practo.com". $url;
			curl_setopt($ch,CURLOPT_POST,1);
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
		}else if ($method == 'PATCH'){
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"PATCH");
			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
			$request_url = "https://solo.practo.com".$url;
		}else if($method == 'DELETE'){
			curl_setopt($ch,CURLOPT_CUSTOMREQUEST,"DELETE");
			$request_url = "https://solo.practo.com".$url;
		}else{
			$request_url = "https://solo.practo.com".$url;
		}
		//echo $request_url;
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
	 
class CommonMethods{

	public function read($id){
		$url = $this->class_url.'/' .$id;
		$response = callServer($this->auth_token,$url,"GET");
		//echo $response;
		return json_decode($response, true);
	}
	
	public function readAll(){
		$url = $this->class_url ;
		$response = callServer($this->auth_token,$url,"GET");
		//echo $response;
		return json_decode($response, true);
	}
	
	public function delete($id){
		$url = $this->class_url."/". $id;
		$response = callServer($this->auth_token,$url,"DELETE");
		//echo $response;
		return json_decode($response,true);
	
	}
	
	public function modifiedBefore(DateTime $datetime){
		$date_str = $datetime->format("Y-m-d H:i:s");
		$url = $this->class_url."?modified_before=". $date_str;
		$response = callServer($this->auth_token,$url,"GET");
		////echo $response;
		return json_decode($response,true);
	}
	
	public function modifiedAfter(DateTime $datetime){
		$date_str = $datetime->format("Y-m-d H:i:s");
		$url = $this->class_url."?modified_after=". $date_str;
		$response = callServer($this->auth_token,$url,"GET");
		return json_decode($response,true);
	}
	
	public function count($with_deleted=null){
		if($with_deleted !== null){
			if(!is_bool($with_deleted)){
				throw new Exception("Value of with_deleted is not boolean");
			}
			$val = $with_deleted? 'true':'false';
			$url = $this->class_url."/count?with_deleted=". $val;
			$response = callServer($this->auth_token,$url,"GET");
			////echo $response;
			return json_decode($response,true);
		}else{
			$url = $this->class_url."/count";
			$response = callServer($this->auth_token,$url,"GET");
			////echo $response;
			return json_decode($response,true);
		}
	}
	/*Can be improved*/
	public function get_custom($args){
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
		$url = $this->class_url."?".$data;
		$response = callServer($this->auth_token,$url,"GET");
		////echo $response;
		return json_decode($response,true);
	}
	
	public function readTill($limit){
		$url = $this->class_url."?limit=".$limit;
		$response = callServer($this->auth_token,$url,"GET");
		////echo $response;
		return json_decode($response,true);
	}
	
	public function softDeleted($ip){
		if(!is_bool($ip)){
			throw new Exception("Input is not a boolean");
		}
		$val = $ip? 'true':'false';
		$url = $this->class_url."?with_deleted=". $val;
		$response = callServer($this->auth_token,$url,"GET");
		////echo $response;
		return json_decode($response,true);
	}


}
class Appointment extends CommonMethods{
	private $_auth_token;
	
	function Appointment($auth_token){
		$this->auth_token = $auth_token;
		$this->class_url = "/appointments";
	}
	
	function update($auth_token){
		$this->auth_token = $auth_token;
	}
	function create(array $ip_array,$sms_doctor, $email_doctor, $sms_patient, $email_patient){
		$url = $this->class_url;
		$data1 = http_build_query($ip_array);
		$keys = array();
		$values = array();
		$res_array = array();
		if (!is_bool($sms_doctor)){
			throw new Exception("Sms_doctor arg is not boolean");
		}else{
			array_push($keys,'sms_doctor');
			array_push($values,$sms_doctor? 'true':'false');
		}
		if (!is_bool($email_doctor)){
			throw new Exception("email_doctor arg is not boolean");
		}else{
			array_push($keys,'email_doctor');
			array_push($values,$email_doctor? 'true':'false');
		}
		
		if (!is_bool($sms_patient)){
			throw new Exception("Sms_patient arg is not boolean");
		}else{
			array_push($keys,'sms_patient');
			array_push($values,$sms_patient? 'true':'false');
		}
		
		if (!is_bool($email_patient)){
			throw new Exception("email_patient arg is not boolean");
		}else{
			array_push($keys,'email_patient');
			array_push($values,$email_patient? 'true':'false');
		}
		
		if (!empty($keys) && !empty($values)){
			$res_array = array_combine($keys,$values);
		}
		
		if(!empty($res_array))
			$data = $data1 ."&".http_build_query($res_array);
		else
			$data = $data1;
		$response = callServer($this->auth_token,$url,"POST",$data);
		return json_decode($response,true);
	}	
	
	function edit($id, $array_info, $sms_doctor, $email_doctor, $sms_patient, $email_patient){
		$url = $this->class_url."/".$id;
		$data1 = http_build_query($array_info);
		$keys = array();
		$values = array();
		$res_array = array();
		if (!is_bool($sms_doctor)){
			throw new Exception("Sms_doctor arg is not boolean");
		}else{
			array_push($keys,'sms_doctor');
			array_push($values,$sms_doctor? 'true':'false');
		}
		if (!is_bool($email_doctor)){
			throw new Exception("email_doctor arg is not boolean");
		}else{
			array_push($keys,'email_doctor');
			array_push($values,$email_doctor? 'true':'false');
		}
		
		if (!is_bool($sms_patient)){
			throw new Exception("Sms_patient arg is not boolean");
		}else{
			array_push($keys,'sms_patient');
			array_push($values,$sms_patient? 'true':'false');
		}
		
		if (!is_bool($email_patient)){
			throw new Exception("email_patient arg is not boolean");
		}else{
			array_push($keys,'email_patient');
			array_push($values,$email_patient? 'true':'false');
		}
		
		if (!empty($keys) && !empty($values)){
			$res_array = array_combine($keys,$values);
		}
		if(!empty($res_array))
			$data = $data1 ."&".http_build_query($res_array);
		else
			$data = $data1;
		$response = callServer($this->auth_token,$url,"PATCH",$data);
		//echo $response;
		return json_decode($response,true);	
	}	
}

class Patient extends CommonMethods{
	private $_auth_token;
	public $class_url = "/patients";
	
	/*Constructor*/
	function Patient($auth_token){
		$this->auth_token = $auth_token;
	}
	
	function update($auth_token){
		$this->auth_token = $auth_token;
	}

	function create(array $patient_info){
		$url = $this->class_url;
		if((!$patient_info['name']) || (strlen($patient_info['name']) == 0)){
			throw new Exception("Patient info array does not contain name");
		}
		$data = http_build_query($patient_info);
		$response = callServer($this->auth_token,$url,"POST",$data);
		//echo $response;
		return json_decode($response,true);
	}
	
	function edit($patient_id, array $info){
		$data = http_build_query($info);
		$url = $this->class_url."/".$patient_id;
		$response = callServer($this->auth_token,$url,"PATCH",$data);
		//echo $response;
		return json_decode($response,true);
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
				$response = $this->loginRequest($ip_cred);
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
	
	function getRole(){
		$url = "/roles";
		$response = callServer($this->auth_token,$url,"GET");
		return json_decode($response,true);
	}
	
	function setRole($role_id){
		$url = "/session";
		$response = callServer($this->auth_token,$url,"PATCH",$role_id);
		return json_decode($response,true);
	}
	function getPracticeProfile(){
		$url = "/practice/profile";
		$return_array = callServer($this->auth_token,$url,"GET");
		return json_decode($return_array,true);
	}
	
	function getPracticeSettings(){
		$url = "/practice/settings";
		$return_array = callServer($this->auth_token,$url,"GET");
		return json_decode($return_array,true);
	}
	
	function getPracticeSubscription(){
		$url = "/practice/subscription";
		$return_array = callServer($this->auth_token,$url,"GET");
		return json_decode($return_array,true);
	}
	
	protected function loginRequest(array $ip_cred){
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