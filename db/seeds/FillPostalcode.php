<?php

use Phinx\Seed\AbstractSeed;

class FillPostalcode extends AbstractSeed
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
            array('postalcode' => 30159,'place' => 'Bult'),
            array('postalcode' => 30159,'place' => 'Mitte'),
            array('postalcode' => 30159,'place' => 'Nordstadt'),
            array('postalcode' => 30159,'place' => 'Südstadt'),

            array('postalcode' => 30161,'place' => 'List'),
            array('postalcode' => 30161,'place' => 'Mitte'),
            array('postalcode' => 30161,'place' => 'Oststadt'),
            array('postalcode' => 30161,'place' => 'Vahrenwald'),

            array('postalcode' => 30163,'place' => 'List'),
            array('postalcode' => 30163,'place' => 'Vahrenwald'),

            array('postalcode' => 30165,'place' => 'Hainholz'),
            array('postalcode' => 30165,'place' => 'Nordstadt'),
            array('postalcode' => 30165,'place' => 'Vahrenwald'),
            array('postalcode' => 30165,'place' => 'Vinnhorst'),

            array('postalcode' => 30167,'place' => 'Calenberger Neustadt'),
            array('postalcode' => 30167,'place' => 'Herrenhausen'),
            array('postalcode' => 30167,'place' => 'Mitte'),
            array('postalcode' => 30167,'place' => 'Nordstadt'),

            array('postalcode' => 30169,'place' => 'Calenberger Neustadt'),
            array('postalcode' => 30169,'place' => 'Mitte'),
            array('postalcode' => 30169,'place' => 'Südstadt'),

            array('postalcode' => 30171,'place' => 'Mitte'),
            array('postalcode' => 30171,'place' => 'Südstadt'),

            array('postalcode' => 30173,'place' => 'Bult'),
            array('postalcode' => 30173,'place' => 'Südstadt'),
            array('postalcode' => 30173,'place' => 'Waldhausen'),
            array('postalcode' => 30173,'place' => 'Waldheim'),

            array('postalcode' => 30175,'place' => 'Bult'),
            array('postalcode' => 30175,'place' => 'Mitte'),
            array('postalcode' => 30175,'place' => 'Oststadt'),
            array('postalcode' => 30175,'place' => 'Südstadt'),
            array('postalcode' => 30175,'place' => 'Zoo'),

            array('postalcode' => 30177,'place' => 'List'),
            array('postalcode' => 30177,'place' => 'Zoo'),

            array('postalcode' => 30179,'place' => 'Brink-Hafen'),
            array('postalcode' => 30179,'place' => 'List'),
            array('postalcode' => 30179,'place' => 'Sahlkamp'),
            array('postalcode' => 30179,'place' => 'Vahrenheide'),
            array('postalcode' => 30179,'place' => 'Vahrenwald'),

            array('postalcode' => 31303,'place' => 'Burgdorf'),
            array('postalcode' => 30419,'place' => 'Burg'),
            array('postalcode' => 30419,'place' => 'Hainholz'),
            array('postalcode' => 30419,'place' => 'Herrenhausen'),
            array('postalcode' => 30419,'place' => 'Ledeburg'),
            array('postalcode' => 30419,'place' => 'Leinhausen'),
            array('postalcode' => 30419,'place' => 'Marienwerder'),
            array('postalcode' => 30419,'place' => 'Misburg-Nord'),
            array('postalcode' => 30419,'place' => 'Nordhafen'),
            array('postalcode' => 30419,'place' => 'Stöcken'),
            array('postalcode' => 30419,'place' => 'Vinnhorst'),

            array('postalcode' => 30449,'place' => 'Linden-Mitte'),
            array('postalcode' => 30449,'place' => 'Linden-Süd'),

            array('postalcode' => 30451,'place' => 'Limmer'),
            array('postalcode' => 30451,'place' => 'Linden-Nord'),

            array('postalcode' => 30453,'place' => 'Badenstedt'),
            array('postalcode' => 30453,'place' => 'Bornum'),
            array('postalcode' => 30453,'place' => 'Davenstedt'),
            array('postalcode' => 30453,'place' => 'Limmer'),
            array('postalcode' => 30453,'place' => 'Linden-Mitte'),
            array('postalcode' => 30453,'place' => 'Linden-Süd'),
            array('postalcode' => 30453,'place' => 'Ricklingen'),

            array('postalcode' => 30455,'place' => 'Badenstedt'),
            array('postalcode' => 30455,'place' => 'Davenstedt'),

            array('postalcode' => 30457,'place' => 'Mühlenberg'),
            array('postalcode' => 30457,'place' => 'Oberricklingen'),
            array('postalcode' => 30457,'place' => 'Wettbergen'),

            array('postalcode' => 30459,'place' => 'Groß Buchholz'),
            array('postalcode' => 30459,'place' => 'Linden-Süd'),
            array('postalcode' => 30459,'place' => 'Oberricklingen'),
            array('postalcode' => 30459,'place' => 'Ricklingen'),

            array('postalcode' => 30519,'place' => 'Döhren'),
            array('postalcode' => 30519,'place' => 'Mittelfeld'),
            array('postalcode' => 30519,'place' => 'Seelhorst'),
            array('postalcode' => 30519,'place' => 'Südstadt'),
            array('postalcode' => 30519,'place' => 'Waldhausen'),
            array('postalcode' => 30519,'place' => 'Waldheim'),
            array('postalcode' => 30519,'place' => 'Wülfel'),

            array('postalcode' => 30521,'place' => 'Ahlem'),
            array('postalcode' => 30521,'place' => 'Bemerode'),
            array('postalcode' => 30521,'place' => 'Leinhausen'),
            array('postalcode' => 30521,'place' => 'Mittelfeld'),
            array('postalcode' => 30521,'place' => 'Oststadt'),
            array('postalcode' => 30521,'place' => 'Südstadt'),

            array('postalcode' => 30539,'place' => 'Bemerode'),
            array('postalcode' => 30539,'place' => 'Mittelfeld'),
            array('postalcode' => 30539,'place' => 'Seelhorst'),
            array('postalcode' => 30539,'place' => 'Wülferode'),

            array('postalcode' => 30559,'place' => 'Anderten'),
            array('postalcode' => 30559,'place' => 'Bemerode'),
            array('postalcode' => 30559,'place' => 'Kirchrode'),
            array('postalcode' => 30559,'place' => 'Kleefeld'),
            array('postalcode' => 30559,'place' => 'Misburg-Süd'),
            array('postalcode' => 30559,'place' => 'Seelhorst'),
            array('postalcode' => 30559,'place' => 'Waldheim'),

            array('postalcode' => 30625,'place' => 'Groß Buchholz'),
            array('postalcode' => 30625,'place' => 'Heideviertel'),
            array('postalcode' => 30625,'place' => 'Kleefeld'),
            array('postalcode' => 30625,'place' => 'Wettbergen'),

            array('postalcode' => 30627,'place' => 'Groß Buchholz'),
            array('postalcode' => 30627,'place' => 'Heideviertel'),
            array('postalcode' => 30627,'place' => 'Misburg-Nord'),

            array('postalcode' => 30629,'place' => 'Misburg-Nord'),
            array('postalcode' => 30629,'place' => 'Misburg-Süd'),

            array('postalcode' => 30655,'place' => 'Bothfeld'),
            array('postalcode' => 30655,'place' => 'Groß Buchholz'),
            array('postalcode' => 30655,'place' => 'List'),
            array('postalcode' => 30655,'place' => 'Misburg-Nord'),

            array('postalcode' => 30657,'place' => 'Bothfeld'),
            array('postalcode' => 30657,'place' => 'Groß Buchholz'),
            array('postalcode' => 30657,'place' => 'Isernhagen-Süd'),
            array('postalcode' => 30657,'place' => 'Lahe'),
            array('postalcode' => 30657,'place' => 'List'),
            array('postalcode' => 30657,'place' => 'Sahlkamp'),

            array('postalcode' => 30659,'place' => 'Bothfeld'),
            array('postalcode' => 30659,'place' => 'Groß Buchholz'),
            array('postalcode' => 30659,'place' => 'Lahe'),

            array('postalcode' => 30669,'place' => 'Flughafen')
            );

        $table = $this -> table('Postalcode');
        $table->insert($data)
              ->save();
    }
}
