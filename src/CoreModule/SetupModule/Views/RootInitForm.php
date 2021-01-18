<?php


namespace App\CoreModule\SetupModule\Views;


use Ui\HTML\Elements\Nested\Form;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Input\EmailInput;
use Ui\Widgets\Input\HiddenInput;
use Ui\Widgets\Input\PasswordInput;
use Ui\Widgets\Input\TextInput;
use App\CoreModule\RoleModule\Model\Role;
use Ui\Widgets\Views\Row;

class RootInitForm extends Form
{
	public function __construct(Role $role)
	{
		parent::__construct();

		$this->setMethod('POST');
		$this->setAction('/init/firstuser');

		$this->feed(
			(new HiddenInput('role_id'))->setValue($role->getId()),
			(new EmailInput('user_email'))
				->setName('user_email')
				->SetPlaceholder('email')
				->setClass('form-input'),
			(new TextInput())->setName('user_name')
				->SetPlaceholder('users name')
				->setClass('form-input'),

			(new PasswordInput())->setName('user_password')
				->SetPlaceholder('passowrd')
				->setClass('form-input'),

			(new PasswordInput())->setName('user_password_confirmation')
				->SetPlaceholder('passowrd confirmation')
				->setClass('form-input'),

			(new Row())->feed(
				(new SubmitButton('Validate'))
			)->setClass('justify-space-around')
		);
		$this->setMethod('POST')->setAction('/init/firstuser');

	}

}