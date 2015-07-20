<?php

namespace Colrow;

final class ColrowClient
{
  const HOST_NAME = 'https://col-row.appspot.com/api/spreadsheet';

  private static $base_params;

  public static function initialize($user, $key, $sheet)
  {
    self::$base_params = [
      'user' => $user,
      'key' => $key,
      'sheet' => $sheet
    ];
  }

  public static function _request($method, $data = null)
  {
    $params = !empty($data) ? array_merge(self::$base_params, $data) : self::$base_params;
    $params['mode'] = 'sdk';
    $headers = array();
    $url = self::HOST_NAME;
    if ($method === 'GET') {
      $url = $url . '?' . http_build_query($params);
    }
    $rest = curl_init();
    curl_setopt($rest, CURLOPT_URL, $url);
    curl_setopt($rest, CURLOPT_RETURNTRANSFER, 1);
    if ($method === 'POST') {
      curl_setopt($rest, CURLOPT_POST, 1);
      curl_setopt($rest, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    curl_setopt($rest, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($rest);
    $status_code = curl_getinfo($rest, CURLINFO_HTTP_CODE);
    if ($status_code !== 200) {
      return [$status_code, null];
    }
    $contentType = curl_getinfo($rest, CURLINFO_CONTENT_TYPE);
    if (curl_errno($rest)) {
      throw new ColrowException(curl_error($rest), curl_errno($rest));
    }
    curl_close($rest);
    if (strpos($contentType, 'text/html') !== false) {
      throw new ColrowException('Bad Request', -1);
    }
    $decoded = json_decode($response, true);
    if (isset($decoded['status'])) {
      if ($decoded['status'] === 200) {
        return [200, $decoded];
      } else if (isset($decoded['result']['reason'])) {
        throw new ColrowException($decoded['result']['reason'], -1);
      }
    }
    throw new ColrowException('Bad Request', -1);
  }
}