<?php

use Phinx\Migration\AbstractMigration;

class AddDeedsTable extends AbstractMigration
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
        $deeds=$this->table('Deeds', array('id'=> 'idGuteTat'));
        $deeds->addColumn('name','string',array('limit' => 64,'null' => true))
              ->addColumn('contactPerson','integer')
              ->addColumn('category','string',array('limit' => 64))
              ->addColumn('street','string',array('limit' => 128,'null' => true))
              ->addColumn('housenumber','string',array('limit' => 5,'null' => true))
              ->addColumn('idPostal','integer')
              ->addColumn('starttime','datetime',array('null' => true))
              ->addColumn('endtime','datetime',array('null' => true))
              ->addColumn('organization','string',array('limit' => 128,'null' => true))
              ->addColumn('countHelper','integer',array('null' => true))
              ->addColumn('idTrust','integer')
              ->addColumn('status','enum',array('values' => array('nichtFreigegeben','freigegeben','geschlossen','abgelehnt')))
              ->addForeignKey('contactPerson','User','idUser',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->addForeignKey('idTrust','Trust','idTrust',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->addForeignKey('idPostal','Postalcode','idPostal',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
              ->addIndex(array('idPostal'))
              ->addIndex(array('idTrust'))
              ->addIndex(array('contactPerson'))
              ->create();


    }
}
