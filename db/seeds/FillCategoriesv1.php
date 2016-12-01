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
            array('categorytext' => 'keine Angabe'),
            array('categorytext' => 'Öffentliches'),
            array('categorytext' => 'Mensch zu Mensch'),
            array('categorytext' => 'Bildung'),
            array('categorytext' => 'Längerfristig'),
            array('categorytext' => 'Social Event'),
            array('categorytext' => 'Haushalt')
            );
        $cate = $this->table('Categories');
        $cate->insert($data)
             ->save();

    }
}
