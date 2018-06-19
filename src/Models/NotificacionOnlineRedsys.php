<?php

namespace Xoborg\LaravelRedsys\Models;

use Illuminate\Support\Carbon;
use Xoborg\LaravelRedsys\Helpers\CryptHelper;
use Xoborg\LaravelRedsys\Services\Redsys\DsMerchantConsumerLanguage;

/**
 * Class NotificacionOnlineRedsys
 * @package Xoborg\LaravelRedsys\Models
 */
class NotificacionOnlineRedsys
{
	/**
	 * Fecha
	 * @var string
	 */
	public $date;
	/**
	 * Hora
	 * @var string
	 */
	public $hour;
	/**
	 * Importe
	 * @var string
	 */
	public $amount;
	/**
	 * Moneda
	 * @var string
	 */
	public $currency;
	/**
	 * Número de pedido
	 * @var string
	 */
	public $order;
	/**
	 * Identificación de comercio: código FUC
	 * @var string
	 */
	public $merchantCode;
	/**
	 * Terminal
	 * @var string
	 */
	public $terminal;
	/**
	 * Código de respuesta
	 * @var string
	 */
	public $response;
	/**
	 * Datos del comercio
	 * @var string
	 */
	public $merchantData;
	/**
	 * Pago Seguro
	 * @var string
	 */
	public $securePayment;
	/**
	 * Tipo de operación
	 * @var string
	 */
	public $transactionType;
	/**
	 * País del titular
	 * @var string
	 */
	public $cardCountry;
	/**
	 * Código de autorización
	 * @var string
	 */
	public $authorisationCode;
	/**
	 * Idioma del titular
	 * @var string
	 */
	public $consumerLanguage;
	/**
	 * Tipo de Tarjeta
	 * @var string
	 */
	public $cardType;
	/**
	 * Sin documentar
	 * @var string
	 */
	public $cardBrand;
	/**
	 * MerchantParameters devueltos por Redsys
	 * @var string
	 */
	private $originalMerchantParametersJson;

	/**
	 * NotificacionOnlineRedsys constructor.
	 * @param string $merchantParameters
	 */
	public function __construct(string $merchantParameters)
	{
		$this->originalMerchantParametersJson = $merchantParameters;

		$merchantParameters = json_decode(base64_decode(strtr($merchantParameters, '-_', '+/')), true);

		$this->date = $merchantParameters['Ds_Date'];
		$this->hour = $merchantParameters['Ds_Hour'];
		$this->amount = $merchantParameters['Ds_Amount'];
		$this->currency = $merchantParameters['Ds_Currency'];
		$this->order = $merchantParameters['Ds_Order'];
		$this->merchantCode = $merchantParameters['Ds_MerchantCode'];
		$this->terminal = $merchantParameters['Ds_Terminal'];
		$this->response = $merchantParameters['Ds_Response'];
		$this->merchantData = array_key_exists('Ds_MerchantData', $merchantParameters) && $merchantParameters['Ds_MerchantData'] ?? '';
		$this->securePayment = $merchantParameters['Ds_SecurePayment'];
		$this->transactionType = $merchantParameters['Ds_TransactionType'];
		$this->cardCountry = array_key_exists('Ds_Card_Country', $merchantParameters) && $merchantParameters['Ds_Card_Country'] ?? '';
		$this->authorisationCode = array_key_exists('Ds_AuthorisationCode', $merchantParameters) && $merchantParameters['Ds_AuthorisationCode'] ?? '';
		$this->consumerLanguage = array_key_exists('Ds_ConsumerLanguage', $merchantParameters) && $merchantParameters['Ds_ConsumerLanguage'] ?? DsMerchantConsumerLanguage::SIN_ESPECIFICAR;
		$this->cardType = array_key_exists('Ds_Card_Type', $merchantParameters) && $merchantParameters['Ds_Card_Type'] ?? '';
		$this->cardBrand = array_key_exists('Ds_Card_Brand', $merchantParameters) && $merchantParameters['Ds_Card_Brand'] ?? '';
	}

	/**
	 * @param string $firma
	 * @return bool
	 */
	public function firmaValida(string $firma): bool
	{
		$key = base64_decode(config('redsys.clave_comercio'));
		$key = CryptHelper::to3DES($this->order, $key);
		$res = CryptHelper::toHmac256($this->originalMerchantParametersJson, $key);
		return $firma === strtr(base64_encode($res), '+/', '-_');
	}

	/**
	 * @param int $idPagoRedsys
	 * @return PagoRedsys
	 */
	public function updatePagoRedsysConDatosNotificacionOnline(int $idPagoRedsys): PagoRedsys
	{
		$pagoRedsys = PagoRedsys::findOrFail($idPagoRedsys);

		$pagoRedsys->ds_date_hour = Carbon::createFromFormat('d/m/Y H:i', "{$this->date} {$this->hour}");
		$pagoRedsys->ds_amount = $this->amount;
		$pagoRedsys->ds_currency = $this->currency;
		$pagoRedsys->ds_order = $this->order;
		$pagoRedsys->ds_response = $this->response;
		$pagoRedsys->ds_secure_payment = $this->securePayment;
		$pagoRedsys->ds_transaction_type = $this->transactionType;
		$pagoRedsys->ds_card_country = $this->cardCountry;
		$pagoRedsys->ds_authorisation_code = $this->authorisationCode;
		$pagoRedsys->ds_consumer_language = $this->consumerLanguage;
		$pagoRedsys->ds_card_type = $this->cardType;
		$pagoRedsys->ds_card_brand = $this->cardBrand;
		$pagoRedsys->save();

		return $pagoRedsys;
	}
}