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

		$notificacionOnline = new NotificacionOnlineRedsys($responseNotificacionOnline['Ds_MerchantParameters']);

		$this->assertTrue($solicitudPagoRedsys->order === $notificacionOnline->order);
		$this->assertTrue($notificacionOnline->firmaValida($responseNotificacionOnline['Ds_Signature']));

		$notificacionOnline->updatePagoRedsysConDatosNotificacionOnline($pagoRedsys->id);

		$this->assertDatabaseHas(
			'pagos_redsys',
			[
				'id' => $pagoRedsys->id,
				'ds_merchant_order' => $solicitudPagoRedsys->order,
				'ds_order' => $notificacionOnline->order,
				'ds_amount' => $notificacionOnline->amount
			]
		);
	}
}