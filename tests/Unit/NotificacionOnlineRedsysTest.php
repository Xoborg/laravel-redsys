<?php

namespace Xoborg\LaravelRedsys\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Xoborg\LaravelRedsys\Helpers\CryptHelper;
use Xoborg\LaravelRedsys\Models\NotificacionOnlineRedsys;
use Xoborg\LaravelRedsys\Models\SolicitudPagoRedsys;
use Xoborg\LaravelRedsys\Tests\TestCase;

class NotificacionOnlineRedsysTest extends TestCase
{
	Use RefreshDatabase;

	/**
	 * @var string
	 */
	private $order;
	/**
	 * @var string
	 */
	private $merchantParameters;

	protected function setUp()
	{
		parent::setUp();

		$this->order = '0001';

		$this->merchantParameters = base64_encode(json_encode([
			'Ds_Date' => now()->format('d/m/Y'),
			'Ds_Hour' => now()->format('H:i'),
			'Ds_Amount' => 1000,
			'Ds_Currency' => config('redsys.ds_merchant_currency'),
			'Ds_Order' => $this->order,
			'Ds_MerchantCode' => config('redsys.ds_merchant_merchantcode'),
			'Ds_Terminal' => config('redsys.ds_merchant_terminal'),
			'Ds_Response' => '0000',
			'Ds_SecurePayment' => 0,
			'Ds_TransactionType' => config('redsys.ds_merchant_transactiontype'),
			'Ds_Card_Brand' => 1
		]));
	}


	/** @test */
	function se_valida_la_firma()
	{
		$key = base64_decode(config('redsys.clave_comercio'));
		$key = CryptHelper::to3DES($this->order, $key);
		$res = CryptHelper::toHmac256($this->merchantParameters, $key);
		$firma = strtr(base64_encode($res), '+/', '-_');

		$notificacionOnlineRedsys = new NotificacionOnlineRedsys($this->merchantParameters);

		$this->assertTrue($notificacionOnlineRedsys->firmaValida($firma));
	}

	/** @test */
	function se_actualiza_pago_redsys_con_datos_notificacion_online()
	{
		$solicitudPagoRedsys = new SolicitudPagoRedsys();
		$solicitudPagoRedsys->order = $this->order;
		$solicitudPagoRedsys->amount = 1;

		$pagoRedsys = $solicitudPagoRedsys->saveInDataBase();

		$notificacionOnlineRedsys = new NotificacionOnlineRedsys($this->merchantParameters);

		$notificacionOnlineRedsys->updatePagoRedsysConDatosNotificacionOnline($pagoRedsys->id);

		$this->assertDatabaseHas(
			'pagos_redsys',
			[
				'id' => $pagoRedsys->id,
				'Ds_Order' => $notificacionOnlineRedsys->order,
				'Ds_Amount' => $notificacionOnlineRedsys->amount
			]
		);
	}
}