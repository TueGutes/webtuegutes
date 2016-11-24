<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddApplication extends AbstractMigration
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
        $application = $this->table('Application', array('id' => false, 'primary_key' => array('idUser', 'idGuteTat')));
        $application->addColumn('idUser', 'integer')
              ->addColumn('idGuteTat', 'integer')
              ->addColumn('applicationText', 'text',array('limit' => MysqlAdapter::TEXT_REGULAR,'null' => true))
              ->addColumn('replyMsg','text',array('limit' => MysqlAdapter::TEXT_REGULAR,'null' => true))
              ->addColumn('status','enum',array('values' => array('offen','angenommen','abgelehnt')))
              ->addIndex(array('idUser'))
              ->addIndex(array('idGuteTat'))
              ->addForeignKey('idUser','User','idUser',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->addForeignKey('idGuteTat','Deeds','idGuteTat',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->create();
    }
}
