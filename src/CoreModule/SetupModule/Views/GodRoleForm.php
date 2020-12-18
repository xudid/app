<?php


namespace App\CoreModule\SetupModule\Views;


use Ui\HTML\Elements\Nested\Form;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Input\TextInput;
use Ui\Widgets\Views\Row;

class GodRoleForm extends Form
{
	public function __construct()
	{
		parent::__construct();
		$this->feed(
			(new TextInput())->setName('role_name')
				->SetPlaceholder('role name')
				->setClass('form-input'),
			(new TextInput())->setName('role_description')
				->SetPlaceholder('role description')
				->setClass('form-input'),
			(new Row())->feed(
				(new SubmitButton('Validate'))
			)->setClass('justify-space-around')

		);
		$this->setMethod('POST')->setAction('/init/firstrole');
	}
}