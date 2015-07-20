<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

require '../vendor/autoload.php';

use Colrow\ColrowClient;

ColrowClient::initialize(
  'somin1968',
  '10NHgHBSl4NqKnaeXHE_ieTGPcJZGLvoVgas9Af783XY',
  'シート1'
);

list($status_code, $response) = ColrowClient::_request('GET');

echo json_encode($response);
