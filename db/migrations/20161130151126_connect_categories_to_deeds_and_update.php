<?php

use Phinx\Migration\AbstractMigration;

class ConnectCategoriesToDeedsAndUpdate extends AbstractMigration
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
        $deeds = $this->table('Deeds');
        $deeds->renameColumn('category','notinuse')
              ->addColumn('category','integer',array('after' => 'notinuse'))
              ->addForeignKey('category','Categories','id',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->update();
    }
}
