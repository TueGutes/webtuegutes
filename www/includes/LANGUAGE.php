<?php
/**
 * Sprachvariablen
 *
 * Enthält alle Sprachvariablen, was das Warten und hinzufügen anderer Sprachen stark vereinfacht
 *
 * @author Henrik Huckauf <henrik.huckauf@stud.hs-hannover.de>
 */

//====error====
$wlang['error'][403] = 'Zugriff verweigert...';
$wlang['error'][404] = 'Diese Seite wurde nicht gefunden :(';
$wlang['error'][500] = 'Es trat ein interner Fehler auf.<br>Der Server kann nicht genauer zu der Fehlerbedingung in seiner Antwort auf den Client sein.';
$wlang['error']['default'] = 'Es trat ein Fehler auf. Bitte versuchen Sie es erneut.';

//====navigation====
$wlang['nav_home'] = 'Home';
$wlang['nav_about'] = 'Über uns';
$wlang['nav_deeds'] = 'Gute Taten';
$wlang['nav_contact'] = 'Kontakt';

//====names====
$wlang['imprint'] = 'Impressum';
$wlang['privacy'] = 'Datenschutzerklärung';
$wlang['terms'] = 'AGB';

//====imprint====
//$wlang['imprint'] = '';

//====privacy====
//$wlang['privacy'] = '';

//====agb====
$wlang['terms_head'] = 'Allgemeine Geschäftsbedingungen';

//====about===
//$wlang['about'] = '';

//====contact====
$wlang['contact_head'] = 'Kontakt';

$wlang['contact_form_name'] = 'Name';
$wlang['contact_form_preName'] = 'Vorname';
$wlang['contact_form_postName'] = 'Nachname';
$wlang['contact_form_age'] = 'Alter';
$wlang['contact_form_years'] = 'Jahre';
$wlang['contact_form_email'] = 'E-Mail (wird für eine Antwort benötigt)';
$wlang['contact_form_message'] = 'Nachricht';
$wlang['contact_form_mandatoryField'] = 'Pflichtfeld';

$wlang['contact_form_submit'] = 'Absenden';
$wlang['contact_form_back'] = 'Zurück';

$wlang['contact_suc_sent'] = 'Die Nachricht wurde erfolgreich gesendet!';
$wlang['contact_err_preName'] = 'Falsche Eingabe beim Vornamen!';
$wlang['contact_err_postName'] = 'Falsche Eingabe beim Nachnamen!';
$wlang['contact_err_age'] = 'Falsche Eingabe beim Alter!';
//$wlang['err_email'] = 'Falsche Eingabe der Mail Adresse!';
$wlang['contact_err_message'] = 'Falsche Eingabe bei der Nachricht!';
$wlang['contact_err_captcha'] = 'Falscher Captcha-Code!';		
$wlang['contact_err_unknown'] = 'Error! Bitte erneut versuchen.';

$wlang['contact_captcha_title'] = 'Captcha - Diesen Code in das darunter liegende Feld eintragen!';

//====login====
$wlang['login_head'] = 'Login';

$wlang['login_placeholder_username'] = 'Benutzername';// / E-Mail';
$wlang['login_placeholder_password'] = 'Passwort';
$wlang['login_button_submit'] = 'Login';

$wlang['login_code_423'] = '<red>Um diese Seite zu besuchen, müssen Sie eingeloggt sein!</red>';
$wlang['login_code_101'] = '<green>Ihr Account wurde erfolgreich verifiziert.<br>Sie können sich nun mit ihrem Account einloggen!</green>';
$wlang['login_code_102'] = '<green>Bestätigungsemail wurde gesendet!<br>Bitte verifizieren Sie ihren Account erst, indem Sie auf den Link in der Bestätigungsemail klicken.</green>';

//====register====
$wlang['register_head'] = 'Registrieren';

$wlang['register_placeholder_username'] = 'Benutzername / E-Mail';
$wlang['register_placeholder_password'] = 'Passwort';
$wlang['register_button_submit'] = 'Registrieren';

//====deeds====
$wlang['deeds_head'] = 'Gute Taten';

//====home====
$wlang['welcome'] = 'Willkommen' . ($_USER->loggedIn() ? ', ' . $_USER->getUsername() : '') . '!';

//====comment====
$wlang['comment_head'] = 'Kommentare';

$wlang['comment_form_head'] = 'Neuer Kommentar:';
$wlang['comment_form_message'] = 'Kommentar';
$wlang['comment_form_submit'] = 'Absenden';

$wlang['comment_suc_sent'] = 'Kommentar wurde veröffentlicht!';
$wlang['comment_err_messageEmpty'] = 'Bitte geben Sie ein Kommentar ein!';
$wlang['comment_err_messageLength'] = 'Kommentare dürfen nicht länger sein als 8000 Zeichen!';

?>