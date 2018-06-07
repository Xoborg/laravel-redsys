<?php

namespace Xoborg\LaravelRedsys\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Xoborg\LaravelRedsys\Exceptions\PagoMerchantParameterException;
use Xoborg\LaravelRedsys\Models\SolicitudPagoRedsys;
use Xoborg\LaravelRedsys\Tests\TestCase;

class SolicitudPagoRedsysTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	function se_validan_los_merchant_parameters()
	{
		$this->expectException(PagoMerchantParameterException::class);

		$this->expectExceptionMessage('El formato del importe no es válido.');

		$solicitudPagoRedsys = new SolicitudPagoRedsys();
		$solicitudPagoRedsys->order = 1;
		$solicitudPagoRedsys->getMerchantParameters();
	}

	/** @test */
	function se_inserta_solicitud_pago_en_db()
	{
		$solicitudPagoRedsys = new SolicitudPagoRedsys();
		$solicitudPagoRedsys->order = 1;
		$solicitudPagoRedsys->amount = 1;

		$id = $solicitudPagoRedsys->saveInDataBase();

		$this->assertDatabaseHas(
			'pagos_redsys',
			[
				'id' => $id,
				'ds_merchant_order' => $solicitudPagoRedsys->order,
				'ds_merchant_amount' => $solicitudPagoRedsys->amount
			]
		);
	}
}