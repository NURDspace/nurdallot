<html>
<body marginwidth=25 marginheight=10 bgcolor="EEEEFF">
<font face="Verdana,'Times New Roman',System">
<?

// nurdallot
// allocate a place in a hackerspace in advance during 1.5m rules

include("settings.php");

if(!isset($_SERVER['Shib-uid'])) {
  die ("wtf");
}

// fill memory with places and allots table

$query = "SELECT * FROM places";
$result = $mysql->query($query);


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $placeIDlookup[$row["placeID"]] = $row["placedate"]."_".$row["allot"];
      $placeIDrevlookup[$row["placedate"]."_".$row["allot"]] = $row["placeID"];
      $places[$row["placedate"]."_".$row["allot"]] = $row["places"];
    }
} else {
    echo "0 places defined! prefill db plz";
}

// update users allots
$query = "SELECT * FROM allots WHERE uid='".$_SERVER['Shib-uid']."'";
$result = $mysql->query($query);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $allots[$placeIDlookup[$row["placeID"]]] = array ( $row['allotID'], $row['placeID'] );
    }
}

$userallots = count ($allots);

if ($_POST['submitbutton'] == "Allot" ) {

  if(!is_numeric($_POST['allot'])) die("FUCK YOU");
  // before first  :D  check if user already reserved this slot

  if (isset($allots[$_POST['placedate']."_".$_POST['allot']])) {
    $submitmessage = "We knew that already :D";
  } else {

    // first, check if user doesnt have too many allots already

    if ($userallots < $maxallots) {

      // then, check if the placeID isnt filled already

      if ($places[$_POST['placedate']."_".$_POST['allot']] > 0) {

        // lastly, execute stuff

        $query = "UPDATE places SET places = places - 1 WHERE placedate = '".mysqli_real_escape_string($mysql,$_POST['placedate'])."' AND allot = '".$_POST['allot']."'";
        $result = $mysql->query($query);

        $query = "INSERT INTO allots (uid, placeid) VALUES ('".$_SERVER['Shib-uid']."', '".$placeIDrevlookup[$_POST['placedate']."_".$_POST['allot']]."')";
        $result = $mysql->query($query);

        $submitmessage = "Reserved a place for ".$allotlabels[$_POST['allot']] . " on ".$_POST['placedate'];

      } else {
        $submitmessage = "This allot is already full!";
      }
     } else {
       $submitmessage = "You have too many allots already!";
    }
  }
}

if ($_POST['submitbutton'] == "Release" ) {
  // parse release form input

  // check if user has a allotment for the day
  // check if placeID doesnt get over $allotplaces (hmm, nah)

  // execute stuff (delete allot & update places)

    if(!is_numeric($_POST['allotID'])) die("FUCK YOU");
    if(!is_numeric($_POST['allot'])) die("FUCK YOU");
  $query = "DELETE FROM allots WHERE uid = '".$_SERVER['Shib-uid']."' AND allotID = '".$_POST['allotID'] . "'";
  $result = $mysql->query($query);

  if ($mysql->affected_rows > 0) {
    $query = "UPDATE places SET places = places + 1 WHERE placedate = '".mysqli_real_escape_string($mysql, $_POST['placedate'])."' AND allot = '".$_POST['allot']."'";
    $result = $mysql->query($query);

    $submitmessage = "Removed your allot for ".$allotlabels[$_POST['allot']]. " on ".$_POST['placedate'];
  } else {
    $submitmessage = "You werent in there ..";
  }

}

// update places in ram
$query = "SELECT * FROM places";
$result = $mysql->query($query);


if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $placeIDlookup[$row["placeID"]] = $row["placedate"]."_".$row["allot"];
      $placeIDrevlookup[$row["placedate"]."_".$row["allot"]] = $row["placeID"];
      $places[$row["placedate"]."_".$row["allot"]] = $row["places"];
    }
}

// update users allots
$query = "SELECT * FROM allots WHERE uid='".$_SERVER['Shib-uid']."'";
$result = $mysql->query($query);

$allots = Array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $allots[$placeIDlookup[$row["placeID"]]] = array ( $row['allotID'], $row['placeID'] );
    }
}

$userallots = count ($allots);

$mysql->close();

//var_dump($places);
//var_dump($allots);
?>

<font size=+1>
Hi <?= $_SERVER['Shib-givenName'] ?> , you have <?= $userallots ?> allots for the coming <?= $daysinadvance ?> days (inc today) out of the <?= $maxallots ?> available to you.<br>
<b><?= $submitmessage ?><br></b>
</font>

<table width=1000 height=500><tr><th>&nbsp;</th>
<?
for ($i = 0; $i < $daysinadvance; $i++) {
  print "<th>";
  print date('l', (time() + ($i * 86400)));
  print "<br>";
  print date('Y-m-d', (time() + ($i * 86400)));
  print "</th>";
}
?>
</tr>
<?
for ($i = 0; $i < (24/$allotduration); $i++) {

  print "<tr>";
  print "<th>";
  print $allotlabels[$i];
  print "</th>";
  for ($j = 0; $j < $daysinadvance; $j++) {
    // print seats available plus allot button , delete if you already alloted it

   if (isset($allots[date('Y-m-d', (time() + ($j * 86400)))."_".$i])) {
    $allotedbyme = 1;
    print "<td bgcolor=lightblue valign=bottom align=center>";

   } else {
    $allotedbyme = 0;
    if ($j % 2 ) {
      print "<td bgcolor=DDDDEE valign=bottom align=center>";
    } else {
      print "<td bgcolor=CCCCDD valign=bottom align=center>";
    }
   }
    print "<font size=-1>";
    print "Places left : ";
    print $places[date('Y-m-d', (time() + ($j * 86400)))."_".$i];
    print "</font><br>";
    print "<div align=center>";
    print "<form method=post><input type=hidden name=allot value=";
    print $i;
    print "><input type=hidden name=placedate value=";
    print date('Y-m-d', (time() + ($j * 86400)));
    if ($allotedbyme == 0) {
      if ($userallots >= $maxallots) {
        print "><input type=submit name=submitbutton value=Allot disabled></form>";
      } else {
        print "><input type=submit name=submitbutton value=Allot></form>";
      }
    } else {
	    print "><input type=hidden name=allotID value='".$allots[date('Y-m-d', (time() + ($j * 86400)))."_".$i][0]."'><input type=submit name=submitbutton value=Release></form>";
    }
    print "</div>";
    print "</td>";
  }
  print "</tr>\n"; 
}
?>
</table>
</body>
</html>
