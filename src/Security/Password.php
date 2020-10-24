<?php


namespace App\Security;


class Password
{
    private static $algo = PASSWORD_DEFAULT;
    private static int $cost = 12;

    // todo think to double hash strategy to avoid timing attack
    public static function hash(string $clear)
    {
        $hash = password_hash($clear, self::$algo, ['cost' => self::$cost]);
        if (is_string($hash)) {
            return $hash;
        }
        return false;
    }

    public static function verify(string $hash, string $password)
    {
        password_verify($password, $hash) ?: false;
    }

    public static function algo($param)
    {
        self::$algo = $param;
    }

    public static function cost(int $param)
    {
        self::$cost = $param;
    }

    public static function bench(float $timeTarget, int $baseCost)
    {
        do {
            $baseCost++;
            $start = microtime(true);
            password_hash("test", self::$algo, ["cost" => $baseCost]);
            $end = microtime(true);
        } while (($end - $start) < $timeTarget);
        return $baseCost;
    }
}