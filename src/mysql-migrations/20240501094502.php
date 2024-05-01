<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Table\Column;

final class V20240501094502 extends AbstractMigration
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
        $table = $this->table('sample_content')

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
            ->addColumn('content', Column::STRING, ['limit' => 2000])

            // Legalabb erre az oszlopra mindig erdemes indexet rakni
            ->addIndex(['record_status']);

        $table->create();

        // Uj default sorok beszurasa ha kell
        $table->insert([
            ['name' => 'Pelda Tartalom 1', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla accumsan sodales consequat. Pellentesque fermentum vehicula purus a egestas.'],
            ['name' => 'Pelda Tartalom 2', 'content' => 'Integer risus odio, sagittis non fringilla non, ornare a sapien. Curabitur porttitor felis et enim volutpat vehicula.'],
            ['name' => 'Pelda Tartalom 3', 'content' => 'Vivamus imperdiet egestas magna non luctus. Nam scelerisque, neque sit amet ultrices sagittis, metus libero aliquet ex, molestie egestas augue justo accumsan lorem'],
        ])->save();
    }
}
