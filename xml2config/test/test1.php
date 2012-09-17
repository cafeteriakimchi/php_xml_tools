<?php

include '../object2xml.php';
$o = new stdClass();
$o->a = 123;
$o->b = 123;
$o->array[] = 1;
$o->array[] = 2;
$o->array[] = 3;

echo object2xml::generateValidXml($o);
