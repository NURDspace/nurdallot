<?php

// execute this -after- midnight and it will remove yesterdays allots and insert new places to allot

include("settings.php");
include("keyfunctions.php");

function intdiv_2($a, $b) {
  return floor($a/$b);
}

disableall();

$allotmin = intdiv_2(date("G",(time() - ($minutesbackallowed*60))) , $allotduration);
if ($allotmin < 0) $allotmin = 0;
$allotmax = intdiv_2(date("G",(time() + ($minutesforwardallowed*60))) ,$allotduration);
if ($allotmax > (24/$allotduration)) $allotmax = (24/$allotduration);

$query = "SELECT * FROM places WHERE placedate = :placedate AND allot IN (:allotmin, :allotmax)";
$statement = $pdo->prepare($query);
$params = [
  'placedate' => date('Y-m-d', time()),
  'allotmin' => $allotmin,
  'allotmax' => $allotmax
];
$statement->execute($params);
$result = $statement->fetchAll();

foreach($result as $row) {
  $query = "SELECT * FROM allots WHERE placeID = :place_id";
  $statement = $pdo->prepare($query);
  $params = [ 'place_id' => $row['placeID']];
  $statement->execute($params);
  $result2 = $statement->fetchAll();
  foreach($result2 as $row2) {
    enableuser($row2['uid']);
//      print "enabled ".$row2['uid']."\n";
  }
}

//print "\n";

?>
