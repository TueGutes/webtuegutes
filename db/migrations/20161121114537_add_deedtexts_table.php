<?php

use Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class AddDeedtextsTable extends AbstractMigration
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
        $deedtexts = $this->table('DeedTexts', array('id'=> false,'primary_key' => array('idDeedTexts')));
        $deedtexts->addColumn('idDeedTexts','integer')
                  ->addColumn('description','text',array('limit' => MysqlAdapter::TEXT_REGULAR,'null' => true))
                  ->addColumn('pictures','text',array('limit' => MysqlAdapter::TEXT_REGULAR,'null' => true))
                  ->addForeignKey('idDeedTexts','Deeds','idGuteTat',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
                  ->create();
    }
}