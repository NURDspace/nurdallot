<?

// execute this -after- midnight and it will remove yesterdays allots and insert new places to allot

include("settings.php");

$query = "SELECT * FROM places WHERE placedate = '".date('Y-m-d', (time() - 86400))."'";
$result = $mysql->query($query);

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
   //remove all placeID allots
   $query = "DELETE FROM allots WHERE placeID=".$row['placeID'];
   $mysql->query($query);
   //and remove the places themselves
   $query = "DELETE FROM places WHERE placeID=".$row['placeID'];
   $mysql->query($query);
  }

  $query = "INSERT INTO places (placedate, allot, places) VALUES ";
  for ($j = 0; $j < (24/$allotduration); $j++) {
    if ($j > 0) $query .= ", ";
    $query .= "('".date('Y-m-d', (time() + (($daysinadvance-1) * 86400)))."', '".$j."', '". $allotplaces. "')";
  }
  $mysql->query($query);

} else {
 print "already executed today?\n";
}

$mysql->close();

?>
