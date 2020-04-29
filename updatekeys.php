<?

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

$query = "SELECT * FROM places WHERE placedate = '".date('Y-m-d', time())."' AND (allot = '".$allotmin."' OR allot = '".$allotmax."')";
$result = $mysql->query($query);
if ($result -> num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $query = "SELECT * FROM allots WHERE placeID = '".$row['placeID']."'";
    $result2 = $mysql->query($query);
    if ($result2->num_rows > 0) {
      while ($row2 = $result2->fetch_assoc()) {
        enableuser($row2['uid']);
//        print "enabled ".$row2['uid']."\n";
      }
    }
  }
}

//print "\n";

?>
