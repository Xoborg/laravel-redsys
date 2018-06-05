<?php

	return [
		/*
		 * Obligatorio. Código FUC asignado al comercio
		 */
		'ds_merchant_merchantcode' => env('DS_MERCHANT_MERCHANTCODE', '999008881'),
		/*
		 * Obligatorio. Se debe enviar el código numérico de la moneda según el ISO-4217
		 */
		'ds_merchant_currency' => env('DS_MERCHANT_CURRENCY', \Xoborg\LaravelRedsys\Services\Redsys\DsMerchantCurrency::EUROS),
		/*
		 * Obligatorio. para el comercio para indicar qué tipo de transacción es.
		 */
		'ds_merchant_transactiontype' => env('DS_MERCHANT_TRANSACTIONTYPE', \Xoborg\LaravelRedsys\Services\Redsys\DsMerchantTransactionType::AUTORIZACION),
		/*
		 * Obligatorio. Número de terminal que le asignará su banco. Tres se considera su longitud máxima
		 */
		'ds_merchant_terminal' => env('DS_MERCHANT_TERMINAL', '01'),
		/*
		 * Constante que indica la versión de firma que se está utilizando
		 */
		'ds_signature_version' => env('DS_SIGNATURE_VERSION', 'HMAC_SHA256_V1'),
		/*
		 * Opcional. Indica el idioma del cliente
		 */
		'ds_merchant_consumer_language' => env('DS_MERCHANT_CONSUMER_LANGUAGE', \Xoborg\LaravelRedsys\Services\Redsys\DsMerchantConsumerLanguage::SIN_ESPECIFICAR),
		/*
		 * Clave de comercio
		 * Se puede obtener la clave accediendo al Módulo de Administración, opción Consulta datos de Comercio, en el apartado "Ver clave"
		 */
		'clave_comercio' => env('REDSYS_CLAVE_COMERCIO', 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'),
		/*
		 * Url a donde debe enviarse el formulario dependiendo de si se quiere
		 * realizar una petición de pruebas u operaciones reales
		 */
		'url' => [
			'pruebas' => env('REDSYS_URL_PRUEBAS', 'https://sis-t.redsys.es:25443/sis/realizarPago'),
			'real' => env('REDSYS_URL_REAL', 'https://sis.redsys.es/sis/realizarPago')
		]
	];