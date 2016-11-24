<?php

use Phinx\Migration\AbstractMigration;

class AddPersDataTable extends AbstractMigration
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
        $persdata = $this->table('PersData', array('id'=> false,'primary_key' => array('idPersData')));
        $persdata->addColumn('idPersData','integer')
                 ->addColumn('firstname','string',array('limit' => 64))
                 ->addColumn('lastname','string',array('limit' => 64))
                 ->addColumn('gender','string',array('limit' => 1,'null' => true))
                 ->addColumn('street','string',array('limit' => 128,'null' => true))
                 ->addColumn('housenumber','string',array('limit' => 5,'null' => true))
                 ->addColumn('idPostal','integer',array('null' => true))
                 ->addColumn('telefonnumber','string',array('limit' => 20,'null' => true))
                 ->addColumn('messengernumber','string',array('limit' => 20,'null' => true))
                 ->addColumn('birthday','date',array('null' => true))
                 ->addIndex(array('idPostal'))
                 ->addForeignKey('idPersData','User','idUser',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
                 ->create();
    }
}
