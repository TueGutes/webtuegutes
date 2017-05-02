<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.0.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.0.0/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.0.0/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster@1.0.0/dist/leaflet.markercluster.js"></script>

<?php
/**
 * Erzeugt die Karte.
 *
 * Erstellt eine Karte, die entweder auf eine Adresse zeigt, oder mehrere Gute Taten anzeigt.
 *
 * @author     Florian Sosch <florian.sosch@stud.hs-hannover.de>
 */
class Map {
    /**
     * Definiert den Bereich, in dem die Karte angezeit werden soll.
     * @param string $left Abstand vom linken Bildrand in Pixel oder Prozent. Zum Beispiel "75px" oder "10%". 
     * @param string $height Höhe der Map nur in Pixel. Zum Beispiel "400px".
     * @param string $width Breite der Map in Pixel oder Prozent. Zum Beispiel "40px" oder "80%".
     */
    public function createSpace($left, $height, $width) {
        ?>
        <style>
            #mapid{ 
				position:relative; left:<?php echo $left ?>; z-index: 0;
                height:<?php echo $height ?>; width:<?php echo $width ?>;
            }
        </style>
        <?php
    }

    /**
     * Generiert eine Karte, die auf die übergebene Adresse zentriert ist.
     * Die Adresse wir mit einem Marker markiert.
     * @param string $address Die Adresse, auf der die Karte zentriert werden soll. Format: "Postleitzahl, Straße, Hausnummer". Es kann auch nur die Postleitzahl oder Postleitzahl und Straße, ohne Hausnummer angegeben werden. 
     */
    public function createMap($address) {
        $latLon = $this->getLatLonFromAddress($address);
        ?>
        <div id="mapid"></div>
        <script type="text/javascript">
		
		// Generieren der Karte
		var mymap = create(<?php echo $latLon["lat"] ?>, <?php echo $latLon["lon"] ?>, 14);
		
		// Marker setzen
		var marker = L.marker([<?php echo $latLon["lat"] ?>, <?php echo $latLon["lon"] ?>]).addTo(mymap);
        </script>
        <?php
    }

    /**
     * Erstellt eine Karte auf der alle guten Taten markiert werden, die in der DB sind.
     * Jede Markierung hat ein Popup, in dem der Name der Guten Tat angezeigt wird. Der Name kann angeklickt werden und leitet auf die entsprechende deeds_details Seite.
     * @param type $placeholder Filter: 'freigegeben','geschlossen','alle'
     */
    public function createAllDeedsMap($placeholder) {
        $numbersOfDeeds = DBFunctions::db_getGuteTatenAnzahl($placeholder);
        $this->createDeedsMap($numbersOfDeeds, $placeholder);
    }
	
    /**
     * Erstellt eine Karte auf der, die guten Taten markiert werden, die auch in der Guten Taten Liste sind.
     * Jede Markierung hat ein Popup, in dem der Name der guten Tat angezeigt wird. Der Name kann angeklickt werden und leitet auf die entsprechende deeds_details Seite.
     * @param type $tatenProSeite Anzahl der aufzulistenden guten Taten
     * @param type $placeholder Filter: 'freigegeben','geschlossen','alle'
     */
    public function createDeedsMap($tatenProSeite, $placeholder, $userID) {
        ?>
        <div id="mapid"></div>
        <script type="text/javascript">
		// Generieren der Karte. Karte wird auf Hannover zentriert.
		var mymap = create(52.375892, 9.73201 , 12);
		
		var markers = L.markerClusterGroup();
        <?php
		if($userID == -1){
			$arr = DBFunctions::db_getGuteTatenForList($tatenProSeite * ($_GET['page'] - 1), $tatenProSeite, $placeholder);
		}else{
			$arr = DBFunctions::db_getGuteTatenForUser($tatenProSeite*($_GET['page']-1), $tatenProSeite, $placeholder, $userID);
		}
        // Gehe alle Taten durch und erstelle für jeden Eintrag ein Marker.
        foreach ($arr as $oneDeed) {
            $latLon = $this->getLatLonFromAddress($oneDeed->postalcode . ',' . $oneDeed->street . ',' . $oneDeed->housenumber);
            ?>
			// latlng repräsentiert einen geographischen Punkt mit den atitude und longitude Werten.
			var latlng = L.latLng(<?php echo $latLon["lat"] ?>, <?php echo $latLon["lon"] ?>);
			// Erstellen den Marker mit einem Popup.
			markers.addLayer(L.marker(latlng).bindPopup('<a href="<?php echo 'deeds_details?id=' . $oneDeed->idGuteTat ?>"><?php echo $oneDeed->name ?></a>'));
            <?php
        }
	?>
        mymap.addLayer(markers);
        </script>
        <?php
    }
	
	/**
     * Erstellt eine Karte auf der, die guten Taten markiert werden, nach denen gesucht wurde.
     * Jede Markierung hat ein Popup, in dem der Name der guten Tat angezeigt wird. Der Name kann angeklickt werden und leitet auf die entsprechende deeds_details Seite.
     * @param string $keyword eingenommene Stichworte (category oder TatName)
	 * @param string $sort die Etikett aus search.php, kann 'starttime', 'endtime' und 'status' sein
     */
    public function createSearchMap($keyword, $sort) {
        ?>
        <div id="mapid"></div>
        <script type="text/javascript">
		// Generieren der Karte. Karte wird auf Hannover zentriert.
		var mymap = create(52.375892, 9.73201 , 12);
		
		var markers = L.markerClusterGroup();
        <?php
		 $arr = DBFunctions::db_searchDuringGutes($keyword,$sort);
        // Gehe alle Taten durch und erstelle für jeden Eintrag ein Marker.
        foreach ($arr as $oneDeed) {
            $latLon = $this->getLatLonFromAddress($oneDeed->postalcode . ',' . $oneDeed->street . ',' . $oneDeed->housenumber);
            ?>
			// latlng repräsentiert einen geographischen Punkt mit den atitude und longitude Werten.
			var latlng = L.latLng(<?php echo $latLon["lat"] ?>, <?php echo $latLon["lon"] ?>);
			// Erstellen den Marker mit einem Popup.
			markers.addLayer(L.marker(latlng).bindPopup('<a href="<?php echo 'deeds_details?id=' . $oneDeed->idGuteTat ?>"><?php echo $oneDeed->name ?></a>'));
            <?php
        }
	?>
        mymap.addLayer(markers);
        </script>
        <?php
    }
	
	/**
	 * Berechnet die Distanz zwischen zwei Punkten in Meter.
	 * @param string $firstAddress Erste Adresse. Format: "Postleitzahl, Straße, Hausnummer". Es kann auch nur die Postleitzahl oder Postleitzahl und Straße, ohne Hausnummer angegeben werden. 
 	 * @param string $secondAddress Zweite Adresse. Format: "Postleitzahl, Straße, Hausnummer". Es kann auch nur die Postleitzahl oder Postleitzahl und Straße, ohne Hausnummer angegeben werden. 
	 * @return int Entfernung der beiden Punktel in Meter.
	 */
	public function getDistance($firstAddress, $secondAddress) {
		$firstLatLon = $this->getLatLonFromAddress($firstAddress);
		$secondLatLon = $this->getLatLonFromAddress($secondAddress);
		$latFirstAddress = $firstLatLon["lat"];
		$lonFirstAddress = $firstLatLon["lon"];
		$latSecondAddress = $secondLatLon["lat"];
		$lonSecondAddress = $secondLatLon["lon"];
		$radLat1 = $latFirstAddress*3.1415926535898/180.0;
        $radLat2 = $latSecondAddress*3.1415926535898/180.0;
        $a = $radLat1 - $radLat2;
        $b = ($lonFirstAddress*3.1415926535898/180.0) - ($lonSecondAddress*3.1415926535898/180.0);
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
        $s = $s * 6378.137; // EARTH_RADIUS;
        $s = round($s * 1000,0); 
        return $s;
	}
	  
    /**
     * Wandelt den Adressstring in die geographischen Koordinaten "lat" und "lon" um.
     * @param string $address Format: "Postleitzahl, Straße, Hausnummer"
     * @return string[] Array mit den "lat" und "lon" Werten.
     */
    private function getLatLonFromAddress($address) {
        $preparedAddress = str_replace(' ', '+', $address);
        // Umwandel der Adresse in geographischen Koordinaten.
        $contents = file('http://nominatim.openstreetmap.org/search?format=json&limit=2&q=' . $preparedAddress);
        $stringArray = explode('"', substr($contents[0], strpos($contents[0], '"lat"') + 7));
        return Array("lat" => $stringArray[0], "lon" => $stringArray[4]);
    }
}
?>
<script type="text/javascript">
		/**
		 *  Generieren der Karte.
		 *	Übergabeparameter:
		 *	[0] = Mittelpunkt der Map (lat)
		 *	[1] = Mittelpunkt der Map (lon)
		 *	[2] = Zoom
		 */
		function create(){
			var mymap = L.map('mapid').setView([arguments[0], arguments[1]], arguments[2]);
			L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors', maxZoom: 18}).addTo(mymap);
			return mymap;
		}
	</script>
