<?php

use Phinx\Migration\AbstractMigration;

class AddTrustTable extends AbstractMigration
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
        $trust = $this->table('Trust', array('id'=> false,'primary_key' => array('idTrust')));
        $trust->addColumn('idTrust','integer')
             ->addColumn('trustleveldescription','string',array('limit' => 45))
             ->create();
    }
    **/
    public function up(){
        $trust = $this->table('Trust', array('id'=> false,'primary_key' => array('idTrust')));
        $trust->addColumn('idTrust','integer')
             ->addColumn('trustleveldescription','string',array('limit' => 45))
             ->create();

        $rows = [
            [
                'idTrust' => 1,
                'trustleveldescription' => 'Neuling'
            ],
            [
                'idTrust' => 2,
                'trustleveldescription' => 'Mitglied'
            ],
            [
                'idTrust' => 3,
                'trustleveldescription' => 'Stammmitglied'
            ],
            [
                'idTrust' => 4,
                'trustleveldescription' => 'Veteran'
            ],
            [
                'idTrust' => 5,
                'trustleveldescription' => 'GuteFreund'
            ],
            [
                'idTrust' => 6,
                'trustleveldescription' => 'Familienmitglied'
            ],
            [
                'idTrust' => 7,
                'trustleveldescription' => 'Seelenverwandte'
            ]
        ];

        // this is a handy shortcut
        $this->insert('Trust', $rows);
    }

    public function down(){


        $this->execute('DELETE FROM Trust WHERE idTrust < 8');

        $this->dropTable('Trust');
    }
}
