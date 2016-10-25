<?php

/**
* Custom Encryption Classes!
*/
class crypt
{
	
	function __construct()
	{
		# code...
		(!isset($_SESSION)) ? session_start() : $useless = true;
	}

	public static function encrypt_string($string, $algorithm, $salt, $iv) {
		(!isset($algorithm)) ? $algorithm = 'aes-256-cbc' : $useless = true;
		return openssl_encrypt ($string, $algorithm, $salt, true, $iv);
	}

	public static function decrypt_string($encrypted_string, $algorithm, $salt, $iv) {
		return openssl_decrypt ($encrypted_string, $algorithm, $salt, true, $iv);
	}
}

/**
* Custom Hashing Class!
*/
class hash
{
	
	function __construct()
	{
		# code...
		(!isset($_SESSION)) ? session_start() : $useless = true;
	}

	public static function hash($string, $algorithm) {
		// sha512
		return hash($algorithm, $string);
	}
}

?>