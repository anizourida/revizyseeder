<?php
$dataStr = "1, 2, 'L\'exercice'";
$values = str_getcsv($dataStr, ",", "'", "\\");
var_dump($values);
