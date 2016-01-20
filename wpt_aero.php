<?php
$doc = simplexml_load_file('frwpt.xml');
echo '<pre>';
//print_r($doc);
$text = '';
foreach($doc->wpt as $wpt) {
	foreach($wpt->attributes() as $name => $value) {
		if($name == 'lat') $lat = $value;
		if($name == 'lon') $lon = $value;
	}
	$text .= '<waypoint>';
	$text .= '<identifier>'.str_replace('/', '', substr($wpt->name, 2)).'</identifier>';
	$text .= '<type>USER WAYPOINT</type>';
    $text .= '<country-code></country-code>';
    $text .= '<lat>'.$lat.'</lat>';
    $text .= '<lon>'.$lon.'</lon>';
    $text .= '<comment></comment>';
	$text .= '</waypoint>';
}
echo $text;


/* 
tab, retour à la ligne
encodage

<?xml version='1.0' encoding='UTF-8'?>
<flight-plan xmlns="http://www8.garmin.com/xmlschemas/FlightPlan/v1">
  <waypoint-table>
    <waypoint>
      <identifier>TUCKUPS</identifier>
      <type>USER WAYPOINT</type>
      <country-code></country-code>
      <lat>36.205333</lat>
      <lon>112.808833</lon>
      <comment>13NM FL105 MEA</comment>
    </waypoint>
    <waypoint>
      <identifier>TUCKUPN</identifier>
      <type>USER WAYPOINT</type>
      <country-code></country-code>
      <lat>36.422000</lat>
      <lon>112.814500</lon>
      <comment>13NM FL105 MEA</comment>
    </waypoint>
    <waypoint>
      <identifier>FOSSILS</identifier>
      <type>USER WAYPOINT</type>
      <country-code></country-code>
      <lat>36.273833</lat>
      <lon>112.582833</lon>
      <comment>15NM FL105 MEA</comment>
    </waypoint>
    <waypoint>
      <identifier>FOSSILN</identifier>
      <type>USER WAYPOINT</type>
      <country-code></country-code>
      <lat>36.381167</lat>
      <lon>112.311667</lon>
      <comment>15NM FL105 MEA</comment>
    </waypoint>
    <waypoint>
      <identifier>DRAGONS</identifier>
      <type>USER WAYPOINT</type>
      <country-code></country-code>
      <lat>36.016667</lat>
      <lon>112.258500</lon>
      <comment>20NM FL105 MEA</comment>
    </waypoint>
    <waypoint>
      <identifier>DRAGONN</identifier>
      <type>USER WAYPOINT</type>
      <country-code></country-code>
      <lat>36.318500</lat>
      <lon>112.110000</lon>
      <comment>20NM FL105 MEA</comment>
    </waypoint>
    <waypoint>
      <identifier>ZUNIS</identifier>
      <type>USER WAYPOINT</type>
      <country-code></country-code>
      <lat>35.972167</lat>
      <lon>111.889833</lon>
      <comment>19NM FL105 MEA</comment>
    </waypoint>
    <waypoint>
      <identifier>ZUNIN</identifier>
      <type>USER WAYPOINT</type>
      <country-code></country-code>
      <lat>36.289667</lat>
      <lon>111.850667</lon>
      <comment>19NM FL105 MEA</comment>
    </waypoint>
  </waypoint-table>
</flight-plan>