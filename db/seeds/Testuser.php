<?php

use Phinx\Seed\AbstractSeed;

class Testuser extends AbstractSeed
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
        $datauser=array(
            array(

                'idUser' => 5,

                'username' => 'testuser',
                'password' => '9d53fbca481ed20edc0c10d6e45fcedf',
                'email' => 'testmailgutetaten@gmail.com',
                'regDate' => '2016-11-01',
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 3,
                'idTrust' => 3
                )
            );
        $dataprivacy=array(
            array(

                'idPrivacy' => 5,
                'privacykey' => '111111111111111',
                'cryptkey' => '345485c1dfc5ebc4dd3fb90e3d591518'
                )
            );
        $datausertexts=array(
            array(

                'idUserTexts' =>5,
                'avatar' => './img/profiles/standard_other.png'
                )
            );
        $datapersdata=array(
            array(

                'idPersData' => 5,
                'firstname' => 'testmax',
                'lastname' => 'testmuster',
                'idPostal' => -1
                )
            );
        $user=$this->table('User');
        $user->insert($datauser)
             ->save();
        $privacy=$this->table('Privacy');
        $privacy->insert($dataprivacy)
             ->save();
        $usertexts=$this->table('UserTexts');
        $usertexts->insert($datausertexts)
             ->save();
        $persdata=$this->table('PersData');
        $persdata->insert($datapersdata)
             ->save();
    }
}
