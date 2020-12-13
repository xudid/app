<?php


namespace App\CoreModule\SetupModule\Views;


use Ui\HTML\Elements\Nested\Form;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Input\TextInput;

class RootInitForm extends Form
{
	public function __construct()
	{
		parent::__construct();

		$this->setMethod('POST');
		$this->setAction('/init/root');

		$this->feed(
			(new TextInput())->setName('users_name')
				->SetPlaceholder('users name'),

			(new TextInput())->setName('users_password')
				->SetPlaceholder('passowrd'),

			(new TextInput())->setName('user_password_confirmation')
				->SetPlaceholder('passowrd confirmation'),

			(new SubmitButton('Validate'))
		);

	}

}