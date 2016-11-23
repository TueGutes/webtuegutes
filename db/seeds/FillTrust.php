<?php

use Phinx\Seed\AbstractSeed;

class FillTrust extends AbstractSeed
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
            array(
                'idTrust' => 1,
                'trustleveldescription' => 'Neuling'
                ),
            array(
                'idTrust' => 2,
                'trustleveldescription' => 'Mitglied'
                ),
            array(
                'idTrust' => 3,
                'trustleveldescription' => 'Stammmitglied'
                ),
            array(
                'idTrust' => 4,
                'trustleveldescription' => 'Veteran'
                ),
            array(
                'idTrust' => 5,
                'trustleveldescription' => 'GuteFreund'
                ),
            array(
                'idTrust' => 6,
                'trustleveldescription' => 'Familienmitglied'
                ),
            array(
                'idTrust' => 7,
                'trustleveldescription' => 'Seelenverwandte'
                ),
            );
        $trust=$this->table('Trust');
        $trust->insert($data)
              ->save();
    }
}
