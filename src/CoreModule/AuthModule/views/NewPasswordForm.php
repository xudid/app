<?php


namespace App\CoreModule\AuthModule\views;


use Ui\HTML\Elements\Bases\H3;
use Ui\HTML\Elements\Nested\Form;
use Ui\Views\EntityView;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Input\HiddenInput;
use Ui\Widgets\Input\PasswordInput;
use Ui\Widgets\Input\TextInput;

class NewPasswordForm extends EntityView
{
    public function __construct($token)
    {
        parent::__construct();
        $this->setTitle(new H3('Réinitialisez votre mot de passe'));
        $form = (new Form())->setClass('text-center')->setAction('/password/recovery')->setMethod('POST');
        $form->add((new TextInput())
            ->setName('user_email')
            ->SetPlaceholder('email')
            ->setClass('form-control mb-2')
        );
        $form->add((new PasswordInput('user_password'))
            ->setClass('form-control mb-2')->SetPlaceholder('mot de passe'));
        $form->add((new PasswordInput('user_password_confirmation'))
            ->setClass('form-control mb-2')->SetPlaceholder('mot de passe'));
        $form->add((new HiddenInput('token'))->setValue($token));
        $form->add((new SubmitButton('Enregister'))->setClass('btn btn-danger '));
        $this->add($form);
    }
}