<?php

namespace Colrow;

/**
 * ColrowClient - Main class for Colrow initialization and communication.
 *
 * @author Somin Kobayashi <somin@rashikucorp.com>
 */
final class ColrowClient
{
  /**
   * Constant for the API Server Host Address.
   */
  const HOST_NAME = 'https://app.colrow.net/api/v1/spreadsheet';

  /**
   * Base parameters.
   *
   * @var array
   */
  private static $base_params = [];

  /**
   * ColrowClient::initialize, must be called before using Colrow features.
   *
   * @param string $user  Account for Colrow API Call.
   * @param string $key   Spreadsheet key.
   * @param string $sheet Worksheet title.
   *
   * @return null
   */
  public static function initialize($user, $key, $sheet)
  {
    if ($user && $key && $sheet) {
      self::$base_params = [
        'user' => $user,
        'key' => $key,
        'sheet' => $sheet
      ];
    }
  }

  /**
   * ColrowClient::_request, internal method for communicating with Colrow.
   *
   * @param string $method  HTTP Method for this request.
   * @param null $data      Data to provide with the request.
   *
   * @throws \Exception
   *
   * @return array          Status code and Result from Colrow API Call.
   */
  public static function _request($method, $data = null)
  {
    if (!self::$base_params) {
      throw new \Exception('You must call ColrowClient::initialize() before making any requests.');
    }
    $params = !empty($data) ? array_merge(self::$base_params, $data) : self::$base_params;
    $params['mode'] = 'sdk';
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