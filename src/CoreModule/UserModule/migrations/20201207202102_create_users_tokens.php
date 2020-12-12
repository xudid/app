<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTokens extends AbstractMigration
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
		$users_tokens = $this->table('users_tokens');

		$users_tokens->addColumn('users_id')
			->addColumn('token')
			->addColumn('expiration')
			->addColumn('used')
			->addForeignKey('users_id', 'users', 'id')
			->create();
    }
}
