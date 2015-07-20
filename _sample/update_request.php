<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

require '../vendor/autoload.php';

use Colrow\ColrowClient;
use Colrow\ColrowObject;
use Colrow\ColrowQuery;

ColrowClient::initialize(
  'somin1968',
  '10NHgHBSl4NqKnaeXHE_ieTGPcJZGLvoVgas9Af783XY',
  'シート1'
);

$query = new ColrowQuery();

$objects = $query->find();
$row = $objects[0];

$row->set('名称', 'ほげほげーん');
$row->set('番号', 'イチ');

$object = $row->save();
echo $object->toJson();
