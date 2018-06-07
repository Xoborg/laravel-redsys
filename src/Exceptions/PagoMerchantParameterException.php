<?php

namespace Xoborg\LaravelRedsys\Exceptions;

use Exception;

class PagoMerchantParameterException extends Exception
{
	public static function invalidOrderFormat(): self
	{
		return new static('El formato de la orden (número de pedido) no es válido.');
	}

	public static function invalidMerchantCode(): self
	{
		return new static('El código de identificación de comercio no es válido, tiene que ser un número de 9 caracteres.');
	}

	public static function invalidAmount(): self
	{
		return new static('El formato del importe no es válido.');
	}

	public static function invalidCurrency(): self
	{
		return new static('El formato del código númerico de la moneda no es válido.');
	}

	public static function invalidTerminal(): self
	{
		return new static('El número de terminal no es válido.');
	}

	public static function invalidTransactionType(): self
	{
		return new static('El tipo de transacción no es válido..');
	}
}