<?php

include_once('config.php');

class Auth {
	
	private $session_key = "ZTE2P2";
	
	private $session_lifetime = 3600; // 1 hour
	
	
	function Auth(){
		
	}
	
	
	function login ($username, $password){
		
		$password = md5(sha1(trim($password)).'ppf');

		if($username == Config::$username && $password == Config::$password){
			$this->setCookie();
			return true;
		}
		
		
		return false;
	}
	
	
	function logout (){
		
		$this->clearCookie();
		
	}
	
	function isAuthenticated (){
		
		$cookie = $this->getCookie();
		
		if($cookie == 'puppifytrack'){
			return true;
		}
				
		return false;
	}
	
	/**
	* Create session cookie
	*/
	private function setCookie (){
				
		// setting cookie
		$expire = time()+$this->session_lifetime;
		setcookie($this->session_key, 'puppifytrack', $expire, "/");
		
	}
	
	private function clearCookie (){
				
		// setting cookie
		$expire = strtotime("2000-01-01");
		setcookie($this->session_key, '', $expire, "/");
		
	}
	
	private function getCookie (){
		
		$this->session = NULL;
		
		if(isset($_COOKIE[$this->session_key])){
			return $_COOKIE[$this->session_key];	
		}
		
		return false;
	}
	
	
}


?>