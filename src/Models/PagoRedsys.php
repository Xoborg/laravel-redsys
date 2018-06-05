<?php

namespace Xoborg\LaravelRedsys\Models;

use Illuminate\Support\Carbon;
use Xoborg\LaravelRedsys\Helpers\CryptHelper;

/**
 * Class PagoRedsys
 */
class PagoRedsys implements \JsonSerializable
{
	/**
	 * Identificación de comercio: código FUC
	 * @var string
	 */
	public $merchantCode;
	/**
	 * Número de terminal
	 * @var string
	 */
	public $terminal;
	/**
	 * Tipo de transacción
	 * @var string
	 */
	public $transactionType;
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
	 * Número de Pedido
	 * @var string
	 */
	public $order;
	/**
	 * URL del comercio para la notificación "on-line"
	 * @var string
	 */
	public $merchantUrl;
	/**
	 * Descripción del producto
	 * @var string
	 */
	public $productDescription;
	/**
	 * Nombre y apellidos del titular
	 * @var string
	 */
	public $titular;
	/**
	 * URLOK
	 * @var string
	 */
	public $urlOk;
	/**
	 * URL KO
	 * @var string
	 */
	public $urlKo;
	/**
	 * Identificación de comercio
	 * @var string
	 */
	public $merchantName;
	/**
	 * Idioma del titular
	 * @var string
	 */
	public $consumerLanguage;
	/**
	 * Importe total (cuota recurrente)
	 * @var string
	 */
	public $sumTotal;
	/**
	 * Datos del comercio
	 * @var string
	 */
	public $merchantData;
	/**
	 * Frecuencia
	 * @var string
	 */
	public $dateFrecuency;
	/**
	 * Fecha límite
	 * @var Carbon
	 */
	public $chargeExpiryDate;
	/**
	 * Código de Autorización
	 * @var string
	 */
	public $authorisationCode;
	/**
	 * Fecha de la operación recurrente sucesiva
	 * @var Carbon
	 */
	public $transactionDate;
	/**
	 * Referencia
	 * @var string
	 */
	public $identifier;
	/**
	 * Código de grupo
	 * @var string
	 */
	public $group;
	/**
	 * Pago sin autenticación
	 * @var string
	 */
	public $directPayment;
	/**
	 * Tarjeta
	 * @var string
	 */
	public $pan;
	/**
	 * Caducidad
	 * @var string
	 */
	public $expiryDate;
	/**
	 * CVV2
	 * @var string
	 */
	public $cvv2;

	/**
	 * PagoRedsys constructor.
	 */
	public function __construct()
	{
		$this->merchantCode = config('redsys.ds_merchant_merchantcode');
		$this->currency = config('redsys.ds_merchant_currency');
		$this->transactionType = config('redsys.ds_merchant_transactiontype');
		$this->terminal = config('redsys.ds_merchant_terminal');
		$this->consumerLanguage = config('redsys.ds_merchant_consumer_language');
	}

	/**
	 * @return array
	 */
	public function jsonSerialize(): array
	{
		return collect([
			'Ds_Merchant_MerchantCode' => $this->merchantCode,
			'Ds_Merchant_Terminal' => $this->terminal,
			'Ds_Merchant_TransactionType' => $this->transactionType,
			'Ds_Merchant_Amount' => $this->amount,
			'Ds_Merchant_Currency' => $this->currency,
			'Ds_Merchant_Order' => $this->order,
			'Ds_Merchant_MerchantURL' => $this->merchantUrl,
			'Ds_Merchant_ProductDescription' => $this->productDescription,
			'Ds_Merchant_Titular' => $this->titular,
			'Ds_Merchant_UrlOK' => $this->urlOk,
			'Ds_Merchant_UrlKO' => $this->urlKo,
			'Ds_Merchant_MerchantName' => $this->merchantName,
			'Ds_Merchant_ConsumerLanguage' => $this->consumerLanguage,
			'Ds_Merchant_SumTotal' => $this->sumTotal,
			'Ds_Merchant_MerchantData' => $this->merchantData,
			'Ds_Merchant_DateFrecuency' => $this->dateFrecuency,
			'Ds_Merchant_ChargeExpiryDate' => optional($this->chargeExpiryDate)->format('Y-m-d'),
			'Ds_Merchant_AuthorisationCode' => $this->authorisationCode,
			'Ds_Merchant_TransactionDate' => optional($this->transactionDate)->format('Y-m-d'),
			'Ds_Merchant_Identifier' => $this->identifier,
			'Ds_Merchant_Group' => $this->group,
			'Ds_Merchant_DirectPayment' => $this->directPayment,
			'Ds_Merchant_Pan' => $this->pan,
			'Ds_Merchant_ExpiryDate' => $this->expiryDate,
			'Ds_Merchant_CVV2' => $this->cvv2,
		])
			->filter(function ($value) {
				return $value && !empty($value);
			})
			->toArray();
	}

	/**
	 * @return string
	 */
	public function getMerchantParameters(): string
	{
		return base64_encode(json_encode($this));
	}

	/**
	 * @return string
	 */
	public function getMerchantSignature(): string
	{
		$key = base64_decode(config('redsys.clave_comercio'));
		$ent = $this->getMerchantParameters();
		$key = CryptHelper::to3DES($this->order, $key);
		return base64_encode(CryptHelper::toHmac256($ent, $key));
	}
}