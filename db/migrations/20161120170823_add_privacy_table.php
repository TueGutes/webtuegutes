<?php

use Phinx\Migration\AbstractMigration;

class AddPrivacyTable extends AbstractMigration
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
        $user = $this->table('Privacy', array('id'=> false,'primary_key' => array('idPrivacy')));
        $user->addColumn('idPrivacy','integer')
             ->addColumn('privacykey','string',array('limit' => 64))
             ->addColumn('cryptkey','string',array('limit' => 128))
             ->addForeignKey('idPrivacy','User','idUser',array('delete'=> 'NO_ACTION', 'update'=> 'NO_ACTION'))
             ->create();
    }
}
