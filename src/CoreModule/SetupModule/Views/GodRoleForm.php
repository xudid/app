<?php


namespace App\CoreModule\SetupModule\Views;


use Ui\HTML\Elements\Nested\Form;
use Ui\Widgets\Button\SubmitButton;
use Ui\Widgets\Input\TextInput;

class GodRoleForm extends Form
{
	public function __construct()
	{
		parent::__construct();
		$this->feed(
			(new TextInput())->setName('roles_name')
				->SetPlaceholder('role name'),
			(new TextInput())->setName('roles_description')
				->SetPlaceholder('role description'),
			(new SubmitButton('Validate'))
		);
		$this->setMethod('POST')->setAction('/init/firstrole');
	}
}