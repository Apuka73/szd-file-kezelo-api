<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Table\Column;

final class V20240503041729 extends AbstractMigration
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
        // Az autoincrement id-t magatol hozzaadja.
        $table = $this->table('file')

            // AbstractModel-nek kotelezo oszlopok minden tablaban
            ->addColumn('record_status', Column::TINYINTEGER, [
                'limit' => 1,
                'default' => 1
            ])
            ->addColumn('created_at', Column::DATETIME, [
                'default' => 'CURRENT_TIMESTAMP'
            ])

            // Uj model oszlopai
            ->addColumn('name', Column::STRING, ['limit' => 200])
            ->addColumn('path', Column::STRING, ['limit' => 250])

            // Legalabb erre az oszlopra mindig erdemes indexet rakni
            ->addIndex(['record_status'])
            ->addIndex(['path'], ['unique' => true]);

        $table->create();
    }
}
