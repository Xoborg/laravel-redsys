<?php

namespace Xoborg\LaravelRedsys\Exceptions;

use Exception;

class PagoMerchantParameterException extends Exception
{
	public static function noOrderSpecified(): self
	{
		return new static('No se ha especificado la orden (número de pedido) del pago.');
	}

	public static function orderFormatInvalid(): self
	{
		return new static('El formato de la orden (número de pedido) no es válido.');
	}

	public static function noAmountSpecified(): self
	{
		return new static('No se ha especificado el importe del pago.');
	}

	public static function noMerchantCodeSpecified(): self
	{
		return new static('No se ha especificado el código de identificación de comercio.');
	}

	public static function noTerminalSpecified(): self
	{
		return new static('No se ha especificado el terminal.');
	}

	public static function noTransactionTypeSpecified(): self
	{
		return new static('No se ha especificado el tipo de transacción.');
	}

	public static function noCurrencySpecified(): self
	{
		return new static('No se ha especificado la moneda.');
	}
}