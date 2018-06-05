<?php

namespace Xoborg\LaravelRedsys\Helpers;

/**
 * Class CryptHelper
 * @package Xoborg\LaravelRedsys\Helpers
 */
class CryptHelper
{
	private const OPENSSL_ENCRYPT_METHOD = 'des-ede3-cbc';
	private const OPENSSL_ENCRYPT_VECTOR = "\0\0\0\0\0\0\0\0";
	private const HMAC_ALGO = 'sha256';

	/**
	 * @param string $data
	 * @param string $key
	 * @return string
	 */
	public static function to3DES(string $data, string $key): string
	{
		$dataLength = ceil(strlen($data) / 8) * 8;
		return substr(openssl_encrypt(self::getDataToEncrypt($data, $dataLength), self::OPENSSL_ENCRYPT_METHOD, $key, OPENSSL_RAW_DATA, self::OPENSSL_ENCRYPT_VECTOR), 0, $dataLength);
	}

	/**
	 * @param string $data
	 * @param float $length
	 * @return string
	 */
	private static function getDataToEncrypt(string $data, float $length): string
	{
		return $data . str_repeat("\0", $length - strlen($data));
	}

	/**
	 * @param string $ent
	 * @param string $key
	 * @return string
	 */
	public static function toHmac256(string $ent, string $key): string
	{
		return hash_hmac(self::HMAC_ALGO, $ent, $key, true);
	}
}