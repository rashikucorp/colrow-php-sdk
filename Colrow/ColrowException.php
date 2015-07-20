<?php

namespace Colrow;

/**
 * ColrowException - Wrapper for \Exception class.
 *
 * @author Somin Kobayashi <somin@rashikucorp.com>
 */
class ColrowException extends \Exception
{
  /**
   * Constructs a ColrowException.
   *
   * @param string $message       Message for the Exception.
   * @param int $code             Error code.
   * @param \Exception $previous  Previous Exception.
   */
  public function __construct($message, $code = 0, \Exception $previous = null)
  {
    parent::__construct($message, $code, $previous);
  }
}