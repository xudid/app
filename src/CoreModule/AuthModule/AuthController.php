<?php

namespace App\CoreModule\AuthModule;


use App\App;
use App\CoreModule\UserModule\Model\User;
use App\CoreModule\UserModule\Views\UserAuthFormFilter;
use Ui\Views\FormFactory;


class AuthController
{
    /**
     * @var $router
     */

    private $router;
    /**
     * @var $renderer
     */
    private $renderer;

    private $container = null;

    private $session_exists = false;

    private function debugSession()
    {
        var_dump($this->session_exists);
        var_dump(session_status());
    }

    private function activeSession()
    {
        session_start();
        $this->session_exists = true;
    }


    /**
     * AuthController constructor.
     * @param App $app
     */
    function __construct(App $app)
    {
        $this->session_exists = (session_status() === PHP_SESSION_ACTIVE) ? true : false;
        $this->app = $app;
    }

    /**
     * [login description]
     * @return string [description]
     */
    public function login(): string
    {
        $view = null;
        $uaff = new UserAuthFormFilter();
        try {
            $ff = new FormFactory(User::class, $uaff, null, 'auth', "POST");
            $view = $ff->getForm();

        } catch (\ReflectionException $e) {
            var_dump($e->getMessage());
        }


        return $view;
    }

    /**
     * [logout description]
     * @return string [description]
     */
    public function logout(): string
    {
        if (!$this->session_exists) {
            $this->activeSession();
        }
        \session_destroy();
        unset($_SESSION);
        return "logged out";
    }

    /**
     * @param App $app
     */
    public function auth(App $app)
    {
        $name = $_POST['name'];
        $password = $_POST['password'];
        $entity = $this->app->getEntity(User::class);
        $result = $entity->FindBy(["name" => $name]);
        $user = $result[0];

        $trusted = false;
        if ($user) {
            $trusted = $user->verifyPassword($password);
        }
        if ($trusted) {
            if (!$this->session_exists) {
                $this->activeSession();
                \session_regenerate_id();
            }
            $_SESSION['user'] = $user;
            $url = $this->restoreAskedUrl();
            if ($url) {
                $this->resetAskedUrl();
                $app->redirectTo($url);
                $app->redirectTo('/test');
            }
        } else {
            $app->redirectTo('/login');
        }
    }

    /**
     * [isloggedin description]
     * @return bool [description]
     */
    public function isloggedin()
    {
        if (!$this->session_exists) {
            $this->activeSession();
        }
        //session_start();
        if (\array_key_exists("user", $_SESSION)) {

            $user = $_SESSION['user'];
            return $user;
        } else {
            return false;
        }
    }

    public function userHasRole($roles): bool
    {
        if (!$this->session_exists) {
            $this->activeSession();
        }
        if (\array_key_exists("user", $_SESSION)) {
            $user = $_SESSION['user'];

            foreach ($roles as $key => $value) {
                $role = $value;
                $usersroles = $user->getRole();
                foreach ($usersroles as $key => $userrole) {
                    if ($role == $userrole) {
                        return true;
                    }
                }
            }
            return false;
        }
    }

    /**
     * [saveAskedUrl description]
     * @param string $url [description]
     */
    public function saveAskedUrl(string $url)
    {
        if ($this->session_exists) {
            $this->activeSession();
        }
        $_SESSION['STORED_URL'] = $url;
    }

    /**
     * [restoreAskedUrl description]
     * @return string|null [description]
     */
    public function restoreAskedUrl(): string
    {
        if (!$this->session_exists) {
            $this->activeSession();
        }
        if (\array_key_exists("STORED_URL", $_SESSION)) {
            return $_SESSION['STORED_URL'];
        }
        return false;
    }

    public function resetAskedUrl()
    {
        if (!$this->session_exists) {
            $this->activeSession();
        }
        $_SESSION['STORED_URL'] = "";
    }
}

?>
