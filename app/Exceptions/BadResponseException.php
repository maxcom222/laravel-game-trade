<?php namespace App\Exceptions;

class BadResponseException extends \Exception {

	public function __construct($message, $code = 502, $previous = NULL)
	{
		parent::__construct($message, $code, $previous);
	}
}
