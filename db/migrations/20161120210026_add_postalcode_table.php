<?php

use Phinx\Migration\AbstractMigration;

class AddPostalcodeTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */

    /**
    public function change()
    {
        $postalcode = $this->table('Postalcode', array('id'=> 'idPostal'));
        $postalcode->addColumn('postalcode','integer')
                   ->addColumn('place','string',array('limit' => 128))
                   ->create();
    }
    **/
    public function up(){
        $postalcode = $this->table('Postalcode', array('id'=> 'idPostal'));
        $postalcode->addColumn('postalcode','integer')
                   ->addColumn('place','string',array('limit' => 128))
                   ->create();

         $rows = [
            [
                'idPostal' => -1,
                'postalcode' => 0,
                'place' => '404'
            ]
        ];

        // this is a handy shortcut
        $this->insert('Postalcode', $rows);
    }

    public function down(){
        $this->execute('DELETE FROM Postalcode WHERE idPostal = -1');

        $this->dropTable('Postalcode');
    }
}
