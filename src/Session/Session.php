<?php
namespace App\Session;


/**
 * 
 */
class Session
{
	
	public function __construct()
	{
		session_start();
	}

	public function hasKey($key)
	{
		return (isset($_SESSION[$key]));

		
	}

	public function get($key)
	{
		if ($this->hasKey($key)) {
			return $_SESSION[$key];
		}
	}

	public function set($key,$value)
	{
		if (isset($key)) {
			$_SESSION[$key] = $value;
		} else {
			throw new Exception("Error Try to set Session value with undefined Key", 1);
			
		}
	}
}