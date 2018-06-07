<?php

namespace Xoborg\LaravelRedsys\Services\Redsys;

use Xoborg\LaravelRedsys\Exceptions\NotificacionOnlineResponseCodeException;

/**
 * Class NotificacionOnlineHumanReadableResponses
 * @package Xoborg\LaravelRedsys\Services\Redsys
 */
abstract class NotificacionOnlineHumanReadableResponses
{
	/**
	 * @param int $responseCode
	 * @return string
	 * @throws NotificacionOnlineResponseCodeException
	 */
	public static function getResponse(int $responseCode): string
	{
		if ($responseCode < 100) {
			return 'Transacción autorizada para pagos y preautorizaciones';
		}
		if ($responseCode == 900) {
			return 'Transacción autorizada para devoluciones y confirmaciones';
		}
		if ($responseCode == 400) {
			return 'Transacción autorizada para anulaciones';
		}
		if ($responseCode == 101) {
			return 'Tarjeta caducada';
		}
		if ($responseCode == 102) {
			return 'Tarjeta en excepción transitoria o bajo sospecha de fraude';
		}
		if ($responseCode == 106) {
			return 'Intentos de PIN excedidos';
		}
		if ($responseCode == 125) {
			return 'Tarjeta no efectiva';
		}
		if ($responseCode == 129) {
			return 'Código de seguridad (CVV2/CVC2) incorrecto';
		}
		if ($responseCode == 180) {
			return 'Tarjeta ajena al servicio';
		}
		if ($responseCode == 184) {
			return 'Error en la autenticación del titular';
		}
		if ($responseCode == 190) {
			return 'Denegación del emisor sin especificar motivo';
		}
		if ($responseCode == 191) {
			return 'Fecha de caducidad errónea';
		}
		if ($responseCode == 202) {
			return 'Tarjeta en excepción transitoria o bajo sospecha de fraude con retirada de tarjeta';
		}
		if ($responseCode == 904) {
			return 'Comercio no registrado en FUC';
		}
		if ($responseCode == 909) {
			return 'Error de sistema';
		}
		if ($responseCode == 913) {
			return 'Pedido repetido';
		}
		if ($responseCode == 944) {
			return 'Sesión Incorrecta';
		}
		if ($responseCode == 950) {
			return 'Operación de devolución no permitida';
		}
		if ($responseCode == 9912 || $responseCode == 912) {
			return 'Emisor no disponible';
		}
		if ($responseCode == 9064) {
			return 'Número de posiciones de la tarjeta incorrecto';
		}
		if ($responseCode == 9078) {
			return 'Tipo de operación no permitida para esa tarjeta';
		}
		if ($responseCode == 9093) {
			return 'Tarjeta no existente';
		}
		if ($responseCode == 9094) {
			return 'Rechazo servidores internacionales';
		}
		if ($responseCode == 9104) {
			return 'Comercio con “titular seguro” y titular sin clave de compra segura';
		}
		if ($responseCode == 9218) {
			return 'El comercio no permite op. seguras por entrada /operaciones';
		}
		if ($responseCode == 9253) {
			return 'Tarjeta no cumple el check-digit';
		}
		if ($responseCode == 9256) {
			return 'El comercio no puede realizar preautorizaciones';
		}
		if ($responseCode == 9257) {
			return 'Esta tarjeta no permite operativa de preautorizaciones';
		}
		if ($responseCode == 9261) {
			return 'Operación detenida por superar el control de restricciones en la entrada al SIS';
		}
		if ($responseCode == 9913) {
			return 'Error en la confirmación que el comercio envía al TPV Virtual (solo aplicable en la opción de sincronización SOAP)';
		}
		if ($responseCode == 9914) {
			return 'Confirmación “KO” del comercio (solo aplicable en la opción de sincronización SOAP)';
		}
		if ($responseCode == 9915) {
			return 'A petición del usuario se ha cancelado el pago';
		}
		if ($responseCode == 9928) {
			return 'Anulación de autorización en diferido realizada por el SIS (proceso batch)';
		}
		if ($responseCode == 9929) {
			return 'Anulación de autorización en diferido realizada por el comercio';
		}
		if ($responseCode == 9997) {
			return 'Se está procesando otra transacción en SIS con la misma tarjeta';
		}
		if ($responseCode == 9998) {
			return 'Operación en proceso de solicitud de datos de tarjeta';
		}
		if ($responseCode == 9999) {
			return 'Operación que ha sido redirigida al emisor a autenticar';
		}

		throw NotificacionOnlineResponseCodeException::invalidResponseCode();
	}
}