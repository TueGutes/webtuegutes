<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class DeedComments extends AbstractMigration
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
        $comm = $this->table('DeedComments');
        $comm->addColumn('deeds_id','integer')
             ->addColumn('user_id_creator','integer')
             ->addColumn('date_created','datetime')
             ->addColumn('commenttext','text',array('limit' => MysqlAdapter::TEXT_REGULAR,'null' => true))
             ->addColumn('parentcomment','integer',array('null' => true))
             ->addForeignKey('deeds_id','Deeds','idGuteTat',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
             ->addForeignKey('user_id_creator','User','idUser',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
             ->addForeignKey('parentcomment','DeedComments','id',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
             ->create();
    }
}
