<link rel="stylesheet" href="https://unpkg.com/leaflet@1.0.1/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.0.1/dist/leaflet.js"></script>

<?php 
	/*+
	*	createMap generiert eine Karte, die auf die übergebene Adresse zentriert ist.
	*	Die Adresse wir mit einem Marker markiert markiert.
	*	Diese Funktion muss in einem definierten Bereich aufgerufen werden, die Map wird im kompletten Bereich angezeigt.
	*/
	function createMap($address){
		$address = str_replace(' ', '+', $address);
		//Umwandel der Adresse in geographischen Koordinaten.
		$contents = file('http://nominatim.openstreetmap.org/search?format=json&limit=2&q=' . $address);
		$stringArray = explode ('"' ,substr($contents[0], strpos($contents[0], '"lat"')+7));
		?>
		
		<script type="text/javascript">
			var lat = "<?php echo $stringArray[0] ?>";
			var lon = "<?php echo $stringArray[4] ?>";
			
			//Generieren der Map
			var mymap = L.map('mapid').setView([lat, lon], 14);
			L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
				attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
				maxZoom: 18,
			}).addTo(mymap);

			//Marker setzen
			var marker = L.marker([lat, lon]).addTo(mymap);
		</script>
		<?php
	}
?>