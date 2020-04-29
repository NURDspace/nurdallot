<?php

include("settings.php");

for ($i = 0; $i < $daysinadvance; $i++) {

  $query = "INSERT INTO places (placedate, allot, places) VALUES ";
  for ($j = 0; $j < (24/$allotduration); $j++) {
    if ($j > 0) $query .= ", ";
    $query .= "('".date('Y-m-d', (time() + ($i * 86400)))."', '".$j."', '". $allotplaces. "')";
  }
//  print $query."\n";
  $pdo->exec($query);
}

?>
