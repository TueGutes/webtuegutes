<?php
/*
*	@author Florian Sosch
*/
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.0.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.0.0/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.0.0/dist/MarkerCluster.Default.css" />
<script src="https://unpkg.com/leaflet.markercluster@1.0.0/dist/leaflet.markercluster.js"></script>


<?php 
	/*
	*	Die Funktionen müssen in einem definierten Bereich aufgerufen werden, die Map wird im kompletten Bereich angezeigt.
	* 
	*	 -	Beispiel für den definierten Bereich:
	*			<style>
	*				#mapid{ 
	*					position:absolute; top:50px; left:100px;
	*					height: 400px; width: 400px;
	*				}
	*			</style>
	*
	*	 -	Aufruf der Funktion in dem Bereich:
	*			<div id="mapid"></div>
	*			<?php
	*				createAllDeedsMap();
	*			?>
	*/
	
	/*
	*	createMap generiert eine Karte, die auf die übergebene Adresse zentriert ist.
	*	Die Adresse wir mit einem Marker markiert.
	*/
	function createMap($address){
		// Ersetzt die Leerzeichen durch ein +, damit die Adresse umgewandelt werden kann. 
		$address = str_replace(' ', '+', $address);
		
		// Umwandel der Adresse in geographischen Koordinaten.
		$contents = file('http://nominatim.openstreetmap.org/search?format=json&limit=2&q=' . $address);
		
		// Ergebnis String zerlegen, damit man an die Werte für lon und lat kommt.
		$stringArray = explode ('"' ,substr($contents[0], strpos($contents[0], '"lat"')+7));
		?>
		<script type="text/javascript">
			var lat = "<?php echo $stringArray[0] ?>";
			var lon = "<?php echo $stringArray[4] ?>";
			
			// Generieren der Map
			var mymap = L.map('mapid').setView([lat, lon], 14);
			L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
				maxZoom: 18,
			}).addTo(mymap);

			// Marker setzen
			var marker = L.marker([lat, lon]).addTo(mymap);
		</script>
		<?php
	}
	
	/*
	*	createAllDeedsMap erstellt eine Map auf der alle Guten Taten angezeigt werden, die in der DB sind.
	*	Die Marker haben ein Popup, in dem der Name der Guten Tat angezeigt wird. Der Name ist ein Link auf die entsprechende deeds_details.
	*/
	function createAllDeedsMap(){
		?>
		<script type="text/javascript">
			// Generieren der Map. Map wird auf Hannover zentriert.
			var mymap = L.map('mapid').setView([52.375892, 9.73201], 12);
			L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
				maxZoom: 18,
			}).addTo(mymap);

			var markers = L.markerClusterGroup();
		</script>
		<?php
			require './includes/db_connector.php';
			$arr = db_getGuteTaten();
			
			// Gehe alle Taten durch und erstelle für jeden Eintrag ein Marker.
			foreach($arr as $i){
				// Adresse zusammensetzen und in geographischen Koordinaten umwandeln.
				$address = $i->postalcode . ',' . $i->street . ',' . $i->housenumber;
				$address = str_replace(' ', '+', $address);
				$contents = file('http://nominatim.openstreetmap.org/search?format=json&limit=2&q=' . $address);
				$stringArray = explode ('"' ,substr($contents[0], strpos($contents[0], '"lat"')+7));
				
				?>
				<script type="text/javascript">
					// Erstellen den Marker mit einem Popup.
					markers.addLayer(L.marker([<?php echo $stringArray[0] ?>, <?php echo $stringArray[4] ?>]).bindPopup('<a href="<?php echo 'deeds_details?id='.$i->idGuteTat ?>"><?php echo $i->name ?></a>'));
				</script>
				<?php
			}
		?>
		<script type="text/javascript">
			mymap.addLayer(markers);
		</script>
		<?php
	}
	
	/*
	*	createDeedsMap erstellt eine Map, für die Gute Taten Liste, auf der die Guten Taten angezeigt werden, die auch in der Liste stehen.
	*/
	function createDeedsMap(){
		?>
		<script type="text/javascript">
			// Generieren der Map. Map wird auf Hannover zentriert.
			var mymap = L.map('mapid').setView([52.375892, 9.73201], 12);
			L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
				maxZoom: 18,
			}).addTo(mymap);

			var markers = L.markerClusterGroup();
		</script>
		<?php
			require './includes/db_connector.php';
			$arr = db_getGuteTatenForList(10*(§_GET['page']-1),10);
			
			// Gehe alle Taten durch und erstelle für jeden Eintrag ein Marker.
			foreach($arr as $i){
				// Adresse zusammensetzen und in geographischen Koordinaten umwandeln.
				$address = $i->postalcode . ',' . $i->street . ',' . $i->housenumber;
				$address = str_replace(' ', '+', $address);
				$contents = file('http://nominatim.openstreetmap.org/search?format=json&limit=2&q=' . $address);
				$stringArray = explode ('"' ,substr($contents[0], strpos($contents[0], '"lat"')+7));

				?>
				<script type="text/javascript">
					// Erstellen den Marker mit einem Popup.
					markers.addLayer(L.marker([<?php echo $stringArray[0] ?>, <?php echo $stringArray[4] ?>]).bindPopup('<a href="<?php echo 'deeds_details?id='.$i->idGuteTat ?>"><?php echo $i->name ?></a>'));
				</script>
				<?php
			}
		?>
		<script type="text/javascript">
			mymap.addLayer(markers);
		</script>
		<?php
	}
?>
