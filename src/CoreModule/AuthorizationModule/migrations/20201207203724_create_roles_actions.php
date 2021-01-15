<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRolesActions extends AbstractMigration
{
	/**
	 * Change Method.
	 *
	 * Write your reversible migrations using this method.
	 *
	 * More information on writing migrations is available here:
	 * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
	 *
	 * Remember to call "create()" or "update()" and NOT "save()" when working
	 * with the Table class.
	 */
	public function change(): void
	{
		$roles_actions = $this->table('roles_actions');

		$roles_actions
			->addColumn('roles_id', 'integer')
			->addColumn('actions_id', 'integer')
			->addColumn('authorized', 'smallint')
			->addForeignKey('roles_id', 'roles', 'id')
			->addForeignKey('actions_id', 'actions', 'id')
			->create();
	}
}
