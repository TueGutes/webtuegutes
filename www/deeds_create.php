<?php
/*
*@	Autor Christian Hock
	alpha in zusammenarbeit mit Klaus Sobotta
	Mailsysstem: Nick Nolting
	Kalenderfunktion: Henrik Huckauf
enthält Teile von deeds_create und deeds_bearbeiten
*/
require './includes/DEF.php';
require './includes/UTILS.php';
require './includes/db_connector.php';
require './includes/_top.php';
//Zeigt an auf welcher Unterseite man gerade ist.
if(isset($_GET['Seite'])){
	$_SESSION['Seite'] =$_GET['Seite'];
}
//Initialisiert alle Variablen bei Erststart
if(!isset($_SESSION['Seite']))$_SESSION['Seite'] =0;
if($_SESSION['Seite']==0){
$_SESSION['tat_name'] ='';
$_SESSION['tat_pictures'] ='';
$_SESSION['tat_description'] ='';
$_SESSION['tat_category'] ='';
$_SESSION['tat_street'] ='';
$_SESSION['tat_housenumber'] ='';
$_SESSION['tat_postalcode'] ='';
$_SESSION['tat_place'] ='';
$_SESSION['tat_starttime'] ='';
$_SESSION['tat_endtime'] ='';
$_SESSION['tat_organization'] ='';
$_SESSION['tat_countHelper'] ='1';
$_SESSION['tat_idTrust'] ='1';
date_default_timezone_set("Europe/Berlin");
$_SESSION['Seite'] +=1;
}
//Damit nur eine Seite aufgerufen wird
$stop='0';
//Name setzen
if($_SESSION['Seite'] ==1 || $_SESSION['Seite'] ==2){
//Guckt ob der Aufruf für Seite 2 erfolgreich war
if($_SESSION['Seite'] ==2) {
        if(isset($_POST['name']))$_SESSION['tat_name']=$_POST['name'];
        if(DBFunctions::db_doesGuteTatNameExists($_SESSION['tat_name'])){
                $_SESSION['Seite'] =1;
                $stop=1;
        }else if ($_SESSION['tat_name'] === ''){
                $stop=2;
                $_SESSION['Seite'] =1;
        }
}
if($_SESSION['Seite'] ==1 ||$_SESSION['Seite'] ==3){
echo'
<h2>Wähle zuerst einen aussagekräftigen Namen für deine Tat. :)</h2>
<h3>Jede Tat hat einen eigenen Namen und kann auch durch diesen gesucht werden. Deine Tat wird dem entsprechend öfter aufgerufen, wenn dein Name intuitiv verständlich ist.</h3>
<br><br>';
//Fehlermeldung für nicht erfolgreichen Aufruf.
if($stop=='1')echo '<h3><red>Eine andere Tat ist bereits unter diesem Namen veröffentlicht.</red></h3>';
if($stop=='2')echo '<h3><red>Bitte einen neuen Namen eingeben.</red></h3><br>';
//le buttons
echo'
<br><br>
        <form action="./deeds_create?Seite=2" method="POST">
                <br>
                <br>
                <input type="text" name="name" value="';echo $_SESSION['tat_name'] ;echo'" placeholder="Name der Tat" />
                <br>
                <br>
                <input type="submit" name="button" value="weiter" />
</form>        ';
}
}
//Bild setzen
if($_SESSION['Seite'] ==2 || $_SESSION['Seite']==3){
        if(isset($_FILES['pictures']))$_SESSION['tat_pictures']=$_FILES['pictures'];        
        if($_SESSION['Seite']==2){
echo'
<h2>Möchtest du ein Bild hochladen?</h2>
<h3>Taten mit Bildern werden eher von anderen Nutzern angeklickt.</h3>
<br><br>
<div class="center block deeds_create"> 
                <br>
                <br>
                <form action="" method="POST" enctype="multipart/form-data">        
                <input type="file" name="pictures" accept="image/*">
                <input type="submit" name="button" value="hochladen" />
                <br>
                <br>';
				if(isset($_SESSION['tat_pictures']['name'])&&$_SESSION['tat_pictures']['name']=='')$_SESSION['tat_pictures']='';
                if($_SESSION['tat_pictures']!=''){
                        if(isset($_FILES['pictures']['name']) && $_FILES['pictures']['name']!=NULL){
							if($_FILES['pictures']['size']>10000){
								echo '<red><h3>bitte ein Bild mit einer Größe unter 10kb hochladen.</h3></red>';
							}
						else{
							$_SESSION['tat_pictures']='data: ' . mime_content_type($_FILES['pictures']['tmp_name']) . ';base64,' . base64_encode (file_get_contents($_FILES['pictures']['tmp_name']));
						
                        echo' <center><img src="';echo $_SESSION['tat_pictures'];echo'" ></center>
                                <br>
                                <br>
                                <br>';
						}
						}
				}
                else if(isset($_FILES['pictures'])&& $_FILES['pictures']['name']!=""){
                $_SESSION['tat_pictures']='data: ' . mime_content_type($_FILES['pictures']['tmp_name']) . ';base64,' . base64_encode (file_get_contents($_FILES['pictures']['tmp_name'])); 
                echo '<h3><green>Das Bild ist nun hochgeladen</h2></green>
                                <center><img src=".$_SESSION["tat_pictures"]." ></center>
                                <br>
                                <br>
                                <br>';
                }               
                echo'
                </form>
                <form action="./deeds_create?Seite=3" method="POST" enctype="multipart/form-data">                
                ';
                if(isset($_FILES['pictures'])){
                        echo'<input type="hidden" name="pictures" value="';$_SESSION['tat_pictures'] ;
                echo'" />';
                }
                echo'
                <a href=./deeds_create?Seite=1><input type="button" name="button" value="zurück" /></a>
                <input type="submit" name="button" value="weiter" />
</form></div>';
}
}
//Beschreibung setzen
if(($_SESSION['Seite'] ==3 || $_SESSION['Seite'] ==4)){        
if($_SESSION['Seite'] ==4){
        if(isset($_POST['description']))$_SESSION['tat_description']=$_POST['description'];
        if ($_SESSION['tat_description'] === ''){
                $stop=1;
                $_SESSION['Seite'] =3;                
}
}
if($_SESSION['Seite'] ==3){        
echo'
<h2>Beschreibe deine Tat.</h2>
<h3>Was und wie ist es zu tun? </h3>';
if($stop==1)echo '<h3><red>Bitte beschreibe deine Tat.</red></h3>';
echo '
<br><br>
<div class="center block deeds_create">
                <form action="./deeds_create?Seite=4" method="POST" enctype="multipart/form-data">
                <br>
                <br>
                <textarea id="text" name="description" rows="10" placeholder="Beschreiben Sie die auszuführende Tat. Werben Sie für Ihr Angebot. Nutzen sie ggf. eine Rechtschreibüberprüfung." >';echo $_SESSION['tat_description'] ;echo'</textarea>
                <br><br>
                <br>
                <a href=./deeds_create?Seite=2><input type="button" name="button" value="zurück" /></a>
                <input type="submit" name="button" value="weiter" />
</form></div>';
} 
}
if(($_SESSION['Seite'] ==4 ||$_SESSION['Seite'] ==5)){
        if($_SESSION['Seite'] ==5){
		if(!isset($_POST['startdate']) ||
                        (intval($_POST['starttime_minutes']) % 5 != 0 && intval($_POST['starttime_minutes']) != 0) ||
                        !DateHandler::isValid($_POST['startdate'] . ' ' . $_POST['starttime_hours'] . ':' . $_POST['starttime_minutes'], 'd.m.Y H:i')){
                        $stop=6;
                }else if(!isset($_POST['enddate']) ||
                        (intval($_POST['endtime_minutes']) % 5 != 0 && intval($_POST['endtime_minutes']) != 0) ||
                        !DateHandler::isValid($_POST['enddate'] . ' ' . $_POST['endtime_hours'] . ':' . $_POST['endtime_minutes'], 'd.m.Y H:i')){
                        $stop=7;
                }
        if(isset($_POST['street']))$_SESSION['tat_street']=$_POST['street'];
        if(isset($_POST['housenumber']))$_SESSION['tat_housenumber']=$_POST['housenumber'];
        if(isset($_POST['postalcode']))$_SESSION['tat_postalcode']=$_POST['postalcode'];
        if(isset($_POST['place']))$_SESSION['tat_place']=$_POST['place'];
        if(isset($_POST['organization']))$_SESSION['tat_organization']=$_POST['organization'];
        if(isset($_POST['starttime']))$_SESSION['tat_starttime']=$_POST['starttime'];
        if(isset($_POST['endtime']))$_SESSION['tat_endtime']=$_POST['endtime'];
        if(isset($_POST['idTrust']))$_SESSION['tat_idTrust']=$_POST['idTrust'];
        if(isset($_POST['countHelper']))$_SESSION['tat_countHelper']=$_POST['countHelper'];
        if(isset($_POST['category']))$_SESSION['tat_category']=$_POST['category'];
		if(isset($_POST['startdate']))$_SESSION['tat_starttime'] =$_POST['startdate'];
		if(isset($_POST['enddate']))$_SESSION['tat_endtime'] =$_POST['enddate'];
        if(!isset($_POST['street'])){
                $stop=1;
        }else if($_POST['housenumber']==''){
                $stop=2;
        }else if($_POST['postalcode']==''|| !is_numeric($_POST['postalcode'])){
                $stop=3;
        }else if($_POST['place']==''){
                $stop=4;
        }else if ((DBFunctions::db_getIdPostalbyPostalcodePlace($_POST['postalcode'],$_POST['place'])==false)){
                $stop=8;
        }
			}        
if($stop!=0)$_SESSION['Seite'] =4;
if($_SESSION['Seite'] ==4){
	if(!isset($startdate))$startdate='';
	if(!isset($enddate))$enddate='';
echo'
<h2>Rahmendaten</h2>
<h3>Hier noch einmal alle notwendigen Informationen für Bewerber.</h3>';
        if($stop==1)echo '<h3><red>Bitte eine neue Straße eingeben.</red></h3><br>';
        if($stop==2)echo '<h3><red>Bitte eine neue Hausnummer eingeben.</red></h3><br>';        
        if($stop==3)echo '<h3><red>Die Postleitzahl bitte als Zahl eingeben.</red></h3><br>';        
        if($stop==4)echo '<h3><red>Bitte einen Ort angeben.</red></h3><br>';
        if($stop==5)echo '<h3><red>Bitte eine neue Organisation eingeben.</red></h3><br>';
        if($stop==6)echo '<h3><red>Das Format von der Startzeit ist falsch.</red></h3><br>';
        if($stop==7)echo '<h3><red>Das Format von der Endzeit ist falsch.</red></h3><br>';
        if($stop==8)echo '<h3><red>Die Postleitzahl passt nicht zum Ort.</red></h3><br>';               
        echo '<br><br>
<div class="center block deeds_create">
<form action="./deeds_create?Seite=5" method="POST" enctype="multipart/form-data">
			<br><br>
                <br>
                <br>
                <br>
                <input type="text" name="street" value="'.$_SESSION["tat_street"].'" placeholder="Straßenname" />
                <input type="text" name="housenumber" value="'.$_SESSION["tat_housenumber"].'" placeholder="Hausnummer" />
                <br>
                <input type="text" name="postalcode" value="'.$_SESSION["tat_postalcode"].'" placeholder="Postleitzahl" />
                <br>
                <input type="text" name="place" value="'.$_SESSION["tat_place"].'"placeholder="Stadtteil" />
                <br>';
				$start_dh = (new DateHandler())->set($startdate);
				$end_dh = (new DateHandler())->set($enddate);
				echo 'Beginn:<br><input type="date" name="startdate" value="' . ($start_dh !== false ? $start_dh->get('d.m.Y') : '') . '" placeholder="DD.MM.YYYY" required />';
				if($start_dh === false) $start_dh = (new DateHandler())->setHours(8)->setMinutes(0);
				echo ' um <select name="starttime_hours"><option value="00"' . (intval($start_dh->getHours()) == 0 ? ' selected' : '') . '>00</option><option value="01"' . (intval($start_dh->getHours()) == 1 ? ' selected' : '') . '>01</option><option value="02"' . (intval($start_dh->getHours()) == 2 ? ' selected' : '') . '>02</option><option value="03"' . (intval($start_dh->getHours()) == 3 ? ' selected' : '') . '>03</option><option value="04"' . (intval($start_dh->getHours()) == 4 ? ' selected' : '') . '>04</option><option value="05"' . (intval($start_dh->getHours()) == 5 ? ' selected' : '') . '>05</option><option value="06"' . (intval($start_dh->getHours()) == 6 ? ' selected' : '') . '>06</option><option value="07"' . (intval($start_dh->getHours()) == 7 ? ' selected' : '') . '>07</option><option value="08"' . (intval($start_dh->getHours()) == 8 ? ' selected' : '') . '>08</option><option value="09"' . (intval($start_dh->getHours()) == 9 ? ' selected' : '') . '>09</option><option value="10"' . (intval($start_dh->getHours()) == 10 ? ' selected' : '') . '>10</option><option value="11"' . (intval($start_dh->getHours()) == 11 ? ' selected' : '') . '>11</option><option value="12"' . (intval($start_dh->getHours()) == 12 ? ' selected' : '') . '>12</option><option value="13"' . (intval($start_dh->getHours()) == 13 ? ' selected' : '') . '>13</option><option value="14"' . (intval($start_dh->getHours()) == 14 ? ' selected' : '') . '>14</option><option value="15"' . (intval($start_dh->getHours()) == 15 ? ' selected' : '') . '>15</option><option value="16"' . (intval($start_dh->getHours()) == 16 ? ' selected' : '') . '>16</option><option value="17"' . (intval($start_dh->getHours()) == 17 ? ' selected' : '') . '>17</option><option value="18"' . (intval($start_dh->getHours()) == 18 ? ' selected' : '') . '>18</option><option value="19"' . (intval($start_dh->getHours()) == 19 ? ' selected' : '') . '>19</option><option value="20"' . (intval($start_dh->getHours()) == 20 ? ' selected' : '') . '>20</option><option value="21"' . (intval($start_dh->getHours()) == 21 ? ' selected' : '') . '>21</option><option value="22"' . (intval($start_dh->getHours()) == 22 ? ' selected' : '') . '>22</option><option value="23"' . (intval($start_dh->getHours()) == 23 ? ' selected' : '') . '>23</option></select>';
				echo ' : <select name="starttime_minutes"><option value="00"' . (intval($start_dh->getMinutes()) == 0 ? ' selected' : '') . '>00</option><option value="05"' . (intval($start_dh->getMinutes()) == 5 ? ' selected' : '') . '>05</option><option value="10"' . (intval($start_dh->getMinutes()) == 10 ? ' selected' : '') . '>10</option><option value="15"' . (intval($start_dh->getMinutes()) == 15 ? ' selected' : '') . '>15</option><option value="20"' . (intval($start_dh->getMinutes()) == 20 ? ' selected' : '') . '>20</option><option value="25"' . (intval($start_dh->getMinutes()) == 25 ? ' selected' : '') . '>25</option><option value="30"' . (intval($start_dh->getMinutes()) == 30 ? ' selected' : '') . '>30</option><option value="35"' . (intval($start_dh->getMinutes()) == 35 ? ' selected' : '') . '>35</option><option value="40"' . (intval($start_dh->getMinutes()) == 40 ? ' selected' : '') . '>40</option><option value="45"' . (intval($start_dh->getMinutes()) == 45 ? ' selected' : '') . '>45</option><option value="50"' . (intval($start_dh->getMinutes()) == 50 ? ' selected' : '') . '>50</option><option value="55"' . (intval($start_dh->getMinutes()) == 55 ? ' selected' : '') . '>55</option></select>';
				echo ' Uhr<br><br>';
				echo 'Ende:<br><input type="date" name="enddate" value="' . ($end_dh !== false ? $end_dh->get('d.m.Y') : '') . '" placeholder="DD.MM.YYYY" required />';
				if($end_dh === false) $end_dh = (new DateHandler())->setHours(8)->setMinutes(0);
				echo ' um <select name="endtime_hours"><option value="00"' . (intval($end_dh->getHours()) == 0 ? ' selected' : '') . '>00</option><option value="01"' . (intval($end_dh->getHours()) == 1 ? ' selected' : '') . '>01</option><option value="02"' . (intval($end_dh->getHours()) == 2 ? ' selected' : '') . '>02</option><option value="03"' . (intval($end_dh->getHours()) == 3 ? ' selected' : '') . '>03</option><option value="04"' . (intval($end_dh->getHours()) == 4 ? ' selected' : '') . '>04</option><option value="05"' . (intval($end_dh->getHours()) == 5 ? ' selected' : '') . '>05</option><option value="06"' . (intval($end_dh->getHours()) == 6 ? ' selected' : '') . '>06</option><option value="07"' . (intval($end_dh->getHours()) == 7 ? ' selected' : '') . '>07</option><option value="08"' . (intval($end_dh->getHours()) == 8 ? ' selected' : '') . '>08</option><option value="09"' . (intval($end_dh->getHours()) == 9 ? ' selected' : '') . '>09</option><option value="10"' . (intval($end_dh->getHours()) == 10 ? ' selected' : '') . '>10</option><option value="11"' . (intval($end_dh->getHours()) == 11 ? ' selected' : '') . '>11</option><option value="12"' . (intval($end_dh->getHours()) == 12 ? ' selected' : '') . '>12</option><option value="13"' . (intval($end_dh->getHours()) == 13 ? ' selected' : '') . '>13</option><option value="14"' . (intval($end_dh->getHours()) == 14 ? ' selected' : '') . '>14</option><option value="15"' . (intval($end_dh->getHours()) == 15 ? ' selected' : '') . '>15</option><option value="16"' . (intval($end_dh->getHours()) == 16 ? ' selected' : '') . '>16</option><option value="17"' . (intval($end_dh->getHours()) == 17 ? ' selected' : '') . '>17</option><option value="18"' . (intval($end_dh->getHours()) == 18 ? ' selected' : '') . '>18</option><option value="19"' . (intval($end_dh->getHours()) == 19 ? ' selected' : '') . '>19</option><option value="20"' . (intval($end_dh->getHours()) == 20 ? ' selected' : '') . '>20</option><option value="21"' . (intval($end_dh->getHours()) == 21 ? ' selected' : '') . '>21</option><option value="22"' . (intval($end_dh->getHours()) == 22 ? ' selected' : '') . '>22</option><option value="23"' . (intval($end_dh->getHours()) == 23 ? ' selected' : '') . '>23</option></select>';
				echo ' : <select name="endtime_minutes"><option value="00"' . (intval($end_dh->getMinutes()) == 0 ? ' selected' : '') . '>00</option><option value="05"' . (intval($end_dh->getMinutes()) == 5 ? ' selected' : '') . '>05</option><option value="10"' . (intval($end_dh->getMinutes()) == 10 ? ' selected' : '') . '>10</option><option value="15"' . (intval($end_dh->getMinutes()) == 15 ? ' selected' : '') . '>15</option><option value="20"' . (intval($end_dh->getMinutes()) == 20 ? ' selected' : '') . '>20</option><option value="25"' . (intval($end_dh->getMinutes()) == 25 ? ' selected' : '') . '>25</option><option value="30"' . (intval($end_dh->getMinutes()) == 30 ? ' selected' : '') . '>30</option><option value="35"' . (intval($end_dh->getMinutes()) == 35 ? ' selected' : '') . '>35</option><option value="40"' . (intval($end_dh->getMinutes()) == 40 ? ' selected' : '') . '>40</option><option value="45"' . (intval($end_dh->getMinutes()) == 45 ? ' selected' : '') . '>45</option><option value="50"' . (intval($end_dh->getMinutes()) == 50 ? ' selected' : '') . '>50</option><option value="55"' . (intval($end_dh->getMinutes()) == 55 ? ' selected' : '') . '>55</option></select>';
				echo ' Uhr<br>';
				echo'
                <br>
                <input type="text" name="organization" value="'.$_SESSION["tat_organization"].'"placeholder="Organisation" />
                <br>
                Benötigte Helfer:<br>
                <input type="number" name="countHelper" value="'.$_SESSION["tat_countHelper"].'" placeholder="Benötigte Helfer" />
                <br>       
                <br>
				Erforderlicher Verantwortungslevel:<br>
				<select name="idTrust">
					<option value="1"<?php echo $_SESSION[';echo"'";echo"tat_idTrust']";echo' == 1?" selected":""; >1</option>
					<option value="2"<?php echo $_SESSION[';echo"'";echo"tat_idTrust']";echo' == 2?" selected":""; >2</option>
					<option value="3"<?php echo $_SESSION[';echo"'";echo"tat_idTrust']";echo' == 3?" selected":""; >3</option>
				</select>
                <br><br>
				Kategorie:<br>';
				//todo: mehr datenbankzugriffe als nötig
				//todo: Kategorien müssen fortlaufend sein
				$kz=0;//KategorieZähler
				echo'<select name="category">';		
				while(DBFunctions::db_doesCategoryIDExist(++$kz)){
					echo'<option value="'.$kz.'">'.DBFunctions::db_getCategorytextbyCategoryid($kz).'</option>';			
					}	
				echo'</select><br><br>
                <br>
				<a href=./deeds_create?Seite=3><input type="button" name="button" value="zurück" /></a>
                <input type="submit" name="button" value="weiter" />
</form></div>';
}
}
if($_SESSION['Seite'] ==5){        
                //Einfügen der Guten Tat
                $uid = DBFunctions::db_idOfBenutzername($_USER->getUsername());
                $plz = DBFunctions::db_getIdPostalbyPostalcodePlace($_SESSION['tat_postalcode'], $_SESSION['tat_place']);
                $start_dh = (new DateHandler())->set($_SESSION['tat_starttime']);
                $end_dh = (new DateHandler())->set($_SESSION['tat_endtime']);
                DBFunctions::db_createGuteTat($_SESSION['tat_name'], $uid, DBFunctions::db_getCategorytextbyCategoryid($_SESSION['tat_category']), $_SESSION['tat_street'], $_SESSION['tat_housenumber'], 
                                                                          $plz, $start_dh->get(),$end_dh->get(), $_SESSION['tat_organization'], $_SESSION['tat_countHelper'],
                                                                          $_SESSION['tat_idTrust'], $_SESSION['tat_description'], $_SESSION['tat_pictures']);
                //Versenden der Info-Mails        
                //Bestimmen der Empfänger
                $mods = DBFunctions::db_getAllModerators();
                $admins = DBFunctions::db_getAllAdministrators();
                //Festlegen des Mail-Inhalts
                $mailSubject = 'Gute Tat ' . "'" . $_SESSION['tat_name'] . "'" . ' wurde erstellt!';
                $mailContent1 = '<div style="margin-left:10%;margin-right:10%;background-color:#757575"><img src="img/wLogo.png" alt="TueGutes" title="TueGutes" style="width:25%"/></div><h2>Hallo!';
                $mailContent2 = '</h2><br>' . $_USER->getUsername() . ' hat gerade eine neue gute Tat erstellt. <br>Um die gute Tat zu bestätigen oder abzulehnen, klicke auf den folgenden Link: <br><a href="' . $HOST . '/deeds_details?id=' . DBFunctions::db_getIDOfGuteTatByName($_SESSION['tat_name']) . '">Zur guten Tat</a>';
                //Versenden der Emails an Moderatoren
                for ($i = 0; $i < sizeof($mods); $i++) {
                        sendEmail($mods[$i], $mailSubject, $mailContent1 . $mailContent2);
                }
                //Versenden der Emails an Administratoren
                for ($i = 0; $i < sizeof($admins); $i++) {
                        sendEmail($admins[$i], $mailSubject, $mailContent1 . $mailContent2);                      
                }

                $_SESSION['Seite']='0';
echo'
<h2><green>Deine Tat wurde erstellt! </green></h2>
<h3>und wird nun von uns geprüft. </h3>
<a href="./deeds.php"><input type="button" name="Toll" value="Toll!"/></a>';
}
require './includes/_bottom.php';
?>