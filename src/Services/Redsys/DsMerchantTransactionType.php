<?php

namespace Xoborg\LaravelRedsys\Services\Redsys;

abstract class DsMerchantTransactionType
{
	public const AUTORIZACION = '0';
	public const PREAUTORIZACION = '1';
	public const CONFIRMACION_DE_PREAUTORIZACION = '2';
	public const DEVOLUCION_AUTOMATICA = '3';
	public const TRANSACCION_RECURRENTE = '5';
	public const TRANSACCION_SUCESIVA = '6';
	public const PRE_AUTENTICACION = '7';
	public const CONFIRMACION_PRE_AUTENTICACION = '8';
	public const ANULACION_PREAUTORIZACION = '9';
	public const AUTORIZACION_EN_DIFERIDO = 'O';
	public const CONFIRMACION_DE_AUTORIZACION_EN_DIFERIDO = 'P';
	public const ANULACION_DE_AUTORIZACION_EN_DIFERIDO = 'Q';
	public const CUOTA_INICIAL_DIFERIDO = 'R';
	public const CUOTA_SUCESIVA_DIFERIDO = 'S';
}