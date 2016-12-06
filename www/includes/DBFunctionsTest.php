<?php
require_once 'PHPUnit/Autoload.php';
require_once 'db_connector.php';
/**
 * Created by PhpStorm.
 * User: dais
 * Date: 2016-12-3
 * Time: 16:35
 * @author     Shanghui Dai <shanghui.dai@stud.hs-hannover.de>
 */

// Alle Tests abhängig von Daten im Datenbank(hirt die Daten in meiner localen)
// Mache Funktionen ohne Rückgabewert werden hier nicht listet. Weil Sie Änderungen in Datenbank machen werden.
// Aber sie sind auch getestet.
class DBFunctionsTest extends PHPUnit_Framework_TestCase
{
//  --------------------- deeds.php --------------------------------
    public function testDeedsGetGuteTatenForList(){
        $classInstance = new DBFunctions();
        $this->assertEquals('array',gettype($classInstance->db_getGuteTatenForList(1,2,'alle')));
    }
    public function testGetIdUserByUsername(){
        $classInstance = new DBFunctions();
        $this->assertEquals(1,$classInstance->db_getIdUserByUsername('testuser'));
        $this->assertEquals(-1,$classInstance->db_getIdUserByUsername('testuser2222'));
    }
    public function testCountGuteTatenForUser(){
        $classInstance = new DBFunctions();
        $this->assertEquals(0,$classInstance->db_countGuteTatenForUser('alle',1));
        $this->assertEquals(0,$classInstance->db_countGuteTatenForUser('alle',1));
    }
    public function testGetGuteTatenAnzahl(){
        $classInstance = new DBFunctions();
        $this->assertEquals(3,$classInstance->db_getGuteTatenAnzahl('alle'));
    }
//   ---------------------- end deeds.php -----------------------------
//   ---------------------- deeds_bearbeiten.php -----------------------
    public function testGetGuteTat(){
        $classInstance = new DBFunctions();
        $this->assertEquals('array',gettype($classInstance->db_getGuteTat(1)));
    }
    public function testGet_user(){
        $classInstance = new DBFunctions();
        $this->assertEquals('array',gettype($classInstance->db_get_user('testuser')));
    }
    public function testDoesGuteTatNameExists(){
        $classInstance = new DBFunctions();
        $this->assertEquals(true,$classInstance->db_doesGuteTatNameExists('bbb'));
        $this->assertEquals(false,$classInstance->db_doesGuteTatNameExists('23456'));
    }
    public function testGetIdPostalbyPostalcodePlace(){
        $classInstance = new DBFunctions();
        $this->assertEquals(false,$classInstance->db_getIdPostalbyPostalcodePlace(1234,'nichts'));
    }
//  -------------------- end deeds_bearbeiten.php -----------------------
//  -------------------- deeds_bewerbung.php --------------------------

    public function testIsUserCandidateOfGuteTat(){
        $classInstance = new DBFunctions();
        $this->assertEquals(false,$classInstance->db_isUserCandidateOfGuteTat(200,200));
        $this->assertEquals(true,$classInstance->db_isUserCandidateOfGuteTat(1,1));
    }
    public function testDoesGuteTatExists(){
        $classInstance = new DBFunctions();
        $this->assertEquals(true,$classInstance->db_doesGuteTatExists(1));
        $this->assertEquals(false,$classInstance->db_doesGuteTatExists(200));
    }
    public function testGetUserIdOfContactPersonByGuteTatID(){
        $classInstance = new DBFunctions();
        $this->assertEquals(1,$classInstance->db_getUserIdOfContactPersonByGuteTatID(1));
        $this->assertEquals(false,$classInstance->db_getUserIdOfContactPersonByGuteTatID(100));
    }
    public function testGetStatusOfGuteTatById(){
        $classInstance = new DBFunctions();
        $this->assertEquals('geschlossen',$classInstance->db_getStatusOfGuteTatById(1));
        $this->assertEquals(false,$classInstance->db_getStatusOfGuteTatById(100));
    }
    public function testIsNumberOfAcceptedCandidatsEqualToRequestedHelpers(){
        $classInstance = new DBFunctions();
        $this->assertEquals(true,$classInstance->db_isNumberOfAcceptedCandidatsEqualToRequestedHelpers(1));
    }
    public function testGetStatusOfBewerbung(){
        $classInstance = new DBFunctions();
        $this->assertEquals('offen',$classInstance->db_getStatusOfBewerbung(1, 1));
        $this->assertEquals(false,$classInstance->db_getStatusOfBewerbung(1, 2));
    }
    public function testGetUsernameOfBenutzerByID(){
        $classInstance = new DBFunctions();
        $this->assertEquals('testuser',$classInstance->db_getUsernameOfBenutzerByID(1));
        $this->assertEquals(false,$classInstance->db_getUsernameOfBenutzerByID(500));
    }
    public function testGetApplicationTextOfApplication(){
        $classInstance = new DBFunctions();
        $this->assertEquals('',$classInstance->db_getApplicationTextOfApplication(1,1));
        $this->assertEquals(false,$classInstance->db_getApplicationTextOfApplication(1,2));
    }
    public function testGetNameOfGuteTatByID(){
        $classInstance = new DBFunctions();
        $this->assertEquals('aaaa',$classInstance->db_getNameOfGuteTatByID(1));
        $this->assertEquals(false,$classInstance->db_getNameOfGuteTatByID(300));
    }
    public function testGetUsernameOfContactPersonByGuteTatID(){
        $classInstance = new DBFunctions();
        $this->assertEquals('testuser',$classInstance->db_getUsernameOfContactPersonByGuteTatID(1));
        $this->assertEquals(false,$classInstance->db_getUsernameOfContactPersonByGuteTatID(300));
    }
    public function testGetEmailOfContactPersonByGuteTatID(){
        $classInstance = new DBFunctions();
        $this->assertEquals('testmailgutetaten@gmail.com',$classInstance->db_getEmailOfContactPersonByGuteTatID(1));
        $this->assertEquals(false,$classInstance->db_getEmailOfContactPersonByGuteTatID(300));
    }
//    public function testAddBewerbung(){
//        $classInstance = new DBFunctions();
//        $this->assertEquals(true,$classInstance->db_addBewerbung(1, 1, 'sehr gut'));
//        $this->assertEquals(false,$classInstance->db_addBewerbung(1, 2, 'gut'));
//    }

