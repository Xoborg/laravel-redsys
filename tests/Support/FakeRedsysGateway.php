<?php

namespace Xoborg\LaravelRedsys\Tests\Support;

use Xoborg\LaravelRedsys\Helpers\CryptHelper;
use Xoborg\LaravelRedsys\Models\SolicitudPagoRedsys;

/**
 * Class FakeRedsysGateway
 * @package Xoborg\LaravelRedsys\Tests\Support
 */
class FakeRedsysGateway
{
	/**
	 * @var SolicitudPagoRedsys
	 */
	private $solicitudPagoRedsys;

	/**
	 * FakeRedsysGateway constructor.
	 * @param SolicitudPagoRedsys $solicitudPagoRedsys
	 */
	public function __construct(SolicitudPagoRedsys $solicitudPagoRedsys)
	{
		$this->solicitudPagoRedsys = $solicitudPagoRedsys;
	}

	/**
	 * @return array
	 */
	public function responseNotificacionOnline(): array
	{
		$merchantParameters = $this->generarMerchantParameters();
		$firma = $this->generarFirma($merchantParameters);

		return [
			'Ds_SignatureVersion' => 'HMAC_SHA256_V1',
			'Ds_MerchantParameters' => $merchantParameters,
			'Ds_Signature' => $firma
		];
	}

	/**
	 * @return string
	 */
	private function generarMerchantParameters(): string
	{
		$merchantParameters = base64_encode(json_encode([
			'Ds_Date' => now()->format('d/m/Y'),
			'Ds_Hour' => now()->format('H:i'),
			'Ds_Amount' => $this->solicitudPagoRedsys->amount,
			'Ds_Currency' => $this->solicitudPagoRedsys->currency,
			'Ds_Order' => $this->solicitudPagoRedsys->order,
			'Ds_MerchantCode' => $this->solicitudPagoRedsys->merchantCode,
			'Ds_Terminal' => $this->solicitudPagoRedsys->terminal,
			'Ds_Response' => '0000',
			'Ds_MerchantData' => $this->solicitudPagoRedsys->merchantData,
			'Ds_SecurePayment' => 0,
			'Ds_TransactionType' => $this->solicitudPagoRedsys->transactionType,
			'Ds_Card_Brand' => 1
		]));

		return $merchantParameters;
	}

	/**
	 * @param string $merchantParameters
	 * @return string
	 */
	private function generarFirma(string $merchantParameters): string
	{
		$key = base64_decode(config('redsys.clave_comercio'));
		$key = CryptHelper::to3DES($this->solicitudPagoRedsys->order, $key);
		$res = CryptHelper::toHmac256($merchantParameters, $key);
		return strtr(base64_encode($res), '+/', '-_');
	}
}