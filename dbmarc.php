<?php
$host = 'sg112.servergrove.com';
$user = 'dalborgo';
$password = '90210';
$db = 'dalborgo_db';


$conn=mysqli_connect($host,$user,$password,$db);
if (!$conn ) die('Cannot connect: ' . mysqli_error());
mysqli_set_charset($conn,"utf8");
?>