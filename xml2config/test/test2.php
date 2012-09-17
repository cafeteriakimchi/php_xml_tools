<?php
include '../xml2object.php';

$o = new xml2object;
var_dump($o->generateFromFileObject('./test.xml'));

