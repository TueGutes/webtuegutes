<?php

use Phinx\Seed\AbstractSeed;

class FillCategoriesv1 extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data=array(
            array('categoryname' => 'keine Angabe'),
            array('categoryname' => 'Öffentliches'),
            array('categoryname' => 'Mensch zu Mensch'),
            array('categoryname' => 'Bildung'),
            array('categoryname' => 'Längerfristig'),
            array('categoryname' => 'Social Event'),
            array('categoryname' => 'Haushalt')
            );
        $cate = $this->table('Categories');
        $cate->insert($data)
             ->save();

    }
}
