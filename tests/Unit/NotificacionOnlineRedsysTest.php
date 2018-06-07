<?php

namespace Xoborg\LaravelRedsys\Tests\Unit;

use Xoborg\LaravelRedsys\Helpers\CryptHelper;
use Xoborg\LaravelRedsys\Models\NotificacionOnlineRedsys;
use Xoborg\LaravelRedsys\Tests\TestCase;

class NotificacionOnlineRedsysTest extends TestCase
{
	/** @test */
	function se_valida_la_firma()
	{
		$order = 1;

		$merchantParameters = base64_encode(json_encode([
			'Ds_Date' => now()->format('d/m/Y'),
			'Ds_Hour' => now()->format('H:i'),
			'Ds_Amount' => 1000,
			'Ds_Currency' => config('redsys.ds_merchant_currency'),
			'Ds_Order' => $order,
			'Ds_MerchantCode' => config('redsys.ds_merchant_merchantcode'),
			'Ds_Terminal' => config('redsys.ds_merchant_terminal'),
			'Ds_Response' => '0000',
			'Ds_SecurePayment' => 0,
			'Ds_TransactionType' => config('redsys.ds_merchant_transactiontype'),
			'Ds_Card_Brand' => 1
		]));

		$key = base64_decode(config('redsys.clave_comercio'));
		$key = CryptHelper::to3DES($order, $key);
		$res = CryptHelper::toHmac256($merchantParameters, $key);
		$firma = strtr(base64_encode($res), '+/', '-_');

		$notificacionOnlineRedsys = new NotificacionOnlineRedsys($merchantParameters);

		$this->assertTrue($notificacionOnlineRedsys->validarFirma($firma));
	}
}