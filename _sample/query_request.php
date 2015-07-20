<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

require '../vendor/autoload.php';

use Colrow\ColrowClient;
use Colrow\ColrowQuery;

ColrowClient::initialize(
  'somin1968',
  '10NHgHBSl4NqKnaeXHE_ieTGPcJZGLvoVgas9Af783XY',
  'シート1'
);

$query1 = new ColrowQuery();
$query1->equalTo('名称', 'ほげ');
$query2 = new ColrowQuery();
$query2->equalTo('番号', '四');

$query = new ColrowQuery();
$query->greaterThan('num', 0);
$query->lessThan('num', 4);
$query->orQuery($query1, $query2);
$query->orderBy('num');
$query->reverse(true);

$objects = $query->find();

$result_array = [];
foreach ($objects as $object) {
  $result_array[] = $object->toJson();
}
echo '[' . implode(',', $result_array) . ']';
