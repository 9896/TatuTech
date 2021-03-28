<?php
$ar = array();
array_push($ar, "alex");
array_push($ar, "alice");
print_r($ar);
$ar[5] = rand(3,9);
//Understanding the ternary short hand
 echo $ar[5] ?? "Sorry no shit";

$test = [{"leo"},{"mary"}];
?>