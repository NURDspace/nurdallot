<?php

error_reporting(E_ALL | E_STRICT);

$daysinadvance = 7;
$maxallots = 4;
$allotduration = 4;
$allotplaces = 5; // how many places are available

$minutesbackallowed = 10; // how many minutes -after- allot the key remains working (for the checks)
$minutesforwardallowed = 10; // how many minutes -before- allot the key starts working

// ok i'm lazy
$allotlabels = array(0 => "0:00-4:00", 1 => "4:00-8:00", 2 => "8:00-12:00", 3 => "12:00-16:00", 4 => "16:00-20:00", 5 => "20:00-24:00");

$shib_uid = $_SERVER['Shib-uid'];

// $pdo = new PDO("mysql:host=localhost;dbname=nurdalllot", "nurdallot", "yoursqlpasswd", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo = new PDO("sqlite:nurdallot.sqlite", "", "", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

?>

