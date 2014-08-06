<?php
class appCred{
	private $clientId = '811775410830-md5k1ke6hautv4agn0uemjr1vd08mve8.apps.googleusercontent.com';
	private $clientSecret = 'VARYKiH4PhCgxVlSFQrdLZ0m';
	private $redirectUrl = 'http://organise-inmyway.rhcloud.com';
	public function getClientId(){
		return $this->clientId;
	}
	public function getClientSecret(){
		return $this->clientSecret;
	}
	public function getRedirectUrl(){
		return $this->redirectUrl;
	}

}
?>