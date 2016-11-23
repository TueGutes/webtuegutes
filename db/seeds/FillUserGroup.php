<?php

use Phinx\Seed\AbstractSeed;

class FillUserGroup extends AbstractSeed
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
                'idUserGroup' => 1,
                'groupDescription' => 'Mitglied'
                ),
            array(
                'idUserGroup' => 2,
                'groupDescription' => 'Moderator'
                ),
            array(
                'idUserGroup' => 3,
                'groupDescription' => 'Administrator'
                )
            );
        $usergroup=$this->table('UserGroup');
        $usergroup->insert($data)
                  ->save();
    }
}
