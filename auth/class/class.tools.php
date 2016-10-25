<?php

/**
* 
*/
class random
{
	
	function __construct()
	{
		(!isset($_SESSION)) ? session_start() : $useless = true;
	}

	public static function string($length) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#%^&*()[]{}\\|/.,<>~`';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
}

?>