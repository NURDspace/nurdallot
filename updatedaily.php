<?php

// execute this -after- midnight and it will remove yesterdays allots and insert new places to allot

include("settings.php");

$query = "SELECT * FROM places WHERE placedate = :placedate";
$statement = $pdo->prepare($query);
$params = ['placedate' => date('Y-m-d', (time() - 86400))];
$statement->execute($params);
$result = $statement->fetchAll();

if (count($result) > 0) {
  foreach($result as $row) {
   //remove all placeID allots
   $query = "DELETE FROM allots WHERE placeID= :place_id";
   $statement = $pdo->prepare($query);
   $params = ['place_id' => $row['placeID']];
   $statement->execute($params);

   //and remove the places themselves
   $query = "DELETE FROM places WHERE placeID= :place_id";
   $statement = $pdo->prepare($query);
   $params = ['place_id' => $row['placeID']];
   $statement->execute($params);
  }

  $query = "INSERT INTO places (placedate, allot, places) VALUES ";
  for ($j = 0; $j < (24/$allotduration); $j++) {
    if ($j > 0) $query .= ", ";
    $query .= "('".date('Y-m-d', (time() + (($daysinadvance-1) * 86400)))."', '".$j."', '". $allotplaces. "')";
  }
  $statement = $pdo->prepare($query);
  $statement->execute();

} else {
 print "already executed today?\n";
}

?>