    public function testGetMailOfBenutzerByID(){
        $classInstance = new DBFunctions();
        $this->assertEquals('testmailgutetaten@gmail.com',$classInstance->db_getMailOfBenutzerByID(1));
        $this->assertEquals(false,$classInstance->db_getMailOfBenutzerByID(200));
    }
//    public function testAcceptBewerbung(){
//        $classInstance = new DBFunctions();
//        $this->assertEquals(true,$classInstance->db_acceptBewerbung(1,1,'gut'));
//        $this->assertEquals(false,$classInstance->db_acceptBewerbung(33,1,'gut'));
//    }

//    public function testAcceptBewerbung(){
//        $classInstance = new DBFunctions();
//        $this->assertEquals(true,$classInstance->db_acceptBewerbung(1,1,'sorry'));
//        $this->assertEquals(false,$classInstance->db_acceptBewerbung(33,1,'nichts'));
//    }
//  -------------------- end deeds_bewerbung.php --------------------
//  -------------------- deeds_bewerten.php --------------------
    public function testIstGeschlossen(){
        $classInstance = new DBFunctions();
        $this->assertEquals(true,$classInstance->db_istGeschlossen(1));
        $this->assertEquals(false,$classInstance->db_istGeschlossen(2));
    }
    public function testIstGetBewerb(){
        $classInstance = new DBFunctions();
        $this->assertEquals(null,$classInstance->db_getBewerb(1));
        $this->assertEquals(null,$classInstance->db_getBewerb(200));
    }
//    public function testUserBewertung(){
//        $classInstance = new DBFunctions();
//        $this->assertEquals(,$classInstance->db_userBewertung(2,'testuser'));
//    }

//    public function testIstGetBewerb(){
//        $classInstance = new DBFunctions();
//        $this->assertEquals(,$classInstance->db_userAnsehen(2,'testuser'));
//    }
//  ------------------- end deeds_bewerten.php --------------------
//  ------------------- deeds_create.php -----------------------
    public function testIdOfBenutzername(){
        $classInstance = new DBFunctions();
        $this->assertEquals(1,$classInstance->db_idOfBenutzername('testuser'));
        $this->assertEquals(false,$classInstance->db_idOfBenutzername('haha'));
    }
    public function testGetAllModerators(){
        $classInstance = new DBFunctions();
        $this->assertEquals('array',gettype($classInstance->db_getAllModerators()));
    }
    public function testGetAllAdministrators(){
        $classInstance = new DBFunctions();
        $this->assertEquals('array',gettype($classInstance->db_getAllAdministrators()));
    }
//  ------------------- end deeds_create.php -------------------------
//  ------------------- deeds_details.php ---------------------
    public function testIstFreigegeben(){
        $classInstance = new DBFunctions();
        $this->assertEquals(true,$classInstance->db_istFreigegeben(1));
        $this->assertEquals(false,$classInstance->db_istFreigegeben(4));
    }
//  ------------------ end deeds_details.php ------------------------




}

