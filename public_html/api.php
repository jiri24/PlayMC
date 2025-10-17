<?php

session_start();

require_once '../system/System.php';
require_once '../controllers/Recommender.php';

$system = new System();
//$random = $system->getRandom();

//$countX = array(0 => 0, 1 => 0);
//$countY = array(0 => 0, 1 => 0, 2 => 0);
//$countZ = array(0 => 0, 1 => 0);
//for ($i = 0; $i < 1000; $i++){
//    $x = $random->uniformDistribution(0, 1);
//    $y = $random->exponentialDistribution(4);
//    $z = $random->bernoulliDistribution(0.1);
//    $countX[$x]++;
//    $countY[$y]++;
//    $countZ[$z]++;
//}

//print_r($countX);
//echo("<br>");
//print_r($countY);
//echo("<br>");
//print_r($countZ);
//echo("<br>");

$recommender = new Recommender($system);