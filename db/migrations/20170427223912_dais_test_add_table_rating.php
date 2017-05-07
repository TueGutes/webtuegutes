<?php

use Phinx\Migration\AbstractMigration;

class DaisTestAddTableRating extends AbstractMigration
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
    public function change()
    {
        $table = $this->table('Rating', array('id' => false, 'primary_key' => array('username', 'deedsName')));
        $table->addColumn('username','string',array('limit' => 32))
            ->addColumn('deedsName','string',array('length' => 64))
            ->addColumn('time','datetime')
            ->addColumn('rating','integer')
            ->addForeignKey('username','User','username',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
            ->addForeignKey('deedsName','Deeds','name',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
            ->create();
    }
}