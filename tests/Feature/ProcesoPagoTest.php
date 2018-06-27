<?php

namespace Xoborg\LaravelRedsys\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Xoborg\LaravelRedsys\Tests\TestCase;
use Xoborg\LaravelRedsys\Models\NotificacionOnlineRedsys;
use Xoborg\LaravelRedsys\Models\SolicitudPagoRedsys;
use Xoborg\LaravelRedsys\Tests\Support\FakeRedsysGateway;

class ProcesoPagoTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function funciona_proceso_de_pago()
	{
		$solicitudPagoRedsys = new SolicitudPagoRedsys();
		$solicitudPagoRedsys->order = '0001';
		$solicitudPagoRedsys->amount = 1075;
		$solicitudPagoRedsys->merchantUrl = 'http://www.example.com';
		$solicitudPagoRedsys->productDescription = 'Producto de prueba';
		$solicitudPagoRedsys->titular = 'Pepe Sánchez';
		$solicitudPagoRedsys->merchantName = 'Empresa de ejemplo S.L.';

		$pagoRedsys = $solicitudPagoRedsys->saveInDataBase();

		$fakeRedsysGateway = new FakeRedsysGateway($solicitudPagoRedsys);

		$responseNotificacionOnline = $fakeRedsysGateway->responseNotificacionOnline();

		$notificacionOnlineRedsys = new NotificacionOnlineRedsys();
		$notificacionOnlineRedsys->setUp($responseNotificacionOnline['Ds_MerchantParameters']);

		$this->assertTrue($solicitudPagoRedsys->order === $notificacionOnlineRedsys->ds_order);
		$this->assertTrue($notificacionOnlineRedsys->firmaValida($responseNotificacionOnline['Ds_Signature']));

		$pagoRedsys->notificacionesOnlineRedsys()->save($notificacionOnlineRedsys);

		$this->assertDatabaseHas(
			'pagos_redsys',
			[
				'id' => $pagoRedsys->id,
				'ds_merchant_order' => $solicitudPagoRedsys->order,
				'ds_merchant_amount' => $solicitudPagoRedsys->amount
			]
		);

		$this->assertDatabaseHas(
			'notificaciones_online_redsys',
			[
				'id' => $notificacionOnlineRedsys->id,
				'ds_order' => $notificacionOnlineRedsys->ds_order,
				'ds_amount' => $notificacionOnlineRedsys->ds_amount,
				'pago_redsys_id' => $pagoRedsys->id
			]
		);
	}
}