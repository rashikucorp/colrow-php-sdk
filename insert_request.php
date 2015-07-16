<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

require 'vendor/autoload.php';

use Colrow\ColrowClient;
use Colrow\ColrowObject;

ColrowClient::initialize(
  'somin1968',
  '10NHgHBSl4NqKnaeXHE_ieTGPcJZGLvoVgas9Af783XY',
  'シート1'
);

$row = new ColrowObject();

$row->set('num', '5');
$row->set('名称', 'ふわふわ');
$row->set('番号', '伍');

$object = $row->save();
echo $object->toJson();