<?php

namespace Colrow;

class ColrowObject
{
  private $data;
  private $row_id;

  public function __construct($row_id = null)
  {
    $this->data = [];
    if ($row_id) {
      $this->row_id = $row_id;
    }
    return $this;
  }

  public function getId()
  {
    return $this->row_id;
  }

  public function get($key)
  {
    foreach ($this->data as $data) {
      if (isset($data['label']) && $data['label'] == $key) {
        return $data['value'];
      }
    }
    return null;
  }

  public function set($key, $value)
  {
    $label_exists = false;
    foreach ($this->data as $index => $data) {
      if (isset($data['label']) && $data['label'] == $key) {
        $this->data[$index]['label'] = $key;
        $this->data[$index]['value'] = $value;
        $label_exists = true;
      }
    }
    if (!$label_exists) {
      $this->data[] = ['label' => $key, 'value' => $value];
    }
    return $this;
  }

  public function save()
  {
    $converted = $this->_convertData($this->data);
    $rows = ['data' => $converted];
    if ($this->row_id) {
      $rows['id'] = $this->row_id;
    }
    $options = ['rows' => json_encode([$rows])];
    list($status_code, $response) = ColrowClient::_request('POST', $options);
    if ($status_code === 200) {
      return $this->_createObjectFromFeed($response['result']['feeds'][0]);
    }
    return null;
  }

  public function destroy()
  {
    if (!$this->row_id) {
      return false;
    }
    $options = ['row_id' => $this->row_id];
    list($status_code, $response) = ColrowClient::_request('POST', $options);
    return ($status_code === 200);
  }

  public function toJson()
  {
    return json_encode($this->data);
  }

  private function _convertData($data)
  {
    $array = [];
    foreach ($data as $each) {
      $array[$each['label']] = $each['value'];
    }
    return $array;
  }

  private function _setAll($array)
  {
    $this->data = $array;
    return $this;
  }

  public static function _createObjectsFromFeeds($feeds)
  {
    $objects = [];
    foreach ($feeds as $feed) {
      $objects[] = self::_createObjectFromFeed($feed);
    }
    return $objects;
  }

  public static function _createObjectFromFeed($feed)
  {
    $object = new ColrowObject($feed['row_id']);
    $object->_setAll($feed['data']);
    return $object;
  }
}