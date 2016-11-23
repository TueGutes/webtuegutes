<?php

use Phinx\Seed\AbstractSeed;

class PostalcodeTest extends AbstractSeed
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
        $data = array(
            array(
                'idPostal' => -1,
                'postalcode' => 0,
                'place' => '404'
                )

            );
        $postalcode = $this->table('Postalcode');
        $postalcode->insert($data)
                   ->save();
    }
}
