<?php


namespace App\Security;


use App\Controller;
use App\CoreModule\UserModule\Model\User;
use Core\Security\Token;
use DateTime;
use Entity\Database\QueryBuilder\QueryBuilderInterface;

class TokenProvider extends Controller
{
    private QueryBuilderInterface $queryBuilder;

    public function __construct()
    {
        parent::__construct();
        $this->queryBuilder = $this->modelManager(User::class)->builder();
    }

    public static int $length = 16;
    public static string $expiration = '+20 minute';

    public function getUserToken($user)
    {
        $token = new Token(self::$length);
        $expiration = (new DateTime('NOW'))->modify(self::$expiration)->format('Y-m-d H:i:s');
        $request = $this->queryBuilder->insert('users_token')
            ->columns('users_id', 'token', 'expiration', 'used')
            ->values(['users_id' => $user->getId(), 'token' => $token->__toString(), 'expiration' => $expiration, 'used' =>0]);
        $this->queryBuilder->execute($request);
        return $token;
    }
// modify to use 2 table one token and another users_token
    public function isValid(string $token)
    {
        if (strlen($token) !== self::$length) {
            return false;
        }
        $request = $this->queryBuilder->select()
            ->from('users_token')
            ->where('token', '=', $token)
            ->where('used', '<>', 1)
            ->where('expiration', '>', (new DateTime('NOW'))->format('Y-m-d H:i:s'));

        return $this->queryBuilder->execute($request);
    }

    public function belongUser(User $user, string $token)
    {

    }

    public function getCsrfToken()
    {
        $token = new Token(self::$length);
        // todo store token
        return $token;
    }
}