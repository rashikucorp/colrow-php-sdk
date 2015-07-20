<?php

namespace Colrow;

/**
 * ColrowObject - Representation of an object stored on Colrow.
 *
 * @author Somin Kobayashi <somin@rashikucorp.com>
 */
class ColrowObject
{
  /**
   * Estimated value.
   *
   * @var array
   */
  private $data;

  /**
   * Unique identifier for row data from entry feeds of a spreadsheet.
   *
   * @var string
   */
  private $row_id;

  /**
   * Create a Colrow Object.
   *
   * @param string $row_id
   */
  public function __construct($row_id = null)
  {
    $this->data = [];
    $this->row_id = $row_id;
  }

  /**
   * Get rowId for an object property.
   *
   * @return string
   */
  public function getId()
  {
    return $this->row_id;
  }

  /**
   * Get current value for an object property.
   *
   * @param string $key Key to retrieve from the estimatedData array.
   *
   * @return mixed
   */
  public function get($key)
  {
    foreach ($this->data as $data) {
      if (isset($data['label']) && $data['label'] == $key) {
        return $data['value'];
      }
    }
    return;
  }

  /**
   * Set a value for an object key.
   *
   * @param string $key Key to set a value for on the object.
   * @param mixed $value Value to set on the key.
   *
   * @throws Exception
   *
   * @return null
   */
  public function set($key, $value)
  {
    if (!$key) {
      throw new Exception('key may not be null.');
    }
    if (is_array($value)) {
      throw new Exception('value may not be array.');
    }
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
  }

  /**
   * Save Object to Parse.
   *
   * @return ColrowObject|null
   */
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
    return;
  }

  /**
   * Delete the row and the object.
   *
   * @return bool
   */
  public function destroy()
  {
    if (!$this->row_id) {
      return false;
    }
    $options = ['row_id' => $this->row_id];
    list($status_code, $response) = ColrowClient::_request('POST', $options);
    return ($status_code === 200);
  }

  /**
   * for debug...
   *
   * @return string
   */
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

  /**
   * Create the objects from the entry feeds.
   *
   * @param array $feeds entry feeds.
   *
   * @return array
   */
  public static function _createObjectsFromFeeds($feeds)
  {
    $objects = [];
    foreach ($feeds as $feed) {
      $objects[] = self::_createObjectFromFeed($feed);
    }
    return $objects;
  }

  /**
   * Create the object from the entry feed.
   *
   * @param array $feed each feed of entry feeds.
   *
   * @return ColrowObject
   */
  public static function _createObjectFromFeed($feed)
  {
    $object = new ColrowObject($feed['row_id']);
    $object->_setAll($feed['data']);
    return $object;
  }
}