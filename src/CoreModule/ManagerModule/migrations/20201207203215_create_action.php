<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAction extends AbstractMigration
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
		$table = $this->table('actions');
		$table->addColumn('name', 'string')
			->addColumn('type', 'string')
			->addColumn('route_name', 'string')
			->addColumn('modules_id', 'integer')
			->addForeignKey('modules_id', 'modules', 'id')
			->create();
	}
}
