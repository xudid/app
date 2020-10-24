<?php


namespace App\CoreModule\AuthModule\views;


use App\CoreModule\UserModule\Model\User;
use Ui\Views\EntityView;
use Ui\Views\FormFactory;
use Ui\Views\ViewFilter;

class ResetPasswordForm extends EntityView
{
    public function __construct(FormFactory $factory)
    {
        parent::__construct();
        $formFilter = new ViewFilter();
        $formFilter->setWritables(['email']);
        $factory->setAccessFilter($formFilter);
        $factory->withAction('/reset/password');
        $this->add($factory->getForm());
    }
}