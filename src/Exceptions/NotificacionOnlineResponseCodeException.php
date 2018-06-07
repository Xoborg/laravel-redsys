<?php

namespace Xoborg\LaravelRedsys\Exceptions;

use Exception;

class NotificacionOnlineResponseCodeException extends Exception
{
	public static function invalidResponseCode(): self
	{
		return new static('El código de respuesta de la operación no es válido.');
	}
}