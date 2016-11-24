<?php

use Phinx\Migration\AbstractMigration;

class AddUserTable extends AbstractMigration
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
        $user = $this->table('User', array('id'=> 'idUser'));
        $user->addColumn('username','string',array('limit' => 32))
             ->addColumn('password','string',array('limit' => 32))
             ->addColumn('email','string',array('limit' => 128))
             ->addColumn('regDate','datetime')
             ->addColumn('points','integer')
             ->addColumn('idTrust','integer')
             ->addColumn('idUserGroup','integer')
             ->addColumn('status','enum',array('values' => array('nichtVerifiziert','Verifiziert')))
             ->create();
    }
}
