<?php

namespace Xoborg\LaravelRedsys\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Xoborg\LaravelRedsys\Helpers\CryptHelper;
use Xoborg\LaravelRedsys\Services\Redsys\DsMerchantConsumerLanguage;
use Xoborg\LaravelRedsys\Services\Redsys\NotificacionOnlineHumanReadableResponses;

/**
 * Class NotificacionOnlineRedsys
 * @package Xoborg\LaravelRedsys\Models
 */
class NotificacionOnlineRedsys extends Model
{
	protected $casts = [
		'ds_date_hour' => 'datetime'
	];

	protected $table = 'notificaciones_online_redsys';

	/**
	 * MerchantParameters devueltos por Redsys
	 * @var string
	 */
	private $originalMerchantParametersJson;

	public function pagoRedsys()
	{
		return $this->belongsTo(PagoRedsys::class);
	}

	/**
	 * @param string $firma
	 * @return bool
	 */
	public function firmaValida(string $firma): bool
	{
		$key = base64_decode(config('redsys.clave_comercio'));
		$key = CryptHelper::to3DES($this->ds_order, $key);
		$res = CryptHelper::toHmac256($this->originalMerchantParametersJson, $key);
		return $firma === strtr(base64_encode($res), '+/', '-_');
	}

	/**
	 * @return string
	 * @throws \Xoborg\LaravelRedsys\Exceptions\NotificacionOnlineResponseCodeException
	 */
	public function getResponseText(): string
	{
		return NotificacionOnlineHumanReadableResponses::getResponse($this->ds_response);
	}

	/**
	 * @param string $merchantParameters
	 */
	public function setUp(string $merchantParameters)
	{
		$this->originalMerchantParametersJson = $merchantParameters;

		$merchantParameters = json_decode(urldecode(base64_decode(strtr($merchantParameters, '-_', '+/'))), true);

		$this->ds_date_hour = Carbon::createFromFormat('d/m/Y H:i', "{$merchantParameters['Ds_Date']} {$merchantParameters['Ds_Hour']}");
		$this->ds_amount = $merchantParameters['Ds_Amount'];
		$this->ds_currency = $merchantParameters['Ds_Currency'];
		$this->ds_order = $merchantParameters['Ds_Order'];
		$this->ds_response = $merchantParameters['Ds_Response'];
		$this->ds_merchant_merchantdata = $merchantParameters['Ds_MerchantData'];
		$this->ds_secure_payment = $merchantParameters['Ds_SecurePayment'];
		$this->ds_transaction_type = $merchantParameters['Ds_TransactionType'];
		$this->ds_card_country = array_key_exists('Ds_Card_Country', $merchantParameters) && $merchantParameters['Ds_Card_Country'] ?? '';
		$this->ds_authorisation_code = $merchantParameters['Ds_AuthorisationCode'] ?? '';
		$this->ds_consumer_language = array_key_exists('Ds_ConsumerLanguage', $merchantParameters) && $merchantParameters['Ds_ConsumerLanguage'] ?? DsMerchantConsumerLanguage::SIN_ESPECIFICAR;
		$this->ds_card_type = array_key_exists('Ds_Card_Type', $merchantParameters) && $merchantParameters['Ds_Card_Type'] ?? '';
		$this->ds_card_brand = array_key_exists('Ds_Card_Brand', $merchantParameters) && $merchantParameters['Ds_Card_Brand'] ?? '';
	}
}
