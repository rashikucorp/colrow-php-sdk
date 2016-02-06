<?php

namespace Colrow;

/**
 * ColrowClient - Class for comminicating with Google Drive.
 *
 * @author Somin Kobayashi <somin@rashikucorp.com>
 */
class ColrowDrive
{
  /**
   * Constant for the API Server Host Address.
   */
  const HOST_NAME = 'https://app.colrow.net/api/v1/drive';

  /**
   * ColrowDrive::upload, internal method for uploading to Google Drive.
   *
   * @param string $user      Account for Colrow API Call.
   * @param string $filename  Name of the file.
   * @param string $mimetype  Mime type of the body.
   * @param string $body      Bytes of body content.
   * @param string $parent_id Unique ID of a parent folder.
   *
   * @throws \Exception
   *
   * @return array|null       Result from Colrow API Call.
   */
  public static function upload($user, $filename, $mimetype, $body, $parent_id)
  {
    if (func_num_args() !== 5) {
      throw new \Exception('Invalid number of arguments.');
    }
    $params = [
      'user' => $user,
      'name' => $filename,
      'type' => $mimetype,
      'body' => $body,
      'parent' => $parent_id
    ];
    $rest = curl_init();
    curl_setopt($rest, CURLOPT_URL, self::HOST_NAME);
    curl_setopt($rest, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($rest, CURLOPT_POST, 1);
    curl_setopt($rest, CURLOPT_POSTFIELDS, http_build_query($params));
    $response = curl_exec($rest);
    $status_code = curl_getinfo($rest, CURLINFO_HTTP_CODE);
    if ($status_code !== 200) {
      return;
    }
    $contentType = curl_getinfo($rest, CURLINFO_CONTENT_TYPE);
    if (curl_errno($rest)) {
      throw new ColrowException(curl_error($rest), curl_errno($rest));
    }
    curl_close($rest);
    if (strpos($contentType, 'text/html') !== false) {
      throw new ColrowException('Bad Request.', -1);
    }
    $decoded = json_decode($response, true);
    if (isset($decoded['status'])) {
      if ($decoded['status'] === 200) {
        return $decoded;
      } else if (isset($decoded['result']['reason'])) {
        throw new ColrowException($decoded['result']['reason'], -1);
      }
    }
    throw new ColrowException('Bad Request.', -1);
  }
}
