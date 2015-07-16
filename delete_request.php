<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

require 'vendor/autoload.php';

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
$row = end($objects);

if ($row->destroy()) {
  echo 'OK';
}