<?php

namespace App\Session;

use Exception;

/**
 * Class Session
 * @package App\Session
 */
class Session
{
	public static function start()
    {
        session_start();
    }

    public static function destroy()
    {
        session_destroy();
        unset($_SESSION);
    }

	public static function has(string $key)
	{
        if (!self::exists())
        {
            self::start();
        }
		return (isset($_SESSION[$key]));
	}

	public static function get(string $key)
	{
        if (!self::exists())
        {
            self::start();
        }
		if (self::exists() && self::has($key)) {
			return $_SESSION[$key];
		}
	}

	public static function set(string $key,$value)
	{
        if (!self::exists())
        {
            self::start();
        }
		if (isset($key)) {
			$_SESSION[$key] = $value;
		} else {
			throw new Exception("Error Try to set Session value with undefined Key", 1);
		}
	}

	public static function delete(string $key)
    {
        if (self::has($key)) {
            unset($_SESSION[$key]);
        }
    }

	public static  function debug()
    {
        echo '<pre>';
        if (self::exists())
        {
           dump('PHP_SESSION_ACTIVE');
        } else {
            $status = session_status();
            $statusString = $status === 0 ? 'PHP_SESSION_DISABLED' : 'PHP_SESSION_NONE';
            dump($statusString);
        }
    }

    public static function exists()
    {
        return (session_status() === PHP_SESSION_ACTIVE) ? true : false;
    }
//Todo add cookies methods

}