<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

require '../vendor/autoload.php';

use Colrow\ColrowDrive;

if (isset($_FILES['image'])) {
  $file = $_FILES['image'];

  $fp = fopen($file['tmp_name'], 'r');
  $file_body = base64_encode(fread($fp, filesize($file['tmp_name'])));
  fclose($fp);

  $parent_id = '0BzYF7TMk17WcfjZpS1hidExwVzM4NVhTWDl4bHdfM0JkYWg2c2RfSmJ4cmNEODc4X0JPalk';
  $response = ColrowDrive::upload('somin1968', $file['name'], $file['type'], $file_body, $parent_id);

  echo json_encode($response);
} else {
  echo '<form method="post" action="" enctype="multipart/form-data">';
  echo '<input type="file" name="image">';
  echo '<br><br>';
  echo '<input type="submit" value="アップロード">';
  echo '</form>';
}
