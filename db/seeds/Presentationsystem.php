<?php

use Phinx\Seed\AbstractSeed;

class Presentationsystem extends AbstractSeed
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
        //Herbert Kleinschmidt
        //----------------------------------------------------------------------------
        $id = 100;
        $pw = 'muster3928';
        $date ='2016-12-10';
        $username = 'schmidtchen';
        $email = 'h.kleinschmidt@dgeosis.de';
        $hobbys = 'Domino, Schach, Badminton';
        $firstname = 'Herbert';
        $lastname = 'Kleinschmidt';
        $gender = 'm';
        $birthday = '1989-08-19';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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


        //Manuel Freimann
        //---------------------------------------------------------------------------- 
        $id = 101;
        $pw = 'muster9283';
        $date ='2016-11-12';
        $username = 'manu93';
        $email = 'm.freimann@dgeosis.de';
        $hobbys = 'Fußball spielen';
        $firstname = 'Manuel';
        $lastname = 'Freimann';
        $gender = 'm';
        $birthday = '1993-04-12';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Sandra Miszcka
        //---------------------------------------------------------------------------- 
        $id = 102;
        $pw = 'muster8323';
        $date ='2016-12-11';
        $username = 'sonnenkind';
        $email = 's.miszcka@dgeosis.de';
        $hobbys = 'Floorball';
        $firstname = 'Sandra';
        $lastname = 'Miszcka';
        $gender = 'w';
        $birthday = '1996-07-06';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Erik Thorwald
        //---------------------------------------------------------------------------- 
        $id = 103;
        $pw = 'muster0182';
        $date ='2016-12-08';
        $username = 'erik_t';
        $email = 'e.thorwald@dgeosis.de';
        $hobbys = 'Computer spielen';
        $firstname = 'Erik';
        $lastname = 'Thorwald';
        $gender = 'm';
        $birthday = '1996-09-20';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Olaf Breitschneider
        //---------------------------------------------------------------------------- 
        $id = 104;
        $pw = 'muster4092';
        $date ='2016-12-10';
        $username = 'baron.olaf';
        $email = 'o.breitschneider@dgeosis.de';
        $hobbys = 'Kegeln (Vahrenwalder Stadtmeister!)';
        $firstname = 'Olaf';
        $lastname = 'Breitschneider';
        $gender = 'm';
        $birthday = '1986-06-16';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Lena Dietmers
        //---------------------------------------------------------------------------- 
        $id = 105;
        $pw = 'muster2647';
        $date ='2016-12-01';
        $username = 'sternchen';
        $email = 'l.dietmers@dgeosis.de';
        $hobbys = 'Voltigieren, Dressurreiten';
        $firstname = 'Lena';
        $lastname = 'Dietmers';
        $gender = 'w';
        $birthday = '1994-11-11';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Manuela Kreischer
        //---------------------------------------------------------------------------- 
        $id = 106;
        $pw = 'muster6288';
        $date ='2016-11-28';
        $username = 'manu_k';
        $email = 'm.kreischer@dgeosis.de';
        $hobbys = 'Golf, Tennis';
        $firstname = 'Manuela';
        $lastname = 'Kreischer';
        $gender = 'w';
        $birthday = '1991-06-12';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Ursula Stettmer
        //---------------------------------------------------------------------------- 
        $id = 107;
        $pw = 'muster0229';
        $date ='2016-12-19';
        $username = 'ursulastettmer';
        $email = 'u.stettmer@dgeosis.de';
        $hobbys = 'Lesen, Spazieren gehen';
        $firstname = 'Ursula';
        $lastname = 'Stettmer';
        $gender = 'w';
        $birthday = '1942-03-11';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Natalie Schenk
        //---------------------------------------------------------------------------- 
        $id = 108;
        $pw = 'muster9282';
        $date ='2016-12-06';
        $username = 'pechmarie';
        $email = 'n.schenk@dgeosis.de';
        $hobbys = 'Fußball';
        $firstname = 'Natalie';
        $lastname = 'Schenk';
        $gender = 'w';
        $birthday = '1991-06-12';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Robert Hecht
        //---------------------------------------------------------------------------- 
        $id = 109;
        $pw = 'muster2847';
        $date ='2016-12-07';
        $username = 'der_echte_hecht';
        $email = 'r.hecht@dgeosis.de';
        $hobbys = 'Computer, programmieren';
        $firstname = 'Robert';
        $lastname = 'Hecht';
        $gender = 'm';
        $birthday = '1982-04-19';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Mario von Galen
        //---------------------------------------------------------------------------- 
        $id = 110;
        $pw = 'muster7565';
        $date ='2016-12-01';
        $username = 'mario';
        $email = 'm.vongalen@dgeosis.de';
        $hobbys = 'Lesen, Gedichte schreiben';
        $firstname = 'Mario';
        $lastname = 'von Galen';
        $gender = 'm';
        $birthday = '1993-10-02';
        $description = 'Wie der Wind auf leisen Wegen, schlug ich mich, durch manche Schlachten. So alltäglich, unerträglich und bei den Engeln flossen Tränen und die Dämonen lachten.';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys,
                'description' => $description
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Hannover Stadt
        //---------------------------------------------------------------------------- 
        $id = 111;
        $pw = 'muster0000';
        $date ='2016-12-05';
        $username = 'Hannover_Stadt';
        $email = 'hannover_stadt@dgeosis.de';
        $hobbys = '';
        $firstname = 'Hannover';
        $lastname = 'Stadt';
        $gender = 'a';
        $birthday = '1990-01-01';
        $description = 'Account der Stadt Hannover';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys,
                'description' => $description
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //Pflegeheim Grossmueller
        //---------------------------------------------------------------------------- 
        $id = 112;
        $pw = 'muster0001';
        $date ='2016-12-01';
        $username = 'Pflegeheim_Grossmueller';
        $email = 'pflegeheim_grossmueller@dgeosis.de';
        $hobbys = '';
        $firstname = 'Pflegeheim';
        $lastname = 'Grossmueller';
        $gender = 'a';
        $birthday = '1990-01-01';
        $description = 'Account des Pflegeheims Grossmuellers';
        $pwhash = md5($pw.$date);
        $cryptkey = md5($username.$date); 
        $datauser=array(
            array(
                'idUser' => $id,
                'username' => $username,
                'password' => $pwhash,
                'email' => $email,
                'regDate' => $date,
                'points' => 0,
                'status' => 'Verifiziert',
                'idUserGroup' => 1,
                'idTrust' => 1
                )
            );
        $dataprivacy=array(
            array(
                'idPrivacy' => $id,
                'privacykey' => '011111011111111',
                'cryptkey' => $cryptkey
                )
            );
        $datausertexts=array(
            array(
                'idUserTexts' =>$id,
                'hobbys' => $hobbys,
                'description' => $description
                //'avatar' => './img/profiles/standard_male.png'
                )
            );
        $datapersdata=array(
            array(
                'idPersData' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'gender' => $gender,
                'idPostal' => -1,
                'birthday' => $birthday
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

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =100;
        $name = 'Freiwillige für den Hannover-Winterlauf gesucht: Auf- und Abbau';
        $contactPerson = 111;
        $category = 2;
        $time = '2016-12-30 11:30';
        $organization = 'Stadt Hannover';
        $countHelper = 20;
        $description ='Am 30.12. findet dieses Jahr wieder der Hannover-Winterlauf statt. Im Zuge der Organisation dieses Events brauchen wir tatkräftige Unterstützung bei Auf- und Abbau, Streckensicherung, Anmeldung der Spieler, Betreuung der Versorgungsstationen und im Preiskomitee. 
        Mit dieser guten Tat suchen wir nach Helfern, die den Auf- und Abbau des ganzen Events unterstützen. Neben Tischen, Bänken, etc. an Start- und Zielpunkt (und einer Bühne am Zielpunkt) umfasst dies auch Absperrbänder oder Barken an kritischen Abschnitten der Laufstrecke.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =101;
        $name = 'Freiwillige für den Hannover-Winterlauf gesucht: Streckensicherung';
        $contactPerson = 111;
        $category = 2;
        $time = '2016-12-30 11:30';
        $organization = 'Stadt Hannover';
        $countHelper = 50;
        $description ='Am 30.12. findet dieses Jahr wieder der Hannover-Winterlauf statt. Im Zuge der Organisation dieses Events brauchen wir tatkräftige Unterstützung bei Auf- und Abbau, Streckensicherung, Anmeldung der Spieler, Betreuung der Versorgungsstationen und im Preiskomitee.
 Mit dieser guten Tat suchen wir Freiwillige, die während des Laufs sicherstellen, dass die Strecke für die Teilnehmer frei bleibt. Ihr sollt also zum einen sicherstellen, dass die aufgehängten Absperrbänder noch an Ort und Stelle sind, und zum anderen dass sich keine Unbefugten auf der Laufstrecke befinden.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =102;
        $name = 'Freiwillige für den Hannover-Winterlauf gesucht: Anmeldung der Spieler';
        $contactPerson = 111;
        $category = 2;
        $time = '2016-12-30 11:30';
        $organization = 'Stadt Hannover';
        $countHelper = 15;
        $description =' Am 30.12. findet dieses Jahr wieder der Hannover-Winterlauf statt. Im Zuge der Organisation dieses Events brauchen wir tatkräftige Unterstützung bei Auf- und Abbau, Streckensicherung, Anmeldung der Spieler, Betreuung der Versorgungsstationen und im Preiskomitee.
Für die Anmeldestation suchen wir mit dieser guten Tat freiwillige Helfer, die die Spieler begrüßen, registrieren, die Startgebühr einsammeln und Nummern austeilen. Als Mitarbeiter an der Anmeldestation seid ihr häufig die ersten Ansprechpartner für die Teilnehmer. Ihr sollten also generell interesse an dem Winterlauf haben und euch nach Möglichkeit schon im Vorfeld mit der gesamten Planung auseinander setzen. Bei Bedarf stellen wir hierfür gerne auch Material zur Verfügung.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();


        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =103;
        $name = 'Freiwillige für den Hannover-Winterlauf gesucht: Betreuung der Versorgungsstationen';
        $contactPerson = 111;
        $category = 2;
        $time = '2016-12-30 11:30';
        $organization = 'Stadt Hannover';
        $countHelper = 20;
        $description =' Am 30.12. findet dieses Jahr wieder der Hannover-Winterlauf statt. Im Zuge der Organisation dieses Events brauchen wir tatkräftige Unterstützung bei Auf- und Abbau, Streckensicherung, Anmeldung der Spieler, Betreuung der Versorgungsstationen und im Preiskomitee.
Mit dieser guten Tat suchen wir Freiwillige, die an den zahlreichen Versorgungsstationen an der Strecke besetzen. Hierzu gehört nicht nur, die bereitgestellten Getränke zu bewachen, sondern auch, die vorbeikommenden Läufer zu motivieren und anzufeuern.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =104;
        $name = 'Freiwillige für den Hannover-Winterlauf gesucht: Preiskomitee';
        $contactPerson = 111;
        $category = 2;
        $time = '2016-12-30 11:30';
        $organization = 'Stadt Hannover';
        $countHelper = 20;
        $description =' Am 30.12. findet dieses Jahr wieder der Hannover-Winterlauf statt. Im Zuge der Organisation dieses Events brauchen wir tatkräftige Unterstützung bei Auf- und Abbau, Streckensicherung, Anmeldung der Spieler, Betreuung der Versorgungsstationen und im Preiskomitee.
Mit dieser guten Tat suchen wir kreative Köpfe, die Lust haben sich in die Planung des Winterlaufes einzubringen. Für die ersten Plätze soll es wieder Preise zu gewinnen geben. Dafür wird dem Komitee ein Budget zur Verfügung gestellt, welches es verplanen und einen Vorschlag ausarbeiten soll, welche Preise für welche Platzierungen sinnvoll sind.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =195;
        $name = 'Spazieren gehen mit Bewohnern unseres Seniorenstifts';
        $contactPerson = 112;
        $category = 3;
        $time = '2016-12-23 15:30';
        $organization = 'Pflegeheim Grossmueller';
        $countHelper = 8;
        $description =' Unsere Bewohner gehen gerne an die frische Luft. Das belebt die Lebensgeister und stellt eine schöne Abwechslung dar. Leider haben viele von ihnen keine Angehörigen in der Nähe die täglich die Möglichkeit haben, sie zu besuchen. Wir suchen regelmäßig Freiwillige, die Lust haben ab und zu mit unseren Bewohnern zusammen spazieren zu gehen und sich über Gott und die Welt zu unterhalten. Viele von ihnen haben spannende Geschichten erlebt und freuen sich immer, diese interessierten Zuhörern erzählen zu können.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =106;
        $name = 'Nachhilfe in theoretische Informatik';
        $contactPerson = 105;
        $category = 4;
        $time = '2016-12-28 10:00';
        $organization = '';
        $countHelper = 1;
        $description ='  Ich habe Schwierigkeiten mit dem Kurs Theoretische Informatik (vor allem Pumping Lemma). Es wäre cool, wenn mir das jemand nochmal erklären könnte. Der angegebene Zeitraum ist nur ein Vorschlag, wenn du zu einer anderen Zeit kannst, schreib mich gerne an.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =107;
        $name = 'Mathe-Nachhilfe gesucht';
        $contactPerson = 110;
        $category = 4;
        $time = '2016-12-29 08:00';
        $organization = '';
        $countHelper = 1;
        $description ='   Ich bin jetzt im 1. Semester und verstehe Mathe einfach nicht. Ich hab schon richtig Angst vor den Prüfungen… Kann mir jemand helfen?';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =108;
        $name = 'Tee-Karawane in Hannover';
        $contactPerson = 100;
        $category = 6;
        $time = '2017-01-05 14:00';
        $organization = '';
        $countHelper = 4;
        $description ='   Du hast Lust dich mit anderen zu engagieren und mit Menschen in Berührung zu kommen? Schließ dich unserer Tee-Karawane an. Zwischen den Jahren wollen wir mit einer lustigen Truppe durch Hannover ziehen und mit Thermoskannen bewaffnet Tee an Obdachlose verteilen. Wäre schön, wenn ein paar Leute zusammen kommen.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

        //---------------------------------------------------------------------------- d.m.Y H:i
        $id =109;
        $name = 'Gassi mit Fiffi';
        $contactPerson = 101;
        $category = 6;
        $time = '2017-01-09 9:00';
        $organization = '';
        $countHelper = 1;
        $description ='   Halli Hallo. Ich bin Manuel und bräuchte jemanden, der sich über die Weihnachtstage um meinen Hund Fiffy kümmern kann. Fiffy ist ein süßer kleiner Dackel, den ich wegen der Allergie meines Vaters leider nicht mit zu meinen Eltern nehmen kann. Du müsstest zweimal am Tag mit Fifa rausgehen (eine kleinere Runde von ca. 5 Minuten und eine größere von ca. 15 min), ihm zu Fressen geben, etc. Erfahrung mit Hunden wäre super, aber nicht unbedingt erforderlich (Fiffy ist auch ganz lieb). Weiteres lässt sich ja auch dann noch persönlich klären. Wäre schön, wenn sich jemand meldet.';
        $data1 = array(
            array(
                'idGuteTat' => $id,
                'name' => $name,
                'contactPerson' => $contactPerson,
                'category' => $category,
                'idPostal' => -1,
                'starttime' => $time,
                'endtime' => $time,
                'organization' => $organization,
                'countHelper' => $countHelper,
                'idTrust' => 1,
                'status' => 'freigegeben'
                )
            );
        $data2 = array(
            array(
                'idDeedTexts' => $id,
                'description' => $description
                )
            );

        $deed=$this->table('Deeds');
        $deed->insert($data1)
             ->save();
        $deedtext=$this->table('DeedTexts');
        $deedtext->insert($data2)
             ->save();

    }
}
