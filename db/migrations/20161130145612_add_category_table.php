<?php

use Phinx\Migration\AbstractMigration;

class AddCategoryTable extends AbstractMigration
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
    	$category = $this->table('Categories');
    	$category->addColumn('categoryname','string',array('limit' => 128))
    			 ->create();
    }
    **/
    public function up(){
        $category = $this->table('Categories');
        $category->addColumn('categoryname','string',array('limit' => 128))
                 ->create();

        $rows = [
            ['categoryname' => 'keine Angabe'],
            ['categoryname' => 'Öffentliches'],
            ['categoryname' => 'Mensch zu Mensch'],
            ['categoryname' => 'Bildung'],
            ['categoryname' => 'Längerfristig'],
            ['categoryname' => 'Social Event'],
            ['categoryname' => 'Haushalt']
        ];

        // this is a handy shortcut
        $this->insert('Categories', $rows);
    }

    public function down(){
        $this->execute('DELETE FROM Categories WHERE categoryname = "keine Angabe"');
        $this->execute('DELETE FROM Categories WHERE categoryname = "Öffentliches"');
        $this->execute('DELETE FROM Categories WHERE categoryname = "Mensch zu Mensch"');
        $this->execute('DELETE FROM Categories WHERE categoryname = "Bildung"');
        $this->execute('DELETE FROM Categories WHERE categoryname = "Längerfristig"');
        $this->execute('DELETE FROM Categories WHERE categoryname = "Social Event"');
        $this->execute('DELETE FROM Categories WHERE categoryname = "Haushalt"');

        $this->dropTable('Categories');
    }
}

