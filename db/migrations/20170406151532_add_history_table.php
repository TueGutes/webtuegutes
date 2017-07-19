<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddHistoryTable extends AbstractMigration
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
        $table=$this->table('History');
        $table->addColumn('actor','integer')
              ->addColumn('action','string',array('limit' => 128))
              ->addColumn('info','text',array('limit' => MysqlAdapter::TEXT_REGULAR))
              ->addColumn('involvedDeed','integer',array('null' => true))
              ->addColumn('involvedUser','integer',array('null' => true))
              ->addForeignKey('actor','User','idUser',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->addForeignKey('involvedUser','User','idUser',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->addForeignKey('involvedDeed','Deeds','idGuteTat',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->create();
    }
}
