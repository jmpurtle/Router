<?php
namespace Magnus\Exceptions;

class HTTPNotFound extends \Exception {
	public function __construct($message = 'Resource not found!', $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}