<?php

namespace Xoborg\LaravelRedsys\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Xoborg\LaravelRedsys\Helpers\CryptHelper;
use Xoborg\LaravelRedsys\Models\NotificacionOnlineRedsys;
use Xoborg\LaravelRedsys\Models\PagoRedsys;
use Xoborg\LaravelRedsys\Models\SolicitudPagoRedsys;
use Xoborg\LaravelRedsys\Services\Redsys\DsMerchantConsumerLanguage;
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
	/**
	 * @var PagoRedsys
	 */
	private $pagoRedsys;

	protected function setUp(): void
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
			'Ds_MerchantData' => '',
			'Ds_SecurePayment' => 0,
			'Ds_TransactionType' => config('redsys.ds_merchant_transactiontype'),
			'Ds_Card_Brand' => 1,
            'Ds_AuthorisationCode' => '123456'
		]));

		$solicitudPagoRedsys = new SolicitudPagoRedsys();
		$solicitudPagoRedsys->order = $this->order;
		$solicitudPagoRedsys->amount = 1;

		$this->pagoRedsys = $solicitudPagoRedsys->saveInDataBase();
	}


	/** @test */
	function se_valida_la_firma()
	{
		$key = base64_decode(config('redsys.clave_comercio'));
		$key = CryptHelper::to3DES($this->order, $key);
		$res = CryptHelper::toHmac256($this->merchantParameters, $key);
		$firma = strtr(base64_encode($res), '+/', '-_');

		$notificacionOnlineRedsys = new NotificacionOnlineRedsys();
		$notificacionOnlineRedsys->setUp($this->merchantParameters);

		$this->assertTrue($notificacionOnlineRedsys->firmaValida($firma));
	}

    /** @test */
    function se_hace_correctamente_setup_si_no_existen_datos_opcionales()
    {
        $notificacionOnlineRedsys = new NotificacionOnlineRedsys();
        $notificacionOnlineRedsys->setUp(base64_encode(json_encode([
            'Ds_Date' => now()->format('d/m/Y'),
            'Ds_Hour' => now()->format('H:i'),
            'Ds_Amount' => 1000,
            'Ds_Currency' => config('redsys.ds_merchant_currency'),
            'Ds_Order' => $this->order,
            'Ds_MerchantCode' => config('redsys.ds_merchant_merchantcode'),
            'Ds_Terminal' => config('redsys.ds_merchant_terminal'),
            'Ds_Response' => '0000',
            'Ds_MerchantData' => '',
            'Ds_SecurePayment' => 0,
            'Ds_TransactionType' => config('redsys.ds_merchant_transactiontype'),
            'Ds_Card_Brand' => 1
        ])));

        $this->assertEquals($notificacionOnlineRedsys->ds_authorisation_code, '');
        $this->assertEquals($notificacionOnlineRedsys->ds_consumer_language, DsMerchantConsumerLanguage::SIN_ESPECIFICAR);
        $this->assertEquals($notificacionOnlineRedsys->ds_card_brand, 1);
    }

	/** @test */
	function se_puede_insertar_notificacion_online_a_un_pago()
	{
		$notificacionOnlineRedsys = new NotificacionOnlineRedsys();
		$notificacionOnlineRedsys->setUp($this->merchantParameters);

		$this->pagoRedsys->notificacionesOnlineRedsys()->save($notificacionOnlineRedsys);

		$this->assertDatabaseHas(
			'notificaciones_online_redsys',
			[
				'id' => $notificacionOnlineRedsys->id,
				'ds_order' => $notificacionOnlineRedsys->ds_order,
				'ds_amount' => $notificacionOnlineRedsys->ds_amount,
				'pago_redsys_id' => $this->pagoRedsys->id
			]
		);
	}
}
