<?php


namespace App\CoreModule\AuthModule\views;


use App\Controller;
use App\CoreModule\UserModule\Model\User;
use App\CoreModule\UserModule\Views\UserAuthFormFilter;

class LoginForm
{
    public function __construct(Controller $controller)
    {
        $factory = $controller->formFactory(User::class);
        $formFilter = new UserAuthFormFilter();
        $factory->setFormTitle('Login');
        $factory->withAction('/auth');
        $factory->setAccessFilter($formFilter);
    }
}