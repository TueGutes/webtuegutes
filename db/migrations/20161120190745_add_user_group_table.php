<?php

use Phinx\Migration\AbstractMigration;

class AddUserGroupTable extends AbstractMigration
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
    /**
    public function change()
    {
        $usergroup = $this->table('UserGroup', array('id'=> false,'primary_key' => array('idUserGroup')));
        $usergroup->addColumn('idUserGroup','integer')
             ->addColumn('groupDescription','string',array('limit' => 45))
             ->create();
    }
    **/
    public function up(){
        $usergroup = $this->table('UserGroup', array('id'=> false,'primary_key' => array('idUserGroup')));
        $usergroup->addColumn('idUserGroup','integer')
             ->addColumn('groupDescription','string',array('limit' => 64))
             ->create();

        $rows = [
            [
                'idUserGroup' => 1,
                'groupDescription' => 'Mitglied'
            ],
            [
                'idUserGroup' => 2,
                'groupDescription' => 'Moderator'
            ],
            [
                'idUserGroup' => 3,
                'groupDescription' => 'Administrator'
            ]
        ];

        // this is a handy shortcut
        $this->insert('UserGroup', $rows);
    }

    public function down(){


        $this->execute('DELETE FROM UserGroup WHERE idUserGroup = 1');
        $this->execute('DELETE FROM UserGroup WHERE idUserGroup = 2');
        $this->execute('DELETE FROM UserGroup WHERE idUserGroup = 3');

        $this->dropTable('UserGroup');
    }
}
